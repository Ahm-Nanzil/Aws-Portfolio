<?php
/**
 * Framework-free helper functions: HTML escaping, redirects, CSRF,
 * flash messages, badge color mapping, date formatting. No database
 * or session logic lives here so it can be safely required by
 * anything else.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function h($str): string {
    return htmlspecialchars((string)($str ?? ''), ENT_QUOTES, 'UTF-8');
}

function is_valid_url(string $url): bool {
    if ($url === '') return true; // empty is allowed
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

function flash(string $type, string $message): void {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array {
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

/**
 * Absolute path (from the web root) to the application's own root
 * directory, regardless of whether the current script lives in the
 * top-level folder or in /actions or /admin. Used so redirects work
 * correctly no matter where the app is deployed (root, subfolder, etc).
 */
function base_path(): string {
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    if (preg_match('#/(actions|admin)/[^/]+$#', $script)) {
        $script = preg_replace('#/(actions|admin)/[^/]+$#', '', $script);
    } else {
        $script = str_replace('\\', '/', dirname($script));
    }
    return rtrim($script, '/');
}

/**
 * Validates that a redirect target is a same-app relative path (starts
 * with a single "/" and not "//" or a scheme), to avoid open-redirect
 * issues. Falls back to $fallback.
 */
function safe_redirect_target(?string $target, string $fallback): string {
    if (!$target) return $fallback;
    if (preg_match('#^https?://#i', $target)) return $fallback;
    if (str_starts_with($target, '//')) return $fallback;
    if (!str_starts_with($target, '/')) return $fallback;
    return $target;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(400);
        die('Invalid or expired form submission (CSRF check failed). Please go back and try again.');
    }
}

function fmt_date($date): string {
    if (empty($date)) return '';
    $ts = strtotime($date);
    return $ts ? date('d M Y', $ts) : h($date);
}

function days_until($date): ?int {
    if (empty($date)) return null;
    $ts = strtotime($date);
    if (!$ts) return null;
    $today = strtotime(date('Y-m-d'));
    return (int) floor(($ts - $today) / 86400);
}

function generate_sub_id(string $prefix = 'id'): string {
    return $prefix . '_' . bin2hex(random_bytes(5));
}

// ---------------------------------------------------------------------
// Badge color helpers (shared by every page that renders a status pill)
// ---------------------------------------------------------------------

function status_badge_class(string $status): string {
    $map = [
        'Not Started' => 'secondary',
        'Researching' => 'info',
        'Completed' => 'primary',
        'Shortlisted' => 'warning',
        'Applied' => 'primary',
        'Offer Received' => 'success',
        'Rejected' => 'danger',
        'Ready to Apply' => 'warning',
    ];
    return $map[$status] ?? 'secondary';
}

function priority_badge_class(string $priority): string {
    $map = ['High' => 'danger', 'Medium' => 'warning', 'Low' => 'secondary'];
    return $map[$priority] ?? 'secondary';
}

function eligibility_badge_class(string $e): string {
    $map = ['Eligible' => 'success', 'Maybe' => 'warning', 'Not Eligible' => 'danger', 'Unknown' => 'secondary'];
    return $map[$e] ?? 'secondary';
}

function doc_status_badge_class(string $s): string {
    $map = [
        'Required' => 'primary', 'Optional' => 'info', 'Not Required' => 'secondary',
        'Unknown' => 'secondary', 'Ready' => 'success', 'Missing' => 'danger',
    ];
    return $map[$s] ?? 'secondary';
}

function user_status_badge_class(string $s): string {
    return $s === 'active' ? 'success' : 'secondary';
}

function role_badge_class(string $r): string {
    return $r === 'admin' ? 'dark' : 'info';
}
