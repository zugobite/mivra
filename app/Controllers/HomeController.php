<?php

/**
 * HomeController
 *
 * Handles user-related routes such as displaying
 * the home page.
 *
 * @package Mivra\Controllers
 */

class HomeController
{

    /**
     * Display the home page.
     *
     * This method includes the home view, which is the landing page
     * for users visiting the application.
     *
     * @return void
     */
    public function index()
    {
        include __DIR__ . '/../Views/Home.php';
    }
}
