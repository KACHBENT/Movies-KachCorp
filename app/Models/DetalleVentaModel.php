<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleVentaModel extends Model
{
    protected $table        = 'detalle_venta';
    protected $primaryKey   = 'id_detalle';
    protected $returnType   = 'array';

    protected $allowedFields = [
        'id_venta',
        'id_producto',
        'cantidad',
        'precio_unitario',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_venta'        => 'required|is_natural_no_zero',
        'id_producto'     => 'required|is_natural_no_zero',
        'cantidad'        => 'required|is_natural_no_zero',
        'precio_unitario' => 'required|decimal|greater_than_equal_to[0]',
    ];
}
