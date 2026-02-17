<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center align-items-center vh-100">
  <div class="col-md-4">
    <div class="card shadow-lg border-0">
      <div class="card-body p-4">

        <h3 class="text-center mb-4 fw-bold">
          #SHOPSYSTEMCRAZY
        </h3>

        <form method="post" action="<?= site_url('acceso/login') ?>">

          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" name="usuario" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <button class="btn btn-primary w-100">
            Iniciar sesión
          </button>

        </form>

      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
