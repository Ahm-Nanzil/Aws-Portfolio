<?php
require __DIR__ . '/../includes/auth.php';
require_login();
$userId = effective_user_id();
$base = base_path();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($base . '/programs.php');
}
csrf_check();

$progId = (int)($_POST['id'] ?? 0);
$uniId = (int)($_POST['university_id'] ?? 0);

$program = get_program($userId, $progId);
if ($program === null) {
    flash('danger', 'Program not found (it may already have been deleted).');
    redirect($base . '/universities.php');
}
$name = $program['name'];
$parentUniId = (int)$program['uni_id'];

if (delete_program($userId, $progId)) {
    flash('success', 'Program "' . $name . '" deleted.');
} else {
    flash('danger', 'Failed to delete program.');
}

$redirectTo = safe_redirect_target($_POST['redirect_to'] ?? null, $base . '/university.php?id=' . ($uniId ?: $parentUniId));
redirect($redirectTo);
