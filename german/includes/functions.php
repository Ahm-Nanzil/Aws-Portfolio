<?php
/**
 * German University Research Manager — data access layer (MySQL/PDO).
 *
 * Every university/program function takes a $userId and enforces
 * ownership at the SQL level (WHERE user_id = ?), so one student can
 * never read or modify another student's records, even by guessing an
 * id in the URL.
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

// =======================================================================
// Default JSON blob shapes (used when creating a new program)
// =======================================================================

function default_document_names(): array {
    return [
        'Passport', "Bachelor's Certificate", "Bachelor's Transcript", 'MOI Certificate',
        'IELTS Certificate', 'CV', 'Motivation Letter', 'Recommendation Letter',
        'APS Certificate', 'Course Description', 'Thesis',
    ];
}

function default_documents(): array {
    $docs = [];
    foreach (default_document_names() as $name) {
        $docs[] = ['id' => generate_sub_id('doc'), 'name' => $name, 'status' => 'Unknown'];
    }
    return $docs;
}

function default_language(): array {
    return [
        'teachingLanguage' => '', 'englishTaught' => 'Unknown', 'englishPercentage' => '',
        'germanRequirement' => '', 'englishRequirement' => '', 'ieltsRequired' => 'Unknown',
        'ieltsMin' => '', 'toeflRequired' => 'Unknown', 'toeflMin' => '', 'otherTests' => '',
        'moiAccepted' => 'Unknown', 'moiDetails' => '',
    ];
}

function default_fees(): array {
    return ['tuitionFee' => '', 'semesterContribution' => '', 'applicationFee' => '', 'uniAssistFee' => '', 'otherFees' => '', 'feeNotes' => ''];
}

function default_application(): array {
    return [
        'method' => 'Direct', 'portal' => '', 'url' => '', 'startDate' => '', 'deadline' => '',
        'winterDeadline' => '', 'summerDeadline' => '', 'intlDeadline' => '', 'otherInfo' => '',
    ];
}

function default_admission(): array {
    return [
        'requiredDegree' => '', 'requiredMajor' => '', 'minGpa' => '', 'requiredEcts' => '',
        'csEcts' => '', 'mathEcts' => '', 'programmingEcts' => '', 'otherEcts' => '',
        'workExperience' => 'Unknown', 'greRequired' => 'Unknown', 'entranceExam' => 'Unknown',
        'interview' => 'Unknown', 'otherAcademic' => '', 'additionalReq' => '',
    ];
}

function default_personal(): array {
    return ['eligibility' => 'Unknown', 'priority' => 'Medium', 'applicationStatus' => 'Not Started', 'notes' => '', 'questions' => '', 'lastChecked' => ''];
}

// =======================================================================
// USERS
// =======================================================================

function db_find_user_by_id(int $id): ?array {
    $stmt = pdo()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function db_find_user_by_email(string $email): ?array {
    $stmt = pdo()->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([trim(strtolower($email))]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function db_create_user(string $name, string $email, string $passwordHash, string $role = 'student', bool $preVerified = true): int {
    $now = db_now();
    $verifiedAt = $preVerified ? $now : null;
    $stmt = pdo()->prepare('INSERT INTO users (name, email, password_hash, role, status, email_verified_at, created_at, updated_at) VALUES (?, ?, ?, ?, "active", ?, ?, ?)');
    $stmt->execute([$name, trim(strtolower($email)), $passwordHash, $role, $verifiedAt, $now, $now]);
    return (int)pdo()->lastInsertId();
}

function db_count_users(): int {
    return (int)pdo()->query('SELECT COUNT(*) FROM users')->fetchColumn();
}

function db_count_admins(): int {
    return (int)pdo()->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
}

/** All users with their university/program counts, for the admin panel. */
function db_all_users_with_stats(): array {
    $sql = 'SELECT u.*,
              (SELECT COUNT(*) FROM universities un WHERE un.user_id = u.id) AS uni_count,
              (SELECT COUNT(*) FROM programs p WHERE p.user_id = u.id) AS prog_count
            FROM users u
            ORDER BY u.created_at DESC';
    return pdo()->query($sql)->fetchAll();
}

function db_update_user_status(int $id, string $status): bool {
    $stmt = pdo()->prepare('UPDATE users SET status = ?, updated_at = ? WHERE id = ?');
    return $stmt->execute([$status, db_now(), $id]);
}

function db_delete_user(int $id): bool {
    // ON DELETE CASCADE takes care of that user's universities & programs.
    $stmt = pdo()->prepare('DELETE FROM users WHERE id = ?');
    return $stmt->execute([$id]);
}

function db_global_stats(): array {
    $pdo = pdo();
    return [
        'totalUsers' => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
        'totalStudents' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn(),
        'activeUsers' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn(),
        'disabledUsers' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'disabled'")->fetchColumn(),
        'totalUniversities' => (int)$pdo->query('SELECT COUNT(*) FROM universities')->fetchColumn(),
        'totalPrograms' => (int)$pdo->query('SELECT COUNT(*) FROM programs')->fetchColumn(),
    ];
}

// =======================================================================
// SETTINGS  (simple admin-toggleable key/value store)
// =======================================================================

function get_setting(string $name, ?string $default = null): ?string {
    $stmt = pdo()->prepare('SELECT value FROM settings WHERE name = ?');
    $stmt->execute([$name]);
    $value = $stmt->fetchColumn();
    return $value === false ? $default : $value;
}

function set_setting(string $name, string $value): void {
    $stmt = pdo()->prepare('INSERT INTO settings (name, value, updated_at) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = VALUES(updated_at)');
    $stmt->execute([$name, $value, db_now()]);
}

function is_email_verification_required(): bool {
    return get_setting('require_email_verification', '0') === '1';
}

// =======================================================================
// EMAIL VERIFICATION
// =======================================================================

function generate_verification_token(): string {
    return bin2hex(random_bytes(32));
}

/** Sets a fresh verification token (24h expiry) on a user and returns it. */
function set_user_verification_token(int $userId): string {
    $token = generate_verification_token();
    $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
    $stmt = pdo()->prepare('UPDATE users SET verification_token = ?, verification_token_expires = ?, verification_last_sent_at = ? WHERE id = ?');
    $stmt->execute([$token, $expires, db_now(), $userId]);
    return $token;
}

function db_find_user_by_verification_token(string $token): ?array {
    $stmt = pdo()->prepare('SELECT * FROM users WHERE verification_token = ?');
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function mark_user_verified(int $userId): void {
    $stmt = pdo()->prepare('UPDATE users SET email_verified_at = ?, verification_token = NULL, verification_token_expires = NULL WHERE id = ?');
    $stmt->execute([db_now(), $userId]);
}

function is_user_verified(array $user): bool {
    return !empty($user['email_verified_at']);
}

/** Seconds remaining before this user is allowed to request another verification email (throttle). Returns 0 if allowed now. */
function verification_resend_cooldown_remaining(array $user): int {
    if (empty($user['verification_last_sent_at'])) return 0;
    $cooldownSeconds = 60;
    $elapsed = time() - strtotime($user['verification_last_sent_at']);
    return max(0, $cooldownSeconds - $elapsed);
}

// =======================================================================
// UNIVERSITIES  (all scoped to a given $userId)
// =======================================================================

function get_universities(int $userId): array {
    $sql = 'SELECT u.*, (SELECT COUNT(*) FROM programs p WHERE p.university_id = u.id) AS program_count
            FROM universities u WHERE u.user_id = ? ORDER BY u.name';
    $stmt = pdo()->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function get_university(int $userId, int $id): ?array {
    $stmt = pdo()->prepare('SELECT * FROM universities WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function university_field_map(): array {
    return ['name', 'officialName', 'city', 'state', 'type', 'website', 'intlWebsite', 'applicationPortal', 'applicationMethod', 'applicationFee', 'tuitionFee', 'semesterContribution', 'generalNotes', 'status'];
}

function camel_to_snake(string $s): string {
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $s));
}
function create_university(int $userId, array $fields): int {
    $now = db_now();
    $cols = ['user_id', 'created_at', 'updated_at'];
    $vals = [$userId, $now, $now];
    foreach (university_field_map() as $key) {
        $cols[] = camel_to_snake($key);
        $vals[] = $fields[$key] ?? '';
    }
    $placeholders = implode(', ', array_fill(0, count($cols), '?'));
    $sql = 'INSERT INTO universities (' . implode(', ', $cols) . ') VALUES (' . $placeholders . ')';
    pdo()->prepare($sql)->execute($vals);
    $newId = (int)pdo()->lastInsertId();
    if ($newId <= 0) {
        throw new RuntimeException('create_university: no insert id returned.');
    }
    return $newId;
}

function update_university(int $userId, int $id, array $fields): bool {
    if ($id <= 0) {
        return false;
    }
    $sets = [];
    $vals = [];
    foreach (university_field_map() as $key) {
        $sets[] = camel_to_snake($key) . ' = ?';
        $vals[] = $fields[$key] ?? '';
    }
    $sets[] = 'updated_at = ?';
    $vals[] = db_now();
    $vals[] = $id;
    $vals[] = $userId;
    $sql = 'UPDATE universities SET ' . implode(', ', $sets) . ' WHERE id = ? AND user_id = ? LIMIT 1';
    $stmt = pdo()->prepare($sql);
    $stmt->execute($vals);
    $affected = $stmt->rowCount();
    if ($affected > 1) {
        throw new RuntimeException('update_university affected more than one row (id ' . $id . ').');
    }
    if ($affected === 1) {
        return true;
    }
    // MySQL reports 0 changed rows when the values were identical; confirm the
    // row really exists and belongs to this user before reporting success.
    return get_university($userId, $id) !== null;
}

function update_university_status(int $userId, int $id, string $status): bool {
    $stmt = pdo()->prepare('UPDATE universities SET status = ?, updated_at = ? WHERE id = ? AND user_id = ?');
    return $stmt->execute([$status, db_now(), $id, $userId]);
}

function delete_university(int $userId, int $id): bool {
    $stmt = pdo()->prepare('DELETE FROM universities WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);
    return $stmt->rowCount() > 0;
}

/** Locks the user's row so concurrent create/update requests for one user run one at a time. Call inside a transaction. */
function lock_user_row(int $userId): void {
    $stmt = pdo()->prepare('SELECT id FROM users WHERE id = ? FOR UPDATE');
    $stmt->execute([$userId]);
}

/** Like get_university(), but takes a row lock. Call inside a transaction. */
function get_university_for_update(int $userId, int $id): ?array {
    $stmt = pdo()->prepare('SELECT * FROM universities WHERE id = ? AND user_id = ? FOR UPDATE');
    $stmt->execute([$id, $userId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Finds another university belonging to THIS user with the same normalized
 * name (and the same state, or either state blank). Never looks at other users.
 */
function find_duplicate_university(int $userId, string $name, string $state, ?int $excludeId = null): ?array {
    $nameKey = normalize_match_key($name);
    if ($nameKey === '') {
        return null;
    }
    $stateKey = normalize_match_key($state);
    $stmt = pdo()->prepare('SELECT id, name, state FROM universities WHERE user_id = ?');
    $stmt->execute([$userId]);
    foreach ($stmt->fetchAll() as $row) {
        if ($excludeId !== null && (int)$row['id'] === $excludeId) {
            continue;
        }
        if (normalize_match_key((string)$row['name']) !== $nameKey) {
            continue;
        }
        $rowState = normalize_match_key((string)$row['state']);
        if ($stateKey === '' || $rowState === '' || $stateKey === $rowState) {
            return $row;
        }
    }
    return null;
}

/** Issues a single-use form token (stored in the session) to block double-submits. */
function issue_submit_token(string $scope): string {
    $token = bin2hex(random_bytes(16));
    $_SESSION['submit_tokens'][$scope][$token] = time();
    if (count($_SESSION['submit_tokens'][$scope]) > 50) {
        $_SESSION['submit_tokens'][$scope] = array_slice($_SESSION['submit_tokens'][$scope], -50, null, true);
    }
    return $token;
}

/** Returns true exactly once per issued token. */
function consume_submit_token(string $scope, string $token): bool {
    if ($token === '' || !isset($_SESSION['submit_tokens'][$scope][$token])) {
        return false;
    }
    unset($_SESSION['submit_tokens'][$scope][$token]);
    return true;
}
// =======================================================================
// PROGRAMS  (all scoped to a given $userId)
// =======================================================================

function decode_program_row(array $row): array {
    $row['language'] = json_decode($row['language_json'], true) ?: default_language();
    $row['fees'] = json_decode($row['fees_json'], true) ?: default_fees();
    $row['application'] = json_decode($row['application_json'], true) ?: default_application();
    $row['admission'] = json_decode($row['admission_json'], true) ?: default_admission();
    $row['documents'] = json_decode($row['documents_json'], true) ?: [];
    $row['links'] = json_decode($row['links_json'], true) ?: [];
    $row['personal'] = json_decode($row['personal_json'], true) ?: default_personal();
    $row['studyMode'] = $row['study_mode'];
    $row['studyLocation'] = $row['study_location'];
    return $row;
}

function get_programs_for_university(int $userId, int $uniId): array {
    $stmt = pdo()->prepare('SELECT * FROM programs WHERE university_id = ? AND user_id = ? ORDER BY name');
    $stmt->execute([$uniId, $userId]);
    return array_map('decode_program_row', $stmt->fetchAll());
}

/** [id => [{id,name}, ...]] grouped by university id — for the sidebar tree. */
function get_programs_light_grouped(int $userId): array {
    $stmt = pdo()->prepare('SELECT id, name, university_id FROM programs WHERE user_id = ? ORDER BY name');
    $stmt->execute([$userId]);
    $grouped = [];
    foreach ($stmt->fetchAll() as $row) {
        $grouped[(int)$row['university_id']][] = $row;
    }
    return $grouped;
}

/**
 * Fetch one program (with its parent university row) scoped to $userId.
 * Returns null if not found or not owned by this user.
 */
function get_program(int $userId, int $progId): ?array {
    $stmt = pdo()->prepare('SELECT p.*, u.id AS uni_id, u.name AS uni_name, u.status AS uni_status
                             FROM programs p JOIN universities u ON p.university_id = u.id
                             WHERE p.id = ? AND p.user_id = ?');
    $stmt->execute([$progId, $userId]);
    $row = $stmt->fetch();
    if (!$row) return null;
    return decode_program_row($row);
}

function create_program(int $userId, int $uniId, array $overview): int {
    $now = db_now();
    $sql = 'INSERT INTO programs
        (university_id, user_id, name, degree, subject, department, faculty, website, description,
         study_location, duration, ects, study_mode, intake,
         language_json, fees_json, application_json, admission_json, documents_json, links_json, personal_json,
         created_at, updated_at)
        VALUES (?,?,?,?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?,?, ?,?)';
    $stmt = pdo()->prepare($sql);
    $stmt->execute([
        $uniId, $userId,
        $overview['name'] ?? '', $overview['degree'] ?? 'M.Sc.', $overview['subject'] ?? '',
        $overview['department'] ?? '', $overview['faculty'] ?? '', $overview['website'] ?? '',
        $overview['description'] ?? '', $overview['studyLocation'] ?? '', $overview['duration'] ?? '',
        $overview['ects'] ?? '', $overview['studyMode'] ?? 'Full-time', $overview['intake'] ?? 'Winter',
        json_encode(default_language()), json_encode(default_fees()), json_encode(default_application()),
        json_encode(default_admission()), json_encode(default_documents()), json_encode([]), json_encode(default_personal()),
        $now, $now,
    ]);
    return (int)pdo()->lastInsertId();
}

function update_program_overview(int $userId, int $progId, array $fields): bool {
    $sql = 'UPDATE programs SET name=?, degree=?, subject=?, department=?, faculty=?, website=?, description=?,
            study_location=?, duration=?, ects=?, study_mode=?, intake=?, updated_at=?
            WHERE id=? AND user_id=?';
    $stmt = pdo()->prepare($sql);
    return $stmt->execute([
        $fields['name'] ?? '', $fields['degree'] ?? '', $fields['subject'] ?? '', $fields['department'] ?? '',
        $fields['faculty'] ?? '', $fields['website'] ?? '', $fields['description'] ?? '',
        $fields['studyLocation'] ?? '', $fields['duration'] ?? '', $fields['ects'] ?? '',
        $fields['studyMode'] ?? 'Full-time', $fields['intake'] ?? 'Winter', db_now(),
        $progId, $userId,
    ]);
}

/** Update one JSON section (language|fees|application|admission|personal) of a program. */
function update_program_section(int $userId, int $progId, string $section, array $data): bool {
    $columnMap = [
        'language' => 'language_json', 'fees' => 'fees_json', 'application' => 'application_json',
        'admission' => 'admission_json', 'personal' => 'personal_json',
    ];
    if (!isset($columnMap[$section])) return false;
    $col = $columnMap[$section];
    $stmt = pdo()->prepare("UPDATE programs SET $col = ?, updated_at = ? WHERE id = ? AND user_id = ?");
    return $stmt->execute([json_encode($data), db_now(), $progId, $userId]);
}

function delete_program(int $userId, int $progId): bool {
    $stmt = pdo()->prepare('DELETE FROM programs WHERE id = ? AND user_id = ?');
    $stmt->execute([$progId, $userId]);
    return $stmt->rowCount() > 0;
}

function duplicate_program(int $userId, int $progId): ?int {
    $program = get_program($userId, $progId);
    if ($program === null) return null;

    // Fresh sub-ids for the copied documents & links so they don't collide
    // with the originals in the UI.
    $docs = $program['documents'];
    foreach ($docs as &$d) { $d['id'] = generate_sub_id('doc'); }
    unset($d);
    $links = $program['links'];
    foreach ($links as &$l) { $l['id'] = generate_sub_id('link'); }
    unset($l);

    $now = db_now();
    $sql = 'INSERT INTO programs
        (university_id, user_id, name, degree, subject, department, faculty, website, description,
         study_location, duration, ects, study_mode, intake,
         language_json, fees_json, application_json, admission_json, documents_json, links_json, personal_json,
         created_at, updated_at)
        VALUES (?,?,?,?,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?,?, ?,?)';
    $stmt = pdo()->prepare($sql);
    $stmt->execute([
        $program['university_id'], $userId, $program['name'] . ' (Copy)', $program['degree'], $program['subject'],
        $program['department'], $program['faculty'], $program['website'], $program['description'],
        $program['studyLocation'], $program['duration'], $program['ects'], $program['studyMode'], $program['intake'],
        json_encode($program['language']), json_encode($program['fees']), json_encode($program['application']),
        json_encode($program['admission']), json_encode($docs), json_encode($links), json_encode($program['personal']),
        $now, $now,
    ]);
    return (int)pdo()->lastInsertId();
}

// ---- Documents (stored as a JSON array within a program row) ----

function add_document(int $userId, int $progId, string $name, string $status): bool {
    $program = get_program($userId, $progId);
    if ($program === null) return false;
    $docs = $program['documents'];
    $docs[] = ['id' => generate_sub_id('doc'), 'name' => $name, 'status' => $status ?: 'Required'];
    $stmt = pdo()->prepare('UPDATE programs SET documents_json = ?, updated_at = ? WHERE id = ? AND user_id = ?');
    return $stmt->execute([json_encode($docs), db_now(), $progId, $userId]);
}

function update_document_status(int $userId, int $progId, string $docId, string $status): bool {
    $program = get_program($userId, $progId);
    if ($program === null) return false;
    $docs = $program['documents'];
    foreach ($docs as &$d) {
        if ($d['id'] === $docId) { $d['status'] = $status; break; }
    }
    unset($d);
    $stmt = pdo()->prepare('UPDATE programs SET documents_json = ?, updated_at = ? WHERE id = ? AND user_id = ?');
    return $stmt->execute([json_encode($docs), db_now(), $progId, $userId]);
}

function delete_document(int $userId, int $progId, string $docId): bool {
    $program = get_program($userId, $progId);
    if ($program === null) return false;
    $docs = array_values(array_filter($program['documents'], fn($d) => $d['id'] !== $docId));
    $stmt = pdo()->prepare('UPDATE programs SET documents_json = ?, updated_at = ? WHERE id = ? AND user_id = ?');
    return $stmt->execute([json_encode($docs), db_now(), $progId, $userId]);
}

// ---- Links (stored as a JSON array within a program row) ----

function add_link(int $userId, int $progId, string $title, string $url, string $description): bool {
    $program = get_program($userId, $progId);
    if ($program === null) return false;
    $links = $program['links'];
    $links[] = ['id' => generate_sub_id('link'), 'title' => $title, 'url' => $url, 'description' => $description];
    $stmt = pdo()->prepare('UPDATE programs SET links_json = ?, updated_at = ? WHERE id = ? AND user_id = ?');
    return $stmt->execute([json_encode($links), db_now(), $progId, $userId]);
}

function delete_link(int $userId, int $progId, string $linkId): bool {
    $program = get_program($userId, $progId);
    if ($program === null) return false;
    $links = array_values(array_filter($program['links'], fn($l) => $l['id'] !== $linkId));
    $stmt = pdo()->prepare('UPDATE programs SET links_json = ?, updated_at = ? WHERE id = ? AND user_id = ?');
    return $stmt->execute([json_encode($links), db_now(), $progId, $userId]);
}

// =======================================================================
// Cross-cutting queries (dashboard, explorer, search)
// =======================================================================

/** Every program for this user, each carrying its parent university row. Same shape as the original single-user app. */
function all_programs_flat(int $userId): array {
    $sql = 'SELECT p.*, u.id AS uni_id, u.name AS uni_name, u.city AS uni_city, u.state AS uni_state,
                   u.type AS uni_type, u.status AS uni_status
            FROM programs p JOIN universities u ON p.university_id = u.id
            WHERE p.user_id = ? ORDER BY u.name, p.name';
    $stmt = pdo()->prepare($sql);
    $stmt->execute([$userId]);
    $out = [];
    foreach ($stmt->fetchAll() as $row) {
        $program = decode_program_row($row);
        $university = [
            'id' => $row['uni_id'], 'name' => $row['uni_name'], 'city' => $row['uni_city'],
            'state' => $row['uni_state'], 'type' => $row['uni_type'], 'status' => $row['uni_status'],
        ];
        $out[] = ['university' => $university, 'program' => $program];
    }
    return $out;
}

function count_all_programs(int $userId): int {
    $stmt = pdo()->prepare('SELECT COUNT(*) FROM programs WHERE user_id = ?');
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}

/** Simple in-PHP substring search across a user's own universities & programs. */
function search_user_data(int $userId, string $q): array {
    $needle = strtolower($q);
    $uniResults = [];
    $progResults = [];

    foreach (get_universities($userId) as $uni) {
        $haystack = strtolower(implode(' ', [
            $uni['name'], $uni['official_name'], $uni['city'], $uni['state'], $uni['type'],
            $uni['general_notes'], $uni['application_method'], $uni['tuition_fee'], $uni['application_fee'],
        ]));
        if (str_contains($haystack, $needle)) {
            $uniResults[] = $uni;
        }
    }

    foreach (all_programs_flat($userId) as $r) {
        $p = $r['program'];
        $haystack = strtolower(implode(' ', [
            $p['name'], $p['degree'], $p['subject'], $p['department'], $p['faculty'], $p['description'],
            $p['language']['teachingLanguage'], $p['language']['moiDetails'], $p['fees']['feeNotes'],
            $p['application']['method'], $p['admission']['requiredMajor'], $p['admission']['otherAcademic'],
            $p['admission']['additionalReq'], $p['personal']['notes'], $p['personal']['questions'],
        ]));
        $keywordMatch =
            (str_contains($needle, 'moi') && ($p['language']['moiAccepted'] !== 'Unknown' || $p['language']['moiDetails'] !== '')) ||
            (str_contains($needle, 'ielts') && ($p['language']['ieltsRequired'] !== 'Unknown' || $p['language']['ieltsMin'] !== '')) ||
            (str_contains($needle, 'uni-assist') && $p['application']['method'] === 'Uni-Assist');

        if (str_contains($haystack, $needle) || $keywordMatch) {
            $progResults[] = $r;
        }
    }

    return ['universities' => $uniResults, 'programs' => $progResults];
}

// =======================================================================
// Export / Import (per user — replaces the old single-file JSON export)
// =======================================================================

function export_user_data(int $userId): array {
    $universities = [];
    foreach (get_universities($userId) as $uni) {
        $programs = [];
        foreach (get_programs_for_university($userId, (int)$uni['id']) as $p) {
            $programs[] = [
                'id' => (int)$p['id'],
                'name' => $p['name'], 'degree' => $p['degree'], 'subject' => $p['subject'],
                'department' => $p['department'], 'faculty' => $p['faculty'], 'website' => $p['website'],
                'description' => $p['description'], 'studyLocation' => $p['studyLocation'], 'duration' => $p['duration'],
                'ects' => $p['ects'], 'studyMode' => $p['studyMode'], 'intake' => $p['intake'],
                'language' => $p['language'], 'fees' => $p['fees'], 'application' => $p['application'],
                'admission' => $p['admission'], 'documents' => $p['documents'], 'links' => $p['links'],
                'personal' => $p['personal'], 'createdAt' => $p['created_at'], 'updatedAt' => $p['updated_at'],
            ];
        }
        $universities[] = [
            'id' => (int)$uni['id'],
            'name' => $uni['name'], 'officialName' => $uni['official_name'], 'city' => $uni['city'],
            'state' => $uni['state'], 'type' => $uni['type'], 'website' => $uni['website'],
            'intlWebsite' => $uni['intl_website'], 'applicationPortal' => $uni['application_portal'],
            'applicationMethod' => $uni['application_method'], 'applicationFee' => $uni['application_fee'],
            'tuitionFee' => $uni['tuition_fee'], 'semesterContribution' => $uni['semester_contribution'],
            'generalNotes' => $uni['general_notes'], 'status' => $uni['status'],
            'createdAt' => $uni['created_at'], 'updatedAt' => $uni['updated_at'],
            'programs' => $programs,
        ];
    }
    return [
        'exportedAt' => date('c'),
        'exportUserId' => $userId, // used only to recognize "this is my own previous export" for id-based matching
        'universities' => $universities,
    ];
}

// NOTE: the old single-step import_user_data() function has been
// replaced by the Smart Merge pipeline below (build_import_plan /
// commit_import_plan for "Smart Merge", replace_all_user_data() for
// "Replace All"), used by actions/import-preview.php and
// actions/import-commit.php. The old function duplicated universities
// on every merge import and could silently delete data on replace —
// see the engine below for the fix.

// =======================================================================
// SMART MERGE (UPSERT) IMPORT ENGINE
// =======================================================================
//
// Two-phase design so the UI can show a preview before anything is
// written: build_import_plan() only READS the database and returns a
// plan describing what would happen; commit_import_plan() is what
// actually writes, wrapped in a single transaction so a failure partway
// through leaves the database exactly as it was before the import.

/** Lowercases, folds German umlauts, and strips punctuation/whitespace differences for robust name comparison. Deliberately avoids mbstring (not guaranteed on all hosting) by folding case-sensitive umlaut variants before a plain strtolower(). */
function normalize_match_key(string $s): string {
    $s = trim($s);
    $s = strtr($s, [
        'Ä' => 'ae', 'Ö' => 'oe', 'Ü' => 'ue', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
    ]);
    $s = strtolower($s);
    $s = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $s);
    $s = preg_replace('/\s+/', ' ', $s);
    return trim($s ?? '');
}

function university_row_to_camel(array $row): array {
    return [
        'name' => $row['name'], 'officialName' => $row['official_name'], 'city' => $row['city'],
        'state' => $row['state'], 'type' => $row['type'], 'website' => $row['website'],
        'intlWebsite' => $row['intl_website'], 'applicationPortal' => $row['application_portal'],
        'applicationMethod' => $row['application_method'], 'applicationFee' => $row['application_fee'],
        'tuitionFee' => $row['tuition_fee'], 'semesterContribution' => $row['semester_contribution'],
        'generalNotes' => $row['general_notes'], 'status' => $row['status'],
    ];
}

function program_overview_keys(): array {
    return ['name', 'degree', 'subject', 'department', 'faculty', 'website', 'description', 'studyLocation', 'duration', 'ects', 'studyMode', 'intake'];
}

/** A value counts as "provided" only if it's a non-empty, non-whitespace string. Empty imported fields must never blank out existing data. */
function is_meaningful_value($v): bool {
    return $v !== null && trim((string)$v) !== '';
}

/**
 * Compares an existing flat field set against incoming data for the
 * given keys. Never lets an empty/missing incoming value erase an
 * existing one. Returns the full merged field set (ready to hand to
 * create/update) plus a list of which keys actually changed (for the
 * preview UI).
 */
function diff_and_merge_fields(array $existing, array $incoming, array $keys): array {
    $merged = $existing;
    $changes = [];
    foreach ($keys as $key) {
        if (!array_key_exists($key, $incoming)) continue;
        $incomingVal = is_array($incoming[$key]) ? $incoming[$key] : (string)$incoming[$key];
        if (!is_meaningful_value(is_array($incomingVal) ? '1' : $incomingVal) && !is_array($incomingVal)) continue;
        $existingVal = $existing[$key] ?? '';
        if ((string)$incomingVal !== (string)$existingVal) {
            $changes[$key] = ['from' => $existingVal, 'to' => $incomingVal];
            $merged[$key] = $incomingVal;
        }
    }
    return ['merged' => $merged, 'changes' => $changes];
}

/** Same idea as diff_and_merge_fields() but for merging TWO imported records together (duplicate rows within one uploaded file). Later non-empty values win on conflict. */
function merge_two_imported_maps(array $base, array $overlay, array $keys): array {
    $out = $base;
    foreach ($keys as $key) {
        if (isset($overlay[$key]) && is_meaningful_value(is_array($overlay[$key]) ? '1' : $overlay[$key])) {
            $out[$key] = $overlay[$key];
        }
    }
    return $out;
}

/**
 * Collapses duplicate entries within the SAME imported list (matched by
 * normalized name, plus $extraKey when given, e.g. degree for programs)
 * into single entries, merging their fields non-destructively and
 * concatenating any nested 'programs' lists. This is what prevents a
 * file that lists the same university twice from creating two records.
 *
 * Used for Smart Merge (an untrusted uploaded file). It is deliberately
 * NOT used by replace_all_user_data() — see that function's docblock
 * for why a full-replace restore must not deduplicate.
 */
function collapse_duplicate_imports(array $items, array $mergeKeys, ?string $extraKey = null): array {
    $order = [];
    $byKey = [];
    foreach ($items as $item) {
        if (!is_array($item)) continue;
        $name = (string)($item['name'] ?? '');
        $k = normalize_match_key($name) . ($extraKey ? '|' . normalize_match_key((string)($item[$extraKey] ?? '')) : '');
        if ($k === '') { $k = '__unnamed_' . count($order); } // never silently drop a nameless row
        if (!isset($byKey[$k])) {
            $byKey[$k] = $item;
            $order[] = $k;
        } else {
            $mergedPrograms = array_merge($byKey[$k]['programs'] ?? [], $item['programs'] ?? []);
            $byKey[$k] = merge_two_imported_maps($byKey[$k], $item, $mergeKeys);
            if ($mergedPrograms) $byKey[$k]['programs'] = $mergedPrograms;
        }
    }
    $result = [];
    foreach ($order as $k) $result[] = $byKey[$k];
    return $result;
}

/**
 * Builds the Smart Merge plan for an uploaded export WITHOUT writing
 * anything to the database. Safe to call as many times as needed (e.g.
 * while the user is reviewing the preview).
 */
function build_import_plan(int $userId, array $data): array {
    $existingUnis = get_universities($userId);
    $existingById = [];
    foreach ($existingUnis as $u) $existingById[(int)$u['id']] = $u;

    $incomingUnis = collapse_duplicate_imports($data['universities'] ?? [], university_field_map());

    $claimedUniIds = [];
    $plan = ['universities' => []];

    foreach ($incomingUnis as $idx => $uniData) {
        $name = trim((string)($uniData['name'] ?? ''));
        $nameKey = normalize_match_key($name);
        $stateKey = normalize_match_key((string)($uniData['state'] ?? ''));

        $matchMethod = 'none';
        $existingId = null;
        $candidates = [];

        // 1) Match by the university's own previously-exported id, if it's
        //    still present in this account (most reliable: re-importing
        //    your own export always lands on exactly the same record).
        $incomingId = isset($uniData['id']) ? (int)$uniData['id'] : 0;
        if ($incomingId > 0 && isset($existingById[$incomingId]) && !in_array($incomingId, $claimedUniIds, true)) {
            $matchMethod = 'id';
            $existingId = $incomingId;
        }

        // 2) Fall back to normalized name (+ state to disambiguate).
        if ($existingId === null && $nameKey !== '') {
            $nameMatches = [];
            foreach ($existingUnis as $u) {
                if (in_array((int)$u['id'], $claimedUniIds, true)) continue;
                if (normalize_match_key($u['name']) === $nameKey) $nameMatches[] = $u;
            }
            if (count($nameMatches) === 1) {
                $matchMethod = 'name';
                $existingId = (int)$nameMatches[0]['id'];
            } elseif (count($nameMatches) > 1) {
                if ($stateKey !== '') {
                    $stateFiltered = array_values(array_filter($nameMatches, fn($u) => normalize_match_key($u['state']) === $stateKey));
                    if (count($stateFiltered) === 1) {
                        $matchMethod = 'name_state';
                        $existingId = (int)$stateFiltered[0]['id'];
                    }
                }
                if ($existingId === null) {
                    $matchMethod = 'ambiguous';
                    foreach ($nameMatches as $u) {
                        $candidates[] = ['id' => (int)$u['id'], 'name' => $u['name'], 'city' => $u['city'], 'state' => $u['state']];
                    }
                }
            }
        }

        $uniPlanItem = [
            'import_index' => $idx,
            'name' => $name, 'city' => $uniData['city'] ?? '', 'state' => $uniData['state'] ?? '',
            'match_method' => $matchMethod,
            'candidates' => $candidates,
            'existing_id' => $existingId,
        ];

        if ($matchMethod === 'ambiguous') {
            $uniPlanItem['action'] = 'ambiguous';
            $uniPlanItem['programs'] = []; // resolved later, after the user picks a target
        } elseif ($existingId !== null) {
            $claimedUniIds[] = $existingId;
            $existingRow = $existingById[$existingId];
            $existingFields = university_row_to_camel($existingRow);
            $incomingFields = array_intersect_key($uniData, array_flip(university_field_map()));
            $diff = diff_and_merge_fields($existingFields, $incomingFields, university_field_map());
            $uniPlanItem['action'] = empty($diff['changes']) ? 'unchanged' : 'update';
            $uniPlanItem['field_changes'] = $diff['changes'];
            $uniPlanItem['programs'] = build_program_plan($userId, $existingId, $uniData['programs'] ?? []);
        } else {
            $uniPlanItem['action'] = 'new';
            $uniPlanItem['programs'] = build_program_plan($userId, null, $uniData['programs'] ?? []);
        }

        $plan['universities'][] = $uniPlanItem;
    }

    // Roll up counts for the summary header on the preview page.
    $stats = ['new_universities' => 0, 'updated_universities' => 0, 'unchanged_universities' => 0, 'ambiguous_universities' => 0, 'new_programs' => 0, 'updated_programs' => 0, 'unchanged_programs' => 0, 'ambiguous_programs' => 0];
    foreach ($plan['universities'] as $u) {
        $stats[$u['action'] . '_universities']++;
        foreach ($u['programs'] as $p) {
            $stats[$p['action'] . '_programs']++;
        }
    }
    $plan['stats'] = $stats;
    $plan['untouchedExistingCount'] = count($existingUnis) - count($claimedUniIds);

    return $plan;
}

/** Same matching logic as universities, scoped to one (matched-or-new) parent university's existing programs. */
function build_program_plan(int $userId, ?int $existingUniId, array $incomingPrograms): array {
    $existingPrograms = $existingUniId !== null ? get_programs_for_university($userId, $existingUniId) : [];
    $existingById = [];
    foreach ($existingPrograms as $p) $existingById[(int)$p['id']] = $p;

    $incomingPrograms = collapse_duplicate_imports($incomingPrograms, program_overview_keys(), 'degree');

    $claimedIds = [];
    $planItems = [];

    foreach ($incomingPrograms as $idx => $progData) {
        $name = trim((string)($progData['name'] ?? ''));
        $nameKey = normalize_match_key($name);
        $degreeKey = normalize_match_key((string)($progData['degree'] ?? ''));

        $matchMethod = 'none';
        $existingId = null;
        $candidates = [];

        $incomingId = isset($progData['id']) ? (int)$progData['id'] : 0;
        if ($incomingId > 0 && isset($existingById[$incomingId]) && !in_array($incomingId, $claimedIds, true)) {
            $matchMethod = 'id';
            $existingId = $incomingId;
        }

        if ($existingId === null && $nameKey !== '') {
            $nameMatches = [];
            foreach ($existingPrograms as $p) {
                if (in_array((int)$p['id'], $claimedIds, true)) continue;
                if (normalize_match_key($p['name']) === $nameKey) $nameMatches[] = $p;
            }
            if (count($nameMatches) === 1) {
                $matchMethod = 'name';
                $existingId = (int)$nameMatches[0]['id'];
            } elseif (count($nameMatches) > 1) {
                if ($degreeKey !== '') {
                    $degreeFiltered = array_values(array_filter($nameMatches, fn($p) => normalize_match_key($p['degree']) === $degreeKey));
                    if (count($degreeFiltered) === 1) {
                        $matchMethod = 'name_degree';
                        $existingId = (int)$degreeFiltered[0]['id'];
                    }
                }
                if ($existingId === null) {
                    $matchMethod = 'ambiguous';
                    foreach ($nameMatches as $p) {
                        $candidates[] = ['id' => (int)$p['id'], 'name' => $p['name'], 'degree' => $p['degree']];
                    }
                }
            }
        }

        $item = [
            'import_index' => $idx,
            'name' => $name, 'degree' => $progData['degree'] ?? '',
            'match_method' => $matchMethod, 'candidates' => $candidates, 'existing_id' => $existingId,
        ];

        if ($matchMethod === 'ambiguous') {
            $item['action'] = 'ambiguous';
        } elseif ($existingId !== null) {
            $claimedIds[] = $existingId;
            $existing = $existingById[$existingId];
            $incomingOverview = array_intersect_key($progData, array_flip(program_overview_keys()));
            $diff = diff_and_merge_fields($existing, $incomingOverview, program_overview_keys());
            $changed = !empty($diff['changes']);
            foreach (['language' => default_language(), 'fees' => default_fees(), 'application' => default_application(), 'admission' => default_admission(), 'personal' => default_personal()] as $section => $defaults) {
                if (isset($progData[$section]) && is_array($progData[$section])) {
                    $sectionDiff = diff_and_merge_fields($existing[$section] ?? $defaults, $progData[$section], array_keys($defaults));
                    if (!empty($sectionDiff['changes'])) $changed = true;
                }
            }
            $item['action'] = $changed ? 'update' : 'unchanged';
            $item['field_changes'] = $diff['changes'];
        } else {
            $item['action'] = 'new';
        }

        $planItems[] = $item;
    }

    return $planItems;
}

/**
 * Actually writes a Smart Merge plan to the database, inside a single
 * transaction — if anything throws partway through, everything is
 * rolled back and the database is left exactly as it was.
 *
 * $resolutions lets the confirm step tell us what to do with entries
 * the plan marked 'ambiguous': for university index $i, a key
 * "u{$i}" => 'new' (create as a new university) or "u{$i}" => '<existingId>'
 * (merge into that specific existing university) or "u{$i}" => 'skip'.
 * Same idea for programs, keyed "u{$i}_p{$j}".
 */
function commit_import_plan(int $userId, array $data, array $plan, array $resolutions): array {
    $incomingUnis = collapse_duplicate_imports($data['universities'] ?? [], university_field_map());
    $summary = ['created_universities' => 0, 'updated_universities' => 0, 'unchanged_universities' => 0, 'skipped_universities' => 0, 'created_programs' => 0, 'updated_programs' => 0, 'unchanged_programs' => 0, 'skipped_programs' => 0];

    $pdo = pdo();
    $pdo->beginTransaction();
    try {
        foreach ($plan['universities'] as $i => $uniPlan) {
            $uniData = $incomingUnis[$uniPlan['import_index']];
            $action = $uniPlan['action'];
            $targetUniId = $uniPlan['existing_id'];

            if ($action === 'ambiguous') {
                $resolution = $resolutions['u' . $i] ?? 'skip';
                if ($resolution === 'skip') {
                    $summary['skipped_universities']++;
                    continue;
                } elseif ($resolution === 'new') {
                    $action = 'new';
                    $targetUniId = null;
                } elseif (ctype_digit((string)$resolution)) {
                    $existingRow = get_university($userId, (int)$resolution);
                    if ($existingRow === null) { // safety: ignore a resolution pointing at a university this user doesn't own
                        $summary['skipped_universities']++;
                        continue;
                    }
                    $existingFields = university_row_to_camel($existingRow);
                    $incomingFields = array_intersect_key($uniData, array_flip(university_field_map()));
                    $diff = diff_and_merge_fields($existingFields, $incomingFields, university_field_map());
                    $action = empty($diff['changes']) ? 'unchanged' : 'update';
                    $targetUniId = (int)$resolution;
                } else {
                    $summary['skipped_universities']++;
                    continue;
                }
            }

            if ($action === 'new') {
                $fields = array_merge(array_fill_keys(university_field_map(), ''), array_intersect_key($uniData, array_flip(university_field_map())));
                $targetUniId = create_university($userId, $fields);
                $summary['created_universities']++;
            } elseif ($action === 'update') {
                $existingRow = get_university($userId, $targetUniId);
                $existingFields = university_row_to_camel($existingRow);
                $incomingFields = array_intersect_key($uniData, array_flip(university_field_map()));
                $diff = diff_and_merge_fields($existingFields, $incomingFields, university_field_map());
                update_university($userId, $targetUniId, $diff['merged']);
                $summary['updated_universities']++;
            } else { // unchanged
                $summary['unchanged_universities']++;
            }

            $progSummary = commit_program_plan($userId, $targetUniId, $uniData['programs'] ?? [], $uniPlan['programs'], $resolutions, $i);
            foreach ($progSummary as $k => $v) $summary[$k] += $v;
        }

        $pdo->commit();
        return $summary;
    } catch (\Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function commit_program_plan(int $userId, int $uniId, array $incomingPrograms, array $programPlan, array $resolutions, int $uniIndex): array {
    $incomingPrograms = collapse_duplicate_imports($incomingPrograms, program_overview_keys(), 'degree');
    $summary = ['created_programs' => 0, 'updated_programs' => 0, 'unchanged_programs' => 0, 'skipped_programs' => 0];

    foreach ($programPlan as $j => $progPlan) {
        $progData = $incomingPrograms[$progPlan['import_index']];
        $action = $progPlan['action'];
        $targetProgId = $progPlan['existing_id'];

        if ($action === 'ambiguous') {
            $resolution = $resolutions['u' . $uniIndex . '_p' . $j] ?? 'skip';
            if ($resolution === 'skip') {
                $summary['skipped_programs']++;
                continue;
            } elseif ($resolution === 'new') {
                $action = 'new';
                $targetProgId = null;
            } elseif (ctype_digit((string)$resolution)) {
                $targetProgId = (int)$resolution;
                $existing = get_program($userId, $targetProgId);
                if ($existing === null || (int)$existing['university_id'] !== $uniId) {
                    $summary['skipped_programs']++;
                    continue;
                }
                $action = 'update';
            } else {
                $summary['skipped_programs']++;
                continue;
            }
        }

        if ($action === 'new') {
            $overview = array_merge(['name' => '', 'degree' => 'M.Sc.', 'subject' => '', 'department' => '', 'faculty' => '', 'website' => '', 'description' => '', 'studyLocation' => '', 'duration' => '', 'ects' => '', 'studyMode' => 'Full-time', 'intake' => 'Winter'], array_intersect_key($progData, array_flip(program_overview_keys())));
            $newProgId = create_program($userId, $uniId, $overview);
            apply_program_sections($userId, $newProgId, $progData, true);
            $summary['created_programs']++;
        } elseif ($action === 'update') {
            $existing = get_program($userId, $targetProgId);
            $incomingOverview = array_intersect_key($progData, array_flip(program_overview_keys()));
            $diff = diff_and_merge_fields($existing, $incomingOverview, program_overview_keys());
            update_program_overview($userId, $targetProgId, $diff['merged']);
            apply_program_sections($userId, $targetProgId, $progData, false);
            $summary['updated_programs']++;
        } else {
            $summary['unchanged_programs']++;
        }
    }

    return $summary;
}

/** Merges the language/fees/application/admission/personal sections and the documents/links lists into an existing (or brand-new) program row, non-destructively. */
function apply_program_sections(int $userId, int $progId, array $progData, bool $isNew): void {
    $existing = $isNew ? null : get_program($userId, $progId);

    foreach (['language' => default_language(), 'fees' => default_fees(), 'application' => default_application(), 'admission' => default_admission(), 'personal' => default_personal()] as $section => $defaults) {
        if (!isset($progData[$section]) || !is_array($progData[$section])) continue;
        $base = $isNew ? $defaults : ($existing[$section] ?? $defaults);
        $diff = diff_and_merge_fields($base, $progData[$section], array_keys($defaults));
        if ($isNew || !empty($diff['changes'])) {
            update_program_section($userId, $progId, $section, $diff['merged']);
        }
    }

    if (isset($progData['documents']) && is_array($progData['documents'])) {
        $existingDocs = $isNew ? [] : ($existing['documents'] ?? []);
        $byName = [];
        foreach ($existingDocs as $idx => $d) $byName[normalize_match_key($d['name'])] = $idx;
        foreach ($progData['documents'] as $incomingDoc) {
            if (!is_array($incomingDoc)) continue;
            $key = normalize_match_key($incomingDoc['name'] ?? '');
            if ($key !== '' && isset($byName[$key])) {
                $idx = $byName[$key];
                if (is_meaningful_value($incomingDoc['status'] ?? '')) $existingDocs[$idx]['status'] = $incomingDoc['status'];
            } else {
                $existingDocs[] = ['id' => generate_sub_id('doc'), 'name' => $incomingDoc['name'] ?? 'Document', 'status' => $incomingDoc['status'] ?? 'Unknown'];
            }
        }
        pdo()->prepare('UPDATE programs SET documents_json = ? WHERE id = ? AND user_id = ?')->execute([json_encode(array_values($existingDocs)), $progId, $userId]);
    }

    if (isset($progData['links']) && is_array($progData['links'])) {
        $existingLinks = $isNew ? [] : ($existing['links'] ?? []);
        $byKey = [];
        foreach ($existingLinks as $idx => $l) $byKey[normalize_match_key($l['title'] . '|' . $l['url'])] = $idx;
        foreach ($progData['links'] as $incomingLink) {
            if (!is_array($incomingLink)) continue;
            $key = normalize_match_key(($incomingLink['title'] ?? '') . '|' . ($incomingLink['url'] ?? ''));
            if ($key !== '' && isset($byKey[$key])) {
                $idx = $byKey[$key];
                if (is_meaningful_value($incomingLink['description'] ?? '')) $existingLinks[$idx]['description'] = $incomingLink['description'];
            } else {
                $existingLinks[] = ['id' => generate_sub_id('link'), 'title' => $incomingLink['title'] ?? '', 'url' => $incomingLink['url'] ?? '', 'description' => $incomingLink['description'] ?? ''];
            }
        }
        pdo()->prepare('UPDATE programs SET links_json = ? WHERE id = ? AND user_id = ?')->execute([json_encode(array_values($existingLinks)), $progId, $userId]);
    }
}

/**
 * "Replace All": wipes this user's current data and replaces it with
 * the imported file, inside a single transaction. A safety snapshot of
 * the data being replaced is taken first (see create_import_backup())
 * by the caller, BEFORE this function runs.
 *
 * Deliberately does NOT run the imported universities through
 * collapse_duplicate_imports(): that de-duplication step exists to
 * protect Smart Merge from an untrusted file that accidentally lists
 * the same university twice. Here, the data being restored is either a
 * full export or an internal backup snapshot — both are, by
 * construction, already a faithful one-row-per-university record of
 * real (possibly similarly-named) universities, so collapsing by
 * normalized name would wrongly merge two genuinely distinct
 * universities that just happen to share a name.
 */
function replace_all_user_data(int $userId, array $data): array {
    $pdo = pdo();
    $pdo->beginTransaction();
    try {
        foreach (get_universities($userId) as $uni) {
            delete_university($userId, (int)$uni['id']);
        }
        $created = [];
        foreach ($data['universities'] ?? [] as $uniData) {
            if (!is_array($uniData)) continue;
            $fields = array_merge(array_fill_keys(university_field_map(), ''), array_intersect_key($uniData, array_flip(university_field_map())));
            $uniId = create_university($userId, $fields);
            foreach ($uniData['programs'] ?? [] as $progData) {
                if (!is_array($progData)) continue;
                $overview = array_merge(['name' => '', 'degree' => 'M.Sc.', 'subject' => '', 'department' => '', 'faculty' => '', 'website' => '', 'description' => '', 'studyLocation' => '', 'duration' => '', 'ects' => '', 'studyMode' => 'Full-time', 'intake' => 'Winter'], array_intersect_key($progData, array_flip(program_overview_keys())));
                $progId = create_program($userId, $uniId, $overview);
                apply_program_sections($userId, $progId, $progData, true);
            }
            $created[] = $uniId;
        }
        $pdo->commit();
        return ['universities' => $created];
    } catch (\Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// =======================================================================
// IMPORT SESSIONS (holds an uploaded file + its plan between the
// preview and confirm steps of the Smart Merge wizard)
// =======================================================================

function create_import_session(int $userId, string $filename, array $payload, array $plan, string $mode): string {
    cleanup_expired_import_sessions();
    $token = bin2hex(random_bytes(24));
    $stmt = pdo()->prepare('INSERT INTO import_sessions (token, user_id, filename, mode, payload, plan, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$token, $userId, $filename, $mode, json_encode($payload), json_encode($plan), db_now()]);
    return $token;
}

function get_import_session(string $token, int $userId): ?array {
    $stmt = pdo()->prepare('SELECT * FROM import_sessions WHERE token = ? AND user_id = ?');
    $stmt->execute([$token, $userId]);
    $row = $stmt->fetch();
    if (!$row) return null;
    $row['payload'] = json_decode($row['payload'], true) ?: [];
    $row['plan'] = json_decode($row['plan'], true) ?: [];
    return $row;
}

function delete_import_session(string $token): void {
    pdo()->prepare('DELETE FROM import_sessions WHERE token = ?')->execute([$token]);
}

function cleanup_expired_import_sessions(): void {
    pdo()->prepare('DELETE FROM import_sessions WHERE created_at < ?')->execute([date('Y-m-d H:i:s', strtotime('-2 hours'))]);
}

// =======================================================================
// IMPORT BACKUPS (automatic safety snapshots, e.g. before Replace All)
// =======================================================================

function create_import_backup(int $userId, string $reason): int {
    $data = export_user_data($userId);
    $uniCount = count($data['universities']);
    $progCount = 0;
    foreach ($data['universities'] as $u) $progCount += count($u['programs']);
    $stmt = pdo()->prepare('INSERT INTO import_backups (user_id, reason, payload, university_count, program_count, created_at) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$userId, $reason, json_encode($data), $uniCount, $progCount, db_now()]);
    $id = (int)pdo()->lastInsertId();
    delete_old_backups($userId, 5);
    return $id;
}

function list_import_backups(int $userId): array {
    $stmt = pdo()->prepare('SELECT id, reason, university_count, program_count, created_at FROM import_backups WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function get_import_backup(int $userId, int $backupId): ?array {
    $stmt = pdo()->prepare('SELECT * FROM import_backups WHERE id = ? AND user_id = ?');
    $stmt->execute([$backupId, $userId]);
    $row = $stmt->fetch();
    if (!$row) return null;
    $row['payload'] = json_decode($row['payload'], true) ?: [];
    return $row;
}

function delete_old_backups(int $userId, int $keep = 5): void {
    $stmt = pdo()->prepare('SELECT id FROM import_backups WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$userId]);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $toDelete = array_slice($ids, $keep);
    if ($toDelete) {
        $placeholders = implode(',', array_fill(0, count($toDelete), '?'));
        pdo()->prepare("DELETE FROM import_backups WHERE id IN ($placeholders)")->execute($toDelete);
    }
}
