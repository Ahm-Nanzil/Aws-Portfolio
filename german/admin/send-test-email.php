<?php
require __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
csrf_check();

$to = trim($_POST['test_email'] ?? '');
if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
    flash('danger', 'Please enter a valid email address.');
    redirect('index.php');
}

[$sent, $error] = send_test_email($to);

if ($sent) {
    flash('success', 'Test email sent to ' . $to . '. Check the inbox (and spam folder) to confirm it arrived.');
} else {
    flash('danger', 'Could not send test email: ' . $error);
}

redirect('index.php');
