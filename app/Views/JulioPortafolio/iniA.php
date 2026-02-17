<?= $this->extend('layout/main') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('css/julio.styles.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('navbar') ?>
<?= $this->include('layout/NavBar') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

  <main class="container justify-content-center">
        <div class="card mt-4">
            <div class="card-header bg-header-custom gap-2 d-flex border-rounded">
                <img src="images/icons/users.svg" alt="users" class="icon-users">
                <h5 class="text-white">Mi portafolio personal</h5>
            </div>
            <div class="card-body text-start">
                <div class="container ">
                    <p class="text-justify">Mi nombre es <span> Julio Antonio Garcia Peza</span> por el momento soy un desarrollador de software en crecimiento.
                        Por el momento estoy estudiante de forma inesperada en la universidad bancaria de méxico.
                    </p>
                </div>
                <div class="container gap-4">
                    <div class="row">
                        <div class="col-12 col-md-6 container">
                            <img src="images/team/yo.jpg" alt="" class="img-fluid image-content">
                        </div>
                        <article class="col-12 col-md-6 container bg-header-custom p-4">
                            <div class="card">
                                <div class="card text-start">
                                    <div class="card-body">
                                        <div class="row align-content-center">
                                            <div class="col-12 col-md-6 container">
                                                <h5 class="card-title text-start">Habilidades Personales</h5>
                                                <ul class="mb-4">
                                                    <li>Comunicativo.</li>
                                                    <li>Cooperativo.</li>
                                                    <li>Creativo.</li>
                                                    <li>Investigador.</li>
                                                    <li>Innovador.</li>
                                                    <li>Presistente.</li>
                                                    <li>Trabajador.</li>
                                                </ul>
                                            </div>
                                            <div class="col-12 col-md-6 container">
                                                <h5 class="card-title text-start">Lenguajes de Programación</h5>
                                                <ul class="mb-4">
                                                    <li>C#.</li>
                                                    <li>Java.</li>
                                                    <li>Javascript.</li>
                                                    <li>HTML 5.</li>
                                                    <li>Css.</li>
                                                    <li>Python más menos.</li>
                                                    <li>Blazor.</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-12 col-md-6 container">
                                                <h5 class="card-title text-start">Habilidades de Carrera</h5>
                                                <ul class="mb-4">
                                                    <li>Gestión de Servidores.</li>
                                                    <li>Infraestructura de sotfware.</li>
                                                    <li>Scrum Master.</li>
                                                    <li>Gestion de Base de datos.</li>
                                                    <li>Sistemas Operativos.</li>
                                                </ul>
                                            </div>
                                            <div class="col-12 col-md-6 container">
                                                <h5 class="card-title text-start">Certificaciones</h5>
                                                <ul>
                                                    <li>Certificación Eclipse IDE.</li>
                                                    <li>Costancia de hablidades organizativa.</li>
                                                    <li>Constancia de Atención al cliente.</li>
                                                    <li>Constancia de IME.</li>
                                                    <li>Tecnico en Programador.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?= $this->endSection() ?>

<?= $this->section('footer') ?>
<?= $this->include('layout/Footer') ?>
<?= $this->endSection() ?>
