<?php

// Carica il file bootstrap
require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

use App\Core\ExceptionManager;
use App\Core\Logger;
use App\Http\Router;

// Set the exception handler
$exceptionHandler = new ExceptionManager();
set_exception_handler([$exceptionHandler, 'handle']);

// Initialize the router
$router = $container->getLazy(Router::class);

// Get all the route files from the routes directory
$routeFiles = glob(__DIR__ . '/../routes/*.php');
// Load the routes from the configuration files
$router->load_routes($routeFiles);

// Route the request
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router->route($uri);