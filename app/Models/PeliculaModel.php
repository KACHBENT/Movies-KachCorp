<?php

namespace App\Models;

use CodeIgniter\Model;

class PeliculaModel extends Model
{
    protected $table            = 'tbl_rel_pelicula';
    protected $primaryKey       = 'peliculaId';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'imageId',
        'clasificacionId',
        'generoId',
        'pelicula_Nombre',
        'pelicula_Descripcion',
        'pelicula_Creacion',
        'pelicula_Activo',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'imageId'             => 'permit_empty|is_natural_no_zero',
        'clasificacionId'     => 'required|is_natural_no_zero',
        'generoId'            => 'required|is_natural_no_zero',
        'pelicula_Nombre'     => 'required|max_length[120]',
        'pelicula_Descripcion'=> 'required|max_length[200]',
        'pelicula_Creacion'   => 'required|valid_date[Y-m-d]',
        'pelicula_Activo'     => 'permit_empty|in_list[0,1]',
    ];
}
