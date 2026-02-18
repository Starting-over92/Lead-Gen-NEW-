<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/layout.php';

$formData = [
    'niche' => '',
    'country' => '',
    'city' => '',
    'company_size' => '',
    'job_titles' => [],
    'outreach_tone' => '',
    'daily_limit' => 50,
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors['csrf'] = 'Security check failed. Please try again.';
    } else {
        $validation = validateCampaignInput($_POST);
        $errors = $validation['errors'];
        $formData = $validation['values'];

        if (empty($errors)) {
            try {
                $createdId = createCampaign($pdo, $formData);
                header('Location: /ai-leadgen/campaigns/view.php?id=' . $createdId . '&success=created');
                exit;
            } catch (Throwable $exception) {
                $errors['general'] = 'Unable to save campaign right now. Please try again.';
            }
        }
    }
}

renderHeader('Create Campaign', 'campaigns');
?>
<section class="page-header-row">
    <h2>Create Campaign</h2>
</section>

<?php if (!empty($errors['csrf'])): ?><div class="alert alert-error"><?= e($errors['csrf']) ?></div><?php endif; ?>
<?php if (!empty($errors['general'])): ?><div class="alert alert-error"><?= e($errors['general']) ?></div><?php endif; ?>

<?php
$submitLabel = 'Create Campaign';
include __DIR__ . '/_form.php';
renderFooter();
