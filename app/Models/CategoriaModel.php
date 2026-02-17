<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model
{
    protected $table          = 'categorias';
    protected $primaryKey     = 'id_categoria';
    protected $returnType     = 'array';

    protected $allowedFields  = [
        'nombre',
        'activo',
        'fecha_creacion',
    ];

    protected $useTimestamps  = false;

    protected $validationRules = [
        'nombre' => 'required|min_length[2]|max_length[100]',
        'activo' => 'permit_empty|in_list[0,1]',
    ];
}
