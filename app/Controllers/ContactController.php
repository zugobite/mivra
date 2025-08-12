<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Validator;
use App\Helpers\Flash;

/**
 * Class ContactController
 *
 * Handles the display and submission of the contact form.
 * Implements input validation and demonstrates JSON API responses.
 *
 * Example usage in routes:
 * ```php
 * $router->get('/contact', 'ContactController@show')->name('contact.show');
 * $router->post('/contact', 'ContactController@submit')->name('contact.submit');
 * ```
 *
 * @package App\Controllers
 */
class ContactController
{
    /**
     * Display the contact form.
     *
     * @param Request               $request HTTP request instance containing query, form, and server data.
     * @param array<string, mixed>  $params  Route parameters extracted from the matched route.
     *
     * @return void
     */
    public function show(Request $request, array $params = []): void
    {
        View::render('Contact', [
            'request' => $request
        ]);
    }

    /**
     * Handle contact form submission.
     *
     * Validates incoming POST data and returns a JSON response
     * indicating success or errors. On successful validation,
     * a success flash message is set. Email sending logic
     * should be implemented where indicated.
     *
     * @param Request               $request HTTP request instance containing POST data.
     * @param array<string, mixed>  $params  Route parameters extracted from the matched route.
     *
     * @return void
     */
    public function submit(Request $request, array $params = []): void
    {
        // Validate form inputs
        $check = Validator::make($_POST, [
            'name'    => 'required|min:2|max:80',
            'email'   => 'required|email|max:200',
            'message' => 'required|min:5|max:2000',
        ]);

        // If validation fails, return 422 JSON response
        if (!$check['valid']) {
            Response::json([
                'ok'     => false,
                'errors' => $check['errors']
            ], 422);
            return;
        }

        // TODO: Implement email sending via mail() or SMTP
        Flash::set('success', 'Thanks! Your message was sent.');

        // Return success JSON response
        Response::json(['ok' => true], 200);
    }
}
