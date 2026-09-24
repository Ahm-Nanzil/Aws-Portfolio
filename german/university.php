<?php
require __DIR__ . '/includes/auth.php';
require_login();
$userId = effective_user_id();

$id = (int)($_GET['id'] ?? 0);
$uni = get_university($userId, $id);
if ($uni === null) {
    flash('danger', 'University not found.');
    redirect(base_path() . '/universities.php');
}
$programs = get_programs_for_university($userId, $id);

$tab = $_GET['tab'] ?? 'overview';
if (!in_array($tab, ['overview', 'programs', 'notes'], true)) $tab = 'overview';

$statuses = ['Not Started', 'Researching', 'Completed', 'Shortlisted', 'Applied', 'Offer Received', 'Rejected'];
$types = ['Public', 'Private', 'University of Applied Sciences'];

$pageTitle = $uni['name'];
$activeNav = 'university';
include __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb small">
    <li class="breadcrumb-item"><a href="universities.php">University Explorer</a></li>
    <li class="breadcrumb-item active"><?= h($uni['name']) ?></li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
  <div>
    <h4 class="mb-1">🏛 <?= h($uni['name']) ?></h4>
    <div class="text-muted small"><?= h($uni['city']) ?><?= $uni['city'] && $uni['state'] ? ', ' : '' ?><?= h($uni['state']) ?> · <span class="badge text-bg-light border"><?= h($uni['type']) ?></span></div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <span class="badge text-bg-<?= status_badge_class($uni['status']) ?> fs-6"><?= h($uni['status']) ?></span>
    <button type="button" class="btn btn-sm btn-outline-danger" data-confirm-delete
      data-delete-action="actions/university-delete.php"
      data-delete-id="<?= h($uni['id']) ?>"
      data-delete-text='Delete "<?= h($uni['name']) ?>" and all <?= count($programs) ?> of its programs? This cannot be undone.'>
      <i class="bi bi-trash3 me-1"></i>Delete University
    </button>
  </div>
</div>

<ul class="nav nav-tabs mb-3">
  <li class="nav-item"><a class="nav-link <?= $tab === 'overview' ? 'active' : '' ?>" href="?id=<?= urlencode($id) ?>&tab=overview"><i class="bi bi-info-circle me-1"></i>Overview</a></li>
  <li class="nav-item"><a class="nav-link <?= $tab === 'programs' ? 'active' : '' ?>" href="?id=<?= urlencode($id) ?>&tab=programs"><i class="bi bi-mortarboard me-1"></i>Programs (<?= count($programs) ?>)</a></li>
  <li class="nav-item"><a class="nav-link <?= $tab === 'notes' ? 'active' : '' ?>" href="?id=<?= urlencode($id) ?>&tab=notes"><i class="bi bi-journal-text me-1"></i>Notes</a></li>
</ul>

<?php if ($tab === 'overview'): ?>
  <div class="card shadow-sm">
    <div class="card-body">
      <form method="post" action="actions/university-save.php">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= h($uni['id']) ?>">
        <input type="hidden" name="generalNotes" value="<?= h($uni['general_notes']) ?>">
        <input type="hidden" name="redirect_to" value="university.php?id=<?= urlencode($id) ?>&tab=overview">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">University Name *</label>
            <input type="text" name="name" class="form-control" value="<?= h($uni['name']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Official Name</label>
            <input type="text" name="officialName" class="form-control" value="<?= h($uni['official_name']) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" value="<?= h($uni['city']) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">State</label>
            <input type="text" name="state" class="form-control" value="<?= h($uni['state']) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">University Type</label>
            <select name="type" class="form-select">
              <?php foreach ($types as $t): ?><option <?= $t === $uni['type'] ? 'selected' : '' ?>><?= h($t) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Official Website</label>
            <input type="url" name="website" class="form-control" value="<?= h($uni['website']) ?>" placeholder="https://">
          </div>
          <div class="col-md-6">
            <label class="form-label">International Student Website</label>
            <input type="url" name="intlWebsite" class="form-control" value="<?= h($uni['intl_website']) ?>" placeholder="https://">
          </div>
          <div class="col-md-6">
            <label class="form-label">Application Portal</label>
            <input type="url" name="applicationPortal" class="form-control" value="<?= h($uni['application_portal']) ?>" placeholder="https://">
          </div>
          <div class="col-md-6">
            <label class="form-label">Application Method</label>
            <select name="applicationMethod" class="form-select">
              <?php foreach (['Direct', 'Uni-Assist', 'Other'] as $m): ?><option <?= $m === $uni['application_method'] ? 'selected' : '' ?>><?= h($m) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Application Fee</label>
            <input type="text" name="applicationFee" class="form-control" value="<?= h($uni['application_fee']) ?>" placeholder="e.g. €0 or Unknown">
          </div>
          <div class="col-md-4">
            <label class="form-label">Tuition Fee</label>
            <input type="text" name="tuitionFee" class="form-control" value="<?= h($uni['tuition_fee']) ?>" placeholder="e.g. €0 or Unknown">
          </div>
          <div class="col-md-4">
            <label class="form-label">Semester Contribution</label>
            <input type="text" name="semesterContribution" class="form-control" value="<?= h($uni['semester_contribution']) ?>" placeholder="e.g. €250">
          </div>
          <div class="col-md-6">
            <label class="form-label">Research Status</label>
            <select name="status" class="form-select">
              <?php foreach ($statuses as $s): ?><option <?= $s === $uni['status'] ? 'selected' : '' ?>><?= h($s) ?></option><?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
        </div>
      </form>
    </div>
  </div>

<?php elseif ($tab === 'programs'): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted small"><?= count($programs) ?> program<?= count($programs) === 1 ? '' : 's' ?></div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProgramModal"><i class="bi bi-plus-lg me-1"></i>Add Program</button>
  </div>

  <div class="row g-3">
    <?php if (empty($programs)): ?>
      <div class="col-12">
        <div class="card shadow-sm"><div class="card-body text-center text-muted py-5">
          No programs added yet. Click "Add Program" to create the first one.
        </div></div>
      </div>
    <?php endif; ?>
    <?php foreach ($programs as $prog): ?>
      <div class="col-md-6 col-xl-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h6 class="mb-0"><a href="program.php?id=<?= urlencode($prog['id']) ?>&university_id=<?= urlencode($id) ?>" class="text-decoration-none">💻 <?= h($prog['name']) ?></a></h6>
              <span class="badge text-bg-<?= priority_badge_class($prog['personal']['priority']) ?>"><?= h($prog['personal']['priority']) ?></span>
            </div>
            <div class="small text-muted mb-2"><?= h($prog['degree']) ?><?= $prog['subject'] ? ' · ' . h($prog['subject']) : '' ?></div>
            <div class="chip-list mb-3">
              <span class="badge text-bg-<?= status_badge_class($prog['personal']['applicationStatus']) ?>"><?= h($prog['personal']['applicationStatus']) ?></span>
              <span class="badge text-bg-<?= eligibility_badge_class($prog['personal']['eligibility']) ?>"><?= h($prog['personal']['eligibility']) ?></span>
              <?php if ($prog['language']['englishTaught'] === 'Yes'): ?><span class="badge text-bg-info">English-taught</span><?php endif; ?>
              <?php if ($prog['language']['moiAccepted'] === 'Yes'): ?><span class="badge text-bg-secondary">MOI OK</span><?php endif; ?>
            </div>
            <div class="mt-auto d-flex gap-2">
              <a href="program.php?id=<?= urlencode($prog['id']) ?>&university_id=<?= urlencode($id) ?>" class="btn btn-sm btn-outline-primary flex-grow-1"><i class="bi bi-pencil-square me-1"></i>Open</a>
              <form method="post" action="actions/program-duplicate.php" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= h($prog['id']) ?>">
                <input type="hidden" name="university_id" value="<?= h($id) ?>">
                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Duplicate"><i class="bi bi-files"></i></button>
              </form>
              <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                data-confirm-delete
                data-delete-action="actions/program-delete.php"
                data-delete-id="<?= h($prog['id']) ?>"
                data-delete-uni-id="<?= h($id) ?>"
                data-delete-text='Delete program "<?= h($prog['name']) ?>"? This cannot be undone.'>
                <i class="bi bi-trash3"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Add Program modal -->
  <div class="modal fade" id="addProgramModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" action="actions/program-save.php">
          <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-mortarboard me-2"></i>Add Program</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="university_id" value="<?= h($id) ?>">
            <input type="hidden" name="section" value="overview">
            <div class="mb-2">
              <label class="form-label">Program Name *</label>
              <input type="text" name="name" class="form-control" required autofocus placeholder="e.g. M.Sc. Computer Science">
            </div>
            <div class="row g-2">
              <div class="col-6">
                <label class="form-label">Degree</label>
                <select name="degree" class="form-select">
                  <option>M.Sc.</option><option>M.A.</option><option>M.Eng.</option><option>MBA</option><option>Other</option>
                </select>
              </div>
              <div class="col-6">
                <label class="form-label">Intake</label>
                <select name="intake" class="form-select">
                  <option>Winter</option><option>Summer</option><option>Both</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add Program</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php elseif ($tab === 'notes'): ?>
  <div class="card shadow-sm">
    <div class="card-body">
      <form method="post" action="actions/university-save.php">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= h($uni['id']) ?>">
        <input type="hidden" name="name" value="<?= h($uni['name']) ?>">
        <input type="hidden" name="officialName" value="<?= h($uni['official_name']) ?>">
        <input type="hidden" name="city" value="<?= h($uni['city']) ?>">
        <input type="hidden" name="state" value="<?= h($uni['state']) ?>">
        <input type="hidden" name="type" value="<?= h($uni['type']) ?>">
        <input type="hidden" name="website" value="<?= h($uni['website']) ?>">
        <input type="hidden" name="intlWebsite" value="<?= h($uni['intl_website']) ?>">
        <input type="hidden" name="applicationPortal" value="<?= h($uni['application_portal']) ?>">
        <input type="hidden" name="applicationMethod" value="<?= h($uni['application_method']) ?>">
        <input type="hidden" name="applicationFee" value="<?= h($uni['application_fee']) ?>">
        <input type="hidden" name="tuitionFee" value="<?= h($uni['tuition_fee']) ?>">
        <input type="hidden" name="semesterContribution" value="<?= h($uni['semester_contribution']) ?>">
        <input type="hidden" name="status" value="<?= h($uni['status']) ?>">
        <input type="hidden" name="redirect_to" value="university.php?id=<?= urlencode($id) ?>&tab=notes">
        <label class="form-label">General Notes</label>
        <textarea name="generalNotes" rows="10" class="form-control" placeholder="General notes about this university…"><?= h($uni['general_notes']) ?></textarea>
        <div class="mt-3">
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Notes</button>
        </div>
      </form>
    </div>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
