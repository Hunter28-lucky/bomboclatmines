<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

// Unset session and destroy
$_SESSION = [];
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

if (isset($_GET['redirect'])) {
    header('Location: ../../login.php');
    exit;
}

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
?>