<?php
// Unified Admin Panel Bridge
require_once dirname(dirname(__DIR__)) . '/config/config.php';
require_once dirname(dirname(__DIR__)) . '/config/db.php';
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';

// Get unified PDO database connection
try {
    $pdo = getDB();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin session management - Strictly unified with Master Admin
function isAdminLoggedIn() {
    return isAdmin();
}

function getCurrentAdminId() {
    $u = getCurrentUser();
    return $u ? $u['id'] : null;
}

function getCurrentAdminUsername() {
    $u = getCurrentUser();
    return $u ? $u['email'] : null;
}

function getCurrentAdminRole() {
    return isAdmin() ? 'super_admin' : null;
}

function isSuperAdmin() {
    return isAdmin();
}

function isAdmin() {
    return getCurrentAdminRole() === 'admin' || getCurrentAdminRole() === 'super_admin';
}

function isModerator() {
    return getCurrentAdminRole() === 'moderator' || getCurrentAdminRole() === 'admin' || getCurrentAdminRole() === 'super_admin';
}

// Helper functions
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function generateRandomString($length = 10) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

function formatCurrency($amount) {
    return '₹' . number_format($amount, 2);
}

function formatDate($date) {
    return date('d M Y, h:i A', strtotime($date));
}

function getTimeAgo($datetime) {
    $time = time() - strtotime($datetime);
    $time = ($time < 1) ? 1 : $time;
    $tokens = array(
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . ' ago';
    }
}

// Activity logging
function logAdminActivity($action, $description = '') {
    global $pdo;
    
    if (!isAdminLoggedIn()) return;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO admin_activity_logs (admin_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            getCurrentAdminId(),
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    } catch (Exception $e) {
        error_log("Failed to log admin activity: " . $e->getMessage());
    }
}

// Settings management
function getSetting($key, $default = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM admin_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        return $result ? $result['setting_value'] : $default;
    } catch (Exception $e) {
        error_log("Failed to get setting: " . $e->getMessage());
        return $default;
    }
}

function updateSetting($key, $value) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE admin_settings SET setting_value = ?, updated_by = ?, updated_at = NOW() WHERE setting_key = ?");
        $stmt->execute([$value, getCurrentAdminId(), $key]);
        
        logAdminActivity('update_setting', "Updated setting: $key = $value");
        return true;
    } catch (Exception $e) {
        error_log("Failed to update setting: " . $e->getMessage());
        return false;
    }
}

// Notification management
function createNotification($adminId, $title, $message, $type = 'info') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO admin_notifications (admin_id, title, message, type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$adminId, $title, $message, $type]);
        return true;
    } catch (Exception $e) {
        error_log("Failed to create notification: " . $e->getMessage());
        return false;
    }
}

function getUnreadNotifications($adminId = null) {
    global $pdo;
    
    if (!$adminId) {
        $adminId = getCurrentAdminId();
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admin_notifications WHERE admin_id = ? AND is_read = 0 ORDER BY created_at DESC");
        $stmt->execute([$adminId]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Failed to get notifications: " . $e->getMessage());
        return [];
    }
}

// Statistics functions
function getTotalUsers() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0");
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

function getTotalTransactions() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM transactions");
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

function getPendingTransactions() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status = 'pending'");
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

function getTotalBets() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM bet_history");
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

function getTotalRevenue() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT SUM(amount) FROM transactions WHERE status = 'approved' AND type = 'deposit'");
        return $stmt->fetchColumn() ?: 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getTotalWithdrawals() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT SUM(amount) FROM transactions WHERE status = 'approved' AND type = 'withdrawal'");
        return $stmt->fetchColumn() ?: 0;
    } catch (Exception $e) {
        return 0;
    }
}

// Security functions
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireSuperAdmin() {
    requireAdminLogin();
    if (!isSuperAdmin()) {
        header('Location: dashboard.php?error=insufficient_permissions');
        exit;
    }
}

function requireAdmin() {
    requireAdminLogin();
    if (!isAdmin()) {
        header('Location: dashboard.php?error=insufficient_permissions');
        exit;
    }
}

function requireModerator() {
    requireAdminLogin();
    if (!isModerator()) {
        header('Location: dashboard.php?error=insufficient_permissions');
        exit;
    }
}

// CSRF protection
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Error handling
function displayError($message) {
    return '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">' . htmlspecialchars($message) . '</div>';
}

function displaySuccess($message) {
    return '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">' . htmlspecialchars($message) . '</div>';
}

function displayWarning($message) {
    return '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">' . htmlspecialchars($message) . '</div>';
}

function displayInfo($message) {
    return '<div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">' . htmlspecialchars($message) . '</div>';
}
?> 