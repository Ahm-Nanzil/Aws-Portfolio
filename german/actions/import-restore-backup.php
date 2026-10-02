<?php
require __DIR__ . '/../includes/auth.php';
require_login();
$userId = effective_user_id();
$base = base_path();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($base . '/import-export.php');
}
csrf_check();

$backupId = (int)($_POST['id'] ?? 0);
$backup = get_import_backup($userId, $backupId);

if ($backup === null) {
    flash('danger', 'That backup was not found.');
    redirect($base . '/import-export.php');
}

try {
    // Snapshot the current state too, in case this restore itself needs undoing.
    create_import_backup($userId, 'pre_restore');
    $result = replace_all_user_data($userId, $backup['payload']);
    flash('success', 'Restored backup from ' . fmt_date($backup['created_at']) . ' (' . count($result['universities']) . ' universities). Your data just before this restore was also saved as a new backup.');
} catch (\Throwable $e) {
    flash('danger', 'Restore failed and was cancelled — no changes were made. (' . $e->getMessage() . ')');
}

redirect($base . '/import-export.php');
