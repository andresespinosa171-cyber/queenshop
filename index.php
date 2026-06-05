<?php
/**
 * QueenShop MVC — Front Controller
 *
 * Para ejecutar:
 *   php -S localhost:8080 -t /ruta/a/pet-shop-mvc
 */

session_start();

// Base URL detectada automáticamente (anda en subcarpeta y en raíz)
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/core/Router.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/Model.php';
require_once __DIR__ . '/app/helpers/functions.php';
require_once __DIR__ . '/app/helpers/auth.php';

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

// Auth routes (public — no login required)
$router->get('/login',     'AuthController@loginForm');
$router->post('/login',    'AuthController@login');
$router->get('/register',  'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout',    'AuthController@logout');

// Dashboard
$router->get('/',           'DashboardController@index');
$router->get('/dashboard',  'DashboardController@index');

// Accounting
$router->get('/accounting', 'AccountingController@index');

// Products
$router->get('/products',          'ProductController@index');
$router->get('/products/create',    'ProductController@create');
$router->post('/products/store',    'ProductController@store');
$router->get('/products/edit/{id}',  'ProductController@edit');
$router->post('/products/update/{id}', 'ProductController@update');
$router->post('/products/delete/{id}', 'ProductController@destroy');
$router->post('/products/restock/{id}', 'ProductController@restock');

// Clients
$router->get('/clients',           'ClientController@index');
$router->get('/clients/create',     'ClientController@create');
$router->post('/clients/store',     'ClientController@store');
$router->get('/clients/{id}',       'ClientController@show');
$router->get('/clients/edit/{id}',  'ClientController@edit');
$router->post('/clients/update/{id}', 'ClientController@update');
$router->post('/clients/delete/{id}', 'ClientController@destroy');
$router->post('/clients/pay/{id}',    'ClientController@pay');
$router->post('/clients/adjust/{id}', 'ClientController@adjust');

// Sales
$router->get('/sales',         'SaleController@index');
$router->get('/sales/create',   'SaleController@create');
$router->post('/sales/store',   'SaleController@store');
$router->get('/sales/{id}',     'SaleController@show');

// Returns
$router->get('/returns',           'ReturnController@index');
$router->get('/returns/create',     'ReturnController@create');
$router->post('/returns/store',     'ReturnController@store');
$router->get('/returns/{id}',       'ReturnController@show');

// Store switching
$router->get('/switch-store/{id}', 'SwitchController@switch');

// API (JSON)
$router->get('/api/products', 'ProductController@apiList');

// ─── Normalizar URI (saca BASE_URL del path) ──────────────────
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';
if (BASE_URL !== '' && str_starts_with($uri, BASE_URL)) {
    $uri = substr($uri, strlen(BASE_URL)) ?: '/';
}

// ─── Auth Guard ───────────────────────────────────────────────
$publicRoutes = ['/login', '/register', '/logout'];
if (!in_array($uri, $publicRoutes, true)) {
    require_login();
}

// ─── Dispatch ─────────────────────────────────────────────────
$router->dispatch($uri);
