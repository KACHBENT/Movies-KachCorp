<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id_usuario';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'usuario',
        'password',
        'rol',
        'direccion_mac',
        'activo',
        'fecha_creacion',
    ];

    protected $useTimestamps    = false;
    protected $validationRules = [
        'usuario' => 'required|min_length[3]|max_length[50]|is_unique[usuarios.usuario,id_usuario,{id_usuario}]',
        'password'=> 'required|min_length[6]|max_length[255]',
        'rol'     => 'required|in_list[admin,cliente]',
        'activo'  => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'usuario' => [
            'is_unique' => 'Ese usuario ya existe.',
        ],
    ];
}
