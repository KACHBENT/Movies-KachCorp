<?= $this->section('footer') ?>

<footer class="footer mt-auto">
  <div class="container py-4">
    <div class="footer-inner">

      <div class="row gy-4">

        <!-- Marca -->
        <div class="col-md-4">
          <h5 class="footer-brand">Movies KachCorp</h5>
          <p class="footer-text">
            Tu tienda especializada en peliculas y series
          </p>
        </div>

        <!-- Contacto -->
        <div class="col-md-4">
          <h6 class="footer-title">Contacto</h6>
          <ul class="list-unstyled footer-links">
            <li><i class="bi bi-envelope"></i> soporte@shopsystemcrazy.com</li>
            <li><i class="bi bi-telephone"></i> 55 9876 5432</li>
            <li><i class="bi bi-geo-alt"></i> Ciudad de México</li>
          </ul>
        </div>

        <!-- Navegación -->
        <div class="col-md-4">
          <h6 class="footer-title">Información</h6>
          <a href="<?= base_url('aviso-privacidad') ?>" class="footer-link"><i class="bi bi-shield-lock"></i> Aviso de privacidad</a><br>
          <a href="<?= base_url('terminos') ?>" class="footer-link"><i class="bi bi-file-earmark-text"></i> Términos y condiciones</a>
        </div>

      </div>

      <hr class="footer-divider">

      <div class="footer-bottom">
        <span>© <?= date('Y') ?> Movies Kach Corp. Todos los derechos reservados.</span>
      </div>

    </div>
  </div>
</footer>

<?= $this->endSection() ?>