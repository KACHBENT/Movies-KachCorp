<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class PeliculasController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $rows = $db->table('tbl_rel_pelicula p')
            ->select("
                p.peliculaId,
                p.pelicula_Nombre,
                p.pelicula_Descripcion,
                p.pelicula_TrailerUrl,
                p.pelicula_Creacion,
                p.pelicula_Activo,
                g.generoId,
                g.genero_Valor,
                c.clasificacionId,
                c.clasificacion_Valor,
                img.imageId,
                img.image_Url
            ")
            ->join('tbl_cat_genero g', 'g.generoId = p.generoId', 'inner')
            ->join('tbl_cat_clasificacion c', 'c.clasificacionId = p.clasificacionId', 'inner')
            ->join('tbl_ope_image img', 'img.imageId = p.imageId AND img.image_Activo = 1', 'left')
            ->where('p.pelicula_Activo', 1)
            ->orderBy('p.peliculaId', 'DESC')
            ->get()->getResultArray();

        foreach ($rows as &$r) {
            $r['image_Url'] = !empty($r['image_Url']) ? base_url($r['image_Url']) : null;
        }

        return $this->response->setJSON([
            'ok' => true,
            'count' => count($rows),
            'data' => $rows
        ]);
    }

    public function show(int $id)
    {
        $db = \Config\Database::connect();

        $row = $db->table('tbl_rel_pelicula p')
            ->select("
                p.peliculaId,
                p.pelicula_Nombre,
                p.pelicula_Descripcion,
                p.pelicula_TrailerUrl,
                p.pelicula_Creacion,
                p.pelicula_Activo,
                g.generoId,
                g.genero_Valor,
                c.clasificacionId,
                c.clasificacion_Valor,
                img.imageId,
                img.image_Url
            ")
            ->join('tbl_cat_genero g', 'g.generoId = p.generoId', 'inner')
            ->join('tbl_cat_clasificacion c', 'c.clasificacionId = p.clasificacionId', 'inner')
            ->join('tbl_ope_image img', 'img.imageId = p.imageId AND img.image_Activo = 1', 'left')
            ->where('p.peliculaId', $id)
            ->get(1)->getRowArray();

        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok' => false,
                'message' => 'Película no encontrada.'
            ]);
        }

        $row['image_Url'] = !empty($row['image_Url']) ? base_url($row['image_Url']) : null;

        return $this->response->setJSON([
            'ok' => true,
            'data' => $row
        ]);
    }
}