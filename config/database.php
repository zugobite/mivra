<?php

/**
 * Database Configuration using .env
 *
 * Loads database credentials from the `.env` file using a helper script,
 * and establishes a PDO connection to the MySQL database.
 *
 * This approach helps keep sensitive credentials out of source control.
 *
 * @package Skavoo/Config
 * @see https://www.php.net/manual/en/book.pdo.php
 */

require_once __DIR__ . '/../app/Helpers/Database.php';

// Declare $pdo as global to make it accessible from other files
global $pdo;

// Retrieve credentials from .env using getenv()
$host = getenv('DB_HOST');   // MySQL hostname
$db   = getenv('DB_NAME');   // Database name
$user = getenv('DB_USER');   // MySQL username
$pass = getenv('DB_PASS');   // MySQL password

// Construct DSN (Data Source Name) for PDO
$dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

try {
    /**
     * Create a new PDO connection instance.
     *
     * @var PDO $pdo The PDO instance for database interaction
     */
    $pdo = new PDO($dsn, $user, $pass);

    // Set PDO error mode to exception for proper error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    /**
     * Output connection failure message.
     * In production, consider logging this instead of displaying it.
     *
     * @var string $e->getMessage PDO error message
     */
    echo 'Connection failed: ' . $e->getMessage();
}
