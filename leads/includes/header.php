<?php
/**
 * includes/header.php
 *
 * Expects (optional) variables set by the calling page before include:
 *   $pageTitle    - string shown in <title> and topbar
 *   $pageSubtitle - string shown under the page title in the topbar
 *
 * Opens: <html> ... <div class="app-wrapper"> ... sidebar ... <div class="app-main"> ... topbar ... <div class="app-content">
 * Must be closed by includes/footer.php
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$pageTitle    = $pageTitle ?? 'Dashboard';
$pageSubtitle = $pageSubtitle ?? '';
$flash        = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> · <?= e(APP_NAME) ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>

<div class="app-wrapper">

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <?php require __DIR__ . '/sidebar.php'; ?>

    <div class="app-main">

        <div class="app-topbar">
            <div class="d-flex align-items-center gap-3">
                <button type="button" id="sidebarToggle" class="btn btn-light btn-icon border">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <h1 class="page-title"><?= e($pageTitle) ?></h1>
                    <?php if ($pageSubtitle): ?>
                        <div class="page-subtitle"><?= e($pageSubtitle) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= e(BASE_URL) ?>/lead-add.php" class="btn btn-primary btn-sm d-none d-sm-inline-flex align-items-center gap-1">
                    <i class="bi bi-plus-lg"></i> New Lead
                </a>
            </div>
        </div>

        <div class="app-content">

            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?>"></i>
                    <div><?= e($flash['message']) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
