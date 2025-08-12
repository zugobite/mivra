<?php

/**
 * Autoloader for the App namespace.
 *
 * This file registers an autoload function that maps fully-qualified class names
 * in the `App\` namespace to file paths under the `app/` directory, following
 * PSR-4 style loading.
 *
 * Example:
 *  - Class: App\Helpers\Seo
 *  - File:  app/Helpers/Seo.php
 *
 * @package App\Core
 */

spl_autoload_register(
    /**
     * Autoload callback function.
     *
     * @param string $class Fully-qualified class name, e.g., "App\Controllers\HomeController".
     *
     * @return void
     */
    function ($class) {
        // Namespace prefix to handle
        $prefix  = 'App\\';

        // Base directory for the namespace prefix
        $baseDir = __DIR__ . '/../'; // Points to app/

        // Length of the namespace prefix
        $len = strlen($prefix);

        // If the class does not use our namespace, skip it
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        // Remove the namespace prefix from the class name
        $relative = substr($class, $len);

        // Replace namespace separators with directory separators and append ".php"
        $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

        // If the file exists, require it
        if (is_file($file)) {
            require $file;
        }
    }
);
