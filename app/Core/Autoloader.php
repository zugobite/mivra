<?php

/**
 * PSR-4 Autoloader for the `App\` namespace.
 *
 * This lightweight autoloader maps fully-qualified class names
 * in the `App\` namespace to file paths in the `app/` directory.
 * It avoids Composer to keep the framework dependency-free.
 *
 * Example:
 * ```
 * App\Controllers\HomeController
 * → app/Controllers/HomeController.php
 * ```
 *
 * @package App\Core
 */

spl_autoload_register(
    /**
     * Autoload callback function.
     *
     * @param string $class Fully-qualified class name (e.g., `App\Controllers\HomeController`).
     *
     * @return void
     */
    function (string $class): void {
        // Namespace prefix handled by this autoloader
        $prefix  = 'App\\';

        // Base directory for namespace prefix
        $baseDir = __DIR__ . '/../';

        // Ensure the class belongs to the App namespace
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        // Convert namespace to file path
        $relative = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

        // Load the file if it exists
        if (is_file($file)) {
            require $file;
        }
    }
);
