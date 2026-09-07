<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Get all settings from database
    $stmt = $pdo->prepare("SELECT setting_key, setting_value FROM admin_settings");
    $stmt->execute();
    
    $settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    $isTrue = function($val) {
        return $val === 'true' || $val === '1' || $val === 1 || $val === true;
    };

    // Merge global system settings (such as global RTP rate)
    try {
        $sysStmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings");
        while ($srow = $sysStmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$srow['setting_key']] = $srow['setting_value'];
        }
    } catch (Exception $e) {}

    $rtp = floatval($settings['chicken_target_rtp'] ?? $settings['rtp_rate'] ?? $settings['target_rtp'] ?? 96.5);
    $houseEdge = floatval($settings['chicken_house_edge'] ?? $settings['house_edge'] ?? (100.0 - $rtp));

    // Return all settings with robust defaults
    echo json_encode([
        'success' => true,
        'settings' => [
            'rtp_rate' => $rtp,
            'target_rtp' => $rtp,
            'chicken_target_rtp' => $rtp,
            'house_edge' => $houseEdge,
            'chicken_house_edge' => $houseEdge,
            'rtp_easy' => floatval($settings['rtp_easy'] ?? $rtp),
            'rtp_medium' => floatval($settings['rtp_medium'] ?? $rtp),
            'rtp_hard' => floatval($settings['rtp_hard'] ?? $rtp),
            'rtp_hardcore' => floatval($settings['rtp_hardcore'] ?? $rtp),
            'game_rig_mode' => $settings['game_rig_mode'] ?? 'fair',
            'min_bet' => floatval($settings['min_bet'] ?? 10),
            'max_bet' => floatval($settings['max_bet'] ?? 10000),
            'min_deposit' => floatval($settings['min_deposit'] ?? 100),
            'max_deposit' => floatval($settings['max_deposit'] ?? 50000),
            'min_withdrawal' => floatval($settings['min_withdrawal'] ?? 500),
            'max_withdrawal' => floatval($settings['max_withdrawal'] ?? 100000),
            'referral_bonus' => floatval($settings['referral_bonus'] ?? 100),
            'signup_bonus' => floatval($settings['signup_bonus'] ?? 0),
            'refer_bonus' => floatval($settings['refer_bonus'] ?? 0),
            'maintenance_mode' => $isTrue($settings['maintenance_mode'] ?? 'false'),
            'upi_id' => $settings['upi_id'] ?? '',
            'qr_code' => $settings['qr_code'] ?? '',
            'bank_details' => $settings['bank_details'] ?? '',
            'support_link' => $settings['support_link'] ?? '',
            'preset_btn_1' => floatval($settings['preset_btn_1'] ?? 10),
            'preset_btn_2' => floatval($settings['preset_btn_2'] ?? 50),
            'preset_btn_3' => floatval($settings['preset_btn_3'] ?? 100),
            'preset_btn_4' => floatval($settings['preset_btn_4'] ?? 500),
            'controlled_crash_enabled' => $isTrue($settings['controlled_crash_enabled'] ?? 'false'),
            'crash_level_easy' => $settings['crash_level_easy'] ?? 'random',
            'crash_level_medium' => $settings['crash_level_medium'] ?? 'random',
            'crash_level_hard' => $settings['crash_level_hard'] ?? 'random',
            'crash_level_hardcore' => $settings['crash_level_hardcore'] ?? 'random',
            'controlled_crash_rectangle' => intval($settings['controlled_crash_rectangle'] ?? 10),
            'site_title' => $settings['site_title'] ?? 'CHICKEN ROAD',
            'logo' => $settings['logo'] ?? 'images/chicken.png',
            'powered_by_logo' => $settings['powered_by_logo'] ?? 'images/chicken.png',
            'kyc_enabled' => $isTrue($settings['kyc_enabled'] ?? 'false'),
            'referral_enabled' => $isTrue($settings['referral_enabled'] ?? 'false')
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to load settings: ' . $e->getMessage()]);
}
?>