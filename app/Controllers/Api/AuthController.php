<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class AuthController extends BaseController
{
    public function login()
    {
        $correo = trim((string)($this->request->getPost('correo') ?? $this->request->getJSON(true)['correo'] ?? ''));
        $pass   = (string)($this->request->getPost('password') ?? $this->request->getJSON(true)['password'] ?? '');

        if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'ok' => false,
                'message' => 'Correo inválido.'
            ])->setStatusCode(400);
        }

        if (strlen($pass) < 6) {
            return $this->response->setJSON([
                'ok' => false,
                'message' => 'Contraseña inválida.'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();

        $user = $db->table('tbl_rel_usuario usr')
            ->select('usr.usuarioId, usr.usuario_nombre, usr.usuario_Contrasena, usr.usuario_Activo, usr.imageId, cont.contacto_Valor AS correo, img.image_Url')
            ->join('tbl_ope_persona per', 'per.personaId = usr.personaId', 'inner')
            ->join('tbl_rel_contacto cont', 'cont.personaId = per.personaId', 'inner')
            ->join('tbl_ope_image img', 'img.imageId = usr.imageId AND img.image_Activo = 1', 'left')
            ->where('usr.usuario_Activo', 1)
            ->where('cont.contacto_Activo', 1)
            ->where('cont.tipocontactoId', 1)
            ->where('cont.contacto_Valor', $correo)
            ->get()->getRowArray();

        if (!$user) {
            return $this->response->setJSON([
                'ok' => false,
                'message' => 'Correo no encontrado o usuario inactivo.'
            ])->setStatusCode(401);
        }

        if (!password_verify($pass, (string)$user['usuario_Contrasena'])) {
            return $this->response->setJSON([
                'ok' => false,
                'message' => 'Correo o contraseña incorrectos.'
            ])->setStatusCode(401);
        }

        // Roles activos
        $roles = $db->table('tbl_ope_rolesdetalle rd')
            ->select('r.rolesId, r.roles_Valor')
            ->join('tbl_cat_roles r', 'r.rolesId = rd.rolesId', 'inner')
            ->where('rd.usuarioId', (int)$user['usuarioId'])
            ->where('rd.rolesDetalle_Activo', 1)
            ->where('r.roles_Activo', 1)
            ->get()->getResultArray();

        $rolesList = array_map(fn($x) => [
            'rolesId' => (int)$x['rolesId'],
            'roles_Valor' => (string)$x['roles_Valor']
        ], $roles);

        return $this->response->setJSON([
            'ok' => true,
            'message' => 'Login correcto.',
            'data' => [
                'usuarioId' => (int)$user['usuarioId'],
                'usuario_nombre' => (string)$user['usuario_nombre'],
                'correo' => (string)$user['correo'],
                'image_Url' => $user['image_Url'] ? base_url($user['image_Url']) : null,
                'roles' => $rolesList,
            ]
        ]);
    }
}