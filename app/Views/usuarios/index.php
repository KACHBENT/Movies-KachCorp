<?= $this->extend('layout/main') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('css/inicio.styles.css') ?>">
<style>
    .wrap {
        max-width: 1200px;
        margin: 0 auto
    }

    .cardx {
        background: rgba(255, 255, 255, .92);
        border-radius: 18px;
        padding: 16px 18px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, .08)
    }

    .thumb {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        object-fit: cover;
        border: 1px solid rgba(0, 0, 0, .12)
    }

    .thumbPh {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f3f3;
        border: 1px dashed #cfcfcf
    }

    .table td {
        vertical-align: middle
    }

    .posterBox {
        width: 86px;
        height: 86px;
        border-radius: 18px;
        overflow: hidden;
        background: #f1f1f1;
        border: 1px dashed #cfcfcf;
        display: flex;
        align-items: center;
        justify-content: center
    }

    .posterBox img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none
    }

    .rounded-4 {
        border-radius: 18px !important
    }

    .actions .btn {
        border-radius: 14px
    }

    .modal {
        color: black
    }

    .filters .btn {
        border-radius: 14px
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('navbar') ?>
<?= $this->include('layout/NavBar') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $estado = $estado ?? 'all'; ?>

<div class="wrap">

    <div class="cardx mb-3 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="m-0 fw-bold text-black text-center">Usuarios</h2>
        </div>

        <button class="btn btn-dark rounded-4" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bi bi-plus-circle me-1"></i> Nuevo usuario
        </button>
    </div>

    <div class="cardx mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="filters d-flex gap-2">
                <a class="btn btn-outline-dark btn-sm <?= $estado === 'all' ? 'active' : '' ?>"
                    href="<?= site_url('usuarios?estado=all') ?>">Todos</a>
                <a class="btn btn-outline-dark btn-sm <?= $estado === '1' ? 'active' : '' ?>"
                    href="<?= site_url('usuarios?estado=1') ?>">Activos</a>
                <a class="btn btn-outline-dark btn-sm <?= $estado === '0' ? 'active' : '' ?>"
                    href="<?= site_url('usuarios?estado=0') ?>">Inactivos</a>
            </div>

            <div class="text-muted small">
                Tip: “Eliminar” aquí significa <b>desactivar</b> (soft delete).
            </div>
        </div>
    </div>

    <div class="cardx">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay usuarios.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($usuarios as $u): ?>
                        <?php
                        $nombreCompleto = trim(($u['persona_Nombre'] ?? '') . ' ' . ($u['persona_ApllP'] ?? '') . ' ' . ($u['persona_ApllM'] ?? ''));
                        $correo = $u['correo'] ?? '';
                        $rol = $u['roles_Valor'] ?? '—';
                        $img = $u['image_Url'] ?? '';
                        $activo = (int) ($u['usuario_Activo'] ?? 0);
                        ?>
                        <tr class="<?= $activo === 1 ? '' : 'table-light' ?>">
                            <td>
                                <?php if ($img): ?>
                                    <img class="thumb" src="<?= base_url($img) ?>" alt="foto">
                                <?php else: ?>
                                    <div class="thumbPh">👤</div>
                                <?php endif; ?>
                            </td>

                            <td class="fw-semibold"><?= esc($nombreCompleto) ?></td>
                            <td><?= esc($correo ?: '—') ?></td>
                            <td><?= esc($u['usuario_nombre'] ?? '') ?></td>
                            <td><?= esc($rol) ?></td>

                            <td>
                                <?php if ($activo === 1): ?>
                                    <span class="badge text-bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-end actions">
                                <!-- Editar (solo si está activo, tú decides; si lo quieres editable aún inactivo quita la condición) -->
                                <button class="btn btn-outline-primary btn-sm btnEdit" data-bs-toggle="modal"
                                    data-bs-target="#modalEdit" data-id="<?= (int) $u['usuarioId'] ?>"
                                    data-nombre="<?= esc($u['persona_Nombre'] ?? '') ?>"
                                    data-ap="<?= esc($u['persona_ApllP'] ?? '') ?>"
                                    data-am="<?= esc($u['persona_ApllM'] ?? '') ?>"
                                    data-fn="<?= esc($u['persona_FechaNacimiento'] ?? '') ?>"
                                    data-correo="<?= esc($correo) ?>" data-rolid="<?= (int) ($u['rolesId'] ?? 0) ?>"
                                    data-image="<?= $img ? base_url($img) : '' ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Toggle: activar / desactivar -->
                                <button class="btn btn-outline-warning btn-sm btnToggle" data-bs-toggle="modal"
                                    data-bs-target="#modalToggle" data-id="<?= (int) $u['usuarioId'] ?>"
                                    data-estado="<?= $activo ?>" data-nombre="<?= esc($nombreCompleto) ?>">
                                    <?php if ($activo === 1): ?>
                                        <i class="bi bi-lock"></i>
                                    <?php else: ?>
                                        <i class="bi bi-unlock"></i>
                                    <?php endif; ?>
                                </button>

                                <!-- compat: si quieres mantener el modal rojo para desactivar -->
                                <button class="btn btn-outline-danger btn-sm btnDelete" data-bs-toggle="modal"
                                    data-bs-target="#modalDelete" data-id="<?= (int) $u['usuarioId'] ?>"
                                    data-nombre="<?= esc($nombreCompleto) ?>" <?= $activo === 1 ? '' : 'disabled' ?>
                                    title="<?= $activo === 1 ? 'Desactivar (soft)' : 'Ya está inactivo' ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
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
                <h5 class="modal-title fw-bold">Nuevo usuario</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="<?= site_url('usuarios/store') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre</label>
                            <input name="persona_Nombre" class="form-control" maxlength="50" required
                                value="<?= esc(old('persona_Nombre')) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de nacimiento</label>
                            <input type="date" name="persona_FechaNacimiento" class="form-control" required
                                value="<?= esc(old('persona_FechaNacimiento')) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido paterno</label>
                            <input name="persona_ApllP" class="form-control" maxlength="50" required
                                value="<?= esc(old('persona_ApllP')) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido materno (opcional)</label>
                            <input name="persona_ApllM" class="form-control" maxlength="50"
                                value="<?= esc(old('persona_ApllM')) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo</label>
                            <input type="email" name="correo" class="form-control" maxlength="50" required
                                value="<?= esc(old('correo')) ?>">
                            <small class="text-muted">Aquí se enviarán las credenciales.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rol</label>
                            <select name="rolesId" class="form-select" required>
                                <option value="">-- Selecciona --</option>
                                <?php foreach (($roles ?? []) as $r): ?>
                                    <option value="<?= (int) $r['rolesId'] ?>" <?= old('rolesId') == $r['rolesId'] ? 'selected' : '' ?>>
                                        <?= esc($r['roles_Valor']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Foto (opcional)</label>
                            <input type="file" name="foto" class="form-control"
                                accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted">JPG/PNG/WEBP · Máx 5MB.</small>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info rounded-4 m-0">
                                <b>Nota:</b> El sistema generará automáticamente <b>usuario</b> y <b>contraseña
                                    temporal</b> y las enviará por correo.
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancelar</button>
                    <button class="btn btn-dark" type="submit">Crear y enviar correo</button>
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
                <h5 class="modal-title fw-bold">Editar usuario</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formEdit" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-12 d-flex gap-3 align-items-center">
                            <div class="posterBox">
                                <img id="editPreview" src="" alt="foto">
                                <span id="editPreviewPh">👤</span>
                            </div>

                            <div class="flex-grow-1">
                                <label class="form-label fw-semibold m-0">Cambiar foto (opcional)</label>
                                <input id="editFoto" type="file" name="foto" class="form-control mt-2"
                                    accept="image/jpeg,image/png,image/webp">
                                <small class="text-muted">Si no eliges imagen, se conserva la actual.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre</label>
                            <input id="editNombre" name="persona_Nombre" class="form-control" maxlength="50" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de nacimiento</label>
                            <input id="editFn" type="date" name="persona_FechaNacimiento" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido paterno</label>
                            <input id="editAp" name="persona_ApllP" class="form-control" maxlength="50" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido materno</label>
                            <input id="editAm" name="persona_ApllM" class="form-control" maxlength="50">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo</label>
                            <input id="editCorreo" type="email" name="correo" class="form-control" maxlength="50"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rol</label>
                            <select id="editRol" name="rolesId" class="form-select" required>
                                <option value="">-- Selecciona --</option>
                                <?php foreach (($roles ?? []) as $r): ?>
                                    <option value="<?= (int) $r['rolesId'] ?>"><?= esc($r['roles_Valor']) ?></option>
                                <?php endforeach; ?>
                            </select>
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

<!-- ================== MODAL TOGGLE (ACTIVAR / DESACTIVAR) ================== -->
<div class="modal fade" id="modalToggle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="toggleTitle">Cambiar estado</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formToggle" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p class="m-0" id="toggleText"></p>
                    <small class="text-muted">Esto no borra el usuario, solo lo marca activo/inactivo.</small>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancelar</button>
                    <button class="btn btn-warning" type="submit" id="toggleBtn">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================== MODAL DELETE (COMPAT: DESACTIVAR) ================== -->
<div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Desactivar usuario</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formDelete" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p class="m-0">¿Seguro que deseas desactivar <b id="deleteNombre"></b>?</p>
                    <small class="text-muted">Se marcará como inactivo (no se borra físicamente).</small>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancelar</button>
                    <button class="btn btn-danger" type="submit">Desactivar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('footer') ?>
<?= $this->include('layout/Footer') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function () {
        // ===== EDIT fill
        const formEdit = document.getElementById('formEdit');

        const editNombre = document.getElementById('editNombre');
        const editAp = document.getElementById('editAp');
        const editAm = document.getElementById('editAm');
        const editFn = document.getElementById('editFn');
        const editCorreo = document.getElementById('editCorreo');
        const editRol = document.getElementById('editRol');

        const img = document.getElementById('editPreview');
        const ph = document.getElementById('editPreviewPh');
        const editFoto = document.getElementById('editFoto');

        document.querySelectorAll('.btnEdit').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                formEdit.action = "<?= site_url('usuarios/update') ?>/" + id;

                editNombre.value = btn.dataset.nombre || '';
                editAp.value = btn.dataset.ap || '';
                editAm.value = btn.dataset.am || '';
                editFn.value = btn.dataset.fn || '';
                editCorreo.value = btn.dataset.correo || '';
                editRol.value = btn.dataset.rolid || '';

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

                if (editFoto) editFoto.value = '';
            });
        });

        // preview live
        if (editFoto) {
            editFoto.addEventListener('change', () => {
                const f = editFoto.files && editFoto.files[0];
                if (!f) return;
                const url = URL.createObjectURL(f);
                img.src = url;
                img.style.display = 'block';
                ph.style.display = 'none';
            });
        }

        // ===== DELETE (compat) -> desactivar
        const formDelete = document.getElementById('formDelete');
        const deleteNombre = document.getElementById('deleteNombre');

        document.querySelectorAll('.btnDelete').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const nombre = btn.dataset.nombre || '';
                // si ya cambiaste route, apunta a desactivar:
                formDelete.action = "<?= site_url('usuarios/desactivar') ?>/" + id;
                deleteNombre.textContent = nombre;
            });
        });

        // ===== TOGGLE activar/desactivar
        const formToggle = document.getElementById('formToggle');
        const toggleTitle = document.getElementById('toggleTitle');
        const toggleText = document.getElementById('toggleText');
        const toggleBtn = document.getElementById('toggleBtn');

        document.querySelectorAll('.btnToggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const estado = parseInt(btn.dataset.estado || '1', 10);
                const nombre = btn.dataset.nombre || '';

                if (estado === 1) {
                    formToggle.action = "<?= site_url('usuarios/desactivar') ?>/" + id;
                    toggleTitle.textContent = 'Desactivar usuario';
                    toggleText.innerHTML = `¿Seguro que deseas <b>desactivar</b> a <b>${nombre}</b>?`;
                    toggleBtn.textContent = 'Desactivar';
                } else {
                    formToggle.action = "<?= site_url('usuarios/activar') ?>/" + id;
                    toggleTitle.textContent = 'Activar usuario';
                    toggleText.innerHTML = `¿Seguro que deseas <b>activar</b> a <b>${nombre}</b>?`;
                    toggleBtn.textContent = 'Activar';
                }
            });
        });
    })();
</script>
<?= $this->endSection() ?>