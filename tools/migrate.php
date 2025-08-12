<?php

/**
 * database/migrate.php
 *
 * Database migration runner for Mivra Micro.
 *
 * Responsibilities:
 * - Load environment variables from `.env`.
 * - Establish a PDO connection using the Database helper.
 * - Locate and run any `.sql` files in `database/migrations` not yet executed.
 * - Track completed migrations in `database/.migrated.json`.
 * - Output success/error messages for each migration.
 *
 * Env Variables:
 *   DB_DSN, DB_DRIVER, DB_HOST, DB_PORT, DB_NAME, DB_CHARSET, DB_USER, DB_PASS
 *
 * Usage:
 *   php database/migrate.php
 *
 * Example:
 *   php database/migrate.php
 *   # Output:
 *   Running migration: 2025_08_12_create_users.sql
 *   Success
 *   All migrations completed.
 *
 * @package Database
 */

require __DIR__ . '/../app/Core/Autoloader.php';

use App\Helpers\Env;
use App\Core\Database as DB;

// Load environment variables
Env::load(__DIR__ . '/../.env');

// Migration tracking
$dir      = __DIR__ . '/../database/migrations';
$doneFile = __DIR__ . '/../database/.migrated.json';
$done     = is_file($doneFile)
    ? json_decode((string)file_get_contents($doneFile), true)
    : [];

// Get and sort migration files
$files = glob($dir . '/*.sql') ?: [];
sort($files);

// Connect to database
$pdo = DB::conn();

// Run pending migrations
foreach ($files as $file) {
    if (in_array(basename($file), $done, true)) {
        continue; // Skip already-run migrations
    }

    echo "Running migration: " . basename($file) . PHP_EOL;
    $sql = file_get_contents($file);

    try {
        $pdo->exec($sql);
        echo "Success\n";
        $done[] = basename($file);
    } catch (\PDOException $e) {
        echo "Error: " . $e->getMessage() . PHP_EOL;
        exit(1);
    }
}

// Save migration state
file_put_contents($doneFile, json_encode($done, JSON_PRETTY_PRINT));

echo "All migrations completed.\n";
