<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit;
}

$avatarUrl = !empty($user['avatar_url']) ? $user['avatar_url'] : null;
$firstLetter = strtoupper(substr($user['full_name'] ?: $user['email'], 0, 1));
$displayName = htmlspecialchars($user['full_name'] ?: explode('@', $user['email'])[0]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="theme-color" content="#070512">
  <title>BOMBACLAT CASINO - Real Games Real Cashouts</title>
  <link rel="icon" type="image/png" href="assets/images/favicon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
  <!-- Performance: Preload Critical Hero & Card Art (WebP) -->
  <link rel="preload" as="image" href="assets/images/hero_crown_bomb.webp" type="image/webp">
  <link rel="preload" as="image" href="assets/images/mines_card_art.webp" type="image/webp">
  <link rel="preload" as="image" href="assets/images/chicken_card_art.webp" type="image/webp">
  <!-- Performance: Prefetch Games for Instant Play Transition -->
  <link rel="prefetch" href="cheakenselim/game.html">
  <link rel="prefetch" href="mines.php">
  <link rel="stylesheet" href="assets/css/app.css?v=<?= filemtime(__DIR__ . '/assets/css/app.css') ?>">
  <style>
    :root {
      --bg-midnight: #070512;
      --bg-panel: #0d0920;
      --bg-card: rgba(18, 13, 38, 0.95);
      --bg-surface: #140f30;
      --border-subtle: rgba(139, 92, 246, 0.2);
      --border-glow: rgba(168, 85, 247, 0.4);
      --accent-purple: #9333ea;
      --accent-purple-bright: #a855f7;
      --accent-purple-light: #c084fc;
      --accent-green: #10b981;
      --accent-green-bright: #34d399;
      --accent-gold: #f59e0b;
      --accent-cyan: #00f0ff;
      --font-outfit: 'Outfit', -apple-system, sans-serif;
      --font-inter: 'Inter', sans-serif;
      --font-mono: 'JetBrains Mono', monospace;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: var(--font-outfit);
      background-color: var(--bg-midnight);
      background-image: 
        radial-gradient(circle at 50% 0%, rgba(147, 51, 234, 0.18) 0%, transparent 60%),
        radial-gradient(circle at 10% 40%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.08) 0%, transparent 50%);
      color: #f8fafc;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
    }

    /* Top Navigation Header */
    .top-navbar {
      background: rgba(10, 7, 22, 0.96);
      border-bottom: 1px solid var(--border-subtle);
      padding: 10px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
      backdrop-filter: blur(14px);
    }

    .brand-section {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
    }
    .brand-icon-box {
      width: 32px;
      height: 32px;
      background: linear-gradient(135deg, #7c3aed, #a855f7);
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 0 14px rgba(168, 85, 247, 0.5);
    }
    .brand-text-col {
      display: flex;
      flex-direction: column;
    }
    .brand-main-title {
      font-size: 1.15rem;
      font-weight: 900;
      letter-spacing: 0.5px;
      color: #fff;
      line-height: 1.1;
    }
    .brand-main-title span {
      color: var(--accent-green-bright);
    }
    .brand-sub-title {
      font-size: 0.62rem;
      font-weight: 700;
      letter-spacing: 1px;
      color: #94a3b8;
      text-transform: uppercase;
    }

    /* Center Navigation Pills */
    .center-nav-pills {
      display: flex;
      align-items: center;
      gap: 6px;
      background: rgba(18, 13, 38, 0.8);
      border: 1px solid var(--border-subtle);
      padding: 4px 6px;
      border-radius: 14px;
    }
    .nav-pill-btn {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 7px 14px;
      border-radius: 10px;
      font-size: 0.8rem;
      font-weight: 700;
      color: #94a3b8;
      text-decoration: none;
      background: transparent;
      border: none;
      cursor: pointer;
      transition: all 0.2s;
    }
    .nav-pill-btn:hover {
      color: #fff;
      background: rgba(168, 85, 247, 0.15);
    }
    .nav-pill-btn.active {
      background: linear-gradient(135deg, #7c3aed, #9333ea);
      color: #fff;
      box-shadow: 0 2px 10px rgba(124, 58, 237, 0.4);
    }

    /* Right Controls */
    .header-right-group {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .wallet-capsule {
      display: flex;
      align-items: center;
      background: rgba(16, 185, 129, 0.12);
      border: 1px solid rgba(16, 185, 129, 0.35);
      border-radius: 12px;
      padding: 3px 4px 3px 12px;
      gap: 8px;
      cursor: pointer;
      transition: all 0.2s;
    }
    .wallet-capsule:hover {
      border-color: var(--accent-green-bright);
      box-shadow: 0 0 14px rgba(16, 185, 129, 0.3);
    }
    .wallet-amount-text {
      font-size: 0.88rem;
      font-weight: 800;
      color: var(--accent-green-bright);
      font-family: var(--font-mono);
    }
    .wallet-plus-square {
      width: 26px;
      height: 26px;
      background: var(--accent-green);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #000;
      font-weight: 900;
      font-size: 1.05rem;
      transition: transform 0.15s;
    }
    .wallet-capsule:hover .wallet-plus-square {
      transform: scale(1.05);
    }

    .gift-nav-btn {
      width: 34px;
      height: 34px;
      background: rgba(168, 85, 247, 0.15);
      border: 1px solid rgba(168, 85, 247, 0.35);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: #f472b6;
      font-size: 1.05rem;
      transition: all 0.2s;
    }
    .gift-nav-btn:hover {
      background: rgba(168, 85, 247, 0.3);
      transform: translateY(-2px);
    }

    .admin-crown-btn {
      width: 34px;
      height: 34px;
      background: rgba(245, 158, 11, 0.15);
      border: 1px solid rgba(245, 158, 11, 0.35);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      font-size: 1.1rem;
      color: #fbbf24;
      transition: all 0.2s;
    }
    .admin-crown-btn:hover {
      background: rgba(245, 158, 11, 0.3);
      transform: scale(1.08);
    }

    .profile-pill {
      display: flex;
      align-items: center;
      gap: 8px;
      background: rgba(18, 13, 38, 0.85);
      border: 1px solid var(--border-subtle);
      border-radius: 12px;
      padding: 4px 10px 4px 5px;
      cursor: pointer;
      transition: all 0.2s;
    }
    .profile-pill:hover {
      border-color: var(--accent-purple-bright);
    }
    .profile-avatar-circle {
      width: 26px;
      height: 26px;
      background: linear-gradient(135deg, #a855f7, #7c3aed);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 0.78rem;
      color: #fff;
    }
    .profile-name-text {
      font-size: 0.8rem;
      font-weight: 700;
      color: #f1f5f9;
    }
    .profile-chevron {
      color: #94a3b8;
      font-size: 0.65rem;
    }

    /* 2-Column Casino Layout */
    .casino-main-layout {
      max-width: 1440px;
      width: 100%;
      margin: 20px auto;
      padding: 0 20px;
      display: grid;
      grid-template-columns: 240px 1fr;
      gap: 20px;
      align-items: start;
      flex: 1;
    }

    /* Left Sidebar */
    .sidebar-col {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .sidebar-menu-card {
      background: var(--bg-card);
      border: 1px solid var(--border-subtle);
      border-radius: 18px;
      padding: 10px;
      display: flex;
      flex-direction: column;
      gap: 4px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
    }

    .sidebar-link {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 14px;
      border-radius: 12px;
      font-size: 0.84rem;
      font-weight: 700;
      color: #94a3b8;
      text-decoration: none;
      transition: all 0.18s;
      cursor: pointer;
    }
    .sidebar-link-left {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .sidebar-link:hover {
      color: #fff;
      background: rgba(168, 85, 247, 0.12);
    }
    .sidebar-link.active {
      background: rgba(124, 58, 237, 0.25);
      color: #fff;
      border-left: 3px solid #a855f7;
    }
    .notif-pink-dot {
      width: 7px;
      height: 7px;
      background: #f43f5e;
      border-radius: 50%;
      box-shadow: 0 0 8px #f43f5e;
    }

    /* Weekly Race Widget */
    .weekly-race-card {
      background: linear-gradient(135deg, rgba(26, 16, 58, 0.95) 0%, rgba(15, 10, 36, 0.95) 100%);
      border: 1.5px solid rgba(245, 158, 11, 0.35);
      border-radius: 18px;
      padding: 16px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    }
    .weekly-race-card::after {
      content: '';
      position: absolute;
      top: -30px;
      right: -30px;
      width: 90px;
      height: 90px;
      background: radial-gradient(circle, rgba(245, 158, 11, 0.25) 0%, transparent 70%);
      pointer-events: none;
    }
    .race-title-label {
      font-size: 0.65rem;
      font-weight: 800;
      color: #fbbf24;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-bottom: 4px;
    }
    .race-amount-heading {
      font-size: 1.35rem;
      font-weight: 900;
      color: #34d399;
      line-height: 1.1;
      margin-bottom: 2px;
    }
    .race-sub-text {
      font-size: 0.68rem;
      font-weight: 700;
      color: #cbd5e1;
      margin-bottom: 12px;
    }
    .race-trophy-img {
      position: absolute;
      right: 12px;
      top: 14px;
      font-size: 2.2rem;
      filter: drop-shadow(0 4px 10px rgba(245, 158, 11, 0.5));
    }
    .btn-race-join {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      width: 100%;
      padding: 8px 14px;
      border-radius: 10px;
      background: linear-gradient(135deg, #7c3aed, #9333ea);
      color: #fff;
      font-size: 0.78rem;
      font-weight: 800;
      text-decoration: none;
      border: none;
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-race-join:hover {
      box-shadow: 0 4px 14px rgba(124, 58, 237, 0.5);
      transform: translateY(-1px);
    }

    /* Trust Badges Box */
    .trust-badges-card {
      background: var(--bg-card);
      border: 1px solid var(--border-subtle);
      border-radius: 18px;
      padding: 14px 16px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .trust-badge-row {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .trust-badge-icon {
      font-size: 1.1rem;
      width: 24px;
      text-align: center;
    }
    .trust-badge-title {
      font-size: 0.74rem;
      font-weight: 800;
      color: #f1f5f9;
    }
    .trust-badge-sub {
      font-size: 0.65rem;
      color: #64748b;
    }

    /* Right Main Column */
    .main-content-col {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    /* Hero Banner */
    .hero-banner-card {
      background: linear-gradient(135deg, rgba(22, 14, 48, 0.96) 0%, rgba(13, 8, 30, 0.98) 100%);
      border: 1.5px solid var(--border-glow);
      border-radius: 24px;
      padding: 28px 32px;
      display: grid;
      grid-template-columns: 1fr 340px 180px;
      gap: 20px;
      align-items: center;
      position: relative;
      overflow: hidden;
      box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6), inset 0 0 40px rgba(168, 85, 247, 0.12);
    }
    .hero-banner-card::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at 60% 50%, rgba(168, 85, 247, 0.2) 0%, transparent 60%);
      pointer-events: none;
    }

    .hero-tag-welcome {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.7rem;
      font-weight: 900;
      letter-spacing: 1px;
      color: #c084fc;
      text-transform: uppercase;
      margin-bottom: 10px;
    }
    .hero-main-h1 {
      font-size: 2.2rem;
      font-weight: 900;
      color: #fff;
      line-height: 1.1;
      letter-spacing: -0.5px;
      margin-bottom: 8px;
    }
    .hero-main-h1 .highlight-cashouts {
      background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      display: block;
    }
    .hero-tagline {
      font-size: 0.84rem;
      color: #94a3b8;
      line-height: 1.45;
      margin-bottom: 18px;
      max-width: 400px;
    }

    .hero-btn-group {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 18px;
    }
    .btn-hero-primary {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 11px 22px;
      border-radius: 12px;
      background: linear-gradient(135deg, #7c3aed, #9333ea);
      color: #fff;
      font-size: 0.84rem;
      font-weight: 800;
      text-decoration: none;
      box-shadow: 0 4px 16px rgba(124, 58, 237, 0.45);
      transition: all 0.2s;
    }
    .btn-hero-primary:hover {
      box-shadow: 0 6px 22px rgba(147, 51, 234, 0.6);
      transform: translateY(-2px);
    }
    .btn-hero-secondary {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 11px 18px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--border-subtle);
      color: #cbd5e1;
      font-size: 0.84rem;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.2s;
    }
    .btn-hero-secondary:hover {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
    }

    .social-proof-stack {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .avatar-stack-group {
      display: flex;
      align-items: center;
    }
    .stack-avatar {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      border: 2px solid #0f0a28;
      margin-left: -6px;
      background: #475569;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.6rem;
      font-weight: 800;
      color: #fff;
    }
    .stack-avatar:first-child { margin-left: 0; }
    .social-proof-text {
      font-size: 0.72rem;
      color: #cbd5e1;
      font-weight: 600;
    }
    .social-proof-text strong {
      color: #fff;
    }

    /* Hero Center Graphic */
    .hero-graphic-box {
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }
    .hero-art-img {
      width: 100%;
      max-width: 320px;
      height: 185px;
      object-fit: cover;
      border-radius: 16px;
      border: 1px solid var(--border-glow);
      box-shadow: 0 10px 30px rgba(124, 58, 237, 0.35);
    }

    /* Hero Right Feature Bullets */
    .hero-features-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .hero-feature-item {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .hf-icon-circle {
      width: 30px;
      height: 30px;
      border-radius: 8px;
      background: rgba(168, 85, 247, 0.15);
      border: 1px solid rgba(168, 85, 247, 0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.85rem;
      color: var(--accent-purple-bright);
      flex-shrink: 0;
    }
    .hf-text-group {
      display: flex;
      flex-direction: column;
    }
    .hf-title {
      font-size: 0.68rem;
      font-weight: 800;
      color: #fff;
      letter-spacing: 0.4px;
      text-transform: uppercase;
    }

    /* Quick Stats 4-Card Bar */
    .quick-stats-row {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 14px;
    }
    .stat-metric-card {
      background: var(--bg-card);
      border: 1px solid var(--border-subtle);
      border-radius: 16px;
      padding: 14px 18px;
      display: flex;
      align-items: center;
      gap: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    .stat-metric-icon {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: rgba(168, 85, 247, 0.12);
      border: 1px solid rgba(168, 85, 247, 0.25);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.15rem;
      flex-shrink: 0;
    }
    .stat-metric-val {
      font-size: 1.15rem;
      font-weight: 900;
      color: #fff;
      line-height: 1.1;
    }
    .stat-metric-label {
      font-size: 0.65rem;
      font-weight: 800;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-top: 2px;
    }

    /* Featured Games Arena Section */
    .games-section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 14px;
    }
    .games-header-left {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }
    .games-section-h2 {
      font-size: 1.25rem;
      font-weight: 900;
      color: #fff;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .games-section-sub {
      font-size: 0.78rem;
      color: #94a3b8;
    }
    .btn-view-all-games {
      background: rgba(18, 13, 38, 0.9);
      border: 1px solid var(--border-subtle);
      color: #cbd5e1;
      padding: 6px 14px;
      border-radius: 10px;
      font-size: 0.78rem;
      font-weight: 800;
      text-decoration: none;
      transition: all 0.2s;
    }
    .btn-view-all-games:hover {
      border-color: var(--accent-purple-bright);
      color: #fff;
    }

    /* 2 Large Featured Game Cards */
    .featured-games-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
    }

    .game-showcase-card {
      background: var(--bg-card);
      border: 1.5px solid var(--border-subtle);
      border-radius: 22px;
      padding: 20px;
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 280px;
      box-shadow: 0 12px 36px rgba(0,0,0,0.45);
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .game-showcase-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 18px 45px rgba(147, 51, 234, 0.3);
      border-color: var(--accent-purple-bright);
    }
    .game-showcase-card.chicken-variant:hover {
      box-shadow: 0 18px 45px rgba(16, 185, 129, 0.3);
      border-color: var(--accent-green);
    }

    .game-bg-art {
      position: absolute;
      top: 0;
      right: 0;
      width: 60%;
      height: 100%;
      object-fit: cover;
      opacity: 0.55;
      mask-image: linear-gradient(to left, rgba(0,0,0,1) 30%, rgba(0,0,0,0) 100%);
      -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,1) 30%, rgba(0,0,0,0) 100%);
      pointer-events: none;
    }

    .card-top-row {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      position: relative;
      z-index: 2;
    }
    .game-badge-pill {
      font-size: 0.65rem;
      font-weight: 900;
      padding: 4px 10px;
      border-radius: 8px;
      letter-spacing: 0.6px;
      text-transform: uppercase;
    }
    .badge-most-played {
      background: rgba(147, 51, 234, 0.3);
      border: 1px solid var(--accent-purple-light);
      color: #fff;
    }
    .badge-trending {
      background: rgba(16, 185, 129, 0.3);
      border: 1px solid var(--accent-green-bright);
      color: #fff;
    }

    .game-info-block {
      position: relative;
      z-index: 2;
      max-width: 320px;
      margin-top: 10px;
    }
    .game-title-text {
      font-size: 1.35rem;
      font-weight: 900;
      color: #fff;
      letter-spacing: -0.3px;
      margin-bottom: 4px;
    }
    .game-desc-text {
      font-size: 0.8rem;
      color: #94a3b8;
      line-height: 1.4;
      margin-bottom: 14px;
    }

    .game-tags-row {
      display: flex;
      gap: 6px;
      flex-wrap: wrap;
      margin-bottom: 20px;
      position: relative;
      z-index: 2;
    }
    .gtag-pill {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      background: rgba(10, 7, 24, 0.85);
      border: 1px solid var(--border-subtle);
      border-radius: 8px;
      padding: 4px 8px;
      font-size: 0.68rem;
      font-weight: 700;
      color: #cbd5e1;
    }

    .card-footer-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      position: relative;
      z-index: 2;
    }
    .btn-game-launch {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      border-radius: 12px;
      font-size: 0.84rem;
      font-weight: 900;
      text-decoration: none;
      color: #fff;
      transition: all 0.2s;
    }
    .btn-launch-mines {
      background: linear-gradient(135deg, #7c3aed, #9333ea);
      box-shadow: 0 4px 16px rgba(124, 58, 237, 0.4);
    }
    .btn-launch-mines:hover {
      box-shadow: 0 6px 22px rgba(147, 51, 234, 0.6);
      transform: translateY(-2px);
    }
    .btn-launch-chicken {
      background: linear-gradient(135deg, #059669, #10b981);
      box-shadow: 0 4px 16px rgba(16, 185, 129, 0.4);
    }
    .btn-launch-chicken:hover {
      box-shadow: 0 6px 22px rgba(16, 185, 129, 0.6);
      transform: translateY(-2px);
    }

    .player-counter-box {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.72rem;
      color: #94a3b8;
      font-weight: 700;
    }
    .player-counter-box strong { color: #fff; }

    /* Live Winners Ticker Stream */
    .live-winners-section {
      margin-top: 10px;
    }
    .live-winners-header {
      font-size: 1rem;
      font-weight: 900;
      color: #fff;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 12px;
    }
    .winners-ticker-wrap {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 12px;
      overflow-x: auto;
    }
    .winner-capsule-card {
      background: var(--bg-card);
      border: 1px solid var(--border-subtle);
      border-radius: 14px;
      padding: 10px 14px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.3);
    }
    .winner-user-col {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .winner-avatar-circle {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      background: #334155;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.65rem;
      font-weight: 800;
      color: #fff;
    }
    .winner-user-name {
      font-size: 0.75rem;
      font-weight: 800;
      color: #fff;
    }
    .winner-won-sub {
      font-size: 0.65rem;
      color: #64748b;
    }
    .winner-payout-badge {
      font-size: 0.78rem;
      font-weight: 900;
      color: #34d399;
      font-family: var(--font-mono);
      text-align: right;
    }
    .winner-game-tag {
      font-size: 0.65rem;
      color: #94a3b8;
    }

    /* Mobile Bottom Sticky Navigation Dock */
    .mobile-bottom-dock {
      display: none;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(10, 7, 24, 0.96);
      border-top: 1px solid var(--border-subtle);
      padding: 6px 12px 10px;
      z-index: 100;
      backdrop-filter: blur(16px);
      justify-content: space-around;
      align-items: center;
    }
    .dock-tab-btn {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 3px;
      color: #94a3b8;
      text-decoration: none;
      font-size: 0.65rem;
      font-weight: 800;
      background: none;
      border: none;
      cursor: pointer;
      padding: 4px 8px;
      border-radius: 8px;
      transition: all 0.2s;
    }
    .dock-tab-btn span.dock-icon {
      font-size: 1.15rem;
    }
    .dock-tab-btn.active, .dock-tab-btn:hover {
      color: #fff;
    }
    .dock-tab-btn.active span.dock-icon {
      filter: drop-shadow(0 0 8px #a855f7);
    }
    .dock-tab-btn.deposit-highlight {
      color: var(--accent-green-bright);
    }
    .dock-tab-btn.deposit-highlight span.dock-icon {
      filter: drop-shadow(0 0 8px #10b981);
    }

    /* Mobile Responsive Rules (Phones & Tablets) */
    @media (max-width: 1100px) {
      .hero-banner-card {
        grid-template-columns: 1fr 280px;
      }
      .hero-features-list { display: none; }
    }

    @media (max-width: 900px) {
      body {
        padding-bottom: 74px; /* Space for mobile bottom bar */
      }
      .mobile-bottom-dock {
        display: flex;
      }
      .top-navbar {
        padding: 8px 14px;
      }
      .brand-sub-title {
        display: none;
      }
      .center-nav-pills {
        display: none;
      }
      .casino-main-layout {
        grid-template-columns: 1fr;
        padding: 0 12px;
        margin: 12px auto;
        gap: 14px;
      }
      .sidebar-col {
        display: none;
      }
      
      /* CRITICAL: On Mobile, Featured Games MUST be at the very TOP! */
      .main-content-col {
        display: flex;
        flex-direction: column;
        gap: 16px;
      }
      #featured-games {
        order: 1 !important;
        margin-top: 0 !important;
      }
      .hero-banner-card {
        order: 2 !important;
        grid-template-columns: 1fr !important;
        padding: 18px 16px !important;
        border-radius: 18px !important;
        gap: 14px !important;
      }
      .hero-graphic-box {
        display: flex !important; /* Make sure image is shown on mobile! */
        justify-content: center !important;
        margin-top: 6px !important;
      }
      .hero-art-img {
        max-width: 260px !important;
        height: 140px !important;
      }
      .hero-main-h1 {
        font-size: 1.6rem !important;
      }
      .hero-tagline {
        font-size: 0.78rem !important;
        margin-bottom: 14px !important;
      }
      .quick-stats-row {
        order: 3 !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 10px !important;
      }
      .stat-metric-card {
        padding: 10px 12px !important;
      }
      .stat-metric-val {
        font-size: 1rem !important;
      }
      .featured-games-grid {
        grid-template-columns: 1fr !important;
        gap: 14px !important;
      }
      .game-showcase-card {
        min-height: 240px !important;
        padding: 16px !important;
        border-radius: 18px !important;
      }
      .game-bg-art {
        width: 75% !important;
        opacity: 0.45 !important;
      }
      .game-title-text {
        font-size: 1.2rem !important;
      }
      .card-footer-row {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 8px !important;
      }
      .btn-game-launch {
        justify-content: center !important;
        padding: 12px !important;
        font-size: 0.9rem !important;
      }
      .player-counter-box {
        justify-content: center !important;
      }
      .live-winners-section {
        order: 4 !important;
      }
      .winners-ticker-wrap {
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)) !important;
      }
    }

    @media (max-width: 480px) {
      .top-navbar {
        padding: 6px 10px;
      }
      .brand-main-title {
        font-size: 0.95rem;
      }
      .brand-icon-box {
        width: 26px;
        height: 26px;
      }
      .brand-icon-box svg {
        width: 16px;
        height: 16px;
      }
      .profile-name-text {
        display: none;
      }
      .profile-pill {
        padding: 2px 4px;
      }
      .wallet-capsule {
        padding: 2px 3px 2px 8px;
      }
      .wallet-amount-text {
        font-size: 0.78rem;
      }
      .wallet-plus-square {
        width: 22px;
        height: 22px;
        font-size: 0.95rem;
      }
    }
  </style>
</head>
<body>

  <!-- Top Navigation Bar -->
  <header class="top-navbar">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-section">
      <div class="brand-icon-box">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="#fff">
          <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
        </svg>
      </div>
      <div class="brand-text-col">
        <div class="brand-main-title">BOMBACLAT <span>CASINO</span></div>
        <div class="brand-sub-title">PLAY BOLD. CASH REAL.</div>
      </div>
    </a>

    <!-- Center Navigation Tabs (Desktop) -->
    <div class="center-nav-pills">
      <a href="index.php" class="nav-pill-btn active">🏠 Home</a>
      <a href="#featured-games" class="nav-pill-btn">🎮 Games</a>
      <button class="nav-pill-btn" data-open-modal="leaderboard-modal">🏆 Leaderboard</button>
      <button class="nav-pill-btn" data-open-modal="promotions-modal">🎁 Rewards</button>
      <button class="nav-pill-btn" data-open-modal="promotions-modal">👑 VIP</button>
    </div>

    <!-- Right Controls -->
    <div class="header-right-group">
      <!-- Wallet Capsule (₹ Amount + Plus) -->
      <div class="wallet-capsule" data-open-wallet="deposit" title="Open Deposit Cashier">
        <span class="wallet-amount-text" id="user-balance-display">₹<?= number_format($user['balance'], 2) ?></span>
        <div class="wallet-plus-square">+</div>
      </div>

      <!-- Bonus Gift Icon Button -->
      <button class="gift-nav-btn" data-open-wallet="deposit" title="Claim Bonus Rewards">
        🎁
      </button>

      <?php if (isAdmin()): ?>
        <a href="admin.php" class="admin-crown-btn" title="Operator Studio (Admin Panel)">👑</a>
      <?php endif; ?>

      <!-- Profile Capsule -->
      <div class="profile-pill" data-open-modal="account-modal" title="Account Settings">
        <div class="profile-avatar-circle">
          <?php if ($avatarUrl): ?>
            <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
          <?php else: ?>
            <?= $firstLetter ?>
          <?php endif; ?>
        </div>
        <span class="profile-name-text"><?= $displayName ?></span>
        <span class="profile-chevron">▼</span>
      </div>

      <a href="api/auth.php?action=logout" style="color:#f87171; text-decoration:none; font-size:0.75rem; font-weight:700; margin-left:4px;" title="Logout">Exit</a>
    </div>
  </header>

  <!-- 2-Column Casino Lobby Arena -->
  <div class="casino-main-layout">

    <!-- LEFT SIDEBAR (Desktop) -->
    <aside class="sidebar-col">
      <!-- Main Nav Menu -->
      <div class="sidebar-menu-card">
        <a href="index.php" class="sidebar-link active">
          <div class="sidebar-link-left">
            <span>🏠</span>
            <span>Home</span>
          </div>
        </a>
        <a href="#featured-games" class="sidebar-link">
          <div class="sidebar-link-left">
            <span>🎮</span>
            <span>All Games</span>
          </div>
        </a>
        <div class="sidebar-link" data-open-modal="promotions-modal">
          <div class="sidebar-link-left">
            <span>🎁</span>
            <span>Promotions</span>
          </div>
          <span class="notif-pink-dot"></span>
        </div>
        <div class="sidebar-link" data-open-modal="promotions-modal">
          <div class="sidebar-link-left">
            <span>👑</span>
            <span>VIP Club</span>
          </div>
        </div>
        <div class="sidebar-link" data-open-modal="leaderboard-modal">
          <div class="sidebar-link-left">
            <span>🏆</span>
            <span>Leaderboard</span>
          </div>
        </div>
        <div class="sidebar-link" data-open-wallet="history">
          <div class="sidebar-link-left">
            <span>📄</span>
            <span>Transactions</span>
          </div>
        </div>
        <div class="sidebar-link" data-open-modal="live-support-modal">
          <div class="sidebar-link-left">
            <span>🎧</span>
            <span>Support</span>
          </div>
        </div>
      </div>

      <!-- Weekly Race Card -->
      <div class="weekly-race-card">
        <div class="race-title-label">WEEKLY RACE</div>
        <div class="race-amount-heading">₹5,00,000</div>
        <div class="race-sub-text">IN PRIZES</div>
        <div class="race-trophy-img">🏆</div>
        <button class="btn-race-join" data-open-modal="promotions-modal">Join Now →</button>
      </div>

      <!-- Trust Badges -->
      <div class="trust-badges-card">
        <div class="trust-badge-row">
          <div class="trust-badge-icon">🇮🇳</div>
          <div>
            <div class="trust-badge-title">India's #1</div>
            <div class="trust-badge-sub">Provably Fair Casino</div>
          </div>
        </div>
        <div class="trust-badge-row">
          <div class="trust-badge-icon">🛡️</div>
          <div>
            <div class="trust-badge-title">100% Fair</div>
            <div class="trust-badge-sub">Provably Fair RNG</div>
          </div>
        </div>
        <div class="trust-badge-row">
          <div class="trust-badge-icon">⚡</div>
          <div>
            <div class="trust-badge-title">Instant Withdrawals</div>
            <div class="trust-badge-sub">UPI • Bank • Crypto</div>
          </div>
        </div>
        <div class="trust-badge-row">
          <div class="trust-badge-icon">🎧</div>
          <div>
            <div class="trust-badge-title">24/7 Live Support</div>
            <div class="trust-badge-sub">We got your back</div>
          </div>
        </div>
      </div>
    </aside>

    <!-- RIGHT MAIN COLUMN (Reordered for Mobile: Games Top -> Hero -> Stats -> Winners) -->
    <main class="main-content-col">

      <!-- 1. FEATURED GAMES ARENA (PRIORITY #1 ON MOBILE) -->
      <div id="featured-games">
        <div class="games-section-header">
          <div class="games-header-left">
            <div class="games-section-h2">🎮 Featured Casino Games</div>
            <div class="games-section-sub">Play, win and cashout instantly. Which game will you choose?</div>
          </div>
          <a href="#featured-games" class="btn-view-all-games">View All Games →</a>
        </div>

        <div class="featured-games-grid">
          <!-- CARD 1: BOMBACLAT MINES -->
          <div class="game-showcase-card">
            <picture>
              <source srcset="assets/images/mines_card_art.webp" type="image/webp">
              <img src="assets/images/mines_card_art.jpg" alt="Bombaclat Mines" class="game-bg-art" loading="lazy" decoding="async">
            </picture>
            
            <div class="card-top-row">
              <div></div>
              <span class="game-badge-pill badge-most-played">🔥 MOST PLAYED</span>
            </div>

            <div class="game-info-block">
              <div class="game-title-text">BOMBACLAT MINES</div>
              <div class="game-desc-text">Find the gems, avoid the bombs, multiply your winnings.</div>
            </div>

            <div class="game-tags-row">
              <span class="gtag-pill">💎 Customizable Bombs</span>
              <span class="gtag-pill">🎯 Auto-Pick</span>
              <span class="gtag-pill">🚀 Up to 10,000x</span>
            </div>

            <div class="card-footer-row">
              <a href="mines.php" class="btn-game-launch btn-launch-mines">Play Mines →</a>
              <div class="player-counter-box">
                <div class="avatar-stack-group">
                  <div class="stack-avatar" style="background:#7c3aed;">M</div>
                  <div class="stack-avatar" style="background:#3b82f6;">J</div>
                </div>
                <div><strong>12,430</strong> playing now</div>
              </div>
            </div>
          </div>

          <!-- CARD 2: CHICKEN CROSS ROAD -->
          <div class="game-showcase-card chicken-variant">
            <picture>
              <source srcset="assets/images/chicken_card_art.webp" type="image/webp">
              <img src="assets/images/chicken_card_art.jpg" alt="Chicken Cross Road" class="game-bg-art" loading="lazy" decoding="async">
            </picture>
            
            <div class="card-top-row">
              <div></div>
              <span class="game-badge-pill badge-trending">★ TRENDING</span>
            </div>

            <div class="game-info-block">
              <div class="game-title-text">CHICKEN CROSS ROAD</div>
              <div class="game-desc-text">Dodge traffic, survive longer, increase your multiplier.</div>
            </div>

            <div class="game-tags-row">
              <span class="gtag-pill">⚡ 4 Modes</span>
              <span class="gtag-pill">📈 Increasing Multiplier</span>
              <span class="gtag-pill">💰 Instant Cashout</span>
            </div>

            <div class="card-footer-row">
              <a href="cheakenselim/game.html" class="btn-game-launch btn-launch-chicken">Play Chicken Road →</a>
              <div class="player-counter-box">
                <div class="avatar-stack-group">
                  <div class="stack-avatar" style="background:#10b981;">C</div>
                  <div class="stack-avatar" style="background:#f59e0b;">S</div>
                </div>
                <div><strong>8,120</strong> playing now</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. HERO BANNER -->
      <div class="hero-banner-card">
        <!-- Left Content -->
        <div>
          <div class="hero-tag-welcome">★ WELCOME TO BOMBACLAT</div>
          <h1 class="hero-main-h1">
            REAL GAMES
            <span class="highlight-cashouts">REAL CASHOUTS</span>
          </h1>
          <p class="hero-tagline">
            Play India's most exciting provably fair games. Instant UPI payouts. No KYC hassles.
          </p>

          <div class="hero-btn-group">
            <a href="mines.php" class="btn-hero-primary">Play Now →</a>
            <a href="#featured-games" class="btn-hero-secondary">🎮 View Games</a>
          </div>

          <div class="social-proof-stack">
            <div class="avatar-stack-group">
              <div class="stack-avatar" style="background:#6366f1;">R</div>
              <div class="stack-avatar" style="background:#ec4899;">P</div>
              <div class="stack-avatar" style="background:#10b981;">A</div>
              <div class="stack-avatar" style="background:#f59e0b;">K</div>
            </div>
            <div class="social-proof-text"><strong>24,850+</strong> Players winning daily!</div>
          </div>
        </div>

        <!-- Center 3D Graphic (Rendered on both Desktop and Mobile) -->
        <div class="hero-graphic-box">
          <picture>
            <source srcset="assets/images/hero_crown_bomb.webp" type="image/webp">
            <img src="assets/images/hero_crown_bomb.jpg" alt="Bombaclat Crown Bomb" class="hero-art-img" fetchpriority="high" decoding="async">
          </picture>
        </div>

        <!-- Right Features List (Desktop only) -->
        <div class="hero-features-list">
          <div class="hero-feature-item">
            <div class="hf-icon-circle">⚡</div>
            <div class="hf-text-group">
              <div class="hf-title">INSTANT UPI</div>
              <div style="font-size:0.62rem; color:#94a3b8;">PAYOUTS</div>
            </div>
          </div>
          <div class="hero-feature-item">
            <div class="hf-icon-circle">🛡️</div>
            <div class="hf-text-group">
              <div class="hf-title">100% FAIR</div>
              <div style="font-size:0.62rem; color:#94a3b8;">&amp; TRANSPARENT</div>
            </div>
          </div>
          <div class="hero-feature-item">
            <div class="hf-icon-circle">💎</div>
            <div class="hf-text-group">
              <div class="hf-title">MULTIPLE GAMES</div>
              <div style="font-size:0.62rem; color:#94a3b8;">MORE WINS</div>
            </div>
          </div>
          <div class="hero-feature-item">
            <div class="hf-icon-circle">👑</div>
            <div class="hf-text-group">
              <div class="hf-title">PLAY ANYTIME</div>
              <div style="font-size:0.62rem; color:#94a3b8;">ANYWHERE</div>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. QUICK STATS 4-CARD ROW -->
      <div class="quick-stats-row">
        <div class="stat-metric-card">
          <div class="stat-metric-icon">👥</div>
          <div>
            <div class="stat-metric-val">24,850+</div>
            <div class="stat-metric-label">ROUNDS PLAYED TODAY</div>
          </div>
        </div>
        <div class="stat-metric-card">
          <div class="stat-metric-icon">🏆</div>
          <div>
            <div class="stat-metric-val" style="color: #34d399;">₹1,842,900</div>
            <div class="stat-metric-label">TOTAL PAYOUTS WON</div>
          </div>
        </div>
        <div class="stat-metric-card">
          <div class="stat-metric-icon">⚡</div>
          <div>
            <div class="stat-metric-val" style="color: #38bdf8;">&lt; 90 Sec</div>
            <div class="stat-metric-label">AVERAGE CASHOUT TIME</div>
          </div>
        </div>
        <div class="stat-metric-card">
          <div class="stat-metric-icon">🛡️</div>
          <div>
            <div class="stat-metric-val" style="color: #c084fc;">100% Fair</div>
            <div class="stat-metric-label">PROVABLY FAIR RNG</div>
          </div>
        </div>
      </div>

      <!-- 4. LIVE WINNERS TICKER -->
      <div class="live-winners-section">
        <div class="live-winners-header">
          <span>🏆</span>
          <span>Live Winners</span>
        </div>

        <div class="winners-ticker-wrap">
          <div class="winner-capsule-card">
            <div class="winner-user-col">
              <div class="winner-avatar-circle" style="background: #6366f1;">R</div>
              <div>
                <div class="winner-user-name">Rohit S.</div>
                <div class="winner-won-sub">Just won</div>
              </div>
            </div>
            <div>
              <div class="winner-payout-badge">₹12,480</div>
              <div class="winner-game-tag">💣 Mines</div>
            </div>
          </div>

          <div class="winner-capsule-card">
            <div class="winner-user-col">
              <div class="winner-avatar-circle" style="background: #ec4899;">P</div>
              <div>
                <div class="winner-user-name">Priya K.</div>
                <div class="winner-won-sub">Just won</div>
              </div>
            </div>
            <div>
              <div class="winner-payout-badge">₹8,250</div>
              <div class="winner-game-tag">🐔 Chicken Road</div>
            </div>
          </div>

          <div class="winner-capsule-card">
            <div class="winner-user-col">
              <div class="winner-avatar-circle" style="background: #10b981;">A</div>
              <div>
                <div class="winner-user-name">Aman T.</div>
                <div class="winner-won-sub">Just won</div>
              </div>
            </div>
            <div>
              <div class="winner-payout-badge">₹26,910</div>
              <div class="winner-game-tag">💣 Mines</div>
            </div>
          </div>

          <div class="winner-capsule-card">
            <div class="winner-user-col">
              <div class="winner-avatar-circle" style="background: #eab308;">N</div>
              <div>
                <div class="winner-user-name">Neha R.</div>
                <div class="winner-won-sub">Just won</div>
              </div>
            </div>
            <div>
              <div class="winner-payout-badge">₹5,600</div>
              <div class="winner-game-tag">🐔 Chicken Road</div>
            </div>
          </div>

          <div class="winner-capsule-card">
            <div class="winner-user-col">
              <div class="winner-avatar-circle" style="background: #8b5cf6;">K</div>
              <div>
                <div class="winner-user-name">Karan V.</div>
                <div class="winner-won-sub">Just won</div>
              </div>
            </div>
            <div>
              <div class="winner-payout-badge">₹18,200</div>
              <div class="winner-game-tag">💣 Mines</div>
            </div>
          </div>
        </div>
      </div>

    </main>
  </div>

  <!-- Mobile Bottom Sticky Navigation Dock -->
  <nav class="mobile-bottom-dock">
    <a href="index.php" class="dock-tab-btn active">
      <span class="dock-icon">🏠</span>
      <span>Home</span>
    </a>
    <a href="mines.php" class="dock-tab-btn">
      <span class="dock-icon">💣</span>
      <span>Mines</span>
    </a>
    <a href="cheakenselim/game.html" class="dock-tab-btn">
      <span class="dock-icon">🐔</span>
      <span>Chicken</span>
    </a>
    <button class="dock-tab-btn deposit-highlight" data-open-wallet="deposit">
      <span class="dock-icon">💳</span>
      <span>Deposit</span>
    </button>
    <button class="dock-tab-btn" data-open-modal="live-support-modal">
      <span class="dock-icon">🎧</span>
      <span>Support</span>
    </button>
  </nav>

  <!-- MODAL 1: UNIFIED CASHIER / WALLET MODAL -->
  <div class="modal-overlay" id="wallet-modal">
    <div class="modal-card">
      <div class="modal-header">
        <div class="modal-title-box">
          <span class="modal-icon">💳</span>
          <span class="modal-title">Cashier &amp; Wallet</span>
        </div>
        <button class="modal-close-btn" data-close-modal>&times;</button>
      </div>
      <div class="modal-body">
        <div class="wallet-balance-box">
          <div class="wb-label">AVAILABLE BALANCE</div>
          <div class="wb-amount" id="modal-wallet-balance">₹<?= number_format($user['balance'], 2) ?></div>
        </div>

        <div class="wallet-tabs-bar">
          <button class="wtab-btn active" data-wtab="deposit">Deposit</button>
          <button class="wtab-btn" data-wtab="withdraw">Withdraw</button>
          <button class="wtab-btn" data-wtab="deposit-history">Deposit History</button>
          <button class="wtab-btn" data-wtab="withdraw-history">Withdraw History</button>
        </div>

        <!-- TAB: DEPOSIT -->
        <div class="wtab-content active" id="wtab-deposit">
          <div class="qr-container-box">
            <div class="qr-instruction">Scan UPI QR to deposit instantly:</div>
            <picture>
              <source srcset="assets/images/qrpayment.webp" type="image/webp">
              <img src="assets/images/qrpayment.jpeg" alt="UPI QR Code" class="qr-image-display" id="lobby-deposit-qr" loading="lazy" decoding="async">
            </picture>
            <div class="upi-id-pill" id="deposit-upi-text">UPI: bomboclat@upi</div>
          </div>

          <form id="lobby-deposit-form">
            <div class="input-form-group">
              <label class="form-label">Deposit Amount (₹)</label>
              <input type="number" id="deposit-amount-input" class="app-input" placeholder="Min ₹100" min="100" max="50000" required>
            </div>

            <div class="amount-chips-row">
              <button type="button" class="amount-chip" data-amt="100">+₹100</button>
              <button type="button" class="amount-chip" data-amt="500">+₹500</button>
              <button type="button" class="amount-chip" data-amt="1000">+₹1,000</button>
              <button type="button" class="amount-chip" data-amt="2000">+₹2,000</button>
              <button type="button" class="amount-chip" data-amt="5000">+₹5,000</button>
            </div>

            <div class="input-form-group">
              <label class="form-label">12-Digit UTR / Transaction ID</label>
              <input type="text" id="deposit-utr-input" class="app-input" placeholder="e.g. 329485729104" required>
            </div>

            <div class="input-form-group">
              <label class="form-label">Mobile Number</label>
              <input type="tel" id="deposit-phone-input" class="app-input" placeholder="10-digit phone" value="<?= htmlspecialchars($user['mobile_number'] ?? '') ?>">
            </div>

            <button type="submit" class="btn-submit-action green">Submit Deposit (Instant Verification)</button>
          </form>
        </div>

        <!-- TAB: WITHDRAW -->
        <div class="wtab-content" id="wtab-withdraw" style="display:none;">
          <form id="lobby-withdraw-form">
            <div class="input-form-group">
              <label class="form-label">Withdrawal Amount (₹)</label>
              <input type="number" id="withdraw-amount-input" class="app-input" placeholder="Min ₹500" min="500" max="100000" required>
            </div>

            <div class="input-form-group">
              <label class="form-label">Your UPI ID / VPA</label>
              <input type="text" id="withdraw-upi-input" class="app-input" placeholder="e.g. name@okhdfcbank" required>
            </div>

            <div class="input-form-group">
              <label class="form-label">Confirm Mobile Number</label>
              <input type="tel" id="withdraw-phone-input" class="app-input" placeholder="10-digit phone" value="<?= htmlspecialchars($user['mobile_number'] ?? '') ?>" required>
            </div>

            <div style="background: rgba(168, 85, 247, 0.1); border: 1px solid rgba(168, 85, 247, 0.25); border-radius: 10px; padding: 10px; font-size: 0.75rem; color: #cbd5e1; margin-bottom: 14px;">
              ⚡ <strong>Instant Payout:</strong> Payouts are processed via automated UPI gateway within 5-15 minutes.
            </div>

            <button type="submit" class="btn-submit-action purple">Request Instant Withdrawal</button>
          </form>
        </div>

        <!-- TAB: DEPOSIT HISTORY -->
        <div class="wtab-content" id="wtab-deposit-history" style="display:none;">
          <div id="deposit-history-list" style="max-height: 280px; overflow-y: auto;">
            <div style="text-align: center; color: #64748b; padding: 20px;">Loading history...</div>
          </div>
        </div>

        <!-- TAB: WITHDRAW HISTORY -->
        <div class="wtab-content" id="wtab-withdraw-history" style="display:none;">
          <div id="withdraw-history-list" style="max-height: 280px; overflow-y: auto;">
            <div style="text-align: center; color: #64748b; padding: 20px;">Loading history...</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL 2: ACCOUNT & KYC MODAL -->
  <div class="modal-overlay" id="account-modal">
    <div class="modal-card">
      <div class="modal-header">
        <div class="modal-title-box">
          <span class="modal-icon">👤</span>
          <span class="modal-title">Account Profile</span>
        </div>
        <button class="modal-close-btn" data-close-modal>&times;</button>
      </div>
      <div class="modal-body">
        <div style="text-align: center; margin-bottom: 18px;">
          <div class="profile-avatar-circle" style="width: 56px; height: 56px; font-size: 1.5rem; margin: 0 auto 10px;">
            <?= $firstLetter ?>
          </div>
          <div style="font-size: 1.1rem; font-weight: 800; color: #fff;"><?= $displayName ?></div>
          <div style="font-size: 0.78rem; color: #94a3b8; font-family: var(--font-mono);"><?= htmlspecialchars($user['email']) ?></div>
        </div>

        <div style="background: rgba(18, 13, 38, 0.8); border: 1px solid var(--border-subtle); border-radius: 12px; padding: 14px; margin-bottom: 16px;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="font-size: 0.78rem; color: #94a3b8;">User ID</span>
            <span style="font-size: 0.78rem; color: #fff; font-family: var(--font-mono);"><?= htmlspecialchars(substr($user['id'], 0, 14)) ?>...</span>
          </div>
          <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="font-size: 0.78rem; color: #94a3b8;">Wallet Balance</span>
            <span style="font-size: 0.85rem; font-weight: 800; color: #34d399;">₹<?= number_format($user['balance'], 2) ?></span>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="font-size: 0.78rem; color: #94a3b8;">KYC Status</span>
            <span style="font-size: 0.75rem; font-weight: 800; color: #34d399;">✓ VERIFIED</span>
          </div>
        </div>

        <div style="display: flex; gap: 10px;">
          <button class="btn-submit-action green" data-open-wallet="deposit" style="flex:1;">Deposit ₹</button>
          <a href="api/auth.php?action=logout" class="btn-submit-action red" style="flex:1; text-decoration:none; text-align:center;">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL 3: LIVE SUPPORT & AI CHAT -->
  <div class="modal-overlay" id="live-support-modal">
    <div class="modal-card" style="max-width: 460px;">
      <div class="modal-header">
        <div class="modal-title-box">
          <span class="modal-icon">🎧</span>
          <span class="modal-title">24/7 Live Support</span>
        </div>
        <button class="modal-close-btn" data-close-modal>&times;</button>
      </div>
      <div class="modal-body" style="padding: 12px;">
        <div id="support-messages-box" style="height: 300px; overflow-y: auto; background: #0b081c; border-radius: 12px; padding: 12px; display: flex; flex-direction: column; gap: 8px; margin-bottom: 10px; border: 1px solid var(--border-subtle);">
          <div style="background: rgba(147, 51, 234, 0.2); border: 1px solid rgba(147, 51, 234, 0.4); padding: 8px 12px; border-radius: 10px; font-size: 0.8rem; color: #f1f5f9; align-self: flex-start; max-width: 85%;">
            👋 Hello <?= $displayName ?>! How can our support team help you with your deposits, withdrawals, or gameplay today?
          </div>
        </div>
        <form id="support-chat-form" style="display: flex; gap: 8px;">
          <input type="text" id="support-msg-input" class="app-input" placeholder="Type your message in English or Hindi..." required style="flex:1;">
          <button type="submit" class="btn-submit-action purple" style="width: auto; padding: 0 16px;">Send</button>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL 4: PROMOTIONS & REWARDS -->
  <div class="modal-overlay" id="promotions-modal">
    <div class="modal-card">
      <div class="modal-header">
        <div class="modal-title-box">
          <span class="modal-icon">🎁</span>
          <span class="modal-title">Promotions &amp; VIP Rewards</span>
        </div>
        <button class="modal-close-btn" data-close-modal>&times;</button>
      </div>
      <div class="modal-body">
        <div style="background: linear-gradient(135deg, rgba(26, 16, 58, 0.95), rgba(15, 10, 36, 0.95)); border: 1.5px solid #fbbf24; border-radius: 14px; padding: 16px; margin-bottom: 14px;">
          <div style="font-size: 0.7rem; font-weight: 900; color: #fbbf24; text-transform: uppercase;">🏆 WEEKLY LEADERBOARD RACE</div>
          <div style="font-size: 1.4rem; font-weight: 900; color: #34d399; margin: 4px 0;">₹5,00,000 Prize Pool</div>
          <div style="font-size: 0.78rem; color: #cbd5e1; line-height: 1.4;">Wager in Bombaclat Mines or Chicken Cross Road to climb the leaderboard. Top 50 players win cash prizes every Sunday!</div>
        </div>

        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px; padding: 12px; margin-bottom: 14px;">
          <div style="font-size: 0.82rem; font-weight: 800; color: #34d399;">💎 Instant ₹500 Signup Deposit Bonus</div>
          <div style="font-size: 0.75rem; color: #94a3b8;">Get 100% matched bonus on your next topup up to ₹5,000!</div>
        </div>

        <button class="btn-submit-action green" data-open-wallet="deposit">Deposit &amp; Claim Bonus</button>
      </div>
    </div>
  </div>

  <!-- MODAL 5: LEADERBOARD -->
  <div class="modal-overlay" id="leaderboard-modal">
    <div class="modal-card">
      <div class="modal-header">
        <div class="modal-title-box">
          <span class="modal-icon">🏆</span>
          <span class="modal-title">Top Weekly High Rollers</span>
        </div>
        <button class="modal-close-btn" data-close-modal>&times;</button>
      </div>
      <div class="modal-body">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">
          <thead>
            <tr style="border-bottom: 1px solid var(--border-subtle); color: #94a3b8; font-size: 0.7rem; text-transform: uppercase;">
              <th style="text-align: left; padding: 8px;">Rank</th>
              <th style="text-align: left; padding: 8px;">Player</th>
              <th style="text-align: left; padding: 8px;">Game</th>
              <th style="text-align: right; padding: 8px;">Winnings</th>
            </tr>
          </thead>
          <tbody>
            <tr style="border-bottom: 1px solid rgba(139, 92, 246, 0.1);">
              <td style="padding: 10px 8px; font-weight: 900; color: #fbbf24;">🥇 #1</td>
              <td style="padding: 10px 8px; font-weight: 800; color: #fff;">Aman T.</td>
              <td style="padding: 10px 8px; color: #c084fc;">💣 Mines</td>
              <td style="padding: 10px 8px; text-align: right; font-weight: 900; color: #34d399;">₹2,48,910</td>
            </tr>
            <tr style="border-bottom: 1px solid rgba(139, 92, 246, 0.1);">
              <td style="padding: 10px 8px; font-weight: 900; color: #cbd5e1;">🥈 #2</td>
              <td style="padding: 10px 8px; font-weight: 800; color: #fff;">Deepak M.</td>
              <td style="padding: 10px 8px; color: #34d399;">🐔 Chicken Road</td>
              <td style="padding: 10px 8px; text-align: right; font-weight: 900; color: #34d399;">₹1,94,200</td>
            </tr>
            <tr style="border-bottom: 1px solid rgba(139, 92, 246, 0.1);">
              <td style="padding: 10px 8px; font-weight: 900; color: #b45309;">🥉 #3</td>
              <td style="padding: 10px 8px; font-weight: 800; color: #fff;">Karan V.</td>
              <td style="padding: 10px 8px; color: #c084fc;">💣 Mines</td>
              <td style="padding: 10px 8px; text-align: right; font-weight: 900; color: #34d399;">₹1,42,800</td>
            </tr>
            <tr>
              <td style="padding: 10px 8px; font-weight: 800; color: #94a3b8;">#4</td>
              <td style="padding: 10px 8px; font-weight: 800; color: #fff;">Rohit S.</td>
              <td style="padding: 10px 8px; color: #c084fc;">💣 Mines</td>
              <td style="padding: 10px 8px; text-align: right; font-weight: 900; color: #34d399;">₹1,12,480</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Modal Triggers
      document.querySelectorAll('[data-open-modal]').forEach(b => {
        b.addEventListener('click', () => {
          const target = b.dataset.openModal;
          const m = document.getElementById(target);
          if (m) m.classList.add('active');
        });
      });

      document.querySelectorAll('[data-open-wallet]').forEach(b => {
        b.addEventListener('click', () => {
          const tab = b.dataset.openWallet || 'deposit';
          const wm = document.getElementById('wallet-modal');
          if (wm) {
            wm.classList.add('active');
            switchWalletTab(tab);
          }
        });
      });

      document.querySelectorAll('[data-close-modal]').forEach(b => {
        b.addEventListener('click', () => {
          document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
        });
      });

      // Wallet Tabs
      const wtabBtns = document.querySelectorAll('.wtab-btn');
      function switchWalletTab(tabName) {
        wtabBtns.forEach(btn => {
          if (btn.dataset.wtab === tabName) btn.classList.add('active');
          else btn.classList.remove('active');
        });
        document.querySelectorAll('.wtab-content').forEach(c => c.style.display = 'none');
        const targetContent = document.getElementById(`wtab-${tabName}`);
        if (targetContent) targetContent.style.display = 'block';

        if (tabName === 'deposit-history') loadDepositHistory();
        if (tabName === 'withdraw-history') loadWithdrawHistory();
      }

      wtabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
          switchWalletTab(btn.dataset.wtab);
        });
      });

      // Quick Amount Chips
      document.querySelectorAll('.amount-chip').forEach(chip => {
        chip.addEventListener('click', () => {
          const amt = chip.dataset.amt;
          const input = document.getElementById('deposit-amount-input');
          if (input) input.value = amt;
        });
      });

      // Deposit Form Submit
      const depForm = document.getElementById('lobby-deposit-form');
      if (depForm) {
        depForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          const amount = parseFloat(document.getElementById('deposit-amount-input').value);
          const utr = document.getElementById('deposit-utr-input').value.trim();
          const phone = document.getElementById('deposit-phone-input').value.trim();

          if (!amount || amount < 100) {
            alert('Minimum deposit amount is ₹100');
            return;
          }
          if (!utr || utr.length < 8) {
            alert('Please enter a valid 12-digit UPI UTR / Reference number');
            return;
          }

          try {
            const res = await fetch('api/deposit.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ amount, utr_number: utr, mobile_number: phone })
            });
            const data = await res.json();
            if (data.success) {
              alert('Deposit request submitted successfully! Your wallet will be credited shortly after verification.');
              depForm.reset();
              switchWalletTab('deposit-history');
            } else {
              alert(data.error || 'Failed to submit deposit');
            }
          } catch (err) {
            alert('Network error. Please try again.');
          }
        });
      }

      // Withdraw Form Submit
      const withForm = document.getElementById('lobby-withdraw-form');
      if (withForm) {
        withForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          const amount = parseFloat(document.getElementById('withdraw-amount-input').value);
          const upi = document.getElementById('withdraw-upi-input').value.trim();
          const phone = document.getElementById('withdraw-phone-input').value.trim();

          if (!amount || amount < 500) {
            alert('Minimum withdrawal is ₹500');
            return;
          }
          if (!upi || !upi.includes('@')) {
            alert('Please enter a valid UPI ID (e.g. name@bank)');
            return;
          }

          try {
            const res = await fetch('api/withdrawal.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ amount, upi_id: upi, mobile_number: phone })
            });
            const data = await res.json();
            if (data.success) {
              alert('Withdrawal request submitted successfully! Your funds will arrive via UPI shortly.');
              withForm.reset();
              if (data.new_balance !== undefined) {
                const balEl = document.getElementById('user-balance-display');
                if (balEl) balEl.textContent = '₹' + Number(data.new_balance).toFixed(2);
                const mbalEl = document.getElementById('modal-wallet-balance');
                if (mbalEl) mbalEl.textContent = '₹' + Number(data.new_balance).toFixed(2);
              }
              switchWalletTab('withdraw-history');
            } else {
              alert(data.error || 'Withdrawal failed');
            }
          } catch (err) {
            alert('Network error. Please try again.');
          }
        });
      }

      // Load Deposit History
      async function loadDepositHistory() {
        const box = document.getElementById('deposit-history-list');
        if (!box) return;
        try {
          const res = await fetch('api/deposit.php?action=history');
          const data = await res.json();
          if (data.success && data.deposits && data.deposits.length > 0) {
            box.innerHTML = data.deposits.map(d => `
              <div style="display:flex; justify-content:space-between; align-items:center; padding:10px; border-bottom:1px solid rgba(139,92,246,0.15);">
                <div>
                  <div style="font-weight:800; color:#fff;">₹${Number(d.amount).toFixed(2)}</div>
                  <div style="font-size:0.7rem; color:#94a3b8; font-family:var(--font-mono);">UTR: ${d.utr_number || '-'}</div>
                </div>
                <div style="text-align:right;">
                  <span style="font-size:0.7rem; font-weight:800; color:${d.status === 'approved' ? '#34d399' : (d.status === 'rejected' ? '#ef4444' : '#fbbf24')};">${d.status.toUpperCase()}</span>
                  <div style="font-size:0.65rem; color:#64748b;">${d.created_at}</div>
                </div>
              </div>
            `).join('');
          } else {
            box.innerHTML = '<div style="text-align:center; color:#64748b; padding:20px;">No deposit transactions yet</div>';
          }
        } catch (e) {
          box.innerHTML = '<div style="text-align:center; color:#ef4444; padding:20px;">Failed to load deposits</div>';
        }
      }

      // Load Withdraw History
      async function loadWithdrawHistory() {
        const box = document.getElementById('withdraw-history-list');
        if (!box) return;
        try {
          const res = await fetch('api/withdrawal.php?action=history');
          const data = await res.json();
          if (data.success && data.withdrawals && data.withdrawals.length > 0) {
            box.innerHTML = data.withdrawals.map(w => `
              <div style="display:flex; justify-content:space-between; align-items:center; padding:10px; border-bottom:1px solid rgba(139,92,246,0.15);">
                <div>
                  <div style="font-weight:800; color:#fff;">₹${Number(w.amount).toFixed(2)}</div>
                  <div style="font-size:0.7rem; color:#94a3b8; font-family:var(--font-mono);">${w.upi_id || '-'}</div>
                </div>
                <div style="text-align:right;">
                  <span style="font-size:0.7rem; font-weight:800; color:${w.status === 'approved' ? '#34d399' : (w.status === 'rejected' ? '#ef4444' : '#fbbf24')};">${w.status.toUpperCase()}</span>
                  <div style="font-size:0.65rem; color:#64748b;">${w.created_at}</div>
                </div>
              </div>
            `).join('');
          } else {
            box.innerHTML = '<div style="text-align:center; color:#64748b; padding:20px;">No withdrawal requests yet</div>';
          }
        } catch (e) {
          box.innerHTML = '<div style="text-align:center; color:#ef4444; padding:20px;">Failed to load withdrawals</div>';
        }
      }

      // Live Support Chat Form
      const supForm = document.getElementById('support-chat-form');
      if (supForm) {
        supForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          const input = document.getElementById('support-msg-input');
          const msg = input.value.trim();
          if (!msg) return;

          const box = document.getElementById('support-messages-box');
          const userMsgDiv = document.createElement('div');
          userMsgDiv.style.cssText = 'background:rgba(124,58,237,0.3); border:1px solid rgba(168,85,247,0.5); padding:8px 12px; border-radius:10px; font-size:0.8rem; color:#fff; align-self:flex-end; max-width:85%;';
          userMsgDiv.textContent = msg;
          box.appendChild(userMsgDiv);
          input.value = '';
          box.scrollTop = box.scrollHeight;

          try {
            const res = await fetch('api/support.php?action=send_message', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ message: msg })
            });
            const data = await res.json();
            if (data.success && data.reply) {
              const replyDiv = document.createElement('div');
              replyDiv.style.cssText = 'background:rgba(16,185,129,0.2); border:1px solid rgba(16,185,129,0.4); padding:8px 12px; border-radius:10px; font-size:0.8rem; color:#fff; align-self:flex-start; max-width:85%; margin-top:4px;';
              replyDiv.textContent = data.reply;
              box.appendChild(replyDiv);
              box.scrollTop = box.scrollHeight;
            }
          } catch (err) {
            console.error(err);
          }
        });
      }

      // Dynamic Live Winners Ticker Updater
      const sampleNames = ['Suresh R.', 'Vikram S.', 'Kunal B.', 'Deepak M.', 'Anjali G.', 'Rahul K.', 'Pooja T.', 'Sunil M.', 'Akash N.'];
      const sampleGames = [
        { name: '💣 Mines', min: 1000, max: 45000 },
        { name: '🐔 Chicken Road', min: 800, max: 35000 }
      ];

      setInterval(() => {
        const wrap = document.querySelector('.winners-ticker-wrap');
        if (!wrap) return;
        const randomName = sampleNames[Math.floor(Math.random() * sampleNames.length)];
        const randomGame = sampleGames[Math.floor(Math.random() * sampleGames.length)];
        const randomAmt = (Math.floor(Math.random() * (randomGame.max - randomGame.min)) + randomGame.min);
        const card = document.createElement('div');
        card.className = 'winner-capsule-card';
        card.style.animation = 'fadeIn 0.4s ease';
        card.innerHTML = `
          <div class="winner-user-col">
            <div class="winner-avatar-circle" style="background: #${Math.floor(Math.random()*16777215).toString(16)};">${randomName[0]}</div>
            <div>
              <div class="winner-user-name">${randomName}</div>
              <div class="winner-won-sub">Just won</div>
            </div>
          </div>
          <div>
            <div class="winner-payout-badge">₹${randomAmt.toLocaleString('en-IN')}</div>
            <div class="winner-game-tag">${randomGame.name}</div>
          </div>
        `;
        wrap.insertBefore(card, wrap.firstChild);
        if (wrap.children.length > 6) {
          wrap.removeChild(wrap.lastChild);
        }
      }, 7000);
    });
  </script>
</body>
</html>
