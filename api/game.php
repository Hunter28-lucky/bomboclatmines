<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/game_engine.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$action = cleanInput($_GET['action'] ?? $_POST['action'] ?? '');
$json = getJsonInput();

if ($action === 'ladder') {
    $gridSize = (int)($json['grid_size'] ?? $_GET['grid_size'] ?? 25);
    $bombCount = (int)($json['bomb_count'] ?? $_GET['bomb_count'] ?? 3);
    $ladder = getMultiplierLadder($gridSize, $bombCount);
    jsonResponse([
        'success' => true,
        'grid_size' => $gridSize,
        'bomb_count' => $bombCount,
        'total_gems' => $gridSize - $bombCount,
        'ladder' => $ladder
    ]);
}

$user = getCurrentUser();
if (!$user) {
    jsonResponse(['success' => false, 'error' => 'Please log in to play'], 401);
}

switch ($action) {
    case 'start':
        $betAmount = (float)($json['bet_amount'] ?? $_POST['bet_amount'] ?? 100);
        $gridSize = (int)($json['grid_size'] ?? $_POST['grid_size'] ?? 25);
        $bombCount = (int)($json['bomb_count'] ?? $_POST['bomb_count'] ?? 5);

        $result = startNewGameSession($user['id'], $betAmount, $gridSize, $bombCount);
        jsonResponse($result, $result['success'] ? 200 : 400);
        break;

    case 'move':
    case 'reveal':
        $sessionId = cleanInput($json['session_id'] ?? $_POST['session_id'] ?? '');
        $tileIndex = (int)($json['tile_index'] ?? $_POST['tile_index'] ?? -1);

        if (empty($sessionId) || $tileIndex < 0) {
            jsonResponse(['success' => false, 'error' => 'Invalid session or tile parameters'], 400);
        }

        $result = processTileReveal($sessionId, $user['id'], $tileIndex);
        jsonResponse($result, $result['success'] ? 200 : 400);
        break;

    case 'cashout':
        $sessionId = cleanInput($json['session_id'] ?? $_POST['session_id'] ?? '');
        if (empty($sessionId)) {
            jsonResponse(['success' => false, 'error' => 'Invalid session ID'], 400);
        }

        $result = cashOutWinnings($sessionId, $user['id']);
        jsonResponse($result, $result['success'] ? 200 : 400);
        break;

    case 'autopick':
        $sessionId = cleanInput($json['session_id'] ?? $_POST['session_id'] ?? '');
        if (empty($sessionId)) {
            jsonResponse(['success' => false, 'error' => 'Invalid session ID'], 400);
        }

        $result = processAutoPick($sessionId, $user['id']);
        jsonResponse($result, $result['success'] ? 200 : 400);
        break;

    case 'toggle_promoter_stealth':
        $profile = getUserRigProfile($user['id']);
        if (empty($profile['is_promoter'])) {
            jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
        }
        $forceBomb = !empty($json['force_bomb']) || !empty($_POST['force_bomb']);
        $_SESSION['promoter_force_bomb'] = $forceBomb;
        jsonResponse([
            'success' => true,
            'stealth_mode' => $forceBomb ? 'fail_next' : 'safe_pattern'
        ]);
        break;

    default:
        jsonResponse(['success' => false, 'error' => 'Invalid game action'], 400);
        break;
}
