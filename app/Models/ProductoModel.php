<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoModel extends Model
{
    protected $table        = 'productos';
    protected $primaryKey   = 'id_producto';
    protected $returnType   = 'array';

    protected $allowedFields = [
        'detalle',
        'marca',
        'modelo',
        'capacidad',
        'precio',
        'descripcion',
        'stock',
        'activo',
        'id_categoria',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'precio'      => 'required|decimal|greater_than_equal_to[0]',
        'stock'       => 'permit_empty|is_natural',
        'id_categoria'=> 'permit_empty|is_natural_no_zero',
        'activo'      => 'permit_empty|in_list[0,1]',
    ];
}
