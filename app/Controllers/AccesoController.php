<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class AccesoController extends BaseController
{
    public function loginShowForm(): string
    {
        return view('AccesosAdministrativo/Login');
    }

    public function login(): RedirectResponse
    {
        if (!$this->request->is('post')) {
            return redirect()->to(site_url('acceso/login'));
        }

        $correo = strtolower(trim((string) ($this->request->getPost('correo') ?? $this->request->getPost('usuario'))));
        $pass   = (string) $this->request->getPost('password');

        if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('toast_error', 'Ingresa un correo válido.');
        }

        if (strlen($pass) < 6) {
            return redirect()->back()->withInput()->with('toast_error', 'Contraseña inválida.');
        }

        $db = \Config\Database::connect();

  
        $user = $db->table('tbl_rel_usuario usr')
            ->select([
                'usr.usuarioId',
                'usr.usuario_nombre',
                'usr.usuario_Contrasena',
                'usr.usuario_Activo',
                'usr.personaId',
                'usr.imageId',
                'cont.contacto_Valor AS correo',
                'per.persona_Nombre',
                'per.persona_ApllP',
                'per.persona_ApllM',
                'img.image_Url AS imageUrl',
            ])
            ->join('tbl_ope_persona per', 'per.personaId = usr.personaId', 'inner')
            ->join('tbl_rel_contacto cont', 'cont.personaId = per.personaId', 'inner')
            ->join('tbl_ope_image img', 'img.imageId = usr.imageId AND img.image_Activo = 1', 'left')
            ->where('usr.usuario_Activo', 1)
            ->where('cont.contacto_Activo', 1)
            ->where('cont.tipocontactoId', 1)
            ->where('cont.contacto_Valor', $correo)
            ->orderBy('usr.usuarioId', 'DESC')
            ->get(1)
            ->getRowArray();

        if (!$user) {
            return redirect()->back()->withInput()->with('toast_error', 'Correo no encontrado o usuario inactivo.');
        }

        if (!password_verify($pass, (string) $user['usuario_Contrasena'])) {
            return redirect()->back()->withInput()->with('toast_error', 'Correo o contraseña incorrectos.');
        }

        $rolesRows = $db->table('tbl_ope_rolesdetalle rd')
            ->select('r.roles_Valor')
            ->join('tbl_cat_roles r', 'r.rolesId = rd.rolesId', 'inner')
            ->where('rd.usuarioId', (int) $user['usuarioId'])
            ->where('rd.rolesDetalle_Activo', 1)
            ->where('r.roles_Activo', 1)
            ->orderBy('r.roles_Valor', 'ASC')
            ->get()
            ->getResultArray();

        $rolesList = array_map(static fn($x) => (string) $x['roles_Valor'], $rolesRows);
        $rolPrincipal = $rolesList[0] ?? 'Sin rol';
        $nombreCompleto = trim(
            ($user['persona_Nombre'] ?? '') . ' ' .
            ($user['persona_ApllP'] ?? '') . ' ' .
            ($user['persona_ApllM'] ?? '')
        );

        session()->regenerate(true);

        session()->set([
            'isLoggedIn' => true,
            'usuario' => [
                'usuarioId'      => (int) $user['usuarioId'],
                'usuario_nombre' => (string) $user['usuario_nombre'],
                'correo'         => (string) $user['correo'],

                'nombre'         => $nombreCompleto !== '' ? $nombreCompleto : (string) $user['usuario_nombre'],
                'roles'          => $rolesList,
                'rol'            => $rolPrincipal,
                'imageUrl'       => $user['imageUrl'] ?? null,
            ],
        ]);

        return redirect()->to(site_url('/'))
            ->with('toast_success', '¡Bienvenido, ' . ($nombreCompleto !== '' ? $nombreCompleto : $user['usuario_nombre']) . '!');
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to(site_url('acceso/login'));
    }
}
