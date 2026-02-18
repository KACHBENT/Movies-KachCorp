<?php

namespace App\Models;

use CodeIgniter\Model;

class ClasificacionModel extends Model
{
    protected $table            = 'tbl_cat_clasificacion';
    protected $primaryKey       = 'clasificacionId';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'clasificacion_Valor',
        'clasificacion_Activo',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'clasificacion_Valor'  => 'required|max_length[50]',
        'clasificacion_Activo' => 'permit_empty|in_list[0,1]',
    ];
}
