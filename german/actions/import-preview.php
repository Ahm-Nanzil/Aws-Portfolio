<?php
require __DIR__ . '/../includes/auth.php';
require_login();
$userId = effective_user_id();
$base = base_path();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($base . '/import-export.php');
}
csrf_check();

$mode = trim($_POST['import_mode'] ?? 'smart_merge');
if (!in_array($mode, ['smart_merge', 'replace_all'], true)) $mode = 'smart_merge';

if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
    flash('danger', 'Please choose a valid JSON file to import.');
    redirect($base . '/import-export.php');
}

$maxSize = 20 * 1024 * 1024; // 20 MB safety cap
if ($_FILES['import_file']['size'] > $maxSize) {
    flash('danger', 'File is too large to import.');
    redirect($base . '/import-export.php');
}

$contents = file_get_contents($_FILES['import_file']['tmp_name']);
$imported = json_decode($contents, true);

// Validate the file BEFORE touching anything in the database.
if (!is_array($imported) || !isset($imported['universities']) || !is_array($imported['universities'])) {
    flash('danger', 'That file does not look like a valid export from this application (missing "universities" array).');
    redirect($base . '/import-export.php');
}
foreach ($imported['universities'] as $u) {
    if (!is_array($u)) {
        flash('danger', 'That file is malformed: each university entry must be an object.');
        redirect($base . '/import-export.php');
    }
}

$originalFilename = basename($_FILES['import_file']['name']);

if ($mode === 'replace_all') {
    // Replace All doesn't need a field-by-field diff — but it still goes
    // through the same preview page so there's one consistent "review
    // before you commit" pattern, with a loud warning instead of a plan.
    $token = create_import_session($userId, $originalFilename, $imported, [], 'replace_all');
    redirect($base . '/import-preview.php?token=' . urlencode($token));
}

$plan = build_import_plan($userId, $imported);
$token = create_import_session($userId, $originalFilename, $imported, $plan, 'smart_merge');
redirect($base . '/import-preview.php?token=' . urlencode($token));
