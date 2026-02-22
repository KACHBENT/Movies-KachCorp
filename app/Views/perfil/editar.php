<?= $this->extend('layout/main') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('css/perfil.styles.css') ?>">
<?= $this->endSection() ?>
<?= $this->section('navbar') ?>
<?= $this->include('layout/NavBar') ?>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<main id="content" class="container">

  <div class="perfil-wrap">

    <header class="perfil-head">
      <h2 class="perfil-title">Mi perfil</h2>
      <p class="perfil-subtitle">Actualiza tus datos del personales del perfil</p>
    </header>

    <?php
      // El controlador manda $perfil
      $p = $perfil ?? [];
      $nombre = $p['persona_Nombre'] ?? '';
      $ap     = $p['persona_ApllP'] ?? '';
      $am     = $p['persona_ApllM'] ?? '';
      $fnac   = $p['persona_FechaNacimiento'] ?? '';
      $correo = $p['correo'] ?? '';
      $imgUrl = $p['image_Url'] ?? null;
      $usernm = $p['usuario_nombre'] ?? '';
    ?>

    <section class="perfil-card">

      <form action="<?= site_url('perfil') ?>" method="post" enctype="multipart/form-data" class="perfil-form">
        <?= csrf_field() ?>

        <div class="perfil-grid">
          <div class="perfil-avatar">
            <div class="avatar-box">
              <img
                id="avatarPreview"
                src="<?= $imgUrl ?: base_url('images/default-user.webp') ?>"
                alt="Foto de perfil"
                onerror="this.src='<?= base_url('images/default-user.webp') ?>'"
              >
            </div>

            <label class="btn-soft" for="foto">Cambiar foto</label>
            <input id="foto" name="foto" type="file" accept="image/png,image/jpeg,image/jpg,image/webp" hidden>

            <small class="hint">Formatos: JPG/PNG/WEBP • Máx 5MB</small>
          </div>

          <!-- Columna derecha: datos -->
          <div class="perfil-fields">

            <div class="row">
              <label class="lbl">Usuario</label>
              <input class="inp" type="text" value="<?= esc($usernm) ?>" disabled>
            </div>

            <div class="row">
              <label class="lbl">Correo</label>
              <input class="inp" type="email" value="<?= esc($correo) ?>" disabled>
            </div>

            <div class="row row-2">
              <div>
                <label class="lbl" for="persona_Nombre">Nombre</label>
                <input
                  class="inp"
                  id="persona_Nombre"
                  name="persona_Nombre"
                  type="text"
                  value="<?= esc(old('persona_Nombre', $nombre)) ?>"
                  required
                  maxlength="50"
                >
              </div>

              <div>
                <label class="lbl" for="persona_FechaNacimiento">Fecha de nacimiento</label>
                <input
                  class="inp"
                  id="persona_FechaNacimiento"
                  name="persona_FechaNacimiento"
                  type="date"
                  value="<?= esc(old('persona_FechaNacimiento', $fnac)) ?>"
                  required
                >
              </div>
            </div>

            <div class="row row-2">
              <div>
                <label class="lbl" for="persona_ApllP">Apellido paterno</label>
                <input
                  class="inp"
                  id="persona_ApllP"
                  name="persona_ApllP"
                  type="text"
                  value="<?= esc(old('persona_ApllP', $ap)) ?>"
                  required
                  maxlength="50"
                >
              </div>

              <div>
                <label class="lbl" for="persona_ApllM">Apellido materno</label>
                <input
                  class="inp"
                  id="persona_ApllM"
                  name="persona_ApllM"
                  type="text"
                  value="<?= esc(old('persona_ApllM', $am)) ?>"
                  maxlength="50"
                >
              </div>
            </div>

            <!-- Botones -->
            <div class="perfil-actions">
              <a class="btn-ghost" href="<?= site_url('/') ?>">Cancelar</a>
              <button class="btn-primary" type="submit">
                Guardar cambios
              </button>
            </div>

          </div>
        </div>

      </form>

    </section>

  </div>

</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('javascript/perfil.script.js') ?>"></script>
<?= $this->endSection() ?>