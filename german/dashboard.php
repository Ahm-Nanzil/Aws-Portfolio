<?php
require __DIR__ . '/includes/auth.php';
require_login();

$userId = effective_user_id();
$universities = get_universities($userId);
$allPrograms = all_programs_flat($userId);

// ---- Stats ----
$totalUniversities = count($universities);
$totalPrograms = count($allPrograms);
$publicUniversities = count(array_filter($universities, fn($u) => $u['type'] === 'Public'));
$englishPrograms = count(array_filter($allPrograms, fn($x) => $x['program']['language']['englishTaught'] === 'Yes'));
$moiAccepted = count(array_filter($allPrograms, fn($x) => $x['program']['language']['moiAccepted'] === 'Yes'));
$shortlisted = count(array_filter($universities, fn($u) => $u['status'] === 'Shortlisted'));
$eligible = count(array_filter($allPrograms, fn($x) => $x['program']['personal']['eligibility'] === 'Eligible'));
$readyToApply = count(array_filter($allPrograms, fn($x) => $x['program']['personal']['applicationStatus'] === 'Ready to Apply'));
$applied = count(array_filter($allPrograms, fn($x) => $x['program']['personal']['applicationStatus'] === 'Applied'));
$offers = count(array_filter($allPrograms, fn($x) => $x['program']['personal']['applicationStatus'] === 'Offer Received'));

$stats = [
    ['Total Universities', $totalUniversities, 'bi-bank', 'primary'],
    ['Total Programs', $totalPrograms, 'bi-mortarboard', 'primary'],
    ['Public Universities', $publicUniversities, 'bi-building', 'secondary'],
    ['English Programs', $englishPrograms, 'bi-translate', 'info'],
    ['MOI Accepted', $moiAccepted, 'bi-file-earmark-check', 'info'],
    ['Shortlisted', $shortlisted, 'bi-star', 'warning'],
    ['Eligible', $eligible, 'bi-patch-check', 'success'],
    ['Ready to Apply', $readyToApply, 'bi-send', 'warning'],
    ['Applied', $applied, 'bi-envelope-paper', 'primary'],
    ['Offers Received', $offers, 'bi-trophy', 'success'],
];

// ---- Upcoming deadlines (across all deadline fields) ----
$deadlines = [];
foreach ($allPrograms as $x) {
    $p = $x['program']; $u = $x['university'];
    $candidates = [
        'Deadline' => $p['application']['deadline'],
        'Winter Deadline' => $p['application']['winterDeadline'],
        'Summer Deadline' => $p['application']['summerDeadline'],
        'Int\'l Applicant Deadline' => $p['application']['intlDeadline'],
    ];
    foreach ($candidates as $label => $dateVal) {
        if (!empty($dateVal)) {
            $days = days_until($dateVal);
            if ($days !== null && $days >= -3) { // include very recently passed too
                $deadlines[] = ['uni' => $u, 'prog' => $p, 'label' => $label, 'date' => $dateVal, 'days' => $days];
            }
        }
    }
}
usort($deadlines, fn($a, $b) => $a['days'] <=> $b['days']);
$deadlines = array_slice($deadlines, 0, 8);

// ---- Recently updated (universities + programs combined) ----
$recent = [];
foreach ($universities as $u) {
      $recent[] = ['type' => 'University', 'name' => $u['name'], 'updatedAt' => $u['updated_at'], 'url' => 'university.php?id=' . urlencode($u['id'])];
}
foreach ($allPrograms as $x) {
    $recent[] = ['type' => 'Program', 'name' => $x['program']['name'] . ' — ' . $x['university']['name'], 'updatedAt' => $x['program']['updated_at'], 'url' => 'program.php?id=' . urlencode($x['program']['id']) . '&university_id=' . urlencode($x['university']['id'])];
}
usort($recent, fn($a, $b) => strtotime($b['updatedAt']) <=> strtotime($a['updatedAt']));
$recent = array_slice($recent, 0, 8);

// ---- High priority programs ----
$highPriority = array_filter($allPrograms, fn($x) => $x['program']['personal']['priority'] === 'High');

// ---- Needs verification ----
$needsVerification = array_filter($allPrograms, function ($x) {
    $p = $x['program'];
    return $p['personal']['eligibility'] === 'Unknown'
        || $p['personal']['eligibility'] === 'Maybe'
        || $p['language']['moiAccepted'] === 'Unknown'
        || $p['language']['englishTaught'] === 'Unknown';
});

$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
include __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <h4 class="mb-0">Dashboard</h4>
  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUniversityModal"><i class="bi bi-plus-lg me-1"></i>Add University</button>
</div>

<div class="row g-3 mb-4 row-cols-2 row-cols-md-3 row-cols-xl-5">
  <?php foreach ($stats as [$label, $value, $icon, $color]): ?>
    <div class="col">
      <div class="stat-card">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="stat-value"><?= (int)$value ?></div>
            <div class="stat-label"><?= h($label) ?></div>
          </div>
          <i class="bi <?= h($icon) ?> fs-4 text-<?= h($color) ?>"></i>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card h-100 shadow-sm">
      <div class="card-header bg-transparent fw-semibold"><i class="bi bi-alarm me-2"></i>Upcoming Deadlines</div>
      <div class="list-group list-group-flush">
        <?php if (empty($deadlines)): ?>
          <div class="list-group-item text-muted small">No deadlines recorded yet. Add deadlines on each program's Application tab.</div>
        <?php endif; ?>
        <?php foreach ($deadlines as $d): ?>
          <a href="program.php?id=<?= urlencode($d['prog']['id']) ?>&university_id=<?= urlencode($d['uni']['id']) ?>&tab=application" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <div>
              <div class="fw-semibold"><?= h($d['prog']['name']) ?></div>
              <div class="small text-muted"><?= h($d['uni']['name']) ?> · <?= h($d['label']) ?></div>
            </div>
            <div class="text-end">
              <div class="<?= $d['days'] < 0 ? 'text-muted' : ($d['days'] <= 7 ? 'deadline-soon' : ($d['days'] <= 30 ? 'deadline-week' : '')) ?>"><?= fmt_date($d['date']) ?></div>
              <div class="small text-muted"><?= $d['days'] < 0 ? 'Passed' : ($d['days'] === 0 ? 'Today' : $d['days'] . ' days left') ?></div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card h-100 shadow-sm">
      <div class="card-header bg-transparent fw-semibold"><i class="bi bi-clock-history me-2"></i>Recently Updated</div>
      <div class="list-group list-group-flush">
        <?php if (empty($recent)): ?>
          <div class="list-group-item text-muted small">Nothing recorded yet.</div>
        <?php endif; ?>
        <?php foreach ($recent as $r): ?>
          <a href="<?= h($r['url']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <div>
              <span class="badge text-bg-light border me-2"><?= h($r['type']) ?></span>
              <?= h($r['name']) ?>
            </div>
            <span class="small text-muted"><?= fmt_date($r['updatedAt']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card h-100 shadow-sm">
      <div class="card-header bg-transparent fw-semibold"><i class="bi bi-star-fill text-danger me-2"></i>High Priority Programs</div>
      <div class="list-group list-group-flush">
        <?php if (empty($highPriority)): ?>
          <div class="list-group-item text-muted small">No programs marked High priority yet.</div>
        <?php endif; ?>
        <?php foreach ($highPriority as $x): ?>
          <a href="program.php?id=<?= urlencode($x['program']['id']) ?>&university_id=<?= urlencode($x['university']['id']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <div>
              <div class="fw-semibold"><?= h($x['program']['name']) ?></div>
              <div class="small text-muted"><?= h($x['university']['name']) ?></div>
            </div>
            <span class="badge text-bg-danger">High</span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card h-100 shadow-sm">
      <div class="card-header bg-transparent fw-semibold"><i class="bi bi-question-circle me-2"></i>Needs Verification</div>
      <div class="list-group list-group-flush">
        <?php if (empty($needsVerification)): ?>
          <div class="list-group-item text-muted small">Nothing pending verification. 🎉</div>
        <?php endif; ?>
        <?php foreach ($needsVerification as $x): ?>
          <a href="program.php?id=<?= urlencode($x['program']['id']) ?>&university_id=<?= urlencode($x['university']['id']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <div>
              <div class="fw-semibold"><?= h($x['program']['name']) ?></div>
              <div class="small text-muted"><?= h($x['university']['name']) ?></div>
            </div>
            <span class="badge text-bg-<?= eligibility_badge_class($x['program']['personal']['eligibility']) ?>"><?= h($x['program']['personal']['eligibility']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
