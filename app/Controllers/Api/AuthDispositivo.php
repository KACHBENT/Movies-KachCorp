<?php

namespace App\Controllers\Api;

use App\Models\UsuarioModel;
use CodeIgniter\RESTful\ResourceController;

class AuthDispositivo extends ResourceController
{
    protected $format = 'json';

    public function deviceLogin()
    {
        $usuarioModel = new UsuarioModel();

        $device = trim((string) $this->request->getHeaderLine('X-MAC-Address'));
        if ($device === '') {
            $payload = $this->request->getJSON(true) ?? $this->request->getPost();
            $device = trim((string)($payload['device_id'] ?? $payload['mac'] ?? ''));
        }

        if ($device === '') {
            return $this->failValidationErrors('Falta X-MAC-Address (o device_id en body).');
        }

        $device = strtoupper($device);
        $device = str_replace('-', ':', $device);
        $device = preg_replace('/\s+/', '', $device);

        $macRegex     = '/^([0-9A-F]{2}:){5}[0-9A-F]{2}$/';
        $androidRegex = '/^[0-9A-F]{16,64}$/'; 

        $deviceNoSep = str_replace(':', '', $device);

        $esMac       = (bool) preg_match($macRegex, $device);
        $esAndroidId = (bool) preg_match($androidRegex, $deviceNoSep);

        if (!$esMac && !$esAndroidId) {
            return $this->failValidationErrors('ID inválido. Envía MAC (AA:BB:CC:DD:EE:FF) o ANDROID_ID (hex).');
        }

        $deviceKey = $esMac ? $device : ('AID:' . $deviceNoSep);

        $user = $usuarioModel->where('direccion_mac', $deviceKey)->first();
        if (!$user) {
            $usuario = 'cli_' . substr(hash('sha1', $deviceKey), 0, 10);
            $plainPass = bin2hex(random_bytes(6)); 
            $hashPass  = password_hash($plainPass, PASSWORD_DEFAULT);

            $insert = [
                'usuario'        => $usuario,
                'password'       => $hashPass,
                'rol'            => 'cliente',
                'direccion_mac'  => $deviceKey,
                'activo'         => 1,
                'fecha_creacion' => date('Y-m-d H:i:s'),
            ];

            if (!$usuarioModel->insert($insert)) {
                return $this->failValidationErrors($usuarioModel->errors());
            }

            $user = $usuarioModel->find($usuarioModel->getInsertID());
        }

        return $this->respond([
            'message' => 'Sesión por dispositivo OK',
            'data' => [
                'id_usuario'    => (int)$user['id_usuario'],
                'usuario'       => (string)$user['usuario'],
                'rol'           => (string)$user['rol'],
                'direccion_mac' => (string)$user['direccion_mac'],
            ],
        ]);
    }
}
