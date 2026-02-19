<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/layout.php';

$totals = [
    'all' => (int)$pdo->query('SELECT COUNT(*) FROM campaigns')->fetchColumn(),
    'active' => (int)$pdo->query("SELECT COUNT(*) FROM campaigns WHERE status = 'active'")->fetchColumn(),
    'paused' => (int)$pdo->query("SELECT COUNT(*) FROM campaigns WHERE status = 'paused'")->fetchColumn(),
    'daily_capacity' => (int)$pdo->query('SELECT COALESCE(SUM(daily_limit),0) FROM campaigns WHERE status = "active"')->fetchColumn(),
];

renderHeader('Dashboard', 'dashboard');
?>
<section class="page-header-row between">
    <h2>Dashboard Overview</h2>
    <a href="/ai-leadgen/campaigns/create.php" class="btn btn-primary">Create Campaign</a>
</section>

<div class="stat-grid">
    <a class="card stat-card stat-link" href="/ai-leadgen/campaigns/index.php?view=all">
        <h3>Total Campaigns</h3>
        <p><?= $totals['all'] ?></p>
    </a>
    <a class="card stat-card stat-link" href="/ai-leadgen/campaigns/index.php?status=active&view=all">
        <h3>Active Campaigns</h3>
        <p><?= $totals['active'] ?></p>
    </a>
    <a class="card stat-card stat-link" href="/ai-leadgen/campaigns/index.php?status=paused&view=all">
        <h3>Paused Campaigns</h3>
        <p><?= $totals['paused'] ?></p>
    </a>
    <div class="card stat-card"><h3>Daily Active Capacity</h3><p><?= $totals['daily_capacity'] ?></p></div>
</div>
<?php renderFooter(); ?>
