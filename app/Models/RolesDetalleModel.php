<?php

namespace App\Models;

use CodeIgniter\Model;

class RolesDetalleModel extends Model
{
    protected $table            = 'tbl_ope_rolesDetalle';
    protected $primaryKey       = 'rolesDetalleId';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'usuarioId',
        'rolesId',
        'rolesDetalle_FechaCreacion',
        'rolesDetalle_Activo',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'usuarioId'            => 'required|is_natural_no_zero',
        'rolesId'              => 'required|is_natural_no_zero',
        'rolesDetalle_Activo'  => 'permit_empty|in_list[0,1]',
    ];
}
