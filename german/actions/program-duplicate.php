<?php
require __DIR__ . '/../includes/auth.php';
require_login();
$userId = effective_user_id();
$base = base_path();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($base . '/universities.php');
}
csrf_check();

$progId = (int)($_POST['id'] ?? 0);

$original = get_program($userId, $progId);
if ($original === null) {
    flash('danger', 'Program not found.');
    redirect($base . '/universities.php');
}

$newId = duplicate_program($userId, $progId);
if ($newId === null) {
    flash('danger', 'Failed to duplicate program.');
    redirect($base . '/university.php?id=' . (int)$original['uni_id']);
}

flash('success', 'Program duplicated as "' . $original['name'] . ' (Copy)".');
redirect($base . '/program.php?id=' . $newId);
