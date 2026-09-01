<?php
// Prevent direct access
if (!defined('BOMBOCLAT_APP')) {
    define('BOMBOCLAT_APP', true);
}

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    // Session security settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// App constants
define('APP_NAME', 'Bombaclat Mine');
define('APP_VERSION', '2.0.0');
define('ADMIN_EMAIL', 'krrishyogi18@gmail.com');
define('INITIAL_USER_BALANCE', 500); // ₹500 starting bonus
// OpenRouter AI Support API Key
$apiKey = getenv('OPENROUTER_API_KEY');
if (!$apiKey && file_exists(__DIR__ . '/openrouter.key')) {
    $apiKey = trim((string)@file_get_contents(__DIR__ . '/openrouter.key'));
}
if (!$apiKey) {
    // Obfuscated fallback key for live support
    $apiKey = base64_decode('c2stb3ItdjEtNTdmMDVkMjZlYzUwMGZhNDY0ZjEyZmM4MDljOGU0YTg5NDIyMTY2ZDIwY2UzNzQwOWFkZmU4MzIyYmI2MGY4Nw==');
}
define('OPENROUTER_API_KEY', $apiKey);


// Base paths
define('BASE_DIR', dirname(__DIR__));
define('UPLOADS_DIR', BASE_DIR . '/uploads');
define('AVATARS_DIR', UPLOADS_DIR . '/avatars');
define('PAYMENTS_DIR', UPLOADS_DIR . '/payments');

// Ensure upload directories exist
if (!file_exists(UPLOADS_DIR)) {
    @mkdir(UPLOADS_DIR, 0755, true);
}
if (!file_exists(AVATARS_DIR)) {
    @mkdir(AVATARS_DIR, 0755, true);
}
if (!file_exists(PAYMENTS_DIR)) {
    @mkdir(PAYMENTS_DIR, 0755, true);
}
