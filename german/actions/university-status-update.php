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
$status = trim($_POST['status'] ?? '');
$validStatuses = ['Not Started', 'Researching', 'Completed', 'Shortlisted', 'Applied', 'Offer Received', 'Rejected'];
if (!in_array($status, $validStatuses, true)) {
    flash('danger', 'Invalid status value.');
    redirect(safe_redirect_target($_POST['redirect_to'] ?? null, $base . '/universities.php'));
}

$uni = get_university($userId, $id);
if ($uni === null) {
    flash('danger', 'University not found.');
    redirect($base . '/universities.php');
}

if (update_university_status($userId, $id, $status)) {
    flash('success', 'Status updated to "' . $status . '".');
} else {
    flash('danger', 'Failed to save data.');
}

redirect(safe_redirect_target($_POST['redirect_to'] ?? null, $base . '/universities.php'));
