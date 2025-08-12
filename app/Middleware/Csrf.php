<?php

namespace App\Middleware;

/**
 * Class Csrf
 *
 * Implements a **session-based Cross-Site Request Forgery (CSRF) protection system**.
 * 
 * **Purpose:**
 * - Generates and stores a CSRF token in the session.
 * - Provides a helper to retrieve the token for embedding in HTML forms.
 * - Offers middleware to automatically reject unsafe HTTP requests
 *   (POST, PUT, DELETE) without a valid CSRF token.
 *
 * **How It Works:**
 * 1. **Initialization** — Call `Csrf::start()` once per request (e.g., in `public/index.php`)
 *    to ensure a CSRF token exists in the session.
 * 2. **Form Usage** — Insert the token into forms:
 *    ```php
 *    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token()) ?>">
 *    ```
 * 3. **Validation** — Attach `Csrf::check()` middleware to routes that modify data.
 *    If the token is missing or invalid, a **419 Page Expired** error is returned.
 *
 * **Security Notes:**
 * - Requires an active PHP session (`session_start()` must be called).
 * - Token is regenerated only on first creation; you may implement rotation if desired.
 * - Validation applies only to state-changing requests (POST, PUT, DELETE).
 *
 * @package App\Middleware
 */
final class Csrf
{
    /**
     * Start the session if not active, and ensure a CSRF token exists.
     *
     * @return void
     */
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['csrf'] = $_SESSION['csrf'] ?? bin2hex(random_bytes(16));
    }

    /**
     * Get the current CSRF token from the session.
     *
     * @return string The CSRF token string (empty if not set).
     */
    public static function token(): string
    {
        return $_SESSION['csrf'] ?? '';
    }

    /**
     * Middleware factory to validate CSRF tokens on unsafe HTTP methods.
     *
     * **Logic:**
     * - Checks the `_csrf` field from `$_POST` or `$_GET` against the session token.
     * - Rejects the request with a 419 error if the token is missing or invalid.
     *
     * @return callable A middleware function `(Request $req, callable $next) => mixed`.
     */
    public static function check(): callable
    {
        return function ($req, $next) {
            if (in_array($req->method(), ['POST', 'PUT', 'DELETE'], true)) {
                $token = $_POST['_csrf'] ?? $_GET['_csrf'] ?? '';
                if (!$token || $token !== ($_SESSION['csrf'] ?? '')) {
                    http_response_code(419);
                    include __DIR__ . '/../Views/Error/419.php';
                    return null;
                }
            }
            return $next($req);
        };
    }
}
