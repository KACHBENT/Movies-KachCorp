<?= $this->extend('layout/main') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('css/inicio.styles.css') ?>">
<style>
  /* mini estilo para el fallback */
  .pg-fallback{
    border: 1px solid rgba(0,0,0,.08);
    border-radius: 16px;
    background: #fff;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('navbar') ?>
<?= $this->include('layout/NavBar') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container my-4">

  <!-- Carrusel banners -->
  <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6500">
    <div class="carousel-indicators">
      <?php foreach (($recomendaciones ?? []) as $i => $item): ?>
        <button type="button"
                data-bs-target="#heroCarousel"
                data-bs-slide-to="<?= $i ?>"
                class="<?= $i === 0 ? 'active' : '' ?>"
                aria-current="<?= $i === 0 ? 'true' : 'false' ?>"
                aria-label="Slide <?= $i + 1 ?>"></button>
      <?php endforeach; ?>
    </div>

    <div class="carousel-inner">
      <?php foreach (($recomendaciones ?? []) as $i => $item): ?>
        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
          <img src="<?= esc($item['imagen']) ?>" class="d-block w-100" style="height:600px;object-fit:cover" alt="<?= esc($item['titulo']) ?>">

          <div class="carousel-caption text-start">
            <span class="badge bg-dark bg-opacity-75"><?= esc($item['tag'] ?? 'Recomendado') ?></span>
            <h2 class="mt-2"><?= esc($item['titulo']) ?></h2>
            <p class="d-none d-md-block"><?= esc($item['descripcion']) ?></p>

            <button class="btn btn-light fw-semibold"
                    type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#videoModal"
                    data-video="<?= esc($item['video']) ?>">
            Ver video
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
      <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
      <span class="visually-hidden">Siguiente</span>
    </button>
  </div>
  <section class="container my-5">

  <!-- Misión y Visión -->
  <div class="row g-4">
    <div class="col-12 col-lg-6">
      <div class="card card-info shadow-sm border-0 h-100" style="border-radius:18px;">
        <div class="card-body p-4">
          <div class=" align-items-center gap-2 mb-2">
            <h5 class="text-center">Misión</h5>
          </div>
          <p class="mb-0" style="line-height:1.7;">
            Brindar soluciones tecnológicas confiables, seguras y escalables que impulsen el crecimiento de nuestros clientes,
            optimizando sus procesos mediante desarrollo de software, integración de sistemas y soporte especializado,
            con enfoque en calidad, innovación y resultados medibles.
          </p>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="card card-info shadow-sm border-0 h-100" style="border-radius:18px;">
        <div class="card-body p-4">
          <div class=" align-items-center gap-2 mb-2">
            <h5 class="text-center">Visión</h5>
          </div>
          <p class="mb-0" style="line-height:1.7;">
            Ser una consultoría líder en transformación digital, reconocida por crear productos de software modernos y eficientes,
            por la excelencia técnica de nuestro equipo y por generar valor sostenido para organizaciones que buscan evolucionar y competir
            en un entorno tecnológico en constante cambio.
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Equipo -->
  <div class="d-flex align-items-center justify-content-between mt-5 mb-3">
    <h3 class="m-0 fw-bold">Nuestro equipo</h3>
  </div>

  <div class="row g-4">

    <!-- Integrante 1 -->
    <div class="col-12 col-md-4">
      <div class="card card-info shadow-sm border-0 h-100" style="border-radius:18px;">
        <div class="card-body p-4 text-center">
          <img src="<?= base_url('images/team/tetla.jpg') ?>"
               alt="Integrante 1"
               class="rounded-circle mb-3"
               style="width:96px;height:96px;object-fit:cover;border:3px solid rgba(13,110,253,.25);">
          <h5 class="fw-bold mb-1">Tetlamatzin</h5>
          <div class=" mb-3">Documentador</div>

          <div class="d-flex justify-content-center gap-2 flex-wrap">
            <span class="badge bg-primary-subtle text-primary">PHP</span>
            <span class="badge bg-primary-subtle text-primary">CodeIgniter</span>
            <span class="badge bg-primary-subtle text-primary">MySQL</span>
          </div>

          <hr class="my-3">

          <p class=" mb-0" style="font-size:.95rem;">
            Enfocado en arquitectura limpia, APIs y optimización de rendimiento.
          </p>
        </div>
      </div>
    </div>

    <!-- Integrante 2 -->
    <div class="col-12 col-md-4">
      <div class="card card-info shadow-sm border-0 h-100" style="border-radius:18px;">
        <div class="card-body p-4 text-center">
          <img src="<?= base_url('images/team/yo-julio.jpg') ?>"
               alt="Integrante 2"
               class="rounded-circle mb-3"
               style="width:96px;height:96px;object-fit:cover;border:3px solid rgba(209, 42, 30, 0.88);">
          <h5 class="fw-bold mb-1">Julio Antonio Garcia Peza</h5>
          <div class=" mb-3">Backend Developer</div>

          <div class="d-flex justify-content-center gap-2 flex-wrap">
            <span class="badge bg-success-subtle text-success">.NET</span>
            <span class="badge bg-success-subtle text-success">SQL Server</span>
            <span class="badge bg-success-subtle text-success">Arquitect System</span>
          </div>

          <hr class="my-3">

          <p class=" mb-0" style="font-size:.95rem;">
            Especialista en servicios, seguridad y manejo de datos.
          </p>
        </div>
      </div>
    </div>

    <!-- Integrante 3 -->
    <div class="col-12 col-md-4">
      <div class="card card-info shadow-sm border-0 h-100" style="border-radius:18px;">
        <div class="card-body p-4 text-center">
          <img src="<?= base_url('images/team/brandon.jpg') ?>"
               alt="Integrante 3"
               class="rounded-circle mb-3"
               style="width:96px;height:96px;object-fit:cover;border:3px solid rgba(255,193,7,.25);">
          <h5 class="fw-bold mb-1">Brandon Javier Villegas Martínez</h5>
          <div class=" mb-3">Frontend / UI Developer</div>

          <div class="d-flex justify-content-center gap-2 flex-wrap">
            <span class="badge bg-warning-subtle text-warning">Bootstrap</span>
            <span class="badge bg-warning-subtle text-warning">JS</span>
            <span class="badge bg-warning-subtle text-warning">UX</span>
          </div>

          <hr class="my-3">

          <p class=" mb-0" style="font-size:.95rem;">
            Diseño limpio, responsive y experiencia de usuario moderna.
          </p>
        </div>
      </div>
    </div>

  </div>
</section>


</div>

<!-- Modal video -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header card-header-custom  linea-contenedor">
        <h5 class="modal-title">Reproducción</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">

        <div class="ratio ratio-16x9 d-none" id="videoFrameWrap">
          <iframe id="videoFrame"
                  src=""
                  title="Video"
                  allow="autoplay; encrypted-media; picture-in-picture"
                  allowfullscreen></iframe>
        </div>

        <div class="ratio ratio-16x9 d-none" id="videoTagWrap">
          <video id="videoTag" controls playsinline>
            <source id="videoSource" src="" type="video/mp4">
            Tu navegador no soporta video MP4.
          </video>
        </div>

        <div id="videoFallback" class="d-none pg-fallback p-4 text-center">
          <div class="fw-semibold mb-2">MEGA rechazó la reproducción dentro del modal.</div>
          <div class="text-muted mb-3">No es tu sistema: MEGA bloquea iframes por seguridad. Ábrelo en otra pestaña 👇</div>

          <a id="videoFallbackLink"
             class="btn btn-primary"
             href="#"
             target="_blank"
             rel="noopener">
            Abrir en MEGA
          </a>
        </div>

      </div>
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
  "use strict";

  const modalEl = document.getElementById("videoModal");

  const frameWrap = document.getElementById("videoFrameWrap");
  const frame = document.getElementById("videoFrame");

  const videoWrap = document.getElementById("videoTagWrap");
  const video = document.getElementById("videoTag");
  const source = document.getElementById("videoSource");

  const fallback = document.getElementById("videoFallback");
  const fallbackLink = document.getElementById("videoFallbackLink");

  function isMp4(url) {
    return typeof url === "string" && url.toLowerCase().includes(".mp4");
  }

  function isMega(url) {
    return typeof url === "string" && /(^https?:\/\/)?(www\.)?mega\.nz\//i.test(url);
  }

  function megaToEmbed(url) {
    try {
      const u = new URL(url);
      if (!u.hostname.includes("mega.nz")) return url;

      const parts = u.pathname.split("/").filter(Boolean); 
      if (parts.length >= 2 && parts[0] === "file") {
        u.pathname = "/embed/" + parts[1];
      }
      return u.toString();
    } catch {
      return url;
    }
  }

  function resetUI() {

    if (frame) frame.src = "";
    if (frameWrap) frameWrap.classList.add("d-none");


    if (video) video.pause();
    if (source) source.src = "";
    if (video) video.load();
    if (videoWrap) videoWrap.classList.add("d-none");


    if (fallback) fallback.classList.add("d-none");
    if (fallbackLink) fallbackLink.href = "#";
  }

  function openVideo(url) {
    resetUI();
    if (!url) return;

    if (isMp4(url)) {
      source.src = url;
      videoWrap.classList.remove("d-none");
      video.load();
      video.play().catch(() => {});
      return;
    }

    if (isMega(url)) {
      const embedUrl = megaToEmbed(url);
      fallbackLink.href = url;

      frameWrap.classList.remove("d-none");
      frame.src = embedUrl;

      const t = setTimeout(() => {
        frameWrap.classList.add("d-none");
        fallback.classList.remove("d-none");
      }, 2200);

      frame.onload = () => clearTimeout(t);
      return;
    }

    frameWrap.classList.remove("d-none");
    frame.src = url;
  }

  if (modalEl) {
    modalEl.addEventListener("show.bs.modal", (ev) => {
      const btn = ev.relatedTarget;
      const url = btn ? btn.getAttribute("data-video") : "";
      openVideo(url);
    });

    modalEl.addEventListener("hidden.bs.modal", resetUI);
  }
})();
</script>
<?= $this->endSection() ?>
