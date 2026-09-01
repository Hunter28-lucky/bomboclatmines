<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

requireAuth();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard - <?= htmlspecialchars(APP_NAME) ?></title>
  <link rel="icon" type="image/png" href="assets/images/favicon.png">
  <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

  <header class="app-header">
    <div class="brand">
      <a href="index.php" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
        <div class="brand-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
          </svg>
        </div>
        <h1 class="brand-title"><?= htmlspecialchars(APP_NAME) ?></h1>
      </a>
    </div>

    <div class="header-right">
      <div class="balance-card">
        <div class="balance-label">Balance</div>
        <div class="balance-val">₹<?= number_format($user['balance'], 0) ?></div>
      </div>
      <a href="index.php" class="btn-secondary" style="padding: 6px 12px; font-size: 0.75rem;">Play Game</a>
      <a href="api/auth.php?action=logout" class="logout-btn" style="text-decoration: none;">Logout</a>
    </div>
  </header>

  <main style="flex: 1; max-width: 780px; width: 100%; margin: 20px auto; padding: 0 16px;">
    
    <!-- Account Overview Card -->
    <div style="background: var(--bg-card); backdrop-filter: blur(16px); border: 1px solid var(--border-card); border-radius: 20px; padding: 22px; margin-bottom: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
      <h2 style="font-size: 1.3rem; font-weight: 800; color: #38bdf8; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
        <span>👤</span> Account Overview
      </h2>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px;">
        <div style="background: rgba(15,23,42,0.6); padding: 14px; border-radius: 12px; border: 1px solid rgba(71,85,105,0.4);">
          <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Full Name</div>
          <div style="font-size: 1.05rem; font-weight: 700; color: #f8fafc; margin-top: 4px;"><?= htmlspecialchars($user['full_name'] ?: 'Not set') ?></div>
        </div>

        <div style="background: rgba(15,23,42,0.6); padding: 14px; border-radius: 12px; border: 1px solid rgba(71,85,105,0.4);">
          <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Email Address</div>
          <div style="font-size: 0.95rem; font-weight: 700; color: #f8fafc; margin-top: 4px;"><?= htmlspecialchars($user['email']) ?></div>
        </div>

        <div style="background: rgba(15,23,42,0.6); padding: 14px; border-radius: 12px; border: 1px solid rgba(16,185,129,0.3);">
          <div style="font-size: 0.75rem; color: #34d399; text-transform: uppercase;">Current Balance</div>
          <div style="font-size: 1.35rem; font-weight: 800; color: #10b981; margin-top: 2px;">₹<?= number_format($user['balance'], 2) ?></div>
        </div>

        <div style="background: rgba(15,23,42,0.6); padding: 14px; border-radius: 12px; border: 1px solid rgba(71,85,105,0.4);">
          <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Successful Topups</div>
          <div style="font-size: 1.2rem; font-weight: 800; color: #38bdf8; margin-top: 4px;"><?= $user['topups'] ?></div>
        </div>
      </div>
    </div>

    <!-- Quick Navigation Actions -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px;">
      <a href="index.php" class="btn-primary-action" style="text-decoration: none;">
        <span>🎮</span> Play Mines
      </a>
      <button class="btn-primary-action" onclick="window.location.href='index.php'" style="background: linear-gradient(135deg, #10b981, #059669);">
        <span>💳</span> Deposit & Withdraw
      </button>
    </div>

  </main>

</body>
</html>
