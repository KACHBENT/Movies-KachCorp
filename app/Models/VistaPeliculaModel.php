<?php

namespace App\Models;

use CodeIgniter\Model;

class VistaPeliculaModel extends Model
{
    protected $table            = 'tbl_rel_vistapelicula';
    protected $primaryKey       = 'vistapeliculaId';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'peliculaId',
        'vistapelicula_UrlMega',
        'vistapelicula_Descripcion',
        'vistapelicula_Activo',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'peliculaId'               => 'required|is_natural_no_zero',
        'vistapelicula_UrlMega'    => 'required',
        'vistapelicula_Descripcion'=> 'required',
        'vistapelicula_Activo'     => 'permit_empty|in_list[0,1]',
    ];
}
