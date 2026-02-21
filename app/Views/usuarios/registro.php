<?= $this->extend('layout/main') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('css/inicio.styles.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/registerusers.styles.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('navbar') ?>
<?= $this->include('layout/NavBar') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="reg-wrap">

  <div class="reg-head">
    <h1 class="text-center">Registro de usuario</h1>
    
  </div>

  <form action="<?= site_url('usuarios/registro') ?>" method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>

    <div class="cardx">
      <h3>Datos de la persona</h3>

      <div class="grid">
        <div class="field">
          <label for="persona_Nombre">Nombre</label>
          <input id="persona_Nombre" type="text" name="persona_Nombre" maxlength="50"
            value="<?= esc(old('persona_Nombre')) ?>" required>
        </div>

        <div class="field">
          <label for="persona_FechaNacimiento">Fecha de nacimiento</label>
          <input id="persona_FechaNacimiento" type="date" name="persona_FechaNacimiento"
            value="<?= esc(old('persona_FechaNacimiento')) ?>" required>
        </div>

        <div class="field">
          <label for="persona_ApllP">Apellido paterno</label>
          <input id="persona_ApllP" type="text" name="persona_ApllP" maxlength="50"
            value="<?= esc(old('persona_ApllP')) ?>" required>
        </div>

        <div class="field">
          <label for="persona_ApllM">Apellido materno (opcional)</label>
          <input id="persona_ApllM" type="text" name="persona_ApllM" maxlength="50"
            value="<?= esc(old('persona_ApllM')) ?>">
        </div>
      </div>
    </div>
    <div class="cardx">
      <h3>Datos de acceso</h3>

      <div class="grid">
        <div class="field">
          <label for="correo">Correo</label>
          <input id="correo" type="email" name="correo" maxlength="50" value="<?= esc(old('correo')) ?>" required>
          <small>Se guarda como contacto tipo <b>correo</b> (tipocontactoId = 1).</small>
        </div>

        <div class="field">
          <label for="rolesId">Rol</label>
          <select id="rolesId" name="rolesId" required>
            <option value="">-- Selecciona --</option>
            <?php foreach (($roles ?? []) as $r): ?>
              <option value="<?= (int) $r['rolesId'] ?>" <?= old('rolesId') == $r['rolesId'] ? 'selected' : '' ?>>
                <?= esc($r['roles_Valor']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <small>El rol se asigna en <b>tbl_ope_rolesDetalle</b>.</small>
        </div>
      </div>

      <div class="grid-1" style="margin-top:12px;">
        <div class="field">
          <label for="foto">Imagen</label>

          <div class="preview">
            <div class="avatar" id="avatarBox">
              <span style="opacity:.6;"> <img id="toggleIcon" src="<?= base_url('images/icons/photo_camera_front.svg') ?>" class="icon-form darken"
                    alt="Ocultar" width="20" height="20"></span>
            </div>

            <div style="flex:1;">
              <input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp">
              <small>Formatos: JPG/PNG/WEBP · Máximo 5MB.</small>
            </div>
          </div>

        </div>
      </div>

      <div class="grid-1" style="margin-top:12px;">
      </div>
    </div>

    <div class="cardx">
      <div class="actions">
        <a class="btnx btnx-light" href="<?= site_url('acceso/login') ?>">Cancelar</a>
        <button class="btnx btnx-primary" type="submit">Registrar</button>
      </div>
    </div>

  </form>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
  (function () {
    const input = document.getElementById('foto');
    const box = document.getElementById('avatarBox');
    if (!input || !box) return;

    input.addEventListener('change', () => {
      const f = input.files && input.files[0];
      if (!f) {
        box.innerHTML = '<span style="opacity:.6;">📷</span>';
        return;
      }
      const url = URL.createObjectURL(f);
      box.innerHTML = '<img alt="preview" src="' + url + '">';
    });
  })();
</script>
<?= $this->endSection() ?>