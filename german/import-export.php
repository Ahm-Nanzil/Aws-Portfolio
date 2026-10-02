<?php
require __DIR__ . '/includes/auth.php';
require_login();
$userId = effective_user_id();
$universities = get_universities($userId);
$programCount = count_all_programs($userId);
$backups = list_import_backups($userId);

$pageTitle = 'Import / Export';
$activeNav = 'importexport';
include __DIR__ . '/includes/header.php';
?>

<h4 class="mb-3">Import / Export / Backup</h4>
<p class="text-muted">This exports and imports <strong><?= is_impersonating() ? h(impersonated_user()['name']) . "'s" : 'your' ?></strong> own research data only — not other users' data. Use this to download a backup, move data to another account, or restore from a previous export.</p>

<div class="row g-3">
  <div class="col-md-6">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-transparent fw-semibold"><i class="bi bi-download me-2"></i>Export / Backup</div>
      <div class="card-body">
        <p>Download everything as a JSON file — this is the format you can re-import later, and it's the safest way to back up your research.</p>
        <a href="actions/export-json.php" class="btn btn-primary mb-2 w-100"><i class="bi bi-filetype-json me-1"></i>Export JSON (full backup)</a>
        <p class="mt-3">Or export a flattened spreadsheet-friendly CSV (one row per program) for use in Excel/Google Sheets. Note: a CSV cannot be re-imported here — use it for viewing/analysis only.</p>
        <a href="actions/export-csv.php" class="btn btn-outline-secondary w-100"><i class="bi bi-filetype-csv me-1"></i>Export CSV</a>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-transparent fw-semibold"><i class="bi bi-upload me-2"></i>Import</div>
      <div class="card-body">
        <form method="post" action="actions/import-preview.php" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
          <div class="mb-3">
            <label class="form-label">JSON file to import</label>
            <input type="file" name="import_file" accept="application/json,.json" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label d-block">Import mode</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="import_mode" id="modeSmartMerge" value="smart_merge" checked>
              <label class="form-check-label" for="modeSmartMerge">
                <strong>Smart Merge (Recommended)</strong> — update existing universities/programs by matching them, add new ones, never create duplicates or delete anything not in the file
              </label>
            </div>
            <div class="form-check mt-1">
              <input class="form-check-input" type="radio" name="import_mode" id="modeReplaceAll" value="replace_all">
              <label class="form-check-label" for="modeReplaceAll">
                <strong class="text-danger">Replace All</strong> — delete all current data and replace it entirely with this file
              </label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary w-100"><i class="bi bi-eye me-1"></i>Preview Import</button>
          <p class="text-muted small mt-2 mb-0">You'll see exactly what will change before anything is saved — nothing is imported on this step.</p>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-header bg-transparent fw-semibold"><i class="bi bi-info-circle me-2"></i>Current data summary</div>
      <div class="card-body">
        <p class="mb-1"><strong><?= count($universities) ?></strong> universities, <strong><?= $programCount ?></strong> programs.</p>
        <p class="mb-0 text-muted small">Data is stored in MySQL, scoped to this account.</p>
      </div>
    </div>
  </div>

  <?php if (!empty($backups)): ?>
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-header bg-transparent fw-semibold"><i class="bi bi-clock-history me-2"></i>Recent Backups</div>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead class="table-light"><tr><th>When</th><th>Reason</th><th>Universities</th><th>Programs</th><th class="text-end">Action</th></tr></thead>
          <tbody>
            <?php foreach ($backups as $b): ?>
              <tr>
                <td><?= h(fmt_date($b['created_at'])) ?></td>
                <td class="text-muted small"><?= $b['reason'] === 'pre_replace_all' ? 'Before Replace All' : ($b['reason'] === 'pre_restore' ? 'Before restoring another backup' : h($b['reason'])) ?></td>
                <td><?= (int)$b['university_count'] ?></td>
                <td><?= (int)$b['program_count'] ?></td>
                <td class="text-end">
                  <button type="button" class="btn btn-sm btn-outline-warning"
                    data-confirm-delete
                    data-delete-action="actions/import-restore-backup.php"
                    data-delete-id="<?= (int)$b['id'] ?>"
                    data-delete-text='Restore this backup from <?= h(fmt_date($b['created_at'])) ?>? Your CURRENT data will be replaced with this snapshot (a backup of your current data will be taken first).'>
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
