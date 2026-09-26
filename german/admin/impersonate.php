<?php
require __DIR__ . '/../includes/auth.php';
require_admin();

$userId = (int)($_GET['user_id'] ?? 0);
$token = $_GET['t'] ?? '';
if (!hash_equals(csrf_token(), $token)) {
    http_response_code(400);
    die('Invalid or expired link. Please go back to the Admin Panel and try again.');
}

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
