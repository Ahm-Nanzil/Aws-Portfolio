<?php
require __DIR__ . '/../includes/auth.php';
require_admin();

$userId = (int)($_GET['user_id'] ?? 0);
$me = current_user();

if ($userId === $me['id']) {
    flash('info', 'That is your own account — just use your normal dashboard.');
    redirect('index.php');
}

$target = db_find_user_by_id($userId);
if ($target === null) {
    flash('danger', 'User not found.');
    redirect('index.php');
}

start_impersonation($userId);
flash('success', 'Now viewing as ' . $target['name'] . '. Use "Return to Admin Panel" at the top of the page when you\'re done.');
redirect(base_path() . '/dashboard.php');
