<?php
require __DIR__ . '/includes/auth.php';
require_login();
$userId = effective_user_id();

$sort = $_GET['sort'] ?? 'name';
$dir = ($_GET['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
$validSorts = ['name', 'city', 'state', 'type', 'status', 'programs'];
if (!in_array($sort, $validSorts, true)) $sort = 'name';

$universities = get_universities($userId);
usort($universities, function ($a, $b) use ($sort, $dir) {
    $av = $sort === 'programs' ? (int)$a['program_count'] : ($a[$sort] ?? '');
    $bv = $sort === 'programs' ? (int)$b['program_count'] : ($b[$sort] ?? '');
    $cmp = is_numeric($av) && is_numeric($bv) ? ($av <=> $bv) : strcasecmp((string)$av, (string)$bv);
    return $dir === 'desc' ? -$cmp : $cmp;
});

$states = array_values(array_unique(array_filter(array_column($universities, 'state'))));
sort($states);
$cities = array_values(array_unique(array_filter(array_column($universities, 'city'))));
sort($cities);
$statuses = ['Not Started', 'Researching', 'Completed', 'Shortlisted', 'Applied', 'Offer Received', 'Rejected'];

function sortLink(string $col, string $label, string $sort, string $dir): string {
    $newDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
    $icon = '';
    if ($sort === $col) $icon = $dir === 'asc' ? ' <i class="bi bi-caret-up-fill"></i>' : ' <i class="bi bi-caret-down-fill"></i>';
    return '<a class="text-decoration-none text-reset" href="?sort=' . urlencode($col) . '&dir=' . urlencode($newDir) . '">' . h($label) . $icon . '</a>';
}

$pageTitle = 'University Explorer';
$activeNav = 'universities';
include __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <h4 class="mb-0">German Public Universities</h4>
  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUniversityModal"><i class="bi bi-plus-lg me-1"></i>Add University</button>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div class="row g-2" id="uniFilterForm">
      <div class="col-12 col-md-4">
        <input type="text" class="form-control form-control-sm" id="uniSearchInput" placeholder="Search universities…">
      </div>
      <div class="col-6 col-md-2">
        <select class="form-select form-select-sm" data-filter-col="type">
          <option value="">All Types</option>
          <option>Public</option>
          <option>Private</option>
          <option>University of Applied Sciences</option>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <select class="form-select form-select-sm" data-filter-col="state">
          <option value="">All States</option>
          <?php foreach ($states as $s): ?><option><?= h($s) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <select class="form-select form-select-sm" data-filter-col="city">
          <option value="">All Cities</option>
          <?php foreach ($cities as $c): ?><option><?= h($c) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <select class="form-select form-select-sm" data-filter-col="status">
          <option value="">All Statuses</option>
          <?php foreach ($statuses as $s): ?><option><?= h($s) ?></option><?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0" id="uniTable">
      <thead class="table-light">
        <tr>
          <th><?= sortLink('name', 'University', $sort, $dir) ?></th>
          <th><?= sortLink('city', 'City', $sort, $dir) ?></th>
          <th><?= sortLink('state', 'State', $sort, $dir) ?></th>
          <th><?= sortLink('type', 'Type', $sort, $dir) ?></th>
          <th><?= sortLink('programs', 'Programs', $sort, $dir) ?></th>
          <th><?= sortLink('status', 'Status', $sort, $dir) ?></th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($universities)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No universities yet. Click "Add University" to get started.</td></tr>
        <?php endif; ?>
        <?php foreach ($universities as $uni): ?>
          <tr data-col-type="<?= h($uni['type']) ?>" data-col-state="<?= h($uni['state']) ?>" data-col-city="<?= h($uni['city']) ?>" data-col-status="<?= h($uni['status']) ?>">
            <td><a href="university.php?id=<?= urlencode($uni['id']) ?>" class="fw-semibold text-decoration-none">🏛 <?= h($uni['name']) ?></a></td>
            <td><?= h($uni['city']) ?></td>
            <td><?= h($uni['state']) ?></td>
            <td><span class="badge text-bg-light border"><?= h($uni['type']) ?></span></td>
            <td><?= (int)$uni['program_count'] ?></td>
            <td>
              <form method="post" action="actions/university-status-update.php" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= h($uni['id']) ?>">
                <input type="hidden" name="redirect_to" value="<?= h($_SERVER['REQUEST_URI']) ?>">
                <select name="status" class="form-select form-select-sm badge-select bg-<?= status_badge_class($uni['status']) ?>-subtle border-0" onchange="this.form.submit()" style="min-width:140px;">
                  <?php foreach ($statuses as $s): ?>
                    <option value="<?= h($s) ?>" <?= $s === $uni['status'] ? 'selected' : '' ?>><?= h($s) ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
            </td>
            <td class="text-end">
              <a href="university.php?id=<?= urlencode($uni['id']) ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
              <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                data-confirm-delete
                data-delete-action="actions/university-delete.php"
                data-delete-id="<?= h($uni['id']) ?>"
                data-delete-text='Delete "<?= h($uni['name']) ?>" and all <?= (int)$uni['program_count'] ?> of its programs? This cannot be undone.'>
                <i class="bi bi-trash3"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  initTableFilter('#uniTable', '#uniFilterForm', '#uniSearchInput');
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
