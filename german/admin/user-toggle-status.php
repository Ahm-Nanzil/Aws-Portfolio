<?php
require __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
csrf_check();

$id = (int)($_POST['id'] ?? 0);
$newStatus = trim($_POST['new_status'] ?? '');
$me = current_user();

if ($id === $me['id']) {
    flash('danger', 'You cannot change your own account status.');
    redirect('index.php');
}
if (!in_array($newStatus, ['active', 'disabled'], true)) {
    flash('danger', 'Invalid status.');
    redirect('index.php');
}

$user = db_find_user_by_id($id);
if ($user === null) {
    flash('danger', 'User not found.');
    redirect('index.php');
}

db_update_user_status($id, $newStatus);
flash('success', $user['name'] . ' is now ' . $newStatus . '.');
redirect('index.php');
