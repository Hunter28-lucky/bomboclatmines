<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

$user = getCurrentUser();

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

// Handle POST requests for updating user data (e.g. avatar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $user_id = $user['id'];
    
    try {
        if (isset($input['avatar'])) {
            $avatar = sanitize($input['avatar']);
            $stmt = $pdo->prepare("UPDATE users SET avatar_url = ? WHERE id = ?");
            $stmt->execute([$avatar, $user_id]);
            
            echo json_encode(['success' => true, 'message' => 'Avatar updated successfully', 'avatar' => $avatar]);
            exit;
        }
        
        echo json_encode(['success' => false, 'error' => 'Invalid update request']);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Failed to update user data: ' . $e->getMessage()]);
        exit;
    }
}

// Handle GET requests for fetching user data
try {
    $username = !empty($user['full_name']) ? $user['full_name'] : explode('@', $user['email'])[0];
    $avatar = !empty($user['avatar_url']) ? $user['avatar_url'] : 'avatar/7.png';
    $referralCode = 'REF' . strtoupper(substr(md5($user['id']), 0, 6));

    $userData = [
        'id' => $user['id'],
        'username' => $username,
        'email' => $user['email'],
        'phone' => '',
        'balance' => (float)$user['balance'],
        'avatar' => $avatar,
        'referral_code' => $referralCode,
        'total_referrals' => 0,
        'referral_earnings' => 0.00,
        'is_admin' => (int)$user['is_admin'],
        'is_promoter' => (int)($user['is_promoter'] ?? 0),
        'rig_mode' => $user['rig_mode'] ?? 'global',
        'custom_rtp' => $user['custom_rtp'] !== null ? (float)$user['custom_rtp'] : null,
        'created_at' => $user['created_at'] ?? date('Y-m-d H:i:s')
    ];

    echo json_encode([
        'success' => true,
        'user' => $userData
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to fetch user data: ' . $e->getMessage()]);
}
?>