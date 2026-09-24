<?php
/**
 * Auth: sessions, login/registration, roles (admin / student), and
 * admin "view as" impersonation.
 */

require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------------------
// Basic session state
// ---------------------------------------------------------------------

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

/** The actually-logged-in user (never the impersonated one). */
function current_user(): ?array {
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    return [
        'id' => (int)$_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'student',
    ];
}

function is_admin(): bool {
    $u = current_user();
    return $u !== null && $u['role'] === 'admin';
}

function require_login(): void {
    if (!is_logged_in()) {
        redirect(base_path() . '/login.php');
    }
    // A disabled account is logged out immediately if it's caught mid-session.
    $row = db_find_user_by_id((int)$_SESSION['user_id']);
    if ($row === null || $row['status'] !== 'active') {
        logout_user();
        flash('danger', 'Your account is no longer active. Please contact the administrator.');
        redirect(base_path() . '/login.php');
    }
}

function require_admin(): void {
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        die('Forbidden: this page is for administrators only.');
    }
}

// ---------------------------------------------------------------------
// Impersonation ("View as") — lets an admin operate the normal app
// screens scoped to a specific student's data, for support purposes.
// ---------------------------------------------------------------------

function is_impersonating(): bool {
    return is_admin() && !empty($_SESSION['impersonate_user_id']);
}

function impersonated_user(): ?array {
    if (!is_impersonating()) return null;
    return db_find_user_by_id((int)$_SESSION['impersonate_user_id']);
}

/** The user ID whose data the current page/action should read & write. */
function effective_user_id(): int {
    if (is_impersonating()) {
        return (int)$_SESSION['impersonate_user_id'];
    }
    return (int)($_SESSION['user_id'] ?? 0);
}

function start_impersonation(int $userId): void {
    $_SESSION['impersonate_user_id'] = $userId;
}

function stop_impersonation(): void {
    unset($_SESSION['impersonate_user_id']);
}

// ---------------------------------------------------------------------
// Login / logout / registration
// ---------------------------------------------------------------------

function login_user(array $row): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$row['id'];
    $_SESSION['user_name'] = $row['name'];
    $_SESSION['user_email'] = $row['email'];
    $_SESSION['user_role'] = $row['role'];
}

function logout_user(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

/**
 * @return array{0: bool, 1: string} [success, message]
 */
function attempt_login(string $email, string $password): array {
    $row = db_find_user_by_email($email);
    if ($row === null || !password_verify($password, $row['password_hash'])) {
        return [false, 'Incorrect email or password.'];
    }
    if ($row['status'] !== 'active') {
        return [false, 'This account has been disabled. Please contact the administrator.'];
    }
    login_user($row);
    return [true, 'Welcome back, ' . $row['name'] . '!'];
}

/**
 * Self-service registration always creates a "student" account.
 * @return array{0: bool, 1: string}
 */
function register_student(string $name, string $email, string $password, string $passwordConfirm): array {
    $name = trim($name);
    $email = trim(strtolower($email));

    if ($name === '' || $email === '' || $password === '') {
        return [false, 'Please fill in all fields.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [false, 'Please enter a valid email address.'];
    }
    if (strlen($password) < 8) {
        return [false, 'Password must be at least 8 characters long.'];
    }
    if ($password !== $passwordConfirm) {
        return [false, 'Passwords do not match.'];
    }
    if (db_find_user_by_email($email) !== null) {
        return [false, 'An account with that email already exists. Try logging in instead.'];
    }

    $id = db_create_user($name, $email, password_hash($password, PASSWORD_DEFAULT), 'student');
    $row = db_find_user_by_id($id);
    login_user($row);
    return [true, 'Account created. Welcome, ' . $name . '!'];
}
