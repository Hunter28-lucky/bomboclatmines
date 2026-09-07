-- Add missing admin tables to the main database
-- Run this SQL to create the required admin functionality

-- Admin Activity Logs Table
CREATE TABLE IF NOT EXISTS `admin_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Admin Notifications Table
CREATE TABLE IF NOT EXISTS `admin_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Admin Reports Table
CREATE TABLE IF NOT EXISTS `admin_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_type` enum('user','transaction','bet','kyc','support') NOT NULL,
  `reference_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','in_progress','resolved','rejected') DEFAULT 'pending',
  `assigned_to` int(11) DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `report_type` (`report_type`),
  KEY `status` (`status`),
  KEY `assigned_to` (`assigned_to`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add admin_role column to users table if it doesn't exist
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `admin_role` enum('admin','super_admin','moderator') DEFAULT NULL;

-- Update existing admin users to have admin role
UPDATE `users` SET `admin_role` = 'super_admin' WHERE `is_admin` = 1 AND `admin_role` IS NULL;

-- Insert some sample admin activity logs if table is empty
INSERT IGNORE INTO `admin_activity_logs` (`admin_id`, `action`, `description`, `ip_address`, `user_agent`) VALUES
(1, 'login', 'Admin logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(1, 'view_dashboard', 'Viewed admin dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(1, 'view_users', 'Viewed users list', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

-- Insert some sample admin notifications if table is empty
INSERT IGNORE INTO `admin_notifications` (`admin_id`, `title`, `message`, `type`) VALUES
(1, 'Welcome to Admin Panel', 'Welcome to the Chicken Road admin panel. You can now manage users, transactions, and game settings.', 'info'),
(1, 'System Update', 'The admin panel has been updated with new features including reports and activity logs.', 'success'); 