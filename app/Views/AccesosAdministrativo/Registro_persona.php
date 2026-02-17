<?= $this->extend('layout/main') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('css/registro_persona.styles.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main id="content">
  <section class="py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12">
          <div class="card bg-white-custom p-4 shadow-sm rounded-4 border-transparent main-content"
               style="max-width:960px; margin:0 auto;">

            <div class="container mb-2 linea-contenedor">
              <div class="text-center">
                <h1 class="m-0">Registro de Usuario</h1>
              </div>
            </div>

            <form id="formRegister" action="<?= site_url('acceso/register') ?>" method="post" novalidate autocomplete="on">
              <?= csrf_field() ?>

              <div class="row g-4 align-items-center">

                <!-- Columna izquierda -->
                <div class="col-md-6 col-12">

                  <!-- Nombre (lo que guardas en usuarios.nombre) -->
                  <div class="input-group mb-3">
                    <span class="input-group-text">
                      <img src="<?= base_url('images/icons/person.svg') ?>" alt="Nombre" class="content-image" loading="lazy" />
                    </span>
                    <div class="form-floating flex-grow-1">
                      <input
                        type="text"
                        class="form-control"
                        id="nombre"
                        name="nombre"
                        placeholder="Ingrese su nombre"
                        value="<?= old('nombre') ?>"
                        required
                        autocomplete="name"
                      />
                      <label for="nombre">Nombre</label>
                    </div>
                  </div>

                  <!-- Email -->
                  <div class="input-group mb-3">
                    <span class="input-group-text">
                      <img src="<?= base_url('images/icons/contact_mail.svg') ?>" alt="Correo" class="content-image black-filter" loading="lazy" />
                    </span>
                    <div class="form-floating flex-grow-1">
                      <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        placeholder="Ingrese su correo"
                        value="<?= old('email') ?>"
                        required
                        autocomplete="email"
                      />
                      <label for="email">Correo</label>
                    </div>
                  </div>


                </div>

                <!-- Columna derecha -->
                <div class="col-md-6 col-12">

                  <!-- Contraseña -->
                  <div class="input-group mb-3">
                    <span class="input-group-text">
                      <img src="<?= base_url('images/icons/key.svg') ?>" alt="Contraseña" class="content-image" loading="lazy" />
                    </span>
                    <div class="form-floating flex-grow-1">
                      <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Ingrese su contraseña"
                        required
                        autocomplete="new-password"
                      />
                      <label for="password">Contraseña</label>
                    </div>
                  </div>

                  <!-- Confirmar contraseña (ojo: name debe ser password_conf) -->
                  <div class="input-group mb-3">
                    <span class="input-group-text">
                      <img src="<?= base_url('images/icons/key.svg') ?>" alt="Confirmación Contraseña" class="content-image" loading="lazy" />
                    </span>
                    <div class="form-floating flex-grow-1">
                      <input
                        type="password"
                        class="form-control"
                        id="password_conf"
                        name="password_conf"
                        placeholder="Confirma Contraseña"
                        required
                        autocomplete="new-password"
                      />
                      <label for="password_conf">Confirmar Contraseña</label>
                    </div>
                  </div>

                  <!-- Toggle ver contraseña (opcional) -->
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary w-100" id="btnShowPass">
                      Mostrar/Ocultar contraseña
                    </button>
                  </div>

                </div>
              </div>

              <!-- Botón & link -->
              <div class="col-12 mt-2">
                <button class="btn btn-primary w-100" type="submit">
                  Registrar Usuario
                </button>
              </div>

              <div class="text-center pt-2">
                <a href="<?= site_url('acceso/login') ?>" class="reference-login">
                  ¿Ya estás registrado? Inicia Sesión
                </a>
              </div>

            </form>
          </div>

        </div>
      </div>
    </div>
  </section>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  (function () {
    const form = document.getElementById('formRegister');
    if (form) {
      form.addEventListener('submit', (e) => {
        if (!form.checkValidity()) {
          e.preventDefault();
          form.classList.add('was-validated');
        }
      });
    }

    const btn = document.getElementById('btnShowPass');
    const p1 = document.getElementById('password');
    const p2 = document.getElementById('password_conf');

    if (btn && p1 && p2) {
      btn.addEventListener('click', () => {
        const show = p1.type === 'password';
        p1.type = show ? 'text' : 'password';
        p2.type = show ? 'text' : 'password';
      });
    }
  })();
</script>
<?= $this->endSection() ?>
