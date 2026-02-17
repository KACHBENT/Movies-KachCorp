<?php

namespace App\Models;

use CodeIgniter\Model;

class ImagenModel extends Model
{
    protected $table        = 'imagenes';
    protected $primaryKey   = 'id_imagen';
    protected $returnType   = 'array';

    protected $allowedFields = [
        'imagen',
        'activo',
        'id_producto',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'imagen'     => 'required|max_length[255]',
        'id_producto'=> 'required|is_natural_no_zero',
        'activo'     => 'permit_empty|in_list[0,1]',
    ];
}
