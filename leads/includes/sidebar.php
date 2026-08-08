<?php
/**
 * includes/sidebar.php
 * Left navigation sidebar. Highlights the active page automatically.
 */

declare(strict_types=1);

$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? '');

/**
 * Return 'active' class if $files contains the current page.
 */
function navActive(string|array $files, string $currentPage): string
{
    $files = is_array($files) ? $files : [$files];
    return in_array($currentPage, $files, true) ? 'active' : '';
}
?>
<aside class="app-sidebar" id="appSidebar">
    <a href="<?= e(BASE_URL) ?>/dashboard.php" class="brand">
        <span class="brand-icon"><i class="bi bi-diagram-3-fill"></i></span>
        <span><?= e(APP_NAME) ?></span>
    </a>

    <nav class="nav flex-column">
        <div class="nav-section-title">Main</div>

        <a href="<?= e(BASE_URL) ?>/dashboard.php" class="nav-link <?= navActive('dashboard.php', $currentPage) ?>">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <a href="<?= e(BASE_URL) ?>/leads.php" class="nav-link <?= navActive(['leads.php', 'lead-view.php', 'lead-edit.php'], $currentPage) ?>">
            <i class="bi bi-people-fill"></i> Leads
        </a>

        <a href="<?= e(BASE_URL) ?>/lead-add.php" class="nav-link <?= navActive('lead-add.php', $currentPage) ?>">
            <i class="bi bi-person-plus-fill"></i> Add Lead
        </a>

        <div class="nav-section-title">Preferences</div>

        <a href="<?= e(BASE_URL) ?>/settings.php" class="nav-link <?= navActive('settings.php', $currentPage) ?>">
            <i class="bi bi-gear-fill"></i> Settings
        </a>
    </nav>

    <div class="sidebar-footer">
        &copy; <?= date('Y') ?> <?= e(APP_NAME) ?><br>
        Personal CRM &middot; v1.0
    </div>
</aside>
