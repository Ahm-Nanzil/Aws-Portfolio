<?php
require __DIR__ . '/../includes/auth.php';
require_login();
$userId = effective_user_id();
$base = base_path();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($base . '/universities.php');
}
csrf_check();

$id = (int)($_POST['id'] ?? 0);
$uni = get_university($userId, $id);

if ($uni === null) {
    flash('danger', 'University not found (it may already have been deleted).');
    redirect($base . '/universities.php');
}

if (delete_university($userId, $id)) {
    flash('success', 'University "' . $uni['name'] . '" and all its programs were deleted.');
} else {
    flash('danger', 'Failed to delete university.');
}

redirect($base . '/universities.php');
