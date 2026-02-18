<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

header('Content-Type: text/plain; charset=utf-8');

echo "AI Lead Gen Health Check\n";
echo "========================\n";
echo "Database: CONNECTED\n";

$requiredTables = ['users', 'campaigns'];

foreach ($requiredTables as $table) {
    $stmt = $pdo->prepare('SHOW TABLES LIKE :table_name');
    $stmt->execute([':table_name' => $table]);
    $exists = (bool)$stmt->fetchColumn();

    echo sprintf("Table %-10s: %s\n", $table, $exists ? 'OK' : 'MISSING');
}

$countStmt = $pdo->query('SELECT COUNT(*) FROM campaigns');
$campaignCount = (int)$countStmt->fetchColumn();
echo "Campaign rows: {$campaignCount}\n";
