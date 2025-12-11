<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('admin', ['filter' => 'guest'], function($routes) {
    $routes->get('login', 'Admin\Auth::login');
    $routes->post('login', 'Admin\Auth::loginPost');
});

$routes->get('admin/logout', 'Admin\Auth::logout');

$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('slayt', 'Admin\Sliders::editPage');
    $routes->get('slayt/edit', 'Admin\Sliders::editPage');
    $routes->post('sliders', 'Admin\Sliders::store');
    $routes->get('sliders/(:num)', 'Admin\Sliders::show/$1');
    $routes->post('sliders/(:num)', 'Admin\Sliders::update/$1');
    $routes->delete('sliders/(:num)', 'Admin\Sliders::delete/$1');
    $routes->get('blog', 'Admin\Posts::index');
    $routes->post('posts', 'Admin\Posts::store');
    $routes->get('posts/(:num)', 'Admin\Posts::show/$1');
    $routes->post('posts/(:num)', 'Admin\Posts::update/$1');
    $routes->delete('posts/(:num)', 'Admin\Posts::delete/$1');
});

$routes->get('admin', 'Admin\Auth::default');
