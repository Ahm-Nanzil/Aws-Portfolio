<?php
/**
 * lead-add.php
 * Create a new lead. Validates and sanitizes all input before saving.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$errors = [];

// Default form values (used for re-populating on validation error)
$old = [
    'company_name'       => '',
    'website'            => '',
    'country'            => '',
    'contact_person'     => '',
    'designation'        => '',
    'email'              => '',
    'phone'              => '',
    'whatsapp'           => '',
    'linkedin'           => '',
    'current_software'   => '',
    'interested_service' => '',
    'status'             => 'New',
    'priority'           => 'Medium',
    'next_followup'      => '',
    'notes'              => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ------------------------------------------------------------
    // Collect + sanitize
    // ------------------------------------------------------------
    $old['company_name']       = sanitizeText($_POST['company_name'] ?? '');
    $old['website']            = sanitizeText($_POST['website'] ?? '');
    $old['country']            = sanitizeText($_POST['country'] ?? '');
    $old['contact_person']     = sanitizeText($_POST['contact_person'] ?? '');
    $old['designation']        = sanitizeText($_POST['designation'] ?? '');
    $old['email']              = sanitizeText($_POST['email'] ?? '');
    $old['phone']              = sanitizeText($_POST['phone'] ?? '');
    $old['whatsapp']           = sanitizeText($_POST['whatsapp'] ?? '');
    $old['linkedin']           = sanitizeText($_POST['linkedin'] ?? '');
    $old['current_software']   = sanitizeText($_POST['current_software'] ?? '');
    $old['interested_service'] = sanitizeText($_POST['interested_service'] ?? '');
    $old['status']             = sanitizeText($_POST['status'] ?? 'New');
    $old['priority']           = sanitizeText($_POST['priority'] ?? 'Medium');
    $old['next_followup']      = sanitizeText($_POST['next_followup'] ?? '');
    $old['notes']              = sanitizeText($_POST['notes'] ?? '');

    // ------------------------------------------------------------
    // Validate
    // ------------------------------------------------------------
    if ($old['company_name'] === '') {
        $errors['company_name'] = 'Company name is required.';
    }

    if ($old['email'] !== '' && sanitizeEmail($old['email']) === '') {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($old['website'] !== '' && sanitizeUrl($old['website']) === '') {
        $errors['website'] = 'Please enter a valid website URL.';
    }

    if ($old['linkedin'] !== '' && sanitizeUrl($old['linkedin']) === '') {
        $errors['linkedin'] = 'Please enter a valid LinkedIn URL.';
    }

    if (!array_key_exists($old['status'], LEAD_STATUSES)) {
        $errors['status'] = 'Please select a valid status.';
    }

    if (!array_key_exists($old['priority'], LEAD_PRIORITIES)) {
        $errors['priority'] = 'Please select a valid priority.';
    }

    if ($old['next_followup'] !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $old['next_followup']);
        if (!$d || $d->format('Y-m-d') !== $old['next_followup']) {
            $errors['next_followup'] = 'Please enter a valid date.';
        }
    }

    // ------------------------------------------------------------
    // Save if valid
    // ------------------------------------------------------------
    if (empty($errors)) {
        $data = $old;
        $data['website']  = sanitizeUrl($old['website']);
        $data['linkedin'] = sanitizeUrl($old['linkedin']);
        $data['email']    = sanitizeEmail($old['email']);

        $lead = createLead($data);

        setFlash('success', 'Lead "' . $data['company_name'] . '" was created successfully.');
        redirect('lead-view.php?id=' . urlencode($lead['id']));
    }
}

$pageTitle    = 'Add Lead';
$pageSubtitle = 'Create a new lead record';

require __DIR__ . '/includes/header.php';
?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger d-flex align-items-start gap-2">
        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
        <div>
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-1">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<form method="post" novalidate>
    <div class="row g-3">

        <!-- ================= Company Info ================= -->
        <div class="col-12">
            <div class="section-card">
                <h6 class="mb-3"><i class="bi bi-building me-1"></i> Company Information</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name"
                               class="form-control <?= isset($errors['company_name']) ? 'is-invalid' : '' ?>"
                               value="<?= e($old['company_name']) ?>" required>
                        <?php if (isset($errors['company_name'])): ?>
                            <div class="invalid-feedback"><?= e($errors['company_name']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Website</label>
                        <input type="text" name="website"
                               class="form-control <?= isset($errors['website']) ? 'is-invalid' : '' ?>"
                               placeholder="https://example.com"
                               value="<?= e($old['website']) ?>">
                        <?php if (isset($errors['website'])): ?>
                            <div class="invalid-feedback"><?= e($errors['website']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" value="<?= e($old['country']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Current Software</label>
                        <input type="text" name="current_software" class="form-control"
                               placeholder="e.g. Excel, Salesforce, None"
                               value="<?= e($old['current_software']) ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= Contact Info ================= -->
        <div class="col-12">
            <div class="section-card">
                <h6 class="mb-3"><i class="bi bi-person-lines-fill me-1"></i> Contact Information</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control" value="<?= e($old['contact_person']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Designation</label>
                        <input type="text" name="designation" class="form-control" value="<?= e($old['designation']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email"
                               class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                               value="<?= e($old['email']) ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= e($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= e($old['phone']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" value="<?= e($old['whatsapp']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">LinkedIn</label>
                        <input type="text" name="linkedin"
                               class="form-control <?= isset($errors['linkedin']) ? 'is-invalid' : '' ?>"
                               placeholder="https://linkedin.com/in/username"
                               value="<?= e($old['linkedin']) ?>">
                        <?php if (isset($errors['linkedin'])): ?>
                            <div class="invalid-feedback"><?= e($errors['linkedin']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= Lead Details ================= -->
        <div class="col-12">
            <div class="section-card">
                <h6 class="mb-3"><i class="bi bi-clipboard-data me-1"></i> Lead Details</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Interested Service</label>
                        <input type="text" name="interested_service" class="form-control"
                               placeholder="e.g. Web Development, CRM"
                               value="<?= e($old['interested_service']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Next Follow-up Date</label>
                        <input type="date" name="next_followup"
                               class="form-control <?= isset($errors['next_followup']) ? 'is-invalid' : '' ?>"
                               value="<?= e($old['next_followup']) ?>">
                        <?php if (isset($errors['next_followup'])): ?>
                            <div class="invalid-feedback"><?= e($errors['next_followup']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <?php foreach (array_keys(LEAD_STATUSES) as $status): ?>
                                <option value="<?= e($status) ?>" <?= $old['status'] === $status ? 'selected' : '' ?>>
                                    <?= e($status) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <?php foreach (array_keys(LEAD_PRIORITIES) as $priority): ?>
                                <option value="<?= e($priority) ?>" <?= $old['priority'] === $priority ? 'selected' : '' ?>>
                                    <?= e($priority) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="4"
                                  placeholder="Any additional notes about this lead..."><?= e($old['notes']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= Actions ================= -->
        <div class="col-12 d-flex justify-content-end gap-2">
            <a href="<?= e(BASE_URL) ?>/leads.php" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> Save Lead
            </button>
        </div>

    </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
