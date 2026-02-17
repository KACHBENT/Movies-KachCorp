<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaModel extends Model
{
    protected $table        = 'ventas';
    protected $primaryKey   = 'id_venta';
    protected $returnType   = 'array';

    protected $allowedFields = [
        'id_usuario',
        'fecha',
        'tipo',
        'total',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'id_usuario' => 'permit_empty|is_natural_no_zero',
        'tipo'       => 'required|in_list[efectivo,tarjeta]',
        'total'      => 'required|decimal|greater_than_equal_to[0]',
    ];
}
