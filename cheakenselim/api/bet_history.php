<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

$user = getCurrentUser();

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = $user['id'];
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;
$result_filter = sanitize($_GET['result'] ?? '');

try {
    // Build query with optional result filter
    $where_clause = "WHERE user_id = :user_id";
    $params = [':user_id' => $user_id];
    
    if (!empty($result_filter)) {
        $where_clause .= " AND result = :result";
        $params[':result'] = $result_filter;
    }
    
    // Get total count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bet_history $where_clause");
    $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();
    
    // Get bet history
    $sql = "SELECT * FROM bet_history $where_clause ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
    if (!empty($result_filter)) {
        $stmt->bindValue(':result', $result_filter, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $bets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_pages = max(1, ceil($total / $limit));
    
    echo json_encode([
        'success' => true,
        'bets' => $bets,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_records' => $total,
            'has_prev' => $page > 1,
            'has_next' => $page < $total_pages
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to fetch bet history: ' . $e->getMessage()]);
}
?>