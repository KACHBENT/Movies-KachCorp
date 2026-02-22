<?= $this->extend('layout/main') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('css/inicio.styles.css') ?>">
<style>
  /* ====== NETFLIX ROW ====== */
  .nf-wrap {
    color: #fff;
  }

  .nf-head {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-top: 28px;
  }

  .nf-title {
    font-size: 2.2rem;
    font-weight: 900;
    line-height: 1;
    margin: 0;
  }

  .nf-sub {
    opacity: .85;
    margin: .35rem 0 0 0;
  }

  .nf-hint {
    opacity: .85;
  }

  .nf-row {
    display: flex;
    gap: 14px;
    overflow-x: auto;
    padding: 14px 2px 6px;
    scroll-snap-type: x mandatory;
  }

  .nf-row::-webkit-scrollbar {
    height: 10px;
  }

  .nf-row::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, .18);
    border-radius: 999px;
  }

  .nf-card {
    width: 180px;
    min-width: 180px;
    height: 265px;
    border-radius: 18px;
    border: 1px solid rgba(255, 255, 255, .12);
    background: rgba(255, 255, 255, .06);
    overflow: hidden;
    position: relative;
    cursor: pointer;
    scroll-snap-align: start;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    text-align: left;
  }

  .nf-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 35px rgba(0, 0, 0, .35);
    border-color: rgba(255, 255, 255, .28);
  }

  .nf-poster {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .nf-grad {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg,
        rgba(0, 0, 0, .06) 0%,
        rgba(0, 0, 0, .35) 55%,
        rgba(0, 0, 0, .78) 100%);
    pointer-events: none;
  }

  .nf-meta {
    position: absolute;
    left: 12px;
    right: 12px;
    bottom: 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .nf-name {
    font-weight: 800;
    font-size: .95rem;
    line-height: 1.2;
    color: white;
  }

  .nf-tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    opacity: .92;
    font-size: .8rem;
    color: white;
  }

  .nf-pill {
    padding: 3px 8px;
    border-radius: 999px;
    background: rgba(255, 255, 255, .12);
    border: 1px solid rgba(255, 255, 255, .14);
    color: white;
  }

  .nf-play {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 40px;
    height: 40px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, .55);
    border: 1px solid rgba(255, 255, 255, .18);
    backdrop-filter: blur(8px);
  }

  .nf-play i {
    color: #fff;
    font-size: 1.2rem;
  }

  .nf-empty {
    margin-top: 16px;
    padding: 14px 16px;
    border-radius: 16px;
    background: rgba(255, 255, 255, .06);
    border: 1px solid rgba(255, 255, 255, .12);
    color: rgba(255, 255, 255, .85);
  }

  /* ====== MODAL ====== */
  .modal-content {
    border-radius: 18px;
    overflow: hidden;
  }

  .m-head {
    background: rgba(10, 10, 18, .96);
    color: #fff;
    border-bottom: 1px solid rgba(255, 255, 255, .10);
  }

  .m-body {
    background: rgba(10, 10, 18, .96);
    color: #fff;
  }

  .m-grid {
    display: grid;
    grid-template-columns: 340px 1fr;
    gap: 16px;
  }

  @media (max-width: 992px) {
    .m-grid {
      grid-template-columns: 1fr;
    }
  }

  .m-poster {
    width: 100%;
    aspect-ratio: 2/3;
    border-radius: 16px;
    object-fit: cover;
    border: 1px solid rgba(255, 255, 255, .14);
    background: rgba(255, 255, 255, .06);
  }

  .m-info h3 {
    margin: 0;
    font-weight: 900;
  }

  .m-desc {
    opacity: .9;
    line-height: 1.55;
    margin-top: 10px;
  }

  .m-line {
    opacity: .85;
    margin-top: 10px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .m-chip {
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(255, 255, 255, .10);
    border: 1px solid rgba(255, 255, 255, .12);
    font-weight: 700;
    font-size: .85rem;
  }

  /* fallback block */
  .pg-fallback {
    border: 1px solid rgba(255, 255, 255, .14);
    border-radius: 16px;
    background: rgba(255, 255, 255, .06);
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('navbar') ?>
<?= $this->include('layout/NavBar') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container my-4 nf-wrap">

  <!-- ===== Carrusel (SIN descripción, solo botón de video) ===== -->
  <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6500">
    <div class="carousel-indicators">
      <?php foreach (($recomendaciones ?? []) as $i => $item): ?>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>"
          class="<?= $i === 0 ? 'active' : '' ?>" aria-current="<?= $i === 0 ? 'true' : 'false' ?>"
          aria-label="Slide <?= $i + 1 ?>"></button>
      <?php endforeach; ?>
    </div>

    <div class="carousel-inner">
      <?php foreach (($recomendaciones ?? []) as $i => $item): ?>
        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
          <img src="<?= esc($item['imagen']) ?>" class="d-block w-100" style="height:600px;object-fit:cover"
            alt="<?= esc($item['titulo']) ?>">

          <div class="carousel-caption text-start">
            <span class="badge bg-dark bg-opacity-75"><?= esc($item['tag'] ?? 'Recomendado') ?></span>
            <h2 class="mt-2"><?= esc($item['titulo']) ?></h2>

            <!-- SOLO VIDEO (el detalle se ve en el modal) -->
            <button class="btn btn-light fw-semibold js-open-movie" type="button" data-bs-toggle="modal"
              data-bs-target="#movieModal" data-title="<?= esc($item['titulo']) ?>"
              data-desc="<?= esc($item['descripcion'] ?? '') ?>" data-img="<?= esc($item['imagen']) ?>"
              data-video="<?= esc($item['video'] ?? '') ?>" data-genre="<?= esc($item['genero'] ?? '') ?>"
              data-rating="<?= esc($item['clasificacion'] ?? '') ?>" data-date="<?= esc($item['creacion'] ?? '') ?>">
              <i class="bi bi-play-circle me-1"></i> Ver video
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

  <div class="nf-head">
    <div>
      <h3 class="nf-title"><img src="<?= base_url('images/icons/movie.svg') ?>" class="white mb-2" alt="peliculas" width="30"
            height="30"> Películas</h3>
      <p class="nf-sub">Haz click en una película para ver el detalle </p>
    </div>
  </div>

  <?php if (empty($peliculasNetflix)): ?>
    <div class="nf-empty">No hay películas activas para mostrar.</div>
  <?php else: ?>
    <div class="nf-row">
      <?php foreach ($peliculasNetflix as $p): ?>
        <?php
        $title = $p['titulo'] ?? '';
        $desc = $p['descripcion'] ?? '';
        $img = $p['imagen'] ?? '';
        // si viene "uploads/..." lo convertimos a URL pública
        $imgUrl = $img ? base_url($img) : '';
        $video = $p['video'] ?? '';
        $genre = $p['genero'] ?? '';
        $rating = $p['clasificacion'] ?? '';
        $date = $p['creacion'] ?? '';
        ?>

        <button type="button" class="nf-card js-open-movie" data-bs-toggle="modal" data-bs-target="#movieModal"
          data-title="<?= esc($title) ?>" data-desc="<?= esc($desc) ?>" data-img="<?= esc($imgUrl) ?>"
          data-video="<?= esc($video) ?>" data-genre="<?= esc($genre) ?>" data-rating="<?= esc($rating) ?>"
          data-date="<?= esc($date) ?>">

          <?php if ($imgUrl): ?>
            <img class="nf-poster" src="<?= esc($imgUrl) ?>" alt="<?= esc($title) ?>">
          <?php else: ?>
            <div class="d-flex align-items-center justify-content-center h-100" style="opacity:.85;">
              <div class="text-center">
                <div style="font-size:2rem"></div>
                <div class="mt-1">Sin póster</div>
              </div>
            </div>
          <?php endif; ?>

          <div class="nf-grad"></div>

          <div class="nf-play"><i class="bi bi-play-fill"></i></div>

          <div class="nf-meta">
            <div class="nf-name"><?= esc($title) ?></div>
            <div class="nf-tags">
              <?php if ($genre): ?><span class="nf-pill"><?= esc($genre) ?></span><?php endif; ?>
              <?php if ($rating): ?><span class="nf-pill"><?= esc($rating) ?></span><?php endif; ?>
            </div>
          </div>
        </button>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<!-- ===== MODAL DETALLE (imagen + descripción + video) ===== -->
<div class="modal fade" id="movieModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header m-head">
        <h5 class="modal-title fw-bold" id="mTitle">Detalle</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body m-body">

        <div class="m-grid">

          <!-- Left: Poster + info -->
          <div>
            <img id="mPoster" class="m-poster" src="" alt="Poster">
          </div>

          <div class="m-info">
            <h3 id="mTitle2"></h3>

            <div class="m-line">
              <span class="m-chip" id="mGenre" style="display:none"></span>
              <span class="m-chip" id="mRating" style="display:none"></span>
              <span class="m-chip" id="mDate" style="display:none"></span>
            </div>

            <p class="m-desc" id="mDesc" style="margin-bottom: 14px;"></p>

            <!-- Video area -->
            <div class="ratio ratio-16x9 d-none" id="videoFrameWrap">
              <iframe id="videoFrame" src="" title="Video" allow="autoplay; encrypted-media; picture-in-picture"
                allowfullscreen></iframe>
            </div>

            <div class="ratio ratio-16x9 d-none" id="videoTagWrap">
              <video id="videoTag" controls playsinline>
                <source id="videoSource" src="" type="video/mp4">
                Tu navegador no soporta video MP4.
              </video>
            </div>

            <div id="videoFallback" class="d-none pg-fallback p-4 text-center mt-3">
              <div class="fw-semibold mb-2">No se pudo reproducir dentro del modal.</div>
              <div class="text-muted mb-3">Ábrelo en otra pestaña 👇</div>

              <a id="videoFallbackLink" class="btn btn-light fw-semibold" href="#" target="_blank" rel="noopener">
                Abrir video
              </a>
            </div>

          </div>

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

    const modalEl = document.getElementById("movieModal");

    const mTitle = document.getElementById("mTitle");
    const mTitle2 = document.getElementById("mTitle2");
    const mDesc = document.getElementById("mDesc");
    const mPoster = document.getElementById("mPoster");

    const mGenre = document.getElementById("mGenre");
    const mRating = document.getElementById("mRating");
    const mDate = document.getElementById("mDate");

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
    function isDailymotion(url) {
      return typeof url === "string" && /dailymotion\.com/i.test(url);
    }

    // Convierte urls de dailymotion a embed estable
    function dmToEmbed(url) {
      try {
        const u = new URL(url);

        // caso: geo.dailymotion.com/player/x7zhh.html?video=ID&mute=true
        if (u.pathname.includes("/player/") && u.searchParams.get("video")) {
          const id = u.searchParams.get("video");
          const mute = u.searchParams.get("mute") === "true" ? "1" : "0";
          return `https://www.dailymotion.com/embed/video/${id}?autoplay=1&mute=${mute}`;
        }

        // caso: www.dailymotion.com/video/ID
        const parts = u.pathname.split("/").filter(Boolean);
        const idx = parts.indexOf("video");
        if (idx !== -1 && parts[idx + 1]) {
          const id = parts[idx + 1];
          return `https://www.dailymotion.com/embed/video/${id}?autoplay=1&mute=1`;
        }

        // si ya es embed, lo dejamos
        return url;
      } catch {
        return url;
      }
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

    function resetVideoUI() {
      // iframe
      if (frame) frame.src = "";
      if (frameWrap) frameWrap.classList.add("d-none");

      // video tag
      if (video) video.pause();
      if (source) source.src = "";
      if (video) video.load();
      if (videoWrap) videoWrap.classList.add("d-none");

      // fallback
      if (fallback) fallback.classList.add("d-none");
      if (fallbackLink) fallbackLink.href = "#";
    }

    function openVideo(url) {
      resetVideoUI();
      if (!url) return;

      // mp4 directo
      if (isMp4(url)) {
        source.src = url;
        videoWrap.classList.remove("d-none");
        video.load();
        video.play().catch(() => { });
        return;
      }

      // mega
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

      // dailymotion
      if (isDailymotion(url)) {
        const embed = dmToEmbed(url);
        fallbackLink.href = url;

        frameWrap.classList.remove("d-none");
        frame.src = embed;

        const t = setTimeout(() => {
          // si el navegador bloquea el iframe por CSP/headers, damos fallback
          fallback.classList.remove("d-none");
        }, 1800);

        frame.onload = () => clearTimeout(t);
        return;
      }

      // cualquier otro iframe
      fallbackLink.href = url;
      frameWrap.classList.remove("d-none");
      frame.src = url;
    }

    function setChip(el, val) {
      if (!el) return;
      if (val && String(val).trim() !== "") {
        el.textContent = val;
        el.style.display = "inline-flex";
      } else {
        el.textContent = "";
        el.style.display = "none";
      }
    }

    if (modalEl) {
      modalEl.addEventListener("show.bs.modal", (ev) => {
        const btn = ev.relatedTarget;
        if (!btn) return;

        const data = btn.dataset || {};

        const title = data.title || "Detalle";
        const desc = data.desc || "";
        const img = data.img || "";
        const videoUrl = data.video || "";

        mTitle.textContent = title;
        mTitle2.textContent = title;
        mDesc.textContent = desc;

        mPoster.src = img || "";
        mPoster.style.display = img ? "block" : "none";

        setChip(mGenre, data.genre || "");
        setChip(mRating, data.rating || "");
        setChip(mDate, data.date || "");

        openVideo(videoUrl);
      });

      modalEl.addEventListener("hidden.bs.modal", () => {
        resetVideoUI();
        mPoster.src = "";
        mDesc.textContent = "";
        mTitle.textContent = "Detalle";
        mTitle2.textContent = "";
        setChip(mGenre, "");
        setChip(mRating, "");
        setChip(mDate, "");
      });
    }
  })();
</script>
<?= $this->endSection() ?>