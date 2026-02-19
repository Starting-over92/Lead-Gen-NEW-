<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/layout.php';

$search = trim((string)($_GET['search'] ?? ''));
$statusFilter = trim((string)($_GET['status'] ?? ''));
$allowedStatus = ['active', 'paused'];
if (!in_array($statusFilter, $allowedStatus, true)) {
    $statusFilter = '';
}

$page = max(1, (int)($_GET['page'] ?? 1));
$showAll = (($_GET['view'] ?? '') === 'all');
$perPage = $showAll ? 1000 : 10;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];

if ($search !== '') {
    $where[] = '(niche LIKE :search OR city LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

if ($statusFilter !== '') {
    $where[] = 'LOWER(TRIM(status)) = :status';
    $params[':status'] = strtolower($statusFilter);
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM campaigns {$whereSql}");
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRows / $perPage));

if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

if ($showAll) {
    $listSql = "SELECT * FROM campaigns {$whereSql} ORDER BY created_at DESC";
    $listStmt = $pdo->prepare($listSql);
    $listStmt->execute($params);
} else {
    $listSql = sprintf('SELECT * FROM campaigns %s ORDER BY created_at DESC LIMIT %d OFFSET %d', $whereSql, $perPage, $offset);
    $listStmt = $pdo->prepare($listSql);
    $listStmt->execute($params);
}
$campaigns = $listStmt->fetchAll();

renderHeader('Campaigns', 'campaigns');
?>
<section class="page-header-row between">
    <h2>Campaigns</h2>
    <a href="/ai-leadgen/campaigns/create.php" class="btn btn-primary">+ Create Campaign</a>
</section>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <?php if ($_GET['success'] === 'created'): ?>Campaign created successfully.
        <?php elseif ($_GET['success'] === 'deleted'): ?>Campaign deleted successfully.
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="card">
    <form method="get" class="filters">
        <?php if ($showAll): ?><input type="hidden" name="view" value="all"><?php endif; ?>
        <input type="text" name="search" placeholder="Search by niche or city" value="<?= e($search) ?>">
        <select name="status">
            <option value="">All statuses</option>
            <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="paused" <?= $statusFilter === 'paused' ? 'selected' : '' ?>>Paused</option>
        </select>
        <button type="submit" class="btn btn-light">Apply</button>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Campaign Name</th>
                <th>Niche</th>
                <th>Country</th>
                <th>City</th>
                <th>Company Size</th>
                <th>Job Titles</th>
                <th>Outreach Tone</th>
                <th>Daily Limit</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$campaigns): ?>
                <tr><td colspan="11">No campaigns found.</td></tr>
            <?php else: ?>
                <?php foreach ($campaigns as $campaign): ?>
                    <?php $jobTitles = parseJobTitles((string)$campaign['job_titles']); ?>
                    <tr>
                        <td><?= e(generateCampaignName((string)$campaign['niche'], (string)$campaign['country'], (string)($campaign['city'] ?? ''))) ?></td>
                        <td><?= e((string)$campaign['niche']) ?></td>
                        <td><?= e((string)$campaign['country']) ?></td>
                        <td><?= e((string)($campaign['city'] ?? '-')) ?></td>
                        <td><?= e((string)$campaign['company_size']) ?></td>
                        <td>
                            <div class="badge-wrap">
                                <?php foreach ($jobTitles as $title): ?>
                                    <span class="badge"><?= e($title) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td><?= e((string)$campaign['outreach_tone']) ?></td>
                        <td><?= e((string)$campaign['daily_limit']) ?></td>
                        <?php $normalizedStatus = strtolower(trim((string)$campaign['status'])); ?>
                        <td><span class="status <?= e($normalizedStatus) ?>"><?= e(ucfirst($normalizedStatus)) ?></span></td>
                        <td><?= e((string)$campaign['created_at']) ?></td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-mini" href="/ai-leadgen/campaigns/view.php?id=<?= (int)$campaign['id'] ?>">View</a>
                                <a class="btn btn-mini" href="/ai-leadgen/campaigns/edit.php?id=<?= (int)$campaign['id'] ?>">Edit</a>
                                <a class="btn btn-mini danger" href="/ai-leadgen/campaigns/delete.php?id=<?= (int)$campaign['id'] ?>">Delete</a>
                                <form method="post" action="/ai-leadgen/campaigns/toggle_status.php" class="inline-form">
                                    <?= csrfField(); ?>
                                    <input type="hidden" name="id" value="<?= (int)$campaign['id'] ?>">
                                    <button type="submit" class="btn btn-mini">
                                        <?= strtolower(trim((string)$campaign['status'])) === 'active' ? 'Pause' : 'Activate' ?>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (!$showAll): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a class="page-link <?= $i === $page ? 'active' : '' ?>" href="?<?= http_build_query(['search' => $search, 'status' => $statusFilter, 'page' => $i]) ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
<?php renderFooter(); ?>
