<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

$user = getCurrentUser();
if (!$user || !isAdmin()) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Operator Studio - <?= htmlspecialchars(APP_NAME) ?></title>
  <link rel="icon" type="image/png" href="assets/images/favicon.png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap">
  <style>
    :root {
      --obsidian-bg: #070512;
      --obsidian-card: rgba(15, 11, 32, 0.92);
      --obsidian-surface: #120d28;
      --obsidian-border: rgba(139, 92, 246, 0.2);
      --accent-purple: #a855f7;
      --accent-emerald: #10b981;
      --accent-cyan: #00f0ff;
      --accent-red: #ef4444;
      --accent-amber: #f59e0b;
      --font-main: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
      --font-mono: 'JetBrains Mono', monospace;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: var(--font-main);
      background: radial-gradient(circle at 50% 10%, rgba(112, 26, 212, 0.15) 0%, transparent 60%),
                  radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.08) 0%, transparent 50%),
                  var(--obsidian-bg);
      color: #f1f5f9;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .admin-header {
      background: rgba(12, 9, 26, 0.95);
      border-bottom: 1px solid var(--obsidian-border);
      padding: 12px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 50;
      backdrop-filter: blur(10px);
    }
    .brand-group {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .brand-title {
      font-size: 1.15rem;
      font-weight: 900;
      letter-spacing: 0.5px;
      color: #fff;
    }
    .brand-badge {
      background: rgba(168, 85, 247, 0.15);
      border: 1px solid rgba(168, 85, 247, 0.4);
      color: var(--accent-purple);
      font-size: 0.68rem;
      font-weight: 800;
      padding: 3px 8px;
      border-radius: 6px;
      letter-spacing: 0.5px;
    }
    .admin-nav-actions {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .btn-launch-game {
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid rgba(16, 185, 129, 0.4);
      color: var(--accent-emerald);
      padding: 6px 14px;
      border-radius: 8px;
      font-size: 0.78rem;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.2s;
    }
    .btn-launch-game:hover { background: var(--accent-emerald); color: #000; }
    .btn-admin-logout {
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid rgba(239, 68, 68, 0.35);
      color: #f87171;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 0.78rem;
      font-weight: 700;
      text-decoration: none;
    }

    /* Container */
    .studio-container {
      max-width: 1320px;
      width: 100%;
      margin: 20px auto;
      padding: 0 20px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    /* KPI Cards Bar */
    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 14px;
    }
    .kpi-card {
      background: var(--obsidian-card);
      border: 1px solid var(--obsidian-border);
      border-radius: 16px;
      padding: 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    }
    .kpi-label {
      font-size: 0.7rem;
      color: #94a3b8;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 6px;
    }
    .kpi-value {
      font-size: 1.5rem;
      font-weight: 900;
      color: #fff;
    }
    .kpi-sub {
      font-size: 0.72rem;
      color: #64748b;
      margin-top: 4px;
    }

    /* Main Grid: Left Settings, Right Tables */
    .studio-main-grid {
      display: grid;
      grid-template-columns: 340px 1fr;
      gap: 20px;
      align-items: start;
    }
    @media (max-width: 1024px) {
      .studio-main-grid { grid-template-columns: 1fr; }
    }

    /* Panel Box */
    .studio-panel {
      background: var(--obsidian-card);
      border: 1px solid var(--obsidian-border);
      border-radius: 18px;
      padding: 20px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.4);
    }
    .panel-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
      padding-bottom: 10px;
      border-bottom: 1px solid rgba(139, 92, 246, 0.15);
    }
    .panel-title {
      font-size: 0.95rem;
      font-weight: 800;
      color: #fff;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* RTP Control Card */
    .rtp-display-box {
      background: #0d0920;
      border: 1px solid rgba(139, 92, 246, 0.3);
      border-radius: 14px;
      padding: 14px;
      text-align: center;
      margin-bottom: 14px;
    }
    .rtp-val-big {
      font-size: 2.2rem;
      font-weight: 900;
      color: var(--accent-purple);
      text-shadow: 0 0 16px rgba(168, 85, 247, 0.4);
    }
    .rtp-slider-input {
      width: 100%;
      height: 6px;
      border-radius: 3px;
      background: #1e1742;
      outline: none;
      accent-color: var(--accent-purple);
      margin: 10px 0;
      cursor: pointer;
    }
    .rtp-presets-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 6px;
      margin-bottom: 14px;
    }
    .btn-preset-pill {
      background: #140e30;
      border: 1px solid rgba(139, 92, 246, 0.25);
      color: #cbd5e1;
      font-size: 0.72rem;
      font-weight: 700;
      padding: 6px;
      border-radius: 8px;
      cursor: pointer;
      text-align: center;
      transition: all 0.15s;
    }
    .btn-preset-pill:hover { background: var(--accent-purple); color: #fff; }

    /* Form Controls */
    .form-group { margin-bottom: 12px; }
    .form-label {
      display: block;
      font-size: 0.72rem;
      color: #94a3b8;
      font-weight: 700;
      margin-bottom: 4px;
      text-transform: uppercase;
    }
    .form-input {
      width: 100%;
      padding: 10px 12px;
      border-radius: 10px;
      background: #0d0920;
      border: 1px solid rgba(139, 92, 246, 0.3);
      color: #f8fafc;
      font-size: 0.85rem;
      font-family: inherit;
      outline: none;
    }
    .form-input:focus { border-color: var(--accent-purple); }

    .btn-save-settings {
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      background: linear-gradient(135deg, #7c3aed, #9333ea);
      border: none;
      color: #fff;
      font-size: 0.85rem;
      font-weight: 800;
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-save-settings:hover { box-shadow: 0 0 16px rgba(147, 51, 234, 0.5); }

    /* Table Styling */
    .table-container {
      overflow-x: auto;
    }
    .studio-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.8rem;
    }
    .studio-table th {
      text-align: left;
      padding: 10px 12px;
      background: #0e0924;
      color: #64748b;
      font-size: 0.68rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 1px solid rgba(139, 92, 246, 0.15);
    }
    .studio-table td {
      padding: 10px 12px;
      border-bottom: 1px solid rgba(139, 92, 246, 0.1);
      vertical-align: middle;
    }
    .studio-table tr:hover td { background: rgba(168, 85, 247, 0.05); }

    .player-cell-box {
      display: flex;
      flex-direction: column;
    }
    .player-name { font-weight: 800; color: #fff; }
    .player-email { font-size: 0.72rem; color: #94a3b8; font-family: var(--font-mono); }

    /* Rig Mode Dropdown */
    .rig-mode-select {
      background: #140e30;
      border: 1px solid rgba(139, 92, 246, 0.4);
      color: #f8fafc;
      font-size: 0.75rem;
      font-weight: 700;
      padding: 6px 8px;
      border-radius: 8px;
      outline: none;
      cursor: pointer;
    }
    .rig-mode-select.trap { border-color: #ef4444; color: #f87171; background: rgba(239, 68, 68, 0.1); }
    .rig-mode-select.lucky { border-color: #10b981; color: #34d399; background: rgba(16, 185, 129, 0.1); }

    /* Action Buttons in Table */
    .table-btn-group {
      display: flex;
      gap: 4px;
      flex-wrap: wrap;
    }
    .btn-action-tiny {
      padding: 4px 8px;
      border-radius: 6px;
      border: 1px solid rgba(139, 92, 246, 0.3);
      background: #140e30;
      color: #cbd5e1;
      font-size: 0.7rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.15s;
    }
    .btn-action-tiny:hover { background: var(--accent-purple); color: #fff; }
    .btn-action-tiny.green:hover { background: var(--accent-emerald); color: #000; border-color: var(--accent-emerald); }
    .btn-action-tiny.red:hover { background: var(--accent-red); color: #fff; border-color: var(--accent-red); }

    /* Tabs */
    .tab-bar-nav {
      display: flex;
      gap: 8px;
      margin-bottom: 16px;
    }
    .tab-bar-btn {
      padding: 8px 16px;
      border-radius: 10px;
      background: #140e30;
      border: 1px solid rgba(139, 92, 246, 0.25);
      color: #94a3b8;
      font-size: 0.78rem;
      font-weight: 800;
      cursor: pointer;
      transition: all 0.2s;
    }
    .tab-bar-btn.active {
      background: var(--accent-purple);
      border-color: var(--accent-purple);
      color: #fff;
      box-shadow: 0 0 12px rgba(168, 85, 247, 0.4);
    }

    /* Modals */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.85);
      backdrop-filter: blur(8px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 100;
      padding: 16px;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.2s;
    }
    .modal-overlay.active { opacity: 1; pointer-events: auto; }
    .modal-box {
      width: 100%;
      max-width: 480px;
      background: #0f0b24;
      border: 1px solid rgba(168, 85, 247, 0.35);
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.85);
      overflow: hidden;
    }
    .modal-header {
      padding: 14px 18px;
      border-bottom: 1px solid rgba(139, 92, 246, 0.2);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .modal-title { font-size: 1.05rem; font-weight: 800; color: #fff; }
    .modal-close {
      background: none; border: none; font-size: 1.2rem; color: #94a3b8; cursor: pointer;
    }
    .modal-body { padding: 18px; }
  </style>
</head>
<body>

  <!-- Top Admin Bar -->
  <header class="admin-header">
    <div class="brand-group">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="#a855f7">
        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
      </svg>
      <div class="brand-title">BOMBACLAT MINES</div>
      <span class="brand-badge">OPERATOR STUDIO</span>
    </div>

    <div class="admin-nav-actions">
      <span style="font-size: 0.75rem; color: #94a3b8;">Operator: <strong style="color:#fff;"><?= htmlspecialchars($user['email']) ?></strong></span>
      <a href="index.php" class="btn-launch-game" target="_blank">🎮 Launch Game</a>
      <a href="api/auth.php?action=logout" class="btn-admin-logout">Logout</a>
    </div>
  </header>

  <div class="studio-container">

    <!-- KPI Metric Summary -->
    <div class="kpi-grid">
      <div class="kpi-card">
        <div class="kpi-label">Total Registered Players</div>
        <div class="kpi-value" id="kpi-total-users">--</div>
        <div class="kpi-sub">Active database users</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Player Balances Held</div>
        <div class="kpi-value" style="color: var(--accent-cyan);" id="kpi-total-balance">₹--</div>
        <div class="kpi-sub">Current player liability</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Total Rounds Played</div>
        <div class="kpi-value" id="kpi-total-games">--</div>
        <div class="kpi-sub">Mines sessions completed</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Active Global RTP</div>
        <div class="kpi-value" style="color: var(--accent-purple);" id="kpi-global-rtp">--%</div>
        <div class="kpi-sub" id="kpi-house-edge">House Edge: --%</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Pending Queues</div>
        <div class="kpi-value" style="color: var(--accent-amber);" id="kpi-pending-count">--</div>
        <div class="kpi-sub">Deposits / Withdrawals</div>
      </div>
    </div>

    <!-- Main Workspace Layout -->
    <div class="studio-main-grid">

      <!-- Left Column: Master Settings & RTP Controller -->
      <div style="display: flex; flex-direction: column; gap: 20px;">

        <!-- RTP & House Edge Controller -->
        <div class="studio-panel">
          <div class="panel-header">
            <div class="panel-title">
              <span>⚙️</span> Global RTP Engine
            </div>
          </div>

          <div class="rtp-display-box">
            <div class="kpi-label">Global Target RTP</div>
            <div class="rtp-val-big" id="rtp-slider-val">97.0%</div>
            <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 2px;">House Margin: <strong id="house-edge-display">3.0%</strong></div>
            <input type="range" min="50" max="99" step="0.5" value="97" id="rtp-range-slider" class="rtp-slider-input">
          </div>

          <div class="rtp-presets-row">
            <button class="btn-preset-pill" data-rtp="97">Fair 97%</button>
            <button class="btn-preset-pill" data-rtp="92">Balanced 92%</button>
            <button class="btn-preset-pill" data-rtp="82">Profit 82%</button>
          </div>

          <button class="btn-save-settings" id="btn-save-rtp" style="background: linear-gradient(135deg, #10b981, #059669);">
            Apply RTP to All Games
          </button>
        </div>

        <!-- System Settings Form -->
        <div class="studio-panel">
          <div class="panel-header">
            <div class="panel-title">
              <span>🛠️</span> System Parameters
            </div>
          </div>

          <form id="system-settings-form">
            <div class="form-group">
              <label class="form-label">Minimum Bet (₹)</label>
              <input type="number" id="setting-min-bet" name="min_bet" class="form-input" value="10">
            </div>
            <div class="form-group">
              <label class="form-label">Maximum Bet (₹)</label>
              <input type="number" id="setting-max-bet" name="max_bet" class="form-input" value="10000">
            </div>
            <div class="form-group">
              <label class="form-label">Minimum Withdrawal (₹)</label>
              <input type="number" id="setting-min-withdrawal" name="min_withdrawal" class="form-input" value="500">
            </div>
            <div class="form-group">
              <label class="form-label">Welcome Signup Bonus (₹)</label>
              <input type="number" id="setting-welcome-bonus" name="welcome_bonus" class="form-input" value="500">
            </div>
            <div class="form-group">
              <label class="form-label">Deposit UPI ID</label>
              <input type="text" id="setting-upi-id" name="upi_id" class="form-input" value="bomboclat@upi">
            </div>

            <button type="submit" class="btn-save-settings">Save Parameters</button>
          </form>
        </div>

        <!-- UPI QR Code Uploader -->
        <div class="studio-panel">
          <div class="panel-header">
            <div class="panel-title">
              <span>📷</span> Deposit QR Code
            </div>
          </div>

          <form id="qr-upload-form" enctype="multipart/form-data">
            <div style="text-align: center; margin-bottom: 12px;">
              <img id="qr-preview-img" src="assets/images/qrpayment.jpeg" alt="QR Code" style="width: 140px; height: 140px; object-fit: contain; border-radius: 10px; border: 1px solid var(--obsidian-border); background:#fff; padding:6px;">
            </div>
            <div class="form-group">
              <input type="file" id="qr-file-input" name="qr_image" accept="image/*" class="form-input" style="padding: 6px;">
            </div>
            <button type="submit" class="btn-save-settings" style="background:#4c1d95;">Update QR Code</button>
          </form>
        </div>

        <!-- Admin Security & Password Change Card -->
        <div class="studio-panel">
          <div class="panel-header">
            <div class="panel-title">
              <span>🔐</span> Admin Security & Password
            </div>
          </div>

          <form id="admin-password-form">
            <div class="form-group">
              <label class="form-label">Current Password</label>
              <input type="password" id="admin-current-password" name="current_password" class="form-input" placeholder="••••••••" required>
            </div>
            <div class="form-group">
              <label class="form-label">New Password (Min. 6 chars)</label>
              <input type="password" id="admin-new-password" name="new_password" class="form-input" placeholder="••••••••" minlength="6" required>
            </div>
            <div class="form-group">
              <label class="form-label">Confirm New Password</label>
              <input type="password" id="admin-confirm-password" name="confirm_password" class="form-input" placeholder="••••••••" minlength="6" required>
            </div>

            <div id="password-change-alert" style="display:none; padding:8px 12px; border-radius:8px; font-size:0.78rem; margin-bottom:12px;"></div>

            <button type="submit" class="btn-save-settings" id="btn-save-password" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #000; font-weight: 900;">
              Update Admin Password
            </button>
          </form>
        </div>

      </div>

      <!-- Right Column: Player Management Console & Approval Queues -->
      <div style="display: flex; flex-direction: column; gap: 20px;">

        <!-- Tab Navigation -->
        <div class="tab-bar-nav">
          <button class="tab-bar-btn active" data-tab="players">👥 Player Management (Rig & Controls)</button>
          <button class="tab-bar-btn" data-tab="deposits">💳 Deposit Approvals</button>
          <button class="tab-bar-btn" data-tab="withdrawals">📤 Withdrawal Payouts</button>
          <button class="tab-bar-btn" data-tab="support">🎧 Live Support & Escalations</button>
        </div>

        <!-- TAB 1: PLAYER MANAGEMENT & RIGGING CONSOLE -->
        <div class="studio-panel" id="tab-content-players">
          <div class="panel-header">
            <div class="panel-title">
              <span>👥</span> Player Database & Rigging Console
            </div>
            <div style="display: flex; gap: 8px;">
              <input type="text" id="user-search-input" class="form-input" placeholder="Search by name, email, or ID..." style="width: 260px; padding: 6px 10px;">
            </div>
          </div>

          <div class="table-container">
            <table class="studio-table">
              <thead>
                <tr>
                  <th>Player Details</th>
                  <th>Wallet Balance</th>
                  <th>Games / Won</th>
                  <th>House Net</th>
                  <th>Game Control / Rig Mode</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="players-tbody">
                <!-- Dynamic Player Rows via JS -->
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB 2: DEPOSIT QUEUE -->
        <div class="studio-panel" id="tab-content-deposits" style="display: none;">
          <div class="panel-header">
            <div class="panel-title">
              <span>💳</span> Deposit Verification Queue
            </div>
          </div>

          <div class="table-container">
            <table class="studio-table">
              <thead>
                <tr>
                  <th>Player</th>
                  <th>Amount</th>
                  <th>UTR Number</th>
                  <th>Mobile</th>
                  <th>Receipt</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Decision</th>
                </tr>
              </thead>
              <tbody id="deposits-tbody">
                <!-- Dynamic Deposit Rows -->
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB 3: WITHDRAWAL QUEUE -->
        <div class="studio-panel" id="tab-content-withdrawals" style="display: none;">
          <div class="panel-header">
            <div class="panel-title">
              <span>📤</span> Withdrawal Payout Requests
            </div>
          </div>

          <div class="table-container">
            <table class="studio-table">
              <thead>
                <tr>
                  <th>Player</th>
                  <th>Amount</th>
                  <th>UPI ID</th>
                  <th>Mobile</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="withdrawals-tbody">
                <!-- Dynamic Withdrawal Rows -->
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB 4: SUPPORT TICKETS & ESCALATIONS -->
        <div class="studio-panel" id="tab-content-support" style="display: none;">
          <div class="panel-header">
            <div class="panel-title">
              <span>🎧</span> Live Support Tickets & Escalated Chats
            </div>
            <button class="btn-action-tiny green" id="btn-refresh-admin-support">🔄 Refresh Chats</button>
          </div>

          <div style="display: grid; grid-template-columns: 300px 1fr; gap: 14px; min-height: 480px;">
            <!-- Threads List -->
            <div style="background: #0d0920; border: 1px solid rgba(139, 92, 246, 0.2); border-radius: 12px; padding: 10px; overflow-y: auto; max-height: 520px;" id="admin-support-threads-list">
              <div style="text-align: center; color: #64748b; padding: 20px;">Loading conversations...</div>
            </div>

            <!-- Active Chat Thread View -->
            <div style="display: flex; flex-direction: column; background: #0d0920; border: 1px solid rgba(139, 92, 246, 0.2); border-radius: 12px; overflow: hidden;">
              <!-- Header -->
              <div style="padding: 12px 16px; background: #140e30; border-bottom: 1px solid rgba(139, 92, 246, 0.2); display: flex; justify-content: space-between; align-items: center;" id="admin-chat-header">
                <div>
                  <strong style="color: #fff; font-size: 0.9rem;" id="admin-chat-user-name">Select a conversation</strong>
                  <div style="font-size: 0.72rem; color: #94a3b8;" id="admin-chat-user-meta">-</div>
                </div>
              </div>

              <!-- Message Stream -->
              <div style="flex: 1; padding: 14px; overflow-y: auto; max-height: 380px; display: flex; flex-direction: column; gap: 8px;" id="admin-chat-messages-container">
                <div style="text-align: center; color: #64748b; padding: 30px;">Select a user from the left list to view their live conversation</div>
              </div>

              <!-- Admin Reply Box -->
              <form id="admin-support-reply-form" style="padding: 10px; background: #140e30; border-top: 1px solid rgba(139, 92, 246, 0.2); display: flex; gap: 8px;">
                <input type="hidden" id="admin-reply-user-id" value="">
                <input type="text" id="admin-reply-input" class="form-input" placeholder="Type official admin reply in English/Hinglish..." style="flex: 1;" required>
                <button type="submit" class="btn-save-settings" style="width: auto; padding: 0 16px; white-space: nowrap; background: linear-gradient(135deg, #10b981, #059669);">Send as Admin</button>
              </form>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

  <!-- MODAL: Edit Player & Set Rigging / Password -->
  <div class="modal-overlay" id="player-manage-modal">
    <div class="modal-box">
      <div class="modal-header">
        <div class="modal-title">⚙️ Player Control & Credentials</div>
        <button class="modal-close" data-close-modal>&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="modal-user-id">

        <div style="margin-bottom: 14px; padding: 10px; background: #0d0920; border-radius: 10px; border: 1px solid var(--obsidian-border);">
          <div style="font-weight: 800; color: #fff;" id="modal-user-name">Player Name</div>
          <div style="font-size: 0.75rem; color: #94a3b8; font-family: var(--font-mono);" id="modal-user-email">email@test.com</div>
        </div>

        <!-- Rigging Mode Controller -->
        <div class="form-group">
          <label class="form-label">Game Outcome Control (Rig Mode)</label>
          <select id="modal-user-rig" class="form-input">
            <option value="global">🌐 Global Default (Follows System RTP)</option>
            <option value="fair">⚖️ Strict Provably Fair (97% RTP)</option>
            <option value="house_favored">💰 House Favored (80% RTP)</option>
            <option value="high_win">🚀 High Win / Lucky Streak (110% RTP)</option>
            <option value="force_lose">⚡ STRICT TRAP (Force Loss on Picks)</option>
            <option value="force_win">🌟 LUCKY STAR (Evade Bombs)</option>
          </select>
        </div>

        <!-- Custom RTP % Override -->
        <div class="form-group">
          <label class="form-label">Custom RTP % Override (Leave empty for default)</label>
          <input type="number" id="modal-user-rtp" class="form-input" placeholder="e.g. 75.0 or 110.0" step="any" min="1" max="200">
        </div>

        <!-- Balance Adjust -->
        <div class="form-group">
          <label class="form-label">Adjust Wallet Balance (₹)</label>
          <div style="display: flex; gap: 8px;">
            <input type="number" id="modal-user-balance" class="form-input" placeholder="Set exact balance" step="any" min="0">
            <button type="button" class="btn-action-tiny green" id="btn-modal-update-bal" style="padding: 8px 14px;">Set ₹</button>
          </div>
        </div>

        <!-- Password Reset & Inspector -->
        <div class="form-group">
          <label class="form-label">Set / Reset Password</label>
          <div style="display: flex; gap: 8px; margin-bottom: 6px;">
            <input type="text" id="modal-user-new-password" class="form-input" placeholder="Enter new password" value="Player@123">
            <button type="button" class="btn-action-tiny" id="btn-modal-gen-pass" style="white-space: nowrap;">⚡ Gen</button>
            <button type="button" class="btn-action-tiny green" id="btn-modal-set-pass" style="white-space: nowrap;">Save Key</button>
          </div>
        </div>

        <!-- Ban Toggle -->
        <div class="form-group">
          <label class="form-label">Account Restriction</label>
          <div style="display: flex; gap: 10px;">
            <button type="button" class="btn-action-tiny red" id="btn-modal-ban" style="padding: 8px 16px; flex: 1;">🔒 Ban User</button>
            <button type="button" class="btn-action-tiny green" id="btn-modal-unban" style="padding: 8px 16px; flex: 1;">🔓 Unban User</button>
          </div>
        </div>

        <button type="button" class="btn-save-settings" id="btn-modal-save-all" style="margin-top: 10px;">
          Save Player Profile
        </button>
      </div>
    </div>
  </div>

  <!-- MODAL: Quick Add Money -->
  <div class="modal-overlay" id="add-money-modal">
    <div class="modal-box" style="max-width: 400px;">
      <div class="modal-header">
        <div class="modal-title">➕ Add Money to Player</div>
        <button class="modal-close" data-close-modal>&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="add-money-user-id">
        <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 12px;">
          Player: <strong style="color:#fff;" id="add-money-user-email">user@email.com</strong>
        </div>
        <div class="form-group">
          <label class="form-label">Amount to Add (₹)</label>
          <input type="number" id="add-money-amount-input" class="form-input" placeholder="Enter amount e.g. 500 or 25000" step="any" min="1" required>
        </div>
        <button type="button" class="btn-save-settings" id="btn-confirm-add-money" style="background: linear-gradient(135deg, #10b981, #059669);">
          Confirm Add ₹
        </button>
      </div>
    </div>
  </div>

  <!-- MODAL: Quick Deduct Money -->
  <div class="modal-overlay" id="deduct-money-modal">
    <div class="modal-box" style="max-width: 400px;">
      <div class="modal-header">
        <div class="modal-title">➖ Deduct Money from Player</div>
        <button class="modal-close" data-close-modal>&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="deduct-money-user-id">
        <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 12px;">
          Player: <strong style="color:#fff;" id="deduct-money-user-email">user@email.com</strong>
        </div>
        <div class="form-group">
          <label class="form-label">Amount to Deduct (₹)</label>
          <input type="number" id="deduct-money-amount-input" class="form-input" placeholder="Enter amount to deduct" step="any" min="1" required>
        </div>
        <button type="button" class="btn-save-settings" id="btn-confirm-deduct-money" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
          Confirm Deduct ₹
        </button>
      </div>
    </div>
  </div>

  <script src="assets/js/admin.js"></script>
</body>
</html>
