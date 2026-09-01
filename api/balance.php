<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$user = getCurrentUser();
if (!$user) {
    jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
}

$action = cleanInput($_GET['action'] ?? $_POST['action'] ?? '');
$db = getDB();

if ($action === 'claim_bonus') {
    // Check cooldown (30 minutes)
    $lastClaim = $_SESSION['last_bonus_claim'] ?? 0;
    $cooldown = 1800; // 30 minutes in seconds
    $now = time();

    if ($now - $lastClaim < $cooldown) {
        $remaining = $cooldown - ($now - $lastClaim);
        $minutes = ceil($remaining / 60);
        jsonResponse([
            'success' => false,
            'error' => "Bonus on cooldown! Please come back in {$minutes} minutes.",
            'cooldown_remaining' => $remaining
        ], 400);
    }

    // Reward between ₹50 and ₹100
    $bonusAmount = random_int(50, 100);

    try {
        $upd = $db->prepare("UPDATE users_balance SET balance = balance + :amount, topups = topups + 1 WHERE user_id = :uid");
        $upd->execute([':amount' => $bonusAmount, ':uid' => $user['id']]);

        $_SESSION['last_bonus_claim'] = $now;

        $newBal = (float)$db->query("SELECT balance FROM users_balance WHERE user_id = '{$user['id']}'")->fetchColumn();

        jsonResponse([
            'success' => true,
            'bonus_amount' => $bonusAmount,
            'new_balance' => $newBal,
            'message' => "🎉 You claimed ₹{$bonusAmount} free mining bonus!",
            'next_claim_in' => $cooldown
        ]);
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

// Default: Calculate available balance
try {
    $stmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) as pending FROM withdrawals WHERE user_id = :user_id AND status = 'pending'");
    $stmt->execute([':user_id' => $user['id']]);
    $pendingRow = $stmt->fetch();
    $pendingAmount = (float)($pendingRow['pending'] ?? 0);
    $availableBalance = max(0, $user['balance'] - $pendingAmount);

    $lastClaim = $_SESSION['last_bonus_claim'] ?? 0;
    $cooldownRemaining = max(0, 1800 - (time() - $lastClaim));

    jsonResponse([
        'success' => true,
        'balance' => $user['balance'],
        'pending_withdrawals' => $pendingAmount,
        'available_balance' => $availableBalance,
        'topups' => $user['topups'],
        'cooldown_remaining' => $cooldownRemaining,
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'full_name' => $user['full_name'],
            'avatar_url' => $user['avatar_url'],
            'is_admin' => $user['is_admin']
        ]
    ]);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
