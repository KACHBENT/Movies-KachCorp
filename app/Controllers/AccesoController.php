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

        $login = trim((string)($this->request->getPost('correo') ?? $this->request->getPost('usuario') ?? ''));
        $pass  = (string)$this->request->getPost('password');

        if ($login === '') {
            return redirect()->back()->withInput()->with('toast_error', 'Ingresa tu correo o usuario.');
        }

        if (strlen($pass) < 6) {
            return redirect()->back()->withInput()->with('toast_error', 'Contraseña inválida.');
        }

        $db = \Config\Database::connect();

        $loginNorm = strtolower(trim($login));
        $isEmail = (bool) filter_var($loginNorm, FILTER_VALIDATE_EMAIL);

        $builder = $db->table('tbl_rel_usuario usr')
            ->select([
                'usr.usuarioId',
                'usr.usuario_nombre',
                'usr.usuario_Contrasena',
                'usr.usuario_Activo',
                'usr.personaId',
                'usr.imageId',
                'per.persona_Nombre',
                'per.persona_ApllP',
                'per.persona_ApllM',
                'cont.contacto_Valor AS correo',
                'img.image_Url AS imageUrl',
            ])
            ->join('tbl_ope_persona per', 'per.personaId = usr.personaId', 'inner')
            ->join('tbl_ope_image img', 'img.imageId = usr.imageId AND img.image_Activo = 1', 'left')
            ->where('usr.usuario_Activo', 1)
            ->orderBy('usr.usuarioId', 'DESC');

        if ($isEmail) {
            $builder->join(
                'tbl_rel_contacto cont',
                "cont.personaId = per.personaId
                 AND cont.tipocontactoId = 1
                 AND cont.contacto_Activo = 1",
                'inner'
            );
            $builder->where("LOWER(TRIM(cont.contacto_Valor)) =", $loginNorm);

        } else {
 
            $builder->join(
                'tbl_rel_contacto cont',
                "cont.personaId = per.personaId
                 AND cont.tipocontactoId = 1
                 AND cont.contacto_Activo = 1",
                'left'
            );

            $builder->where('usr.usuario_nombre', $loginNorm);
        }

        $user = $builder->get(1)->getRowArray();

        if (!$user) {
            return redirect()->back()->withInput()->with('toast_error', 'Usuario/correo no encontrado o inactivo.');
        }

        if (!password_verify($pass, (string)$user['usuario_Contrasena'])) {
            return redirect()->back()->withInput()->with('toast_error', 'Usuario/correo o contraseña incorrectos.');
        }

        $rolesRows = $db->table('tbl_ope_rolesdetalle rd')
            ->select(['r.roles_Valor'])
            ->join('tbl_cat_roles r', 'r.rolesId = rd.rolesId AND r.roles_Activo = 1', 'inner')
            ->where('rd.usuarioId', (int)$user['usuarioId'])
            ->where('rd.rolesDetalle_Activo', 1)
            ->orderBy('r.roles_Valor', 'ASC')
            ->get()
            ->getResultArray();

        $rolesList = array_map(static fn($x) => strtolower((string)$x['roles_Valor']), $rolesRows);
        $rolPrincipal = $rolesList[0] ?? 'sin rol';

        $nombreCompleto = trim(
            ($user['persona_Nombre'] ?? '') . ' ' .
            ($user['persona_ApllP'] ?? '') . ' ' .
            ($user['persona_ApllM'] ?? '')
        );

        $avatarUrl = !empty($user['imageUrl']) ? base_url($user['imageUrl']) : null;

        session()->regenerate(true);

        session()->set([
            'auth_logged_in' => true,
            'isLoggedIn'     => true,
            'usuario' => [
                'usuarioId'      => (int)$user['usuarioId'],
                'usuario_nombre' => (string)$user['usuario_nombre'],
                'correo'         => (string)($user['correo'] ?? ''),

                'nombre'         => $nombreCompleto !== '' ? $nombreCompleto : (string)$user['usuario_nombre'],
                'roles'          => $rolesList,
                'rol'            => $rolPrincipal,
                'imageUrl'       => $avatarUrl,
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