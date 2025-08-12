<?php

namespace App\Helpers;

/**
 * Class Asset
 *
 * Generates asset URLs (CSS, JS, images, etc.) with automatic
 * cache-busting based on the file's last modification time.
 *
 * **Purpose:**
 * - Ensures that when static assets (like CSS or JS files) are updated,
 *   browsers will load the latest version instead of using a cached copy.
 * - Achieves this by appending a `?v={timestamp}` query string based on `filemtime()`.
 *
 * **Usage Example:**
 * ```php
 * use App\Helpers\Asset;
 *
 * // Generates: /assets/css/app.css?v=1691745554
 * echo Asset::url('/assets/css/app.css');
 * ```
 *
 * **Notes:**
 * - Looks for the file under the `/public` directory.
 * - If the file does not exist, uses `0` as the version string.
 *
 * @package App\Helpers
 */
final class Asset
{
    /**
     * Generate a public URL for an asset with a cache-busting query parameter.
     *
     * @param string $path Public path to the asset (starting with a `/`).
     * @return string Asset URL with `?v=` query parameter based on modification time.
     */
    public static function url(string $path): string
    {
        $file = __DIR__ . '/../../public' . $path;
        $v = is_file($file) ? (string)filemtime($file) : '0';

        return $path . '?v=' . $v;
    }
}
