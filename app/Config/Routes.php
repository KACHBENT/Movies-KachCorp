<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */



$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Home::index');
});


// ! Login 
$routes->get('acceso/login', 'AccesoController::loginShowForm');
$routes->post('acceso/login', 'AccesoController::login');
$routes->get('acceso/logout', 'AccesoController::logout');

// todo usuarios

$routes->group('usuarios', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'UsuariosController::index');
    $routes->get('registro', 'UsuariosController::create');
    $routes->post('registro', 'UsuariosController::store');
    $routes->post('store', 'UsuariosController::store');
    $routes->post('update/(:num)', 'UsuariosController::update/$1');
    $routes->post('delete/(:num)', 'UsuariosController::delete/$1');
    $routes->post('desactivar/(:num)', 'UsuariosController::deactivate/$1');
    $routes->post('activar/(:num)', 'UsuariosController::activate/$1');
    $routes->get('registro/resultado', 'UsuariosController::resultado');
});

//todo Peliculas

$routes->group('peliculas', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'PeliculasController::index');
    $routes->post('store', 'PeliculasController::store');
    $routes->post('desactivar/(:num)', 'PeliculasController::deactivate/$1');
    $routes->post('activar/(:num)', 'PeliculasController::activate/$1');
    $routes->post('update/(:num)', 'PeliculasController::update/$1');
    $routes->post('delete/(:num)', 'PeliculasController::delete/$1');
});


$routes->group('api', static function($routes) {
    $routes->post('auth/login', 'AuthController::login');
    $routes->get('peliculas', 'PeliculasController::index');        
    $routes->get('peliculas/(:num)', 'PeliculasController::show/$1'); 
});




/*
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
*/

