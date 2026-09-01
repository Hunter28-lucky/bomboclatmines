<?php
require_once __DIR__ . '/config.php';

// Database Configuration
// Note for InfinityFree: Replace these with your MySQL details from InfinityFree Control Panel
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'bomboclatmines');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_PORT', getenv('DB_PORT') ?: '3306');

// Database driver fallback ('mysql' or 'sqlite')
define('DB_DRIVER', getenv('DB_DRIVER') ?: 'auto');

/**
 * Returns a PDO database connection instance.
 * Automatically tries MySQL first, and falls back to SQLite if MySQL is not available.
 * Also automatically creates required tables if they do not exist.
 */
function getDB() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $driver = DB_DRIVER;

    // Try MySQL if driver is 'mysql' or 'auto'
    if ($driver === 'mysql' || $driver === 'auto') {
        try {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            initializeTables($pdo, 'mysql');
            return $pdo;
        } catch (PDOException $e) {
            // If MySQL fails and driver is 'auto', fall back to SQLite for local development
            if ($driver === 'auto') {
                $driver = 'sqlite';
            } else {
                throw new Exception("MySQL Connection Failed: " . $e->getMessage());
            }
        }
    }

    // SQLite fallback for standalone local execution / zero config
    if ($driver === 'sqlite') {
        try {
            $dbDir = BASE_DIR . '/data';
            if (!file_exists($dbDir)) {
                @mkdir($dbDir, 0755, true);
            }
            $sqlitePath = $dbDir . '/database.sqlite';
            $pdo = new PDO('sqlite:' . $sqlitePath, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            // Enable WAL mode & foreign keys for SQLite
            $pdo->exec('PRAGMA foreign_keys = ON;');
            $pdo->exec('PRAGMA journal_mode = WAL;');
            initializeTables($pdo, 'sqlite');
            return $pdo;
        } catch (PDOException $e) {
            throw new Exception("SQLite Connection Failed: " . $e->getMessage());
        }
    }

    throw new Exception("No suitable database driver found.");
}

/**
 * Initializes tables if not already present
 */
function initializeTables(PDO $pdo, string $driverType) {
    if ($driverType === 'mysql') {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id VARCHAR(64) PRIMARY KEY,
                email VARCHAR(191) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                full_name VARCHAR(191) DEFAULT '',
                avatar_url VARCHAR(255) DEFAULT '',
                is_admin TINYINT(1) DEFAULT 0,
                is_banned TINYINT(1) DEFAULT 0,
                ban_reason TEXT NULL,
                is_promoter TINYINT(1) DEFAULT 0,
                custom_rtp DECIMAL(5,2) DEFAULT NULL,
                rig_mode VARCHAR(32) DEFAULT 'global',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS users_balance (
                user_id VARCHAR(64) PRIMARY KEY,
                balance DECIMAL(12,2) NOT NULL DEFAULT 500.00,
                topups INT NOT NULL DEFAULT 0,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS game_sessions (
                id VARCHAR(64) PRIMARY KEY,
                user_id VARCHAR(64) NOT NULL,
                bet_amount DECIMAL(10,2) NOT NULL,
                grid_size INT NOT NULL,
                bomb_count INT NOT NULL,
                tiles_data MEDIUMTEXT NOT NULL,
                current_winnings DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                state ENUM('betting', 'playing', 'trapped', 'collected') NOT NULL DEFAULT 'playing',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS payments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(64) NOT NULL,
                amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                mobile_number VARCHAR(30) NOT NULL,
                utr_number VARCHAR(100) NOT NULL,
                screenshot_url VARCHAR(255) DEFAULT '',
                status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                reviewed_at DATETIME NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS withdrawals (
                id VARCHAR(64) PRIMARY KEY,
                user_id VARCHAR(64) NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                upi_id VARCHAR(191) NOT NULL,
                mobile_number VARCHAR(30) NOT NULL,
                status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
                admin_note TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                processed_at DATETIME NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS system_settings (
                setting_key VARCHAR(64) PRIMARY KEY,
                setting_value TEXT NOT NULL,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS support_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(64) NOT NULL,
                sender ENUM('user', 'bot', 'admin') NOT NULL DEFAULT 'user',
                message TEXT NOT NULL,
                is_escalated TINYINT(1) DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                KEY idx_support_user (user_id, created_at),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        try { $pdo->exec("ALTER TABLE payments ADD COLUMN amount DECIMAL(10,2) NOT NULL DEFAULT 0.00;"); } catch (Exception $e) {}
    } else {
        // SQLite Schema
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id TEXT PRIMARY KEY,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                full_name TEXT DEFAULT '',
                avatar_url TEXT DEFAULT '',
                is_admin INTEGER DEFAULT 0,
                is_banned INTEGER DEFAULT 0,
                ban_reason TEXT DEFAULT '',
                is_promoter INTEGER DEFAULT 0,
                custom_rtp NUMERIC DEFAULT NULL,
                rig_mode TEXT DEFAULT 'global',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS users_balance (
                user_id TEXT PRIMARY KEY,
                balance NUMERIC NOT NULL DEFAULT 500.00,
                topups INTEGER NOT NULL DEFAULT 0,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS game_sessions (
                id TEXT PRIMARY KEY,
                user_id TEXT NOT NULL,
                bet_amount NUMERIC NOT NULL,
                grid_size INTEGER NOT NULL,
                bomb_count INTEGER NOT NULL,
                tiles_data TEXT NOT NULL,
                current_winnings NUMERIC NOT NULL DEFAULT 0.00,
                state TEXT NOT NULL DEFAULT 'playing',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS payments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id TEXT NOT NULL,
                amount NUMERIC NOT NULL DEFAULT 0.00,
                mobile_number TEXT NOT NULL,
                utr_number TEXT NOT NULL,
                screenshot_url TEXT DEFAULT '',
                status TEXT NOT NULL DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                reviewed_at DATETIME NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS withdrawals (
                id TEXT PRIMARY KEY,
                user_id TEXT NOT NULL,
                amount NUMERIC NOT NULL,
                upi_id TEXT NOT NULL,
                mobile_number TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'pending',
                admin_note TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                processed_at DATETIME NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS system_settings (
                setting_key TEXT PRIMARY KEY,
                setting_value TEXT NOT NULL,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS support_messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id TEXT NOT NULL,
                sender TEXT NOT NULL DEFAULT 'user',
                message TEXT NOT NULL,
                is_escalated INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );
        ");

        // Add columns if migrating existing SQLite database
        try { $pdo->exec("ALTER TABLE users ADD COLUMN is_banned INTEGER DEFAULT 0;"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN ban_reason TEXT DEFAULT '';"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN custom_rtp NUMERIC DEFAULT NULL;"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN is_promoter INTEGER DEFAULT 0;"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE payments ADD COLUMN amount NUMERIC NOT NULL DEFAULT 0.00;"); } catch (Exception $e) {}
    }

    // Seed default settings if empty
    $defaultSettings = [
        'rtp_rate' => '97.0',
        'house_edge' => '3.0',
        'game_rig_mode' => 'fair', // 'fair', 'house_favored', 'strict'
        'min_bet' => '10',
        'max_bet' => '10000',
        'min_withdrawal' => '500',
        'welcome_bonus' => '500',
        'announcement' => 'Welcome to Bombaclat Mine! Enjoy 24/7 instant UPI withdrawals.',
        'upi_id' => 'bomboclat@upi'
    ];

    foreach ($defaultSettings as $key => $val) {
        $st = $pdo->prepare("SELECT setting_key FROM system_settings WHERE setting_key = :k");
        $st->execute([':k' => $key]);
        if (!$st->fetch()) {
            $ins = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (:k, :v)");
            $ins->execute([':k' => $key, ':v' => $val]);
        }
    }
}
