<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
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

        $rules = [
            'usuario'  => 'required|min_length[3]|max_length[50]',
            'password' => 'required|min_length[6]|max_length[255]',
            
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('toast_error', array_values($this->validator->getErrors()));
        }

        $usuario = trim((string) $this->request->getPost('usuario'));
        $pass    = (string) $this->request->getPost('password');

        $usuarioModel = new UsuarioModel();

        $user = $usuarioModel->where('usuario', $usuario)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('toast_error', 'Usuario no encontrado.');
        }

        if ((int)($user['activo'] ?? 0) !== 1) {
            return redirect()->back()->withInput()->with('toast_error', 'Usuario inactivo.');
        }

        if (!password_verify($pass, (string) $user['password'])) {
            return redirect()->back()->withInput()->with('toast_error', 'Usuario o contraseña incorrectos.');
        }

        session()->regenerate();

        session()->set([
            'isLoggedIn' => true,
            'usuario' => [
                'id_usuario'     => (int) $user['id_usuario'],
                'usuario'        => (string) $user['usuario'],
                'rol'            => (string) $user['rol'],
                'direccion_mac'  => $user['direccion_mac'] ?? null,
            ],
        ]);

        return redirect()->to(site_url('/'))
            ->with('toast_success', '¡Bienvenido, ' . $user['usuario'] . '!');
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to(site_url('acceso/login'));
    }
}
