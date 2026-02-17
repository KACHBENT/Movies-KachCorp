<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container">

  <div class="d-flex justify-content-between mb-3">
    <h3>Gestión de Categorías</h3>
    <a href="<?= base_url('categorias/create') ?>" class="btn btn-success">
      <i class="bi bi-plus"></i> Nueva
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">

      <table class="table table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categorias ?? [] as $c): ?>
            <tr>
              <td><?= $c['id'] ?></td>
              <td><?= esc($c['nombre']) ?></td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>

    </div>
  </div>

</div>

<?= $this->endSection() ?>
