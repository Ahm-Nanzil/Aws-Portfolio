<?php
require __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
csrf_check();

$id = (int)($_POST['id'] ?? 0);
$me = current_user();

if ($id === $me['id']) {
    flash('danger', 'You cannot delete your own account.');
    redirect('index.php');
}

$user = db_find_user_by_id($id);
if ($user === null) {
    flash('danger', 'User not found (already deleted?).');
    redirect('index.php');
}

if ($user['role'] === 'admin' && db_count_admins() <= 1) {
    flash('danger', 'Cannot delete the last remaining admin account.');
    redirect('index.php');
}

// If the admin happened to be impersonating this user, stop first.
if (is_impersonating() && (int)($_SESSION['impersonate_user_id'] ?? 0) === $id) {
    stop_impersonation();
}

db_delete_user($id); // cascades to their universities & programs
flash('success', 'Deleted "' . $user['name'] . '" and all their research data.');
redirect('index.php');
