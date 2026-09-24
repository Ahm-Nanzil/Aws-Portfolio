<?php
/**
 * Database connection (PDO / MySQL).
 */

require_once __DIR__ . '/../config.php';

function pdo(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        if (defined('DB_DEBUG') && DB_DEBUG) {
            die('Database connection failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
        }
        die('Database connection failed. Please check the settings in config.php (and that schema.sql has been imported).');
    }
    return $pdo;
}

/** Current datetime string in MySQL DATETIME format. */
function db_now(): string {
    return date('Y-m-d H:i:s');
}
