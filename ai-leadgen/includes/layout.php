<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function renderHeader(string $pageTitle, string $activeMenu = 'dashboard'): void
{
    $menu = [
        'dashboard' => ['label' => 'Dashboard', 'href' => '/ai-leadgen/campaigns/dashboard.php'],
        'campaigns' => ['label' => 'Campaigns', 'href' => '/ai-leadgen/campaigns/index.php'],
        'leads' => ['label' => 'Leads', 'href' => '#'],
        'outreach' => ['label' => 'Outreach', 'href' => '#'],
        'settings' => ['label' => 'Settings', 'href' => '#'],
    ];

    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e($pageTitle) ?> | AI Lead Gen</title>
        <link rel="stylesheet" href="/ai-leadgen/assets/css/style.css">
    </head>
    <body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">AI Lead Gen</div>
            <nav class="menu">
                <?php foreach ($menu as $key => $item): ?>
                    <a class="menu-item <?= $key === $activeMenu ? 'active' : '' ?>" href="<?= e($item['href']) ?>">
                        <?= e($item['label']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>
        <div class="main-wrapper">
            <header class="topbar">
                <h1>AI Lead Gen System</h1>
            </header>
            <main class="content">
    <?php
}

function renderFooter(): void
{
    ?>
            </main>
        </div>
    </div>
    <script src="/ai-leadgen/assets/js/job_titles.js"></script>
    </body>
    </html>
    <?php
}
