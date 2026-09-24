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
$op = trim($_POST['op'] ?? ''); // add | update_status | delete

$program = get_program($userId, $progId);
if ($program === null) {
    flash('danger', 'Program not found.');
    redirect($base . '/universities.php');
}

$redirectTo = $base . '/program.php?id=' . $progId . '&tab=documents';
$validStatuses = ['Required', 'Optional', 'Not Required', 'Unknown', 'Ready', 'Missing'];

switch ($op) {
    case 'add':
        $name = trim($_POST['doc_name'] ?? '');
        $status = trim($_POST['doc_status'] ?? 'Required');
        if (!in_array($status, $validStatuses, true)) $status = 'Required';
        if ($name === '') {
            flash('danger', 'Please enter a document name.');
            break;
        }
        add_document($userId, $progId, $name, $status);
        flash('success', 'Document "' . $name . '" added to checklist.');
        break;

    case 'update_status':
        $docId = trim($_POST['doc_id'] ?? '');
        $newStatus = trim($_POST['doc_status'] ?? 'Unknown');
        if (!in_array($newStatus, $validStatuses, true)) $newStatus = 'Unknown';
        update_document_status($userId, $progId, $docId, $newStatus);
        flash('success', 'Document status updated.');
        break;

    case 'delete':
        $docId = trim($_POST['doc_id'] ?? '');
        delete_document($userId, $progId, $docId);
        flash('success', 'Document removed from checklist.');
        break;

    default:
        flash('danger', 'Unknown document action.');
}

redirect(safe_redirect_target($_POST['redirect_to'] ?? null, $redirectTo));
