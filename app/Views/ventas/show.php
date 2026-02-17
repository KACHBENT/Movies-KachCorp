<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">

  <h3 class="mb-4">Detalle de Venta #<?= $venta['id'] ?></h3>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <p><strong>Fecha:</strong> <?= $venta['fecha'] ?></p>
      <p><strong>Total:</strong> $<?= number_format($venta['total'],2) ?></p>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body table-responsive">

      <table class="table table-striped">
        <thead>
          <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($detalles ?? [] as $d): ?>
            <tr>
              <td><?= esc($d['producto_nombre']) ?></td>
              <td><?= $d['cantidad'] ?></td>
              <td>$<?= number_format($d['precio'],2) ?></td>
              <td>$<?= number_format($d['cantidad'] * $d['precio'],2) ?></td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>

    </div>
  </div>

</div>

<?= $this->endSection() ?>
