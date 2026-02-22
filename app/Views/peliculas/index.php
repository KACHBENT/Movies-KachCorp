<?= $this->extend('layout/main') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('css/inicio.styles.css') ?>">
<style>
  .wrap{max-width:1200px;margin:0 auto;color:#111}
  .cardx{background:rgba(255,255,255,.92);border-radius:18px;padding:16px 18px;box-shadow:0 10px 24px rgba(0,0,0,.08)}
  .thumb{width:56px;height:56px;border-radius:14px;object-fit:cover;border:1px solid rgba(0,0,0,.12)}
  .thumbPh{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;background:#f3f3f3;border:1px dashed #cfcfcf}
  .table td{vertical-align:middle}
  .rounded-4{border-radius:18px!important}
  .actions .btn{border-radius:14px}
  .modal{color:#111}
  .posterBox{width:86px;height:86px;border-radius:18px;overflow:hidden;background:#f1f1f1;border:1px dashed #cfcfcf;display:flex;align-items:center;justify-content:center}
  .posterBox img{width:100%;height:100%;object-fit:cover;display:none}

  /* Pills estado */
  .pill{display:inline-flex;align-items:center;gap:8px;padding:6px 12px;border-radius:999px;font-weight:800;font-size:.85rem}
  .pill-ok{background:#e9f7ef;color:#0f6b3a;border:1px solid rgba(15,107,58,.18)}
  .pill-off{background:#f3f4f6;color:#374151;border:1px solid rgba(55,65,81,.18)}

  /* Tabs filtros */
  .tabs{display:flex;gap:10px;flex-wrap:wrap}
  .tabbtn{border:1px solid rgba(0,0,0,.14);background:#fff;border-radius:999px;padding:8px 12px;font-weight:800;text-decoration:none;color:#111;transition:.15s}
  .tabbtn:hover{transform:translateY(-1px)}
  .tabbtn.active{background:#111;color:#fff;border-color:#111}
</style>
<?= $this->endSection() ?>

<?= $this->section('navbar') ?>
<?= $this->include('layout/NavBar') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $estado = $estado ?? (string)($_GET['estado'] ?? 'activos'); ?>

<div class="wrap">

  <div class="cardx mb-3 d-flex justify-content-between align-items-center">
    <div>
      <h2 class="m-0 fw-bold">Películas</h2>
    </div>

    <button class="btn btn-dark rounded-4" data-bs-toggle="modal" data-bs-target="#modalCreate">
      <i class="bi bi-plus-circle me-1"></i> Nueva película
    </button>
  </div>

  <!-- Filtros -->
  <div class="cardx mb-3">
    <div class="tabs">
      <a class="tabbtn <?= $estado === 'todos' ? 'active' : '' ?>" href="<?= site_url('peliculas?estado=todos') ?>">Todos</a>
      <a class="tabbtn <?= $estado === 'activos' ? 'active' : '' ?>" href="<?= site_url('peliculas?estado=activos') ?>">Activos</a>
      <a class="tabbtn <?= $estado === 'inactivos' ? 'active' : '' ?>" href="<?= site_url('peliculas?estado=inactivos') ?>">Inactivos</a>
    </div>
  </div>

  <div class="cardx">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Póster</th>
            <th>Nombre</th>
            <th>Género</th>
            <th>Clasificación</th>
            <th>Creación</th>
            <th>Tráiler</th>
            <th>Estado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>

        <tbody>
        <?php if (empty($peliculas)): ?>
          <tr>
            <td colspan="8" class="text-center text-muted py-4">No hay películas registradas.</td>
          </tr>
        <?php endif; ?>

        <?php foreach ($peliculas as $p): ?>
          <?php
            $isActive = ((int)($p['pelicula_Activo'] ?? 0) === 1);
            $posterUrl = !empty($p['image_Url']) ? base_url($p['image_Url']) : '';
            $trailerUrl = (string)($p['pelicula_TrailerUrl'] ?? '');
          ?>
          <tr>
            <td>
              <?php if ($posterUrl): ?>
                <img class="thumb" src="<?= esc($posterUrl) ?>" alt="poster">
              <?php else: ?>
                <div class="thumbPh">🎬</div>
              <?php endif; ?>
            </td>

            <td class="fw-semibold"><?= esc($p['pelicula_Nombre'] ?? '') ?></td>
            <td><?= esc($p['genero_Valor'] ?? '—') ?></td>
            <td><?= esc($p['clasificacion_Valor'] ?? '—') ?></td>
            <td><?= esc($p['pelicula_Creacion'] ?? '') ?></td>

            <td>
              <?php if ($trailerUrl !== ''): ?>
                <a class="btn btn-outline-dark btn-sm rounded-4"
                   href="<?= esc($trailerUrl) ?>" target="_blank" rel="noopener">
                  <i class="bi bi-play-circle"></i> Ver
                </a>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>

            <td>
              <?php if ($isActive): ?>
                <span class="pill pill-ok"><i class="bi bi-check-circle-fill"></i> Activo</span>
              <?php else: ?>
                <span class="pill pill-off"><i class="bi bi-slash-circle"></i> Inactivo</span>
              <?php endif; ?>
            </td>

            <td class="text-end actions">
              <!-- Edit -->
              <button class="btn btn-outline-primary btn-sm btnEdit"
                      data-bs-toggle="modal" data-bs-target="#modalEdit"
                      data-id="<?= (int)$p['peliculaId'] ?>"
                      data-nombre="<?= esc($p['pelicula_Nombre'] ?? '') ?>"
                      data-desc="<?= esc($p['pelicula_Descripcion'] ?? '') ?>"
                      data-fecha="<?= esc($p['pelicula_Creacion'] ?? '') ?>"
                      data-genero="<?= (int)($p['generoId'] ?? 0) ?>"
                      data-clasificacion="<?= (int)($p['clasificacionId'] ?? 0) ?>"
                      data-trailer="<?= esc($trailerUrl) ?>"
                      data-image="<?= esc($posterUrl) ?>">
                <i class="bi bi-pencil-square"></i>
              </button>

              <?php if ($isActive): ?>
                <!-- Desactivar -->
                <button class="btn btn-outline-warning btn-sm btnDeactivate"
                        data-bs-toggle="modal" data-bs-target="#modalDeactivate"
                        data-id="<?= (int)$p['peliculaId'] ?>"
                        data-nombre="<?= esc($p['pelicula_Nombre'] ?? '') ?>">
                  <i class="bi bi-lock"></i>
                </button>

                <!-- Delete (alias desactivar) -->
                <button class="btn btn-outline-danger btn-sm btnDelete"
                        data-bs-toggle="modal" data-bs-target="#modalDelete"
                        data-id="<?= (int)$p['peliculaId'] ?>"
                        data-nombre="<?= esc($p['pelicula_Nombre'] ?? '') ?>">
                  <i class="bi bi-trash"></i>
                </button>
              <?php else: ?>
                <!-- Activar -->
                <button class="btn btn-outline-success btn-sm btnActivate"
                        data-bs-toggle="modal" data-bs-target="#modalActivate"
                        data-id="<?= (int)$p['peliculaId'] ?>"
                        data-nombre="<?= esc($p['pelicula_Nombre'] ?? '') ?>">
                  <i class="bi bi-unlock"></i>
                </button>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>

      </table>
    </div>
  </div>

</div>

<!-- ================== MODAL CREATE ================== -->
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4">

      <div class="modal-header">
        <h5 class="modal-title fw-bold">Nueva película</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="<?= site_url('peliculas/store') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="modal-body">
          <div class="row g-3">

            <div class="col-md-8">
              <label class="form-label fw-semibold">Nombre</label>
              <input name="pelicula_Nombre" class="form-control" maxlength="120"
                     value="<?= esc(old('pelicula_Nombre')) ?>" required>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Creación</label>
              <input type="date" name="pelicula_Creacion" class="form-control"
                     value="<?= esc(old('pelicula_Creacion')) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Género</label>
              <select name="generoId" class="form-select" required>
                <option value="">-- Selecciona --</option>
                <?php foreach (($generos ?? []) as $g): ?>
                  <option value="<?= (int)$g['generoId'] ?>" <?= old('generoId') == $g['generoId'] ? 'selected' : '' ?>>
                    <?= esc($g['genero_Valor']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Clasificación</label>
              <select name="clasificacionId" class="form-select" required>
                <option value="">-- Selecciona --</option>
                <?php foreach (($clasificaciones ?? []) as $c): ?>
                  <option value="<?= (int)$c['clasificacionId'] ?>" <?= old('clasificacionId') == $c['clasificacionId'] ? 'selected' : '' ?>>
                    <?= esc($c['clasificacion_Valor']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="pelicula_Descripcion" class="form-control" maxlength="200" rows="3" required><?= esc(old('pelicula_Descripcion')) ?></textarea>
              <small class="text-muted">Máximo 200 caracteres.</small>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">URL del tráiler</label>
              <input type="url" name="pelicula_TrailerUrl" class="form-control"
                     placeholder="https://youtube.com/watch?v=..."
                     value="<?= esc(old('pelicula_TrailerUrl')) ?>">
              <small class="text-muted">Debe incluir https://</small>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Póster (opcional)</label>
              <input type="file" name="poster" class="form-control" accept="image/jpeg,image/png,image/webp">
              <small class="text-muted">JPG/PNG/WEBP · Máx 5MB.</small>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-dark" type="submit">Guardar</button>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- ================== MODAL EDIT ================== -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4">

      <div class="modal-header">
        <h5 class="modal-title fw-bold">Editar película</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="formEdit" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="modal-body">
          <div class="row g-3">

            <div class="col-12 d-flex gap-3 align-items-center">
              <div class="posterBox">
                <img id="editPreview" src="" alt="poster">
                <span id="editPreviewPh">🎬</span>
              </div>

              <div class="flex-grow-1">
                <label class="form-label fw-semibold m-0">Cambiar póster</label>
                <input id="editPoster" type="file" name="poster" class="form-control mt-2" accept="image/jpeg,image/png,image/webp">
                <small class="text-muted">Si no eliges un póster, se conserva el actual.</small>
              </div>
            </div>

            <div class="col-md-8">
              <label class="form-label fw-semibold">Nombre</label>
              <input id="editNombre" name="pelicula_Nombre" class="form-control" maxlength="120" required>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Creación</label>
              <input id="editFecha" type="date" name="pelicula_Creacion" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Género</label>
              <select id="editGenero" name="generoId" class="form-select" required>
                <option value="">-- Selecciona --</option>
                <?php foreach (($generos ?? []) as $g): ?>
                  <option value="<?= (int)$g['generoId'] ?>"><?= esc($g['genero_Valor']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Clasificación</label>
              <select id="editClasificacion" name="clasificacionId" class="form-select" required>
                <option value="">-- Selecciona --</option>
                <?php foreach (($clasificaciones ?? []) as $c): ?>
                  <option value="<?= (int)$c['clasificacionId'] ?>"><?= esc($c['clasificacion_Valor']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea id="editDesc" name="pelicula_Descripcion" class="form-control" maxlength="200" rows="3" required></textarea>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">URL del tráiler (opcional)</label>
              <input id="editTrailer" type="url" name="pelicula_TrailerUrl" class="form-control" placeholder="https://youtube.com/watch?v=...">
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-dark" type="submit">Actualizar</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- ================== MODAL DELETE (alias desactivar) ================== -->
<div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">

      <div class="modal-header">
        <h5 class="modal-title fw-bold">Desactivar película</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="formDelete" method="post">
        <?= csrf_field() ?>

        <div class="modal-body">
          <p class="m-0">¿Seguro que deseas desactivar <b id="deleteNombre"></b>?</p>
          <small class="text-muted">No se borra físicamente, solo se marca como inactiva.</small>
        </div>

        <div class="modal-footer">
          <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-danger" type="submit">Desactivar</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- ================== MODAL DEACTIVATE ================== -->
<div class="modal fade" id="modalDeactivate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">

      <div class="modal-header">
        <h5 class="modal-title fw-bold">Desactivar película</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="formDeactivate" method="post">
        <?= csrf_field() ?>

        <div class="modal-body">
          <p class="m-0">¿Seguro que deseas desactivar <b id="deactivateNombre"></b>?</p>
          <small class="text-muted">La película ya no aparecerá en “Activos”.</small>
        </div>

        <div class="modal-footer">
          <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-warning" type="submit">Desactivar</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- ================== MODAL ACTIVATE ================== -->
<div class="modal fade" id="modalActivate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">

      <div class="modal-header">
        <h5 class="modal-title fw-bold">Activar película</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="formActivate" method="post">
        <?= csrf_field() ?>

        <div class="modal-body">
          <p class="m-0">¿Seguro que deseas activar <b id="activateNombre"></b>?</p>
          <small class="text-muted">Volverá a aparecer en “Activos”.</small>
        </div>

        <div class="modal-footer">
          <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-success" type="submit">Activar</button>
        </div>
      </form>

    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
  // ===== Edit fill
  const formEdit = document.getElementById('formEdit');
  const editNombre = document.getElementById('editNombre');
  const editDesc = document.getElementById('editDesc');
  const editFecha = document.getElementById('editFecha');
  const editGenero = document.getElementById('editGenero');
  const editClasificacion = document.getElementById('editClasificacion');
  const editTrailer = document.getElementById('editTrailer');

  const img = document.getElementById('editPreview');
  const ph = document.getElementById('editPreviewPh');
  const editPoster = document.getElementById('editPoster');

  document.querySelectorAll('.btnEdit').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      formEdit.action = "<?= site_url('peliculas/update') ?>/" + id;

      editNombre.value = btn.dataset.nombre || '';
      editDesc.value = btn.dataset.desc || '';
      editFecha.value = btn.dataset.fecha || '';
      editGenero.value = btn.dataset.genero || '';
      editClasificacion.value = btn.dataset.clasificacion || '';
      editTrailer.value = btn.dataset.trailer || '';

      const image = btn.dataset.image || '';
      if (image) {
        img.src = image;
        img.style.display = 'block';
        ph.style.display = 'none';
      } else {
        img.src = '';
        img.style.display = 'none';
        ph.style.display = 'inline';
      }
      if (editPoster) editPoster.value = '';
    });
  });

  if (editPoster) {
    editPoster.addEventListener('change', () => {
      const f = editPoster.files && editPoster.files[0];
      if (!f) return;
      const url = URL.createObjectURL(f);
      img.src = url;
      img.style.display = 'block';
      ph.style.display = 'none';
    });
  }

  // ===== Delete (alias desactivar)
  const formDelete = document.getElementById('formDelete');
  const deleteNombre = document.getElementById('deleteNombre');

  document.querySelectorAll('.btnDelete').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      const nombre = btn.dataset.nombre || '';
      formDelete.action = "<?= site_url('peliculas/desactivar') ?>/" + id;
      deleteNombre.textContent = nombre;
    });
  });

  // ===== Deactivate
  const formDeactivate = document.getElementById('formDeactivate');
  const deactivateNombre = document.getElementById('deactivateNombre');

  document.querySelectorAll('.btnDeactivate').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      const nombre = btn.dataset.nombre || '';
      formDeactivate.action = "<?= site_url('peliculas/desactivar') ?>/" + id;
      deactivateNombre.textContent = nombre;
    });
  });

  // ===== Activate
  const formActivate = document.getElementById('formActivate');
  const activateNombre = document.getElementById('activateNombre');

  document.querySelectorAll('.btnActivate').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      const nombre = btn.dataset.nombre || '';
      formActivate.action = "<?= site_url('peliculas/activar') ?>/" + id;
      activateNombre.textContent = nombre;
    });
  });

})();
</script>
<?= $this->endSection() ?>