<?php
require __DIR__ . '/../includes/auth.php';
require_admin();

$stats = db_global_stats();
$users = db_all_users_with_stats();
$me = current_user();

$pageTitle = 'Admin Panel';
$activeNav = 'admin';
include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <h4 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Admin Panel</h4>
  <a href="dashboard.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-speedometer2 me-1"></i>My Own Dashboard</a>
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
                <a href="impersonate.php?user_id=<?= (int)$u['id'] ?>" class="btn btn-sm btn-outline-primary" title="View as this user"><i class="bi bi-eye"></i></a>
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

<p class="text-muted small mt-3">New accounts created via the registration page are always students. To create another admin, promote a user directly in the database: <code>UPDATE users SET role='admin' WHERE email='...';</code></p>

<?php include __DIR__ . '/../includes/footer.php'; ?>
