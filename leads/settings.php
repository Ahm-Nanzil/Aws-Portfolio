<?php
/**
 * settings.php
 * Simple settings page: update app settings, export JSON, backup JSON, restore backup.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

// ------------------------------------------------------------------
// Handle "Export JSON" — stream leads.json as a download (before any HTML output)
// ------------------------------------------------------------------
if (isset($_GET['export']) && $_GET['export'] === 'leads') {
    $leads = getAllLeads();
    $json  = json_encode($leads, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="leads_export_' . date('Y-m-d_His') . '.json"');
    header('Content-Length: ' . strlen($json));
    echo $json;
    exit;
}

// ------------------------------------------------------------------
// Handle "Download Backup" — stream a specific backup file (before any HTML output)
// ------------------------------------------------------------------
if (isset($_GET['download_backup'])) {
    $filename = basename(sanitizeText($_GET['download_backup']));
    $path = BACKUPS_PATH . '/' . $filename;

    if (str_starts_with($filename, 'leads_backup_') && file_exists($path)) {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    setFlash('danger', 'Backup file not found.');
    redirect('settings.php');
}

// ------------------------------------------------------------------
// Handle POST actions
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitizeText($_POST['action'] ?? '');

    switch ($action) {

        case 'update_settings':
            $settings = [
                'app_name'     => sanitizeText($_POST['app_name'] ?? APP_NAME),
                'company_name' => sanitizeText($_POST['company_name'] ?? ''),
                'currency'     => sanitizeText($_POST['currency'] ?? 'USD'),
                'date_format'  => sanitizeText($_POST['date_format'] ?? 'M d, Y'),
                'created_at'   => getSettings()['created_at'] ?? date('Y-m-d H:i:s'),
            ];

            if ($settings['app_name'] === '') {
                setFlash('danger', 'App name cannot be empty.');
            } else {
                saveSettings($settings);
                setFlash('success', 'Settings updated successfully.');
            }
            redirect('settings.php');
            break;

        case 'backup':
            $filename = createLeadsBackup();
            if ($filename) {
                setFlash('success', 'Backup created: ' . $filename);
            } else {
                setFlash('danger', 'Failed to create backup.');
            }
            redirect('settings.php');
            break;

        case 'restore':
            $filename = sanitizeText($_POST['backup_file'] ?? '');
            if ($filename !== '' && restoreLeadsBackup($filename)) {
                setFlash('success', 'Leads restored from backup: ' . $filename);
            } else {
                setFlash('danger', 'Failed to restore backup.');
            }
            redirect('settings.php');
            break;

        case 'delete_backup':
            $filename = sanitizeText($_POST['backup_file'] ?? '');
            if ($filename !== '' && deleteLeadsBackup($filename)) {
                setFlash('success', 'Backup deleted: ' . $filename);
            } else {
                setFlash('danger', 'Failed to delete backup.');
            }
            redirect('settings.php');
            break;

        case 'restore_upload':
            if (!empty($_FILES['restore_file']['tmp_name']) && $_FILES['restore_file']['error'] === UPLOAD_ERR_OK) {
                $tmpPath = $_FILES['restore_file']['tmp_name'];
                $content = file_get_contents($tmpPath);
                $decoded = json_decode((string) $content, true);

                if (is_array($decoded)) {
                    // Backup current data first, then overwrite
                    createLeadsBackup();
                    saveAllLeads($decoded);
                    setFlash('success', 'Leads restored successfully from uploaded file.');
                } else {
                    setFlash('danger', 'Uploaded file is not valid JSON.');
                }
            } else {
                setFlash('danger', 'Please choose a valid JSON file to upload.');
            }
            redirect('settings.php');
            break;

        default:
            setFlash('danger', 'Unknown action.');
            redirect('settings.php');
    }
}

// ------------------------------------------------------------------
// Render page
// ------------------------------------------------------------------
$settings = getSettings();
$backups  = listLeadsBackups();
$leadsCount = count(getAllLeads());

$pageTitle    = 'Settings';
$pageSubtitle = 'Manage app preferences and data backups';

require __DIR__ . '/includes/header.php';
?>

<div class="row g-3">

    <!-- ================= General Settings ================= -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-sliders me-1"></i> General Settings</div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="action" value="update_settings">

                    <div class="mb-3">
                        <label class="form-label">App Name</label>
                        <input type="text" name="app_name" class="form-control" value="<?= e($settings['app_name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Your Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="<?= e($settings['company_name']) ?>"
                               placeholder="e.g. Acme Consulting">
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Currency</label>
                            <select name="currency" class="form-select">
                                <?php foreach (['USD', 'EUR', 'GBP', 'BDT', 'INR', 'AUD', 'CAD'] as $cur): ?>
                                    <option value="<?= e($cur) ?>" <?= $settings['currency'] === $cur ? 'selected' : '' ?>><?= e($cur) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Date Format</label>
                            <select name="date_format" class="form-select">
                                <?php
                                $formats = ['M d, Y' => 'Aug 08, 2026', 'd/m/Y' => '08/08/2026', 'm/d/Y' => '08/08/2026', 'Y-m-d' => '2026-08-08'];
                                foreach ($formats as $fmt => $example): ?>
                                    <option value="<?= e($fmt) ?>" <?= $settings['date_format'] === $fmt ? 'selected' : '' ?>>
                                        <?= e($fmt) ?> (<?= e($example) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ================= Data Management ================= -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-database me-1"></i> Data Management</div>
            <div class="card-body">

                <div class="d-flex align-items-center justify-content-between p-3 mb-3 rounded-3" style="background:var(--brand-light);">
                    <div>
                        <div class="fw-semibold">Export Leads</div>
                        <div class="text-muted-sm">Download all <?= (int) $leadsCount ?> leads as a JSON file.</div>
                    </div>
                    <a href="<?= e(BASE_URL) ?>/settings.php?export=leads" class="btn btn-primary btn-sm">
                        <i class="bi bi-download"></i> Export JSON
                    </a>
                </div>

                <div class="d-flex align-items-center justify-content-between p-3 mb-3 rounded-3 border">
                    <div>
                        <div class="fw-semibold">Create Backup</div>
                        <div class="text-muted-sm">Save a timestamped snapshot of your leads data.</div>
                    </div>
                    <form method="post">
                        <input type="hidden" name="action" value="backup">
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-shield-check"></i> Backup Now
                        </button>
                    </form>
                </div>

                <div class="p-3 rounded-3 border">
                    <div class="fw-semibold mb-1">Restore from Uploaded File</div>
                    <div class="text-muted-sm mb-2">Upload a previously exported/backed-up JSON file to restore leads. Your current data will be auto-backed-up first.</div>
                    <form method="post" enctype="multipart/form-data" class="d-flex gap-2">
                        <input type="hidden" name="action" value="restore_upload">
                        <input type="file" name="restore_file" accept="application/json,.json" class="form-control form-control-sm" required>
                        <button type="submit" class="btn btn-outline-warning btn-sm text-nowrap"
                                onclick="return confirm('This will replace your current leads data. Continue?');">
                            <i class="bi bi-upload"></i> Restore
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- ================= Backup History ================= -->
    <div class="col-12">
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-1"></i> Backup History</div>
            <div class="card-body p-0">
                <?php if (empty($backups)): ?>
                    <div class="empty-state">
                        <i class="bi bi-archive"></i>
                        No backups created yet. Click "Backup Now" above to create one.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>Filename</th>
                                    <th>Date Created</th>
                                    <th>Size</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($backups as $backup): ?>
                                    <tr>
                                        <td><i class="bi bi-file-earmark-zip me-1 text-muted"></i><?= e($backup['filename']) ?></td>
                                        <td class="text-muted-sm"><?= e(formatDate($backup['date'], 'M d, Y \a\t g:i A')) ?></td>
                                        <td class="text-muted-sm"><?= e(formatBytes($backup['size'])) ?></td>
                                        <td class="text-end pe-3">
                                            <div class="d-flex justify-content-end gap-1">
                                                <form method="post" onsubmit="return confirm('Restore leads from this backup? Current data will be replaced.');">
                                                    <input type="hidden" name="action" value="restore">
                                                    <input type="hidden" name="backup_file" value="<?= e($backup['filename']) ?>">
                                                    <button type="submit" class="btn btn-icon btn-outline-primary" title="Restore this backup">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                </form>
                                                <a href="<?= e(BASE_URL) ?>/settings.php?download_backup=<?= urlencode($backup['filename']) ?>"
                                                   class="btn btn-icon btn-outline-secondary" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <form method="post" onsubmit="return confirm('Delete this backup permanently?');">
                                                    <input type="hidden" name="action" value="delete_backup">
                                                    <input type="hidden" name="backup_file" value="<?= e($backup['filename']) ?>">
                                                    <button type="submit" class="btn btn-icon btn-outline-danger" title="Delete backup">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
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
    </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
