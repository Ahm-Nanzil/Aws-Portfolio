<?php
require __DIR__ . '/includes/auth.php';

if (db_count_users() === 0) {
    redirect(base_path() . '/setup.php');
}
if (is_logged_in()) {
    redirect(base_path() . '/dashboard.php');
}

$error = '';
$checkEmailMessage = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$ok, $message, $needsVerification] = register_student(
        $_POST['name'] ?? '',
        $_POST['email'] ?? '',
        $_POST['password'] ?? '',
        $_POST['password_confirm'] ?? ''
    );
    if ($ok && $needsVerification) {
        $checkEmailMessage = $message;
    } elseif ($ok) {
        flash('success', $message);
        redirect(base_path() . '/dashboard.php');
    } else {
        $error = $message;
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register · German University Research Manager</title>
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
      <h1 class="auth-brand-headline">Every university,<br>every deadline,<br>one dashboard.</h1>
      <p class="auth-brand-sub">Your own private account — admission requirements, fees, documents, and notes, organized by program.</p>
    </div>
    <div class="auth-brand-foot">German University Research Manager</div>
  </div>
  <div class="auth-form-panel">
    <div class="auth-form-inner">
      <h4 class="mb-1">Create your account</h4>
      <p class="text-muted small mb-4">Start organizing your German university research.</p>
      <?php if ($checkEmailMessage): ?>
        <div class="text-center py-3">
          <div class="fs-1 mb-3 text-primary"><i class="bi bi-envelope-paper"></i></div>
          <p><?= h($checkEmailMessage) ?></p>
          <p class="text-muted small">Didn't get it? Check your spam folder, or try logging in to resend it.</p>
          <a href="login.php" class="btn btn-outline-secondary btn-sm">Back to Log In</a>
        </div>
      <?php else: ?>
      <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" required autofocus value="<?= h($_POST['name'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required value="<?= h($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required minlength="8">
          <div class="form-text">At least 8 characters.</div>
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="password_confirm" class="form-control" required minlength="8">
        </div>
        <button type="submit" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Create Account</button>
      </form>
      <?php endif; ?>
      <p class="text-center small text-muted mt-4 mb-0">Already have an account? <a href="login.php">Log in</a></p>
    </div>
  </div>
</div>
</body>
</html>
