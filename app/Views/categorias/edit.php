<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">

  <h3 class="mb-4">Editar Categoría</h3>

  <div class="card shadow-sm">
    <div class="card-body">

      <form method="post" action="<?= base_url('categorias/update/'.$categoria['id']) ?>">

        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input type="text"
                 value="<?= esc($categoria['nombre']) ?>"
                 name="nombre"
                 class="form-control"
                 required>
        </div>

        <button class="btn btn-warning">Actualizar</button>
        <a href="<?= base_url('categorias') ?>" class="btn btn-secondary">Cancelar</a>

      </form>

    </div>
  </div>

</div>

<?= $this->endSection() ?>
