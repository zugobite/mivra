<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * MySQL-only database connection helper for Mivra Micro.
 *
 * Responsibilities:
 * - Provide a singleton PDO connection via `conn()`.
 * - Use `DB_DSN` directly if present, otherwise build a MySQL DSN from env vars.
 * - Restrict connections strictly to MySQL (throws error if DB_DRIVER != mysql).
 * - Configure PDO for exception-based error handling and associative fetch mode.
 *
 * Env Variables:
 *   DB_DSN        — Full DSN string (overrides others if present)
 *   DB_DRIVER     — Must be "mysql" (required unless using DB_DSN)
 *   DB_HOST       — MySQL server hostname (required if no DB_DSN)
 *   DB_PORT       — MySQL port (default: 3306)
 *   DB_NAME       — Database name (required if no DB_DSN)
 *   DB_CHARSET    — Charset (default: utf8mb4)
 *   DB_USER       — Username
 *   DB_PASS       — Password
 *
 * Usage:
 *   $pdo = \App\Core\Database::conn();
 *
 * @package Core
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function conn(): PDO
    {
        if (self::$pdo) {
            return self::$pdo;
        }

        // Prefer explicit DSN
        $dsn = $_ENV['DB_DSN'] ?? getenv('DB_DSN') ?: '';

        if ($dsn === '') {
            $driver = $_ENV['DB_DRIVER'] ?? getenv('DB_DRIVER') ?: 'mysql';
            if ($driver !== 'mysql') {
                throw new \RuntimeException(
                    "This project is configured for MySQL only. Set DB_DRIVER=mysql or define DB_DSN."
                );
            }

            $host    = $_ENV['DB_HOST']    ?? getenv('DB_HOST')    ?: '';
            $port    = $_ENV['DB_PORT']    ?? getenv('DB_PORT')    ?: '3306';
            $name    = $_ENV['DB_NAME']    ?? getenv('DB_NAME')    ?: '';
            $charset = $_ENV['DB_CHARSET'] ?? getenv('DB_CHARSET') ?: 'utf8mb4';

            if ($host === '' || $name === '') {
                throw new \RuntimeException(
                    "Missing DB config. Set either DB_DSN or DB_HOST/DB_NAME (and optionally DB_PORT/DB_CHARSET)."
                );
            }

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $host,
                $port,
                $name,
                $charset
            );
        }

        $user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: '';
        $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '';

        try {
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            throw new PDOException("DB connection failed: " . $e->getMessage(), (int)$e->getCode());
        }

        return self::$pdo;
    }
}
