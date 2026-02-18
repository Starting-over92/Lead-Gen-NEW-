<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/layout.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /ai-leadgen/campaigns/index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM campaigns WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$campaign = $stmt->fetch();

if (!$campaign) {
    http_response_code(404);
    exit('Campaign not found.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Security check failed. Please try again.';
    } else {
        $delete = $pdo->prepare('DELETE FROM campaigns WHERE id = :id');
        $delete->execute([':id' => $id]);

        header('Location: /ai-leadgen/campaigns/index.php?success=deleted');
        exit;
    }
}

renderHeader('Delete Campaign', 'campaigns');
?>
<div class="card small-card">
    <h2>Delete Campaign</h2>
    <p>Are you sure you want to delete <strong><?= e(generateCampaignName((string)$campaign['niche'], (string)$campaign['country'], (string)($campaign['city'] ?? ''))) ?></strong>?</p>

    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <form method="post" class="inline-actions">
        <?= csrfField(); ?>
        <button type="submit" class="btn danger">Yes, Delete</button>
        <a class="btn btn-light" href="/ai-leadgen/campaigns/index.php">Cancel</a>
    </form>
</div>
<?php renderFooter(); ?>
