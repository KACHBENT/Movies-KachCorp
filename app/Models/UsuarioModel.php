<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'tbl_rel_usuario';
    protected $primaryKey       = 'usuarioId';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'usuario_nombre',
        'personaId',
        'imageId',
        'usuario_Contrasena',
        'usuario_Activo',
        'usuario_FechaCreacion',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'usuario_nombre'     => 'required|min_length[3]|max_length[50]',
        'personaId'          => 'required|is_natural_no_zero',
        'imageId'            => 'permit_empty|is_natural_no_zero',
        'usuario_Contrasena' => 'required|max_length[255]',
        'usuario_Activo'     => 'permit_empty|in_list[0,1]',
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (!isset($data['data']['usuario_Contrasena'])) {
            return $data;
        }

        $pass = (string) $data['data']['usuario_Contrasena'];

        // Si ya parece hash (bcrypt/argon), no rehasear
        if (str_starts_with($pass, '$2y$') || str_starts_with($pass, '$argon2')) {
            return $data;
        }

        $data['data']['usuario_Contrasena'] = password_hash($pass, PASSWORD_DEFAULT);
        return $data;
    }
}
