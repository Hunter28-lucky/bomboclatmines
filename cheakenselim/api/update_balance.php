<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

$user = getCurrentUser();

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$new_balance = floatval($input['balance'] ?? 0);

if ($new_balance < 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid balance']);
    exit;
}

try {
    $user_id = $user['id'];
    
    // Update user balance in users_balance table
    $stmt = $pdo->prepare("UPDATE users_balance SET balance = :balance WHERE user_id = :user_id");
    $stmt->execute([':balance' => $new_balance, ':user_id' => $user_id]);
    
    if ($stmt->rowCount() === 0) {
        // Insert if missing
        $stmtIns = $pdo->prepare("INSERT INTO users_balance (user_id, balance, topups) VALUES (:user_id, :balance, 0)");
        $stmtIns->execute([':user_id' => $user_id, ':balance' => $new_balance]);
    }

    $updatedUser = getCurrentUser();
    $username = !empty($updatedUser['full_name']) ? $updatedUser['full_name'] : explode('@', $updatedUser['email'])[0];
    $avatar = !empty($updatedUser['avatar_url']) ? $updatedUser['avatar_url'] : 'avatar/7.png';

    echo json_encode([
        'success' => true,
        'balance' => $new_balance,
        'user' => [
            'id' => $updatedUser['id'],
            'username' => $username,
            'balance' => (float)$updatedUser['balance'],
            'avatar' => $avatar,
            'is_admin' => (int)$updatedUser['is_admin']
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to update balance: ' . $e->getMessage()]);
}
?>