<?php

namespace App\Middleware;

/**
 * Class Throttle
 *
 * Implements a simple **IP + path-based rate limiting system** using
 * temporary files as storage.
 *
 * **Purpose:**
 * - Limits the number of requests a single IP can make to a given path
 *   within a fixed time window (default: 60 seconds).
 * - Helps prevent brute-force attacks and API abuse.
 *
 * **How It Works:**
 * 1. **Key Generation** — Each client is identified by their IP address
 *    combined with the request path (`$req->path()`), hashed into a filename.
 * 2. **Storage** — Request timestamps are stored in a JSON file in the system's
 *    temporary directory (`sys_get_temp_dir()`).
 * 3. **Cleanup** — On each request, timestamps older than the window
 *    (default: 60 seconds) are discarded.
 * 4. **Enforcement** — If the request count in the current window meets or
 *    exceeds `$max`, the middleware returns **HTTP 429 Too Many Requests**.
 *
 * **Example Usage:**
 * ```php
 * use App\Middleware\Throttle;
 * $router->post('/contact', 'ContactController@submit', [
 *     Throttle::perMinute(10)
 * ]);
 * ```
 *
 * **Notes:**
 * - This is **not** distributed; each PHP process/server node has its own temp storage.
 * - For production-grade distributed rate limiting, use Redis or another shared store.
 * - IP detection relies on `$_SERVER['REMOTE_ADDR']`; ensure your server is configured properly.
 *
 * @package App\Middleware
 */
final class Throttle
{
    /**
     * Create a middleware that limits requests per minute.
     *
     * @param int $max The maximum allowed requests within the window (default: 20).
     * @return callable Middleware closure `(Request $req, callable $next) => mixed`.
     */
    public static function perMinute(int $max = 20): callable
    {
        return function ($req, $next) use ($max) {
            $ip  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $key = sys_get_temp_dir() . '/mivra_throttle_' . md5($ip . $req->path());
            $now = time();
            $win = 60; // 60-second sliding window

            // Load previous request timestamps
            $data = is_file($key)
                ? json_decode((string)file_get_contents($key), true)
                : [];

            // Keep only timestamps within the last $win seconds
            $data = array_values(array_filter($data ?: [], fn($t) => $t > $now - $win));

            // If limit reached, deny request
            if (count($data) >= $max) {
                http_response_code(429);
                header('Content-Type: text/plain; charset=utf-8');
                echo 'Too Many Requests';
                return null;
            }

            // Record the current request time
            $data[] = $now;
            file_put_contents($key, json_encode($data));

            return $next($req);
        };
    }
}
