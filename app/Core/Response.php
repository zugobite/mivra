<?php

namespace App\Core;

/**
 * HTTP Response Helper.
 *
 * Provides minimal static methods for sending common types of HTTP responses:
 * - JSON responses
 * - Redirect responses
 * - HTML responses
 *
 * Each method sets the appropriate status code and headers before outputting content.
 *
 * @package App\Core
 */
final class Response
{
    /**
     * Send a JSON response.
     *
     * Outputs JSON-encoded data with the specified HTTP status code and sets
     * the `Content-Type` header to `application/json; charset=utf-8`.
     *
     * @param mixed $data The data to encode as JSON.
     * @param int   $code The HTTP status code (default: 200).
     *
     * @return void
     */
    public static function json($data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }

    /**
     * Redirect the client to a given URL.
     *
     * Sends an HTTP `Location` header with the specified URL and sets the
     * provided HTTP status code.
     *
     * @param string $to   The target URL for redirection.
     * @param int    $code The HTTP status code (default: 302 Found).
     *
     * @return void
     */
    public static function redirect(string $to, int $code = 302): void
    {
        http_response_code($code);
        header('Location: ' . $to);
    }

    /**
     * Send an HTML response.
     *
     * Outputs the provided HTML content with the specified HTTP status code
     * and sets the `Content-Type` header to `text/html; charset=utf-8`.
     *
     * @param string $html The HTML content to send.
     * @param int    $code The HTTP status code (default: 200).
     *
     * @return void
     */
    public static function html(string $html, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
    }
}
