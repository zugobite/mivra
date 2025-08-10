<?php

/**
 * Web Routes
 *
 * This file defines all the web-accessible routes for the application.
 * Each route maps a URI to a specific controller and method.
 *
 * @package Mivra
 */

/**
 * Define a GET route for the home page.
 *
 * When the user navigates to "/", the request will be handled by
 * the `index` method of the `HomeController` class.
 * This route is used to display the home page.
 */
$router->get('/', 'HomeController@index');