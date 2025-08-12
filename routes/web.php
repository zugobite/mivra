<?php

/**
 * Web Routes.
 *
 * Defines the application's HTTP routes, their handlers, and optional middleware.
 *
 * Features Demonstrated:
 * - Named routes for easy URL generation.
 * - Middleware applied per route (CSRF protection, throttling).
 * - Route parameters (supported by the router, though not used here).
 *
 * Supported HTTP Methods:
 * - GET
 * - POST
 * - PUT
 * - DELETE
 *
 * Example:
 * ```php
 * $router->get('/users/{id}', 'UserController@show')->name('user.show');
 * ```
 *
 * @package Routes
 */

use App\Core\Router;
use App\Middleware\Csrf;
use App\Middleware\Throttle;

/**
 * @var Router $router The application's router instance.
 */

// Home route
$router->get('/', 'HomeController@index')
    ->name('home');

// Contact form display
$router->get('/contact', 'ContactController@show')
    ->name('contact.show');

// Contact form submission with CSRF + request throttling
$router->post(
    '/contact',
    'ContactController@submit',
    [Csrf::check(), Throttle::perMinute(10)]
)->name('contact.submit');
