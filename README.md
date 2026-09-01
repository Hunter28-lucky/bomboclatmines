# 💣 Bomboclat Mines - Standalone PHP & MySQL Web App

An ultra-fast, modern Mines gambling game web application built with pure **PHP 7.4+**, **MySQL (PDO)**, and high-end responsive **Vanilla CSS & JS**. 
Completely free of any Supabase, Node.js, or complex server build dependencies — ready to upload and run directly on **InfinityFree** or any standard cPanel/LAMP hosting!

---

## 🌟 Key Features

- **🎮 Mines Game Engine**: Progressive multipliers with house edge, customizable grid size (4x4, 5x5, 6x6), bomb slider (1 to 40%), real-time win accumulation, and instant cashout.
- **🔊 Web Audio & Haptics**: Built-in sound effects (reward chime, bomb blast, cashout fanfare) and mobile vibration haptic feedback.
- **🔐 Secure Authentication**: Native PHP session-based login, signup with ₹500 welcome bonus, avatar upload, and profile management.
- **💳 UPI Deposit System (RazorPay Style)**: QR Code display (`assets/images/qrpayment.jpeg`), UTR number verification, and screenshot receipt uploads.
- **💸 Withdrawal Management**: Minimum ₹500 validation, UPI ID and mobile tracking, real-time withdrawal history with status indicators (Pending / Approved / Rejected).
- **🛡️ Comprehensive Admin Panel (`admin.php` & `goswami.php`)**:
  - Protected admin access (`krrishyogi18@gmail.com`).
  - Total users, balance pool, and payment metrics overview.
  - Inline user balance editor.
  - Deposit payment verification & approval (with screenshot lightbox).
  - Withdrawal processing with custom admin notes and automatic balance adjustment.

---

## 🚀 How to Host on InfinityFree

1. **Create an Account on InfinityFree**:
   - Go to [InfinityFree](https://www.infinityfree.com) and create an account.
   - Create a new free hosting account and open the **Control Panel (cPanel)**.

2. **Create MySQL Database**:
   - In cPanel, click **MySQL Databases**.
   - Create a new database (e.g. `if0_xxxx_bomboclat`).
   - Note down:
     - **MySQL Hostname** (e.g. `sqlxxx.infinityfree.com`)
     - **MySQL Database Name** (e.g. `if0_xxxx_bomboclat`)
     - **MySQL Username** (e.g. `if0_xxxx`)
     - **MySQL Password** (your account password)

3. **Import Database Schema**:
   - In cPanel, click **phpMyAdmin** and select your database.
   - Click the **Import** tab.
   - Select the `schema.sql` file from this project and click **Go** to create all tables.

4. **Update Database Credentials in `config/db.php`**:
   - Open `config/db.php` and set your credentials:
     ```php
     define('DB_HOST', 'sqlxxx.infinityfree.com'); // Your InfinityFree MySQL Host
     define('DB_NAME', 'if0_xxxx_bomboclat');      // Your Database Name
     define('DB_USER', 'if0_xxxx');                // Your MySQL Username
     define('DB_PASS', 'your_password_here');      // Your MySQL Password
     ```

5. **Upload Files via FTP or File Manager**:
   - Connect via FTP (using FileZilla or InfinityFree Online File Manager).
   - Upload all files into the **`htdocs/`** directory.

6. **Open Your Website**:
   - Visit `http://your-domain.infinityfreeapp.com` in your browser!
   - Default Admin account: `krrishyogi18@gmail.com` (password can be set on signup or via `install.php`).

---

## 💻 Local Testing

You can run the application immediately on your local machine using the built-in PHP server:

```bash
php -S localhost:8000
```
Then open `http://localhost:8000` in your browser. (The app automatically falls back to an embedded SQLite database in `data/database.sqlite` if MySQL is not running locally).

---

## 📁 Project Structure

```
bomboclatmines/
├── config/
│   ├── config.php          # App constants (admin email, initial balance, paths)
│   └── db.php              # PDO MySQL connection with auto-init & SQLite fallback
├── includes/
│   ├── auth.php            # Session authentication, login, registration, roles
│   ├── game_engine.php     # Server-side mines RNG, tile reveals, multiplier math
│   └── helpers.php         # JSON wrappers, UUID generator, image upload handler
├── api/
│   ├── auth.php            # Auth REST API
│   ├── balance.php         # User balance & stats API
│   ├── game.php            # Game start, move, cashout API
│   ├── deposit.php         # UPI deposit payment submission API
│   ├── withdrawal.php      # Withdrawal requests & history API
│   └── admin.php           # Admin control panel API
├── uploads/
│   ├── avatars/            # User avatars
│   └── payments/           # Payment verification screenshots
├── assets/
│   ├── css/app.css         # Glassmorphism dark mode styles & animations
│   ├── js/game.js          # Mines interactive game controller & Web Audio
│   ├── js/admin.js         # Admin table & approval workflows
│   ├── images/             # QR code & Favicon images
│   └── lib/confetti.min.js # Standalone canvas confetti
├── index.php               # Main Game SPA
├── login.php               # Sign In / Sign Up Page
├── dashboard.php           # User Account Overview
├── admin.php               # Admin Dashboard
├── goswami.php             # Admin route alias
├── install.php             # Diagnostic & Health Check tool
├── schema.sql              # MySQL Schema for phpMyAdmin
└── .htaccess               # Apache Rewrite and Security Rules
```
