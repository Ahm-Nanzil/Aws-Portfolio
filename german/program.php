<?php
require __DIR__ . '/includes/auth.php';
require_login();
$userId = effective_user_id();

$progId = (int)($_GET['id'] ?? 0);
$program = get_program($userId, $progId);

if ($program === null) {
    flash('danger', 'Program not found.');
    redirect(base_path() . '/programs.php');
}
$uniId = (int)$program['uni_id'];
$uni = ['id' => $uniId, 'name' => $program['uni_name'], 'status' => $program['uni_status']];

$tab = $_GET['tab'] ?? 'overview';
$validTabs = ['overview', 'admission', 'language', 'fees', 'application', 'documents', 'links', 'personal'];
if (!in_array($tab, $validTabs, true)) $tab = 'overview';

$backUrl = 'university.php?id=' . $uniId . '&tab=programs';
$pageTitle = $program['name'];
$activeNav = 'program';
$yn = ['Yes', 'No', 'Conditional', 'Unknown'];
$ynSimple = ['Yes', 'No', 'Unknown'];

include __DIR__ . '/includes/header.php';

function tabLink($key, $icon, $label, $tab, $progId, $uniId) {
    $active = $tab === $key ? 'active' : '';
    echo '<li class="nav-item"><a class="nav-link ' . $active . '" href="program.php?id=' . urlencode($progId) . '&university_id=' . urlencode($uniId) . '&tab=' . $key . '"><i class="bi ' . $icon . ' me-1"></i>' . h($label) . '</a></li>';
}
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb small">
    <li class="breadcrumb-item"><a href="universities.php">University Explorer</a></li>
    <li class="breadcrumb-item"><a href="university.php?id=<?= urlencode($uniId) ?>"><?= h($uni['name']) ?></a></li>
    <li class="breadcrumb-item active"><?= h($program['name']) ?></li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
  <div>
    <h4 class="mb-1">💻 <?= h($program['name']) ?></h4>
    <div class="text-muted small"><?= h($uni['name']) ?><?= $program['degree'] ? ' · ' . h($program['degree']) : '' ?></div>
  </div>
  <div class="d-flex align-items-center gap-2 flex-wrap">
    <span class="badge text-bg-<?= priority_badge_class($program['personal']['priority']) ?>"><?= h($program['personal']['priority']) ?> priority</span>
    <span class="badge text-bg-<?= status_badge_class($program['personal']['applicationStatus']) ?>"><?= h($program['personal']['applicationStatus']) ?></span>
    <form method="post" action="actions/program-duplicate.php" class="d-inline">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <input type="hidden" name="id" value="<?= h($program['id']) ?>">
      <input type="hidden" name="university_id" value="<?= h($uniId) ?>">
      <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-files me-1"></i>Duplicate</button>
    </form>
    <button type="button" class="btn btn-sm btn-outline-danger" data-confirm-delete
      data-delete-action="actions/program-delete.php"
      data-delete-id="<?= h($program['id']) ?>"
      data-delete-uni-id="<?= h($uniId) ?>"
      data-delete-text='Delete program "<?= h($program['name']) ?>"? This cannot be undone.'>
      <i class="bi bi-trash3 me-1"></i>Delete
    </button>
    <a href="<?= h($backUrl) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
  </div>
</div>

<ul class="nav nav-tabs mb-3 flex-nowrap overflow-auto">
  <?php
  tabLink('overview', 'bi-info-circle', 'Overview', $tab, $progId, $uniId);
  tabLink('admission', 'bi-clipboard-check', 'Admission', $tab, $progId, $uniId);
  tabLink('language', 'bi-translate', 'Language', $tab, $progId, $uniId);
  tabLink('fees', 'bi-cash-coin', 'Fees', $tab, $progId, $uniId);
  tabLink('application', 'bi-calendar-event', 'Deadlines', $tab, $progId, $uniId);
  tabLink('documents', 'bi-file-earmark-check', 'Documents', $tab, $progId, $uniId);
  tabLink('links', 'bi-link-45deg', 'Links', $tab, $progId, $uniId);
  tabLink('personal', 'bi-journal-text', 'Notes', $tab, $progId, $uniId);
  ?>
</ul>

<div class="card shadow-sm">
<div class="card-body">

<?php if ($tab === 'overview'): ?>
  <form method="post" action="actions/program-save.php">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= h($program['id']) ?>">
    <input type="hidden" name="university_id" value="<?= h($uniId) ?>">
    <input type="hidden" name="section" value="overview">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Program Name *</label>
        <input type="text" name="name" class="form-control" value="<?= h($program['name']) ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Degree</label>
        <input type="text" name="degree" class="form-control" value="<?= h($program['degree']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Intake</label>
        <select name="intake" class="form-select">
          <?php foreach (['Winter', 'Summer', 'Both'] as $i): ?><option <?= $i === $program['intake'] ? 'selected' : '' ?>><?= h($i) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Subject / Field</label>
        <input type="text" name="subject" class="form-control" value="<?= h($program['subject']) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Department</label>
        <input type="text" name="department" class="form-control" value="<?= h($program['department']) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Faculty</label>
        <input type="text" name="faculty" class="form-control" value="<?= h($program['faculty']) ?>">
      </div>
      <div class="col-md-8">
        <label class="form-label">Program Website</label>
        <input type="url" name="website" class="form-control" value="<?= h($program['website']) ?>" placeholder="https://">
      </div>
      <div class="col-md-4">
        <label class="form-label">Study Location</label>
        <input type="text" name="studyLocation" class="form-control" value="<?= h($program['studyLocation']) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Duration</label>
        <input type="text" name="duration" class="form-control" value="<?= h($program['duration']) ?>" placeholder="e.g. 4 semesters">
      </div>
      <div class="col-md-4">
        <label class="form-label">ECTS / Credits</label>
        <input type="text" name="ects" class="form-control" value="<?= h($program['ects']) ?>" placeholder="e.g. 120">
      </div>
      <div class="col-md-4">
        <label class="form-label">Full-time / Part-time</label>
        <select name="studyMode" class="form-select">
          <?php foreach (['Full-time', 'Part-time'] as $m): ?><option <?= $m === $program['studyMode'] ? 'selected' : '' ?>><?= h($m) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-12">
        <label class="form-label">Program Description</label>
        <textarea name="description" rows="4" class="form-control"><?= h($program['description']) ?></textarea>
      </div>
    </div>
    <div class="mt-3"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Overview</button></div>
  </form>

<?php elseif ($tab === 'admission'): ?>
  <?php $a = $program['admission']; ?>
  <form method="post" action="actions/program-save.php">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= h($program['id']) ?>">
    <input type="hidden" name="university_id" value="<?= h($uniId) ?>">
    <input type="hidden" name="section" value="admission">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Required Bachelor's Degree</label>
        <input type="text" name="requiredDegree" class="form-control" value="<?= h($a['requiredDegree']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Required Major</label>
        <input type="text" name="requiredMajor" class="form-control" value="<?= h($a['requiredMajor']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Minimum GPA / Grade</label>
        <input type="text" name="minGpa" class="form-control" value="<?= h($a['minGpa']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Required ECTS</label>
        <input type="text" name="requiredEcts" class="form-control" value="<?= h($a['requiredEcts']) ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label">CS ECTS</label>
        <input type="text" name="csEcts" class="form-control" value="<?= h($a['csEcts']) ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label">Math ECTS</label>
        <input type="text" name="mathEcts" class="form-control" value="<?= h($a['mathEcts']) ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label">Programming ECTS</label>
        <input type="text" name="programmingEcts" class="form-control" value="<?= h($a['programmingEcts']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Other ECTS Requirements</label>
        <input type="text" name="otherEcts" class="form-control" value="<?= h($a['otherEcts']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Work Experience Required?</label>
        <select name="workExperience" class="form-select"><?php foreach ($yn as $v): ?><option <?= $v === $a['workExperience'] ? 'selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?></select>
      </div>
      <div class="col-md-3">
        <label class="form-label">GRE Required?</label>
        <select name="greRequired" class="form-select"><?php foreach ($yn as $v): ?><option <?= $v === $a['greRequired'] ? 'selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?></select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Entrance Exam?</label>
        <select name="entranceExam" class="form-select"><?php foreach ($yn as $v): ?><option <?= $v === $a['entranceExam'] ? 'selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?></select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Interview?</label>
        <select name="interview" class="form-select"><?php foreach ($yn as $v): ?><option <?= $v === $a['interview'] ? 'selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?></select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Other Academic Requirements</label>
        <textarea name="otherAcademic" rows="3" class="form-control"><?= h($a['otherAcademic']) ?></textarea>
      </div>
      <div class="col-md-6">
        <label class="form-label">Additional Requirements</label>
        <textarea name="additionalReq" rows="3" class="form-control"><?= h($a['additionalReq']) ?></textarea>
      </div>
    </div>
    <div class="mt-3"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Admission Requirements</button></div>
  </form>

<?php elseif ($tab === 'language'): ?>
  <?php $l = $program['language']; ?>
  <form method="post" action="actions/program-save.php">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= h($program['id']) ?>">
    <input type="hidden" name="university_id" value="<?= h($uniId) ?>">
    <input type="hidden" name="section" value="language">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Teaching Language</label>
        <input type="text" name="teachingLanguage" class="form-control" value="<?= h($l['teachingLanguage']) ?>" placeholder="e.g. English">
      </div>
      <div class="col-md-4">
        <label class="form-label">English-taught?</label>
        <select name="englishTaught" class="form-select"><?php foreach ($ynSimple as $v): ?><option <?= $v === $l['englishTaught'] ? 'selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?></select>
      </div>
      <div class="col-md-4">
        <label class="form-label">English Percentage</label>
        <input type="text" name="englishPercentage" class="form-control" value="<?= h($l['englishPercentage']) ?>" placeholder="e.g. 100%">
      </div>
      <div class="col-md-6">
        <label class="form-label">German Requirement</label>
        <input type="text" name="germanRequirement" class="form-control" value="<?= h($l['germanRequirement']) ?>" placeholder="e.g. None / B1 / A2">
      </div>
      <div class="col-md-6">
        <label class="form-label">English Requirement</label>
        <input type="text" name="englishRequirement" class="form-control" value="<?= h($l['englishRequirement']) ?>" placeholder="e.g. B2 / C1">
      </div>
      <div class="col-md-3">
        <label class="form-label">IELTS Required?</label>
        <select name="ieltsRequired" class="form-select"><?php foreach ($yn as $v): ?><option <?= $v === $l['ieltsRequired'] ? 'selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?></select>
      </div>
      <div class="col-md-3">
        <label class="form-label">IELTS Minimum Score</label>
        <input type="text" name="ieltsMin" class="form-control" value="<?= h($l['ieltsMin']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">TOEFL Required?</label>
        <select name="toeflRequired" class="form-select"><?php foreach ($yn as $v): ?><option <?= $v === $l['toeflRequired'] ? 'selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?></select>
      </div>
      <div class="col-md-3">
        <label class="form-label">TOEFL Minimum Score</label>
        <input type="text" name="toeflMin" class="form-control" value="<?= h($l['toeflMin']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Other English Tests</label>
        <input type="text" name="otherTests" class="form-control" value="<?= h($l['otherTests']) ?>" placeholder="e.g. Duolingo, PTE">
      </div>
      <div class="col-md-3">
        <label class="form-label">MOI Accepted?</label>
        <select name="moiAccepted" class="form-select"><?php foreach ($yn as $v): ?><option <?= $v === $l['moiAccepted'] ? 'selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?></select>
      </div>
      <div class="col-md-12">
        <label class="form-label">MOI Details</label>
        <textarea name="moiDetails" rows="2" class="form-control"><?= h($l['moiDetails']) ?></textarea>
      </div>
    </div>
    <div class="mt-3"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Language Requirements</button></div>
  </form>

<?php elseif ($tab === 'fees'): ?>
  <?php $f = $program['fees']; ?>
  <form method="post" action="actions/program-save.php">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= h($program['id']) ?>">
    <input type="hidden" name="university_id" value="<?= h($uniId) ?>">
    <input type="hidden" name="section" value="fees">
    <div class="alert alert-light border small">Public universities usually charge no tuition, but always verify — some states or programs do charge fees. Enter what you've confirmed; leave blank if unverified.</div>
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Tuition Fee</label>
        <input type="text" name="tuitionFee" class="form-control" value="<?= h($f['tuitionFee']) ?>" placeholder="e.g. €0 or Unknown">
      </div>
      <div class="col-md-3">
        <label class="form-label">Semester Contribution</label>
        <input type="text" name="semesterContribution" class="form-control" value="<?= h($f['semesterContribution']) ?>" placeholder="e.g. €250">
      </div>
      <div class="col-md-3">
        <label class="form-label">Application Fee</label>
        <input type="text" name="applicationFee" class="form-control" value="<?= h($f['applicationFee']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Uni-Assist Fee</label>
        <input type="text" name="uniAssistFee" class="form-control" value="<?= h($f['uniAssistFee']) ?>" placeholder="e.g. €75">
      </div>
      <div class="col-md-6">
        <label class="form-label">Other Fees</label>
        <input type="text" name="otherFees" class="form-control" value="<?= h($f['otherFees']) ?>">
      </div>
      <div class="col-md-12">
        <label class="form-label">Fee Notes</label>
        <textarea name="feeNotes" rows="3" class="form-control"><?= h($f['feeNotes']) ?></textarea>
      </div>
    </div>
    <div class="mt-3"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Fees</button></div>
  </form>

<?php elseif ($tab === 'application'): ?>
  <?php $ap = $program['application']; ?>
  <form method="post" action="actions/program-save.php">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= h($program['id']) ?>">
    <input type="hidden" name="university_id" value="<?= h($uniId) ?>">
    <input type="hidden" name="section" value="application">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Application Method</label>
        <select name="method" class="form-select">
          <?php foreach (['Direct', 'Uni-Assist', 'Other'] as $m): ?><option <?= $m === $ap['method'] ? 'selected' : '' ?>><?= h($m) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Application Portal</label>
        <input type="url" name="portal" class="form-control" value="<?= h($ap['portal']) ?>" placeholder="https://">
      </div>
      <div class="col-md-4">
        <label class="form-label">Application URL</label>
        <input type="url" name="url" class="form-control" value="<?= h($ap['url']) ?>" placeholder="https://">
      </div>
      <div class="col-md-3">
        <label class="form-label">Application Start Date</label>
        <input type="date" name="startDate" class="form-control" value="<?= h($ap['startDate']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Application Deadline</label>
        <input type="date" name="deadline" class="form-control" value="<?= h($ap['deadline']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Winter Deadline</label>
        <input type="date" name="winterDeadline" class="form-control" value="<?= h($ap['winterDeadline']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Summer Deadline</label>
        <input type="date" name="summerDeadline" class="form-control" value="<?= h($ap['summerDeadline']) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">International Applicant Deadline</label>
        <input type="date" name="intlDeadline" class="form-control" value="<?= h($ap['intlDeadline']) ?>">
      </div>
      <div class="col-md-8">
        <label class="form-label">Other Deadline Information</label>
        <input type="text" name="otherInfo" class="form-control" value="<?= h($ap['otherInfo']) ?>">
      </div>
    </div>
    <div class="mt-3"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Application Info</button></div>
  </form>

<?php elseif ($tab === 'documents'): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted small"><?= count($program['documents']) ?> document<?= count($program['documents']) === 1 ? '' : 's' ?> in checklist</div>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDocModal"><i class="bi bi-plus-lg me-1"></i>Add Document</button>
  </div>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead class="table-light"><tr><th>Document</th><th style="width:220px;">Status</th><th class="text-end" style="width:60px;"></th></tr></thead>
      <tbody>
        <?php foreach ($program['documents'] as $doc): ?>
          <tr>
            <td>☐ <?= h($doc['name']) ?></td>
            <td>
              <form method="post" action="actions/document-save.php">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="op" value="update_status">
                <input type="hidden" name="id" value="<?= h($program['id']) ?>">
                <input type="hidden" name="university_id" value="<?= h($uniId) ?>">
                <input type="hidden" name="doc_id" value="<?= h($doc['id']) ?>">
                <select name="doc_status" class="form-select form-select-sm" onchange="this.form.submit()">
                  <?php foreach (['Required', 'Optional', 'Not Required', 'Unknown', 'Ready', 'Missing'] as $s): ?>
                    <option value="<?= h($s) ?>" <?= $s === $doc['status'] ? 'selected' : '' ?>><?= h($s) ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
            </td>
            <td class="text-end">
              <form method="post" action="actions/document-save.php" onsubmit="return confirm('Remove this document from the checklist?');">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="op" value="delete">
                <input type="hidden" name="id" value="<?= h($program['id']) ?>">
                <input type="hidden" name="university_id" value="<?= h($uniId) ?>">
                <input type="hidden" name="doc_id" value="<?= h($doc['id']) ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="modal fade" id="addDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" action="actions/document-save.php">
          <div class="modal-header">
            <h5 class="modal-title">Add Custom Document</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="op" value="add">
            <input type="hidden" name="id" value="<?= h($program['id']) ?>">
            <input type="hidden" name="university_id" value="<?= h($uniId) ?>">
            <div class="mb-2">
              <label class="form-label">Document Name *</label>
              <input type="text" name="doc_name" class="form-control" required placeholder="e.g. Portfolio">
            </div>
            <div class="mb-2">
              <label class="form-label">Initial Status</label>
              <select name="doc_status" class="form-select">
                <?php foreach (['Required', 'Optional', 'Not Required', 'Unknown', 'Ready', 'Missing'] as $s): ?><option><?= h($s) ?></option><?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Document</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php elseif ($tab === 'links'): ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted small"><?= count($program['links']) ?> link<?= count($program['links']) === 1 ? '' : 's' ?></div>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addLinkModal"><i class="bi bi-plus-lg me-1"></i>Add Link</button>
  </div>
  <?php if (empty($program['links'])): ?>
    <p class="text-muted">No links added yet.</p>
  <?php endif; ?>
  <div class="list-group">
    <?php foreach ($program['links'] as $link): ?>
      <div class="list-group-item d-flex justify-content-between align-items-start">
        <div>
          <div class="fw-semibold"><i class="bi bi-link-45deg me-1"></i><a href="<?= h($link['url']) ?>" target="_blank" rel="noopener noreferrer"><?= h($link['title']) ?></a></div>
          <?php if ($link['description']): ?><div class="small text-muted"><?= h($link['description']) ?></div><?php endif; ?>
          <div class="small text-truncate" style="max-width:500px;"><a href="<?= h($link['url']) ?>" target="_blank" rel="noopener noreferrer" class="text-muted"><?= h($link['url']) ?></a></div>
        </div>
        <form method="post" action="actions/link-save.php" onsubmit="return confirm('Remove this link?');">
          <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
          <input type="hidden" name="op" value="delete">
          <input type="hidden" name="id" value="<?= h($program['id']) ?>">
          <input type="hidden" name="university_id" value="<?= h($uniId) ?>">
          <input type="hidden" name="link_id" value="<?= h($link['id']) ?>">
          <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3"></i></button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="modal fade" id="addLinkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" action="actions/link-save.php">
          <div class="modal-header">
            <h5 class="modal-title">Add Link</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
            <input type="hidden" name="op" value="add">
            <input type="hidden" name="id" value="<?= h($program['id']) ?>">
            <input type="hidden" name="university_id" value="<?= h($uniId) ?>">
            <div class="mb-2">
              <label class="form-label">Link Title *</label>
              <input type="text" list="linkTitleSuggestions" name="link_title" class="form-control" required placeholder="e.g. Program Page">
              <datalist id="linkTitleSuggestions">
                <option>Official University Website</option><option>Program Page</option><option>Application Portal</option>
                <option>Admission Requirements</option><option>Language Requirements</option><option>Fee Information</option>
                <option>Deadline Information</option><option>Course Catalog</option><option>Other</option>
              </datalist>
            </div>
            <div class="mb-2">
              <label class="form-label">URL *</label>
              <input type="url" name="link_url" class="form-control" required placeholder="https://">
            </div>
            <div class="mb-2">
              <label class="form-label">Description</label>
              <input type="text" name="link_description" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Link</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php elseif ($tab === 'personal'): ?>
  <?php $pe = $program['personal']; ?>
  <form method="post" action="actions/program-save.php">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= h($program['id']) ?>">
    <input type="hidden" name="university_id" value="<?= h($uniId) ?>">
    <input type="hidden" name="section" value="personal">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Eligibility</label>
        <select name="eligibility" class="form-select">
          <?php foreach (['Eligible', 'Maybe', 'Not Eligible', 'Unknown'] as $v): ?><option <?= $v === $pe['eligibility'] ? 'selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Priority</label>
        <select name="priority" class="form-select">
          <?php foreach (['High', 'Medium', 'Low'] as $v): ?><option <?= $v === $pe['priority'] ? 'selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Application Status</label>
        <select name="applicationStatus" class="form-select">
          <?php foreach (['Not Started', 'Researching', 'Ready to Apply', 'Applied', 'Offer Received', 'Rejected'] as $v): ?><option <?= $v === $pe['applicationStatus'] ? 'selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Last Checked</label>
        <input type="date" name="lastChecked" class="form-control" value="<?= h($pe['lastChecked']) ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Personal Notes</label>
        <textarea name="notes" rows="6" class="form-control" placeholder="Anything you want to remember about this program…"><?= h($pe['notes']) ?></textarea>
      </div>
      <div class="col-12">
        <label class="form-label">Questions / Things to Verify</label>
        <textarea name="questions" rows="6" class="form-control" placeholder="Open questions to resolve before applying…"><?= h($pe['questions']) ?></textarea>
      </div>
    </div>
    <div class="mt-3"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Notes</button></div>
  </form>
<?php endif; ?>

</div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
