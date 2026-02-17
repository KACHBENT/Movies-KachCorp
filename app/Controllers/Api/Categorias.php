<?php

namespace App\Controllers\Api;

use App\Models\CategoriaModel;
use CodeIgniter\RESTful\ResourceController;

class Categorias extends ResourceController
{
    protected $modelName = CategoriaModel::class;
    protected $format    = 'json';

    public function index()
    {
        $activo = $this->request->getGet('activo');
        if ($activo !== null && $activo !== '') {
            return $this->respond(
                $this->model->where('activo', (int)$activo)->findAll()
            );
        }
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $row = $this->model->find($id);
        if (!$row) return $this->failNotFound('Categoría no encontrada');
        return $this->respond($row);
    }

    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        // defaults
        $data['activo'] = isset($data['activo']) ? (int)$data['activo'] : 1;
        $data['fecha_creacion'] = $data['fecha_creacion'] ?? date('Y-m-d H:i:s');

        if (!$this->model->insert($data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        $id = $this->model->getInsertID();
        return $this->respondCreated([
            'message' => 'Categoría creada',
            'data'    => $this->model->find($id),
        ]);
    }

    public function update($id = null)
    {
        $exists = $this->model->find($id);
        if (!$exists) return $this->failNotFound('Categoría no encontrada');

        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        if (!$data) return $this->failValidationErrors('Sin datos para actualizar');

        $data['id_categoria'] = (int)$id;

        if (!$this->model->save($data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respond([
            'message' => 'Categoría actualizada',
            'data'    => $this->model->find($id),
        ]);
    }

    public function delete($id = null)
    {
        $exists = $this->model->find($id);
        if (!$exists) return $this->failNotFound('Categoría no encontrada');

        $this->model->delete($id);
        return $this->respondDeleted(['message' => 'Categoría eliminada']);
    }
}
