<?php

namespace App\Helpers;

/**
 * Class Env
 *
 * Minimal .env file loader with helper function `envv()`.
 *
 * Loads environment variables from a given file and makes them available via:
 * - `putenv()` (system environment)
 * - `$_ENV` (superglobal array)
 * - `$_SERVER` (superglobal array)
 *
 * Skips:
 * - Empty lines
 * - Comment lines starting with `#`
 * - Lines without an "=" separator
 *
 * Quotes around values (single or double) are stripped automatically.
 *
 * Example `.env` file:
 *   APP_ENV=local
 *   DB_HOST=localhost
 *   DB_PASS="secret"
 *
 * Usage:
 * ```php
 * use App\Helpers\Env;
 *
 * Env::load(__DIR__ . '/../.env');
 * $dbHost = envv('DB_HOST', '127.0.0.1');
 * ```
 *
 * @package App\Helpers
 */
final class Env
{
    /**
     * Load environment variables from a file path.
     *
     * @param string $path Path to the `.env` file.
     * @return void
     */
    public static function load(string $path): void
    {
        if (!is_file($path)) return;

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }
            [$k, $v] = array_map('trim', explode('=', $line, 2));
            $v = trim($v, "'\""); // strip simple quotes
            putenv("$k=$v");
            $_ENV[$k]    = $v;
            $_SERVER[$k] = $v;
        }
    }
}

/**
 * envv()
 *
 * Global helper to retrieve an environment variable.
 * Falls back to a default value if the variable is not set.
 *
 * @param string $key     Environment variable name.
 * @param mixed  $default Default value if variable is missing.
 * @return mixed          The environment variable value or the default.
 */
function envv(string $key, $default = null)
{
    $v = $_ENV[$key] ?? getenv($key);
    return $v !== false ? $v : $default;
}
