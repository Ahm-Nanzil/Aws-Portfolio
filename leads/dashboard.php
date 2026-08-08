<?php
/**
 * dashboard.php
 * Overview page: summary stat cards, recent leads, upcoming follow-ups.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$leads   = getAllLeads();
$counts  = getLeadCounts($leads);
$recent  = getRecentLeads($leads, 5);
$upcoming = getUpcomingFollowups($leads, 5);

$pageTitle    = 'Dashboard';
$pageSubtitle = 'Overview of your leads and activity';

require __DIR__ . '/includes/header.php';
?>

<!-- ================= Summary Cards ================= -->
<div class="row g-3 mb-4">

    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon bg-icon-indigo"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-value"><?= (int) $counts['total'] ?></div>
                <div class="stat-label">Total Leads</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon bg-icon-blue"><i class="bi bi-stars"></i></div>
            <div>
                <div class="stat-value"><?= (int) $counts['New'] ?></div>
                <div class="stat-label">New Leads</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon bg-icon-cyan"><i class="bi bi-telephone-fill"></i></div>
            <div>
                <div class="stat-value"><?= (int) $counts['Contacted'] ?></div>
                <div class="stat-label">Contacted</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon bg-icon-purple"><i class="bi bi-heart-fill"></i></div>
            <div>
                <div class="stat-value"><?= (int) $counts['Interested'] ?></div>
                <div class="stat-label">Interested</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon bg-icon-green"><i class="bi bi-trophy-fill"></i></div>
            <div>
                <div class="stat-value"><?= (int) $counts['Won'] ?></div>
                <div class="stat-label">Won</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon bg-icon-red"><i class="bi bi-x-circle-fill"></i></div>
            <div>
                <div class="stat-value"><?= (int) $counts['Lost'] ?></div>
                <div class="stat-label">Lost</div>
            </div>
        </div>
    </div>

</div>

<div class="row g-3">

    <!-- ================= Recent Leads ================= -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-clock-history me-1"></i> Recent Leads</span>
                <a href="<?= e(BASE_URL) ?>/leads.php" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recent)): ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        No leads yet. <a href="<?= e(BASE_URL) ?>/lead-add.php">Add your first lead</a>.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Added</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent as $lead): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-circle">
                                                    <?= e(strtoupper(substr($lead['company_name'] ?? '?', 0, 1))) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold"><?= e($lead['company_name']) ?></div>
                                                    <div class="text-muted-sm"><?= e($lead['country']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= e($lead['contact_person']) ?: '—' ?></td>
                                        <td>
                                            <span class="badge bg-<?= e(statusColor($lead['status'] ?? 'New')) ?>">
                                                <?= e($lead['status'] ?? 'New') ?>
                                            </span>
                                        </td>
                                        <td class="text-muted-sm"><?= e(formatDate($lead['created_at'] ?? '', 'M d, Y')) ?></td>
                                        <td class="text-end pe-3">
                                            <a href="<?= e(BASE_URL) ?>/lead-view.php?id=<?= urlencode($lead['id']) ?>" class="btn btn-icon btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ================= Upcoming Follow-ups ================= -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-calendar-event me-1"></i> Upcoming Follow-ups</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($upcoming)): ?>
                    <div class="empty-state">
                        <i class="bi bi-calendar-check"></i>
                        No upcoming follow-ups scheduled.
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($upcoming as $lead): ?>
                            <li class="list-group-item d-flex align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle">
                                        <?= e(strtoupper(substr($lead['company_name'] ?? '?', 0, 1))) ?>
                                    </div>
                                    <div>
                                        <a href="<?= e(BASE_URL) ?>/lead-view.php?id=<?= urlencode($lead['id']) ?>" class="fw-semibold text-decoration-none text-dark">
                                            <?= e($lead['company_name']) ?>
                                        </a>
                                        <div class="text-muted-sm"><?= e($lead['contact_person']) ?: '—' ?></div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-<?= e(priorityColor($lead['priority'] ?? 'Medium')) ?>">
                                        <?= e($lead['priority'] ?? 'Medium') ?>
                                    </span>
                                    <div class="text-muted-sm mt-1">
                                        <i class="bi bi-calendar3"></i> <?= e(formatDate($lead['next_followup'] ?? '')) ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
