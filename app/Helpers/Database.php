<?php

/**
 * .env Loader
 *
 * This script reads key-value pairs from a `.env` file and sets them
 * as environment variables using `putenv()`.
 *
 * Expected format in `.env`:
 * DB_HOST=your_host
 * DB_USER=your_user
 * DB_PASS=your_password
 *
 * Comments starting with '#' are ignored.
 * Empty lines are skipped.
 *
 * @package Mivra\Helpers
 * @see https://www.php.net/manual/en/function.getenv.php
 */

// Define path to the .env file
$envFile = dirname(__DIR__, 2) . '/.env';

/**
 * Load environment variables from the .env file if it exists.
 */
if (file_exists($envFile)) {
    // echo ".env file FOUND<br>";
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // echo "Processing: $line<br>";
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;

        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            putenv("$key=$value");
            // echo "Set $key<br>";
        }
    }
} else {
    // echo ".env file NOT FOUND<br>";
}
