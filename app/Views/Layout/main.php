<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>
    <?= esc($title ?? 'SHOPSYSTEMCRAZY | Equipa tu mundo digital') ?>
    <?php if (session()->get('usuario')): ?>
      | <?= esc(session()->get('usuario')['rol']) ?>
    <?php endif; ?>
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

  <?= $this->renderSection('scripts') ?>

</body>
</html>
