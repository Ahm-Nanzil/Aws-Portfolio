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

// The form must say explicitly whether it is a creation or an update.
// A missing/invalid ID can therefore never turn an update into a creation.
$action = (string)($_POST['action'] ?? '');
if (!in_array($action, ['create', 'update'], true)) {
    flash('danger', 'Invalid request: no action was specified. Nothing was changed.');
    redirect($base . '/universities.php');
}

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

$validStatuses = [
    'Not Started',
    'Application Fee Paid',
    'Researching',
    'Completed',
    'Shortlisted',
    'Applied',
    'Offer Received',
    'Rejected'
];
if (!in_array($fields['status'], $validStatuses, true)) {
    $fields['status'] = 'Not Started';
}

$pdo = pdo();

// ---------------------------------------------------------------------
// UPDATE — exactly one explicitly selected university owned by this user
// ---------------------------------------------------------------------
if ($action === 'update') {
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false || $id === null) {
        flash('danger', 'Missing or invalid university ID. Nothing was changed.');
        redirect($base . '/universities.php');
    }
    $id = (int)$id;

    try {
        $pdo->beginTransaction();
        lock_user_row($userId);

        $existing = get_university_for_update($userId, $id);
        if ($existing === null) {
            $pdo->rollBack();
            flash('danger', 'University not found.');
            redirect($base . '/universities.php');
        }

        // Only enforce the duplicate rule when the name/state actually changes,
        // so records that already have a twin can still be edited.
        $identityChanged =
            normalize_match_key($fields['name']) !== normalize_match_key((string)$existing['name']) ||
            normalize_match_key($fields['state']) !== normalize_match_key((string)$existing['state']);
        if ($identityChanged && find_duplicate_university($userId, $fields['name'], $fields['state'], $id) !== null) {
            $pdo->rollBack();
            flash('danger', 'Another university named "' . $fields['name'] . '" already exists in your list. Nothing was changed.');
            redirect($redirectTo);
        }

        if (!update_university($userId, $id, $fields)) {
            throw new RuntimeException('The selected university could not be updated.');
        }
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('university-save (update id ' . $id . '): ' . $e->getMessage());
        flash('danger', 'Failed to save the university. Nothing was changed.');
        redirect($redirectTo);
    }

    flash('success', 'University "' . $fields['name'] . '" updated.');
    redirect($redirectTo);
}

// ---------------------------------------------------------------------
// CREATE — only when the request is explicitly a creation request
// ---------------------------------------------------------------------
if (!consume_submit_token('university_create', (string)($_POST['submit_token'] ?? ''))) {
    flash('warning', 'This form was already submitted (or has expired), so no new university was created. Please check your list.');
    redirect($base . '/universities.php');
}

try {
    $pdo->beginTransaction();
    lock_user_row($userId);

    $dup = find_duplicate_university($userId, $fields['name'], $fields['state'], null);
    if ($dup !== null) {
        $pdo->rollBack();
        flash('danger', 'A university named "' . $dup['name'] . '" already exists in your list, so no new record was created.');
        redirect($redirectTo);
    }

    $newId = create_university($userId, $fields);
    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('university-save (create): ' . $e->getMessage());
    flash('danger', 'Failed to add the university. Nothing was saved.');
    redirect($redirectTo);
}

flash('success', 'University "' . $fields['name'] . '" added.');
// After creating a brand-new university from the quick-add modal (which
// can be triggered from any page), send the user straight to its detail
// page so they can start adding programs right away.
redirect($base . '/university.php?id=' . $newId);