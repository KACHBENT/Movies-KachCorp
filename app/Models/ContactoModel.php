<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactoModel extends Model
{
    protected $table            = 'tbl_rel_contacto';
    protected $primaryKey       = 'contactoId';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'personaId',
        'tipocontactoId',
        'contacto_Valor',
        'contacto_Activo',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'personaId'       => 'required|is_natural_no_zero',
        'tipocontactoId'  => 'required|is_natural_no_zero',
        'contacto_Valor'  => 'required|max_length[50]',
        'contacto_Activo' => 'permit_empty|in_list[0,1]',
    ];
}
