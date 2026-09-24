<?php
require __DIR__ . '/includes/auth.php';

// Only usable before any account exists at all. Once someone has
// registered or the admin has been created, this page refuses to run
// again — further admins (if ever needed) can be promoted directly in
// the database: UPDATE users SET role='admin' WHERE email='...';
if (db_count_users() > 0) {
    flash('info', 'Setup has already been completed. Please log in.');
    redirect(base_path() . '/login.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (db_count_users() > 0) {
        // Race-condition guard in case two people load this page at once.
        redirect(base_path() . '/login.php');
    } else {
        $id = db_create_user($name, $email, password_hash($password, PASSWORD_DEFAULT), 'admin');
        $row = db_find_user_by_id($id);
        login_user($row);
        flash('success', 'Admin account created. Welcome, ' . $name . '!');
        redirect(base_path() . '/admin/index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>First-time Setup · German University Research Manager</title>
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
        <h4 class="mb-0">Welcome</h4>
        <p class="text-muted small">This looks like a brand-new install. Create the administrator account to get started.</p>
      </div>
      <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Your Name</label>
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
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-shield-check me-1"></i>Create Admin Account</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
