<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit;
}

$avatarUrl = !empty($user['avatar_url']) ? $user['avatar_url'] : null;
$firstLetter = strtoupper(substr($user['email'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <link rel="icon" type="image/png" href="assets/images/favicon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
  <!-- Psychophysiological Luminance Bloom Overlay (25L-50L Sudden Flash) -->
  <div id="screen-luminance-flash" class="screen-luminance-flash"></div>
  
  <!-- Live Big-Win Floating Notification Toasts -->
  <div id="live-toast-container" class="live-toast-container"></div>

  <!-- Top Navigation Header -->
  <header class="app-header">
    <div class="header-left-group">
      <button class="menu-hamburger-btn" id="btn-mobile-menu" aria-label="Menu" data-open-modal="account-modal">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>
      <div class="brand-section">
        <svg class="brand-bolt" width="22" height="22" viewBox="0 0 24 24" fill="#a855f7">
          <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
        </svg>
        <div class="brand-titles">
          <span class="brand-title">BOMBACLAT</span>
          <span class="mine-highlight">MINE</span>
        </div>
      </div>
    </div>

    <div class="header-center-badge">
      <span>🚀</span>
      <span>Play Smart. <span class="badge-highlight">Cash Big.</span></span>
    </div>

    <div class="header-right">
      <!-- Wallet Balance Capsule with + Button -->
      <div class="header-wallet-capsule" id="header-wallet-badge">
        <div class="wallet-balance-info">
          <div class="wallet-val" id="user-balance-display">₹<?= number_format($user['balance'], 2) ?></div>
          <div class="wallet-label">WALLET BALANCE</div>
        </div>
        <button class="wallet-plus-btn" data-open-modal="deposit-modal" aria-label="Deposit">+</button>
      </div>

      <!-- Bonus Gift Icon -->
      <button class="gift-box-btn" data-open-modal="deposit-modal" aria-label="Gift Bonus">
        <img src="assets/images/gift_box.png" alt="Gift" class="gift-btn-img">
      </button>

      <!-- Neon Lime Deposit Button -->
      <button class="btn-deposit-neon" data-open-modal="deposit-modal">
        DEPOSIT
      </button>

      <?php if (isAdmin()): ?>
        <a href="admin.php" class="btn-admin-pill">ADMIN</a>
      <?php endif; ?>

      <button class="avatar-circle-btn" data-open-modal="account-modal" aria-label="Account Settings">
        <?php if ($avatarUrl): ?>
          <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar">
        <?php else: ?>
          <span><?= $firstLetter ?></span>
        <?php endif; ?>
      </button>

      <button class="notification-bell-btn" aria-label="Notifications" onclick="alert('No new notifications');">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
          <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <span class="bell-dot"></span>
      </button>
    </div>
  </header>

  <!-- Main 3-Column Dashboard Grid -->
  <main class="main-dashboard-grid">

    <!-- Column 1: Left Betting Controls (Desktop Deck) -->
    <aside class="left-betting-column desktop-only-deck">

      <!-- Bet Input Card -->
      <div class="bet-input-card">
        <div class="bet-card-header">
          <span class="bet-card-label">BET AMOUNT</span>
          <span class="bet-card-min">Min ₹10</span>
        </div>
        <div class="bet-card-main-row">
          <div class="bet-amount-wrapper">
            <span class="bet-rupee-symbol">₹</span>
            <input type="number" id="bet-amount-input" class="bet-number-input" value="100" min="10" step="any">
          </div>
          <div class="bet-stepper-btns">
            <button class="stepper-btn" data-action="minus" aria-label="Decrease Bet">-</button>
            <button class="stepper-btn" data-action="plus" aria-label="Increase Bet">+</button>
          </div>
        </div>
        <div class="bet-shortcuts-row">
          <button class="shortcut-pill" data-action="half">/2</button>
          <button class="shortcut-pill" data-action="double">2X</button>
          <button class="shortcut-pill" data-action="max">MAX</button>
        </div>
      </div>

      <!-- Start Mining Action Button -->
      <button class="btn-start-mining" id="big-action-btn-desktop">
        <span>▶</span> START MINING
      </button>

      <!-- BOMBS Selector -->
      <div>
        <div class="section-sublabel" style="display: flex; justify-content: space-between; align-items: center;">
          <span>BOMBS</span>
          <span id="risk-level-badge" style="font-size: 0.65rem; color: #a3e635; font-weight: 800;">LOW RISK</span>
        </div>
        <div class="options-pill-grid six-cols">
          <button class="choice-pill-btn bomb-pill-btn" data-bombs="1">1</button>
          <button class="choice-pill-btn bomb-pill-btn active" data-bombs="2">2</button>
          <button class="choice-pill-btn bomb-pill-btn" data-bombs="3">3</button>
          <button class="choice-pill-btn bomb-pill-btn" data-bombs="5">5</button>
          <button class="choice-pill-btn bomb-pill-btn" data-bombs="10">10</button>
          <button class="choice-pill-btn bomb-pill-btn" data-bombs="15">15</button>
        </div>
      </div>

      <!-- GRID SIZE Selector -->
      <div>
        <div class="section-sublabel">GRID SIZE</div>
        <div class="options-pill-grid three-cols">
          <button class="choice-pill-btn grid-pill-btn active" data-size="16">4x4</button>
          <button class="choice-pill-btn grid-pill-btn" data-size="25">5x5</button>
          <button class="choice-pill-btn grid-pill-btn" data-size="36">6x6</button>
        </div>
      </div>

      <!-- Quick Psychological Agency Tools -->
      <div class="agency-tools-row">
        <button class="agency-tool-btn" id="btn-ghostmap-toggle" title="View previous round bomb positions">
          <span>👻</span> Ghost Map
        </button>
        <button class="agency-tool-btn" id="btn-autopick" title="Auto-select a safe tile instantly">
          <span>⚡</span> Auto Pick
        </button>
      </div>

      <!-- Footer Trust Badges -->
      <div class="footer-trust-row">
        <span>🛡️ Provably Fair</span>
        <span>🟢 100% Secure</span>
      </div>

      <!-- Dare to Win Big Promo Card -->
      <div class="promo-dare-card">
        <div class="promo-text-side">
          <div class="promo-title">DARE TO WIN BIG?</div>
          <div class="promo-subtext">Higher risk. Higher reward.</div>
          <button class="btn-try-bombs" id="btn-try-5-bombs">Try 10 Bombs</button>
        </div>
        <img src="assets/images/skull_bomb.png" alt="Skull Bomb" class="promo-bomb-img">
      </div>

    </aside>

    <!-- Column 2: Center Game Arena & Cash Out Bar -->
    <section class="center-game-column panel-card" id="arena-panel">

      <!-- 4-Stat Strip: Next Multiplier | Diamonds | Bombs | Potential Win -->
      <div class="arena-stats-strip-four">
        <div class="stat-quad-cell">
          <div class="stat-quad-label">NEXT MULTIPLIER</div>
          <div class="stat-quad-val stat-cyan" id="next-multiplier-display">1.08x</div>
        </div>

        <div class="stat-quad-cell">
          <div class="stat-quad-label">DIAMONDS</div>
          <div class="stat-quad-val stat-cyan"><span style="font-size: 1.05rem;">💎</span> <span id="gem-counter-val">14</span></div>
        </div>

        <div class="stat-quad-cell">
          <div class="stat-quad-label">BOMBS</div>
          <div class="stat-quad-val stat-red"><span style="font-size: 1.05rem;">💣</span> <span id="bomb-counter-val">2</span></div>
        </div>

        <div class="stat-quad-cell" style="text-align: right;">
          <div class="stat-quad-label">POTENTIAL WIN</div>
          <div class="stat-quad-val stat-green" id="potential-win-display">₹108.00</div>
        </div>
      </div>

      <!-- Dynamic Near-Miss Alert Banner -->
      <div class="near-miss-banner" id="near-miss-banner" style="display: none;">
        <div class="near-miss-glow"></div>
        <span class="near-miss-bolt">⚡</span>
        <div class="near-miss-text-group">
          <div class="near-miss-title">SO CLOSE!</div>
          <div class="near-miss-sub" id="near-miss-subtext">Next diamond was worth ₹250.00 (1.45x)</div>
        </div>
      </div>

      <!-- Center Flanked Grid with Glowing Circular Badges -->
      <div class="arena-matrix-container">
        
        <!-- Left Floating Cyan Badge: Diamonds -->
        <div class="floating-side-badge badge-diamonds-cyan">
          <span class="badge-icon-emoji">💎</span>
          <span class="badge-count-text" id="floating-gem-count">14</span>
        </div>

        <!-- Mines Grid Canvas Stage -->
        <div class="mines-grid-stage" id="grid-stage">
          <div class="mines-grid-canvas" id="mines-grid">
            <!-- Dynamic Grid Buttons -->
          </div>
        </div>

        <!-- Right Floating Red Badge: Bombs -->
        <div class="floating-side-badge badge-bombs-red">
          <span class="badge-icon-emoji">💣</span>
          <span class="badge-count-text" id="floating-bomb-count">2</span>
        </div>

      </div>

      <!-- Multiplier Step Ladder Strip -->
      <div class="multiplier-ladder-box">
        <button class="ladder-arrow-button" id="ladder-arrow-left" aria-label="Previous">&lt;</button>
        <div class="ladder-scroll-lane" id="ladder-scroll-wrapper">
          <!-- Dynamic Step Pills -->
        </div>
        <button class="ladder-chart-button" id="ladder-chart-btn" aria-label="Stats">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <line x1="18" y1="20" x2="18" y2="10"></line>
            <line x1="12" y1="20" x2="12" y2="4"></line>
            <line x1="6" y1="20" x2="6" y2="14"></line>
          </svg>
        </button>
      </div>

      <!-- Cash Out & Auto Cashout Dual Control Bar -->
      <div class="cashout-control-bar">
        <!-- Cash Out Main Card -->
        <div class="cashout-action-card disabled" id="cashout-card-btn">
          <div class="cashout-main-info">
            <div class="cashout-label-top">CASH OUT</div>
            <div class="cashout-amount-huge" id="cashout-amount-text">₹105.00</div>
            <div class="cashout-subtext" id="cashout-subtext">Lock in your winnings!</div>
          </div>
          <div class="cashout-arrow-circle">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#a3e635" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="13 17 18 12 13 7"></polyline>
              <polyline points="6 17 11 12 6 7"></polyline>
            </svg>
          </div>
        </div>

        <!-- Auto Cashout Card -->
        <div class="auto-cashout-box" id="auto-cashout-box-btn" style="cursor: pointer;">
          <div class="auto-cashout-label">AUTO CASH OUT</div>
          <div class="auto-cashout-row-controls">
            <span class="toggle-pill-off" id="auto-cashout-toggle">OFF</span>
            <span class="auto-gear-icon" id="auto-cashout-gear">⚙️</span>
          </div>
        </div>
      </div>

      <!-- Mobile Integrated Compact Bet Row (Under Game Arena) -->
      <div class="mobile-compact-bet-deck">
        <div class="compact-bet-row-three">
          <!-- 1. Bet Amount with - / + -->
          <div class="compact-cell bet-cell">
            <div class="compact-label-row">
              <span class="compact-label">BET AMOUNT</span>
              <span class="compact-sub-badge">Min ₹10</span>
            </div>
            <div class="compact-input-stepper-wrap">
              <span class="rupee-char">₹</span>
              <input type="number" id="bet-amount-input-mobile" class="compact-bet-input" value="100" min="10" step="any">
              <div class="compact-steppers">
                <button class="stepper-btn-mini" data-action="minus">-</button>
                <button class="stepper-btn-mini" data-action="plus">+</button>
              </div>
            </div>
          </div>

          <!-- 2. Bombs Pills: 2, 3, 5, 7 -->
          <div class="compact-cell bombs-cell">
            <div class="compact-label-row">
              <span class="compact-label">BOMBS</span>
            </div>
            <div class="compact-pills-row">
              <button class="choice-pill-btn bomb-pill-btn active" data-bombs="2">2</button>
              <button class="choice-pill-btn bomb-pill-btn" data-bombs="3">3</button>
              <button class="choice-pill-btn bomb-pill-btn" data-bombs="5">5</button>
              <button class="choice-pill-btn bomb-pill-btn" data-bombs="7">7</button>
            </div>
          </div>

          <!-- 3. Grid Size Pills: 4x4, 5x5, 6x6 -->
          <div class="compact-cell grid-cell">
            <div class="compact-label-row">
              <span class="compact-label">GRID SIZE</span>
            </div>
            <div class="compact-pills-row">
              <button class="choice-pill-btn grid-pill-btn active" data-size="16">4x4</button>
              <button class="choice-pill-btn grid-pill-btn" data-size="25">5x5</button>
              <button class="choice-pill-btn grid-pill-btn" data-size="36">6x6</button>
            </div>
          </div>
        </div>

        <!-- Big Start Mining Action Button -->
        <button class="btn-start-mining" id="big-action-btn">
          <span>▶</span> START MINING
        </button>

        <!-- Fast Replay Acceleration Bar -->
        <div class="quick-rebet-bar" id="quick-rebet-bar">
          <button class="btn-fast-rebet" id="btn-quick-rebet">
            <span>🔁</span> REPLAY ₹100.00
          </button>
          <button class="btn-fast-rebet-double" id="btn-double-rebet">
            <span>⚡</span> 2X REPLAY ₹200.00
          </button>
        </div>

        <!-- Dare to Win Big Promo Card (Mobile) -->
        <div class="promo-dare-card mobile-promo-card">
          <div class="promo-text-side">
            <div class="promo-title">DARE TO WIN BIG?</div>
            <div class="promo-subtext">Higher risk. Higher reward.</div>
            <button class="btn-try-bombs" id="btn-try-5-bombs-mobile">TRY 10 BOMBS</button>
          </div>
          <img src="assets/images/skull_bomb.png" alt="Skull Bomb" class="promo-bomb-img">
        </div>

        <!-- Trust Row -->
        <div class="footer-trust-row" style="margin-top: 6px;">
          <span>🛡️ Provably Fair</span>
          <span>🔒 100% Secure</span>
        </div>
      </div>

    </section>

    <!-- Column 3: Right Sidebar Cards -->
    <aside class="right-sidebar-column">

      <!-- Card 1: STATS -->
      <div class="panel-card">
        <div class="sidebar-card-header">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="#a855f7">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
          </svg>
          <span>STATS</span>
        </div>
        <div>
          <div class="stat-row-item">
            <span class="stat-row-label">Games Played</span>
            <span class="stat-row-val" id="stats-games-played">1,248</span>
          </div>
          <div class="stat-row-item">
            <span class="stat-row-label">Biggest Win</span>
            <span class="stat-row-val" id="stats-biggest-win">₹12,450</span>
          </div>
          <div class="stat-row-item">
            <span class="stat-row-label">Total Won</span>
            <span class="stat-row-val" style="color: var(--neon-green);" id="stats-total-won">₹45,320</span>
          </div>
        </div>
      </div>

      <!-- Card 2: RECENT WINS -->
      <div class="panel-card">
        <div class="sidebar-card-header">
          <span>🏆</span>
          <span>RECENT WINS</span>
        </div>
        <div id="recent-wins-container">
          <div class="recent-win-row">
            <span class="recent-win-player">Rohit_24</span>
            <span class="recent-win-amount">₹8,450</span>
            <span class="recent-win-mult">1.42x</span>
          </div>
          <div class="recent-win-row">
            <span class="recent-win-player">BeastMode</span>
            <span class="recent-win-amount">₹5,230</span>
            <span class="recent-win-mult">1.31x</span>
          </div>
          <div class="recent-win-row">
            <span class="recent-win-player">LuckyGuy</span>
            <span class="recent-win-amount">₹3,120</span>
            <span class="recent-win-mult">1.25x</span>
          </div>
          <div class="recent-win-row">
            <span class="recent-win-player">MineHunter</span>
            <span class="recent-win-amount">₹2,450</span>
            <span class="recent-win-mult">1.18x</span>
          </div>
          <div class="recent-win-row">
            <span class="recent-win-player">King_07</span>
            <span class="recent-win-amount">₹1,980</span>
            <span class="recent-win-mult">1.15x</span>
          </div>
        </div>
      </div>

      <!-- Card 3: DAILY CHALLENGE -->
      <div class="panel-card">
        <div class="sidebar-card-header">
          <span>🎯</span>
          <span>DAILY CHALLENGE</span>
        </div>
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
          <div>
            <div class="challenge-desc-text">Win 10 games to get</div>
            <div class="challenge-bonus-tag">₹500 Bonus</div>
            <div class="challenge-progress-bar-wrapper">
              <div class="challenge-progress-meta">
                <span id="challenge-count-text">6 / 10</span>
              </div>
              <div class="progress-track">
                <div class="progress-fill-green" style="width: 60%;"></div>
              </div>
            </div>
          </div>
          <img src="assets/images/gift_box.png" alt="Gift Box" class="challenge-gift-img">
        </div>
      </div>

    </aside>

  </main>

  <!-- Bottom Section: LIVE BETS Table -->
  <section class="live-bets-full-card">
    <div class="live-bets-inner-panel">
      <div class="bets-top-header">
        <div class="bets-header-left">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="#a855f7">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
          </svg>
          <span>LIVE BETS</span>
        </div>
        <div class="bets-pill-tabs">
          <button class="bets-tab-pill active">ALL BETS</button>
          <button class="bets-tab-pill">MY BETS</button>
          <button class="bets-tab-pill">TOP WINS</button>
        </div>
      </div>

      <div style="overflow-x: auto;">
        <table class="live-table">
          <thead>
            <tr>
              <th>PLAYER</th>
              <th>BET AMOUNT</th>
              <th>BOMBS</th>
              <th>MULTIPLIER</th>
              <th>PAYOUT</th>
              <th>TIME</th>
            </tr>
          </thead>
          <tbody id="live-bets-tbody">
            <!-- Dynamic Bet Rows -->
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- Mobile Bottom Sticky Navigation Dock (from Reference Mockup) -->
  <nav class="mobile-bottom-dock">
    <button class="dock-nav-item active" id="dock-btn-game" onclick="window.scrollTo({top:0, behavior:'smooth'});">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
        <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
        <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
        <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
        <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
      </svg>
      <span>GAME</span>
    </button>
    <button class="dock-nav-item" id="dock-btn-mybets" data-open-modal="account-modal">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
        <polyline points="14 2 14 8 20 8"></polyline>
        <line x1="16" y1="13" x2="8" y2="13"></line>
        <line x1="16" y1="17" x2="8" y2="17"></line>
      </svg>
      <span>MY BETS</span>
    </button>
    <button class="dock-nav-item" id="dock-btn-topwins" onclick="document.querySelector('.live-bets-full-card').scrollIntoView({behavior:'smooth'});">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
        <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
        <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
        <path d="M4 22h16"></path>
        <path d="M10 14.66V17c0 .55-.45 1-1 1H7.5c-.55 0-1 .45-1 1v1c0 .55.45 1 1 1h9c.55 0 1-.45 1-1v-1c0-.55-.45-1-1-1H15c-.55 0-1-.45-1-1v-2.34"></path>
        <path d="M18 9V4a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v5a6 6 0 0 0 12 0Z"></path>
      </svg>
      <span>TOP WINS</span>
    </button>
    <button class="dock-nav-item" id="dock-btn-profile" data-open-modal="account-modal">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
        <circle cx="12" cy="7" r="4"></circle>
      </svg>
      <span>PROFILE</span>
    </button>
  </nav>

  <!-- Floating Live Support Button -->
  <button class="floating-live-support-btn" data-open-modal="live-support-modal" id="btn-open-live-support">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
      <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"></path>
    </svg>
    <span>Live Support</span>
  </button>

  <!-- MODAL: Deposit Funds (UPI / RazorPay Style) -->
  <div class="modal-overlay" id="deposit-modal">
    <div class="modal-box" style="max-width: 420px;">
      <div class="modal-header">
        <h3 class="modal-title">+ Add Funds</h3>
        <button class="modal-close-btn" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body">
        <div id="deposit-form-content">
          <div style="text-align: center; margin-bottom: 16px;">
            <div style="display: inline-block; background: #ffffff; padding: 10px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
              <img src="assets/images/qrpayment.jpeg" alt="UPI QR Code" style="width: 160px; height: 160px; object-fit: contain; border-radius: 10px; display: block;" onerror="this.src='qrpayment.jpeg'">
            </div>
            <p style="font-size: 0.78rem; color: #94a3b8; margin-top: 8px;">Scan with Google Pay, PhonePe, Paytm, or any UPI app</p>
          </div>

          <form id="deposit-form" enctype="multipart/form-data">
            <div class="form-group">
              <label class="form-label" for="deposit-amount">Deposit Amount (₹) <span style="color: #ef4444;">*</span></label>
              <input type="number" id="deposit-amount" name="amount" class="form-input" min="50" step="any" placeholder="Enter deposit amount (Min ₹50)" required>
              <div class="quick-chip-grid" style="display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px;">
                <button type="button" class="btn-deposit-chip" data-amt="100">₹100</button>
                <button type="button" class="btn-deposit-chip" data-amt="250">₹250</button>
                <button type="button" class="btn-deposit-chip" data-amt="500">₹500</button>
                <button type="button" class="btn-deposit-chip" data-amt="1000">₹1,000</button>
                <button type="button" class="btn-deposit-chip" data-amt="2000">₹2,000</button>
                <button type="button" class="btn-deposit-chip" data-amt="5000">₹5,000</button>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="deposit-utr">UTR / Transaction Number <span style="color: #ef4444;">*</span></label>
              <input type="text" id="deposit-utr" name="utr_number" class="form-input" placeholder="e.g. 423589123456" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="deposit-phone">Mobile Number</label>
              <input type="tel" id="deposit-phone" name="mobile_number" class="form-input" placeholder="10-digit mobile number">
            </div>

            <div class="form-group">
              <label class="form-label">Payment Screenshot</label>
              <div style="border: 2px dashed rgba(168, 85, 247, 0.4); border-radius: 12px; padding: 14px; text-align: center; cursor: pointer; background: rgba(14, 10, 32, 0.6);" onclick="document.getElementById('deposit-screenshot').click();">
                <input type="file" id="deposit-screenshot" name="screenshot" accept="image/*" style="display: none;">
                <div style="font-size: 0.8rem; color: #a855f7; font-weight: 700;">Tap to select screenshot</div>
                <div style="font-size: 0.68rem; color: #64748b; margin-top: 2px;">PNG, JPG up to 5MB</div>
              </div>
              <div id="screenshot-preview-container" style="display: none; margin-top: 8px; text-align: center;">
                <img id="screenshot-preview-img" src="" alt="Preview" style="max-height: 80px; border-radius: 8px; border: 1px solid #a855f7;">
              </div>
            </div>

            <button type="submit" class="btn-primary-action" style="margin-top: 10px;">Submit Payment</button>
          </form>
        </div>

        <div id="deposit-success-view" style="display: none; text-align: center; padding: 20px 10px;">
          <div style="font-size: 3rem; margin-bottom: 8px;">✅</div>
          <h4 style="font-size: 1.2rem; font-weight: 800; color: #34d399; margin-bottom: 6px;">Payment Submitted!</h4>
          <p style="font-size: 0.85rem; color: #cbd5e1; margin-bottom: 18px;">Your payment has been received and is pending admin verification. Your balance will be updated shortly.</p>
          <button class="btn-primary-action" data-close-modal style="width: 100%;">Back to Game</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL: Withdraw Funds (Instant UPI) -->
  <div class="modal-overlay" id="withdrawal-modal">
    <div class="modal-box" style="max-width: 440px;">
      <div class="modal-header">
        <h3 class="modal-title">📤 Withdraw Funds</h3>
        <button class="modal-close-btn" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body">
        <!-- Balance Status Bar -->
        <div style="background: rgba(16, 11, 38, 0.9); border: 1px solid rgba(139, 92, 246, 0.3); border-radius: 12px; padding: 12px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
          <div>
            <div style="font-size: 0.68rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Available Balance</div>
            <div style="font-size: 1.25rem; font-weight: 900; color: #34d399;" id="withdraw-available-bal">₹<?= number_format($user['balance'], 2) ?></div>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 0.68rem; color: #94a3b8; font-weight: 700;">PAYOUT SPEED</div>
            <div style="font-size: 0.8rem; font-weight: 800; color: var(--accent-cyan);">⚡ Instant UPI</div>
          </div>
        </div>

        <div id="withdrawal-form-content">
          <form id="withdrawal-form">
            <div class="form-group">
              <label class="form-label" for="withdraw-amount">Withdrawal Amount (₹) <span style="color: #ef4444;">*</span></label>
              <input type="number" id="withdraw-amount" name="amount" class="form-input" min="500" step="any" placeholder="Min ₹500" required>
              <div class="quick-chip-grid" style="display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px;">
                <button type="button" class="btn-withdraw-chip" data-amt="500">₹500</button>
                <button type="button" class="btn-withdraw-chip" data-amt="1000">₹1,000</button>
                <button type="button" class="btn-withdraw-chip" data-amt="2500">₹2,500</button>
                <button type="button" class="btn-withdraw-chip" data-amt="5000">₹5,000</button>
                <button type="button" class="btn-withdraw-chip" data-amt="max">MAX</button>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="withdraw-upi">Your UPI ID / VPA <span style="color: #ef4444;">*</span></label>
              <input type="text" id="withdraw-upi" name="upi_id" class="form-input" placeholder="e.g. yourname@oksbi / 9876543210@paytm" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="withdraw-phone">Registered Mobile Number <span style="color: #ef4444;">*</span></label>
              <input type="tel" id="withdraw-phone" name="mobile_number" class="form-input" placeholder="10-digit phone number" pattern="[0-9]{10}" required>
            </div>

            <button type="submit" class="btn-primary-action" id="btn-submit-withdraw" style="margin-top: 12px; background: linear-gradient(135deg, #10b981, #059669);">
              Request Withdrawal (Instant UPI)
            </button>
          </form>
        </div>

        <div id="withdrawal-success-view" style="display: none; text-align: center; padding: 20px 10px;">
          <div style="font-size: 3rem; margin-bottom: 8px;">🎉</div>
          <h4 style="font-size: 1.2rem; font-weight: 800; color: #34d399; margin-bottom: 6px;">Withdrawal Requested!</h4>
          <p style="font-size: 0.85rem; color: #cbd5e1; margin-bottom: 18px;">Your withdrawal request has been placed and will be transferred to your UPI account shortly.</p>
          <button class="btn-primary-action" data-close-modal style="width: 100%;">Back to Game</button>
        </div>

        <!-- Withdrawal History Section -->
        <div style="margin-top: 20px; border-top: 1px solid rgba(139, 92, 246, 0.2); padding-top: 14px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="font-size: 0.78rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Recent Withdrawals</span>
            <button type="button" id="btn-refresh-withdrawals" style="background: none; border: none; color: var(--accent-purple); font-size: 0.75rem; font-weight: 700; cursor: pointer;">🔄 Refresh</button>
          </div>
          <div id="withdrawal-history-list" style="max-height: 140px; overflow-y: auto; font-size: 0.75rem; color: #cbd5e1;">
            <div style="text-align: center; color: #64748b; padding: 10px;">No recent withdrawal requests</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL: Account & Profile -->
  <div class="modal-overlay" id="account-modal">
    <div class="modal-box" style="max-width: 400px;">
      <div class="modal-header">
        <h3 class="modal-title">My Account</h3>
        <button class="modal-close-btn" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body">
        <div style="text-align: center; margin-bottom: 16px;">
          <div style="width: 72px; height: 72px; margin: 0 auto 10px; border-radius: 50%; overflow: hidden; border: 3px solid #a855f7; box-shadow: 0 0 16px rgba(168, 85, 247, 0.4);">
            <?php if ($avatarUrl): ?>
              <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
              <div style="width:100%; height:100%; background: linear-gradient(135deg,#7c3aed,#9333ea); display:flex; align-items:center; justify-content:center; font-size:1.8rem; font-weight:800; color:#fff;">
                <?= $firstLetter ?>
              </div>
            <?php endif; ?>
          </div>
          <div style="font-size: 1.1rem; font-weight: 700; color: #f8fafc;"><?= htmlspecialchars($user['full_name'] ?: 'Player') ?></div>
          <div style="font-size: 0.8rem; color: #94a3b8;"><?= htmlspecialchars($user['email']) ?></div>
        </div>

        <form id="profile-form" enctype="multipart/form-data">
          <div class="form-group">
            <label class="form-label" for="profile-name">Display Name</label>
            <input type="text" id="profile-name" name="full_name" class="form-input" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" placeholder="Enter your name">
          </div>

          <div class="form-group">
            <label class="form-label">Upload New Avatar</label>
            <input type="file" name="avatar" accept="image/*" class="form-input" style="padding: 8px;">
          </div>

          <button type="submit" class="btn-primary-action" style="margin-top: 8px;">Save Changes</button>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL: Live Support AI Chat -->
  <div class="modal-overlay" id="live-support-modal">
    <div class="modal-box" style="max-width: 440px; height: 560px; display: flex; flex-direction: column; padding: 0; overflow: hidden;">
      <!-- Header -->
      <div class="modal-header" style="padding: 12px 16px; background: #140e30; border-bottom: 1px solid rgba(139, 92, 246, 0.25);">
        <div style="display: flex; align-items: center; gap: 10px;">
          <div style="position: relative;">
            <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #7c3aed, #9333ea); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; box-shadow: 0 0 10px rgba(168, 85, 247, 0.4);">
              🎧
            </div>
            <span style="position: absolute; bottom: 0; right: 0; width: 10px; height: 10px; border-radius: 50%; background: #10b981; border: 2px solid #0d0920;"></span>
          </div>
          <div>
            <h3 class="modal-title" style="font-size: 0.95rem; margin: 0; display: flex; align-items: center; gap: 6px;">
              Bombaclat Live Support <span style="font-size: 0.65rem; padding: 2px 6px; background: rgba(16, 185, 129, 0.2); color: #34d399; border-radius: 4px; border: 1px solid rgba(16, 185, 129, 0.3);">24/7 ONLINE</span>
            </h3>
            <div style="font-size: 0.68rem; color: #94a3b8;">Instant AI & Human Executive Assistance</div>
          </div>
        </div>
        <button class="modal-close-btn" aria-label="Close">&times;</button>
      </div>

      <!-- User Banner -->
      <div style="padding: 8px 16px; background: #0c0822; border-bottom: 1px solid rgba(139, 92, 246, 0.15); display: flex; justify-content: space-between; align-items: center; font-size: 0.72rem; color: #94a3b8;">
        <div>👤 <strong><?= htmlspecialchars($user['full_name'] ?: 'Player') ?></strong></div>
        <div>Wallet: <strong style="color: #34d399;">₹<?= number_format($user['balance'], 2) ?></strong></div>
      </div>

      <!-- Quick Suggested Chips -->
      <div style="padding: 8px 12px; background: rgba(20, 14, 48, 0.6); display: flex; gap: 6px; overflow-x: auto; scrollbar-width: none; border-bottom: 1px solid rgba(139, 92, 246, 0.1);">
        <button type="button" class="support-quick-chip" data-text="Bhai mera deposit kab tak aayega?">💸 Deposit Status</button>
        <button type="button" class="support-quick-chip" data-text="Withdrawal kitne time me milta hai?">📤 Withdrawal Speed</button>
        <button type="button" class="support-quick-chip" data-text="Kya ye game real aur safe hai?">🛡️ Is it Fair?</button>
        <button type="button" class="support-quick-chip" data-text="I want to talk to a human support executive.">👤 Human Agent</button>
      </div>

      <!-- Messages Stream -->
      <div id="support-chat-messages" style="flex: 1; padding: 14px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
        <!-- Dynamic Chat Messages -->
      </div>

      <!-- Typing Indicator -->
      <div id="support-typing-indicator" style="display: none; padding: 4px 16px 8px; font-size: 0.72rem; color: #c084fc;">
        <span class="typing-dots">Support agent is typing<span>.</span><span>.</span><span>.</span></span>
      </div>

      <!-- Input Bar -->
      <form id="support-chat-form" style="padding: 10px 14px; background: #140e30; border-top: 1px solid rgba(139, 92, 246, 0.25); display: flex; gap: 8px;">
        <input type="text" id="support-chat-input" class="form-input" placeholder="Type your message in English or Hinglish..." autocomplete="off" required style="padding: 10px 14px; border-radius: 12px; font-size: 0.85rem;">
        <button type="submit" id="btn-support-send" class="btn-primary-action" style="width: auto; padding: 0 16px; border-radius: 12px; background: linear-gradient(135deg, #7c3aed, #9333ea);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
          </svg>
        </button>
      </form>
    </div>
  </div>

  <!-- Scripts -->
  <script src="assets/lib/confetti.min.js"></script>
  <script src="assets/js/game.js"></script>
</body>
</html>
