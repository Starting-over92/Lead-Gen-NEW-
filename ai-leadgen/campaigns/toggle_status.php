<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /ai-leadgen/campaigns/index.php');
    exit;
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    exit('Invalid CSRF token.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /ai-leadgen/campaigns/index.php');
    exit;
}

$stmt = $pdo->prepare("UPDATE campaigns SET status = CASE WHEN status = 'active' THEN 'paused' ELSE 'active' END WHERE id = :id");
$stmt->execute([':id' => $id]);

header('Location: /ai-leadgen/campaigns/index.php');
exit;
