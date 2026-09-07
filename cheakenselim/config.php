<?php
// Unified Bridge Configuration for Chicken Road (Chickenseum)
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/auth.php';

// Get unified PDO database connection (MySQL or SQLite fallback)
try {
    $pdo = getDB();
} catch (Exception $e) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]));
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Helper function to generate random strings
function generateRandomString($length = 10) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

// Helper function to generate referral code
function generateReferralCode() {
    return 'REF' . strtoupper(generateRandomString(6));
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return getCurrentUser() !== null;
}

// Helper function to get current user ID
function getCurrentUserId() {
    $user = getCurrentUser();
    return $user ? $user['id'] : null;
}

// Helper function to sanitize input
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(strip_tags(trim((string)$input)), ENT_QUOTES, 'UTF-8');
}
?>