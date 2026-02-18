<?php

declare(strict_types=1);

/**
 * Database config
 * Priority:
 * 1) Environment variables (recommended for production)
 * 2) Fallback defaults below
 */
$host = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'ai_leadgen';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$port = (int)(getenv('DB_PORT') ?: 3306);
$charset = 'utf8mb4';

// Set APP_DEBUG=1 in environment only when troubleshooting.
$debugMode = (getenv('APP_DEBUG') === '1');

$dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $exception) {
    http_response_code(500);

    error_log('DB connection failed: ' . $exception->getMessage());

    if ($debugMode) {
        echo '<h3>Database connection failed</h3>';
        echo '<p><strong>Error:</strong> ' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><strong>Current DSN:</strong> ' . htmlspecialchars($dsn, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><strong>Current DB user:</strong> ' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<hr>';
    }

    exit('Database connection failed. Check host, DB name, username, password, and DB privileges in /includes/db.php (or DB_* environment variables).');
}
