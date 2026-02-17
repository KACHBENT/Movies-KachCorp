<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container">

  <h2 class="mb-4 fw-bold">Panel de Administración</h2>

  <div class="row g-4">

    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body text-center">
          <i class="bi bi-box-seam fs-1 text-primary"></i>
          <h5 class="mt-3">Productos</h5>
          <a href="<?= base_url('productos') ?>" class="btn btn-outline-primary btn-sm mt-2">
            Gestionar
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body text-center">
          <i class="bi bi-tags fs-1 text-success"></i>
          <h5 class="mt-3">Categorías</h5>
          <a href="<?= base_url('categorias') ?>" class="btn btn-outline-success btn-sm mt-2">
            Gestionar
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body text-center">
          <i class="bi bi-cash-stack fs-1 text-warning"></i>
          <h5 class="mt-3">Ventas</h5>
          <a href="<?= base_url('ventas') ?>" class="btn btn-outline-warning btn-sm mt-2">
            Ver ventas
          </a>
        </div>
      </div>
    </div>

  </div>
</div>

<?= $this->endSection() ?>
