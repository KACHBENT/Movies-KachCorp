<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>
    <?= esc($title ?? 'Movies KachCorp') ?>
  </title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="<?= base_url('bootstrap-5.3.3/css/bootstrap.min.css') ?>">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- Estilos -->
  <link rel="stylesheet" href="<?= base_url('css/index.styles.css') ?>">
  <link rel="stylesheet" href="<?= base_url('css/footer.styles.css') ?>">

  <?= $this->renderSection('css') ?>

  <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">
</head>

<body class="fondo-tech">

  <!-- NAVBAR -->
  <?= $this->renderSection('navbar') ?>

  <!-- TOAST -->
  <div id="toastMount" class="position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="toastContainer"></div>
  </div>

  <!-- CONTENIDO -->
  <main class="container-fluid py-4 content-wrapper">
    <?= $this->renderSection('content') ?>
  </main>

  <!-- FOOTER -->
  <?= $this->renderSection('footer') ?>

  <!-- SCRIPTS -->
  <script src="<?= base_url('bootstrap-5.3.3/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('JQuery/jquery.min.js') ?>"></script>
      <script>
  window.__FLASH__ = <?= json_encode([
      'success' => session()->getFlashdata('toast_success'),
      'error'   => session()->getFlashdata('toast_error'),
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
</script>

<script>
  function showToast(type, message) {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toastId = 't_' + Math.random().toString(36).slice(2);

    const icon = {
      success: 'bi-check-circle-fill',
      danger: 'bi-x-circle-fill',
      warning: 'bi-exclamation-triangle-fill',
      info: 'bi-info-circle-fill'
    }[type] || 'bi-info-circle-fill';

    const html = `
      <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            <i class="bi ${icon} me-2"></i>${String(message)}
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
      </div>
    `;

    container.insertAdjacentHTML('beforeend', html);

    const el = document.getElementById(toastId);
    const t  = new bootstrap.Toast(el, { delay: 4500 });
    t.show();

    el.addEventListener('hidden.bs.toast', () => el.remove());
  }

  (function () {
    const f = window.__FLASH__ || {};

    if (f.success) {
      showToast('success', f.success);
    }

    if (f.error) {
      if (Array.isArray(f.error)) {
        f.error.forEach(msg => showToast('danger', msg));
      } else {
        showToast('danger', f.error);
      }
    }
  })();
</script>
  <?= $this->renderSection('scripts') ?>

</body>
</html>
