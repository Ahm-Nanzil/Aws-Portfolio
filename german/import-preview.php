<?php
require __DIR__ . '/includes/auth.php';
require_login();
$userId = effective_user_id();

$token = trim($_GET['token'] ?? '');
$session = $token !== '' ? get_import_session($token, $userId) : null;

if ($session === null) {
    flash('danger', 'This import preview has expired or was not found. Please upload the file again.');
    redirect(base_path() . '/import-export.php');
}

$mode = $session['mode'];
$plan = $session['plan'];
$payload = $session['payload'];

// For Replace All, work out what's currently there so the warning is concrete, not vague.
if ($mode === 'replace_all') {
    $currentUnis = get_universities($userId);
    $currentProgramCount = count_all_programs($userId);
    $incomingUniCount = count($payload['universities'] ?? []);
    $incomingProgramCount = 0;
    foreach ($payload['universities'] ?? [] as $u) $incomingProgramCount += count($u['programs'] ?? []);
}

function fieldLabel(string $key): string {
    $labels = [
        'name' => 'Name', 'officialName' => 'Official name', 'city' => 'City', 'state' => 'State',
        'type' => 'Type', 'website' => 'Website', 'intlWebsite' => "Int'l website", 'applicationPortal' => 'Application portal',
        'applicationMethod' => 'Application method', 'applicationFee' => 'Application fee', 'tuitionFee' => 'Tuition fee',
        'semesterContribution' => 'Semester contribution', 'generalNotes' => 'General notes', 'status' => 'Status',
        'degree' => 'Degree', 'subject' => 'Subject', 'department' => 'Department', 'faculty' => 'Faculty',
        'description' => 'Description', 'studyLocation' => 'Study location', 'duration' => 'Duration',
        'ects' => 'ECTS', 'studyMode' => 'Study mode', 'intake' => 'Intake',
    ];
    return $labels[$key] ?? $key;
}

function actionBadge(string $action): string {
    $map = [
        'new' => ['success', 'New'], 'update' => ['warning', 'Update'],
        'unchanged' => ['secondary', 'Unchanged'], 'ambiguous' => ['danger', 'Needs review'],
    ];
    [$color, $label] = $map[$action] ?? ['secondary', ucfirst($action)];
    return '<span class="badge text-bg-' . $color . '">' . h($label) . '</span>';
}

$pageTitle = 'Import Preview';
$activeNav = 'importexport';
include __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <h4 class="mb-0"><i class="bi bi-eye me-2"></i>Import Preview</h4>
  <span class="text-muted small">File: <?= h($session['filename']) ?></span>
</div>

<?php if ($mode === 'replace_all'): ?>

  <div class="alert alert-danger">
    <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>This will replace ALL of your data</h5>
    <p class="mb-2">You are about to <strong>permanently delete</strong> your current data and replace it with the contents of this file:</p>
    <table class="table table-sm mb-2" style="max-width:420px;">
      <tr><th></th><th>Currently have</th><th>Will become</th></tr>
      <tr><td>Universities</td><td><?= count($currentUnis) ?></td><td><?= $incomingUniCount ?></td></tr>
      <tr><td>Programs</td><td><?= $currentProgramCount ?></td><td><?= $incomingProgramCount ?></td></tr>
    </table>
    <p class="mb-0">A backup of your <strong>current</strong> data will be saved automatically before anything is deleted, and can be restored afterward from this page if needed.</p>
  </div>

  <form method="post" action="actions/import-commit.php" class="card shadow-sm">
    <div class="card-body">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <input type="hidden" name="token" value="<?= h($token) ?>">
      <input type="hidden" name="mode" value="replace_all">
      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="confirm_replace" id="confirmReplace" required>
        <label class="form-check-label" for="confirmReplace">
          I understand this will permanently delete my <?= count($currentUnis) ?> existing universit<?= count($currentUnis) === 1 ? 'y' : 'ies' ?> and replace them with this file's contents.
        </label>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1"></i>Yes, Replace All My Data</button>
        <a href="import-export.php" class="btn btn-outline-secondary" onclick="return cancelSession();">Cancel</a>
      </div>
    </div>
  </form>

<?php else: ?>

  <?php $s = $plan['stats']; ?>
  <div class="row g-3 mb-4 row-cols-2 row-cols-md-4">
    <div class="col"><div class="stat-card"><div class="stat-value text-success"><?= $s['new_universities'] + $s['new_programs'] ?></div><div class="stat-label">New records</div></div></div>
    <div class="col"><div class="stat-card"><div class="stat-value text-warning"><?= $s['updated_universities'] + $s['updated_programs'] ?></div><div class="stat-label">Will be updated</div></div></div>
    <div class="col"><div class="stat-card"><div class="stat-value text-secondary"><?= $s['unchanged_universities'] + $s['unchanged_programs'] ?></div><div class="stat-label">Already up to date</div></div></div>
    <div class="col"><div class="stat-card"><div class="stat-value text-danger"><?= $s['ambiguous_universities'] + $s['ambiguous_programs'] ?></div><div class="stat-label">Need your review</div></div></div>
  </div>

  <?php if ($plan['untouchedExistingCount'] > 0): ?>
    <div class="alert alert-info small">
      <i class="bi bi-info-circle me-1"></i>
      <?= $plan['untouchedExistingCount'] ?> of your existing universit<?= $plan['untouchedExistingCount'] === 1 ? 'y' : 'ies' ?> not mentioned in this file will be left exactly as-is.
    </div>
  <?php endif; ?>

  <?php if (($s['ambiguous_universities'] + $s['ambiguous_programs']) > 0): ?>
    <div class="alert alert-warning small">
      <i class="bi bi-exclamation-triangle me-1"></i>
      Some entries matched more than one existing record by name and couldn't be resolved automatically. Choose what to do with each one marked "Needs review" below before confirming.
    </div>
  <?php endif; ?>

  <form method="post" action="actions/import-commit.php" id="importForm">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="token" value="<?= h($token) ?>">
    <input type="hidden" name="mode" value="smart_merge">

    <?php foreach ($plan['universities'] as $i => $u): ?>
      <div class="card shadow-sm mb-3">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div>🏛 <strong><?= h($u['name']) ?></strong> <span class="text-muted small"><?= h($u['city']) ?><?= $u['city'] && $u['state'] ? ', ' : '' ?><?= h($u['state']) ?></span></div>
          <?= actionBadge($u['action']) ?>
        </div>
        <div class="card-body">

          <?php if ($u['action'] === 'ambiguous'): ?>
            <p class="small text-muted mb-2">This name matches more than one existing university. Choose what to do:</p>
            <select name="resolutions[u<?= $i ?>]" class="form-select form-select-sm mb-2" style="max-width:420px;">
              <option value="new">Create as a new, separate university</option>
              <?php foreach ($u['candidates'] as $c): ?>
                <option value="<?= (int)$c['id'] ?>">Merge into existing: <?= h($c['name']) ?> (<?= h($c['city']) ?><?= $c['city'] && $c['state'] ? ', ' : '' ?><?= h($c['state']) ?>)</option>
              <?php endforeach; ?>
              <option value="skip">Skip this university entirely</option>
            </select>
          <?php elseif ($u['action'] === 'update' && !empty($u['field_changes'])): ?>
            <table class="table table-sm mb-0">
              <thead><tr><th style="width:180px;">Field</th><th>Current value</th><th>New value</th></tr></thead>
              <tbody>
                <?php foreach ($u['field_changes'] as $key => $ch): ?>
                  <tr>
                    <td><?= h(fieldLabel($key)) ?></td>
                    <td class="text-muted"><?= h($ch['from'] !== '' ? $ch['from'] : '(empty)') ?></td>
                    <td><?= h($ch['to']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php elseif ($u['action'] === 'new'): ?>
            <p class="text-muted small mb-0">Will be added as a new university.</p>
          <?php else: ?>
            <p class="text-muted small mb-0">Matches your existing record exactly — nothing to change.</p>
          <?php endif; ?>

          <?php if (!empty($u['programs'])): ?>
            <hr>
            <div class="small fw-semibold mb-2">Programs (<?= count($u['programs']) ?>)</div>
            <?php foreach ($u['programs'] as $j => $p): ?>
              <div class="d-flex justify-content-between align-items-start border-top pt-2 pb-2 flex-wrap gap-2">
                <div>
                  💻 <?= h($p['name']) ?> <span class="text-muted small"><?= h($p['degree']) ?></span>
                  <?php if ($p['action'] === 'update' && !empty($p['field_changes'])): ?>
                    <div class="small text-muted">
                      <?php foreach ($p['field_changes'] as $key => $ch): ?>
                        <?= h(fieldLabel($key)) ?>: "<?= h($ch['from']) ?>" → "<?= h($ch['to']) ?>"<br>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="text-end">
                  <?= actionBadge($p['action']) ?>
                  <?php if ($p['action'] === 'ambiguous'): ?>
                    <select name="resolutions[u<?= $i ?>_p<?= $j ?>]" class="form-select form-select-sm mt-1" style="min-width:260px;">
                      <option value="new">Create as new program</option>
                      <?php foreach ($p['candidates'] as $c): ?>
                        <option value="<?= (int)$c['id'] ?>">Merge into: <?= h($c['name']) ?> (<?= h($c['degree']) ?>)</option>
                      <?php endforeach; ?>
                      <option value="skip">Skip this program</option>
                    </select>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div>
      </div>
    <?php endforeach; ?>

    <?php if (empty($plan['universities'])): ?>
      <div class="alert alert-info">This file doesn't contain any universities to import.</div>
    <?php endif; ?>

    <div class="d-flex gap-2 mb-4">
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Confirm Import</button>
      <a href="import-export.php" class="btn btn-outline-secondary" onclick="return cancelSession();">Cancel</a>
    </div>
  </form>

<?php endif; ?>

<form method="post" action="actions/import-cancel.php" id="cancelForm" class="d-none">
  <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
  <input type="hidden" name="token" value="<?= h($token) ?>">
</form>
<script>
function cancelSession() {
  document.getElementById('cancelForm').submit();
  return false;
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
