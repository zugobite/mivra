<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;

/**
 * Class HomeController
 *
 * Handles the rendering of the home page.
 * Acts as the entry point for the `/` route.
 *
 * Example usage in routes:
 * ```php
 * $router->get('/', 'HomeController@index');
 * ```
 *
 * @package App\Controllers
 */
class HomeController
{
    /**
     * Display the home page.
     *
     * @param Request               $request HTTP request instance containing query, form, and server data.
     * @param array<string, mixed>  $params  Route parameters extracted from the matched route.
     *
     * @return void
     */
    public function index(Request $request, array $params = []): void
    {
        View::render('Home', [
            'request' => $request
        ]);
    }
}
