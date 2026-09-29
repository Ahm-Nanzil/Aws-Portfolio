<?php
require __DIR__ . '/../includes/auth.php';
require_admin();

$stats = db_global_stats();
$users = db_all_users_with_stats();
$me = current_user();
$emailVerificationRequired = is_email_verification_required();

$pageTitle = 'Admin Panel';
$activeNav = 'admin';
include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <h4 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Admin Panel</h4>
  <a href="<?= h(base_path()) ?>/dashboard.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-speedometer2 me-1"></i>My Own Dashboard</a>
</div>

<div class="row g-3 mb-4 row-cols-2 row-cols-md-3 row-cols-xl-6">
  <div class="col"><div class="stat-card"><div class="stat-value"><?= $stats['totalUsers'] ?></div><div class="stat-label">Total Users</div></div></div>
  <div class="col"><div class="stat-card"><div class="stat-value"><?= $stats['totalStudents'] ?></div><div class="stat-label">Students</div></div></div>
  <div class="col"><div class="stat-card"><div class="stat-value"><?= $stats['activeUsers'] ?></div><div class="stat-label">Active</div></div></div>
  <div class="col"><div class="stat-card"><div class="stat-value"><?= $stats['disabledUsers'] ?></div><div class="stat-label">Disabled</div></div></div>
  <div class="col"><div class="stat-card"><div class="stat-value"><?= $stats['totalUniversities'] ?></div><div class="stat-label">Universities (all users)</div></div></div>
  <div class="col"><div class="stat-card"><div class="stat-value"><?= $stats['totalPrograms'] ?></div><div class="stat-label">Programs (all users)</div></div></div>
</div>

<div class="card shadow-sm">
  <div class="card-header bg-transparent fw-semibold d-flex justify-content-between align-items-center">
    <span><i class="bi bi-people me-2"></i>All Users</span>
    <span class="text-muted small"><?= count($users) ?> account<?= count($users) === 1 ? '' : 's' ?></span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Universities</th>
          <th>Programs</th>
          <th>Joined</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td class="fw-semibold"><?= h($u['name']) ?><?= (int)$u['id'] === $me['id'] ? ' <span class="badge text-bg-light border">You</span>' : '' ?></td>
            <td><?= h($u['email']) ?></td>
            <td><span class="badge text-bg-<?= role_badge_class($u['role']) ?>"><?= h(ucfirst($u['role'])) ?></span></td>
            <td><span class="badge text-bg-<?= user_status_badge_class($u['status']) ?>"><?= h(ucfirst($u['status'])) ?></span></td>
            <td><?= (int)$u['uni_count'] ?></td>
            <td><?= (int)$u['prog_count'] ?></td>
            <td class="small text-muted"><?= fmt_date($u['created_at']) ?></td>
            <td class="text-end text-nowrap">
              <?php if ((int)$u['id'] !== $me['id']): ?>
                <a href="impersonate.php?user_id=<?= (int)$u['id'] ?>&t=<?= h(csrf_token()) ?>" class="btn btn-sm btn-outline-primary" title="View as this user"><i class="bi bi-eye"></i></a>
                <form method="post" action="user-toggle-status.php" class="d-inline">
                  <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                  <input type="hidden" name="new_status" value="<?= $u['status'] === 'active' ? 'disabled' : 'active' ?>">
                  <button type="submit" class="btn btn-sm btn-outline-<?= $u['status'] === 'active' ? 'warning' : 'success' ?>" title="<?= $u['status'] === 'active' ? 'Disable account' : 'Activate account' ?>">
                    <i class="bi bi-<?= $u['status'] === 'active' ? 'slash-circle' : 'check-circle' ?>"></i>
                  </button>
                </form>
                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete user"
                  data-confirm-delete
                  data-delete-action="user-delete.php"
                  data-delete-id="<?= (int)$u['id'] ?>"
                  data-delete-text='Delete "<?= h($u['name']) ?>" and ALL of their research data (<?= (int)$u['uni_count'] ?> universities, <?= (int)$u['prog_count'] ?> programs)? This cannot be undone.'>
                  <i class="bi bi-trash3"></i>
                </button>
              <?php else: ?>
                <span class="text-muted small">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="card shadow-sm mt-4">
  <div class="card-header bg-transparent fw-semibold"><i class="bi bi-envelope-check me-2"></i>Email Verification</div>
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <p class="mb-1">
          Require new accounts to confirm their email address before they can log in.
          Currently:
          <span class="badge text-bg-<?= $emailVerificationRequired ? 'success' : 'secondary' ?>"><?= $emailVerificationRequired ? 'Enabled' : 'Disabled' ?></span>
        </p>
        <p class="text-muted small mb-0">
          When enabled, new registrations get a verification email (sent via the SMTP settings in <code>config.php</code>) and can't log in until they click the link.
          Existing accounts are never affected retroactively. Turning this off returns registration to instant access, exactly as before.
        </p>
      </div>
      <form method="post" action="settings-update.php">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <input type="hidden" name="require_email_verification" value="<?= $emailVerificationRequired ? '0' : '1' ?>">
        <button type="submit" class="btn btn-<?= $emailVerificationRequired ? 'outline-secondary' : 'primary' ?>">
          <?= $emailVerificationRequired ? 'Disable' : 'Enable' ?> Email Verification
        </button>
      </form>
    </div>
    <hr>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <p class="mb-1 fw-semibold">Test your mail server</p>
        <p class="text-muted small mb-0">Sends a real test email using the SMTP settings currently in <code>config.php</code>, so you can confirm they work before relying on them.</p>
      </div>
      <form method="post" action="send-test-email.php" class="d-flex gap-2">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <input type="email" name="test_email" class="form-control form-control-sm" placeholder="you@example.com" required value="<?= h($me['email']) ?>" style="min-width:220px;">
        <button type="submit" class="btn btn-outline-primary btn-sm text-nowrap"><i class="bi bi-envelope-paper me-1"></i>Send Test Email</button>
      </form>
    </div>
  </div>
</div>

<p class="text-muted small mt-3">New accounts created via the registration page are always students. To create another admin, promote a user directly in the database: <code>UPDATE users SET role='admin' WHERE email='...';</code></p>

<?php include __DIR__ . '/../includes/footer.php'; ?>
