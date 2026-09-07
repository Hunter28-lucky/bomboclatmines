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
  <title>Game Hub & Dashboard - <?= htmlspecialchars(APP_NAME) ?></title>
  <link rel="icon" type="image/png" href="assets/images/favicon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/app.css">
  <style>
    .hub-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }
    .game-card-launch {
      background: rgba(18, 11, 40, 0.85);
      border: 1.5px solid rgba(139, 92, 246, 0.35);
      border-radius: 20px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      backdrop-filter: blur(16px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
      transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
      position: relative;
      overflow: hidden;
    }
    .game-card-launch:hover {
      transform: translateY(-4px);
      border-color: #c084fc;
      box-shadow: 0 14px 40px rgba(168, 85, 247, 0.35);
    }
    .game-card-launch.chicken:hover {
      border-color: #34d399;
      box-shadow: 0 14px 40px rgba(16, 185, 129, 0.35);
    }
    .game-card-badge {
      position: absolute;
      top: 14px;
      right: 14px;
      font-size: 0.68rem;
      font-weight: 800;
      padding: 3px 8px;
      border-radius: 6px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .badge-purple {
      background: rgba(168, 85, 247, 0.25);
      color: #c084fc;
      border: 1px solid rgba(168, 85, 247, 0.4);
    }
    .badge-green {
      background: rgba(16, 185, 129, 0.25);
      color: #34d399;
      border: 1px solid rgba(16, 185, 129, 0.4);
    }
  </style>
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
        <div class="balance-val">₹<?= number_format($user['balance'], 2) ?></div>
      </div>
      <?php if (isAdmin()): ?>
        <a href="admin.php" class="header-admin-crown-btn" title="Open Operator Studio">👑</a>
      <?php endif; ?>
      <a href="api/auth.php?action=logout" class="logout-btn" style="text-decoration: none;">Logout</a>
    </div>
  </header>

  <main style="flex: 1; max-width: 860px; width: 100%; margin: 20px auto; padding: 0 16px;">
    
    <!-- Games Arena Hub -->
    <div style="margin-bottom: 24px;">
      <h2 style="font-size: 1.25rem; font-weight: 800; color: #f8fafc; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
        <span>🎮</span> Games Arena
      </h2>

      <div class="hub-grid">
        <!-- Game 1: Bombaclat Mines -->
        <div class="game-card-launch">
          <span class="game-card-badge badge-purple">Popular</span>
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">
            <div style="width:48px; height:48px; border-radius:14px; background:rgba(147,51,234,0.25); border:1px solid rgba(168,85,247,0.4); display:flex; align-items:center; justify-content:center; font-size:1.8rem;">
              💣
            </div>
            <div>
              <h3 style="font-size:1.15rem; font-weight:900; color:#fff; margin:0;">BOMBACLAT MINES</h3>
              <p style="font-size:0.75rem; color:#94a3b8; margin:2px 0 0 0;">Reveal gems, dodge bombs & multiply cash</p>
            </div>
          </div>
          <a href="mines.php" class="btn-primary-action" style="text-decoration: none; text-align: center; margin-top: auto;">
            <span>💣 Play Mines Now</span>
          </a>
        </div>

        <!-- Game 2: Chicken Road -->
        <div class="game-card-launch chicken">
          <span class="game-card-badge badge-green">New Release</span>
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">
            <div style="width:48px; height:48px; border-radius:14px; background:rgba(16,185,129,0.25); border:1px solid rgba(52,211,153,0.4); display:flex; align-items:center; justify-content:center; font-size:1.8rem;">
              🐔
            </div>
            <div>
              <h3 style="font-size:1.15rem; font-weight:900; color:#fff; margin:0;">CHICKEN ROAD</h3>
              <p style="font-size:0.75rem; color:#94a3b8; margin:2px 0 0 0;">Cross the lanes, climb multiplier & cash out</p>
            </div>
          </div>
          <a href="cheakenselim/game.html" class="btn-primary-action" style="text-decoration: none; text-align: center; background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 0 20px rgba(16,185,129,0.4); margin-top: auto;">
            <span>🐔 Play Chicken Road</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Account Overview Card -->
    <div style="background: var(--bg-card); backdrop-filter: blur(16px); border: 1px solid var(--border-card); border-radius: 20px; padding: 22px; margin-bottom: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
      <h2 style="font-size: 1.25rem; font-weight: 800; color: #38bdf8; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
        <span>👤</span> Account & Shared Balance
      </h2>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px;">
        <div style="background: rgba(15,23,42,0.6); padding: 14px; border-radius: 12px; border: 1px solid rgba(71,85,105,0.4);">
          <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Player Name</div>
          <div style="font-size: 1.05rem; font-weight: 700; color: #f8fafc; margin-top: 4px;"><?= htmlspecialchars($user['full_name'] ?: 'Player') ?></div>
        </div>

        <div style="background: rgba(15,23,42,0.6); padding: 14px; border-radius: 12px; border: 1px solid rgba(71,85,105,0.4);">
          <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Email Address</div>
          <div style="font-size: 0.95rem; font-weight: 700; color: #f8fafc; margin-top: 4px;"><?= htmlspecialchars($user['email']) ?></div>
        </div>

        <div style="background: rgba(15,23,42,0.6); padding: 14px; border-radius: 12px; border: 1px solid rgba(16,185,129,0.3);">
          <div style="font-size: 0.75rem; color: #34d399; text-transform: uppercase;">Shared Wallet Balance</div>
          <div style="font-size: 1.35rem; font-weight: 800; color: #10b981; margin-top: 2px;">₹<?= number_format($user['balance'], 2) ?></div>
        </div>

        <div style="background: rgba(15,23,42,0.6); padding: 14px; border-radius: 12px; border: 1px solid rgba(71,85,105,0.4);">
          <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">Successful Topups</div>
          <div style="font-size: 1.2rem; font-weight: 800; color: #38bdf8; margin-top: 4px;"><?= $user['topups'] ?></div>
        </div>
      </div>
    </div>

    <!-- Quick Navigation Actions -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 12px; margin-bottom: 24px;">
      <button class="btn-primary-action" onclick="window.location.href='index.php'" style="background: linear-gradient(135deg, #7c3aed, #9333ea);">
        <span>💳 Deposit & Withdraw in Cashier</span>
      </button>
    </div>

  </main>

</body>
</html>
