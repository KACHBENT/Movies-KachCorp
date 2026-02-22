<?php

namespace App\Controllers;

use App\Models\PersonaModel;
use App\Models\UsuarioModel;
use App\Models\ContactoModel;
use App\Models\ImageModel;
use CodeIgniter\HTTP\RedirectResponse;

class PerfilController extends BaseController
{
    private int $TIPO_CONTACTO_EMAIL = 1;

    public function edit(): string
    {
        $usuarioId = (int) (session('usuario.usuarioId') ?? session('usuarioId') ?? 0);

        if ($usuarioId <= 0) {
            return redirect()->to(site_url('acceso/login'))->with('toast_error', 'Inicia sesión.')->send();
        }

        $db = \Config\Database::connect();

        $perfil = $db->table('tbl_rel_usuario u')
            ->select('
                u.usuarioId, u.usuario_nombre, u.personaId, u.imageId,
                p.persona_Nombre, p.persona_ApllP, p.persona_ApllM, p.persona_FechaNacimiento,
                c.contacto_Valor AS correo,
                img.image_Url
            ')
            ->join('tbl_ope_persona p', 'p.personaId = u.personaId', 'inner')
            ->join('tbl_rel_contacto c', 'c.personaId = p.personaId AND c.tipocontactoId = ' . (int)$this->TIPO_CONTACTO_EMAIL . ' AND c.contacto_Activo = 1', 'left')
            ->join('tbl_ope_image img', 'img.imageId = u.imageId AND img.image_Activo = 1', 'left')
            ->where('u.usuarioId', $usuarioId)
            ->get()->getRowArray();

        if (!$perfil) {
            return redirect()->to(site_url('/'))->with('toast_error', 'No se encontró tu perfil.')->send();
        }

        $perfil['image_Url'] = !empty($perfil['image_Url']) ? base_url($perfil['image_Url']) : null;

        return view('perfil/editar', [
            'perfil' => $perfil
        ]);
    }

    public function update(): RedirectResponse
    {
        $usuarioId = (int) (session('usuario.usuarioId') ?? session('usuarioId') ?? 0);

        if ($usuarioId <= 0) {
            return redirect()->to(site_url('acceso/login'))->with('toast_error', 'Inicia sesión.');
        }

        if (!$this->request->is('post')) {
            return redirect()->to(site_url('perfil'));
        }

        $rules = [
            'persona_Nombre'          => 'required|min_length[2]|max_length[50]',
            'persona_ApllP'           => 'required|min_length[2]|max_length[50]',
            'persona_ApllM'           => 'permit_empty|max_length[50]',
            'persona_FechaNacimiento' => 'required|valid_date[Y-m-d]',
            'foto'                    => 'permit_empty|uploaded[foto]|max_size[foto,5120]|ext_in[foto,jpg,jpeg,png,webp]'
        ];

     
        $rules = [
            'persona_Nombre'          => 'required|min_length[2]|max_length[50]',
            'persona_ApllP'           => 'required|min_length[2]|max_length[50]',
            'persona_ApllM'           => 'permit_empty|max_length[50]',
            'persona_FechaNacimiento' => 'required|valid_date[Y-m-d]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('toast_error', array_values($this->validator->getErrors()));
        }

        $nombre = trim((string)$this->request->getPost('persona_Nombre'));
        $ap     = trim((string)$this->request->getPost('persona_ApllP'));
        $am     = trim((string)$this->request->getPost('persona_ApllM'));
        $fnac   = (string)$this->request->getPost('persona_FechaNacimiento');

        $db = \Config\Database::connect();

        $u = $db->table('tbl_rel_usuario')->where('usuarioId', $usuarioId)->get()->getRowArray();
        if (!$u) {
            return redirect()->to(site_url('perfil'))->with('toast_error', 'Usuario no encontrado.');
        }

        $personaId = (int)($u['personaId'] ?? 0);
        if ($personaId <= 0) {
            return redirect()->to(site_url('perfil'))->with('toast_error', 'Persona inválida.');
        }

        // ==== FOTO (opcional) ====
        $file = $this->request->getFile('foto');
        $hasImage = $file && $file->isValid() && !$file->hasMoved() && $file->getSize() > 0;

        $clientName = null;
        $clientMime = null;
        $clientSize = null;
        $movedFullPath = null;

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

        $personaModel = new PersonaModel();
        $usuarioModel = new UsuarioModel();
        $imageModel   = new ImageModel();

        $db->transBegin();

        try {
     
            $okPersona = $personaModel->update($personaId, [
                'persona_Nombre'          => $nombre,
                'persona_ApllP'           => $ap,
                'persona_ApllM'           => $am !== '' ? $am : null,
                'persona_FechaNacimiento' => $fnac,
            ]);

            if (!$okPersona) {
                throw new \RuntimeException('No se pudo actualizar la persona.');
            }

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

                // Reusar imagen si ya existe por hash
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

                    if (!$imageId) {
                        throw new \RuntimeException('No se pudo guardar la imagen en BD.');
                    }
                }

                $okUser = $usuarioModel->update($usuarioId, ['imageId' => $imageId]);
                if (!$okUser) {
                    throw new \RuntimeException('No se pudo asociar la imagen al usuario.');
                }
            }

            $db->transCommit();

            return redirect()->to(site_url('perfil'))
                ->with('toast_success', 'Perfil actualizado correctamente.');

        } catch (\Throwable $e) {
            $db->transRollback();

            if ($movedFullPath && is_file($movedFullPath)) {
                @unlink($movedFullPath);
            }

            return redirect()->back()->withInput()->with('toast_error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
}