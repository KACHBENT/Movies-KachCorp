<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */



$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Home::index');
});

//$routes->get('/', 'Home::index');

// ! Login 
$routes->get('acceso/login', 'AccesoController::loginShowForm');
$routes->post('acceso/login', 'AccesoController::login');
$routes->get('acceso/logout', 'AccesoController::logout');

// ! Routes of API´s
$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    $routes->resource('categorias', [
        'controller' => 'Categorias',
        'only'       => ['index', 'show', 'create', 'update', 'delete'],
    ]);

    $routes->resource('productos', [
        'controller' => 'Productos',
        'only'       => ['index', 'show', 'create', 'update', 'delete'],
    ]);

    $routes->resource('ventas', [
        'controller' => 'Ventas',
        'only'       => ['index', 'show', 'create', 'update', 'delete'],
    ]);

    $routes->get('ventas/(:num)/detalles',  'DetalleVentas::index/$1');
    $routes->post('ventas/(:num)/detalles', 'DetalleVentas::create/$1');

    $routes->post('auth/device-login', 'AuthDispositivo::deviceLogin');
});
$routes->get('uploads/(:any)', 'Uploads::show/$1');


