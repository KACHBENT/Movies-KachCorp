<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Uploads extends Controller
{
    public function show(string $path)
    {
        $path = str_replace("\0", '', $path);

        $baseDir = realpath(WRITEPATH . 'uploads');
        if ($baseDir === false) {
            return $this->response->setStatusCode(500)->setBody('Uploads dir not found');
        }

        $file = WRITEPATH . 'uploads/' . $path;
        $real = realpath($file);
        if ($real === false || strpos($real, $baseDir) !== 0 || !is_file($real)) {
            return $this->response->setStatusCode(404)->setBody('Archivo no encontrado');
        }

        $mime = mime_content_type($real) ?: 'application/octet-stream';

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($real) . '"')
            ->setBody(file_get_contents($real));
    }
}
