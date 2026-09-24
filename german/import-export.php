<?php
require __DIR__ . '/includes/auth.php';
require_login();
$userId = effective_user_id();
$universities = get_universities($userId);
$programCount = count_all_programs($userId);

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
      <div class="card-header bg-transparent fw-semibold"><i class="bi bi-upload me-2"></i>Import / Restore</div>
      <div class="card-body">
        <form method="post" action="actions/import-json.php" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
          <div class="mb-3">
            <label class="form-label">JSON file to import</label>
            <input type="file" name="import_file" accept="application/json,.json" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label d-block">Import mode</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="import_mode" id="modeMerge" value="merge" checked>
              <label class="form-check-label" for="modeMerge">Merge — add these universities alongside my current data</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="import_mode" id="modeReplace" value="replace">
              <label class="form-check-label" for="modeReplace">Replace — <strong class="text-danger">overwrite</strong> all current data with this file</label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary w-100" onclick="return confirmImport();"><i class="bi bi-upload me-1"></i>Import</button>
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
</div>

<script>
function confirmImport() {
  const replaceMode = document.getElementById('modeReplace').checked;
  if (replaceMode) {
    return confirm('This will REPLACE all current data with the imported file. This cannot be undone. Continue?');
  }
  return true;
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
