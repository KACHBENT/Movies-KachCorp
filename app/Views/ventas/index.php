<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container">

  <h3 class="mb-4">Ventas Registradas</h3>

  <div class="card shadow-sm">
    <div class="card-body table-responsive">

      <table class="table table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Detalle</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ventas ?? [] as $v): ?>
            <tr>
              <td><?= $v['id'] ?></td>
              <td><?= $v['fecha'] ?></td>
              <td>$<?= number_format($v['total'],2) ?></td>
              <td>
                <a href="<?= base_url('ventas/'.$v['id']) ?>"
                   class="btn btn-sm btn-info">
                  Ver
                </a>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>

    </div>
  </div>

</div>

<?= $this->endSection() ?>
