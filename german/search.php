<?php
require __DIR__ . '/includes/auth.php';
require_login();
$userId = effective_user_id();

$q = trim($_GET['q'] ?? '');
$uniResults = [];
$progResults = [];

if ($q !== '') {
    $results = search_user_data($userId, $q);
    $uniResults = $results['universities'];
    $progResults = $results['programs'];
}

$pageTitle = 'Search';
$activeNav = 'search';
include __DIR__ . '/includes/header.php';
?>

<h4 class="mb-3">Global Search</h4>

<form method="get" class="mb-4">
  <div class="input-group input-group-lg">
    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
    <input type="search" name="q" class="form-control" placeholder="Search universities, programs, notes, MOI, IELTS, fees…" value="<?= h($q) ?>" autofocus>
    <button class="btn btn-primary" type="submit">Search</button>
  </div>
</form>

<?php if ($q === ''): ?>
  <p class="text-muted">Try searching for a university name, city, program subject, or research terms like "MOI", "IELTS", or "Uni-Assist".</p>
<?php else: ?>
  <p class="text-muted"><?= count($uniResults) + count($progResults) ?> result<?= (count($uniResults) + count($progResults)) === 1 ? '' : 's' ?> for "<?= h($q) ?>"</p>

  <?php if ($uniResults): ?>
    <h6 class="text-muted mt-4 mb-2">Universities (<?= count($uniResults) ?>)</h6>
    <div class="list-group mb-4">
      <?php foreach ($uniResults as $uni): ?>
        <a href="university.php?id=<?= urlencode($uni['id']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <div><span class="fw-semibold">🏛 <?= h($uni['name']) ?></span> <span class="text-muted small"><?= h($uni['city']) ?><?= $uni['city'] && $uni['state'] ? ', ' : '' ?><?= h($uni['state']) ?></span></div>
          <span class="badge text-bg-<?= status_badge_class($uni['status']) ?>"><?= h($uni['status']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if ($progResults): ?>
    <h6 class="text-muted mt-4 mb-2">Programs (<?= count($progResults) ?>)</h6>
    <div class="list-group mb-4">
      <?php foreach ($progResults as $r): ?>
        <a href="program.php?id=<?= urlencode($r['program']['id']) ?>&university_id=<?= urlencode($r['university']['id']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <div><span class="fw-semibold">💻 <?= h($r['program']['name']) ?></span> <span class="text-muted small"><?= h($r['university']['name']) ?></span></div>
          <span class="badge text-bg-<?= eligibility_badge_class($r['program']['personal']['eligibility']) ?>"><?= h($r['program']['personal']['eligibility']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (!$uniResults && !$progResults): ?>
    <div class="text-center text-muted py-5"><i class="bi bi-search fs-1 d-block mb-2"></i>No matches found.</div>
  <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
