<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$action = cleanInput($_GET['action'] ?? $_POST['action'] ?? '');
$json = getJsonInput();

switch ($action) {
    case 'me':
        $user = getCurrentUser();
        if ($user) {
            jsonResponse(['success' => true, 'user' => $user]);
        } else {
            jsonResponse(['success' => false, 'user' => null], 401);
        }
        break;

    case 'login':
        $email = $json['email'] ?? $_POST['email'] ?? '';
        $password = $json['password'] ?? $_POST['password'] ?? '';
        $result = loginUser($email, $password);
        jsonResponse($result, $result['success'] ? 200 : 400);
        break;

    case 'signup':
        $email = $json['email'] ?? $_POST['email'] ?? '';
        $password = $json['password'] ?? $_POST['password'] ?? '';
        $fullName = $json['full_name'] ?? $_POST['full_name'] ?? '';
        $result = registerUser($email, $password, $fullName);
        jsonResponse($result, $result['success'] ? 200 : 400);
        break;

    case 'logout':
        logoutUser();
        jsonResponse(['success' => true, 'message' => 'Logged out successfully']);
        break;

    case 'update_profile':
        $user = getCurrentUser();
        if (!$user) {
            jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $fullName = $_POST['full_name'] ?? $json['full_name'] ?? null;
        $avatarUrl = null;

        // Check if an avatar image is uploaded
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            try {
                $avatarUrl = handleImageUpload($_FILES['avatar'], AVATARS_DIR, 'avatar_' . $user['id'] . '_');
            } catch (Exception $e) {
                jsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
            }
        }

        $res = updateUserProfile($user['id'], $fullName, $avatarUrl);
        jsonResponse($res, $res['success'] ? 200 : 400);
        break;

    default:
        jsonResponse(['success' => false, 'error' => 'Invalid action parameter'], 400);
        break;
}
