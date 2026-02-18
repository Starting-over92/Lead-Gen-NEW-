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
            $stmt = $pdo->prepare('INSERT INTO campaigns (niche, country, city, company_size, job_titles, outreach_tone, daily_limit, status) VALUES (:niche, :country, :city, :company_size, :job_titles, :outreach_tone, :daily_limit, :status)');
            $stmt->execute([
                ':niche' => $formData['niche'],
                ':country' => $formData['country'],
                ':city' => $formData['city'] !== '' ? $formData['city'] : null,
                ':company_size' => $formData['company_size'],
                ':job_titles' => json_encode($formData['job_titles'], JSON_UNESCAPED_UNICODE),
                ':outreach_tone' => $formData['outreach_tone'],
                ':daily_limit' => $formData['daily_limit'],
                ':status' => 'active',
            ]);

            header('Location: /ai-leadgen/campaigns/index.php?success=created');
            exit;
        }
    }
}

renderHeader('Create Campaign', 'campaigns');
?>
<section class="page-header-row">
    <h2>Create Campaign</h2>
</section>

<?php if (!empty($errors['csrf'])): ?><div class="alert alert-error"><?= e($errors['csrf']) ?></div><?php endif; ?>

<?php
$submitLabel = 'Create Campaign';
include __DIR__ . '/_form.php';
renderFooter();
