<?php

namespace App\Controllers;

use App\Models\PersonaModel;
use App\Models\ContactoModel;
use App\Models\UsuarioModel;
use App\Models\RolModel;
use App\Models\ImageModel;
use App\Libraries\Mailer;
use CodeIgniter\HTTP\RedirectResponse;

class UsuariosController extends BaseController
{
    // ====== AJUSTA SI TU BD CAMBIA NOMBRES ======
    private const TBL_USUARIO = 'tbl_rel_usuario';
    private const TBL_PERSONA = 'tbl_ope_persona';
    private const TBL_CONTACTO = 'tbl_rel_contacto';
    private const TBL_ROL = 'tbl_cat_roles';
    private const TBL_ROLDET = 'tbl_ope_rolesdetalle';
    private const TBL_IMAGE = 'tbl_ope_image';

    private const CONTACTO_EMAIL_ID = 1; // tipocontactoId para email

    // ====== HELPERS ======
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

        while ($db->table(self::TBL_USUARIO)->where('usuario_nombre', $username)->countAllResults() > 0) {
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
        for ($i = 0; $i < $length; $i++) {
            $pass .= $alphabet[ord($bytes[$i]) % strlen($alphabet)];
        }
        return $pass;
    }

    private function assertActiveRole(int $rolesId, \CodeIgniter\Database\BaseConnection $db): void
    {
        $rol = $db->table(self::TBL_ROL)
            ->where('rolesId', $rolesId)
            ->where('roles_Activo', 1)
            ->get()->getRowArray();

        if (!$rol) {
            throw new \RuntimeException('Rol inválido o inactivo.');
        }
    }

    /**
     * FIX ROL:
     * - Apaga roles activos del usuario
     * - Si existe el mismo rol -> lo activa
     * - Si existe un registro del usuario pero tu tabla es "1 rol por usuario" (unique usuarioId) -> actualiza ese registro
     * - Si no existe -> inserta
     */
    private function setUserRole(int $usuarioId, int $rolesId, \CodeIgniter\Database\BaseConnection $db): void
    {
        // 1) Apagar roles activos
        $db->table(self::TBL_ROLDET)
            ->where('usuarioId', $usuarioId)
            ->update(['rolesDetalle_Activo' => 0]);

        // 2) ¿Ya existe ese rol para el usuario? re-activarlo
        $same = $db->table(self::TBL_ROLDET)
            ->select('rolesDetalleId')
            ->where('usuarioId', $usuarioId)
            ->where('rolesId', $rolesId)
            ->get()->getRowArray();

        if ($same) {
            $ok = $db->table(self::TBL_ROLDET)
                ->where('rolesDetalleId', (int)$same['rolesDetalleId'])
                ->update(['rolesDetalle_Activo' => 1]);

            if (!$ok) {
                throw new \RuntimeException('No se pudo activar el rol existente. DBError=' . json_encode($db->error()));
            }
            return;
        }

        // 3) Si tu tabla solo permite 1 registro por usuario (unique usuarioId),
        // actualiza el último registro del usuario a ese rol.
        $any = $db->table(self::TBL_ROLDET)
            ->select('rolesDetalleId')
            ->where('usuarioId', $usuarioId)
            ->orderBy('rolesDetalleId', 'DESC')
            ->get(1)->getRowArray();

        if ($any) {
            $ok = $db->table(self::TBL_ROLDET)
                ->where('rolesDetalleId', (int)$any['rolesDetalleId'])
                ->update([
                    'rolesId' => $rolesId,
                    'rolesDetalle_Activo' => 1
                ]);

            if (!$ok) {
                throw new \RuntimeException('No se pudo actualizar el rol del usuario. DBError=' . json_encode($db->error()));
            }
            return;
        }

        // 4) Insert normal (si no existe nada)
        $okInsert = $db->table(self::TBL_ROLDET)->insert([
            'usuarioId' => $usuarioId,
            'rolesId' => $rolesId,
            'rolesDetalle_Activo' => 1,
        ]);

        if (!$okInsert) {
            throw new \RuntimeException('No se pudo asignar el rol. DBError=' . json_encode($db->error()));
        }
    }

    // ====== VISTAS ======
    public function create(): string
    {
        $rolModel = new RolModel();

        return view('usuarios/registro', [
            'roles' => $rolModel->where('roles_Activo', 1)->orderBy('roles_Valor', 'ASC')->findAll(),
        ]);
    }

    // ====== INDEX (LISTADO) ======
    public function index(): string
    {
        $db = \Config\Database::connect();

        $estado = (string)($this->request->getGet('estado') ?? 'all'); // '1','0','all'
        $estado = in_array($estado, ['1', '0', 'all'], true) ? $estado : 'all';

        $builder = $db->table(self::TBL_USUARIO . ' u')
            ->select("
                u.usuarioId, u.usuario_nombre, u.usuario_Activo, u.personaId, u.imageId,
                p.persona_Nombre, p.persona_ApllP, p.persona_ApllM, p.persona_FechaNacimiento,
                c.contacto_Valor AS correo,
                r.rolesId, r.roles_Valor,
                img.image_Url
            ")
            ->join(self::TBL_PERSONA . ' p', 'p.personaId = u.personaId', 'inner')
            ->join(self::TBL_CONTACTO . ' c', 'c.personaId = p.personaId AND c.tipocontactoId = ' . self::CONTACTO_EMAIL_ID . ' AND c.contacto_Activo = 1', 'left')
            ->join(self::TBL_ROLDET . ' rd', 'rd.usuarioId = u.usuarioId AND rd.rolesDetalle_Activo = 1', 'left')
            ->join(self::TBL_ROL . ' r', 'r.rolesId = rd.rolesId AND r.roles_Activo = 1', 'left')
            ->join(self::TBL_IMAGE . ' img', 'img.imageId = u.imageId AND img.image_Activo = 1', 'left')
            ->orderBy('u.usuarioId', 'DESC');

        if ($estado !== 'all') {
            $builder->where('u.usuario_Activo', (int)$estado);
        }

        $usuarios = $builder->get()->getResultArray();

        $roles = $db->table(self::TBL_ROL)
            ->where('roles_Activo', 1)
            ->orderBy('roles_Valor', 'ASC')
            ->get()->getResultArray();

        return view('usuarios/index', [
            'usuarios' => $usuarios,
            'roles'    => $roles,
            'estado'   => $estado,
        ]);
    }

    // ====== STORE (REGISTRO) ======
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
            'correo'                  => 'required|valid_email|max_length[80]',
            'rolesId'                 => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('toast_error', array_values($this->validator->getErrors()));
        }

        $nombre  = trim((string)$this->request->getPost('persona_Nombre'));
        $ap      = trim((string)$this->request->getPost('persona_ApllP'));
        $am      = trim((string)$this->request->getPost('persona_ApllM'));
        $fnac    = (string)$this->request->getPost('persona_FechaNacimiento');
        $correo  = strtolower(trim((string)$this->request->getPost('correo')));
        $rolesId = (int)$this->request->getPost('rolesId');

        $db = \Config\Database::connect();

        // correo duplicado
        $correoExistente = $db->table(self::TBL_CONTACTO)
            ->where('tipocontactoId', self::CONTACTO_EMAIL_ID)
            ->where('contacto_Valor', $correo)
            ->where('contacto_Activo', 1)
            ->get()->getRowArray();

        if ($correoExistente) {
            return redirect()->back()->withInput()->with('toast_error', 'Ese correo ya está registrado.');
        }

        // rol válido
        try {
            $this->assertActiveRole($rolesId, $db);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('toast_error', $e->getMessage());
        }

        // imagen
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

        $usuarioGenerado = $this->generateUsername($nombre, $ap, $db);
        $passTemp        = $this->generateTempPassword(10);
        $passHash        = password_hash($passTemp, PASSWORD_DEFAULT);

        $personaModel  = new PersonaModel();
        $contactoModel = new ContactoModel();
        $usuarioModel  = new UsuarioModel();
        $imageModel    = new ImageModel();

        $movedFullPath = null;

        $db->transBegin();

        try {
            // Persona
            $personaId = $personaModel->insert([
                'persona_Nombre'          => $nombre,
                'persona_ApllP'           => $ap,
                'persona_ApllM'           => $am !== '' ? $am : null,
                'persona_FechaNacimiento' => $fnac,
                'persona_Activo'          => 1,
            ], true);

            if (!$personaId) {
                throw new \RuntimeException('No se pudo crear la persona: ' . json_encode($personaModel->errors()));
            }

            // Contacto email
            $okContacto = $contactoModel->insert([
                'personaId'       => (int)$personaId,
                'tipocontactoId'  => self::CONTACTO_EMAIL_ID,
                'contacto_Valor'  => $correo,
                'contacto_Activo' => 1,
            ]);

            if (!$okContacto) {
                throw new \RuntimeException('No se pudo registrar el correo: ' . json_encode($contactoModel->errors()));
            }

            // Imagen
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

                $existing = $db->table(self::TBL_IMAGE)
                    ->select('imageId')
                    ->where('image_Hash', $hash)
                    ->get()->getRowArray();

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

                    if (!$imageId) {
                        throw new \RuntimeException('No se pudo guardar la imagen: ' . json_encode($imageModel->errors()));
                    }
                }
            }

            // Usuario
            $usuarioId = $usuarioModel->insert([
                'usuario_nombre'     => $usuarioGenerado,
                'personaId'          => (int)$personaId,
                'imageId'            => $imageId,
                'usuario_Contrasena' => $passHash,
                'usuario_Activo'     => 1,
            ], true);

            if (!$usuarioId) {
                throw new \RuntimeException('No se pudo crear el usuario: ' . json_encode($usuarioModel->errors()));
            }

       
            $this->setUserRole((int)$usuarioId, (int)$rolesId, $db);

            $db->transCommit();

        } catch (\Throwable $e) {
            $db->transRollback();
            if ($movedFullPath && is_file($movedFullPath)) @unlink($movedFullPath);

            return redirect()->back()->withInput()->with('toast_error', 'Error al registrar: ' . $e->getMessage());
        }

        // Email (no afecta rol)
        $mailer  = new Mailer();
        $loginUrl = site_url('acceso/login');

        $html = "
          <h2>Bienvenido(a)</h2>
          <p>Tu cuenta fue creada correctamente.</p>
          <p>
            <b>Usuario:</b> {$usuarioGenerado}<br>
            <b>Correo:</b> {$correo}<br>
            <b>Contraseña:</b> {$passTemp}
          </p>
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

    // ====== DESACTIVAR ======
    public function deactivate(int $usuarioId): RedirectResponse
    {
        $db = \Config\Database::connect();

        $u = $db->table(self::TBL_USUARIO)->where('usuarioId', $usuarioId)->get()->getRowArray();
        if (!$u) return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', 'Usuario no encontrado.');

        $db->transBegin();
        try {
            $db->table(self::TBL_USUARIO)->where('usuarioId', $usuarioId)->update(['usuario_Activo' => 0]);
            $db->table(self::TBL_ROLDET)->where('usuarioId', $usuarioId)->update(['rolesDetalle_Activo' => 0]);

            $db->transCommit();
            return redirect()->to(site_url('usuarios?estado=all'))->with('toast_success', 'Usuario desactivado.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', 'Error al desactivar: ' . $e->getMessage());
        }
    }

    // ====== ACTIVAR ======
    public function activate(int $usuarioId): RedirectResponse
    {
        $db = \Config\Database::connect();

        $u = $db->table(self::TBL_USUARIO)->where('usuarioId', $usuarioId)->get()->getRowArray();
        if (!$u) return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', 'Usuario no encontrado.');

        $db->transBegin();
        try {
            $db->table(self::TBL_USUARIO)->where('usuarioId', $usuarioId)->update(['usuario_Activo' => 1]);

            // activa el último rol (si hay)
            $last = $db->table(self::TBL_ROLDET)
                ->select('rolesDetalleId')
                ->where('usuarioId', $usuarioId)
                ->orderBy('rolesDetalleId', 'DESC')
                ->get(1)->getRowArray();

            if ($last) {
                $db->table(self::TBL_ROLDET)->where('usuarioId', $usuarioId)->update(['rolesDetalle_Activo' => 0]);
                $db->table(self::TBL_ROLDET)->where('rolesDetalleId', (int)$last['rolesDetalleId'])->update(['rolesDetalle_Activo' => 1]);
            }

            $db->transCommit();
            return redirect()->to(site_url('usuarios?estado=all'))->with('toast_success', 'Usuario activado.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', 'Error al activar: ' . $e->getMessage());
        }
    }

    // ====== UPDATE (EDITAR) ======
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
            'correo'                  => 'required|valid_email|max_length[80]',
            'rolesId'                 => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(site_url('usuarios?estado=all'))
                ->with('toast_error', array_values($this->validator->getErrors()));
        }

        $nombre  = trim((string)$this->request->getPost('persona_Nombre'));
        $ap      = trim((string)$this->request->getPost('persona_ApllP'));
        $am      = trim((string)$this->request->getPost('persona_ApllM'));
        $fnac    = (string)$this->request->getPost('persona_FechaNacimiento');
        $correo  = strtolower(trim((string)$this->request->getPost('correo')));
        $rolesId = (int)$this->request->getPost('rolesId');

        $db = \Config\Database::connect();

        $u = $db->table(self::TBL_USUARIO)->where('usuarioId', $usuarioId)->get()->getRowArray();
        if (!$u) return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', 'Usuario no encontrado.');

        $personaId = (int)$u['personaId'];

        // correo duplicado (otra persona)
        $correoDup = $db->table(self::TBL_CONTACTO)
            ->where('tipocontactoId', self::CONTACTO_EMAIL_ID)
            ->where('contacto_Valor', $correo)
            ->where('contacto_Activo', 1)
            ->where('personaId !=', $personaId)
            ->get()->getRowArray();

        if ($correoDup) {
            return redirect()->to(site_url('usuarios?estado=all'))
                ->with('toast_error', 'Ese correo ya está registrado en otra persona.');
        }

        // rol válido
        try {
            $this->assertActiveRole($rolesId, $db);
        } catch (\Throwable $e) {
            return redirect()->to(site_url('usuarios?estado=all'))->with('toast_error', $e->getMessage());
        }

        // imagen
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

        $personaModel  = new PersonaModel();
        $contactoModel = new ContactoModel();
        $usuarioModel  = new UsuarioModel();
        $imageModel    = new ImageModel();

        $db->transBegin();
        $movedFullPath = null;

        try {
            // persona
            $okPersona = $personaModel->update($personaId, [
                'persona_Nombre'          => $nombre,
                'persona_ApllP'           => $ap,
                'persona_ApllM'           => $am !== '' ? $am : null,
                'persona_FechaNacimiento' => $fnac,
            ]);

            if (!$okPersona) {
                throw new \RuntimeException('No se pudo actualizar la persona: ' . json_encode($personaModel->errors()));
            }

            // correo contacto
            $contacto = $db->table(self::TBL_CONTACTO)
                ->where('personaId', $personaId)
                ->where('tipocontactoId', self::CONTACTO_EMAIL_ID)
                ->where('contacto_Activo', 1)
                ->get()->getRowArray();

            if ($contacto) {
                $okCorreo = $contactoModel->update((int)$contacto['contactoId'], ['contacto_Valor' => $correo]);
            } else {
                $okCorreo = $contactoModel->insert([
                    'personaId'       => $personaId,
                    'tipocontactoId'  => self::CONTACTO_EMAIL_ID,
                    'contacto_Valor'  => $correo,
                    'contacto_Activo' => 1,
                ]);
            }

            if (!$okCorreo) {
                throw new \RuntimeException('No se pudo actualizar el correo: ' . json_encode($contactoModel->errors()));
            }

            // imagen (opcional)
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

                $existing = $db->table(self::TBL_IMAGE)
                    ->select('imageId')
                    ->where('image_Hash', $hash)
                    ->get()->getRowArray();

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

                    if (!$imageId) {
                        throw new \RuntimeException('No se pudo guardar la imagen: ' . json_encode($imageModel->errors()));
                    }
                }

                $okUserImg = $usuarioModel->update($usuarioId, ['imageId' => $imageId]);
                if (!$okUserImg) {
                    throw new \RuntimeException('No se pudo asociar la imagen al usuario: ' . json_encode($usuarioModel->errors()));
                }
            }

         
            $this->setUserRole((int)$usuarioId, (int)$rolesId, $db);

            $db->transCommit();
            return redirect()->to(site_url('usuarios?estado=all'))->with('toast_success', 'Usuario actualizado.');

        } catch (\Throwable $e) {
            $db->transRollback();
            if ($movedFullPath && is_file($movedFullPath)) @unlink($movedFullPath);

            return redirect()->to(site_url('usuarios?estado=all'))
                ->with('toast_error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
}