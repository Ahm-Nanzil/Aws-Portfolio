<?php
/**
 * functions.php
 * Reusable helper functions: JSON storage, sanitization, escaping,
 * lead CRUD helpers, flash messages, formatting helpers.
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

// ----------------------------------------------------------------------
// Generic JSON storage helpers
// ----------------------------------------------------------------------

/**
 * Read a JSON file and decode it into an associative array.
 * Returns $default if the file does not exist or is invalid.
 */
function readJsonFile(string $path, array $default = []): array
{
    if (!file_exists($path)) {
        return $default;
    }

    $handle = fopen($path, 'r');
    if ($handle === false) {
        return $default;
    }

    $data = '';
    if (flock($handle, LOCK_SH)) {
        $data = stream_get_contents($handle);
        flock($handle, LOCK_UN);
    }
    fclose($handle);

    if ($data === '' || $data === false) {
        return $default;
    }

    $decoded = json_decode($data, true);

    return is_array($decoded) ? $decoded : $default;
}

/**
 * Encode an array as JSON and write it to a file safely (with locking).
 */
function writeJsonFile(string $path, array $data): bool
{
    $dir = dirname($path);
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    $handle = fopen($path, 'c');
    if ($handle === false) {
        return false;
    }

    $result = false;
    if (flock($handle, LOCK_EX)) {
        ftruncate($handle, 0);
        rewind($handle);
        $result = fwrite($handle, $json) !== false;
        fflush($handle);
        flock($handle, LOCK_UN);
    }
    fclose($handle);

    return $result;
}

// ----------------------------------------------------------------------
// Sanitization / escaping helpers
// ----------------------------------------------------------------------

/**
 * Trim and strip tags from a raw input string. Returns '' for null.
 */
function sanitizeText(?string $value): string
{
    if ($value === null) {
        return '';
    }
    return trim(strip_tags($value));
}

/**
 * Escape a string for safe HTML output.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Validate an email address. Returns '' if invalid.
 */
function sanitizeEmail(?string $value): string
{
    $value = trim((string) $value);
    $filtered = filter_var($value, FILTER_VALIDATE_EMAIL);
    return $filtered !== false ? $filtered : '';
}

/**
 * Validate a URL. Returns '' if invalid. Auto-prepends https:// if missing scheme.
 */
function sanitizeUrl(?string $value): string
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }
    if (!preg_match('#^https?://#i', $value)) {
        $value = 'https://' . $value;
    }
    $filtered = filter_var($value, FILTER_VALIDATE_URL);
    return $filtered !== false ? $filtered : '';
}

// ----------------------------------------------------------------------
// Flash messages (session based)
// ----------------------------------------------------------------------

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ----------------------------------------------------------------------
// Lead helpers
// ----------------------------------------------------------------------

/**
 * Get all leads from storage.
 */
function getAllLeads(): array
{
    return readJsonFile(LEADS_FILE, []);
}

/**
 * Save the full leads array back to storage.
 */
function saveAllLeads(array $leads): bool
{
    return writeJsonFile(LEADS_FILE, $leads);
}

/**
 * Find a single lead by ID. Returns null if not found.
 */
function findLeadById(string $id): ?array
{
    $leads = getAllLeads();
    foreach ($leads as $lead) {
        if (($lead['id'] ?? '') === $id) {
            return $lead;
        }
    }
    return null;
}

/**
 * Generate a unique lead ID.
 */
function generateLeadId(): string
{
    return uniqid('lead_', true);
}

/**
 * Insert a new lead. Returns the created lead array.
 */
function createLead(array $data): array
{
    $leads = getAllLeads();

    $now = date('Y-m-d H:i:s');
    $lead = [
        'id'                 => generateLeadId(),
        'company_name'       => $data['company_name'],
        'website'            => $data['website'],
        'country'            => $data['country'],
        'contact_person'     => $data['contact_person'],
        'designation'        => $data['designation'],
        'email'              => $data['email'],
        'phone'              => $data['phone'],
        'whatsapp'           => $data['whatsapp'],
        'linkedin'           => $data['linkedin'],
        'current_software'   => $data['current_software'],
        'interested_service' => $data['interested_service'],
        'status'             => $data['status'],
        'priority'           => $data['priority'],
        'next_followup'      => $data['next_followup'],
        'notes'              => $data['notes'],
        'created_at'         => $now,
        'updated_at'         => $now,
    ];

    $leads[] = $lead;
    saveAllLeads($leads);

    return $lead;
}

/**
 * Update an existing lead by ID. Returns true on success, false if not found.
 */
function updateLead(string $id, array $data): bool
{
    $leads = getAllLeads();
    $found = false;

    foreach ($leads as &$lead) {
        if (($lead['id'] ?? '') === $id) {
            $lead['company_name']       = $data['company_name'];
            $lead['website']            = $data['website'];
            $lead['country']            = $data['country'];
            $lead['contact_person']     = $data['contact_person'];
            $lead['designation']        = $data['designation'];
            $lead['email']              = $data['email'];
            $lead['phone']              = $data['phone'];
            $lead['whatsapp']           = $data['whatsapp'];
            $lead['linkedin']           = $data['linkedin'];
            $lead['current_software']   = $data['current_software'];
            $lead['interested_service'] = $data['interested_service'];
            $lead['status']             = $data['status'];
            $lead['priority']           = $data['priority'];
            $lead['next_followup']      = $data['next_followup'];
            $lead['notes']              = $data['notes'];
            $lead['updated_at']         = date('Y-m-d H:i:s');
            $found = true;
            break;
        }
    }
    unset($lead);

    if ($found) {
        saveAllLeads($leads);
    }

    return $found;
}

/**
 * Delete a lead by ID. Returns true on success, false if not found.
 */
function deleteLead(string $id): bool
{
    $leads = getAllLeads();
    $newLeads = array_values(array_filter($leads, fn($lead) => ($lead['id'] ?? '') !== $id));

    if (count($newLeads) === count($leads)) {
        return false; // nothing removed
    }

    return saveAllLeads($newLeads);
}

/**
 * Count leads grouped by status.
 */
function getLeadCounts(array $leads): array
{
    $counts = [
        'total'      => count($leads),
        'New'        => 0,
        'Contacted'  => 0,
        'Interested' => 0,
        'Won'        => 0,
        'Lost'       => 0,
    ];

    foreach ($leads as $lead) {
        $status = $lead['status'] ?? 'New';
        if (isset($counts[$status])) {
            $counts[$status]++;
        }
    }

    return $counts;
}

/**
 * Get leads with an upcoming follow-up date (today or future), sorted ascending.
 */
function getUpcomingFollowups(array $leads, int $limit = 5): array
{
    $today = date('Y-m-d');

    $upcoming = array_filter($leads, function ($lead) use ($today) {
        $date = $lead['next_followup'] ?? '';
        return $date !== '' && $date >= $today;
    });

    usort($upcoming, fn($a, $b) => strcmp($a['next_followup'], $b['next_followup']));

    return array_slice($upcoming, 0, $limit);
}

/**
 * Get most recently created leads, sorted descending.
 */
function getRecentLeads(array $leads, int $limit = 5): array
{
    $sorted = $leads;
    usort($sorted, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));

    return array_slice($sorted, 0, $limit);
}

/**
 * Return the Bootstrap color class for a given status.
 */
function statusColor(string $status): string
{
    return LEAD_STATUSES[$status] ?? 'secondary';
}

/**
 * Return the Bootstrap color class for a given priority.
 */
function priorityColor(string $priority): string
{
    return LEAD_PRIORITIES[$priority] ?? 'secondary';
}

/**
 * Format a date string (Y-m-d) into a friendlier display format.
 */
function formatDate(?string $date, string $format = 'M d, Y'): string
{
    if (empty($date)) {
        return '—';
    }
    $timestamp = strtotime($date);
    return $timestamp !== false ? date($format, $timestamp) : '—';
}

/**
 * Redirect helper.
 */
function redirect(string $path): void
{
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

// ----------------------------------------------------------------------
// Settings helpers
// ----------------------------------------------------------------------

function getSettings(): array
{
    $defaults = [
        'app_name'     => APP_NAME,
        'company_name' => '',
        'currency'     => 'USD',
        'date_format'  => 'M d, Y',
        'created_at'   => date('Y-m-d H:i:s'),
    ];
    return array_merge($defaults, readJsonFile(SETTINGS_FILE, []));
}

function saveSettings(array $settings): bool
{
    return writeJsonFile(SETTINGS_FILE, $settings);
}

// ----------------------------------------------------------------------
// Backup / Restore helpers
// ----------------------------------------------------------------------

/**
 * Create a timestamped backup copy of leads.json inside storage/backups/.
 * Returns the backup filename on success, or null on failure.
 */
function createLeadsBackup(): ?string
{
    if (!file_exists(LEADS_FILE)) {
        return null;
    }

    if (!is_dir(BACKUPS_PATH)) {
        @mkdir(BACKUPS_PATH, 0775, true);
    }

    $filename = 'leads_backup_' . date('Y-m-d_His') . '.json';
    $destination = BACKUPS_PATH . '/' . $filename;

    return copy(LEADS_FILE, $destination) ? $filename : null;
}

/**
 * List all backup files, most recent first.
 * Each entry: ['filename' => ..., 'size' => ..., 'date' => ...]
 */
function listLeadsBackups(): array
{
    if (!is_dir(BACKUPS_PATH)) {
        return [];
    }

    $files = glob(BACKUPS_PATH . '/leads_backup_*.json') ?: [];

    $backups = array_map(function ($path) {
        return [
            'filename' => basename($path),
            'size'     => filesize($path) ?: 0,
            'date'     => date('Y-m-d H:i:s', filemtime($path) ?: time()),
        ];
    }, $files);

    usort($backups, fn($a, $b) => strcmp($b['date'], $a['date']));

    return $backups;
}

/**
 * Restore leads.json from a given backup filename (must exist inside storage/backups/).
 * Returns true on success.
 */
function restoreLeadsBackup(string $filename): bool
{
    $filename = basename($filename); // prevent path traversal
    $path = BACKUPS_PATH . '/' . $filename;

    if (!file_exists($path) || !str_starts_with($filename, 'leads_backup_')) {
        return false;
    }

    $data = readJsonFile($path, []);
    if (!is_array($data)) {
        return false;
    }

    return saveAllLeads($data);
}

/**
 * Delete a backup file by filename.
 */
function deleteLeadsBackup(string $filename): bool
{
    $filename = basename($filename); // prevent path traversal
    $path = BACKUPS_PATH . '/' . $filename;

    if (!file_exists($path) || !str_starts_with($filename, 'leads_backup_')) {
        return false;
    }

    return unlink($path);
}

/**
 * Format a byte count into a human-readable string.
 */
function formatBytes(int $bytes): string
{
    if ($bytes < 1024) {
        return $bytes . ' B';
    }
    if ($bytes < 1024 * 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }
    return round($bytes / (1024 * 1024), 2) . ' MB';
}
