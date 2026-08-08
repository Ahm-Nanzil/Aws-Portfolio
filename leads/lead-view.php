<?php
/**
 * lead-view.php
 * Beautiful profile page showing all information for a single lead.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$id = sanitizeText($_GET['id'] ?? '');
$lead = $id !== '' ? findLeadById($id) : null;

if (!$lead) {
    setFlash('danger', 'Lead not found.');
    redirect('leads.php');
}

$pageTitle    = $lead['company_name'];
$pageSubtitle = 'Lead Profile';

require __DIR__ . '/includes/header.php';
?>

<div class="row g-3">

    <div class="col-12">
        <div class="profile-header d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="profile-avatar-lg">
                    <?= e(strtoupper(substr($lead['company_name'] ?? '?', 0, 1))) ?>
                </div>
                <div>
                    <h3 class="mb-1"><?= e($lead['company_name']) ?></h3>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-geo-alt"></i> <?= e($lead['country']) ?: 'Unknown location' ?>
                        </span>
                        <span class="badge bg-<?= e(statusColor($lead['status'] ?? 'New')) ?>">
                            <?= e($lead['status'] ?? 'New') ?>
                        </span>
                        <span class="badge bg-<?= e(priorityColor($lead['priority'] ?? 'Medium')) ?>">
                            <?= e($lead['priority'] ?? 'Medium') ?> Priority
                        </span>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <?php if (!empty($lead['website'])): ?>
                    <a href="<?= e($lead['website']) ?>" target="_blank" rel="noopener" class="btn btn-light btn-sm">
                        <i class="bi bi-globe"></i> Visit Website
                    </a>
                <?php endif; ?>
                <?php if (!empty($lead['email'])): ?>
                    <a href="mailto:<?= e($lead['email']) ?>" class="btn btn-light btn-sm">
                        <i class="bi bi-envelope"></i> Send Email
                    </a>
                <?php endif; ?>
                <a href="<?= e(BASE_URL) ?>/lead-edit.php?id=<?= urlencode($lead['id']) ?>" class="btn btn-light btn-sm">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <!-- ================= Contact Information ================= -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-person-lines-fill me-1"></i> Contact Information</div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Contact Person</span>
                    <span class="info-value"><?= e($lead['contact_person']) ?: '—' ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Designation</span>
                    <span class="info-value"><?= e($lead['designation']) ?: '—' ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value">
                        <?php if (!empty($lead['email'])): ?>
                            <a href="mailto:<?= e($lead['email']) ?>"><?= e($lead['email']) ?></a>
                        <?php else: ?>—<?php endif; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone</span>
                    <span class="info-value">
                        <?php if (!empty($lead['phone'])): ?>
                            <a href="tel:<?= e($lead['phone']) ?>" class="text-reset text-decoration-none"><?= e($lead['phone']) ?></a>
                        <?php else: ?>—<?php endif; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">WhatsApp</span>
                    <span class="info-value">
                        <?php if (!empty($lead['whatsapp'])): ?>
                            <a href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $lead['whatsapp'])) ?>" target="_blank" rel="noopener">
                                <?= e($lead['whatsapp']) ?>
                            </a>
                        <?php else: ?>—<?php endif; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">LinkedIn</span>
                    <span class="info-value">
                        <?php if (!empty($lead['linkedin'])): ?>
                            <a href="<?= e($lead['linkedin']) ?>" target="_blank" rel="noopener">View Profile</a>
                        <?php else: ?>—<?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= Business Information ================= -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-briefcase me-1"></i> Business Information</div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Website</span>
                    <span class="info-value">
                        <?php if (!empty($lead['website'])): ?>
                            <a href="<?= e($lead['website']) ?>" target="_blank" rel="noopener"><?= e($lead['website']) ?></a>
                        <?php else: ?>—<?php endif; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Country</span>
                    <span class="info-value"><?= e($lead['country']) ?: '—' ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Current Software</span>
                    <span class="info-value"><?= e($lead['current_software']) ?: '—' ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Interested Service</span>
                    <span class="info-value"><?= e($lead['interested_service']) ?: '—' ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Next Follow-up</span>
                    <span class="info-value"><?= e(formatDate($lead['next_followup'] ?? '')) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Lead Added</span>
                    <span class="info-value"><?= e(formatDate($lead['created_at'] ?? '', 'M d, Y \a\t g:i A')) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Last Updated</span>
                    <span class="info-value"><?= e(formatDate($lead['updated_at'] ?? '', 'M d, Y \a\t g:i A')) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= Notes ================= -->
    <div class="col-12">
        <div class="card">
            <div class="card-header"><i class="bi bi-journal-text me-1"></i> Notes</div>
            <div class="card-body">
                <?php if (!empty($lead['notes'])): ?>
                    <p class="mb-0" style="white-space: pre-wrap;"><?= e($lead['notes']) ?></p>
                <?php else: ?>
                    <p class="text-muted-sm mb-0">No notes added for this lead.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- ================= Delete Confirmation Modal ================= -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= e(BASE_URL) ?>/lead-delete.php">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Delete Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        Are you sure you want to delete <strong><?= e($lead['company_name']) ?></strong>?
                        This action cannot be undone.
                    </p>
                    <input type="hidden" name="id" value="<?= e($lead['id']) ?>">
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

<?php require __DIR__ . '/includes/footer.php'; ?>
