<?php
/**
 * lead-delete.php
 * Handles lead deletion.
 * - POST: deletes the lead (called from the confirmation modal on leads.php / lead-view.php)
 * - GET: shows a standalone confirmation page (fallback for direct access / no JS)
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

// ------------------------------------------------------------------
// Handle POST — actually perform the deletion
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = sanitizeText($_POST['id'] ?? '');
    $redirect = sanitizeText($_POST['redirect'] ?? 'leads.php');

    // Only allow redirecting back to known safe pages
    $allowedRedirects = ['leads.php', 'dashboard.php'];
    if (!in_array($redirect, $allowedRedirects, true)) {
        $redirect = 'leads.php';
    }

    $lead = $id !== '' ? findLeadById($id) : null;

    if ($lead && deleteLead($id)) {
        setFlash('success', 'Lead "' . $lead['company_name'] . '" was deleted successfully.');
    } else {
        setFlash('danger', 'Lead not found or could not be deleted.');
    }

    redirect($redirect);
}

// ------------------------------------------------------------------
// Handle GET — show a standalone confirmation page
// ------------------------------------------------------------------
$id = sanitizeText($_GET['id'] ?? '');
$lead = $id !== '' ? findLeadById($id) : null;

if (!$lead) {
    setFlash('danger', 'Lead not found.');
    redirect('leads.php');
}

$pageTitle    = 'Delete Lead';
$pageSubtitle = 'Confirm deletion';

require __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">
        <div class="card text-center">
            <div class="card-body py-5">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 text-danger"
                          style="width:64px;height:64px;font-size:1.75rem;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </span>
                </div>
                <h5 class="mb-2">Delete this lead?</h5>
                <p class="text-muted-sm mb-4">
                    You are about to permanently delete
                    <strong class="text-dark"><?= e($lead['company_name']) ?></strong>.
                    This action cannot be undone.
                </p>

                <form method="post" class="d-flex justify-content-center gap-2">
                    <input type="hidden" name="id" value="<?= e($lead['id']) ?>">
                    <input type="hidden" name="redirect" value="leads.php">
                    <a href="<?= e(BASE_URL) ?>/lead-view.php?id=<?= urlencode($lead['id']) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Yes, Delete Lead
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
