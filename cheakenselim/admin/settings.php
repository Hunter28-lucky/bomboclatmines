<?php
require_once 'config.php';

// Set page title
$page_title = 'System Settings';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success_message = '';
    $error_message = '';
    
    try {
        // Update settings
        foreach ($_POST['settings'] as $key => $value) {
            if (updateSetting($key, $value)) {
                $success_message = 'Settings updated successfully!';
            } else {
                $error_message = 'Failed to update some settings.';
                break;
            }
        }
    } catch (Exception $e) {
        $error_message = 'An error occurred while updating settings.';
    }
}

// Get all settings
$settings = [];
try {
    $stmt = $pdo->query("SELECT * FROM admin_settings ORDER BY id");
    $settings_data = $stmt->fetchAll();
    
    foreach ($settings_data as $setting) {
        $settings[$setting['setting_key']] = $setting;
    }
} catch (Exception $e) {
    $settings = [];
}

// Include header
include 'includes/header.php';
?>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">System Settings</h1>
        <p class="page-subtitle">Configure game settings and system parameters</p>
    </div>
    
    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <!-- Game Settings -->
        <div class="table-container" style="margin-bottom: 30px;">
            <div class="content-header">
                <h2 class="content-title">🎮 Game Settings</h2>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="site_title" class="form-label">Site Title</label>
                    <input type="text" name="settings[site_title]" id="site_title" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['site_title']['setting_value'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="site_description" class="form-label">Site Description</label>
                    <input type="text" name="settings[site_description]" id="site_description" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['site_description']['setting_value'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="min_bet" class="form-label">Minimum Bet Amount</label>
                    <input type="number" name="settings[min_bet]" id="min_bet" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['min_bet']['setting_value'] ?? '10'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="max_bet" class="form-label">Maximum Bet Amount</label>
                    <input type="number" name="settings[max_bet]" id="max_bet" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['max_bet']['setting_value'] ?? '10000'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="house_edge" class="form-label">House Edge (%)</label>
                    <input type="number" step="0.1" name="settings[house_edge]" id="house_edge" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['house_edge']['setting_value'] ?? '5'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="game_crash_min" class="form-label">Min Crash Multiplier</label>
                    <input type="number" step="0.1" name="settings[game_crash_min]" id="game_crash_min" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['game_crash_min']['setting_value'] ?? '1.1'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="game_crash_max" class="form-label">Max Crash Multiplier</label>
                    <input type="number" step="0.1" name="settings[game_crash_max]" id="game_crash_max" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['game_crash_max']['setting_value'] ?? '100'); ?>">
                </div>
            </div>
        </div>
        
        <!-- Crash Control Settings -->
        <div class="table-container" style="margin-bottom: 30px;">
            <div class="content-header">
                <h2 class="content-title">🎯 Crash Control Settings</h2>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Controlled Crash Mode</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="radio" name="settings[controlled_crash_enabled]" value="true" id="controlled_crash_on" 
                               <?php echo ($settings['controlled_crash_enabled']['setting_value'] ?? 'false') === 'true' ? 'checked' : ''; ?>>
                        <label for="controlled_crash_on" style="margin: 0; color: var(--text_color_L1);">Enabled</label>
                        
                        <input type="radio" name="settings[controlled_crash_enabled]" value="false" id="controlled_crash_off" 
                               <?php echo ($settings['controlled_crash_enabled']['setting_value'] ?? 'false') === 'false' ? 'checked' : ''; ?>>
                        <label for="controlled_crash_off" style="margin: 0; color: var(--text_color_L1);">Disabled</label>
                    </div>
                    <small style="color: #666; font-size: 0.9rem;">When enabled, game will crash at specific levels instead of random crashes</small>
                </div>
                
                <div class="form-group">
                    <label for="controlled_crash_rectangle" class="form-label">Crash Rectangle Position</label>
                    <input type="number" min="1" max="25" name="settings[controlled_crash_rectangle]" id="controlled_crash_rectangle" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['controlled_crash_rectangle']['setting_value'] ?? '1'); ?>">
                    <small style="color: #666; font-size: 0.9rem;">Position where the game will crash (1-25)</small>
                </div>
                
                <div class="form-group">
                    <label for="crash_level_easy" class="form-label">Easy Mode Crash Level</label>
                    <select name="settings[crash_level_easy]" id="crash_level_easy" class="form-control">
                        <option value="random" <?php echo ($settings['crash_level_easy']['setting_value'] ?? 'random') === 'random' ? 'selected' : ''; ?>>Random</option>
                        <option value="1" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '1' ? 'selected' : ''; ?>>Level 1 (1.03x)</option>
                        <option value="2" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '2' ? 'selected' : ''; ?>>Level 2 (1.07x)</option>
                        <option value="3" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '3' ? 'selected' : ''; ?>>Level 3 (1.12x)</option>
                        <option value="4" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '4' ? 'selected' : ''; ?>>Level 4 (1.17x)</option>
                        <option value="5" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '5' ? 'selected' : ''; ?>>Level 5 (1.23x)</option>
                        <option value="6" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '6' ? 'selected' : ''; ?>>Level 6 (1.29x)</option>
                        <option value="7" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '7' ? 'selected' : ''; ?>>Level 7 (1.36x)</option>
                        <option value="8" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '8' ? 'selected' : ''; ?>>Level 8 (1.44x)</option>
                        <option value="9" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '9' ? 'selected' : ''; ?>>Level 9 (1.53x)</option>
                        <option value="10" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '10' ? 'selected' : ''; ?>>Level 10 (1.63x)</option>
                        <option value="11" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '11' ? 'selected' : ''; ?>>Level 11 (1.75x)</option>
                        <option value="12" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '12' ? 'selected' : ''; ?>>Level 12 (1.88x)</option>
                        <option value="13" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '13' ? 'selected' : ''; ?>>Level 13 (2.04x)</option>
                        <option value="14" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '14' ? 'selected' : ''; ?>>Level 14 (2.22x)</option>
                        <option value="15" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '15' ? 'selected' : ''; ?>>Level 15 (2.45x)</option>
                        <option value="16" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '16' ? 'selected' : ''; ?>>Level 16 (2.72x)</option>
                        <option value="17" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '17' ? 'selected' : ''; ?>>Level 17 (3.06x)</option>
                        <option value="18" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '18' ? 'selected' : ''; ?>>Level 18 (3.50x)</option>
                        <option value="19" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '19' ? 'selected' : ''; ?>>Level 19 (4.08x)</option>
                        <option value="20" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '20' ? 'selected' : ''; ?>>Level 20 (4.90x)</option>
                        <option value="21" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '21' ? 'selected' : ''; ?>>Level 21 (6.13x)</option>
                        <option value="22" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '22' ? 'selected' : ''; ?>>Level 22 (6.61x)</option>
                        <option value="23" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '23' ? 'selected' : ''; ?>>Level 23 (9.81x)</option>
                        <option value="24" <?php echo ($settings['crash_level_easy']['setting_value'] ?? '') === '24' ? 'selected' : ''; ?>>Level 24 (19.44x)</option>
                    </select>
                    <small style="color: #666; font-size: 0.9rem;">Select specific crash level for Easy mode</small>
                </div>
                
                <div class="form-group">
                    <label for="crash_level_medium" class="form-label">Medium Mode Crash Level</label>
                    <select name="settings[crash_level_medium]" id="crash_level_medium" class="form-control">
                        <option value="random" <?php echo ($settings['crash_level_medium']['setting_value'] ?? 'random') === 'random' ? 'selected' : ''; ?>>Random</option>
                        <option value="1" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '1' ? 'selected' : ''; ?>>Level 1 (1.12x)</option>
                        <option value="2" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '2' ? 'selected' : ''; ?>>Level 2 (1.28x)</option>
                        <option value="3" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '3' ? 'selected' : ''; ?>>Level 3 (1.47x)</option>
                        <option value="4" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '4' ? 'selected' : ''; ?>>Level 4 (1.70x)</option>
                        <option value="5" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '5' ? 'selected' : ''; ?>>Level 5 (1.98x)</option>
                        <option value="6" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '6' ? 'selected' : ''; ?>>Level 6 (2.33x)</option>
                        <option value="7" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '7' ? 'selected' : ''; ?>>Level 7 (2.76x)</option>
                        <option value="8" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '8' ? 'selected' : ''; ?>>Level 8 (3.32x)</option>
                        <option value="9" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '9' ? 'selected' : ''; ?>>Level 9 (4.03x)</option>
                        <option value="10" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '10' ? 'selected' : ''; ?>>Level 10 (4.96x)</option>
                        <option value="11" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '11' ? 'selected' : ''; ?>>Level 11 (6.20x)</option>
                        <option value="12" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '12' ? 'selected' : ''; ?>>Level 12 (6.91x)</option>
                        <option value="13" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '13' ? 'selected' : ''; ?>>Level 13 (8.90x)</option>
                        <option value="14" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '14' ? 'selected' : ''; ?>>Level 14 (11.74x)</option>
                        <option value="15" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '15' ? 'selected' : ''; ?>>Level 15 (15.99x)</option>
                        <option value="16" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '16' ? 'selected' : ''; ?>>Level 16 (22.61x)</option>
                        <option value="17" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '17' ? 'selected' : ''; ?>>Level 17 (33.58x)</option>
                        <option value="18" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '18' ? 'selected' : ''; ?>>Level 18 (53.20x)</option>
                        <option value="19" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '19' ? 'selected' : ''; ?>>Level 19 (92.17x)</option>
                        <option value="20" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '20' ? 'selected' : ''; ?>>Level 20 (182.51x)</option>
                        <option value="21" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '21' ? 'selected' : ''; ?>>Level 21 (451.71x)</option>
                        <option value="22" <?php echo ($settings['crash_level_medium']['setting_value'] ?? '') === '22' ? 'selected' : ''; ?>>Level 22 (1788.80x)</option>
                    </select>
                    <small style="color: #666; font-size: 0.9rem;">Select specific crash level for Medium mode</small>
                </div>
                
                <div class="form-group">
                    <label for="crash_level_hard" class="form-label">Hard Mode Crash Level</label>
                    <select name="settings[crash_level_hard]" id="crash_level_hard" class="form-control">
                        <option value="random" <?php echo ($settings['crash_level_hard']['setting_value'] ?? 'random') === 'random' ? 'selected' : ''; ?>>Random</option>
                        <option value="1" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '1' ? 'selected' : ''; ?>>Level 1 (1.23x)</option>
                        <option value="2" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '2' ? 'selected' : ''; ?>>Level 2 (1.55x)</option>
                        <option value="3" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '3' ? 'selected' : ''; ?>>Level 3 (1.98x)</option>
                        <option value="4" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '4' ? 'selected' : ''; ?>>Level 4 (2.56x)</option>
                        <option value="5" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '5' ? 'selected' : ''; ?>>Level 5 (3.36x)</option>
                        <option value="6" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '6' ? 'selected' : ''; ?>>Level 6 (4.49x)</option>
                        <option value="7" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '7' ? 'selected' : ''; ?>>Level 7 (5.49x)</option>
                        <option value="8" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '8' ? 'selected' : ''; ?>>Level 8 (7.53x)</option>
                        <option value="9" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '9' ? 'selected' : ''; ?>>Level 9 (10.56x)</option>
                        <option value="10" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '10' ? 'selected' : ''; ?>>Level 10 (15.21x)</option>
                        <option value="11" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '11' ? 'selected' : ''; ?>>Level 11 (22.59x)</option>
                        <option value="12" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '12' ? 'selected' : ''; ?>>Level 12 (34.79x)</option>
                        <option value="13" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '13' ? 'selected' : ''; ?>>Level 13 (55.97x)</option>
                        <option value="14" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '14' ? 'selected' : ''; ?>>Level 14 (94.99x)</option>
                        <option value="15" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '15' ? 'selected' : ''; ?>>Level 15 (172.42x)</option>
                        <option value="16" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '16' ? 'selected' : ''; ?>>Level 16 (341.40x)</option>
                        <option value="17" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '17' ? 'selected' : ''; ?>>Level 17 (760.46x)</option>
                        <option value="18" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '18' ? 'selected' : ''; ?>>Level 18 (2007.63x)</option>
                        <option value="19" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '19' ? 'selected' : ''; ?>>Level 19 (6956.47x)</option>
                        <option value="20" <?php echo ($settings['crash_level_hard']['setting_value'] ?? '') === '20' ? 'selected' : ''; ?>>Level 20 (41321.43x)</option>
                    </select>
                    <small style="color: #666; font-size: 0.9rem;">Select specific crash level for Hard mode</small>
                </div>
                
                <div class="form-group">
                    <label for="crash_level_hardcore" class="form-label">Hardcore Mode Crash Level</label>
                    <select name="settings[crash_level_hardcore]" id="crash_level_hardcore" class="form-control">
                        <option value="random" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? 'random') === 'random' ? 'selected' : ''; ?>>Random</option>
                        <option value="1" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '1' ? 'selected' : ''; ?>>Level 1 (1.63x)</option>
                        <option value="2" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '2' ? 'selected' : ''; ?>>Level 2 (2.80x)</option>
                        <option value="3" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '3' ? 'selected' : ''; ?>>Level 3 (4.95x)</option>
                        <option value="4" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '4' ? 'selected' : ''; ?>>Level 4 (9.08x)</option>
                        <option value="5" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '5' ? 'selected' : ''; ?>>Level 5 (15.21x)</option>
                        <option value="6" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '6' ? 'selected' : ''; ?>>Level 6 (30.12x)</option>
                        <option value="7" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '7' ? 'selected' : ''; ?>>Level 7 (62.96x)</option>
                        <option value="8" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '8' ? 'selected' : ''; ?>>Level 8 (140.24x)</option>
                        <option value="9" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '9' ? 'selected' : ''; ?>>Level 9 (337.19x)</option>
                        <option value="10" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '10' ? 'selected' : ''; ?>>Level 10 (890.19x)</option>
                        <option value="11" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '11' ? 'selected' : ''; ?>>Level 11 (2643.89x)</option>
                        <option value="12" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '12' ? 'selected' : ''; ?>>Level 12 (9161.08x)</option>
                        <option value="13" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '13' ? 'selected' : ''; ?>>Level 13 (39301.05x)</option>
                        <option value="14" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '14' ? 'selected' : ''; ?>>Level 14 (233448.29x)</option>
                        <option value="15" <?php echo ($settings['crash_level_hardcore']['setting_value'] ?? '') === '15' ? 'selected' : ''; ?>>Level 15 (2542251.93x)</option>
                    </select>
                    <small style="color: #666; font-size: 0.9rem;">Select specific crash level for Hardcore mode</small>
                </div>
            </div>
        </div>
        
        <!-- Payment Settings -->
        <div class="table-container" style="margin-bottom: 30px;">
            <div class="content-header">
                <h2 class="content-title">💰 Payment Settings</h2>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="min_deposit" class="form-label">Minimum Deposit</label>
                    <input type="number" name="settings[min_deposit]" id="min_deposit" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['min_deposit']['setting_value'] ?? '100'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="max_deposit" class="form-label">Maximum Deposit</label>
                    <input type="number" name="settings[max_deposit]" id="max_deposit" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['max_deposit']['setting_value'] ?? '50000'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="min_withdrawal" class="form-label">Minimum Withdrawal</label>
                    <input type="number" name="settings[min_withdrawal]" id="min_withdrawal" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['min_withdrawal']['setting_value'] ?? '500'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="max_withdrawal" class="form-label">Maximum Withdrawal</label>
                    <input type="number" name="settings[max_withdrawal]" id="max_withdrawal" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['max_withdrawal']['setting_value'] ?? '100000'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="upi_id" class="form-label">UPI ID</label>
                    <input type="text" name="settings[upi_id]" id="upi_id" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['upi_id']['setting_value'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="qr_code" class="form-label">QR Code Path</label>
                    <input type="text" name="settings[qr_code]" id="qr_code" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['qr_code']['setting_value'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 20px;">
                <label for="bank_details" class="form-label">Bank Account Details</label>
                <textarea name="settings[bank_details]" id="bank_details" class="form-control" rows="4" 
                          placeholder="Bank: SBI&#10;Account: 1234567890&#10;IFSC: SBIN0001234"><?php echo htmlspecialchars($settings['bank_details']['setting_value'] ?? ''); ?></textarea>
            </div>
        </div>
        
        <!-- Bonus Settings -->
        <div class="table-container" style="margin-bottom: 30px;">
            <div class="content-header">
                <h2 class="content-title">🎁 Bonus Settings</h2>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="signup_bonus" class="form-label">Signup Bonus</label>
                    <input type="number" name="settings[signup_bonus]" id="signup_bonus" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['signup_bonus']['setting_value'] ?? '0'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="referral_bonus" class="form-label">Referral Bonus</label>
                    <input type="number" name="settings[referral_bonus]" id="referral_bonus" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['referral_bonus']['setting_value'] ?? '100'); ?>">
                </div>
            </div>
        </div>
        
        <!-- Support Settings -->
        <div class="table-container" style="margin-bottom: 30px;">
            <div class="content-header">
                <h2 class="content-title">📞 Support Settings</h2>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="support_whatsapp" class="form-label">WhatsApp Number</label>
                    <input type="text" name="settings[support_whatsapp]" id="support_whatsapp" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['support_whatsapp']['setting_value'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="support_email" class="form-label">Support Email</label>
                    <input type="email" name="settings[support_email]" id="support_email" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['support_email']['setting_value'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="support_telegram" class="form-label">Telegram Link</label>
                    <input type="text" name="settings[support_telegram]" id="support_telegram" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['support_telegram']['setting_value'] ?? ''); ?>">
                </div>
            </div>
        </div>
        
        <!-- System Toggles -->
        <div class="table-container" style="margin-bottom: 30px;">
            <div class="content-header">
                <h2 class="content-title">⚙️ System Toggles</h2>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Maintenance Mode</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="radio" name="settings[maintenance_mode]" value="true" id="maintenance_on" 
                               <?php echo ($settings['maintenance_mode']['setting_value'] ?? 'false') === 'true' ? 'checked' : ''; ?>>
                        <label for="maintenance_on" style="margin: 0; color: var(--text_color_L1);">Enabled</label>
                        
                        <input type="radio" name="settings[maintenance_mode]" value="false" id="maintenance_off" 
                               <?php echo ($settings['maintenance_mode']['setting_value'] ?? 'false') === 'false' ? 'checked' : ''; ?>>
                        <label for="maintenance_off" style="margin: 0; color: var(--text_color_L1);">Disabled</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">KYC Required</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="radio" name="settings[kyc_enabled]" value="true" id="kyc_on" 
                               <?php echo ($settings['kyc_enabled']['setting_value'] ?? 'false') === 'true' ? 'checked' : ''; ?>>
                        <label for="kyc_on" style="margin: 0; color: var(--text_color_L1);">Enabled</label>
                        
                        <input type="radio" name="settings[kyc_enabled]" value="false" id="kyc_off" 
                               <?php echo ($settings['kyc_enabled']['setting_value'] ?? 'false') === 'false' ? 'checked' : ''; ?>>
                        <label for="kyc_off" style="margin: 0; color: var(--text_color_L1);">Disabled</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Referral System</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="radio" name="settings[referral_enabled]" value="true" id="referral_on" 
                               <?php echo ($settings['referral_enabled']['setting_value'] ?? 'true') === 'true' ? 'checked' : ''; ?>>
                        <label for="referral_on" style="margin: 0; color: var(--text_color_L1);">Enabled</label>
                        
                        <input type="radio" name="settings[referral_enabled]" value="false" id="referral_off" 
                               <?php echo ($settings['referral_enabled']['setting_value'] ?? 'true') === 'false' ? 'checked' : ''; ?>>
                        <label for="referral_off" style="margin: 0; color: var(--text_color_L1);">Disabled</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Crash Game</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="radio" name="settings[game_crash_enabled]" value="true" id="crash_on" 
                               <?php echo ($settings['game_crash_enabled']['setting_value'] ?? 'true') === 'true' ? 'checked' : ''; ?>>
                        <label for="crash_on" style="margin: 0; color: var(--text_color_L1);">Enabled</label>
                        
                        <input type="radio" name="settings[game_crash_enabled]" value="false" id="crash_off" 
                               <?php echo ($settings['game_crash_enabled']['setting_value'] ?? 'true') === 'false' ? 'checked' : ''; ?>>
                        <label for="crash_off" style="margin: 0; color: var(--text_color_L1);">Disabled</label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div style="text-align: center; margin-top: 30px;">
            <button type="submit" class="btn btn-primary" style="padding: 15px 40px; font-size: 1.1rem;">
                💾 Save All Settings
            </button>
        </div>
    </form>
</div>

<style>
.form-group {
    margin-bottom: 20px;
}

.form-group input[type="radio"] {
    accent-color: var(--main-color);
    transform: scale(1.2);
}

.form-group label {
    cursor: pointer;
}

.form-control:focus {
    border-color: var(--main-color);
    box-shadow: 0 0 0 3px rgba(0, 236, 190, 0.3);
}
</style>

<?php include 'includes/footer.php'; ?> 