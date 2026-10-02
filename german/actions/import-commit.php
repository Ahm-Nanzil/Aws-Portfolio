<?php
require __DIR__ . '/../includes/auth.php';
require_login();
$userId = effective_user_id();
$base = base_path();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($base . '/import-export.php');
}
csrf_check();

$token = trim($_POST['token'] ?? '');
$mode = trim($_POST['mode'] ?? '');
$session = $token !== '' ? get_import_session($token, $userId) : null;

if ($session === null) {
    flash('danger', 'This import preview has expired or was not found. Please upload the file again.');
    redirect($base . '/import-export.php');
}

if ($session['mode'] !== $mode) {
    flash('danger', 'This import request looks inconsistent. Please start the import again.');
    delete_import_session($token);
    redirect($base . '/import-export.php');
}

$payload = $session['payload'];

try {
    if ($mode === 'replace_all') {
        if (empty($_POST['confirm_replace'])) {
            flash('danger', 'Please tick the confirmation box to proceed with Replace All.');
            redirect($base . '/import-preview.php?token=' . urlencode($token));
        }

        $before = ['universities' => count(get_universities($userId)), 'programs' => count_all_programs($userId)];
        create_import_backup($userId, 'pre_replace_all');

        $result = replace_all_user_data($userId, $payload);
        $after = ['universities' => count($result['universities']), 'programs' => count_all_programs($userId)];

        delete_import_session($token);
        flash('success', "Replace All complete. Replaced {$before['universities']} universit(y/ies) and {$before['programs']} program(s) with {$after['universities']} universit(y/ies) and {$after['programs']} program(s). A backup of your previous data was saved — see \"Recent Backups\" below if you need to restore it.");
        redirect($base . '/import-export.php');
    }

    // Smart Merge
    $plan = $session['plan'];
    $resolutions = $_POST['resolutions'] ?? [];
    if (!is_array($resolutions)) $resolutions = [];

    $summary = commit_import_plan($userId, $payload, $plan, $resolutions);
    delete_import_session($token);

    $parts = [];
    if ($summary['created_universities']) $parts[] = $summary['created_universities'] . ' new universit' . ($summary['created_universities'] === 1 ? 'y' : 'ies');
    if ($summary['updated_universities']) $parts[] = $summary['updated_universities'] . ' universit' . ($summary['updated_universities'] === 1 ? 'y' : 'ies') . ' updated';
    if ($summary['unchanged_universities']) $parts[] = $summary['unchanged_universities'] . ' already up to date';
    if ($summary['skipped_universities']) $parts[] = $summary['skipped_universities'] . ' skipped';
    if ($summary['created_programs']) $parts[] = $summary['created_programs'] . ' new program' . ($summary['created_programs'] === 1 ? '' : 's');
    if ($summary['updated_programs']) $parts[] = $summary['updated_programs'] . ' program' . ($summary['updated_programs'] === 1 ? '' : 's') . ' updated';

    if (empty($parts)) {
        flash('info', 'Import complete — everything in that file already matched your existing data exactly, so nothing changed.');
    } else {
        flash('success', 'Import complete: ' . implode(', ', $parts) . '.');
    }
    redirect($base . '/import-export.php');

} catch (\Throwable $e) {
    // The transaction inside commit_import_plan()/replace_all_user_data()
    // has already been rolled back at this point, so the database is
    // untouched — nothing was left half-written.
    flash('danger', 'The import failed and was cancelled — no changes were made to your data. (' . $e->getMessage() . ')');
    redirect($base . '/import-export.php');
}
