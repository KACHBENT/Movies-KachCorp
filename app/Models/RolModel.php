<?php

namespace App\Models;

use CodeIgniter\Model;

class RolModel extends Model
{
    protected $table            = 'tbl_cat_roles';
    protected $primaryKey       = 'rolesId';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'roles_Valor',
        'roles_Activo',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'roles_Valor'  => 'required|max_length[50]',
        'roles_Activo' => 'permit_empty|in_list[0,1]',
    ];
}
