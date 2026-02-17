<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container">

  <div class="d-flex justify-content-between mb-3">
    <h3>Gestión de Productos</h3>
    <a href="<?= base_url('productos/create') ?>" class="btn btn-primary">
      <i class="bi bi-plus"></i> Nuevo
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body table-responsive">

      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($productos ?? [] as $p): ?>
            <tr>
              <td><?= $p['id'] ?></td>
              <td><?= esc($p['nombre']) ?></td>
              <td>$<?= number_format($p['precio'],2) ?></td>
              <td><?= $p['stock'] ?></td>
              <td>
                <a href="<?= base_url('productos/edit/'.$p['id']) ?>"
                   class="btn btn-sm btn-warning">
                  Editar
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
