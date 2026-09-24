<?php
require __DIR__ . '/../includes/auth.php';
require_login();
$userId = effective_user_id();
$base = base_path();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($base . '/import-export.php');
}
csrf_check();

$mode = trim($_POST['import_mode'] ?? 'merge'); // replace | merge
if (!in_array($mode, ['replace', 'merge'], true)) $mode = 'merge';

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

if (!is_array($imported) || !isset($imported['universities']) || !is_array($imported['universities'])) {
    flash('danger', 'That file does not look like a valid export from this application (missing "universities" array).');
    redirect($base . '/import-export.php');
}

$count = import_user_data($userId, $imported, $mode);

if ($mode === 'replace') {
    flash('success', "Import complete. Replaced your data with $count universit" . ($count === 1 ? 'y' : 'ies') . ' from the file.');
} else {
    flash('success', "Merged $count universit" . ($count === 1 ? 'y' : 'ies') . ' from the imported file into your existing data.');
}

redirect($base . '/import-export.php');
