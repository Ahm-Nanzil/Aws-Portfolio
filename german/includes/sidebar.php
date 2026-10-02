<?php
// Sidebar is self-contained: it loads its own copy of the current
// (effective) user's data so any page can include header.php without
// worrying about this dependency.
// NOTE: every variable here is prefixed "sb" because this file is included
// into the page's own variable scope; plain names like $uni/$prog/$uniId
// would overwrite the page's variables.
$sidebarUserId = effective_user_id();
$sidebarUniversities = get_universities($sidebarUserId);
$sidebarProgramsGrouped = get_programs_light_grouped($sidebarUserId);
$sbCurrentUniId = (int)($_GET['university_id'] ?? ($activeNav === 'university' ? ($_GET['id'] ?? 0) : 0));
$sbCurrentProgId = ($activeNav ?? '') === 'program' ? (int)($_GET['id'] ?? 0) : 0;
?>
<aside class="app-sidebar" id="appSidebar">
  <div class="sidebar-scroll">
    <div class="sidebar-section-title">🇩🇪 German Universities</div>
    <nav class="nav flex-column mb-2 top-nav">
      <a class="nav-link <?= $activeNav === 'dashboard' ? 'active' : '' ?>" href="<?= h(base_path()) ?>/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
      <a class="nav-link <?= $activeNav === 'universities' ? 'active' : '' ?>" href="<?= h(base_path()) ?>/universities.php"><i class="bi bi-bank me-2"></i>University Explorer</a>
      <a class="nav-link <?= $activeNav === 'programs' ? 'active' : '' ?>" href="<?= h(base_path()) ?>/programs.php"><i class="bi bi-mortarboard me-2"></i>Program Explorer</a>
      <a class="nav-link <?= $activeNav === 'search' ? 'active' : '' ?>" href="<?= h(base_path()) ?>/search.php"><i class="bi bi-search me-2"></i>Search</a>
      <a class="nav-link <?= $activeNav === 'importexport' ? 'active' : '' ?>" href="<?= h(base_path()) ?>/import-export.php"><i class="bi bi-arrow-down-up me-2"></i>Import / Export</a>
      <?php if (is_admin()): ?>
        <a class="nav-link <?= $activeNav === 'admin' ? 'active' : '' ?>" href="<?= h(base_path()) ?>/admin/index.php"><i class="bi bi-shield-lock me-2"></i>Admin Panel</a>
      <?php endif; ?>
    </nav>
    <hr class="sidebar-divider">
    <div class="d-flex justify-content-between align-items-center px-2 mb-1">
      <div class="sidebar-section-title mb-0">Tree View</div>
      <button class="btn btn-sm btn-outline-primary py-0 px-2" type="button" data-bs-toggle="modal" data-bs-target="#addUniversityModal" title="Add university">
        <i class="bi bi-plus-lg"></i>
      </button>
    </div>
    <div class="tree-root">
      <?php if (empty($sidebarUniversities)): ?>
        <div class="text-muted small px-3 py-2">No universities yet. Click + to add one.</div>
      <?php endif; ?>
      <?php foreach ($sidebarUniversities as $sbUni): $sbUniId = (int)$sbUni['id']; ?>
        <?php $sbIsOpenUni = ($sbUniId === $sbCurrentUniId); ?>
        <div class="tree-uni">
          <div class="tree-uni-row <?= $sbIsOpenUni ? 'tree-open' : '' ?>">
            <button class="tree-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#tree-<?= $sbUniId ?>" aria-expanded="<?= $sbIsOpenUni ? 'true' : 'false' ?>">
              <i class="bi bi-caret-right-fill tree-caret"></i>
            </button>
            <a href="<?= h(base_path()) ?>/university.php?id=<?= $sbUniId ?>" class="tree-uni-link <?= $sbIsOpenUni && empty($sbCurrentProgId) && $activeNav === 'university' ? 'fw-semibold text-primary' : '' ?>">
              🏛 <?= h($sbUni['name'] ?: '(Unnamed university)') ?>
            </a>
            <span class="badge rounded-pill text-bg-<?= status_badge_class($sbUni['status']) ?> tree-status-badge"><?= h($sbUni['status']) ?></span>
          </div>
          <div class="collapse <?= $sbIsOpenUni ? 'show' : '' ?>" id="tree-<?= $sbUniId ?>">
            <div class="tree-children">
              <a href="<?= h(base_path()) ?>/university.php?id=<?= $sbUniId ?>&tab=overview" class="tree-item">📋 University Information</a>
              <div class="tree-item tree-programs-label">🎓 Programs (<?= (int)$sbUni['program_count'] ?>)</div>
              <?php foreach (($sidebarProgramsGrouped[$sbUniId] ?? []) as $sbProg): ?>
                <a href="<?= h(base_path()) ?>/program.php?id=<?= (int)$sbProg['id'] ?>&university_id=<?= $sbUniId ?>"
                   class="tree-item tree-program <?= (int)$sbProg['id'] === $sbCurrentProgId ? 'fw-semibold text-primary' : '' ?>">
                  💻 <?= h($sbProg['name'] ?: '(Unnamed program)') ?>
                </a>
              <?php endforeach; ?>
              <a href="<?= h(base_path()) ?>/university.php?id=<?= $sbUniId ?>&tab=notes" class="tree-item">📝 Notes</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- Quick-add university modal, available from every page -->
<div class="modal fade" id="addUniversityModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="<?= h(base_path()) ?>/actions/university-save.php" id="addUniversityForm">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-bank me-2"></i>Add University</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
          <input type="hidden" name="action" value="create">
          <input type="hidden" name="submit_token" value="<?= h(issue_submit_token('university_create')) ?>">
          <input type="hidden" name="redirect_to" value="<?= h($_SERVER['REQUEST_URI'] ?? 'dashboard.php') ?>">
          <div class="mb-2">
            <label class="form-label">University Name *</label>
            <input type="text" name="name" class="form-control" required autofocus placeholder="e.g. University of Passau">
          </div>
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label">City</label>
              <input type="text" name="city" class="form-control">
            </div>
            <div class="col-6">
              <label class="form-label">State</label>
              <input type="text" name="state" class="form-control">
            </div>
          </div>
          <div class="mb-2 mt-2">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
              <option>Public</option>
              <option>Private</option>
              <option>University of Applied Sciences</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add University</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Prevent double-submits of the quick-add form (double-click / Enter + click).
(function () {
  var f = document.getElementById('addUniversityForm');
  if (!f) return;
  f.addEventListener('submit', function (e) {
    if (f.dataset.submitted === '1') { e.preventDefault(); return; }
    f.dataset.submitted = '1';
    var b = f.querySelector('button[type="submit"]');
    if (b) b.disabled = true;
  });
  // Restore the button if the page is shown again from the back/forward cache.
  window.addEventListener('pageshow', function (ev) {
    if (ev.persisted) {
      f.dataset.submitted = '0';
      var b = f.querySelector('button[type="submit"]');
      if (b) b.disabled = false;
    }
  });
})();
</script>