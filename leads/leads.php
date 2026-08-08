<?php
/**
 * leads.php
 * Full leads table with search, status filter, column sorting,
 * and quick view/edit/delete actions.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

// ----------------------------------------------------------------------
// Read & sanitize query params
// ----------------------------------------------------------------------
$search       = sanitizeText($_GET['q'] ?? '');
$statusFilter = sanitizeText($_GET['status'] ?? 'all');

$allowedSorts = ['company_name', 'contact_person', 'status', 'priority', 'next_followup', 'created_at'];
$sortField    = in_array($_GET['sort'] ?? '', $allowedSorts, true) ? $_GET['sort'] : 'created_at';
$sortDir      = ($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

// ----------------------------------------------------------------------
// Load, filter, sort
// ----------------------------------------------------------------------
$leads = getAllLeads();

if ($search !== '') {
    $needle = mb_strtolower($search);
    $leads = array_filter($leads, function ($lead) use ($needle) {
        $haystack = mb_strtolower(implode(' ', [
            $lead['company_name'] ?? '',
            $lead['contact_person'] ?? '',
            $lead['email'] ?? '',
            $lead['phone'] ?? '',
            $lead['country'] ?? '',
        ]));
        return str_contains($haystack, $needle);
    });
}

if ($statusFilter !== 'all' && array_key_exists($statusFilter, LEAD_STATUSES)) {
    $leads = array_filter($leads, fn($lead) => ($lead['status'] ?? '') === $statusFilter);
}

$leads = array_values($leads);

usort($leads, function ($a, $b) use ($sortField, $sortDir) {
    $valA = $a[$sortField] ?? '';
    $valB = $b[$sortField] ?? '';
    $cmp  = strcasecmp((string) $valA, (string) $valB);
    return $sortDir === 'asc' ? $cmp : -$cmp;
});

/**
 * Build a sort link URL, toggling direction when clicking the active column.
 */
function sortUrl(string $field, string $currentField, string $currentDir, string $search, string $status): string
{
    $newDir = ($field === $currentField && $currentDir === 'asc') ? 'desc' : 'asc';
    $params = [
        'q'      => $search,
        'status' => $status,
        'sort'   => $field,
        'dir'    => $newDir,
    ];
    return '?' . http_build_query($params);
}

/**
 * Render the sort icon for a column header.
 */
function sortIcon(string $field, string $currentField, string $currentDir): string
{
    if ($field !== $currentField) {
        return '<i class="bi bi-arrow-down-up"></i>';
    }
    return $currentDir === 'asc' ? '<i class="bi bi-sort-alpha-down"></i>' : '<i class="bi bi-sort-alpha-up"></i>';
}

$pageTitle    = 'Leads';
$pageSubtitle = count($leads) . ' lead' . (count($leads) === 1 ? '' : 's') . ' found';

require __DIR__ . '/includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <form method="get" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input
                        type="text"
                        name="q"
                        class="form-control"
                        placeholder="Search by company, contact, email, phone, country..."
                        value="<?= e($search) ?>"
                    >
                </div>
            </div>
            <div class="col-8 col-md-4">
                <select name="status" class="form-select">
                    <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                    <?php foreach (array_keys(LEAD_STATUSES) as $status): ?>
                        <option value="<?= e($status) ?>" <?= $statusFilter === $status ? 'selected' : '' ?>>
                            <?= e($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-4 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <?php if ($search !== '' || $statusFilter !== 'all'): ?>
                    <a href="<?= e(BASE_URL) ?>/leads.php" class="btn btn-outline-secondary" title="Clear filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <?php if (empty($leads)): ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <?php if ($search !== '' || $statusFilter !== 'all'): ?>
                    No leads match your search/filter.
                    <div class="mt-2"><a href="<?= e(BASE_URL) ?>/leads.php">Clear filters</a></div>
                <?php else: ?>
                    No leads yet. <a href="<?= e(BASE_URL) ?>/lead-add.php">Add your first lead</a>.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th class="sortable">
                                <a class="text-decoration-none text-reset d-flex align-items-center" href="<?= e(sortUrl('company_name', $sortField, $sortDir, $search, $statusFilter)) ?>">
                                    Company <?= sortIcon('company_name', $sortField, $sortDir) ?>
                                </a>
                            </th>
                            <th class="sortable">
                                <a class="text-decoration-none text-reset d-flex align-items-center" href="<?= e(sortUrl('contact_person', $sortField, $sortDir, $search, $statusFilter)) ?>">
                                    Contact <?= sortIcon('contact_person', $sortField, $sortDir) ?>
                                </a>
                            </th>
                            <th>Email / Phone</th>
                            <th class="sortable">
                                <a class="text-decoration-none text-reset d-flex align-items-center" href="<?= e(sortUrl('status', $sortField, $sortDir, $search, $statusFilter)) ?>">
                                    Status <?= sortIcon('status', $sortField, $sortDir) ?>
                                </a>
                            </th>
                            <th class="sortable">
                                <a class="text-decoration-none text-reset d-flex align-items-center" href="<?= e(sortUrl('priority', $sortField, $sortDir, $search, $statusFilter)) ?>">
                                    Priority <?= sortIcon('priority', $sortField, $sortDir) ?>
                                </a>
                            </th>
                            <th class="sortable">
                                <a class="text-decoration-none text-reset d-flex align-items-center" href="<?= e(sortUrl('next_followup', $sortField, $sortDir, $search, $statusFilter)) ?>">
                                    Follow-up <?= sortIcon('next_followup', $sortField, $sortDir) ?>
                                </a>
                            </th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leads as $lead): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle">
                                            <?= e(strtoupper(substr($lead['company_name'] ?? '?', 0, 1))) ?>
                                        </div>
                                        <div>
                                            <a href="<?= e(BASE_URL) ?>/lead-view.php?id=<?= urlencode($lead['id']) ?>" class="fw-semibold text-decoration-none text-dark">
                                                <?= e($lead['company_name']) ?>
                                            </a>
                                            <div class="text-muted-sm"><?= e($lead['country']) ?: '—' ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?= e($lead['contact_person']) ?: '—' ?>
                                    <?php if (!empty($lead['designation'])): ?>
                                        <div class="text-muted-sm"><?= e($lead['designation']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="text-muted-sm"><?= e($lead['email']) ?: '—' ?></div>
                                    <div class="text-muted-sm"><?= e($lead['phone']) ?: '—' ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= e(statusColor($lead['status'] ?? 'New')) ?>">
                                        <?= e($lead['status'] ?? 'New') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= e(priorityColor($lead['priority'] ?? 'Medium')) ?>">
                                        <?= e($lead['priority'] ?? 'Medium') ?>
                                    </span>
                                </td>
                                <td class="text-muted-sm"><?= e(formatDate($lead['next_followup'] ?? '')) ?></td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?= e(BASE_URL) ?>/lead-view.php?id=<?= urlencode($lead['id']) ?>"
                                           class="btn btn-icon btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= e(BASE_URL) ?>/lead-edit.php?id=<?= urlencode($lead['id']) ?>"
                                           class="btn btn-icon btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                           class="btn btn-icon btn-outline-danger btn-delete-lead"
                                           title="Delete"
                                           data-id="<?= e($lead['id']) ?>"
                                           data-name="<?= e($lead['company_name']) ?>"
                                           data-bs-toggle="modal"
                                           data-bs-target="#deleteModal">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ================= Delete Confirmation Modal ================= -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= e(BASE_URL) ?>/lead-delete.php" id="deleteForm">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Delete Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        Are you sure you want to delete
                        <strong id="deleteLeadName"></strong>?
                        This action cannot be undone.
                    </p>
                    <input type="hidden" name="id" id="deleteLeadId" value="">
                    <input type="hidden" name="redirect" value="leads.php">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete Lead
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$extraScripts = <<<'HTML'
<script>
document.querySelectorAll('.btn-delete-lead').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.getElementById('deleteLeadId').value = btn.dataset.id;
        document.getElementById('deleteLeadName').textContent = btn.dataset.name;
    });
});
</script>
HTML;

require __DIR__ . '/includes/footer.php';
