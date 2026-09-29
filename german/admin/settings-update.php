<?php
require __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
csrf_check();

$value = trim($_POST['require_email_verification'] ?? '0');
$value = ($value === '1') ? '1' : '0';

set_setting('require_email_verification', $value);

flash('success', 'Email verification is now ' . ($value === '1' ? 'enabled' : 'disabled') . '.');
redirect('index.php');
