<?php
header('Content-Type: application/json');
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$username = sanitize($input['username'] ?? '');
$phone = sanitize($input['phone'] ?? '');
$password = $input['password'] ?? '';
$referral_code = sanitize($input['referral_code'] ?? '');

// Validation
if (empty($username) || empty($phone) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

if (strlen($username) < 3 || strlen($username) > 20) {
    echo json_encode(['success' => false, 'error' => 'Username must be 3-20 characters']);
    exit;
}

// Basic phone number validation (10 digits)
if (!preg_match('/^\d{10}$/', $phone)) {
    echo json_encode(['success' => false, 'error' => 'Phone number must be 10 digits']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
    exit;
}

try {
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Username already exists']);
        exit;
    }

    // Check if phone already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Phone number already exists']);
        exit;
    }

    // Check referral code if provided
    $referred_by = null;
    // Fetch referral_enabled setting
    $referral_enabled = true;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM admin_settings WHERE setting_key = 'referral_enabled'");
        $stmt->execute();
        $referral_enabled = ($stmt->fetchColumn() ?? 'true') === 'true';
    } catch (Exception $e) {
        $referral_enabled = true; // fallback to enabled if error
    }
    if (!empty($referral_code)) {
        if (!$referral_enabled) {
            echo json_encode(['success' => false, 'error' => 'Referral system is currently disabled']);
            exit;
        }
        $stmt = $pdo->prepare("SELECT id FROM users WHERE referral_code = ?");
        $stmt->execute([$referral_code]);
        $referrer = $stmt->fetch();
        if ($referrer) {
            $referred_by = $referrer['id'];
        }
    }

    // Generate unique referral code
    do {
        $new_referral_code = generateReferralCode();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE referral_code = ?");
        $stmt->execute([$new_referral_code]);
    } while ($stmt->fetch());

    // Hash password properly
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Get signup and referral bonus from settings
    $signup_bonus = 0;
    $refer_bonus = 0;
    
    try {
        $stmt = $pdo->prepare("SELECT setting_key, setting_value FROM admin_settings WHERE setting_key IN ('signup_bonus', 'refer_bonus')");
        $stmt->execute();
        $bonus_settings = [];
        while ($row = $stmt->fetch()) {
            $bonus_settings[$row['setting_key']] = floatval($row['setting_value']);
        }
        $signup_bonus = $bonus_settings['signup_bonus'] ?? 0;
        $refer_bonus = $bonus_settings['refer_bonus'] ?? 0;
    } catch (Exception $e) {
        // If settings table doesn't exist or has issues, use default values
        $signup_bonus = 0;
        $refer_bonus = 0;
    }

    // Insert new user
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, phone, password, referral_code, referred_by, balance) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $phone, $hashed_password, $new_referral_code, $referred_by, $signup_bonus]);
        $user_id = $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("User insertion error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Registration failed. Please try again.']);
        exit;
    }

    // If user was referred, give refer_bonus to both users
    if ($referred_by && $referral_enabled) {
        try {
            // Add bonus to referrer
            $stmt = $pdo->prepare("UPDATE users SET referral_earnings = referral_earnings + ?, total_referrals = total_referrals + 1, balance = balance + ? WHERE id = ?");
            $stmt->execute([$refer_bonus, $refer_bonus, $referred_by]);
            // Add bonus to new user (in addition to signup bonus)
            $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$refer_bonus, $user_id]);
        } catch (Exception $e) {
            error_log("Referral bonus error: " . $e->getMessage());
            // Continue with registration even if referral bonus fails
        }
    }

    // Log in the user
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;

    echo json_encode([
        'success' => true, 
        'message' => 'Registration successful!',
        'user' => [
            'id' => $user_id,
            'username' => $username,
            'phone' => $phone,
            'referral_code' => $new_referral_code
        ]
    ]);

} catch (Exception $e) {
    // Log the error for debugging
    error_log("Registration error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Registration failed. Please try again.']);
}
?> 