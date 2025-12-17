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
    $routes->get('slider', 'Admin\Sliders::editPage');
    $routes->get('slider/edit', 'Admin\Sliders::editPage');
    $routes->post('sliders', 'Admin\Sliders::store');
    $routes->get('sliders/(:num)', 'Admin\Sliders::show/$1');
    $routes->post('sliders/(:num)', 'Admin\Sliders::update/$1');
    $routes->delete('sliders/(:num)', 'Admin\Sliders::delete/$1');
    $routes->get('blog', 'Admin\Posts::index');
    $routes->post('posts', 'Admin\Posts::store');
    $routes->get('posts/(:num)', 'Admin\Posts::show/$1');
    $routes->post('posts/(:num)', 'Admin\Posts::update/$1');
    $routes->delete('posts/(:num)', 'Admin\Posts::delete/$1');
    $routes->get('comment', 'Admin\Comments::index');
    $routes->post('comments', 'Admin\Comments::store');
    $routes->get('comments/(:num)', 'Admin\Comments::show/$1');
    $routes->post('comments/(:num)', 'Admin\Comments::update/$1');
    $routes->delete('comments/(:num)', 'Admin\Comments::delete/$1');
    $routes->get('language', 'Admin\Languages::index');
    $routes->post('languages', 'Admin\Languages::store');
    $routes->get('languages/(:num)', 'Admin\Languages::show/$1');
    $routes->post('languages/(:num)', 'Admin\Languages::update/$1');
    $routes->delete('languages/(:num)', 'Admin\Languages::delete/$1');
    $routes->get('settings', 'Admin\SiteSettings::index');
    $routes->post('settings/general', 'Admin\SiteSettings::updateSettings');
    $routes->get('settings/translation/(:num)', 'Admin\SiteSettings::showTranslation/$1');
    $routes->post('settings/translation/(:num)', 'Admin\SiteSettings::updateTranslation/$1');
    $routes->post('content/update-order', 'Admin\ContentOrder::update');
});

$routes->get('admin', 'Admin\Auth::default');
