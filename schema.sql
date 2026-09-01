-- Bomboclat Mines - MySQL Database Schema
-- Compatible with MySQL 5.7+ / MariaDB 10.2+ / InfinityFree Hosting

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Table: users
CREATE TABLE IF NOT EXISTS `users` (
  `id` VARCHAR(64) NOT NULL,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(191) DEFAULT '',
  `avatar_url` VARCHAR(255) DEFAULT '',
  `is_admin` TINYINT(1) DEFAULT 0,
  `is_banned` TINYINT(1) DEFAULT 0,
  `ban_reason` TEXT NULL,
  `is_promoter` TINYINT(1) DEFAULT 0,
  `custom_rtp` DECIMAL(5,2) DEFAULT NULL,
  `rig_mode` VARCHAR(32) DEFAULT 'global',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: users_balance
CREATE TABLE IF NOT EXISTS `users_balance` (
  `user_id` VARCHAR(64) NOT NULL,
  `balance` DECIMAL(12,2) NOT NULL DEFAULT 500.00,
  `topups` INT NOT NULL DEFAULT 0,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_balance_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: game_sessions
CREATE TABLE IF NOT EXISTS `game_sessions` (
  `id` VARCHAR(64) NOT NULL,
  `user_id` VARCHAR(64) NOT NULL,
  `bet_amount` DECIMAL(10,2) NOT NULL,
  `grid_size` INT NOT NULL,
  `bomb_count` INT NOT NULL,
  `tiles_data` MEDIUMTEXT NOT NULL,
  `current_winnings` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `state` ENUM('betting', 'playing', 'trapped', 'collected') NOT NULL DEFAULT 'playing',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_session_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: payments
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `user_id` VARCHAR(64) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `mobile_number` VARCHAR(30) NOT NULL,
  `utr_number` VARCHAR(100) NOT NULL,
  `screenshot_url` VARCHAR(255) DEFAULT '',
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_payments_created_at` (`created_at`),
  CONSTRAINT `fk_payments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: withdrawals
CREATE TABLE IF NOT EXISTS `withdrawals` (
  `id` VARCHAR(64) NOT NULL,
  `user_id` VARCHAR(64) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `upi_id` VARCHAR(191) NOT NULL,
  `mobile_number` VARCHAR(30) NOT NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `admin_note` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `processed_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_withdrawals_created_at` (`created_at`),
  CONSTRAINT `fk_withdrawals_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: system_settings
CREATE TABLE IF NOT EXISTS `system_settings` (
  `setting_key` VARCHAR(64) NOT NULL,
  `setting_value` TEXT NOT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: support_messages
CREATE TABLE IF NOT EXISTS `support_messages` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `user_id` VARCHAR(64) NOT NULL,
  `sender` ENUM('user', 'bot', 'admin') NOT NULL DEFAULT 'user',
  `message` TEXT NOT NULL,
  `is_escalated` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_support_user` (`user_id`, `created_at`),
  CONSTRAINT `fk_support_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Settings
INSERT IGNORE INTO `system_settings` (`setting_key`, `setting_value`) VALUES
('rtp_rate', '97.0'),
('house_edge', '3.0'),
('game_rig_mode', 'fair'),
('min_bet', '10'),
('max_bet', '10000'),
('min_withdrawal', '500'),
('welcome_bonus', '500'),
('announcement', 'Welcome to Bombaclat Mine! Instant 24/7 UPI Withdrawals.'),
('upi_id', 'bomboclat@upi');
