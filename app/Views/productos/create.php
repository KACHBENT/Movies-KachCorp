<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">

  <h3 class="mb-4">Nuevo Producto</h3>

  <div class="card shadow-sm">
    <div class="card-body">

      <form method="post" action="<?= base_url('productos/store') ?>">

        <div class="row">

          <div class="col-md-6 mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Categoría</label>
            <select name="categoria_id" class="form-select" required>
              <?php foreach ($categorias ?? [] as $c): ?>
                <option value="<?= $c['id'] ?>">
                  <?= esc($c['nombre']) ?>
                </option>
              <?php endforeach ?>
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Precio</label>
            <input type="number" step="0.01" name="precio" class="form-control" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" required>
          </div>

          <div class="col-12 mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3"></textarea>
          </div>

        </div>

        <button class="btn btn-primary">Guardar</button>
        <a href="<?= base_url('productos') ?>" class="btn btn-secondary">Cancelar</a>

      </form>

    </div>
  </div>

</div>

<?= $this->endSection() ?>
