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

$formData = [
    'niche' => (string)$campaign['niche'],
    'country' => (string)$campaign['country'],
    'city' => (string)($campaign['city'] ?? ''),
    'company_size' => (string)$campaign['company_size'],
    'job_titles' => parseJobTitles((string)$campaign['job_titles']),
    'outreach_tone' => (string)$campaign['outreach_tone'],
    'daily_limit' => (int)$campaign['daily_limit'],
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
            $update = $pdo->prepare('UPDATE campaigns SET niche = :niche, country = :country, city = :city, company_size = :company_size, job_titles = :job_titles, outreach_tone = :outreach_tone, daily_limit = :daily_limit WHERE id = :id');
            $update->execute([
                ':niche' => $formData['niche'],
                ':country' => $formData['country'],
                ':city' => $formData['city'] !== '' ? $formData['city'] : null,
                ':company_size' => $formData['company_size'],
                ':job_titles' => json_encode($formData['job_titles'], JSON_UNESCAPED_UNICODE),
                ':outreach_tone' => $formData['outreach_tone'],
                ':daily_limit' => $formData['daily_limit'],
                ':id' => $id,
            ]);

            header('Location: /ai-leadgen/campaigns/view.php?id=' . $id . '&success=updated');
            exit;
        }
    }
}

renderHeader('Edit Campaign', 'campaigns');
?>
<section class="page-header-row">
    <h2>Edit Campaign</h2>
</section>

<?php if (!empty($errors['csrf'])): ?><div class="alert alert-error"><?= e($errors['csrf']) ?></div><?php endif; ?>

<?php
$submitLabel = 'Update Campaign';
include __DIR__ . '/_form.php';
renderFooter();
