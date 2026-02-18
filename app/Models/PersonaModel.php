<?php

namespace App\Models;

use CodeIgniter\Model;

class PersonaModel extends Model
{
    protected $table            = 'tbl_ope_persona';
    protected $primaryKey       = 'personaId';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'persona_Nombre',
        'persona_ApllP',
        'persona_ApllM',
        'persona_FechaNacimiento',
        'persona_Activo',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'persona_Nombre'          => 'required|min_length[2]|max_length[50]',
        'persona_ApllP'           => 'required|min_length[2]|max_length[50]',
        'persona_ApllM'           => 'permit_empty|max_length[50]',
        'persona_FechaNacimiento' => 'required|valid_date[Y-m-d]',
        'persona_Activo'          => 'permit_empty|in_list[0,1]',
    ];
}
