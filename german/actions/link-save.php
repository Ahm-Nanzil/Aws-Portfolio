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
$op = trim($_POST['op'] ?? ''); // add | delete

$program = get_program($userId, $progId);
if ($program === null) {
    flash('danger', 'Program not found.');
    redirect($base . '/universities.php');
}

$redirectTo = $base . '/program.php?id=' . $progId . '&tab=links';

switch ($op) {
    case 'add':
        $title = trim($_POST['link_title'] ?? '');
        $url = trim($_POST['link_url'] ?? '');
        $desc = trim($_POST['link_description'] ?? '');
        if ($title === '' || $url === '') {
            flash('danger', 'Link title and URL are both required.');
            break;
        }
        if (!is_valid_url($url)) {
            flash('danger', 'Please enter a valid URL (including https://).');
            break;
        }
        add_link($userId, $progId, $title, $url, $desc);
        flash('success', 'Link "' . $title . '" added.');
        break;

    case 'delete':
        $linkId = trim($_POST['link_id'] ?? '');
        delete_link($userId, $progId, $linkId);
        flash('success', 'Link removed.');
        break;

    default:
        flash('danger', 'Unknown link action.');
}

redirect(safe_redirect_target($_POST['redirect_to'] ?? null, $redirectTo));
