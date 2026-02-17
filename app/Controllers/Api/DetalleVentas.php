<?php

namespace App\Controllers\Api;

use App\Models\VentaModel;
use App\Models\DetalleVentaModel;
use App\Models\ProductoModel;
use CodeIgniter\RESTful\ResourceController;

class DetalleVentas extends ResourceController
{
    protected $format = 'json';

    protected VentaModel $ventaModel;
    protected DetalleVentaModel $detalleModel;
    protected ProductoModel $productoModel;

    public function __construct()
    {
        $this->ventaModel    = new VentaModel();
        $this->detalleModel  = new DetalleVentaModel();
        $this->productoModel = new ProductoModel();
    }

    public function index($idVenta = null)
    {
        $venta = $this->ventaModel->find($idVenta);
        if (!$venta) return $this->failNotFound('Venta no encontrada');

        $detalles = $this->detalleModel
            ->select('detalle_venta.*, productos.detalle, productos.marca, productos.modelo')
            ->join('productos', 'productos.id_producto = detalle_venta.id_producto', 'inner')
            ->where('detalle_venta.id_venta', (int)$idVenta)
            ->findAll();

        return $this->respond([
            'venta'    => $venta,
            'detalles' => $detalles,
        ]);
    }

    public function create($idVenta = null)
    {
        $venta = $this->ventaModel->find($idVenta);
        if (!$venta) return $this->failNotFound('Venta no encontrada');

        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $items = $payload['items'] ?? null;

        if (!is_array($items) || count($items) === 0) {
            return $this->failValidationErrors('Debes enviar "items" como arreglo.');
        }

        $db = db_connect();
        $db->transBegin();

        try {
            foreach ($items as $it) {
                $idProducto = (int)($it['id_producto'] ?? 0);
                $cantidad   = (int)($it['cantidad'] ?? 0);

                if ($idProducto <= 0 || $cantidad <= 0) {
                    throw new \RuntimeException('Cada item requiere id_producto y cantidad (>0).');
                }

                $producto = $this->productoModel->find($idProducto);
                if (!$producto) {
                    throw new \RuntimeException("Producto no existe: {$idProducto}");
                }

                if ((int)($producto['activo'] ?? 1) !== 1) {
                    throw new \RuntimeException("Producto inactivo: {$idProducto}");
                }

                $precioUnitario = (float)$producto['precio'];

                $row = [
                    'id_venta'        => (int)$idVenta,
                    'id_producto'     => $idProducto,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precioUnitario,
                ];

                if (!$this->detalleModel->insert($row)) {
                    $errors = $this->detalleModel->errors();
                    throw new \RuntimeException('Error insert detalle: ' . json_encode($errors));
                }
            }

            $detalles = $this->detalleModel->where('id_venta', (int)$idVenta)->findAll();

            $total = 0;
            foreach ($detalles as $d) {
                $total += ((float)$d['precio_unitario']) * ((int)$d['cantidad']);
            }

            $this->ventaModel->update((int)$idVenta, ['total' => $total]);

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transacción inválida.');
            }

            $db->transCommit();
            return $this->respondCreated([
                'message' => 'Detalles registrados y total actualizado',
                'id_venta'=> (int)$idVenta,
                'total'   => $total,
                'next'    => "/api/ventas/{$idVenta}",
            ]);

        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->failValidationErrors($e->getMessage());
        }
    }
}
