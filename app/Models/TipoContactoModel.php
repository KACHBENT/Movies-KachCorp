<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoContactoModel extends Model
{
    protected $table            = 'tbl_cat_tipocontacto';
    protected $primaryKey       = 'tipocontactoId';
    protected $useAutoIncrement = false; // NO es auto_increment en tu DDL
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'tipocontactoId',
        'tipocontacto_Valor',
        'tipocontacto_Activo',
        'tipocontacto_FechaCreacion',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'tipocontactoId'    => 'required|is_natural_no_zero',
        'tipocontacto_Valor' => 'required|max_length[50]',
        'tipocontacto_Activo' => 'permit_empty|in_list[0,1]',
    ];
}
