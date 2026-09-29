<?php
require __DIR__ . '/../includes/auth.php';

$base = base_path();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($base . '/login.php');
}
csrf_check();

$email = trim($_POST['email'] ?? '');
$user = $email !== '' ? db_find_user_by_email($email) : null;

// Deliberately vague if the account doesn't exist or is already
// verified, so this endpoint can't be used to probe which emails are
// registered.
$genericMessage = 'If that account exists and is not yet verified, a new verification email has been sent.';

if ($user === null || is_user_verified($user) || $user['status'] !== 'active') {
    flash('info', $genericMessage);
    redirect($base . '/login.php');
}

$cooldown = verification_resend_cooldown_remaining($user);
if ($cooldown > 0) {
    flash('info', 'Please wait ' . $cooldown . ' more second' . ($cooldown === 1 ? '' : 's') . ' before requesting another verification email.');
    redirect($base . '/login.php');
}

$token = set_user_verification_token((int)$user['id']);
[$sent, $mailError] = send_verification_email($user, $token);

if ($sent) {
    flash('success', $genericMessage);
} else {
    flash('danger', 'Could not send the verification email (' . $mailError . '). Please contact the administrator.');
}

redirect($base . '/login.php');
