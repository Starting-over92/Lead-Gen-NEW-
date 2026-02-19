<?php

declare(strict_types=1);

/**
 * Database config
 * Priority:
 * 1) Environment variables (recommended for production)
 * 2) Fallback defaults below
 */
$envHost = getenv('DB_HOST');
$envName = getenv('DB_NAME');
$envUser = getenv('DB_USER');
$envPass = getenv('DB_PASS');

$hasCompleteEnvConfig = $envHost !== false && $envHost !== ''
    && $envName !== false && $envName !== ''
    && $envUser !== false && $envUser !== ''
    && $envPass !== false;

if ($hasCompleteEnvConfig) {
    $host = $envHost;
    $dbName = $envName;
    $username = $envUser;
    $password = $envPass;
} else {
    // Hostinger fallback values (used only when a full DB_* env config is not provided).
    $host = 'localhost';
    $dbName = 'u419638158_Pooky';
    $username = 'u419638158_Pooky01';
    $password = 'Primaldevs01@@';
}

$port = (int)(getenv('DB_PORT') ?: 3306);
$charset = 'utf8mb4';

// Set APP_DEBUG=1 only temporarily when troubleshooting.
$debugMode = (getenv('APP_DEBUG') === '1');
$dbConfigSource = $hasCompleteEnvConfig ? 'environment variables' : 'fallback defaults';

/**
 * Try connection with multiple DSN variants.
 * Some shared hosts work better without explicit port in DSN.
 */
$dsnCandidates = [
    "mysql:host={$host};port={$port};dbname={$dbName};charset={$charset}",
    "mysql:host={$host};dbname={$dbName};charset={$charset}",
];

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$pdo = null;
$lastException = null;
$usedDsn = null;

foreach ($dsnCandidates as $candidate) {
    try {
        $pdo = new PDO($candidate, $username, $password, $options);
        $usedDsn = $candidate;
        break;
    } catch (PDOException $exception) {
        $lastException = $exception;
    }
}

if (!$pdo) {
    http_response_code(500);

    $message = $lastException instanceof PDOException ? $lastException->getMessage() : 'Unknown connection error';
    $errorLower = strtolower($message);

    $hint = 'Check host, DB name, username, password, and DB privileges.';

    if (str_contains($errorLower, 'access denied')) {
        $hint = 'Access denied: DB username or password is incorrect, or user has no privileges on the selected DB.';
    } elseif (str_contains($errorLower, 'unknown database')) {
        $hint = 'Unknown database: DB name is wrong (on Hostinger it is usually prefixed, e.g. u123456789_dbname).';
    } elseif (str_contains($errorLower, 'getaddrinfo') || str_contains($errorLower, 'name or service not known')) {
        $hint = 'DB host is invalid. Use the exact host shown in Hostinger hPanel (commonly localhost).';
    } elseif (str_contains($errorLower, 'connection refused')) {
        $hint = 'Connection refused: wrong host/port, or DB server is not reachable from this account.';
    }

    error_log('DB connection failed: ' . $message);

    if ($debugMode) {
        echo '<h3>Database connection failed</h3>';
        echo '<p><strong>Hint:</strong> ' . htmlspecialchars($hint, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><strong>Error:</strong> ' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><strong>DB config source:</strong> ' . htmlspecialchars($dbConfigSource, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><strong>Tried user:</strong> ' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><strong>Tried DSN(s):</strong> ' . htmlspecialchars(implode(' | ', $dsnCandidates), ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<hr>';
    }

    exit('Database connection failed. ' . $hint . ' Update /includes/db.php (or DB_* env variables), then refresh.');
}

// Optional ping-style query to fail fast if connection is stale.
$pdo->query('SELECT 1');
