<?php
// $pageTitle and $activeNav should be set before including this file.
$pageTitle = $pageTitle ?? 'German University Research Manager';
$activeNav = $activeNav ?? '';
?><!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($pageTitle) ?> · German University Research Manager</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= h(base_path()) ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg app-navbar sticky-top">
  <div class="container-fluid px-3">
    <button class="btn btn-link text-decoration-none d-lg-none me-1 p-1" id="sidebarToggleBtn" type="button" aria-label="Toggle navigation">
      <i class="bi bi-list fs-3"></i>
    </button>
    <a class="navbar-brand fw-semibold" href="<?= h(base_path()) ?>/dashboard.php">🇩🇪 German University Research Manager</a>
    <form class="d-none d-md-flex ms-auto me-3 global-search-form" role="search" action="<?= h(base_path()) ?>/search.php" method="get">
      <input class="form-control form-control-sm" type="search" name="q" placeholder="Search everything…" value="<?= h($_GET['q'] ?? '') ?>" style="width:260px;">
    </form>
    <button class="btn btn-sm btn-outline-secondary me-2" id="themeToggleBtn" type="button" title="Toggle light / dark mode">
      <i class="bi bi-moon-stars"></i>
    </button>
    <div class="dropdown">
      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="bi bi-person-circle me-1"></i><?= h(current_user()['name'] ?? '') ?>
        <?php if (is_admin()): ?><span class="badge text-bg-dark ms-1">Admin</span><?php endif; ?>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <?php if (is_admin()): ?>
          <li><a class="dropdown-item" href="<?= h(base_path()) ?>/admin/index.php"><i class="bi bi-shield-lock me-2"></i>Admin Panel</a></li>
          <li><hr class="dropdown-divider"></li>
        <?php endif; ?>
        <li><a class="dropdown-item text-danger" href="<?= h(base_path()) ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a></li>
      </ul>
    </div>
  </div>
</nav>
<?php if (is_impersonating()): $iu = impersonated_user(); ?>
  <div class="impersonation-banner">
    <i class="bi bi-eye-fill"></i>
    <span>Admin view: you are viewing and editing <strong><?= h($iu['name'] ?? '') ?></strong>'s (<?= h($iu['email'] ?? '') ?>) dashboard.</span>
    <a href="<?= h(base_path()) ?>/admin/stop-impersonate.php" class="btn btn-sm btn-warning">Return to Admin Panel</a>
  </div>
<?php endif; ?>
<div class="app-shell">
<?php include __DIR__ . '/sidebar.php'; ?>
  <main class="app-main">
    <div class="container-fluid py-3 px-3 px-md-4">
      <?php $flashes = get_flashes(); if ($flashes): ?>
        <div class="flash-stack mb-3">
        <?php foreach ($flashes as $f): ?>
          <div class="alert alert-<?= h($f['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= h($f['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endforeach; ?>
        </div>
      <?php endif; ?>
