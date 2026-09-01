<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$user = getCurrentUser();
if (!$user) {
    jsonResponse(['success' => false, 'error' => 'Please log in to manage withdrawals'], 401);
}

$action = cleanInput($_GET['action'] ?? $_POST['action'] ?? '');
$json = getJsonInput();

switch ($action) {
    case 'request':
        $amount = (float)($json['amount'] ?? $_POST['amount'] ?? 0);
        $upiId = cleanInput($json['upi_id'] ?? $_POST['upi_id'] ?? '');
        $mobileNumber = cleanInput($json['mobile_number'] ?? $_POST['mobile_number'] ?? '');

        if ($amount < MIN_WITHDRAWAL_AMOUNT) {
            jsonResponse(['success' => false, 'error' => 'Minimum withdrawal amount is ₹' . MIN_WITHDRAWAL_AMOUNT], 400);
        }

        if (empty($upiId)) {
            jsonResponse(['success' => false, 'error' => 'Please enter a valid UPI ID'], 400);
        }

        if (empty($mobileNumber) || !preg_match('/^[0-9]{10}$/', $mobileNumber)) {
            jsonResponse(['success' => false, 'error' => 'Please enter a valid 10-digit mobile number'], 400);
        }

        try {
            $db = getDB();
            $db->beginTransaction();

            // Fetch current balance & pending withdrawals
            $forUpdate = ($db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') ? ' FOR UPDATE' : '';
            $balStmt = $db->prepare("SELECT balance FROM users_balance WHERE user_id = :user_id" . $forUpdate);
            $balStmt->execute([':user_id' => $user['id']]);
            $balRow = $balStmt->fetch();

            if (!$balRow) {
                $db->rollBack();
                jsonResponse(['success' => false, 'error' => 'User balance record not found'], 404);
            }

            $currentBalance = (float)$balRow['balance'];

            $pendStmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) as pending FROM withdrawals WHERE user_id = :user_id AND status = 'pending'");
            $pendStmt->execute([':user_id' => $user['id']]);
            $pendRow = $pendStmt->fetch();
            $pendingTotal = (float)($pendRow['pending'] ?? 0);

            $availableBalance = $currentBalance - $pendingTotal;

            if ($availableBalance < $amount) {
                $db->rollBack();
                jsonResponse([
                    'success' => false, 
                    'error' => "Insufficient available balance. Available: ₹{$availableBalance}, Requested: ₹{$amount}"
                ], 400);
            }

            $withdrawalId = generateUUID();
            $insStmt = $db->prepare("
                INSERT INTO withdrawals (id, user_id, amount, upi_id, mobile_number, status, created_at)
                VALUES (:id, :user_id, :amount, :upi, :mobile, 'pending', datetime('now'))
            ");

            try {
                $insStmt->execute([
                    ':id' => $withdrawalId,
                    ':user_id' => $user['id'],
                    ':amount' => $amount,
                    ':upi' => $upiId,
                    ':mobile' => $mobileNumber
                ]);
            } catch (Exception $fallback) {
                $insStmt = $db->prepare("
                    INSERT INTO withdrawals (id, user_id, amount, upi_id, mobile_number, status, created_at)
                    VALUES (:id, :user_id, :amount, :upi, :mobile, 'pending', NOW())
                ");
                $insStmt->execute([
                    ':id' => $withdrawalId,
                    ':user_id' => $user['id'],
                    ':amount' => $amount,
                    ':upi' => $upiId,
                    ':mobile' => $mobileNumber
                ]);
            }

            $db->commit();

            jsonResponse([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully. It will be reviewed by admin shortly.',
                'withdrawal_id' => $withdrawalId
            ]);
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            jsonResponse(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
        }
        break;

    case 'history':
    default:
        try {
            $db = getDB();
            $stmt = $db->prepare("
                SELECT id, amount, upi_id, mobile_number, status, admin_note, created_at, processed_at 
                FROM withdrawals 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC
            ");
            $stmt->execute([':user_id' => $user['id']]);
            $withdrawals = $stmt->fetchAll();

            // Format amounts
            foreach ($withdrawals as &$w) {
                $w['amount'] = (float)$w['amount'];
            }

            jsonResponse([
                'success' => true,
                'withdrawals' => $withdrawals
            ]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;
}
