<?php

/**
 * public/index.php
 *
 * Single entry point for all HTTP requests.
 *
 * Responsibilities:
 * - Boot PSR-4 autoloader and load environment variables.
 * - Serve static files directly under PHP’s built-in server.
 * - Configure session cookie params (HttpOnly, Secure, SameSite).
 * - Start session and initialize CSRF protection.
 * - Apply global security headers (XFO, XCTO, Referrer-Policy).
 * - Optional HSTS and CSP headers from .env.
 * - Convert PHP errors to exceptions.
 * - Centralized exception handling (pretty in dev, safe in prod).
 * - Optional route caching for performance.
 * - Dispatch the current request via Router.
 *
 * Usage:
 *   php -S localhost:8000 -t public
 *
 * @package Public
 */

declare(strict_types=1);

// Development error visibility
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Serve static files when using built-in server
if (PHP_SAPI === 'cli-server') {
    $reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $fsPath  = __DIR__ . $reqPath;
    if ($reqPath && is_file($fsPath)) {
        return false;
    }
}

// Autoloader
require_once __DIR__ . '/../app/Core/Autoloader.php';

use App\Helpers\Env;
use App\Middleware\Csrf;
use App\Core\Router;

// Environment
Env::load(__DIR__ . '/../.env');
$dev = (($_ENV['APP_ENV'] ?? 'local') === 'local');

// Configure session cookie params before starting session
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
} else {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', $secure ? '1' : '0');
    ini_set('session.cookie_samesite', 'Lax');
}

// Start session + CSRF
Csrf::start();

// Convert PHP errors to exceptions
set_error_handler(function (int $severity, string $message, string $file, int $line): void {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Exception handler
set_exception_handler(function (Throwable $e) use ($dev): void {
    error_log((string)$e);
    http_response_code(500);

    if ($dev) {
        header('Content-Type: text/html; charset=utf-8');
        echo "<pre style='padding:1rem;background:#111;color:#eee;white-space:pre-wrap'>"
            . htmlspecialchars((string)$e, ENT_QUOTES, 'UTF-8')
            . "</pre>";
        return;
    }

    $errView = __DIR__ . '/../app/Views/Error/500.php';
    if (is_file($errView)) {
        include $errView;
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo "500 Server Error";
    }
});

// Router + security headers
$router = new Router();
$router->use(function ($req, $next) {
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer-when-downgrade');
    if (($_ENV['HSTS'] ?? '') === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
    if (!empty($_ENV['CSP'])) {
        header('Content-Security-Policy: ' . $_ENV['CSP']);
    }
    return $next($req);
});

// Route cache
$cacheFile = __DIR__ . '/../routes/.cache.php';
if (!$router->loadCache($cacheFile)) {
    require __DIR__ . '/../routes/web.php';
    if (($_ENV['ROUTE_CACHE'] ?? 'off') === 'on') {
        $router->dumpCache($cacheFile);
    }
}

// Dispatch
$router->dispatch();
