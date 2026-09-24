<?php
require __DIR__ . '/includes/auth.php';

if (db_count_users() === 0) {
    redirect(base_path() . '/setup.php');
}
if (is_logged_in()) {
    redirect(base_path() . '/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    [$ok, $message] = attempt_login($email, $password);
    if ($ok) {
        flash('success', $message);
        redirect(base_path() . '/dashboard.php');
    }
    $error = $message;
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log In · German University Research Manager</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="auth-body">
<div class="auth-wrap">
  <div class="card shadow-sm auth-card">
    <div class="card-body p-4">
      <div class="text-center mb-3">
        <div class="fs-1">🇩🇪</div>
        <h4 class="mb-0">German University Research Manager</h4>
        <p class="text-muted small">Log in to your research dashboard.</p>
      </div>
      <?php foreach (get_flashes() as $f): ?>
        <div class="alert alert-<?= h($f['type']) ?>"><?= h($f['message']) ?></div>
      <?php endforeach; ?>
      <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required autofocus value="<?= h($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right me-1"></i>Log In</button>
      </form>
      <p class="text-center small text-muted mt-3 mb-0">Don't have an account? <a href="register.php">Register</a></p>
    </div>
  </div>
</div>
</body>
</html>
