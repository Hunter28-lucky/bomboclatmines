<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

if (!isAdmin()) {
    jsonResponse(['success' => false, 'error' => 'Unauthorized admin access'], 403);
}

$action = cleanInput($_GET['action'] ?? $_POST['action'] ?? '');
$json = getJsonInput();

$db = getDB();

switch ($action) {
    case 'stats':
        try {
            $userCount = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $totalBalance = (float)$db->query("SELECT COALESCE(SUM(balance), 0) FROM users_balance")->fetchColumn();
            $pendingPayments = (int)$db->query("SELECT COUNT(*) FROM payments WHERE status = 'pending'")->fetchColumn();
            $pendingWithdrawals = (int)$db->query("SELECT COUNT(*) FROM withdrawals WHERE status = 'pending'")->fetchColumn();
            $approvedWithdrawals = (float)$db->query("SELECT COALESCE(SUM(amount), 0) FROM withdrawals WHERE status = 'approved'")->fetchColumn();
            
            // Mines Stats
            $minesGames = (int)$db->query("SELECT COUNT(*) FROM game_sessions")->fetchColumn();
            $minesWagered = (float)$db->query("SELECT COALESCE(SUM(bet_amount), 0) FROM game_sessions")->fetchColumn();
            $minesWon = (float)$db->query("SELECT COALESCE(SUM(current_winnings), 0) FROM game_sessions WHERE state = 'collected'")->fetchColumn();
            $minesProfit = $minesWagered - $minesWon;
            $minesMargin = ($minesWagered > 0) ? round(($minesProfit / $minesWagered) * 100, 2) : 0;

            // Chicken Road Stats
            $chickenGames = 0;
            $chickenWagered = 0.0;
            $chickenWon = 0.0;
            try {
                $chickenGames = (int)$db->query("SELECT COUNT(*) FROM bet_history")->fetchColumn();
                $chickenWagered = (float)$db->query("SELECT COALESCE(SUM(bet_amount), 0) FROM bet_history")->fetchColumn();
                $chickenWon = (float)$db->query("SELECT COALESCE(SUM(CASE WHEN result = 'cashout' THEN (bet_amount + profit_loss) ELSE 0 END), 0) FROM bet_history")->fetchColumn();
            } catch (Exception $e) {}
            $chickenProfit = $chickenWagered - $chickenWon;
            $chickenMargin = ($chickenWagered > 0) ? round(($chickenProfit / $chickenWagered) * 100, 2) : 0;

            // Platform Aggregates
            $totalGames = $minesGames + $chickenGames;
            $totalWagered = $minesWagered + $chickenWagered;
            $totalWon = $minesWon + $chickenWon;
            $totalHouseProfit = $totalWagered - $totalWon;
            $mostProfitable = ($chickenProfit > $minesProfit && $chickenWagered > 0) ? 'Chicken Cross Road' : 'Bombaclat Mines';

            $rtp = (float)getSystemSetting('rtp_rate', 97.0);
            $houseEdge = (float)getSystemSetting('house_edge', 3.0);
            $rigMode = getSystemSetting('game_rig_mode', 'fair');

            jsonResponse([
                'success' => true,
                'stats' => [
                    'total_users' => $userCount,
                    'total_balance' => $totalBalance,
                    'pending_payments' => $pendingPayments,
                    'pending_withdrawals' => $pendingWithdrawals,
                    'total_withdrawn' => $approvedWithdrawals,
                    'total_games' => $totalGames,
                    'total_wagered' => $totalWagered,
                    'total_won' => $totalWon,
                    'total_house_profit' => $totalHouseProfit,
                    'house_profit' => $totalHouseProfit,
                    'most_profitable_game' => $mostProfitable,
                    'rtp_rate' => $rtp,
                    'house_edge' => $houseEdge,
                    'game_rig_mode' => $rigMode,
                    // Breakdown
                    'mines' => [
                        'games_played' => $minesGames,
                        'games_count' => $minesGames,
                        'total_wagered' => $minesWagered,
                        'wagered' => $minesWagered,
                        'total_won' => $minesWon,
                        'won' => $minesWon,
                        'net_profit' => $minesProfit,
                        'profit' => $minesProfit,
                        'margin_pct' => $minesMargin
                    ],
                    'chicken' => [
                        'games_played' => $chickenGames,
                        'games_count' => $chickenGames,
                        'total_wagered' => $chickenWagered,
                        'wagered' => $chickenWagered,
                        'total_won' => $chickenWon,
                        'won' => $chickenWon,
                        'net_profit' => $chickenProfit,
                        'profit' => $chickenProfit,
                        'margin_pct' => $chickenMargin
                    ]
                ]
            ]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'users':
        try {
            $search = cleanInput($_GET['search'] ?? '');
            $sql = "
                SELECT u.id as user_id, u.email, u.full_name, u.avatar_url, u.is_admin, u.is_banned, u.ban_reason,
                       COALESCE(u.is_promoter, 0) as is_promoter,
                       u.custom_rtp, COALESCE(u.rig_mode, 'global') as rig_mode, u.created_at,
                       COALESCE(b.balance, 0) as balance, COALESCE(b.topups, 0) as topups,
                       (SELECT COUNT(*) FROM game_sessions g WHERE g.user_id = u.id) as mines_played,
                       (SELECT COALESCE(SUM(bet_amount), 0) FROM game_sessions g WHERE g.user_id = u.id) as mines_wagered,
                       (SELECT COALESCE(SUM(current_winnings), 0) FROM game_sessions g WHERE g.user_id = u.id AND g.state = 'collected') as mines_won,
                       (SELECT COUNT(*) FROM bet_history bh WHERE bh.user_id = u.id) as chicken_played,
                       (SELECT COALESCE(SUM(bet_amount), 0) FROM bet_history bh WHERE bh.user_id = u.id) as chicken_wagered,
                       (SELECT COALESCE(SUM(CASE WHEN bh.result = 'cashout' THEN (bh.bet_amount + bh.profit_loss) ELSE 0 END), 0) FROM bet_history bh WHERE bh.user_id = u.id) as chicken_won
                FROM users u
                LEFT JOIN users_balance b ON u.id = b.user_id
            ";
            if (!empty($search)) {
                $sql .= " WHERE LOWER(u.email) LIKE :search OR LOWER(u.full_name) LIKE :search ";
            }
            $sql .= " ORDER BY u.created_at DESC";

            $stmt = $db->prepare($sql);
            if (!empty($search)) {
                $stmt->execute([':search' => '%' . strtolower($search) . '%']);
            } else {
                $stmt->execute();
            }

            $users = $stmt->fetchAll();
            foreach ($users as &$u) {
                $u['balance'] = (float)$u['balance'];
                $u['topups'] = (int)$u['topups'];
                $u['is_banned'] = (int)($u['is_banned'] ?? 0);
                $u['is_promoter'] = (int)($u['is_promoter'] ?? 0);
                $u['custom_rtp'] = $u['custom_rtp'] !== null ? (float)$u['custom_rtp'] : null;
                $u['rig_mode'] = $u['rig_mode'] ?: 'global';
                
                $minesPlayed = (int)($u['mines_played'] ?? 0);
                $chickenPlayed = (int)($u['chicken_played'] ?? 0);
                $u['games_played'] = $minesPlayed + $chickenPlayed;

                $minesWagered = (float)($u['mines_wagered'] ?? 0);
                $chickenWagered = (float)($u['chicken_wagered'] ?? 0);
                $u['total_wagered'] = $minesWagered + $chickenWagered;

                $minesWon = (float)($u['mines_won'] ?? 0);
                $chickenWon = (float)($u['chicken_won'] ?? 0);
                $u['total_won'] = $minesWon + $chickenWon;

                $u['net_profit'] = $u['total_wagered'] - $u['total_won']; // House profit from this user
            }

            jsonResponse(['success' => true, 'users' => $users]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'update_user_rig':
        $userId = cleanInput($json['user_id'] ?? $_POST['user_id'] ?? '');
        $rigMode = cleanInput($json['rig_mode'] ?? $_POST['rig_mode'] ?? 'global');
        $customRtp = isset($json['custom_rtp']) && $json['custom_rtp'] !== '' ? (float)$json['custom_rtp'] : null;

        if (empty($userId)) {
            jsonResponse(['success' => false, 'error' => 'User ID is required'], 400);
        }

        $validRigModes = ['global', 'fair', 'house_favored', 'high_win', 'force_lose', 'force_win'];
        if (!in_array($rigMode, $validRigModes, true)) {
            $rigMode = 'global';
        }

        try {
            $stmt = $db->prepare("UPDATE users SET rig_mode = :rig, custom_rtp = :rtp WHERE id = :uid");
            $stmt->execute([':rig' => $rigMode, ':rtp' => $customRtp, ':uid' => $userId]);
            jsonResponse(['success' => true, 'message' => "User game control updated: {$rigMode}"]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'add_money':
        $userId = cleanInput($json['user_id'] ?? $_POST['user_id'] ?? '');
        $amount = (float)($json['amount'] ?? $_POST['amount'] ?? 0);

        if (empty($userId) || $amount <= 0) {
            jsonResponse(['success' => false, 'error' => 'Valid user ID and positive amount required'], 400);
        }

        try {
            $stmt = $db->prepare("UPDATE users_balance SET balance = balance + :amount, topups = topups + 1 WHERE user_id = :uid");
            $stmt->execute([':amount' => $amount, ':uid' => $userId]);
            jsonResponse(['success' => true, 'message' => "₹{$amount} added to user balance successfully"]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'deduct_money':
        $userId = cleanInput($json['user_id'] ?? $_POST['user_id'] ?? '');
        $amount = (float)($json['amount'] ?? $_POST['amount'] ?? 0);

        if (empty($userId) || $amount <= 0) {
            jsonResponse(['success' => false, 'error' => 'Valid user ID and positive amount required'], 400);
        }

        try {
            $stmt = $db->prepare("UPDATE users_balance SET balance = CASE WHEN balance >= :amount THEN balance - :amount ELSE 0 END WHERE user_id = :uid");
            $stmt->execute([':amount' => $amount, ':uid' => $userId]);
            jsonResponse(['success' => true, 'message' => "₹{$amount} deducted from user balance"]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'update_balance':
        $userId = cleanInput($json['user_id'] ?? $_POST['user_id'] ?? '');
        $newBalance = (float)($json['balance'] ?? $_POST['balance'] ?? 0);

        if (empty($userId)) {
            jsonResponse(['success' => false, 'error' => 'User ID is required'], 400);
        }

        try {
            $stmt = $db->prepare("UPDATE users_balance SET balance = :balance WHERE user_id = :uid");
            $stmt->execute([':balance' => $newBalance, ':uid' => $userId]);
            jsonResponse(['success' => true, 'message' => 'Balance updated successfully']);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'toggle_ban':
        $userId = cleanInput($json['user_id'] ?? $_POST['user_id'] ?? '');
        $isBanned = (int)($json['is_banned'] ?? $_POST['is_banned'] ?? 0);
        $reason = cleanInput($json['ban_reason'] ?? $_POST['ban_reason'] ?? '');

        if (empty($userId)) {
            jsonResponse(['success' => false, 'error' => 'User ID is required'], 400);
        }

        try {
            $stmt = $db->prepare("UPDATE users SET is_banned = :banned, ban_reason = :reason WHERE id = :uid");
            $stmt->execute([':banned' => $isBanned, ':reason' => $reason, ':uid' => $userId]);
            jsonResponse(['success' => true, 'message' => $isBanned ? 'User has been banned' : 'User unbanned']);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'reset_password':
        $userId = cleanInput($json['user_id'] ?? $_POST['user_id'] ?? '');
        $newPass = trim($json['new_password'] ?? $_POST['new_password'] ?? '');

        if (empty($userId) || strlen($newPass) < 6) {
            jsonResponse(['success' => false, 'error' => 'Password must be at least 6 characters'], 400);
        }

        try {
            $hash = password_hash($newPass, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE users SET password_hash = :hash WHERE id = :uid");
            $stmt->execute([':hash' => $hash, ':uid' => $userId]);
            jsonResponse(['success' => true, 'message' => 'Password reset successfully']);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'toggle_promoter':
        $userId = cleanInput($json['user_id'] ?? $_POST['user_id'] ?? '');
        $isPromoter = (int)($json['is_promoter'] ?? $_POST['is_promoter'] ?? 0);

        if (empty($userId)) {
            jsonResponse(['success' => false, 'error' => 'User ID is required'], 400);
        }

        try {
            $stmt = $db->prepare("UPDATE users SET is_promoter = :promoter WHERE id = :uid");
            $stmt->execute([':promoter' => $isPromoter ? 1 : 0, ':uid' => $userId]);
            jsonResponse([
                'success' => true,
                'message' => $isPromoter ? 'User upgraded to VIP Promoter (Stealth Safe-Path & Auto-Deposit active)' : 'User removed from VIP Promoter mode'
            ]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'delete_user':
        $userId = cleanInput($json['user_id'] ?? $_POST['user_id'] ?? '');
        if (empty($userId)) {
            jsonResponse(['success' => false, 'error' => 'User ID is required'], 400);
        }

        try {
            $stmt = $db->prepare("DELETE FROM users WHERE id = :uid");
            $stmt->execute([':uid' => $userId]);
            jsonResponse(['success' => true, 'message' => 'User deleted successfully']);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'get_settings':
        $settings = getAllSystemSettings();
        jsonResponse(['success' => true, 'settings' => $settings]);
        break;

    case 'update_settings':
        $allowedKeys = [
            'rtp_rate', 'house_edge', 'game_rig_mode', 'min_bet', 'max_bet',
            'min_withdrawal', 'welcome_bonus', 'announcement', 'upi_id'
        ];

        $payload = !empty($json) ? $json : $_POST;
        foreach ($payload as $key => $val) {
            if (in_array($key, $allowedKeys, true)) {
                // Keep rtp_rate and house_edge synchronized
                if ($key === 'rtp_rate') {
                    $rtpVal = max(50.0, min(99.0, (float)$val));
                    setSystemSetting('rtp_rate', (string)$rtpVal);
                    setSystemSetting('house_edge', (string)(100.0 - $rtpVal));
                    try {
                        $db->prepare("INSERT INTO admin_settings (setting_key, setting_value) VALUES ('target_rtp', :v) ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value")->execute([':v' => (string)$rtpVal]);
                        $db->prepare("INSERT INTO admin_settings (setting_key, setting_value) VALUES ('rtp_rate', :v) ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value")->execute([':v' => (string)$rtpVal]);
                    } catch (Exception $e) {}
                } elseif ($key === 'house_edge') {
                    $edgeVal = max(1.0, min(50.0, (float)$val));
                    setSystemSetting('house_edge', (string)$edgeVal);
                    setSystemSetting('rtp_rate', (string)(100.0 - $edgeVal));
                    try {
                        $db->prepare("INSERT INTO admin_settings (setting_key, setting_value) VALUES ('target_rtp', :v) ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value")->execute([':v' => (string)(100.0 - $edgeVal)]);
                        $db->prepare("INSERT INTO admin_settings (setting_key, setting_value) VALUES ('rtp_rate', :v) ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value")->execute([':v' => (string)(100.0 - $edgeVal)]);
                    } catch (Exception $e) {}
                } else {
                    setSystemSetting($key, (string)$val);
                }
            }
        }
        jsonResponse(['success' => true, 'message' => 'Settings saved successfully', 'settings' => getAllSystemSettings()]);
        break;

    case 'upload_qr':
        try {
            if (!isset($_FILES['qr_image'])) {
                jsonResponse(['success' => false, 'error' => 'No image file uploaded'], 400);
            }
            $targetDir = BASE_DIR . '/assets/images';
            $uploadedPath = handleImageUpload($_FILES['qr_image'], $targetDir, 'qr_');
            if ($uploadedPath) {
                // Also copy as qrpayment.jpeg for standard fallback
                copy(BASE_DIR . '/' . $uploadedPath, $targetDir . '/qrpayment.jpeg');
                setSystemSetting('upi_qr_image', $uploadedPath);
                jsonResponse(['success' => true, 'message' => 'QR Code updated successfully', 'path' => $uploadedPath]);
            } else {
                jsonResponse(['success' => false, 'error' => 'Failed to save QR image'], 500);
            }
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
        }
        break;

    case 'payments':
        try {
            $stmt = $db->query("
                SELECT p.*, u.email as user_email, u.full_name as user_name, b.balance as current_balance
                FROM payments p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN users_balance b ON u.id = b.user_id
                ORDER BY p.created_at DESC
            ");
            jsonResponse(['success' => true, 'payments' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'approve_payment':
    case 'reject_payment':
    case 'process_payment':
        $paymentId = (int)($json['payment_id'] ?? $_POST['payment_id'] ?? 0);
        $status = ($action === 'approve_payment') ? 'approved' : (($action === 'reject_payment') ? 'rejected' : cleanInput($json['status'] ?? $_POST['status'] ?? ''));
        $creditAmount = (float)($json['credit_amount'] ?? $json['amount'] ?? $_POST['credit_amount'] ?? $_POST['amount'] ?? 0);

        if ($paymentId <= 0 || !in_array($status, ['approved', 'rejected'], true)) {
            jsonResponse(['success' => false, 'error' => 'Invalid payment ID or status'], 400);
        }

        try {
            $db->beginTransaction();
            $stmt = $db->prepare("SELECT * FROM payments WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $paymentId]);
            $payment = $stmt->fetch();

            if (!$payment) {
                $db->rollBack();
                jsonResponse(['success' => false, 'error' => 'Payment record not found'], 404);
            }

            if ($status === 'approved') {
                $finalCredit = ($creditAmount > 0) ? $creditAmount : (float)($payment['amount'] ?? 0);
                if ($finalCredit > 0) {
                    $balStmt = $db->prepare("UPDATE users_balance SET balance = balance + :amount, topups = topups + 1 WHERE user_id = :uid");
                    $balStmt->execute([':amount' => $finalCredit, ':uid' => $payment['user_id']]);
                }
            }

            $upd = $db->prepare("UPDATE payments SET status = :st, reviewed_at = datetime('now') WHERE id = :id");
            try {
                $upd->execute([':st' => $status, ':id' => $paymentId]);
            } catch (Exception $fallback) {
                $upd = $db->prepare("UPDATE payments SET status = :st, reviewed_at = NOW() WHERE id = :id");
                $upd->execute([':st' => $status, ':id' => $paymentId]);
            }

            $db->commit();
            jsonResponse(['success' => true, 'message' => "Payment marked as {$status}"]);
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'withdrawals':
        try {
            $stmt = $db->query("
                SELECT w.*, u.email as user_email, u.full_name as user_name, b.balance as current_balance
                FROM withdrawals w
                JOIN users u ON w.user_id = u.id
                LEFT JOIN users_balance b ON u.id = b.user_id
                ORDER BY w.created_at DESC
            ");
            jsonResponse(['success' => true, 'withdrawals' => $stmt->fetchAll()]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'approve_withdrawal':
    case 'reject_withdrawal':
    case 'process_withdrawal':
        $withdrawalId = cleanInput($json['withdrawal_id'] ?? $_POST['withdrawal_id'] ?? '');
        $status = ($action === 'approve_withdrawal') ? 'approved' : (($action === 'reject_withdrawal') ? 'rejected' : cleanInput($json['status'] ?? $_POST['status'] ?? ''));
        $adminNote = cleanInput($json['admin_note'] ?? $_POST['admin_note'] ?? '');

        if (empty($withdrawalId) || !in_array($status, ['approved', 'rejected'], true)) {
            jsonResponse(['success' => false, 'error' => 'Invalid parameters'], 400);
        }

        try {
            $db->beginTransaction();
            $stmt = $db->prepare("SELECT * FROM withdrawals WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $withdrawalId]);
            $withdrawal = $stmt->fetch();

            if (!$withdrawal) {
                $db->rollBack();
                jsonResponse(['success' => false, 'error' => 'Withdrawal not found'], 404);
            }

            if ($withdrawal['status'] !== 'pending') {
                $db->rollBack();
                jsonResponse(['success' => false, 'error' => 'Withdrawal is already processed'], 400);
            }

            if ($status === 'approved') {
                $deduct = $db->prepare("UPDATE users_balance SET balance = balance - :amount WHERE user_id = :uid AND balance >= :amount");
                $deduct->execute([':amount' => $withdrawal['amount'], ':uid' => $withdrawal['user_id']]);
                if ($deduct->rowCount() === 0) {
                    $db->rollBack();
                    jsonResponse(['success' => false, 'error' => 'User has insufficient balance to complete withdrawal'], 400);
                }
            }

            $upd = $db->prepare("UPDATE withdrawals SET status = :st, admin_note = :note, processed_at = datetime('now') WHERE id = :id");
            try {
                $upd->execute([':st' => $status, ':note' => $adminNote, ':id' => $withdrawalId]);
            } catch (Exception $fallback) {
                $upd = $db->prepare("UPDATE withdrawals SET status = :st, admin_note = :note, processed_at = NOW() WHERE id = :id");
                $upd->execute([':st' => $status, ':note' => $adminNote, ':id' => $withdrawalId]);
            }

            $db->commit();
            jsonResponse(['success' => true, 'message' => "Withdrawal {$status} successfully"]);
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'support_tickets':
        try {
            $selectedUserId = cleanInput($_GET['user_id'] ?? '');

            // Fetch list of users who have chatted
            $threadsStmt = $db->query("
                SELECT sm.user_id, u.email as user_email, u.full_name as user_name, b.balance,
                       MAX(sm.created_at) as last_activity,
                       MAX(sm.is_escalated) as has_escalation,
                       (SELECT message FROM support_messages WHERE user_id = sm.user_id ORDER BY created_at DESC LIMIT 1) as last_message,
                       (SELECT sender FROM support_messages WHERE user_id = sm.user_id ORDER BY created_at DESC LIMIT 1) as last_sender
                FROM support_messages sm
                JOIN users u ON sm.user_id = u.id
                LEFT JOIN users_balance b ON u.id = b.user_id
                GROUP BY sm.user_id, u.email, u.full_name, b.balance
                ORDER BY has_escalation DESC, last_activity DESC
            ");
            $threads = $threadsStmt->fetchAll();

            $activeThread = [];
            if (!empty($selectedUserId) || !empty($threads[0]['user_id'])) {
                $targetUid = !empty($selectedUserId) ? $selectedUserId : $threads[0]['user_id'];
                $msgStmt = $db->prepare("
                    SELECT id, user_id, sender, message, is_escalated, created_at 
                    FROM support_messages 
                    WHERE user_id = :uid 
                    ORDER BY created_at ASC
                ");
                $msgStmt->execute([':uid' => $targetUid]);
                $activeThread = $msgStmt->fetchAll();
            }

            jsonResponse([
                'success' => true,
                'threads' => $threads,
                'active_thread' => $activeThread,
                'selected_user_id' => !empty($selectedUserId) ? $selectedUserId : ($threads[0]['user_id'] ?? null)
            ]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'send_support_reply':
        $targetUserId = cleanInput($json['user_id'] ?? $_POST['user_id'] ?? '');
        $replyText = trim($json['message'] ?? $_POST['message'] ?? '');

        if (empty($targetUserId) || empty($replyText)) {
            jsonResponse(['success' => false, 'error' => 'User ID and message cannot be empty'], 400);
        }

        try {
            $stmt = $db->prepare("
                INSERT INTO support_messages (user_id, sender, message, is_escalated, created_at)
                VALUES (:uid, 'admin', :msg, 0, datetime('now'))
            ");
            try {
                $stmt->execute([':uid' => $targetUserId, ':msg' => $replyText]);
            } catch (Exception $fallback) {
                $stmt = $db->prepare("
                    INSERT INTO support_messages (user_id, sender, message, is_escalated, created_at)
                    VALUES (:uid, 'admin', :msg, 0, NOW())
                ");
                $stmt->execute([':uid' => $targetUserId, ':msg' => $replyText]);
            }

            jsonResponse(['success' => true, 'message' => 'Admin reply sent successfully']);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'recent_bets':
        try {
            $gameFilter = cleanInput($_GET['game'] ?? 'all');
            $limit = min(100, max(10, (int)($_GET['limit'] ?? 50)));

            $bets = [];

            // 1. Mines bets from game_sessions
            if ($gameFilter === 'all' || $gameFilter === 'mines') {
                $minesStmt = $db->query("
                    SELECT g.id, g.user_id, 'mines' as game, g.bet_amount, 
                           (CASE WHEN g.bet_amount > 0 AND g.state = 'collected' THEN ROUND(g.current_winnings / g.bet_amount, 2) ELSE 0.0 END) as multiplier,
                           (CASE WHEN g.state = 'collected' THEN g.current_winnings ELSE 0 END) as payout,
                           g.bomb_count || ' Mines (' || g.grid_size || 'x' || g.grid_size || ')' as details,
                           g.state as result, g.created_at,
                           u.email as user_email, u.full_name as user_name
                    FROM game_sessions g
                    LEFT JOIN users u ON g.user_id = u.id
                    WHERE g.state IN ('collected', 'exploded')
                    ORDER BY g.created_at DESC
                    LIMIT {$limit}
                ");
                while ($r = $minesStmt->fetch(PDO::FETCH_ASSOC)) {
                    $r['bet_amount'] = (float)$r['bet_amount'];
                    $r['multiplier'] = (float)$r['multiplier'];
                    $r['payout'] = (float)$r['payout'];
                    $r['house_profit'] = (float)$r['bet_amount'] - (float)$r['payout'];
                    $r['status'] = ($r['result'] === 'collected') ? 'won' : 'lost';
                    $bets[] = $r;
                }
            }

            // 2. Chicken Road bets from bet_history
            if ($gameFilter === 'all' || $gameFilter === 'chicken') {
                try {
                    $chkStmt = $db->query("
                        SELECT bh.id, bh.user_id, 'chicken' as game, bh.bet_amount,
                               bh.multiplier, 
                               (CASE WHEN bh.result = 'cashout' THEN (bh.bet_amount + bh.profit_loss) ELSE 0 END) as payout,
                               bh.profit_loss,
                               bh.difficulty as details,
                               bh.result, bh.created_at,
                               u.email as user_email, u.full_name as user_name
                        FROM bet_history bh
                        LEFT JOIN users u ON bh.user_id = u.id
                        ORDER BY bh.created_at DESC
                        LIMIT {$limit}
                    ");
                    while ($r = $chkStmt->fetch(PDO::FETCH_ASSOC)) {
                        $r['bet_amount'] = (float)$r['bet_amount'];
                        $r['multiplier'] = (float)str_replace('x', '', (string)$r['multiplier']);
                        $r['payout'] = (float)$r['payout'];
                        $r['house_profit'] = (float)$r['bet_amount'] - (float)$r['payout'];
                        $r['status'] = ($r['result'] === 'cashout') ? 'won' : 'lost';
                        $bets[] = $r;
                    }
                } catch (Exception $e) {}
            }

            // Sort merged bets by created_at DESC
            usort($bets, function($a, $b) {
                return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
            });

            if (count($bets) > $limit) {
                $bets = array_slice($bets, 0, $limit);
            }

            jsonResponse(['success' => true, 'bets' => $bets]);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    case 'chicken_settings':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $stmt = $db->query("SELECT setting_key, setting_value FROM admin_settings");
                $settings = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $settings[$row['setting_key']] = $row['setting_value'];
                }
                jsonResponse([
                    'success' => true,
                    'settings' => [
                        'controlled_crash_enabled' => ($settings['controlled_crash_enabled'] ?? 'false') === 'true',
                        'crash_level_easy' => $settings['crash_level_easy'] ?? 'random',
                        'crash_level_medium' => $settings['crash_level_medium'] ?? 'random',
                        'crash_level_hard' => $settings['crash_level_hard'] ?? 'random',
                        'crash_level_hardcore' => $settings['crash_level_hardcore'] ?? 'random',
                        'controlled_crash_rectangle' => (int)($settings['controlled_crash_rectangle'] ?? 10),
                        'min_bet' => (float)($settings['min_bet'] ?? 10),
                        'max_bet' => (float)($settings['max_bet'] ?? 10000),
                        'preset_btn_1' => (float)($settings['preset_btn_1'] ?? 10),
                        'preset_btn_2' => (float)($settings['preset_btn_2'] ?? 50),
                        'preset_btn_3' => (float)($settings['preset_btn_3'] ?? 100),
                        'preset_btn_4' => (float)($settings['preset_btn_4'] ?? 500),
                        'maintenance_mode' => ($settings['maintenance_mode'] ?? 'false') === 'true',
                        'target_rtp' => (float)($settings['target_rtp'] ?? 96.5),
                        'chicken_target_rtp' => (float)($settings['chicken_target_rtp'] ?? $settings['target_rtp'] ?? 96.5),
                        'rtp_easy' => isset($settings['rtp_easy']) && $settings['rtp_easy'] !== '' ? (float)$settings['rtp_easy'] : '',
                        'rtp_medium' => isset($settings['rtp_medium']) && $settings['rtp_medium'] !== '' ? (float)$settings['rtp_medium'] : '',
                        'rtp_hard' => isset($settings['rtp_hard']) && $settings['rtp_hard'] !== '' ? (float)$settings['rtp_hard'] : '',
                        'rtp_hardcore' => isset($settings['rtp_hardcore']) && $settings['rtp_hardcore'] !== '' ? (float)$settings['rtp_hardcore'] : ''
                    ]
                ]);
            } catch (Exception $e) {
                jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
            }
        } else {
            // POST / UPDATE
            try {
                $payload = !empty($json) ? $json : $_POST;
                $updStmt = $db->prepare("
                    INSERT INTO admin_settings (setting_key, setting_value) 
                    VALUES (:key, :val) 
                    ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value, updated_at = CURRENT_TIMESTAMP
                ");

                foreach ($payload as $key => $val) {
                    $strVal = is_bool($val) ? ($val ? 'true' : 'false') : (string)$val;
                    try {
                        $updStmt->execute([':key' => $key, ':val' => $strVal]);
                    } catch (Exception $fallback) {
                        $mysqlUpd = $db->prepare("
                            INSERT INTO admin_settings (setting_key, setting_value) 
                            VALUES (:key, :val) 
                            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP
                        ");
                        $mysqlUpd->execute([':key' => $key, ':val' => $strVal]);
                    }
                }
                jsonResponse(['success' => true, 'message' => 'Chicken Road game settings updated successfully!']);
            } catch (Exception $e) {
                jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }
        break;

    case 'change_password':
        $currentPass = $json['current_password'] ?? $_POST['current_password'] ?? '';
        $newPass = $json['new_password'] ?? $_POST['new_password'] ?? '';
        $confirmPass = $json['confirm_password'] ?? $_POST['confirm_password'] ?? '';

        if (empty($currentPass) || empty($newPass)) {
            jsonResponse(['success' => false, 'error' => 'Please fill all password fields'], 400);
        }

        if (strlen($newPass) < 6) {
            jsonResponse(['success' => false, 'error' => 'New password must be at least 6 characters'], 400);
        }

        if ($newPass !== $confirmPass) {
            jsonResponse(['success' => false, 'error' => 'New password and confirmation do not match'], 400);
        }

        $adminUser = getCurrentUser();
        if (!$adminUser) {
            jsonResponse(['success' => false, 'error' => 'Session expired'], 401);
        }

        try {
            $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $adminUser['id']]);
            $hash = $stmt->fetchColumn();

            if (!$hash || !password_verify($currentPass, $hash)) {
                jsonResponse(['success' => false, 'error' => 'Incorrect current password'], 400);
            }

            $newHash = password_hash($newPass, PASSWORD_BCRYPT);
            $updateStmt = $db->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
            $updateStmt->execute([':hash' => $newHash, ':id' => $adminUser['id']]);

            jsonResponse(['success' => true, 'message' => 'Admin password updated successfully!']);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
        break;

    default:
        jsonResponse(['success' => false, 'error' => 'Invalid admin action'], 400);
        break;
}
