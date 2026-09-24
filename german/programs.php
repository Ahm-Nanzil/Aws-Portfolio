<?php
require __DIR__ . '/includes/auth.php';
require_login();
$userId = effective_user_id();

$rows = all_programs_flat($userId);
$universityCount = count(get_universities($userId));

$universities = array_values(array_unique(array_map(fn($r) => $r['university']['name'], $rows)));
sort($universities);
$states = array_values(array_unique(array_filter(array_map(fn($r) => $r['university']['state'], $rows))));
sort($states);

$pageTitle = 'Program Explorer';
$activeNav = 'programs';
include __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <h4 class="mb-0">All Programs</h4>
  <span class="text-muted small"><?= count($rows) ?> program<?= count($rows) === 1 ? '' : 's' ?> across <?= $universityCount ?> universit<?= $universityCount === 1 ? 'y' : 'ies' ?></span>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div class="row g-2" id="progFilterForm">
      <div class="col-12 col-md-3">
        <input type="text" class="form-control form-control-sm" id="progSearchInput" placeholder="Search programs…">
      </div>
      <div class="col-6 col-md-2">
        <select class="form-select form-select-sm" data-filter-col="university">
          <option value="">All Universities</option>
          <?php foreach ($universities as $u): ?><option><?= h($u) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <select class="form-select form-select-sm" data-filter-col="state">
          <option value="">All States</option>
          <?php foreach ($states as $s): ?><option><?= h($s) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <select class="form-select form-select-sm" data-filter-col="english">
          <option value="">English (any)</option>
          <option value="Yes">English-taught: Yes</option>
          <option value="No">English-taught: No</option>
          <option value="Unknown">English-taught: Unknown</option>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <select class="form-select form-select-sm" data-filter-col="moi">
          <option value="">MOI (any)</option>
          <option value="Yes">MOI Accepted</option>
          <option value="No">MOI Not Accepted</option>
          <option value="Conditional">MOI Conditional</option>
          <option value="Unknown">MOI Unknown</option>
        </select>
      </div>
      <div class="col-6 col-md-1">
        <select class="form-select form-select-sm" data-filter-col="intake">
          <option value="">Intake</option>
          <option>Winter</option><option>Summer</option><option>Both</option>
        </select>
      </div>
    </div>
    <div class="row g-2 mt-1">
      <div class="col-6 col-md-2">
        <select class="form-select form-select-sm" data-filter-col="method">
          <option value="">Method (any)</option>
          <option>Direct</option><option>Uni-Assist</option><option>Other</option>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <select class="form-select form-select-sm" data-filter-col="ielts">
          <option value="">IELTS (any)</option>
          <option value="Yes">Required</option><option value="No">Not required</option><option value="Unknown">Unknown</option>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <select class="form-select form-select-sm" data-filter-col="eligibility">
          <option value="">Eligibility (any)</option>
          <option>Eligible</option><option>Maybe</option><option>Not Eligible</option><option>Unknown</option>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <select class="form-select form-select-sm" data-filter-col="priority">
          <option value="">Priority (any)</option>
          <option>High</option><option>Medium</option><option>Low</option>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <select class="form-select form-select-sm" data-filter-col="appstatus">
          <option value="">App. Status (any)</option>
          <?php foreach (['Not Started', 'Researching', 'Ready to Apply', 'Applied', 'Offer Received', 'Rejected'] as $s): ?><option><?= h($s) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <select class="form-select form-select-sm" data-filter-col="subject">
          <option value="">Subject (any)</option>
          <?php $subjects = array_values(array_unique(array_filter(array_map(fn($r) => $r['program']['subject'], $rows)))); sort($subjects); ?>
          <?php foreach ($subjects as $s): ?><option><?= h($s) ?></option><?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0" id="progTable">
      <thead class="table-light">
        <tr>
          <th>Program</th>
          <th>University</th>
          <th>Language</th>
          <th>Tuition</th>
          <th>MOI</th>
          <th>Intake</th>
          <th>Eligibility</th>
          <th>Priority</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="10" class="text-center text-muted py-4">No programs yet. Add one from a university's page.</td></tr>
        <?php endif; ?>
        <?php foreach ($rows as $r): $p = $r['program']; $u = $r['university']; ?>
          <tr
            data-col-university="<?= h($u['name']) ?>"
            data-col-state="<?= h($u['state']) ?>"
            data-col-english="<?= h($p['language']['englishTaught']) ?>"
            data-col-moi="<?= h($p['language']['moiAccepted']) ?>"
            data-col-intake="<?= h($p['intake']) ?>"
            data-col-method="<?= h($p['application']['method']) ?>"
            data-col-ielts="<?= h($p['language']['ieltsRequired']) ?>"
            data-col-eligibility="<?= h($p['personal']['eligibility']) ?>"
            data-col-priority="<?= h($p['personal']['priority']) ?>"
            data-col-appstatus="<?= h($p['personal']['applicationStatus']) ?>"
            data-col-subject="<?= h($p['subject']) ?>"
          >
            <td><a href="program.php?id=<?= urlencode($p['id']) ?>&university_id=<?= urlencode($u['id']) ?>" class="fw-semibold text-decoration-none">💻 <?= h($p['name']) ?></a></td>
            <td><a href="university.php?id=<?= urlencode($u['id']) ?>" class="subtle-link">🏛 <?= h($u['name']) ?></a></td>
            <td><?= h($p['language']['teachingLanguage'] ?: '—') ?><?php if ($p['language']['englishTaught'] === 'Yes'): ?> <span class="badge text-bg-info">EN</span><?php endif; ?></td>
            <td><?= h($p['fees']['tuitionFee'] ?: '—') ?></td>
            <td>
              <?php $moi = $p['language']['moiAccepted']; $moiClass = ['Yes' => 'success', 'No' => 'danger', 'Conditional' => 'warning', 'Unknown' => 'secondary'][$moi] ?? 'secondary'; ?>
              <span class="badge text-bg-<?= $moiClass ?>"><?= h($moi) ?></span>
            </td>
            <td><?= h($p['intake']) ?></td>
            <td><span class="badge text-bg-<?= eligibility_badge_class($p['personal']['eligibility']) ?>"><?= h($p['personal']['eligibility']) ?></span></td>
            <td><span class="badge text-bg-<?= priority_badge_class($p['personal']['priority']) ?>"><?= h($p['personal']['priority']) ?></span></td>
            <td><span class="badge text-bg-<?= status_badge_class($p['personal']['applicationStatus']) ?>"><?= h($p['personal']['applicationStatus']) ?></span></td>
            <td class="text-end">
              <a href="program.php?id=<?= urlencode($p['id']) ?>&university_id=<?= urlencode($u['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Open"><i class="bi bi-pencil-square"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  initTableFilter('#progTable', '#progFilterForm', '#progSearchInput');
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
