<?php

namespace App\Controllers\Api;

use App\Models\VentaModel;
use App\Models\DetalleVentaModel;
use App\Models\ProductoModel;
use App\Models\ImagenModel;
use CodeIgniter\RESTful\ResourceController;

class Ventas extends ResourceController
{
    protected $modelName = VentaModel::class;
    protected $format = 'json';

    protected DetalleVentaModel $detalleModel;
    protected ProductoModel $productoModel;
    protected ImagenModel $imagenModel;

    public function __construct()
    {
        $this->detalleModel = new DetalleVentaModel();
        $this->productoModel = new ProductoModel();
        $this->imagenModel = new ImagenModel();
    }

    public function index()
    {
        return $this->respond($this->model->orderBy('id_venta', 'DESC')->findAll());
    }

    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $data['fecha'] = $data['fecha'] ?? date('Y-m-d H:i:s');
        $data['total'] = $data['total'] ?? 0;

        if (!$this->model->insert($data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        $id = (int) $this->model->getInsertID();
        return $this->respondCreated([
            'message' => 'Venta creada',
            'data' => $this->model->find($id),
        ]);
    }

    public function show($id = null)
    {
        $venta = $this->model->find($id);
        if (!$venta)
            return $this->failNotFound('Venta no encontrada');

        $detalles = $this->detalleModel
            ->select('detalle_venta.*, productos.detalle, productos.marca, productos.modelo, productos.precio AS precio_producto')
            ->join('productos', 'productos.id_producto = detalle_venta.id_producto', 'inner')
            ->where('detalle_venta.id_venta', (int) $id)
            ->findAll();

        $productIds = array_values(array_unique(array_map(fn($d) => (int) $d['id_producto'], $detalles)));
        $imgsByProduct = [];

        if (count($productIds) > 0) {
            $imgs = $this->imagenModel
                ->whereIn('id_producto', $productIds)
                ->where('activo', 1)
                ->findAll();

            foreach ($imgs as $img) {
                $pid = (int) $img['id_producto'];

                $file = (string) ($img['imagen'] ?? '');
                $file = preg_replace('#^uploads/#', '', $file);

                $img['url'] = base_url('uploads/' . ltrim($file, '/'));
                $imgsByProduct[$pid][] = $img;
            }

        }

        $total = 0;
        foreach ($detalles as &$d) {
            $lineTotal = ((float) $d['precio_unitario']) * ((int) $d['cantidad']);
            $d['line_total'] = $lineTotal;
            $d['imagenes'] = $imgsByProduct[(int) $d['id_producto']] ?? [];
            $total += $lineTotal;
        }
        unset($d);
        if ((float) $venta['total'] !== (float) $total) {
            $this->model->update((int) $id, ['total' => $total]);
            $venta['total'] = $total;
        }

        $venta['detalles'] = $detalles;

        return $this->respond($venta);
    }

    public function delete($id = null)
    {
        $venta = $this->model->find($id);
        if (!$venta)
            return $this->failNotFound('Venta no encontrada');

        // borrar detalles
        $this->detalleModel->where('id_venta', (int) $id)->delete();

        $this->model->delete((int) $id);
        return $this->respondDeleted(['message' => 'Venta eliminada']);
    }
}
