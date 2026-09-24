<?php
require __DIR__ . '/../includes/auth.php';
require_login();
$userId = effective_user_id();
$base = base_path();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($base . '/universities.php');
}
csrf_check();

$redirectTo = safe_redirect_target($_POST['redirect_to'] ?? null, $base . '/universities.php');

$id = (int)($_POST['id'] ?? 0);

$fields = [
    'name' => trim($_POST['name'] ?? ''),
    'officialName' => trim($_POST['officialName'] ?? ''),
    'city' => trim($_POST['city'] ?? ''),
    'state' => trim($_POST['state'] ?? ''),
    'type' => trim($_POST['type'] ?? 'Public'),
    'website' => trim($_POST['website'] ?? ''),
    'intlWebsite' => trim($_POST['intlWebsite'] ?? ''),
    'applicationPortal' => trim($_POST['applicationPortal'] ?? ''),
    'applicationMethod' => trim($_POST['applicationMethod'] ?? 'Direct'),
    'applicationFee' => trim($_POST['applicationFee'] ?? ''),
    'tuitionFee' => trim($_POST['tuitionFee'] ?? ''),
    'semesterContribution' => trim($_POST['semesterContribution'] ?? ''),
    'generalNotes' => trim($_POST['generalNotes'] ?? ''),
    'status' => trim($_POST['status'] ?? 'Not Started'),
];

if ($fields['name'] === '') {
    flash('danger', 'University name is required.');
    redirect($redirectTo);
}

foreach (['website', 'intlWebsite', 'applicationPortal'] as $urlField) {
    if (!is_valid_url($fields[$urlField])) {
        flash('danger', 'Please enter a valid URL for "' . $urlField . '".');
        redirect($redirectTo);
    }
}

$validStatuses = ['Not Started', 'Researching', 'Completed', 'Shortlisted', 'Applied', 'Offer Received', 'Rejected'];
if (!in_array($fields['status'], $validStatuses, true)) {
    $fields['status'] = 'Not Started';
}

if ($id > 0) {
    $existing = get_university($userId, $id);
    if ($existing === null) {
        flash('danger', 'University not found.');
        redirect($base . '/universities.php');
    }
    update_university($userId, $id, $fields);
    flash('success', 'University "' . $fields['name'] . '" updated.');
    redirect($redirectTo);
}

$newId = create_university($userId, $fields);
flash('success', 'University "' . $fields['name'] . '" added.');
// After creating a brand-new university from the quick-add modal (which
// can be triggered from any page), send the user straight to its detail
// page so they can start adding programs right away.
redirect($base . '/university.php?id=' . $newId);
