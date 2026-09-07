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
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="theme-color" content="#070513">
  <link rel="icon" type="image/png" href="assets/images/favicon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Performance: Preload Critical Game Assets (WebP) -->
  <link rel="preload" as="image" href="assets/images/skull_bomb.webp" type="image/webp">
  <link rel="preload" as="image" href="assets/images/gift_box.webp" type="image/webp">
  <link rel="prefetch" href="cheakenselim/game.html">
  <link rel="stylesheet" href="assets/css/app.css?v=<?= filemtime(__DIR__ . '/assets/css/app.css') ?>">
</head>
<body>
  <!-- Psychophysiological Luminance Bloom Overlay (25L-50L Sudden Flash) -->
  <div id="screen-luminance-flash" class="screen-luminance-flash"></div>
  
  <!-- Live Big-Win Floating Notification Toasts -->
  <div id="live-toast-container" class="live-toast-container"></div>

  <!-- Top Navigation Header -->
  <!-- Top Navigation Header -->
  <header class="app-header-bar">
    <div class="app-header">
      <!-- Left: Brand -->
      <div class="header-left-group">
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

      <!-- Right: Wallet Capsule + Gift Icon + Hamburger Menu -->
      <div class="header-right">
        <!-- Casino Lobby Switcher Button -->
        <a href="index.php" class="header-chicken-switch-btn" title="Back to Casino Lobby" style="background: rgba(147, 51, 234, 0.2); border-color: rgba(168, 85, 247, 0.4); color: #c084fc;">
          <span style="font-size: 1.1rem; line-height: 1;">🏠</span>
          <span class="chicken-switch-label">Lobby</span>
        </a>

        <!-- Chicken Road Switcher Button -->
        <a href="cheakenselim/game.html" class="header-chicken-switch-btn" title="Play Chicken Road">
          <span style="font-size: 1.15rem; line-height: 1;">🐔</span>
          <span class="chicken-switch-label">Chicken Road</span>
        </a>

        <!-- Wallet Capsule (Green Amount + Circular Plus) -->
        <button class="header-wallet-widget" id="header-wallet-badge" data-open-wallet="deposit" title="Open Wallet & Cashier" aria-label="Wallet">
          <span class="wallet-val" id="user-balance-display">₹<?= number_format($user['balance'], 2) ?></span>
          <span class="wallet-plus-circle">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
          </span>
        </button>

        <!-- Bonus Gift Icon Button -->
        <button class="gift-box-btn" data-open-wallet="deposit" aria-label="Gift Bonus" title="Free Bonus / Topup">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f472b6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 12 20 22 4 22 4 12"></polyline>
            <rect x="2" y="7" width="20" height="5"></rect>
            <line x1="12" y1="22" x2="12" y2="7"></line>
            <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
            <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
          </svg>
        </button>

        <?php if (isAdmin()): ?>
          <a href="admin.php" class="header-admin-crown-btn" title="Open Operator Studio (Admin Panel)">👑</a>
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

        <!-- Hamburger Menu on Far Right for Mobile -->
        <button class="menu-hamburger-btn" id="btn-mobile-menu" aria-label="Menu" data-open-modal="account-modal">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round">
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>
      </div>
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
        <picture>
          <source srcset="assets/images/skull_bomb.webp" type="image/webp">
          <img src="assets/images/skull_bomb.png" alt="Skull Bomb" class="promo-bomb-img" loading="lazy" decoding="async">
        </picture>
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

        <!-- Mines Grid Canvas Stage -->
        <div class="mines-grid-stage" id="grid-stage">
          <div class="mines-grid-canvas" id="mines-grid">
            <!-- Dynamic Grid Buttons -->
          </div>
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
          <div class="cashout-arrow-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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
            <span class="auto-gear-icon" id="auto-cashout-gear">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
              </svg>
            </span>
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
          <svg width="18" height="18" viewBox="0 0 24 24" fill="#ffffff"><polygon points="5 3 19 12 5 21 5 3"/></svg>
          <span>START MINING</span>
        </button>

        <!-- Fast Replay Acceleration Bar -->
        <div class="quick-rebet-bar" id="quick-rebet-bar">
          <button class="btn-fast-rebet" id="btn-quick-rebet">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00f0ff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.87"/></svg>
            <span>REPLAY <span class="rebet-val-cyan" id="replay-amount-label">₹100.00</span></span>
          </button>
          <button class="btn-fast-rebet-double" id="btn-double-rebet">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="#f59e0b"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            <span>2X REPLAY <span class="rebet-val-gold" id="double-amount-label">₹200.00</span></span>
          </button>
        </div>

        <!-- Dare to Win Big Promo Card (Mobile) -->
        <div class="promo-dare-card mobile-promo-card">
          <div class="promo-text-side">
            <div class="promo-title">DARE TO WIN BIG?</div>
            <div class="promo-subtext">Higher risk. Higher reward.</div>
            <button class="btn-try-bombs" id="btn-try-5-bombs-mobile">TRY 10 BOMBS</button>
          </div>
          <picture>
            <source srcset="assets/images/skull_bomb.webp" type="image/webp">
            <img src="assets/images/skull_bomb.png" alt="Skull Bomb" class="promo-bomb-img" loading="lazy" decoding="async">
          </picture>
        </div>

        <!-- Trust Row -->
        <div class="footer-trust-row" style="margin-top: 8px; margin-bottom: 4px;">
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
          <picture>
            <source srcset="assets/images/gift_box.webp" type="image/webp">
            <img src="assets/images/gift_box.png" alt="Gift Box" class="challenge-gift-img" loading="lazy" decoding="async">
          </picture>
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

  <!-- Mobile Bottom Sticky Navigation Dock (Reference Mockup Style with Center Bolt) -->
  <nav class="mobile-bottom-dock">
    <button class="dock-nav-item active" id="dock-btn-game" onclick="window.scrollTo({top:0, behavior:'smooth'});">
      <!-- 3D Cube Icon -->
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
        <line x1="12" y1="22.08" x2="12" y2="12"></line>
      </svg>
      <span>Game</span>
    </button>
    <button class="dock-nav-item" id="dock-btn-mybets" data-open-modal="account-modal">
      <!-- Checklist / Note Icon -->
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="16" rx="3"></rect>
        <line x1="8" y1="8" x2="16" y2="8"></line>
        <line x1="8" y1="12" x2="16" y2="12"></line>
        <line x1="8" y1="16" x2="12" y2="16"></line>
      </svg>
      <span>My Bets</span>
    </button>

    <!-- Center Elevated Floating Lightning Action Button -->
    <button class="dock-center-action-btn" id="dock-center-action-btn" onclick="window.scrollTo({top:0, behavior:'smooth'});" aria-label="Start Mining">
      <div class="dock-bolt-circle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="#ffffff">
          <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
        </svg>
      </div>
    </button>

    <button class="dock-nav-item" id="dock-btn-topwins" onclick="document.querySelector('.live-bets-full-card').scrollIntoView({behavior:'smooth'});">
      <!-- Trophy Icon -->
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
        <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
        <path d="M4 22h16"></path>
        <path d="M10 14.66V17c0 .55-.45 1-1 1H7.5c-.55 0-1 .45-1 1v1c0 .55.45 1 1 1h9c.55 0 1-.45 1-1v-1c0-.55-.45-1-1-1H15c-.55 0-1-.45-1-1v-2.34"></path>
        <path d="M18 9V4a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v5a6 6 0 0 0 12 0Z"></path>
      </svg>
      <span>Top Wins</span>
    </button>
    <button class="dock-nav-item" id="dock-btn-profile" data-open-modal="account-modal">
      <!-- Profile Avatar Icon -->
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
        <circle cx="12" cy="7" r="4"></circle>
      </svg>
      <span>Profile</span>
    </button>
  </nav>

  <!-- Floating Live Support Button -->
  <button class="floating-live-support-btn" data-open-modal="live-support-modal" id="btn-open-live-support">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
      <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"></path>
    </svg>
    <span>Live Support</span>
  </button>

  <!-- MODAL: Unified Wallet & Cashier Hub (Stake / Roobet Style) -->
  <div class="modal-overlay" id="wallet-modal">
    <div class="modal-box wallet-modal-box" style="max-width: 480px; width: 100%;">
      <div class="modal-header" style="padding-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 10px;">
          <span style="font-size: 1.4rem;">👛</span>
          <div>
            <h3 class="modal-title" style="margin: 0; font-size: 1.15rem; color: #f8fafc;">Wallet & Cashier</h3>
            <div style="font-size: 0.72rem; color: #94a3b8;">Instant UPI Payments & Fast Withdrawals</div>
          </div>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
          <div class="wallet-modal-bal-pill">
            <span class="bal-pill-label">Balance:</span>
            <span class="bal-pill-val" id="wallet-modal-balance-display">₹<?= number_format($user['balance'], 2) ?></span>
          </div>
          <button class="modal-close-btn" aria-label="Close">&times;</button>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="wallet-nav-tabs">
        <button type="button" class="wallet-tab-btn active" data-wallet-tab="deposit">
          <span class="tab-icon">➕</span>
          <span class="tab-title">Deposit</span>
        </button>
        <button type="button" class="wallet-tab-btn" data-wallet-tab="withdraw">
          <span class="tab-icon">📤</span>
          <span class="tab-title">Withdraw</span>
        </button>
        <button type="button" class="wallet-tab-btn" data-wallet-tab="deposit-history">
          <span class="tab-icon">📥</span>
          <span class="tab-title">Deposit History</span>
        </button>
        <button type="button" class="wallet-tab-btn" data-wallet-tab="withdraw-history">
          <span class="tab-icon">📑</span>
          <span class="tab-title">Withdraw History</span>
        </button>
      </div>

      <div class="modal-body" style="padding-top: 14px;">
        
        <!-- TAB PANE 1: DEPOSIT (ADD FUNDS) -->
        <div class="wallet-tab-pane active" id="wallet-pane-deposit">
          <div id="deposit-form-content">
            <div style="text-align: center; margin-bottom: 14px;">
              <div style="display: inline-block; background: #ffffff; padding: 10px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                <picture>
                  <source srcset="assets/images/qrpayment.webp" type="image/webp">
                  <img src="assets/images/qrpayment.jpeg" alt="UPI QR Code" style="width: 150px; height: 150px; object-fit: contain; border-radius: 10px; display: block;" onerror="this.src='qrpayment.jpeg'" loading="lazy" decoding="async">
                </picture>
              </div>
              <p style="font-size: 0.78rem; color: #94a3b8; margin-top: 8px;">Scan with Google Pay, PhonePe, Paytm, or any UPI app</p>
            </div>

            <form id="deposit-form" enctype="multipart/form-data">
              <div class="form-group">
                <label class="form-label" for="deposit-amount">Deposit Amount (₹) <span style="color: #ef4444;">*</span></label>
                <input type="number" id="deposit-amount" name="amount" class="form-input" min="10" step="any" placeholder="Enter deposit amount (Min ₹10)" required>
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
                <div style="border: 2px dashed rgba(168, 85, 247, 0.4); border-radius: 12px; padding: 12px; text-align: center; cursor: pointer; background: rgba(14, 10, 32, 0.6);" onclick="document.getElementById('deposit-screenshot').click();">
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
            <p style="font-size: 0.85rem; color: #cbd5e1; margin-bottom: 18px;">Your payment has been received and is pending verification. Your balance will be updated shortly.</p>
            <div style="display: flex; gap: 10px; justify-content: center;">
              <button class="btn-primary-action" type="button" id="btn-goto-deposit-history">View Deposit History</button>
              <button class="btn-primary-action" data-close-modal style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">Back to Game</button>
            </div>
          </div>
        </div>

        <!-- TAB PANE 2: WITHDRAW (CASH OUT) -->
        <div class="wallet-tab-pane" id="wallet-pane-withdraw">
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
            <div style="display: flex; gap: 10px; justify-content: center;">
              <button class="btn-primary-action" type="button" id="btn-goto-withdraw-history">View Withdrawal History</button>
              <button class="btn-primary-action" data-close-modal style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">Back to Game</button>
            </div>
          </div>
        </div>

        <!-- TAB PANE 3: DEPOSIT HISTORY -->
        <div class="wallet-tab-pane" id="wallet-pane-deposit-history">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <span style="font-size: 0.82rem; font-weight: 800; color: #cbd5e1; text-transform: uppercase;">📥 Recent Deposits</span>
            <button type="button" id="btn-refresh-deposits" class="btn-tx-refresh">🔄 Refresh</button>
          </div>
          <div id="deposit-history-list" class="tx-history-scroll-container">
            <div style="text-align: center; color: #64748b; padding: 20px;">Loading deposits...</div>
          </div>
        </div>

        <!-- TAB PANE 4: WITHDRAWAL HISTORY -->
        <div class="wallet-tab-pane" id="wallet-pane-withdraw-history">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <span style="font-size: 0.82rem; font-weight: 800; color: #cbd5e1; text-transform: uppercase;">📤 Recent Withdrawals</span>
            <button type="button" id="btn-refresh-withdrawals" class="btn-tx-refresh">🔄 Refresh</button>
          </div>
          <div id="withdrawal-history-list" class="tx-history-scroll-container">
            <div style="text-align: center; color: #64748b; padding: 20px;">Loading withdrawals...</div>
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

        <?php if (isAdmin()): ?>
          <div style="margin-bottom: 16px; padding: 12px; background: rgba(168, 85, 247, 0.15); border: 1.5px solid #a855f7; border-radius: 14px; text-align: center;">
            <div style="font-size: 0.72rem; font-weight: 800; color: #a855f7; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px;">👑 Administrator Privileges</div>
            <a href="admin.php" style="padding: 10px 14px; border-radius: 10px; background: linear-gradient(135deg, #7c3aed, #9333ea); color: #fff; font-size: 0.85rem; font-weight: 800; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.4);">
              <span>⚙️ Open Operator Studio (Admin)</span>
            </a>
          </div>
        <?php endif; ?>

        <!-- Switch to Chicken Road Game -->
        <div style="margin-bottom: 16px; padding: 12px; background: rgba(16, 185, 129, 0.12); border: 1.5px solid rgba(52, 211, 153, 0.4); border-radius: 14px; text-align: center;">
          <div style="font-size: 0.72rem; font-weight: 800; color: #34d399; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px;">🐔 Other Games</div>
          <a href="cheakenselim/game.html" style="padding: 10px 14px; border-radius: 10px; background: linear-gradient(135deg, #10b981, #059669); color: #fff; font-size: 0.85rem; font-weight: 800; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);">
            <span>🐔 Play Chicken Road</span>
          </a>
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
  <script src="assets/js/game.js?v=<?= filemtime(__DIR__ . '/assets/js/game.js') ?>"></script>
</body>
</html>
