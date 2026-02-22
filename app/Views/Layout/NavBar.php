<?php

$user = session()->get('usuario') ?? [];
$isLoggedIn = (bool) (session()->get('isLoggedIn') ?? false);
$nombre = $isLoggedIn ? ($user['nombre'] ?? ($user['usuario_nombre'] ?? 'Usuario')) : 'Invitado';
$rol = $isLoggedIn ? ($user['rol'] ?? 'Sin rol') : 'Sin rol';
$correo = $isLoggedIn ? ($user['correo'] ?? null) : null;
$imageUrl = $isLoggedIn ? ($user['imageUrl'] ?? null) : null;
$avatarSrc = $imageUrl ? base_url($imageUrl) : null;
$isLoggedIn = (bool) (session()->get('auth_logged_in') ?? session()->get('isLoggedIn') ?? false);
$roles = $isLoggedIn ? ($user['roles'] ?? []) : [];
if (!is_array($roles))
  $roles = [$roles];
$roles = array_map(static fn($r) => strtolower(trim((string) $r)), $roles);
$hasRole = static function (...$required) use ($roles): bool {
  foreach ($required as $r) {
    if (in_array(strtolower(trim((string) $r)), $roles, true)) {
      return true;
    }
  }
  return false;
};
?>

<?= $this->section('navbar') ?>
<nav class="navbar bg-custom px-3 sticky-top">
  <button class="btn btn-light me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar"
    aria-controls="sidebar">
    <img src="<?= base_url('images/icons/menu.svg') ?>" class="black-filter" alt="menu" width="20" height="20">
  </button>

  <a class="navbar-brand content-logo-business d-flex align-items-center gap-2 text-white fw-semibold text-decoration-none"
    href="<?= site_url('/') ?>" aria-label="Ir al inicio">
    <picture class="brand-logo d-inline-block rounded-2 overflow-hidden">
      <source srcset="<?= base_url('images/logo movies.png') ?>" type="image/png">
      <img src="<?= base_url('images/logo movies.png') ?>" width="180" height="80" loading="lazy"
        alt="Movies Kach Corp">
    </picture>
  </a>
</nav>

<div class="offcanvas offcanvas-start sidebar-tech text-white" tabindex="-1" id="sidebar">

  <div class="offcanvas-header">
    <h5>Movies KachCorp</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body d-flex flex-column">
    <div class="d-flex align-items-center gap-3 mb-3 p-2 rounded bg-black bg-opacity-50">

      <div
        style="width:56px;height:56px;border-radius:16px;overflow:hidden;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;">
        <?php if ($isLoggedIn && $avatarSrc): ?>
          <img src="<?= esc(session('usuario.imageUrl') ?? base_url('images/icons/account.svg')) ?>" alt="Avatar"
            style="width:65px; height:auto;">
        <?php else: ?>
          <i class="bi bi-person-circle fs-1"></i>
        <?php endif; ?>
      </div>

      <!-- Info -->
      <div class="flex-grow-1">
        <div class="fw-semibold" style="line-height:1.1;">
          <?= esc($nombre) ?>
        </div>

        <small class="text-white-50 d-block" style="word-break:break-all;">
          <?= esc($correo ?? 'Sin correo') ?>
        </small>

        <span class="badge rounded-pill text-bg-light mt-1">
          <?= esc($rol) ?>
        </span>
      </div>
    </div>

    <!-- Navegación -->

    <nav class="nav flex-column gap-1 flex-grow-1">

      <a class="nav-link nav-link-function text-white d-flex align-items-center gap-2 active"
        href="<?= base_url('/') ?>">
        <img src="<?= base_url('images/icons/home.svg') ?>" class="white" alt="inicio" width="30" height="30"> Inicio
      </a>

      <?php if ($hasRole('administrador', 'empleado')): ?>
        <div>
          <button
            class="btn btn-toggle nav-link-function align-items-center rounded text-start w-100 text-white d-flex gap-2"
            data-bs-toggle="collapse" data-bs-target="#Peliculas" aria-expanded="false">
            <img src="<?= base_url('images/icons/movie.svg') ?>" class="white" alt="peliculas" width="30" height="30">
            Peliculas
            <i class="bi bi-chevron-down ms-auto"></i>
          </button>

          <div class="collapse ps-4 mt-1" id="Peliculas">
            <a class="nav-link nav-link-list text-white" href="<?= base_url('peliculas') ?>">
              Administración de Peliculas
            </a>
          </div>
        </div>
      <?php endif; ?>
      <div>
        <?php if ($hasRole('administrador', 'empleado')): ?>
          <button
            class="btn btn-toggle nav-link-function align-items-center rounded text-start w-100 text-white d-flex gap-2"
            data-bs-toggle="collapse" data-bs-target="#Usuarios" aria-expanded="false">
            <img src="<?= base_url('images/icons/users.svg') ?>" class="white" alt="usuarios" width="30" height="30">
            Usuarios
            <i class="bi bi-chevron-down ms-auto"></i>
          </button>

          <div class="collapse ps-4 mt-1" id="Usuarios">
            <a class="nav-link nav-link-list text-white" href="<?= base_url('usuarios') ?>">
              Administración de usuarios
            </a>
            <a class="nav-link nav-link-list text-white" href="<?= base_url('usuarios/registro') ?>">
              Registro de usuarios
            </a>
          </div>
        </div>
      <?php endif; ?>
      <a class="nav-link nav-link-function text-white d-flex align-items-center gap-2 active"
        href="<?= base_url('perfil') ?>">
        <img src="<?= base_url('images/icons/settings.svg') ?>" class="white" alt="perfil" width="30" height="30">
        Configuración de Perfil
      </a>

      <?php if ($isLoggedIn): ?>
        <a class="nav-close nav-link text-white d-flex align-items-center gap-2" href="<?= site_url('acceso/logout') ?>">
          <img src="<?= base_url('images/icons/exit_to_app.svg') ?>" class="white" alt="cerrar sesión" width="30"
            height="30">
          Cerrar sesión
        </a>
      <?php else: ?>
        <a class="nav-close nav-link text-white d-flex align-items-center gap-2" href="<?= site_url('acceso/login') ?>">
          <img src="<?= base_url('images/icons/exit_to_app.svg') ?>" class="white" alt="iniciar sesión" width="30"
            height="30">
          Iniciar sesión
        </a>
      <?php endif; ?>

    </nav>
  </div>
</div>

<?= $this->endSection() ?>