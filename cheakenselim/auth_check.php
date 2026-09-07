<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

$user = getCurrentUser();

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$username = !empty($user['full_name']) ? $user['full_name'] : explode('@', $user['email'])[0];
$avatar = !empty($user['avatar_url']) ? $user['avatar_url'] : 'avatar/7.png';
$referralCode = 'REF' . strtoupper(substr(md5($user['id']), 0, 6));

echo json_encode([
    'success' => true,
    'user' => [
        'id' => $user['id'],
        'username' => $username,
        'email' => $user['email'],
        'phone' => '',
        'balance' => (float)$user['balance'],
        'avatar' => $avatar,
        'referral_code' => $referralCode,
        'is_admin' => (int)$user['is_admin']
    ]
]);
?>