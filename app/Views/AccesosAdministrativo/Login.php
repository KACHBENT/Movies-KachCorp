<?= $this->extend('layout/main') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('css/login.styles.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main id="content" class="container py-4">
  <section class="justify-content-center align-items-center mt-2 mb-2 d-flex flex-column">
    <div class="card mb-4">
      <div class="card-header linea-contenedor bg-white d-flex text-center gap-2">
        <img src="<?= base_url('images/icons/login.svg') ?>" class="icon-head darken" alt="Login" loading="lazy">
        <h4 class="card-title title text-center"><span>Iniciar Sesión</span></h4>
      </div>

      <form id="formLogin" action="<?= site_url('acceso/login') ?>" method="POST" novalidate autocomplete="on">
        <?= csrf_field() ?>

        <div class="card-body bg-white">
          <article class="align-items-center">
            <div class="container-fluid mb-2">
              <div class="container mb-lg-4">
                <center>
                  <img src="<?= base_url('images/icons/group-user.svg') ?>" alt="Users" class="img-fluid darken" width="100">
                </center>
              </div>

              <div class="input-group mb-2 mt-2">
                <span class="input-group-text">
                  <img src="<?= base_url('images/icons/account_box.svg') ?>" alt="username" class="icon-form darken" loading="lazy" />
                </span>
                <div class="form-floating flex-grow-1">
                  <input
                    type="text"
                    class="form-control"
                    id="usuario"
                    name="usuario"
                    placeholder="Ingresa el Usuario o correo"
                    value="<?= old('usuario') ?>"
                    required
                  />
                  <label for="usuario">Usuario o correo</label>
                </div>
              </div>

              <div class="input-group mb-3">
                <span class="input-group-text">
                  <img src="<?= base_url('images/icons/password.svg') ?>" class="darken" alt="password" loading="lazy" />
                </span>

                <input
                  type="password"
                  id="passInput"
                  name="password"
                  class="form-control"
                  placeholder="Ingresa la Contraseña"
                  required
                  autocomplete="current-password"
                />

                <button
                  type="button"
                  id="btnToggle"
                  class="btn btn-outline-secondary custom-view border-lighter"
                  aria-label="Mostrar u ocultar contraseña"
                  data-icon-view="<?= base_url('images/icons/view.svg') ?>"
                  data-icon-hide="<?= base_url('images/icons/visibility_off.svg') ?>"
                >
                  <img id="toggleIcon" src="<?= base_url('images/icons/visibility_off.svg') ?>" class="icon-form darken" alt="Ocultar" width="20" height="20">
                </button>
              </div>
            </div>
          </article>
        </div>

        <div class="card-footer bg-white gap-2 justify-content-center">
          <button type="submit" class="btn btn-secondary w-100 p-sm-2">Iniciar Sesión</button>
        </div>
      </form>
    </div>
  </section>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('javascript/login.script.js') ?>"></script>
<?= $this->endSection() ?>
