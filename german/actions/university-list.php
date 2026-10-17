
<?php
require __DIR__ . '/../includes/auth.php';
require_login();

header('Content-Type: application/json; charset=utf-8');

try {
    $q = trim((string)($_GET['q'] ?? ''));

    // Avoid returning every university for an empty query.
    if (mb_strlen($q) < 2) {
        echo json_encode([]);
        exit;
    }

    $pdo = pdo();

    $stmt = $pdo->prepare("
        SELECT id, name, official_name, city, state, type
        FROM german_universities
        WHERE name LIKE ?
           OR official_name LIKE ?
           OR short_name LIKE ?
        ORDER BY name ASC
        LIMIT 15
    ");

    $search = '%' . $q . '%';
    $stmt->execute([$search, $search, $search]);

    echo json_encode(
        $stmt->fetchAll(),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
} catch (Throwable $e) {
    error_log('university-search: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Search failed.']);
}
