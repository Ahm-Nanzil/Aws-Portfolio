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

function db_create_user(string $name, string $email, string $passwordHash, string $role = 'student'): int {
    $now = db_now();
    $stmt = pdo()->prepare('INSERT INTO users (name, email, password_hash, role, status, created_at, updated_at) VALUES (?, ?, ?, ?, "active", ?, ?)');
    $stmt->execute([$name, trim(strtolower($email)), $passwordHash, $role, $now, $now]);
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
    return (int)pdo()->lastInsertId();
}

function update_university(int $userId, int $id, array $fields): bool {
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
    $sql = 'UPDATE universities SET ' . implode(', ', $sets) . ' WHERE id = ? AND user_id = ?';
    $stmt = pdo()->prepare($sql);
    $stmt->execute($vals);
    return true;
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
        'universities' => $universities,
    ];
}

/**
 * Imports a previously exported array into $userId's own account.
 * $mode is 'replace' (wipes this user's existing data first) or 'merge'
 * (adds alongside). Only ever touches rows owned by $userId.
 */
function import_user_data(int $userId, array $data, string $mode): int {
    if ($mode === 'replace') {
        foreach (get_universities($userId) as $uni) {
            delete_university($userId, (int)$uni['id']);
        }
    }
    $count = 0;
    foreach ($data['universities'] ?? [] as $uniData) {
        if (!is_array($uniData)) continue;
        $uniFields = array_merge(array_fill_keys(university_field_map(), ''), $uniData);
        $uniId = create_university($userId, $uniFields);
        foreach ($uniData['programs'] ?? [] as $progData) {
            if (!is_array($progData)) continue;
            $overview = array_merge([
                'name' => '', 'degree' => 'M.Sc.', 'subject' => '', 'department' => '', 'faculty' => '',
                'website' => '', 'description' => '', 'studyLocation' => '', 'duration' => '', 'ects' => '',
                'studyMode' => 'Full-time', 'intake' => 'Winter',
            ], $progData);
            $progId = create_program($userId, $uniId, $overview);
            foreach (['language' => default_language(), 'fees' => default_fees(), 'application' => default_application(), 'admission' => default_admission(), 'personal' => default_personal()] as $section => $defaults) {
                if (isset($progData[$section]) && is_array($progData[$section])) {
                    update_program_section($userId, $progId, $section, array_merge($defaults, $progData[$section]));
                }
            }
            if (isset($progData['documents']) && is_array($progData['documents'])) {
                $docs = [];
                foreach ($progData['documents'] as $d) {
                    $docs[] = ['id' => generate_sub_id('doc'), 'name' => $d['name'] ?? 'Document', 'status' => $d['status'] ?? 'Unknown'];
                }
                pdo()->prepare('UPDATE programs SET documents_json = ? WHERE id = ? AND user_id = ?')->execute([json_encode($docs), $progId, $userId]);
            }
            if (isset($progData['links']) && is_array($progData['links'])) {
                $links = [];
                foreach ($progData['links'] as $l) {
                    $links[] = ['id' => generate_sub_id('link'), 'title' => $l['title'] ?? '', 'url' => $l['url'] ?? '', 'description' => $l['description'] ?? ''];
                }
                pdo()->prepare('UPDATE programs SET links_json = ? WHERE id = ? AND user_id = ?')->execute([json_encode($links), $progId, $userId]);
            }
        }
        $count++;
    }
    return $count;
}
