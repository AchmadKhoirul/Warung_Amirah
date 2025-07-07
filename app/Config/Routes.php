<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('home', 'Home::index');

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

$routes->group('produk', ['filter' => 'auth'], function ($routes) { 
    $routes->get('', 'ProdukController::index');
    $routes->post('', 'ProdukController::create');
    $routes->post('edit/(:any)', 'ProdukController::edit/$1');
    $routes->get('delete/(:any)', 'ProdukController::delete/$1');
    $routes->get('download','ProdukController::download');
});

$routes->group('keranjang', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'TransaksiController::index');
    $routes->post('', 'TransaksiController::cart_add');
    $routes->post('edit', 'TransaksiController::cart_edit');
    $routes->get('delete/(:any)', 'TransaksiController::cart_delete/$1');
    $routes->get('clear', 'TransaksiController::cart_clear');
});

$routes->get('checkout', 'TransaksiController::checkout', ['filter' => 'auth']);
$routes->post('buy', 'TransaksiController::buy', ['filter' => 'auth']);

$routes->get('get-location', 'TransaksiController::getLocation', ['filter' => 'auth']);
$routes->get('get-cost', 'TransaksiController::getCost', ['filter' => 'auth']);

$routes->get('profile', 'Home::profile', ['filter' => 'auth']);

$routes->resource('api', ['controller' => 'apiController']);

$routes->get('register', 'RegisterController::index');
$routes->post('register', 'RegisterController::store');

$routes->get('admin/dashboard', 'AdminDashboardController::index', ['filter' => 'adminauth']);
$routes->get('admin/transaksi', 'AdminTransaksiController::index', ['filter' => 'adminauth']);
$routes->get('admin/transaksi/delete/(:num)', 'AdminTransaksiController::delete/$1', ['filter' => 'adminauth']);
$routes->post('transaksi/update_status/(:num)', 'TransaksiController::update_status/$1');
$routes->post('transaksi/update_status_pembayaran/(:num)', 'TransaksiController::update_status_pembayaran/$1');
$routes->post('transaksi/upload_bukti/(:num)', 'TransaksiController::upload_bukti/$1');
$routes->post('transaksi/cancel/(:num)', 'TransaksiController::cancel/$1');
$routes->get('admin/transaksi/print', 'AdminTransaksiController::printTransaksi');

$routes->get('admin/login', 'Admin\\AuthAdminController::login');
$routes->post('admin/login', 'Admin\\AuthAdminController::login');
$routes->get('admin/logout', 'Admin\\AuthAdminController::logout');

$routes->get('admin/dashboard/data', 'AdminDashboardController::data');
