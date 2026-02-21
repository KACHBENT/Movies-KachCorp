<?php

namespace App\Controllers;

use App\Models\PersonaModel;
use App\Models\ContactoModel;
use App\Models\UsuarioModel;
use App\Models\RolModel;
use App\Models\RolesDetalleModel;
use App\Models\ImageModel;
use App\Libraries\Mailer;
use CodeIgniter\HTTP\RedirectResponse;

class UsuariosController extends BaseController
{
    private function normalizeUser(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        $text = preg_replace('/[^a-z0-9]+/', '', $text);
        return $text ?: 'user';
    }

    private function generateUsername(string $nombre, string $ap, \CodeIgniter\Database\BaseConnection $db): string
    {
        $base = $this->normalizeUser(mb_substr($nombre, 0, 1, 'UTF-8') . $ap);
        $username = $base;
        $i = 1;

        while ($db->table('tbl_rel_usuario')->where('usuario_nombre', $username)->countAllResults() > 0) {
            $username = $base . $i;
            $i++;
        }

        return $username;
    }

    private function generateTempPassword(int $length = 10): string
    {

        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        $bytes = random_bytes($length);
        $pass = '';
        for ($i=0; $i<$length; $i++) {
            $pass .= $alphabet[ord($bytes[$i]) % strlen($alphabet)];
        }
        return $pass;
    }

    public function create(): string
    {
        $rolModel = new RolModel();

        return view('usuarios/registro', [
            'roles' => $rolModel->where('roles_Activo', 1)->orderBy('roles_Valor', 'ASC')->findAll(),
        ]);
    }

    public function store(): RedirectResponse
    {
        if (!$this->request->is('post')) {
            return redirect()->to(site_url('usuarios/registro'));
        }

        $rules = [
            'persona_Nombre'          => 'required|min_length[2]|max_length[50]',
            'persona_ApllP'           => 'required|min_length[2]|max_length[50]',
            'persona_ApllM'           => 'permit_empty|max_length[50]',
            'persona_FechaNacimiento' => 'required|valid_date[Y-m-d]',

            'correo'                  => 'required|valid_email|max_length[50]',
            'rolesId'                 => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('toast_error', array_values($this->validator->getErrors()));
        }

        $nombre = trim((string)$this->request->getPost('persona_Nombre'));
        $ap     = trim((string)$this->request->getPost('persona_ApllP'));
        $am     = trim((string)$this->request->getPost('persona_ApllM'));
        $fnac   = (string)$this->request->getPost('persona_FechaNacimiento');

        $correo = strtolower(trim((string)$this->request->getPost('correo')));
        $rolesId = (int)$this->request->getPost('rolesId');

        $file = $this->request->getFile('foto');
        $hasImage = $file && $file->isValid() && !$file->hasMoved() && $file->getSize() > 0;

        $clientName = null;
        $clientMime = null;
        $clientSize = null;

        if ($hasImage) {
            $clientName = $file->getClientName();
            $clientMime = (string)$file->getMimeType();
            $clientSize = (int)$file->getSize();

            $validMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!in_array($clientMime, $validMimes, true)) {
                return redirect()->back()->withInput()->with('toast_error', 'La imagen debe ser JPG, PNG o WEBP.');
            }
            if ($file->getSizeByUnit('mb') > 5) {
                return redirect()->back()->withInput()->with('toast_error', 'La imagen no debe exceder 5 MB.');
            }
        }

        $db = \Config\Database::connect();

        $correoExistente = $db->table('tbl_rel_contacto')
            ->where('tipocontactoId', 1)
            ->where('contacto_Valor', $correo)
            ->where('contacto_Activo', 1)
            ->get()->getRowArray();

        if ($correoExistente) {
            return redirect()->back()->withInput()->with('toast_error', 'Ese correo ya está registrado.');
        }

        $rolModel = new RolModel();
        $rol = $rolModel->find($rolesId);
        if (!$rol || (int)($rol['roles_Activo'] ?? 0) !== 1) {
            return redirect()->back()->withInput()->with('toast_error', 'Rol inválido.');
        }

        $usuarioGenerado = $this->generateUsername($nombre, $ap, $db);
        $passTemp        = $this->generateTempPassword(10);
        $passHash        = password_hash($passTemp, PASSWORD_DEFAULT);

        $personaModel      = new PersonaModel();
        $contactoModel     = new ContactoModel();
        $usuarioModel      = new UsuarioModel();
        $rolesDetalleModel = new RolesDetalleModel();
        $imageModel        = new ImageModel();

        $movedFullPath = null;

        $db->transBegin();

        try {

            $personaId = $personaModel->insert([
                'persona_Nombre'          => $nombre,
                'persona_ApllP'           => $ap,
                'persona_ApllM'           => $am !== '' ? $am : null,
                'persona_FechaNacimiento' => $fnac,
                'persona_Activo'          => 1,
            ], true);

            if (!$personaId) throw new \RuntimeException('No se pudo crear la persona.');

            // Contacto correo
            if (!$contactoModel->insert([
                'personaId'       => (int)$personaId,
                'tipocontactoId'  => 1,
                'contacto_Valor'  => $correo,
                'contacto_Activo' => 1,
            ])) {
                throw new \RuntimeException('No se pudo registrar el correo.');
            }

            $imageId = null;

            if ($hasImage) {
                $newName = $file->getRandomName();
                $dir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'usuarios';

                if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                    throw new \RuntimeException('No se pudo crear uploads/usuarios.');
                }

                if (!$file->move($dir, $newName)) {
                    throw new \RuntimeException('No se pudo mover la imagen.');
                }

                $movedFullPath = $dir . DIRECTORY_SEPARATOR . $newName;
                $relativeUrl   = 'uploads/usuarios/' . $newName;

                $hash = hash_file('sha256', $movedFullPath);

                $existing = $db->table('tbl_ope_image')->select('imageId')->where('image_Hash', $hash)->get()->getRowArray();
                if ($existing) {
                    $imageId = (int)$existing['imageId'];
                } else {
                    $imageId = $imageModel->insert([
                        'image_Url'       => $relativeUrl,
                        'image_FileName'  => $clientName ?: $newName,
                        'image_Mime'      => $clientMime ?: (mime_content_type($movedFullPath) ?: 'application/octet-stream'),
                        'image_SizeBytes' => $clientSize ?: (int)@filesize($movedFullPath),
                        'image_Hash'      => $hash,
                        'image_Activo'    => 1,
                    ], true);

                    if (!$imageId) throw new \RuntimeException('No se pudo guardar la imagen en BD.');
                }
            }

 
            $usuarioId = $usuarioModel->insert([
                'usuario_nombre'     => $usuarioGenerado,
                'personaId'          => (int)$personaId,
                'imageId'            => $imageId,
                'usuario_Contrasena' => $passHash,
                'usuario_Activo'     => 1,
            ], true);

            if (!$usuarioId) throw new \RuntimeException('No se pudo crear el usuario.');
            if (!$rolesDetalleModel->insert([
                'usuarioId'           => (int)$usuarioId,
                'rolesId'             => (int)$rolesId,
                'rolesDetalle_Activo' => 1,
            ])) {
                throw new \RuntimeException('No se pudo asignar el rol.');
            }

            $db->transCommit();

        } catch (\Throwable $e) {
            $db->transRollback();
            if ($movedFullPath && is_file($movedFullPath)) @unlink($movedFullPath);

            return redirect()->back()->withInput()->with('toast_error', 'Error al registrar: ' . $e->getMessage());
        }

        $mailer = new Mailer();

        $loginUrl = site_url('acceso/login');
        $html = "
          <h2>Bienvenido(a)</h2>
          <p>Tu cuenta fue creada correctamente.</p>
          <p><b>Usuario:</b> {$correo}<br>
             <b>Contraseña temporal:</b> {$passTemp}</p>
          <p>Inicia sesión aquí: <a href='{$loginUrl}'>{$loginUrl}</a></p>
          <p style='font-size:12px;color:#666'>Por seguridad, cambia tu contraseña después de ingresar.</p>
        ";

        $send = $mailer->send($correo, $nombre . ' ' . $ap, 'Tus accesos al sistema', $html);

        if (!$send['ok']) {
            return redirect()->to(site_url('acceso/login'))
                ->with('toast_error', 'Usuario creado, pero no se pudo enviar el correo: ' . $send['error']);
        }

        return redirect()->to(site_url('acceso/login'))
            ->with('toast_success', 'Usuario creado y credenciales enviadas al correo.');
    }

 public function index(): string
    {
        $db = \Config\Database::connect();

        $estado = (string)($this->request->getGet('estado') ?? 'all'); // '1','0','all'
        $estado = in_array($estado, ['1','0','all'], true) ? $estado : 'all';

        $builder = $db->table('tbl_rel_usuario u')
            ->select("
                u.usuarioId, u.usuario_nombre, u.usuario_Activo, u.personaId, u.imageId,
                p.persona_Nombre, p.persona_ApllP, p.persona_ApllM, p.persona_FechaNacimiento,
                c.contacto_Valor AS correo,
                r.rolesId, r.roles_Valor,
                img.image_Url
            ")
            ->join('tbl_ope_persona p', 'p.personaId = u.personaId', 'inner')
            ->join('tbl_rel_contacto c', 'c.personaId = p.personaId AND c.tipocontactoId = 1 AND c.contacto_Activo = 1', 'left')
            // solo el rol ACTIVO
            ->join('tbl_ope_rolesdetalle rd', 'rd.usuarioId = u.usuarioId AND rd.rolesDetalle_Activo = 1', 'left')
            ->join('tbl_cat_roles r', 'r.rolesId = rd.rolesId AND r.roles_Activo = 1', 'left')
            ->join('tbl_ope_image img', 'img.imageId = u.imageId AND img.image_Activo = 1', 'left')
            ->orderBy('u.usuarioId', 'DESC');

        if ($estado !== 'all') {
            $builder->where('u.usuario_Activo', (int)$estado);
        }

        $usuarios = $builder->get()->getResultArray();

        $roles = $db->table('tbl_cat_roles')
            ->where('roles_Activo', 1)
            ->orderBy('roles_Valor', 'ASC')
            ->get()->getResultArray();

        return view('usuarios/index', [
            'usuarios' => $usuarios,
            'roles'    => $roles,
            'estado'   => $estado,
        ]);
    }

    public function deactivate(int $usuarioId): RedirectResponse
    {
        $db = \Config\Database::connect();

        $u = $db->table('tbl_rel_usuario')->where('usuarioId', $usuarioId)->get()->getRowArray();
        if (!$u) return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', 'Usuario no encontrado.');

        $db->transBegin();
        try {
            $db->table('tbl_rel_usuario')->where('usuarioId', $usuarioId)->update(['usuario_Activo' => 0]);

            // desactiva roles activos
            $db->table('tbl_ope_rolesdetalle')
                ->where('usuarioId', $usuarioId)
                ->update(['rolesDetalle_Activo' => 0]);

            $db->transCommit();
            return redirect()->to(site_url('usuarios?estado=all'))->with('toast_success', 'Usuario desactivado.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', 'Error al desactivar: ' . $e->getMessage());
        }
    }

    public function activate(int $usuarioId): RedirectResponse
    {
        $db = \Config\Database::connect();

        $u = $db->table('tbl_rel_usuario')->where('usuarioId', $usuarioId)->get()->getRowArray();
        if (!$u) return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', 'Usuario no encontrado.');

        $db->transBegin();
        try {
            $db->table('tbl_rel_usuario')->where('usuarioId', $usuarioId)->update(['usuario_Activo' => 1]);

            $last = $db->table('tbl_ope_rolesdetalle')
                ->select('rolesDetalleId')
                ->where('usuarioId', $usuarioId)
                ->orderBy('rolesDetalleId', 'DESC')
                ->get(1)->getRowArray();

            if ($last) {
                $db->table('tbl_ope_rolesdetalle')
                    ->where('usuarioId', $usuarioId)
                    ->update(['rolesDetalle_Activo' => 0]);

                $db->table('tbl_ope_rolesdetalle')
                    ->where('rolesDetalleId', (int)$last['rolesDetalleId'])
                    ->update(['rolesDetalle_Activo' => 1]);
            }

            $db->transCommit();
            return redirect()->to(site_url('usuarios?estado=all'))->with('toast_success', 'Usuario activado.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', 'Error al activar: ' . $e->getMessage());
        }
    }

    public function update(int $usuarioId): RedirectResponse
    {
        if (!$this->request->is('post')) {
            return redirect()->to(site_url('usuarios?estado=all'));
        }

        $rules = [
            'persona_Nombre'          => 'required|min_length[2]|max_length[50]',
            'persona_ApllP'           => 'required|min_length[2]|max_length[50]',
            'persona_ApllM'           => 'permit_empty|max_length[50]',
            'persona_FechaNacimiento' => 'required|valid_date[Y-m-d]',
            'correo'                  => 'required|valid_email|max_length[50]',
            'rolesId'                 => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(site_url('usuarios?estado=all'))
                ->with('toast_error', array_values($this->validator->getErrors()));
        }

        $nombre = trim((string)$this->request->getPost('persona_Nombre'));
        $ap     = trim((string)$this->request->getPost('persona_ApllP'));
        $am     = trim((string)$this->request->getPost('persona_ApllM'));
        $fnac   = (string)$this->request->getPost('persona_FechaNacimiento');
        $correo = strtolower(trim((string)$this->request->getPost('correo')));
        $rolesId = (int)$this->request->getPost('rolesId');

        $db = \Config\Database::connect();

        $u = $db->table('tbl_rel_usuario')->where('usuarioId', $usuarioId)->get()->getRowArray();
        if (!$u) return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', 'Usuario no encontrado.');
        $personaId = (int)$u['personaId'];

        $correoDup = $db->table('tbl_rel_contacto')
            ->where('tipocontactoId', 1)
            ->where('contacto_Valor', $correo)
            ->where('contacto_Activo', 1)
            ->where('personaId !=', $personaId)
            ->get()->getRowArray();

        if ($correoDup) {
            return redirect()->to(site_url('usuarios?estado=all'))
                ->with('toast_error', 'Ese correo ya está registrado en otra persona.');
        }

        $rolModel = new RolModel();
        $rol = $rolModel->find($rolesId);
        if (!$rol || (int)($rol['roles_Activo'] ?? 0) !== 1) {
            return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', 'Rol inválido.');
        }

        $file = $this->request->getFile('foto');
        $hasImage = $file && $file->isValid() && !$file->hasMoved() && $file->getSize() > 0;

        $clientName = null;
        $clientMime = null;
        $clientSize = null;

        if ($hasImage) {
            $clientName = $file->getClientName();
            $clientMime = (string)$file->getMimeType();
            $clientSize = (int)$file->getSize();

            $validMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!in_array($clientMime, $validMimes, true)) {
                return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', 'La imagen debe ser JPG, PNG o WEBP.');
            }
            if ($file->getSizeByUnit('mb') > 5) {
                return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', 'La imagen no debe exceder 5 MB.');
            }
        }

        $personaModel = new PersonaModel();
        $contactoModel = new ContactoModel();
        $usuarioModel = new UsuarioModel();
        $rolesDetalleModel = new RolesDetalleModel();
        $imageModel = new ImageModel();

        $db->transBegin();
        $movedFullPath = null;

        try {
            if (!$personaModel->update($personaId, [
                'persona_Nombre'          => $nombre,
                'persona_ApllP'           => $ap,
                'persona_ApllM'           => $am !== '' ? $am : null,
                'persona_FechaNacimiento' => $fnac,
            ])) {
                throw new \RuntimeException('No se pudo actualizar la persona.');
            }


            $contacto = $db->table('tbl_rel_contacto')
                ->where('personaId', $personaId)
                ->where('tipocontactoId', 1)
                ->where('contacto_Activo', 1)
                ->get()->getRowArray();

            if ($contacto) {
                $okCorreo = $contactoModel->update((int)$contacto['contactoId'], ['contacto_Valor' => $correo]);
            } else {
                $okCorreo = $contactoModel->insert([
                    'personaId'       => $personaId,
                    'tipocontactoId'  => 1,
                    'contacto_Valor'  => $correo,
                    'contacto_Activo' => 1,
                ]);
            }

            if (!$okCorreo) throw new \RuntimeException('No se pudo actualizar el correo.');

            $imageId = $u['imageId'] ?? null;

            if ($hasImage) {
                $newName = $file->getRandomName();
                $dir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'usuarios';

                if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                    throw new \RuntimeException('No se pudo crear uploads/usuarios.');
                }

                if (!$file->move($dir, $newName)) {
                    throw new \RuntimeException('No se pudo mover la imagen.');
                }

                $movedFullPath = $dir . DIRECTORY_SEPARATOR . $newName;
                $relativeUrl   = 'uploads/usuarios/' . $newName;
                $hash = hash_file('sha256', $movedFullPath);

                $existing = $db->table('tbl_ope_image')->select('imageId')->where('image_Hash', $hash)->get()->getRowArray();
                if ($existing) {
                    $imageId = (int)$existing['imageId'];
                } else {
                    $imageId = $imageModel->insert([
                        'image_Url'       => $relativeUrl,
                        'image_FileName'  => $clientName ?: $newName,
                        'image_Mime'      => $clientMime ?: (mime_content_type($movedFullPath) ?: 'application/octet-stream'),
                        'image_SizeBytes' => $clientSize ?: (int)@filesize($movedFullPath),
                        'image_Hash'      => $hash,
                        'image_Activo'    => 1,
                    ], true);

                    if (!$imageId) throw new \RuntimeException('No se pudo guardar la imagen en BD.');
                }

                if (!$usuarioModel->update($usuarioId, ['imageId' => $imageId])) {
                    throw new \RuntimeException('No se pudo asociar la imagen al usuario.');
                }
            }

            $db->table('tbl_ope_rolesdetalle')
                ->where('usuarioId', $usuarioId)
                ->update(['rolesDetalle_Activo' => 0]);

            $rd = $db->table('tbl_ope_rolesdetalle')
                ->select('rolesDetalleId')
                ->where('usuarioId', $usuarioId)
                ->where('rolesId', $rolesId)
                ->get()->getRowArray();

            if ($rd) {
                $db->table('tbl_ope_rolesdetalle')
                    ->where('rolesDetalleId', (int)$rd['rolesDetalleId'])
                    ->update(['rolesDetalle_Activo' => 1]);
            } else {
                // si no existe -> insert
                if (!$rolesDetalleModel->insert([
                    'usuarioId'           => $usuarioId,
                    'rolesId'             => $rolesId,
                    'rolesDetalle_Activo' => 1,
                ])) {
                    throw new \RuntimeException('No se pudo asignar el rol.');
                }
            }

            $db->transCommit();
            return redirect()->to(site_url('usuarios?estado=all'))->with('toast_success', 'Usuario actualizado.');
        } catch (\Throwable $e) {
            $db->transRollback();
            if ($movedFullPath && is_file($movedFullPath)) @unlink($movedFullPath);
            return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
}
