<?php

namespace App\Controllers;

use App\Models\ImageModel;
use CodeIgniter\HTTP\RedirectResponse;

class PeliculasController extends BaseController
{
    public function index(): string
    {
        $db = \Config\Database::connect();

        $estado = (string) ($this->request->getGet('estado') ?? 'activos');

        $qb = $db->table('tbl_rel_pelicula p')
            ->select('p.*, g.genero_Valor, c.clasificacion_Valor, img.image_Url')
            ->join('tbl_cat_genero g', 'g.generoId = p.generoId', 'inner')
            ->join('tbl_cat_clasificacion c', 'c.clasificacionId = p.clasificacionId', 'inner')
            ->join('tbl_ope_image img', 'img.imageId = p.imageId AND img.image_Activo = 1', 'left')
            ->orderBy('p.peliculaId', 'DESC');

        if ($estado === 'activos') {
            $qb->where('p.pelicula_Activo', 1);
        } elseif ($estado === 'inactivos') {
            $qb->where('p.pelicula_Activo', 0);
        } // todos = sin filtro

        $peliculas = $qb->get()->getResultArray();

        $generos = $db->table('tbl_cat_genero')
            ->where('genero_Activo', 1)
            ->orderBy('genero_Valor', 'ASC')
            ->get()->getResultArray();

        $clasificaciones = $db->table('tbl_cat_clasificacion')
            ->where('clasificacion_Activo', 1)
            ->orderBy('clasificacion_Valor', 'ASC')
            ->get()->getResultArray();

        return view('peliculas/index', [
            'peliculas'       => $peliculas,
            'generos'         => $generos,
            'clasificaciones' => $clasificaciones,
            'estado'          => $estado,
        ]);
    }

    public function store(): RedirectResponse
    {
        if (!$this->request->is('post')) {
            return redirect()->to(site_url('peliculas'));
        }

        $rules = [
            'pelicula_Nombre'       => 'required|min_length[2]|max_length[120]',
            'pelicula_Descripcion'  => 'required|min_length[5]|max_length[200]',
            'pelicula_TrailerUrl'   => 'permit_empty|valid_url|max_length[255]',
            'pelicula_Creacion'     => 'required|valid_date[Y-m-d]',
            'generoId'              => 'required|is_natural_no_zero',
            'clasificacionId'       => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('toast_error', array_values($this->validator->getErrors()));
        }

        $db = \Config\Database::connect();

        $nombre = trim((string) $this->request->getPost('pelicula_Nombre'));
        $desc   = trim((string) $this->request->getPost('pelicula_Descripcion'));
        $trailer= trim((string) $this->request->getPost('pelicula_TrailerUrl'));
        $fecha  = (string) $this->request->getPost('pelicula_Creacion');

        $generoId = (int) $this->request->getPost('generoId');
        $clasificacionId = (int) $this->request->getPost('clasificacionId');

        // ===== Póster opcional
        $file = $this->request->getFile('poster');
        $hasImage = $file && $file->isValid() && !$file->hasMoved() && $file->getSize() > 0;

        $clientName = null;
        $clientMime = null;
        $clientSize = null;

        if ($hasImage) {
            $clientName = $file->getClientName();
            $clientMime = (string) $file->getMimeType();
            $clientSize = (int) $file->getSize();

            $validMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!in_array($clientMime, $validMimes, true)) {
                return redirect()->back()->withInput()->with('toast_error', 'El póster debe ser JPG, PNG o WEBP.');
            }
            if ($file->getSizeByUnit('mb') > 5) {
                return redirect()->back()->withInput()->with('toast_error', 'El póster no debe exceder 5 MB.');
            }
        }

        $imageModel = new ImageModel();

        $db->transBegin();

        $movedFullPath = null;
        $imageId = null;

        try {
            if ($hasImage) {
         
                $tmpPath = $file->getTempName();
                if (!$tmpPath || !is_file($tmpPath)) {
                    throw new \RuntimeException('No se encontró el archivo temporal del póster.');
                }

                $hash = hash_file('sha256', $tmpPath);

                $existing = $db->table('tbl_ope_image')
                    ->select('imageId, image_Url')
                    ->where('image_Hash', $hash)
                    ->where('image_Activo', 1)
                    ->get()->getRowArray();

                if ($existing) {
                    $imageId = (int) $existing['imageId'];
                } else {
  
                    $dir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'peliculas';
                    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                        throw new \RuntimeException('No se pudo crear la carpeta uploads/peliculas.');
                    }

                    $newName = $file->getRandomName();

                    if (!$file->move($dir, $newName)) {
                        throw new \RuntimeException('No se pudo mover el póster.');
                    }

                    $movedFullPath = $dir . DIRECTORY_SEPARATOR . $newName;
                    $relativeUrl   = 'uploads/peliculas/' . $newName;

                    $imageId = $imageModel->insert([
                        'image_Url'       => $relativeUrl,
                        'image_FileName'  => $clientName ?: $newName,
                        'image_Mime'      => $clientMime ?: (mime_content_type($movedFullPath) ?: 'application/octet-stream'),
                        'image_SizeBytes' => $clientSize ?: (int) @filesize($movedFullPath),
                        'image_Hash'      => $hash,
                        'image_Activo'    => 1,
                    ], true);

                    if (!$imageId) {
                        throw new \RuntimeException('No se pudo registrar la imagen en BD.');
                    }
                }
            }


            $ok = $db->table('tbl_rel_pelicula')->insert([
                'imageId'              => $imageId,
                'clasificacionId'      => $clasificacionId,
                'generoId'             => $generoId,
                'pelicula_Nombre'      => $nombre,
                'pelicula_Descripcion' => $desc,
                'pelicula_TrailerUrl'  => $trailer !== '' ? $trailer : null,
                'pelicula_Creacion'    => $fecha,
                'pelicula_Activo'      => 1,
            ]);

            if (!$ok) {
                throw new \RuntimeException('No se pudo guardar la película.');
            }

            $db->transCommit();
            return redirect()->to(site_url('peliculas'))->with('toast_success', 'Película registrada correctamente.');
        } catch (\Throwable $e) {
            $db->transRollback();

            if ($movedFullPath && is_file($movedFullPath)) {
                @unlink($movedFullPath);
            }

            return redirect()->back()->withInput()->with('toast_error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(int $id): RedirectResponse
    {
        if (!$this->request->is('post')) {
            return redirect()->to(site_url('peliculas'));
        }

        $rules = [
            'pelicula_Nombre'       => 'required|min_length[2]|max_length[120]',
            'pelicula_Descripcion'  => 'required|min_length[5]|max_length[200]',
            'pelicula_TrailerUrl'   => 'permit_empty|valid_url|max_length[255]',
            'pelicula_Creacion'     => 'required|valid_date[Y-m-d]',
            'generoId'              => 'required|is_natural_no_zero',
            'clasificacionId'       => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(site_url('peliculas'))
                ->with('toast_error', array_values($this->validator->getErrors()));
        }

        $db = \Config\Database::connect();

        $pelicula = $db->table('tbl_rel_pelicula')->where('peliculaId', $id)->get()->getRowArray();
        if (!$pelicula) {
            return redirect()->to(site_url('peliculas'))->with('toast_error', 'Película no encontrada.');
        }

        $nombre = trim((string) $this->request->getPost('pelicula_Nombre'));
        $desc   = trim((string) $this->request->getPost('pelicula_Descripcion'));
        $trailer= trim((string) $this->request->getPost('pelicula_TrailerUrl'));
        $fecha  = (string) $this->request->getPost('pelicula_Creacion');

        $generoId = (int) $this->request->getPost('generoId');
        $clasificacionId = (int) $this->request->getPost('clasificacionId');

        $file = $this->request->getFile('poster');
        $hasImage = $file && $file->isValid() && !$file->hasMoved() && $file->getSize() > 0;

        $clientName = null;
        $clientMime = null;
        $clientSize = null;

        if ($hasImage) {
            $clientName = $file->getClientName();
            $clientMime = (string) $file->getMimeType();
            $clientSize = (int) $file->getSize();

            $validMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!in_array($clientMime, $validMimes, true)) {
                return redirect()->to(site_url('peliculas'))->with('toast_error', 'El póster debe ser JPG, PNG o WEBP.');
            }
            if ($file->getSizeByUnit('mb') > 5) {
                return redirect()->to(site_url('peliculas'))->with('toast_error', 'El póster no debe exceder 5 MB.');
            }
        }

        $imageModel = new ImageModel();

        $db->transBegin();
        $movedFullPath = null;

        try {
            $imageId = $pelicula['imageId'] ?? null;

            if ($hasImage) {
                $tmpPath = $file->getTempName();
                if (!$tmpPath || !is_file($tmpPath)) {
                    throw new \RuntimeException('No se encontró el archivo temporal del póster.');
                }

                $hash = hash_file('sha256', $tmpPath);

                $existing = $db->table('tbl_ope_image')
                    ->select('imageId')
                    ->where('image_Hash', $hash)
                    ->where('image_Activo', 1)
                    ->get()->getRowArray();

                if ($existing) {
                    $imageId = (int) $existing['imageId'];
                } else {
                    $dir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'peliculas';
                    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                        throw new \RuntimeException('No se pudo crear la carpeta uploads/peliculas.');
                    }

                    $newName = $file->getRandomName();
                    if (!$file->move($dir, $newName)) {
                        throw new \RuntimeException('No se pudo mover el póster.');
                    }

                    $movedFullPath = $dir . DIRECTORY_SEPARATOR . $newName;
                    $relativeUrl   = 'uploads/peliculas/' . $newName;

                    $imageId = $imageModel->insert([
                        'image_Url'       => $relativeUrl,
                        'image_FileName'  => $clientName ?: $newName,
                        'image_Mime'      => $clientMime ?: (mime_content_type($movedFullPath) ?: 'application/octet-stream'),
                        'image_SizeBytes' => $clientSize ?: (int) @filesize($movedFullPath),
                        'image_Hash'      => $hash,
                        'image_Activo'    => 1,
                    ], true);

                    if (!$imageId) {
                        throw new \RuntimeException('No se pudo registrar la imagen en BD.');
                    }
                }
            }

            $ok = $db->table('tbl_rel_pelicula')
                ->where('peliculaId', $id)
                ->update([
                    'imageId'              => $imageId,
                    'clasificacionId'      => $clasificacionId,
                    'generoId'             => $generoId,
                    'pelicula_Nombre'      => $nombre,
                    'pelicula_Descripcion' => $desc,
                    'pelicula_TrailerUrl'  => $trailer !== '' ? $trailer : null,
                    'pelicula_Creacion'    => $fecha,
                ]);

            if (!$ok) {
                throw new \RuntimeException('No se pudo actualizar la película.');
            }

            $db->transCommit();
            return redirect()->to(site_url('peliculas'))->with('toast_success', 'Película actualizada.');
        } catch (\Throwable $e) {
            $db->transRollback();
            if ($movedFullPath && is_file($movedFullPath)) @unlink($movedFullPath);

            return redirect()->to(site_url('peliculas'))->with('toast_error', 'Error: ' . $e->getMessage());
        }
    }

    public function deactivate(int $id): RedirectResponse
    {
        $db = \Config\Database::connect();

        $pelicula = $db->table('tbl_rel_pelicula')->where('peliculaId', $id)->get()->getRowArray();
        if (!$pelicula) {
            return redirect()->to(site_url('peliculas'))->with('toast_error', 'Película no encontrada.');
        }

        $db->table('tbl_rel_pelicula')->where('peliculaId', $id)->update(['pelicula_Activo' => 0]);
        return redirect()->to(site_url('peliculas'))->with('toast_success', 'Película desactivada.');
    }

    public function activate(int $id): RedirectResponse
    {
        $db = \Config\Database::connect();

        $pelicula = $db->table('tbl_rel_pelicula')->where('peliculaId', $id)->get()->getRowArray();
        if (!$pelicula) {
            return redirect()->to(site_url('peliculas'))->with('toast_error', 'Película no encontrada.');
        }

        $db->table('tbl_rel_pelicula')->where('peliculaId', $id)->update(['pelicula_Activo' => 1]);
        return redirect()->to(site_url('peliculas?estado=inactivos'))->with('toast_success', 'Película activada.');
    }

    // Si quieres mantener delete como "desactivar"
    public function delete(int $id): RedirectResponse
    {
        return $this->deactivate($id);
    }
}