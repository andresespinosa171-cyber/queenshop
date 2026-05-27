<?php
/**
 * PetShop MVC — Front Controller
 *
 * Para ejecutar:
 *   php -S localhost:8080 -t /ruta/a/pet-shop-mvc
 */

session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/core/Router.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/Model.php';
require_once __DIR__ . '/app/helpers/functions.php';

// Autoload controllers & models
spl_autoload_register(function ($class) {
    foreach (['app/controllers/', 'app/models/'] as $dir) {
        $file = __DIR__ . '/' . $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ─── Routes ───────────────────────────────────────────────────
$router = new Router();

// Dashboard
$router->get('/',           'DashboardController@index');
$router->get('/dashboard',  'DashboardController@index');

// Products
$router->get('/products',          'ProductController@index');
$router->get('/products/create',    'ProductController@create');
$router->post('/products/store',    'ProductController@store');
$router->get('/products/edit/{id}',  'ProductController@edit');
$router->post('/products/update/{id}', 'ProductController@update');
$router->post('/products/delete/{id}', 'ProductController@destroy');
$router->post('/products/restock/{id}', 'ProductController@restock');

// Sales
$router->get('/sales',         'SaleController@index');
$router->get('/sales/create',   'SaleController@create');
$router->post('/sales/store',   'SaleController@store');
$router->get('/sales/{id}',     'SaleController@show');

// API (JSON)
$router->get('/api/products', 'ProductController@apiList');

// ─── Dispatch ─────────────────────────────────────────────────
$router->dispatch();
