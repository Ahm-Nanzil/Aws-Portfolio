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

        // Preserve the English name after a record has been linked.
        $linkMasterId = null;

        if (!empty($existing['master_university_id'])) {
            // Already linked: do not allow the name to be changed.
            $fields['name'] = $existing['name'];
        } else {
            $rawMasterId = trim((string)($_POST['master_university_id'] ?? ''));

            if ($rawMasterId !== '') {
                $submittedMasterId = filter_var(
                    $rawMasterId,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );

                if ($submittedMasterId === false) {
                    $pdo->rollBack();
                    flash('danger', 'Invalid master university selection.');
                    redirect($redirectTo);
                }

                $masterStmt = $pdo->prepare("
                    SELECT
                        id,
                        COALESCE(NULLIF(TRIM(english_name), ''), name) AS name,
                        official_name,
                        city,
                        state,
                        type
                    FROM german_universities
                    WHERE id = ?
                    LIMIT 1
                ");

                $masterStmt->execute([(int)$submittedMasterId]);
                $master = $masterStmt->fetch(PDO::FETCH_ASSOC);

                if (!$master) {
                    $pdo->rollBack();
                    flash('danger', 'The selected university was not found.');
                    redirect($redirectTo);
                }

                // Replace submitted identity fields with master-list values.
                $fields['name'] = $master['name'];
                $fields['officialName'] = $master['official_name'];
                $fields['city'] = $master['city'];
                $fields['state'] = $master['state'];
                $fields['type'] = $master['type'];

                $linkMasterId = (int)$master['id'];
            } elseif (
                normalize_match_key($fields['name']) !==
                normalize_match_key((string)$existing['name'])
            ) {
                $pdo->rollBack();
                flash('danger', 'Please select the correct university from the suggestions.');
                redirect($redirectTo);
            }
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
                throw new RuntimeException(
                    'The selected university could not be updated.'
                );
            }

            if ($linkMasterId !== null) {
                $linkStmt = $pdo->prepare("
                    UPDATE universities
                    SET master_university_id = ?
                    WHERE id = ?
                    AND user_id = ?
                    AND master_university_id IS NULL
                ");

                $linkStmt->execute([$linkMasterId, $id, $userId]);

                if ($linkStmt->rowCount() !== 1) {
                    throw new RuntimeException(
                        'Could not save the master university link.'
                    );
                }
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

/*
 * Require selection from the master university list.
 * Only applies to CREATE; existing UPDATE logic remains unchanged.
 */
$masterUniversityId = filter_var(
    $_POST['master_university_id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($masterUniversityId === false || $masterUniversityId === null) {
    flash('danger', 'Please select a university from the suggestions.');
    redirect($redirectTo);
}

$masterStmt = $pdo->prepare("
    SELECT
        id,
        COALESCE(NULLIF(TRIM(english_name), ''), name) AS name,
        official_name,
        city,
        state,
        type,
        website
    FROM german_universities
    WHERE id = ?
    LIMIT 1
");

$masterStmt->execute([(int)$masterUniversityId]);
$masterUniversity = $masterStmt->fetch(PDO::FETCH_ASSOC);

if (!$masterUniversity) {
    flash('danger', 'The selected university was not found in the master list.');
    redirect($redirectTo);
}

// Replace browser-submitted identity fields with trusted database values.
$fields['name'] = $masterUniversity['name'];
$fields['officialName'] = $masterUniversity['official_name'];
$fields['city'] = $masterUniversity['city'];
$fields['state'] = $masterUniversity['state'];
$fields['type'] = $masterUniversity['type'];

// Use the master website when the form has no website.
if ($fields['website'] === '') {
    $fields['website'] = $masterUniversity['website'] ?? '';
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

    $linkStmt = $pdo->prepare("
        UPDATE universities
        SET master_university_id = ?
        WHERE id = ? AND user_id = ?
    ");
    $linkStmt->execute([
        (int)$masterUniversityId,
        $newId,
        $userId
    ]);

    if ($linkStmt->rowCount() !== 1) {
        throw new RuntimeException(
            'Could not save the master university link.'
        );
    }

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