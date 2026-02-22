<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Home::index');
});

// ! Login 

$routes->get(
    'acceso/login',
    'AccesoController::loginShowForm'
);
$routes->post(
    'acceso/login',
    'AccesoController::login'
);
$routes->get(
    'acceso/logout',
    'AccesoController::logout'
);

// todo usuarios

$routes->group('usuarios', ['filter' => 'auth'], static function ($routes) {
    $routes->get(
        '/',
        'UsuariosController::index',
        ['filter' => 'role:administrador,empleado']
    );
    $routes->get(
        'registro',
        'UsuariosController::create',
        ['filter' => 'role:administrador,empleado']
    );
    $routes->post(
        'registro',
        'UsuariosController::store',
        ['filter' => 'role:administrador,empleado']
    );
    $routes->post(
        'store',
        'UsuariosController::store',
        ['filter' => 'role:administrador,empleado']
    );
    $routes->post(
        'update/(:num)',
        'UsuariosController::update/$1',
        ['filter' => 'role:administrador,empleado']
    );
    $routes->post(
        'delete/(:num)',
        'UsuariosController::delete/$1',
        ['filter' => 'role:administrador,empleado']
    );
    $routes->post(
        'desactivar/(:num)'
        ,
        'UsuariosController::deactivate/$1',
        ['filter' => 'role:administrador,empleado']
    );
    $routes->post(
        'activar/(:num)'
        ,
        'UsuariosController::activate/$1',
        ['filter' => 'role:administrador,empleado']
    );
    $routes->get(
        'registro/resultado',
        'UsuariosController::resultado',
        ['filter' => 'role:administrador,empleado']
    );
});

//todo Peliculas

$routes->group('peliculas', ['filter' => 'auth'], static function ($routes) {
    $routes->get(
        '/',
        'PeliculasController::index',
        ['filter' => 'role:administrador,empleado']
    );
    $routes->post(
        'store',
        'PeliculasController::store',
        ['filter' => 'role:administrador,empleado']
    );
    $routes->post(
        'desactivar/(:num)',
        'PeliculasController::deactivate/$1',
        ['filter' => 'role:administrador,empleado']
    );
    $routes->post(
        'activar/(:num)',
        'PeliculasController::activate/$1',
        ['filter' => 'role:administrador,empleado']
    );
    $routes->post(
        'update/(:num)',
        'PeliculasController::update/$1',
        ['filter' => 'role:administrador,empleado']
    );
    $routes->post(
        'delete/(:num)',
        'PeliculasController::delete/$1',
        ['filter' => 'role:administrador,empleado']
    );
});

$routes->get(
    'perfil',
    'PerfilController::edit'
);
$routes->post(
    'perfil',
    'PerfilController::update'
);

$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    $routes->post(
        'auth/login',
        'AuthController::login'
    );
    $routes->get(
        'peliculas',
        'PeliculasController::index'
    );
    $routes->get(
        'peliculas/(:num)',
        'PeliculasController::show/$1'
    );
});