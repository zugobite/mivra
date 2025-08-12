<?php

namespace App\Helpers;

/**
 * Class Url
 *
 * Provides helper methods for generating fully qualified URLs
 * for the application's base and the current request path.
 *
 * **Features:**
 * - Derives the base URL from the `APP_URL` environment variable (if set).
 * - Falls back to detecting scheme (HTTP/HTTPS) and host from `$_SERVER`.
 * - Generates the current request's full canonical URL.
 *
 * **Usage Example:**
 * ```php
 * use App\Helpers\Url;
 *
 * // Get the site base URL
 * echo Url::base(); // e.g., "https://example.com"
 *
 * // Get the current page's full URL
 * echo Url::current(); // e.g., "https://example.com/contact"
 * ```
 *
 * @package App\Helpers
 */
final class Url
{
    /**
     * Get the base URL of the application.
     *
     * Priority:
     * 1. Use `APP_URL` from environment variables if set.
     * 2. Detect scheme and host from server parameters.
     *
     * @return string Fully qualified base URL (without trailing slash).
     */
    public static function base(): string
    {
        $env = $_ENV['APP_URL'] ?? '';
        if ($env) {
            return rtrim($env, '/');
        }

        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host  = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $https . '://' . $host;
    }

    /**
     * Get the current request's full canonical URL.
     *
     * @return string Fully qualified URL to the current path.
     */
    public static function current(): string
    {
        $base = self::base();
        $path = (string)parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        return $base . $path;
    }
}
