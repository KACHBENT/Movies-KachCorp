<?php
$user = session()->get('usuario') ?? [];
$isLoggedIn = (bool) (session()->get('isLoggedIn') ?? false);
?>

<?= $this->section('navbar') ?>

<!-- Navbar superior -->
<nav class="navbar bg-custom px-3 sticky-top">
  <button class="btn btn-light me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar"
    aria-controls="sidebar">
    <img src="<?= base_url('images/icons/menu.svg') ?>" class="black-filter" alt="menu" width="20" height="20">
  </button>
  <a class="navbar-brand content-logo-business d-flex align-items-center gap-2 text-white fw-semibold text-decoration-none"
    href="<?= site_url('/') ?>" aria-label="Ir al inicio de AppForrajes">
    <picture class="brand-logo d-inline-block rounded-2 overflow-hidden">
      <source srcset="<?= base_url('images/Icono-minimalista-pa.webp') ?>" type="image/webp">
      <img src="<?= base_url('images/Icono-minimalista-pa.webp') ?>" width="36" height="36" loading="lazy"
        alt="AppForrajes">
    </picture>

    <span class="d-flex flex-column lh-1">
      <span class="brand-title">Shop System Crazy</span>
    </span>
  </a>
</nav>

<!-- SIDEBAR -->
<div class="offcanvas offcanvas-start sidebar-tech text-white"
     tabindex="-1"
     id="sidebar">

  <div class="offcanvas-header">
    <h5>#SHOPSYSTEMCRAZY</h5>
    <button type="button" class="btn-close btn-close-white"
      data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body d-flex flex-column">
    <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded bg-black bg-opacity-50">
      <i class="bi bi-person-circle fs-3"></i>
      <div>
        <div class="fw-semibold">
          <?= esc($isLoggedIn ? ($user['nombre'] ?? 'Invitado') : 'Invitado') ?>
        </div>
        <small class="text-white-50">
          <?= esc($isLoggedIn ? ($user['rol'] ?? 'Invitado') : 'Sin rol') ?>
        </small>
      </div>
    </div>

    <!-- Navegación -->
    <nav class="nav flex-column gap-1 flex-grow-1">
      <a class="nav-link nav-link-function text-white d-flex align-items-center gap-2 active"
        href="<?= base_url('/') ?>">
        <img src="<?= base_url('images/icons/home.svg') ?>" class="white" alt="inicio" width="30" height="30"> Inicio
      </a>
      <div>
        <button
          class="btn btn-toggle nav-link-function align-items-center rounded text-start w-100 text-white d-flex gap-2"
          data-bs-toggle="collapse" data-bs-target="#submenu1" aria-expanded="false">
          <img src="<?= base_url('images/icons/supervised_user.svg') ?>" class="white" alt="inicio" width="30"
            height="30"> Portafolios
          <i class="bi bi-chevron-down ms-auto"></i>
        </button>
        <div class="collapse ps-4 mt-1" id="submenu1">
          <a class="nav-link nav-link-list text-white" href="<?= base_url('/brandon') ?>">Brandon</a>
          <a class="nav-link nav-link-list text-white" href="<?= base_url('/enrique') ?>">Enrique</a>
          <a class="nav-link nav-link-list text-white" href="<?= base_url('/julio') ?>">Julio</a>
        </div>
      </div>

      <div>
        <button
          class="btn btn-toggle nav-link-function align-items-center rounded text-start w-100 text-white d-flex gap-2"
          data-bs-toggle="collapse" data-bs-target="#Catalogos" aria-expanded="false">
          <img src="<?= base_url('images/icons/category.svg') ?>" class="white" alt="inicio" width="30" height="30">
          Contactanos
          <i class="bi bi-chevron-down ms-auto"></i>
        </button>

        <div class="collapse ps-4 mt-1" id="Catalogos">
          <a class="nav-link nav-link-list text-white" href="<?= base_url('contacto') ?>">
            Registro de solicitud
          </a>
        </div>
      </div>

      <div>

        <a class="nav-close nav-link text-white d-flex align-items-center gap-2"
          href="<?= site_url('acceso/logout') ?>">
          <img src="<?= base_url('images/icons/exit_to_app.svg') ?>" class="white" alt="cerrar sesión" width="30"
            height="30"> Cerrar sesión
        </a>
    </nav>
  </div>
</div>

<?= $this->endSection() ?>
