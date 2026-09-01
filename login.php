<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

// If already logged in, redirect to index
if (getCurrentUser()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>BOMBACLAT MINE - Login & Play</title>
  <link rel="icon" type="image/png" href="assets/images/favicon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,800;0,900;1,900&family=Outfit:wght@400;600;700;800;900&family=Permanent+Marker&family=Space+Grotesk:wght@500;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/app.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      min-height: 100vh;
      background: #070312 radial-gradient(circle at 50% 12%, rgba(147, 51, 234, 0.28) 0%, rgba(7, 3, 18, 0.96) 65%), url('assets/images/login_bg.png') center top / cover no-repeat fixed;
      font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: #ffffff;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 24px 16px;
      overflow-x: hidden;
      position: relative;
    }

    /* Ambient Glow Spots */
    body::before {
      content: '';
      position: fixed;
      top: -100px;
      left: 50%;
      transform: translateX(-50%);
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(168, 85, 247, 0.35) 0%, rgba(0, 0, 0, 0) 70%);
      pointer-events: none;
      z-index: 0;
    }

    .login-container {
      width: 100%;
      max-width: 440px;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      z-index: 1;
      margin: 0 auto;
    }

    /* 3D Hero Bomb Emblem */
    .hero-bomb-wrap {
      position: relative;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: floatBomb 4s ease-in-out infinite;
    }
    .hero-bomb-img {
      width: 155px;
      height: 155px;
      object-fit: contain;
      filter: drop-shadow(0 0 32px rgba(192, 132, 252, 0.65)) drop-shadow(0 15px 25px rgba(0, 0, 0, 0.8));
      display: block;
    }
    @keyframes floatBomb {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-8px) rotate(1.5deg); }
    }

    /* Branding Typography */
    .brand-logo-area {
      text-align: center;
      margin-bottom: 18px;
      user-select: none;
    }
    .brand-title-bombaclat {
      font-family: 'Montserrat', 'Outfit', sans-serif;
      font-size: 2.5rem;
      font-weight: 900;
      letter-spacing: 2px;
      text-transform: uppercase;
      line-height: 0.95;
      background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 25%, #cbd5e1 55%, #94a3b8 80%, #64748b 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      filter: drop-shadow(0 0 18px rgba(168, 85, 247, 0.7)) drop-shadow(0 5px 8px rgba(0, 0, 0, 0.9));
    }
    .brand-title-mine {
      font-family: 'Permanent Marker', 'Impact', cursive;
      font-size: 2.3rem;
      font-weight: 900;
      color: #a3e635;
      letter-spacing: 3px;
      text-transform: uppercase;
      line-height: 0.9;
      transform: rotate(-3.5deg) translateY(-4px);
      display: inline-block;
      text-shadow: 0 0 20px rgba(163, 230, 53, 0.95), 0 0 35px rgba(163, 230, 53, 0.6), 0 4px 10px rgba(0, 0, 0, 0.9);
    }
    .brand-tagline-bar {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 0.78rem;
      font-weight: 800;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-top: 10px;
    }
    .brand-tagline-bar::before,
    .brand-tagline-bar::after {
      content: '';
      height: 1.5px;
      width: 28px;
      background: linear-gradient(90deg, transparent, #a855f7);
    }
    .brand-tagline-bar::after {
      background: linear-gradient(90deg, #a855f7, transparent);
    }
    .highlight-cash-big {
      color: #a3e635;
      text-shadow: 0 0 10px rgba(163, 230, 53, 0.8);
    }

    /* Glassmorphic Glowing Login Card */
    .glass-login-card {
      width: 100%;
      background: rgba(17, 10, 38, 0.78);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      border: 1.5px solid rgba(168, 85, 247, 0.45);
      border-radius: 24px;
      padding: 26px 24px;
      box-shadow: 0 0 35px rgba(147, 51, 234, 0.28), 0 24px 64px rgba(0, 0, 0, 0.85), inset 0 0 20px rgba(168, 85, 247, 0.12);
      margin-bottom: 24px;
      position: relative;
    }

    .card-header-title {
      text-align: center;
      margin-bottom: 20px;
    }
    .card-title-text {
      font-size: 1.15rem;
      font-weight: 900;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .lightning-icon {
      color: #c084fc;
      font-size: 1rem;
      filter: drop-shadow(0 0 8px #c084fc);
    }
    .card-subtitle-text {
      font-size: 0.78rem;
      color: #94a3b8;
      margin-top: 4px;
      font-weight: 500;
    }

    /* Sleek Icon Inputs */
    .custom-input-group {
      margin-bottom: 14px;
    }
    .custom-input-wrap {
      display: flex;
      align-items: center;
      background: #0d0722;
      border: 1.5px solid rgba(139, 92, 246, 0.35);
      border-radius: 14px;
      overflow: hidden;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .custom-input-wrap:focus-within {
      border-color: #c084fc;
      box-shadow: 0 0 16px rgba(192, 132, 252, 0.4);
    }
    .input-icon-box {
      width: 46px;
      height: 46px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(147, 51, 234, 0.18);
      border-right: 1px solid rgba(139, 92, 246, 0.25);
      color: #c084fc;
      flex-shrink: 0;
    }
    .input-field-custom {
      flex: 1;
      background: transparent;
      border: none;
      outline: none;
      padding: 12px 14px;
      font-size: 0.92rem;
      color: #ffffff;
      font-family: inherit;
    }
    .input-field-custom::placeholder {
      color: #64748b;
    }
    .password-toggle-btn {
      background: transparent;
      border: none;
      color: #94a3b8;
      padding: 0 14px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: color 0.2s;
    }
    .password-toggle-btn:hover {
      color: #c084fc;
    }

    /* Links Row */
    .forgot-link-row {
      display: flex;
      justify-content: flex-end;
      margin-top: -4px;
      margin-bottom: 18px;
    }
    .forgot-pass-link {
      font-size: 0.76rem;
      color: #c084fc;
      text-decoration: none;
      font-weight: 700;
      transition: color 0.2s;
      cursor: pointer;
    }
    .forgot-pass-link:hover {
      color: #e9d5ff;
      text-decoration: underline;
    }

    /* Glowing Lime Action Button */
    .btn-lime-login {
      width: 100%;
      height: 52px;
      background: linear-gradient(135deg, #a3e635 0%, #84cc16 100%);
      color: #06040e;
      border: none;
      border-radius: 14px;
      font-size: 1.05rem;
      font-weight: 900;
      letter-spacing: 1.2px;
      text-transform: uppercase;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      cursor: pointer;
      box-shadow: 0 0 24px rgba(163, 230, 53, 0.55), 0 4px 16px rgba(0, 0, 0, 0.5);
      transition: transform 0.15s, box-shadow 0.15s;
    }
    .btn-lime-login:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 0 35px rgba(163, 230, 53, 0.8), 0 8px 24px rgba(0, 0, 0, 0.6);
    }
    .btn-lime-login:active:not(:disabled) {
      transform: translateY(1px);
    }
    .btn-lime-login:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    /* Card Footer Switch */
    .card-switch-footer {
      text-align: center;
      margin-top: 18px;
      font-size: 0.82rem;
      color: #94a3b8;
    }
    .switch-auth-link {
      color: #a3e635;
      font-weight: 800;
      cursor: pointer;
      text-decoration: none;
      margin-left: 4px;
      transition: color 0.2s;
    }
    .switch-auth-link:hover {
      color: #bef264;
      text-decoration: underline;
    }

    /* Error Banner */
    .auth-error-banner {
      background: rgba(239, 68, 68, 0.18);
      border: 1px solid rgba(239, 68, 68, 0.5);
      color: #fca5a5;
      padding: 10px 14px;
      border-radius: 12px;
      font-size: 0.82rem;
      margin-bottom: 14px;
      display: none;
      text-align: center;
    }

    /* Bottom Trust & Feature Badges */
    .bottom-features-grid {
      width: 100%;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      text-align: center;
    }
    .feature-badge-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 4px;
    }
    .feature-icon-svg {
      color: #c084fc;
      filter: drop-shadow(0 0 8px rgba(192, 132, 252, 0.7));
      margin-bottom: 2px;
    }
    .feature-title-bold {
      font-size: 0.75rem;
      font-weight: 900;
      color: #f8fafc;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .feature-desc-muted {
      font-size: 0.65rem;
      color: #94a3b8;
      line-height: 1.2;
    }

    @media (max-width: 480px) {
      .hero-bomb-img {
        width: 130px;
        height: 130px;
      }
      .brand-title-bombaclat {
        font-size: 2.1rem;
      }
      .brand-title-mine {
        font-size: 1.95rem;
      }
      .glass-login-card {
        padding: 22px 18px;
        border-radius: 20px;
      }
      .bottom-features-grid {
        gap: 8px;
      }
      .feature-title-bold {
        font-size: 0.68rem;
      }
      .feature-desc-muted {
        font-size: 0.6rem;
      }
    }
  </style>
</head>
<body>

  <div class="login-container">

    <!-- 3D Glowing Skull Bomb -->
    <div class="hero-bomb-wrap">
      <img src="assets/images/login_hero_bomb.png" alt="Bombaclat Mine Skull Bomb" class="hero-bomb-img" onerror="this.src='assets/images/skull_bomb.png'">
    </div>

    <!-- Metallic & Neon Branding -->
    <div class="brand-logo-area">
      <h1 class="brand-title-bombaclat">BOMBACLAT</h1>
      <span class="brand-title-mine">MINE</span>
      <div class="brand-tagline-bar">
        <span>PICK A TILE. RISK IT ALL. <span class="highlight-cash-big">CASH BIG.</span></span>
      </div>
    </div>

    <!-- Glassmorphic Card -->
    <div class="glass-login-card">

      <!-- Error Alert -->
      <div class="auth-error-banner" id="auth-error-alert"></div>

      <!-- ================= LOGIN FORM ================= -->
      <div id="login-section">
        <div class="card-header-title">
          <div class="card-title-text">
            <span class="lightning-icon">⚡</span>
            <span>WELCOME BACK</span>
            <span class="lightning-icon">⚡</span>
          </div>
          <p class="card-subtitle-text">Login to continue and win big!</p>
        </div>

        <form id="login-form">
          <!-- Phone or Email -->
          <div class="custom-input-group">
            <div class="custom-input-wrap">
              <div class="input-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
              </div>
              <input type="text" id="login-email" name="email" class="input-field-custom" placeholder="Phone or Email" autocomplete="username" required>
            </div>
          </div>

          <!-- Password -->
          <div class="custom-input-group">
            <div class="custom-input-wrap">
              <div class="input-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
              </div>
              <input type="password" id="login-password" name="password" class="input-field-custom" placeholder="Password" autocomplete="current-password" required>
              <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('login-password', this)" aria-label="Toggle password visibility">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>
          </div>

          <!-- Forgot Password Link -->
          <div class="forgot-link-row">
            <span class="forgot-pass-link" onclick="alert('Please contact 24/7 Live Support to recover or reset your account credentials.');">Forgot Password?</span>
          </div>

          <!-- Submit Action -->
          <button type="submit" class="btn-lime-login" id="btn-login-submit">
            <span>LOGIN</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </button>
        </form>

        <div class="card-switch-footer">
          <span>Don't have an account?</span>
          <span class="switch-auth-link" onclick="switchView('signup')">Sign Up</span>
        </div>
      </div>

      <!-- ================= SIGN UP FORM ================= -->
      <div id="signup-section" style="display: none;">
        <div class="card-header-title">
          <div class="card-title-text">
            <span class="lightning-icon">⚡</span>
            <span>CREATE ACCOUNT</span>
            <span class="lightning-icon">⚡</span>
          </div>
          <p class="card-subtitle-text" style="color: #34d399; font-weight: 700;">🎁 Free ₹500 Welcome Bonus on Signup!</p>
        </div>

        <form id="signup-form">
          <!-- Full Name -->
          <div class="custom-input-group">
            <div class="custom-input-wrap">
              <div class="input-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
              </div>
              <input type="text" id="signup-name" name="full_name" class="input-field-custom" placeholder="Full Name" required>
            </div>
          </div>

          <!-- Email or Phone -->
          <div class="custom-input-group">
            <div class="custom-input-wrap">
              <div class="input-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                  <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
              </div>
              <input type="email" id="signup-email" name="email" class="input-field-custom" placeholder="Email Address" required>
            </div>
          </div>

          <!-- Password -->
          <div class="custom-input-group" style="margin-bottom: 18px;">
            <div class="custom-input-wrap">
              <div class="input-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
              </div>
              <input type="password" id="signup-password" name="password" class="input-field-custom" placeholder="Create Password (Min. 6 chars)" minlength="6" required>
              <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('signup-password', this)" aria-label="Toggle password visibility">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>
          </div>

          <!-- Submit Signup -->
          <button type="submit" class="btn-lime-login" id="btn-signup-submit">
            <span>CLAIM ₹500 & SIGN UP</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </button>
        </form>

        <div class="card-switch-footer">
          <span>Already have an account?</span>
          <span class="switch-auth-link" onclick="switchView('login')">Log In</span>
        </div>
      </div>

    </div>

    <!-- Bottom Feature Badges -->
    <div class="bottom-features-grid">
      <!-- 100% SECURE -->
      <div class="feature-badge-card">
        <svg class="feature-icon-svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
        </svg>
        <div class="feature-title-bold">100% SECURE</div>
        <div class="feature-desc-muted">Your data is protected</div>
      </div>

      <!-- INSTANT PLAY -->
      <div class="feature-badge-card">
        <svg class="feature-icon-svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
        </svg>
        <div class="feature-title-bold">INSTANT PLAY</div>
        <div class="feature-desc-muted">No downloads required</div>
      </div>

      <!-- BIG WINS -->
      <div class="feature-badge-card">
        <svg class="feature-icon-svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
          <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
          <path d="M4 22h16"></path>
          <path d="M10 14.66V17c0 .55-.45 1-1 1H7.5c-.55 0-1 .45-1 1v1c0 .55.45 1 1 1h9c.55 0 1-.45 1-1v-1c0-.55-.45-1-1-1H15c-.55 0-1-.45-1-1v-2.34"></path>
          <path d="M18 9V4a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v5a6 6 0 0 0 12 0Z"></path>
        </svg>
        <div class="feature-title-bold">BIG WINS</div>
        <div class="feature-desc-muted">Real players. Real cash.</div>
      </div>
    </div>

  </div>

  <script>
    function switchView(view) {
      const isLogin = (view === 'login');
      document.getElementById('login-section').style.display = isLogin ? 'block' : 'none';
      document.getElementById('signup-section').style.display = isLogin ? 'none' : 'block';
      hideError();
    }

    function togglePasswordVisibility(inputId, btn) {
      const input = document.getElementById(inputId);
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = `
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
            <line x1="1" y1="1" x2="23" y2="23"></line>
          </svg>
        `;
      } else {
        input.type = 'password';
        btn.innerHTML = `
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
          </svg>
        `;
      }
    }

    function showError(msg) {
      const alert = document.getElementById('auth-error-alert');
      alert.textContent = msg;
      alert.style.display = 'block';
    }

    function hideError() {
      const alert = document.getElementById('auth-error-alert');
      alert.style.display = 'none';
    }

    // Login Form Submit
    document.getElementById('login-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      hideError();
      const form = e.target;
      const btn = document.getElementById('btn-login-submit');
      btn.disabled = true;
      btn.querySelector('span').textContent = 'VERIFYING...';

      const email = form.email.value.trim();
      const password = form.password.value;

      try {
        const res = await fetch('api/auth.php?action=login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, password })
        });
        const data = await res.json();
        if (data.success) {
          window.location.href = 'index.php';
        } else {
          showError(data.error || 'Failed to sign in. Please check your credentials.');
        }
      } catch (err) {
        showError('Network connection error');
      } finally {
        btn.disabled = false;
        btn.querySelector('span').textContent = 'LOGIN';
      }
    });

    // Signup Form Submit
    document.getElementById('signup-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      hideError();
      const form = e.target;
      const btn = document.getElementById('btn-signup-submit');
      btn.disabled = true;
      btn.querySelector('span').textContent = 'CREATING ACCOUNT...';

      const fullName = form.full_name.value.trim();
      const email = form.email.value.trim();
      const password = form.password.value;

      try {
        const res = await fetch('api/auth.php?action=signup', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ full_name: fullName, email, password })
        });
        const data = await res.json();
        if (data.success) {
          window.location.href = 'index.php';
        } else {
          showError(data.error || 'Registration failed');
        }
      } catch (err) {
        showError('Network connection error');
      } finally {
        btn.disabled = false;
        btn.querySelector('span').textContent = 'CLAIM ₹500 & SIGN UP';
      }
    });
  </script>
</body>
</html>
