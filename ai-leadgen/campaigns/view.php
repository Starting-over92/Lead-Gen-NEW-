<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
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

$jobTitles = parseJobTitles((string)$campaign['job_titles']);
$campaignName = generateCampaignName((string)$campaign['niche'], (string)$campaign['country'], (string)($campaign['city'] ?? ''));

renderHeader('Campaign Details', 'campaigns');
?>
<section class="page-header-row between">
    <h2><?= e($campaignName) ?></h2>
    <a href="/ai-leadgen/campaigns/edit.php?id=<?= (int)$campaign['id'] ?>" class="btn btn-primary">Edit Campaign</a>
</section>

<?php if (isset($_GET['success']) && $_GET['success'] === 'updated'): ?>
    <div class="alert alert-success">Campaign updated successfully.</div>
<?php endif; ?>

<div class="grid-2">
    <div class="card">
        <h3>Campaign Information</h3>
        <p><strong>Niche:</strong> <?= e((string)$campaign['niche']) ?></p>
        <p><strong>Country:</strong> <?= e((string)$campaign['country']) ?></p>
        <p><strong>City:</strong> <?= e((string)($campaign['city'] ?: '-')) ?></p>
        <p><strong>Company Size:</strong> <?= e((string)$campaign['company_size']) ?></p>
        <p><strong>Outreach Tone:</strong> <?= e((string)$campaign['outreach_tone']) ?> (<?= e(toneDescription((string)$campaign['outreach_tone'])) ?>)</p>
        <p><strong>Daily Limit:</strong> <?= e((string)$campaign['daily_limit']) ?> emails/day</p>
        <p><strong>Status:</strong> <span class="status <?= e((string)$campaign['status']) ?>"><?= e(ucfirst((string)$campaign['status'])) ?></span></p>
    </div>

    <div class="card">
        <h3>Target Job Titles</h3>
        <div class="badge-wrap">
            <?php foreach ($jobTitles as $title): ?>
                <span class="badge"><?= e($title) ?></span>
            <?php endforeach; ?>
        </div>
        <hr>
        <h3>AI Lead Generation Rules Summary</h3>
        <p>
            Target niche: <?= e((string)$campaign['niche']) ?>
            <?php if (!empty($campaign['city'])): ?>in <?= e((string)$campaign['city']) ?>, <?= e((string)$campaign['country']) ?><?php else: ?>in <?= e((string)$campaign['country']) ?><?php endif; ?>.
            Company size: <?= e((string)$campaign['company_size']) ?>.
            Target job titles: <?= e(implode(', ', $jobTitles)) ?>.
            Outreach tone: <?= e((string)$campaign['outreach_tone']) ?>.
            Daily outreach limit: <?= e((string)$campaign['daily_limit']) ?> emails/day.
        </p>
    </div>
</div>
<?php renderFooter(); ?>
