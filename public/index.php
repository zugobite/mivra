<?php

/**
 * Application Front Controller.
 *
 * This is the single entry point for all HTTP requests to the application.
 * It initializes core services, loads route definitions, and dispatches requests
 * to the appropriate controllers and methods.
 *
 * Responsibilities:
 * - Enable full error reporting for development.
 * - Register the autoloader for namespaced classes.
 * - Initialize the application router.
 * - Load route definitions from the routes directory.
 * - Dispatch the matched route to its controller action.
 *
 * PHP version 8+
 *
 * @package Mivra
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Register autoloader and core classes
require_once __DIR__ . '/../app/Core/Autoloader.php';
require_once __DIR__ . '/../app/Core/Router.php';

// Instantiate the Router
$router = new Router();

/**
 * Load application routes.
 *
 * This file maps URIs to specific controller methods using
 * the Router instance created above.
 *
 * @see /routes/web.php
 */
require_once __DIR__ . '/../routes/web.php';

// Dispatch the current request to the matched controller action
$router->dispatch();
