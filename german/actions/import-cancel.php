<?php
require __DIR__ . '/../includes/auth.php';
require_login();
$userId = effective_user_id();
$base = base_path();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $token = trim($_POST['token'] ?? '');
    if ($token !== '' && get_import_session($token, $userId) !== null) {
        delete_import_session($token);
    }
    flash('info', 'Import cancelled. No changes were made.');
}

redirect($base . '/import-export.php');
