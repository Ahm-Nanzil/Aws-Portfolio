<?php
require __DIR__ . '/includes/auth.php';

$token = trim($_GET['token'] ?? '');
$state = 'invalid'; // invalid | expired | already | success
$user = null;

if ($token !== '') {
    $user = db_find_user_by_verification_token($token);
    if ($user !== null) {
        if (is_user_verified($user)) {
            $state = 'already';
        } elseif (!empty($user['verification_token_expires']) && strtotime($user['verification_token_expires']) < time()) {
            $state = 'expired';
        } else {
            mark_user_verified((int)$user['id']);
            $state = 'success';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Email · German University Research Manager</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Serif:ital,wght@0,500;0,600;1,500&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="auth-body">
<div class="auth-shell">
  <div class="auth-brand-panel">
    <div>
      <div class="auth-brand-mark">🇩🇪</div>
      <h1 class="auth-brand-headline">Almost there.</h1>
      <p class="auth-brand-sub">One click to confirm your email, then your research dashboard is ready.</p>
    </div>
    <div class="auth-brand-foot">German University Research Manager</div>
  </div>
  <div class="auth-form-panel">
    <div class="auth-form-inner text-center">
      <?php if ($state === 'success'): ?>
        <div class="fs-1 mb-3 text-success"><i class="bi bi-check-circle"></i></div>
        <h4 class="mb-2">Email verified</h4>
        <p class="text-muted mb-4">Thanks, <?= h($user['name']) ?> — your account is active. You can log in now.</p>
        <a href="login.php" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-1"></i>Go to Log In</a>
      <?php elseif ($state === 'already'): ?>
        <div class="fs-1 mb-3 text-primary"><i class="bi bi-patch-check"></i></div>
        <h4 class="mb-2">Already verified</h4>
        <p class="text-muted mb-4">This email was already confirmed. You can log in whenever you're ready.</p>
        <a href="login.php" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-1"></i>Go to Log In</a>
      <?php elseif ($state === 'expired'): ?>
        <div class="fs-1 mb-3 text-warning"><i class="bi bi-hourglass-split"></i></div>
        <h4 class="mb-2">This link has expired</h4>
        <p class="text-muted mb-4">Verification links are valid for 24 hours. Request a new one below.</p>
        <form method="post" action="actions/resend-verification.php">
          <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
          <input type="hidden" name="email" value="<?= h($user['email']) ?>">
          <button type="submit" class="btn btn-primary"><i class="bi bi-envelope me-1"></i>Send a new link</button>
        </form>
      <?php else: ?>
        <div class="fs-1 mb-3 text-danger"><i class="bi bi-x-circle"></i></div>
        <h4 class="mb-2">Invalid verification link</h4>
        <p class="text-muted mb-4">This link isn't valid. It may have already been used, or the address might be incomplete.</p>
        <a href="login.php" class="btn btn-outline-secondary">Back to Log In</a>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
