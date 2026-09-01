<?php
require_once dirname(__DIR__) . '/config/config.php';

/**
 * Send a standardized JSON response and exit
 */
function jsonResponse(array $data, int $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Generate a cryptographically secure UUID v4 string
 */
function generateUUID(): string {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * Clean & sanitize string inputs
 */
function cleanInput($data): string {
    if ($data === null) return '';
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Parse JSON request body if POST content-type is application/json
 */
function getJsonInput(): array {
    $raw = file_get_contents('php://input');
    if (empty($raw)) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/**
 * Safely upload an image file (avatar or payment screenshot)
 */
function handleImageUpload(array $file, string $targetDir, string $filePrefix = 'img_'): ?string {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Limit to 5MB
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('File exceeds maximum size limit of 5MB');
    }

    // Verify MIME type using finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    if (!isset($allowedMimes[$mime])) {
        throw new Exception('Invalid file format. Only JPEG, PNG, WEBP, and GIF are allowed.');
    }

    $extension = $allowedMimes[$mime];
    $filename = $filePrefix . bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
    $destination = rtrim($targetDir, '/') . '/' . $filename;

    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Return relative path for web access
        $relPath = str_replace(BASE_DIR, '', $destination);
        return ltrim($relPath, '/');
    }

    return null;
}

/**
 * Fetch a system setting by key
 */
function getSystemSetting(string $key, $default = null) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = :key LIMIT 1");
        $stmt->execute([':key' => $key]);
        $row = $stmt->fetch();
        return $row ? $row['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Set a system setting by key
 */
function setSystemSetting(string $key, string $value): bool {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT setting_key FROM system_settings WHERE setting_key = :key");
        $stmt->execute([':key' => $key]);
        if ($stmt->fetch()) {
            $upd = $db->prepare("UPDATE system_settings SET setting_value = :val WHERE setting_key = :key");
            return $upd->execute([':val' => $value, ':key' => $key]);
        } else {
            $ins = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (:key, :val)");
            return $ins->execute([':key' => $key, ':val' => $value]);
        }
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Fetch all system settings as key-value associative array
 */
function getAllSystemSettings(): array {
    try {
        $db = getDB();
        $rows = $db->query("SELECT setting_key, setting_value FROM system_settings")->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = $r['setting_value'];
        }
        return $settings;
    } catch (Exception $e) {
        return [];
    }
}
