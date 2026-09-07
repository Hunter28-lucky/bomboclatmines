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

$bet_amount = floatval($input['bet_amount'] ?? 0);
$difficulty = sanitize($input['difficulty'] ?? 'Normal');
$result = sanitize($input['result'] ?? '');
$multiplier = floatval($input['multiplier'] ?? 0);
$profit_loss = floatval($input['profit_loss'] ?? 0);
$game_data = $input['game_data'] ?? null;

// Validation
if ($bet_amount <= 0 || empty($result)) {
    echo json_encode(['success' => false, 'error' => 'Invalid bet data']);
    exit;
}

if (!in_array($result, ['cashout', 'crash'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid result']);
    exit;
}

try {
    $user_id = $user['id'];
    
    // Insert bet history
    $stmt = $pdo->prepare("INSERT INTO bet_history (user_id, bet_amount, difficulty, result, multiplier, profit_loss, game_data) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $user_id,
        $bet_amount,
        $difficulty,
        $result,
        $multiplier,
        $profit_loss,
        is_string($game_data) ? $game_data : json_encode($game_data)
    ]);
    
    $currentUser = getCurrentUser();
    $username = !empty($currentUser['full_name']) ? $currentUser['full_name'] : explode('@', $currentUser['email'])[0];
    $avatar = !empty($currentUser['avatar_url']) ? $currentUser['avatar_url'] : 'avatar/7.png';

    echo json_encode([
        'success' => true, 
        'message' => 'Bet saved successfully',
        'user' => [
            'id' => $currentUser['id'],
            'username' => $username,
            'balance' => (float)$currentUser['balance'],
            'avatar' => $avatar,
            'is_admin' => (int)$currentUser['is_admin']
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to save bet: ' . $e->getMessage()]);
}
?>