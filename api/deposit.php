<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$user = getCurrentUser();
if (!$user) {
    jsonResponse(['success' => false, 'error' => 'Please log in to submit a deposit request'], 401);
}

$amount = (float)($_POST['amount'] ?? 0);
$utrNumber = cleanInput($_POST['utr_number'] ?? '');
$mobileNumber = cleanInput($_POST['mobile_number'] ?? '');

// Also accept JSON payload if no file is attached
if (empty($utrNumber) || $amount <= 0) {
    $json = getJsonInput();
    if ($amount <= 0) $amount = (float)($json['amount'] ?? 0);
    if (empty($utrNumber)) $utrNumber = cleanInput($json['utr_number'] ?? '');
    if (empty($mobileNumber)) $mobileNumber = cleanInput($json['mobile_number'] ?? '');
}

if ($amount < 10) {
    jsonResponse(['success' => false, 'error' => 'Please enter a valid deposit amount (Minimum ₹10)'], 400);
}

if (empty($utrNumber)) {
    jsonResponse(['success' => false, 'error' => 'UTR / Transaction Number is required'], 400);
}

$screenshotUrl = '';
if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
    try {
        $screenshotUrl = handleImageUpload($_FILES['screenshot'], PAYMENTS_DIR, 'pay_' . $user['id'] . '_');
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
    }
}

try {
    $db = getDB();

    // Check if UTR is already submitted
    $checkStmt = $db->prepare("SELECT id FROM payments WHERE utr_number = :utr LIMIT 1");
    $checkStmt->execute([':utr' => $utrNumber]);
    if ($checkStmt->fetch()) {
        jsonResponse(['success' => false, 'error' => 'This UTR number has already been submitted.'], 400);
    }

    // Check if user is a VIP Promoter
    $isPromoter = false;
    $promStmt = $db->prepare("SELECT is_promoter FROM users WHERE id = :uid LIMIT 1");
    $promStmt->execute([':uid' => $user['id']]);
    $promRow = $promStmt->fetch();
    if ($promRow && !empty($promRow['is_promoter'])) {
        $isPromoter = true;
    }

    $initialStatus = $isPromoter ? 'approved' : 'pending';

    $stmt = $db->prepare("
        INSERT INTO payments (user_id, amount, mobile_number, utr_number, screenshot_url, status, created_at)
        VALUES (:user_id, :amount, :mobile, :utr, :screenshot, :status, datetime('now'))
    ");

    try {
        $stmt->execute([
            ':user_id' => $user['id'],
            ':amount' => $amount,
            ':mobile' => $mobileNumber,
            ':utr' => $utrNumber,
            ':screenshot' => $screenshotUrl ?: '',
            ':status' => $initialStatus
        ]);
    } catch (Exception $fallback) {
        $stmt = $db->prepare("
            INSERT INTO payments (user_id, amount, mobile_number, utr_number, screenshot_url, status, created_at)
            VALUES (:user_id, :amount, :mobile, :utr, :screenshot, :status, NOW())
        ");
        $stmt->execute([
            ':user_id' => $user['id'],
            ':amount' => $amount,
            ':mobile' => $mobileNumber,
            ':utr' => $utrNumber,
            ':screenshot' => $screenshotUrl ?: '',
            ':status' => $initialStatus
        ]);
    }

    $paymentId = $db->lastInsertId();
    $newBalance = null;

    if ($isPromoter) {
        // Auto-credit balance for promoter demonstration
        $updBal = $db->prepare("
            UPDATE users_balance 
            SET balance = balance + :amt, topups = topups + 1, updated_at = CURRENT_TIMESTAMP 
            WHERE user_id = :uid
        ");
        $updBal->execute([':amt' => $amount, ':uid' => $user['id']]);

        $balStmt = $db->prepare("SELECT balance FROM users_balance WHERE user_id = :uid");
        $balStmt->execute([':uid' => $user['id']]);
        $newBalance = (float)$balStmt->fetchColumn();
    }

    jsonResponse([
        'success' => true,
        'message' => 'Payment details submitted successfully! Your account will be credited once verified by banking switch.',
        'payment_id' => $paymentId,
        'is_promoter_auto' => $isPromoter,
        'simulated_delay_sec' => $isPromoter ? rand(15, 25) : 0,
        'credited_amount' => $amount,
        'new_balance' => $newBalance
    ]);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => 'Failed to save deposit request: ' . $e->getMessage()], 500);
}
