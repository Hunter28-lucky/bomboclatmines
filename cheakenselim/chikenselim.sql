-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 08, 2025 at 07:36 AM
-- Server version: 10.6.22-MariaDB
-- PHP Version: 8.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `luckeywi_ch`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_logs`
--

CREATE TABLE `admin_activity_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_activity_logs`
--

INSERT INTO `admin_activity_logs` (`id`, `admin_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(0, 1, 'login', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-01 02:53:43'),
(0, 1, 'login', 'Admin logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-01 02:59:08'),
(0, 1, 'login', 'Admin logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-08-01 03:21:58'),
(0, 1, 'view_dashboard', 'Viewed admin dashboard', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-08-01 03:21:58'),
(0, 1, 'view_users', 'Viewed users list', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-08-01 03:21:58'),
(0, 1, 'setup_complete', 'Admin panel setup completed', '127.0.0.1', 'Setup Script', '2025-08-01 03:23:35'),
(0, 1, 'setup_complete', 'Admin panel setup completed', '127.0.0.1', 'Setup Script', '2025-08-01 03:26:06'),
(0, 2, 'login', 'Admin logged in successfully', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 11:57:28'),
(0, 2, 'approve_transaction', 'Approved transaction ID: 20', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 11:58:03'),
(0, 2, 'update_setting', 'Updated setting: site_title = Chicken Road Game', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: site_description = Best Chicken Road Game', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: min_bet = 10', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: max_bet = 10000', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: house_edge = 5', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: game_crash_min = 9.8', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: game_crash_max = 100', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: min_deposit = 100', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: max_deposit = 50000', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: min_withdrawal = 500', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: max_withdrawal = 100000', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: upi_id = admin@upi', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: qr_code = uploads/qr_code.png', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: bank_details = Bank: SBI\r\nAccount: 1234567890\r\nIFSC: SBIN0001234', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: signup_bonus = 0', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: referral_bonus = 100', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: support_whatsapp = +919876543210', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: support_email = support@chickengame.com', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: support_telegram = @chickengame_support', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: maintenance_mode = false', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: kyc_enabled = true', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: referral_enabled = true', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'update_setting', 'Updated setting: game_crash_enabled = true', '152.59.176.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 12:00:49'),
(0, 2, 'login', 'Admin logged in successfully', '152.58.156.125', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2025-08-02 13:52:32'),
(0, 2, 'login', 'Admin logged in successfully', '152.59.143.232', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/22G86 Safari/604.1', '2025-08-02 13:53:19'),
(0, 2, 'login', 'Admin logged in successfully', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:57:58'),
(0, 2, 'update_setting', 'Updated setting: site_title = Chicken Road Game', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: site_description = Best Chicken Road Game', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: min_bet = 10', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: max_bet = 10000', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: house_edge = 5', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: game_crash_min = 9.8', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: game_crash_max = 100', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: min_deposit = 100', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: max_deposit = 50000', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: min_withdrawal = 500', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: max_withdrawal = 100000', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: upi_id = admin@upi', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: qr_code = uploads/qr_code.png', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: bank_details = Bank: SBI\r\nAccount: 1234567898\r\nIFSC: SBIN0001234', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: signup_bonus = 0', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: referral_bonus = 100', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: support_whatsapp = +919876543210', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: support_email = support@chickengame.com', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: support_telegram = @chickengame_support', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: maintenance_mode = false', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: kyc_enabled = true', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: referral_enabled = true', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: game_crash_enabled = true', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:45'),
(0, 2, 'update_setting', 'Updated setting: site_title = Chicken Road Game', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: site_description = Best Chicken Road Game', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: min_bet = 10', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: max_bet = 10000', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: house_edge = 5', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: game_crash_min = 9.8', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: game_crash_max = 100', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: min_deposit = 100', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: max_deposit = 50000', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: min_withdrawal = 500', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: max_withdrawal = 100000', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: upi_id = admin@upi', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: qr_code = uploads/qr_code.png', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: bank_details = Bank: SBI\r\nAccount: 1234567898\r\nIFSC: SBIN0001234', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: signup_bonus = 0', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: referral_bonus = 100', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: support_whatsapp = +919876543210', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: support_email = support@chickengame.com', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: support_telegram = @chickengame_support', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: maintenance_mode = false', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: kyc_enabled = true', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: referral_enabled = true', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'update_setting', 'Updated setting: game_crash_enabled = true', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 13:58:50'),
(0, 2, 'logout', 'Admin logged out', '152.58.156.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:05:33'),
(0, 2, 'login', 'Admin logged in successfully', '89.187.180.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-08-02 14:11:37'),
(0, 1, 'login', 'Admin logged in successfully', '223.184.130.148', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:47:51'),
(0, 1, 'approve_transaction', 'Approved transaction ID: 21', '223.184.130.148', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:51:16'),
(0, 2, 'login', 'Admin logged in successfully', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:07'),
(0, 2, 'update_setting', 'Updated setting: site_title = Chicken Road Game Prince Dev', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: site_description = Best Chicken Road Game', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: min_bet = 10', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: max_bet = 10000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: house_edge = 5', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: game_crash_min = 9.8', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: game_crash_max = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: min_deposit = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: max_deposit = 50000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: min_withdrawal = 500', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: max_withdrawal = 100000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: upi_id = admin@upi', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: qr_code = uploads/qr_code.png', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: bank_details = Bank: SBI\r\nAccount: 1234567898\r\nIFSC: SBIN0001234', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: signup_bonus = 0', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: referral_bonus = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: support_whatsapp = +919876543210', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: support_email = support@chickengame.com', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: support_telegram = @chickengame_support', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: maintenance_mode = false', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: kyc_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: referral_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: game_crash_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:41'),
(0, 2, 'update_setting', 'Updated setting: site_title = Chicken Road Game ', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: site_description = Best Chicken Road Game', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: min_bet = 10', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: max_bet = 10000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: house_edge = 5', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: game_crash_min = 9.8', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: game_crash_max = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: min_deposit = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: max_deposit = 50000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: min_withdrawal = 500', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: max_withdrawal = 100000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: upi_id = admin@upi', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: qr_code = uploads/qr_code.png', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: bank_details = Bank: SBI\r\nAccount: 1234567898\r\nIFSC: SBIN0001234', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: signup_bonus = 0', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: referral_bonus = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: support_whatsapp = +919876543210', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: support_email = support@chickengame.com', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: support_telegram = @chickengame_support', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: maintenance_mode = false', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: kyc_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: referral_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: game_crash_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 14:53:52'),
(0, 2, 'update_setting', 'Updated setting: site_title = Chicken Road Game ', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: site_description = Best Chicken Road Game', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: min_bet = 10', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: max_bet = 10000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: house_edge = 5', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: game_crash_min = 9.8', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: game_crash_max = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: controlled_crash_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: controlled_crash_rectangle = 1', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: crash_level_easy = 3', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: crash_level_medium = random', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: crash_level_hard = random', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: crash_level_hardcore = random', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: min_deposit = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: max_deposit = 50000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: min_withdrawal = 500', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: max_withdrawal = 100000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: upi_id = admin@upi', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: qr_code = uploads/qr_code.png', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: bank_details = Bank: SBI\r\nAccount: 1234567898\r\nIFSC: SBIN0001234', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: signup_bonus = 0', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: referral_bonus = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: support_whatsapp = +919876543210', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: support_email = support@chickengame.com', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: support_telegram = @chickengame_support', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: maintenance_mode = false', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: kyc_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: referral_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: game_crash_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:06:32'),
(0, 2, 'update_setting', 'Updated setting: site_title = Chicken Road Game ', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: site_description = Best Chicken Road Game', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: min_bet = 10', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: max_bet = 10000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: house_edge = 5', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: game_crash_min = 9.8', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: game_crash_max = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: controlled_crash_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: controlled_crash_rectangle = 25', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: crash_level_easy = 3', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: crash_level_medium = random', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: crash_level_hard = random', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: crash_level_hardcore = random', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: min_deposit = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: max_deposit = 50000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: min_withdrawal = 500', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: max_withdrawal = 100000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: upi_id = admin@upi', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: qr_code = uploads/qr_code.png', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: bank_details = Bank: SBI\r\nAccount: 1234567898\r\nIFSC: SBIN0001234', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: signup_bonus = 0', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: referral_bonus = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: support_whatsapp = +919876543210', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: support_email = support@chickengame.com', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: support_telegram = @chickengame_support', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: maintenance_mode = false', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: kyc_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: referral_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: game_crash_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:07:12'),
(0, 2, 'update_setting', 'Updated setting: site_title = Chicken Road Game ', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: site_description = Best Chicken Road Game', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: min_bet = 10', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: max_bet = 10000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: house_edge = 5', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: game_crash_min = 9.8', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: game_crash_max = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: controlled_crash_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: controlled_crash_rectangle = 4', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: crash_level_easy = 4', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: crash_level_medium = random', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: crash_level_hard = random', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: crash_level_hardcore = random', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: min_deposit = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: max_deposit = 50000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: min_withdrawal = 500', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: max_withdrawal = 100000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: upi_id = admin@upi', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: qr_code = uploads/qr_code.png', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: bank_details = Bank: SBI\r\nAccount: 1234567898\r\nIFSC: SBIN0001234', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: signup_bonus = 0', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: referral_bonus = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: support_whatsapp = +919876543210', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: support_email = support@chickengame.com', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: support_telegram = @chickengame_support', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: maintenance_mode = false', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: kyc_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: referral_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: game_crash_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:08:09'),
(0, 2, 'update_setting', 'Updated setting: site_title = Chicken Road Game ', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: site_description = Best Chicken Road Game', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: min_bet = 10', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: max_bet = 10000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: house_edge = 5', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00');
INSERT INTO `admin_activity_logs` (`id`, `admin_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(0, 2, 'update_setting', 'Updated setting: game_crash_min = 9.8', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: game_crash_max = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: controlled_crash_enabled = false', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: controlled_crash_rectangle = 4', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: crash_level_easy = 4', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: crash_level_medium = random', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: crash_level_hard = random', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: crash_level_hardcore = random', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: min_deposit = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: max_deposit = 50000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: min_withdrawal = 500', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: max_withdrawal = 100000', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: upi_id = admin@upi', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: qr_code = uploads/qr_code.png', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: bank_details = Bank: SBI\r\nAccount: 1234567898\r\nIFSC: SBIN0001234', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: signup_bonus = 0', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: referral_bonus = 100', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: support_whatsapp = +919876543210', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: support_email = support@chickengame.com', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: support_telegram = @chickengame_support', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: maintenance_mode = false', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: kyc_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: referral_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'update_setting', 'Updated setting: game_crash_enabled = true', '152.59.177.75', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-02 15:09:00'),
(0, 2, 'login', 'Admin logged in successfully', '152.59.177.155', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', '2025-08-06 01:14:04'),
(0, 2, 'login', 'Admin logged in successfully', '152.58.157.215', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2025-08-06 04:46:59'),
(0, 2, 'login', 'Admin logged in successfully', '152.58.182.26', 'Mozilla/5.0 (Linux; Android 15; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.7204.169 Mobile Safari/537.36', '2025-08-06 09:39:36'),
(0, 2, 'login', 'Admin logged in successfully', '152.59.149.61', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2025-08-08 07:34:01');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `admin_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(0, 1, 'Welcome to Admin Panel', 'Welcome to the Chicken Road admin panel. You can now manage users, transactions, and game settings.', 'info', 0, '2025-08-01 03:21:58'),
(0, 1, 'System Update', 'The admin panel has been updated with new features including reports and activity logs.', 'success', 0, '2025-08-01 03:21:58'),
(0, 1, 'Setup Complete', 'Admin panel has been successfully set up with all required tables and features.', 'success', 0, '2025-08-01 03:23:35'),
(0, 1, 'Setup Complete', 'Admin panel has been successfully set up with all required tables and features.', 'success', 0, '2025-08-01 03:26:06');

-- --------------------------------------------------------

--
-- Table structure for table `admin_reports`
--

CREATE TABLE `admin_reports` (
  `id` int(11) NOT NULL,
  `report_type` enum('user','transaction','bet','kyc','support') NOT NULL,
  `reference_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','in_progress','resolved','rejected') DEFAULT 'pending',
  `assigned_to` int(11) DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_settings`
--

CREATE TABLE `admin_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json') DEFAULT 'text',
  `description` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_settings`
--

INSERT INTO `admin_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'site_title', 'Chicken Road Game ', 'text', 'Website title', 2, '2025-08-02 15:09:00'),
(2, 'site_description', 'Best Chicken Road Game', 'text', 'Website description', 2, '2025-08-02 15:09:00'),
(3, 'min_bet', '10', 'number', 'Minimum bet amount', 2, '2025-08-02 15:09:00'),
(4, 'max_bet', '10000', 'number', 'Maximum bet amount', 2, '2025-08-02 15:09:00'),
(5, 'min_deposit', '100', 'number', 'Minimum deposit amount', 2, '2025-08-02 15:09:00'),
(6, 'max_deposit', '50000', 'number', 'Maximum deposit amount', 2, '2025-08-02 15:09:00'),
(7, 'min_withdrawal', '500', 'number', 'Minimum withdrawal amount', 2, '2025-08-02 15:09:00'),
(8, 'max_withdrawal', '100000', 'number', 'Maximum withdrawal amount', 2, '2025-08-02 15:09:00'),
(9, 'referral_bonus', '100', 'number', 'Referral bonus amount', 2, '2025-08-02 15:09:00'),
(10, 'signup_bonus', '0', 'number', 'Signup bonus amount', 2, '2025-08-02 15:09:00'),
(11, 'maintenance_mode', 'false', 'boolean', 'Maintenance mode status', 2, '2025-08-02 15:09:00'),
(12, 'kyc_enabled', 'true', 'boolean', 'KYC verification required', 2, '2025-08-02 15:09:00'),
(13, 'referral_enabled', 'true', 'boolean', 'Referral system enabled', 2, '2025-08-02 15:09:00'),
(14, 'upi_id', 'admin@upi', 'text', 'UPI ID for payments', 2, '2025-08-02 15:09:00'),
(15, 'qr_code', 'uploads/qr_code.png', 'text', 'QR code image path', 2, '2025-08-02 15:09:00'),
(16, 'bank_details', 'Bank: SBI\r\nAccount: 1234567898\r\nIFSC: SBIN0001234', 'text', 'Bank account details', 2, '2025-08-02 15:09:00'),
(17, 'support_whatsapp', '+919876543210', 'text', 'WhatsApp support number', 2, '2025-08-02 15:09:00'),
(18, 'support_email', 'support@chickengame.com', 'text', 'Support email address', 2, '2025-08-02 15:09:00'),
(19, 'support_telegram', '@chickengame_support', 'text', 'Telegram support link', 2, '2025-08-02 15:09:00'),
(20, 'game_crash_enabled', 'true', 'boolean', 'Crash game enabled', 2, '2025-08-02 15:09:00'),
(21, 'game_crash_min', '9.8', 'number', 'Minimum crash multiplier', 2, '2025-08-02 15:09:00'),
(22, 'game_crash_max', '100', 'number', 'Maximum crash multiplier', 2, '2025-08-02 15:09:00'),
(23, 'house_edge', '5', 'number', 'House edge percentage', 2, '2025-08-02 15:09:00'),
(0, 'crash_level_easy', '4', 'text', NULL, 2, '2025-08-02 15:09:00'),
(0, 'crash_level_medium', 'random', 'text', NULL, 2, '2025-08-02 15:09:00'),
(0, 'crash_level_hard', 'random', 'text', NULL, 2, '2025-08-02 15:09:00'),
(0, 'crash_level_hardcore', 'random', 'text', NULL, 2, '2025-08-02 15:09:00'),
(0, 'controlled_crash_enabled', 'false', 'text', NULL, 2, '2025-08-02 15:09:00'),
(0, 'controlled_crash_rectangle', '4', 'text', NULL, 2, '2025-08-02 15:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','moderator') DEFAULT 'admin',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `email`, `password`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'admin@chickengame.com', 'admin123', 'super_admin', 'active', '2025-08-02 14:47:51', '2025-01-01 00:00:00', '2025-08-02 14:47:51'),
(2, 'admin', 'admin@chickengame.com', 'admin123', 'admin', 'active', '2025-08-08 07:34:01', '2025-01-01 00:00:00', '2025-08-08 07:34:01');

-- --------------------------------------------------------

--
-- Table structure for table `bet_history`
--

CREATE TABLE `bet_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bet_amount` decimal(10,2) NOT NULL,
  `difficulty` varchar(20) DEFAULT 'Normal',
  `result` enum('cashout','crash') NOT NULL,
  `multiplier` decimal(10,4) NOT NULL,
  `profit_loss` decimal(10,2) NOT NULL,
  `game_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`game_data`)),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bet_history`
--

INSERT INTO `bet_history` (`id`, `user_id`, `bet_amount`, `difficulty`, `result`, `multiplier`, `profit_loss`, `game_data`, `created_at`) VALUES
(1, 4, 500.00, 'Easy', 'cashout', 1.1200, 60.00, 'null', '2025-06-28 14:19:58'),
(2, 4, 0.60, 'Easy', 'cashout', 1.0700, 0.04, 'null', '2025-06-28 14:26:31'),
(3, 4, 0.60, 'Easy', 'cashout', 1.0300, 0.02, 'null', '2025-06-28 14:26:31'),
(4, 4, 0.60, 'Easy', 'cashout', 1.2300, 0.14, 'null', '2025-06-28 14:26:31'),
(5, 4, 0.60, 'Easy', 'crash', 0.0000, -0.60, 'null', '2025-06-28 14:26:31'),
(6, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 14:29:11'),
(7, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 14:37:01'),
(8, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 14:37:05'),
(9, 4, 500.00, 'Easy', 'cashout', 1.0300, 15.00, 'null', '2025-06-28 14:37:07'),
(10, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 14:59:10'),
(11, 4, 500.00, 'Easy', 'cashout', 1.1700, 85.00, 'null', '2025-06-28 14:59:13'),
(12, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:03:35'),
(13, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:03:37'),
(14, 4, 500.00, 'Easy', 'cashout', 1.2300, 115.00, 'null', '2025-06-28 15:03:42'),
(15, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:03:51'),
(16, 4, 500.00, 'Easy', 'cashout', 1.0700, 35.00, 'null', '2025-06-28 15:04:36'),
(17, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:05:22'),
(18, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:05:30'),
(19, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:05:34'),
(20, 4, 500.00, 'Easy', 'cashout', 1.0700, 35.00, 'null', '2025-06-28 15:07:19'),
(21, 4, 500.00, 'Hardcore', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:07:49'),
(22, 4, 500.00, 'Easy', 'cashout', 1.0300, 15.00, 'null', '2025-06-28 15:08:58'),
(23, 4, 500.00, 'Easy', 'cashout', 1.0700, 35.00, 'null', '2025-06-28 15:09:04'),
(24, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:09:16'),
(25, 4, 500.00, 'Hardcore', 'cashout', 2.8000, 900.00, 'null', '2025-06-28 15:09:24'),
(26, 4, 5000.00, 'Hardcore', 'cashout', 2.8000, 9000.00, 'null', '2025-06-28 15:10:08'),
(27, 4, 10000.00, 'Hardcore', 'crash', 0.0000, -10000.00, 'null', '2025-06-28 15:10:26'),
(28, 4, 5006.00, 'Easy', 'crash', 0.0000, -5006.00, 'null', '2025-06-28 15:10:57'),
(29, 4, 500.00, 'Easy', 'cashout', 1.0700, 35.00, 'null', '2025-06-28 15:22:30'),
(30, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:22:32'),
(31, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:22:37'),
(32, 4, 500.00, 'Easy', 'cashout', 1.0700, 35.00, 'null', '2025-06-28 15:22:40'),
(33, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:22:51'),
(34, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:22:58'),
(35, 4, 500.00, 'Easy', 'cashout', 1.0700, 35.00, 'null', '2025-06-28 15:23:10'),
(36, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:29:53'),
(37, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:29:56'),
(38, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:29:58'),
(39, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:30:01'),
(40, 4, 500.00, 'Easy', 'cashout', 1.0300, 15.00, 'null', '2025-06-28 15:30:25'),
(41, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:30:27'),
(42, 4, 500.00, 'Easy', 'cashout', 1.0300, 15.00, 'null', '2025-06-28 15:30:30'),
(43, 4, 500.00, 'Easy', 'cashout', 1.0700, 35.00, 'null', '2025-06-28 15:30:33'),
(44, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:30:35'),
(45, 4, 500.00, 'Easy', 'cashout', 1.0700, 35.00, 'null', '2025-06-28 15:30:59'),
(46, 4, 10000.00, 'Easy', 'cashout', 1.0300, 300.00, 'null', '2025-06-28 15:31:05'),
(47, 4, 10000.00, 'Easy', 'crash', 0.0000, -10000.00, 'null', '2025-06-28 15:32:06'),
(48, 4, 10000.00, 'Easy', 'cashout', 1.0300, 300.00, 'null', '2025-06-28 15:32:08'),
(49, 4, 10000.00, 'Easy', 'crash', 0.0000, -10000.00, 'null', '2025-06-28 15:32:16'),
(50, 4, 10000.00, 'Easy', 'cashout', 1.0700, 700.00, 'null', '2025-06-28 15:32:44'),
(51, 4, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:33:19'),
(52, 4, 500.00, 'Easy', 'cashout', 1.0300, 15.00, 'null', '2025-06-28 15:33:27'),
(53, 4, 500.00, 'Easy', 'cashout', 1.0300, 15.00, 'null', '2025-06-28 15:34:31'),
(54, 4, 500.00, 'Easy', 'cashout', 1.0300, 15.00, 'null', '2025-06-28 15:34:34'),
(55, 4, 500.00, 'Easy', 'cashout', 1.0300, 515.00, 'null', '2025-06-28 15:36:24'),
(56, 4, 500.00, 'Easy', 'cashout', 1.1200, 560.00, 'null', '2025-06-28 15:36:30'),
(57, 4, 500.00, 'Easy', 'cashout', 1.0300, 515.00, 'null', '2025-06-28 15:36:51'),
(58, 4, 500.00, 'Hardcore', 'cashout', 1.6300, 815.00, 'null', '2025-06-28 15:36:58'),
(59, 5, 500.00, 'Easy', 'cashout', 1.0300, 515.00, 'null', '2025-06-28 15:44:25'),
(60, 5, 500.00, 'Easy', 'crash', 0.0000, -500.00, 'null', '2025-06-28 15:44:27'),
(61, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 15:47:53'),
(62, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 15:47:57'),
(63, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 15:48:02'),
(64, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 15:48:11'),
(65, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 15:48:18'),
(66, 4, 1000.00, 'Easy', 'crash', 0.0000, -1000.00, 'null', '2025-06-28 15:48:40'),
(67, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 15:50:26'),
(68, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 15:51:30'),
(69, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 15:51:56'),
(70, 4, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-28 15:52:02'),
(71, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 15:52:19'),
(72, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 15:52:34'),
(73, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-28 15:52:41'),
(74, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 15:53:38'),
(75, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 15:53:44'),
(76, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 15:53:46'),
(77, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 15:53:51'),
(78, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 15:53:54'),
(79, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 15:54:56'),
(80, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-28 15:55:06'),
(81, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 15:55:15'),
(82, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-28 15:55:24'),
(83, 4, 10.00, 'Easy', 'cashout', 1.0300, 20.30, 'null', '2025-06-28 15:59:38'),
(84, 4, 10.00, 'Easy', 'cashout', 1.0300, 20.30, 'null', '2025-06-28 15:59:43'),
(85, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 16:00:19'),
(86, 4, 10.00, 'Easy', 'cashout', 1.0300, 20.30, 'null', '2025-06-28 16:00:27'),
(87, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 16:02:06'),
(88, 4, 10.00, 'Easy', 'cashout', 1.0300, 20.30, 'null', '2025-06-28 16:02:10'),
(89, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 16:04:05'),
(90, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 16:04:07'),
(91, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-28 16:04:13'),
(92, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 16:04:40'),
(93, 4, 10.00, 'Easy', 'crash', 0.0000, -10.00, 'null', '2025-06-28 16:04:57'),
(94, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-28 16:05:01'),
(95, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 16:07:43'),
(96, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 16:07:51'),
(97, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 16:10:15'),
(98, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 16:11:41'),
(99, 4, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 16:11:46'),
(100, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 16:11:50'),
(101, 4, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 16:12:18'),
(102, 4, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 16:20:31'),
(103, 4, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 16:20:35'),
(104, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 16:22:11'),
(105, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 16:22:14'),
(106, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 16:22:22'),
(107, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 16:33:09'),
(108, 4, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-28 16:40:49'),
(109, 4, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 16:40:56'),
(110, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-28 16:41:21'),
(111, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-28 16:41:30'),
(112, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-28 16:41:39'),
(113, 4, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-28 16:51:36'),
(114, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-28 16:53:25'),
(115, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:01:50'),
(116, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:01:52'),
(117, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:01:53'),
(118, 2, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:01:57'),
(119, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:01:59'),
(120, 2, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:02:00'),
(121, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:42:33'),
(122, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:42:34'),
(123, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:42:35'),
(124, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:43:03'),
(125, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:43:06'),
(126, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:43:08'),
(127, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:43:11'),
(128, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:43:12'),
(129, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:43:14'),
(130, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:43:16'),
(131, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:43:16'),
(132, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:43:17'),
(133, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:43:18'),
(134, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:43:26'),
(135, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:43:51'),
(136, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:44:01'),
(137, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:44:19'),
(138, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:44:59'),
(139, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:45:01'),
(140, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:45:02'),
(141, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:45:04'),
(142, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:45:06'),
(143, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:45:08'),
(144, 2, 10000.00, 'Easy', 'cashout', 1.1700, 11700.00, 'null', '2025-06-28 17:46:02'),
(145, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:46:10'),
(146, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:46:16'),
(147, 2, 10000.00, 'Easy', 'cashout', 1.1700, 11700.00, 'null', '2025-06-28 17:46:21'),
(148, 2, 10000.00, 'Hardcore', 'cashout', 9.0800, 90800.00, 'null', '2025-06-28 17:46:37'),
(149, 2, 10000.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:46:44'),
(150, 2, 10000.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:46:53'),
(151, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:51:48'),
(152, 2, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-28 17:52:00'),
(153, 3, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 03:32:29'),
(154, 3, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 03:32:36'),
(155, 3, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-29 03:32:50'),
(156, 3, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-06-29 03:33:03'),
(157, 3, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 03:34:52'),
(158, 2, 50.00, 'Easy', 'cashout', 1.0700, 53.50, 'null', '2025-06-29 03:36:23'),
(159, 3, 50.00, 'Easy', 'cashout', 1.1200, 56.00, 'null', '2025-06-29 03:36:42'),
(160, 3, 50.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 03:40:05'),
(161, 3, 50.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 03:40:09'),
(162, 3, 50.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 03:40:58'),
(163, 3, 50.00, 'Easy', 'cashout', 1.1700, 58.50, 'null', '2025-06-29 03:41:05'),
(164, 3, 50.00, 'Easy', 'cashout', 1.1200, 56.00, 'null', '2025-06-29 03:41:10'),
(165, 3, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-06-29 03:54:17'),
(166, 3, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 03:56:15'),
(167, 3, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 03:56:22'),
(168, 3, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 03:56:24'),
(169, 3, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 03:56:26'),
(170, 3, 10.00, 'Hardcore', 'cashout', 1.6300, 16.30, 'null', '2025-06-29 03:56:31'),
(171, 3, 10.00, 'Hardcore', 'cashout', 1.6300, 16.30, 'null', '2025-06-29 03:56:34'),
(172, 3, 10.00, 'Hardcore', 'cashout', 1.6300, 16.30, 'null', '2025-06-29 03:56:37'),
(173, 3, 10.00, 'Hardcore', 'cashout', 1.6300, 16.30, 'null', '2025-06-29 03:56:39'),
(174, 3, 10.00, 'Hardcore', 'cashout', 1.6300, 16.30, 'null', '2025-06-29 03:56:41'),
(175, 3, 10.00, 'Hardcore', 'cashout', 1.6300, 16.30, 'null', '2025-06-29 03:56:43'),
(176, 3, 200.00, 'Hardcore', 'cashout', 1.6300, 326.00, 'null', '2025-06-29 03:56:47'),
(177, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 05:20:42'),
(178, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 05:20:47'),
(179, 4, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-06-29 07:59:25'),
(180, 4, 10.00, 'Hardcore', 'cashout', 1.6300, 16.30, 'null', '2025-06-29 07:59:29'),
(181, 1, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-06-29 08:04:06'),
(182, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:05:37'),
(183, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:05:46'),
(184, 3, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:08:52'),
(185, 3, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:09:01'),
(186, 3, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:09:07'),
(187, 3, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 08:09:15'),
(188, 3, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:09:30'),
(189, 3, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:09:36'),
(190, 3, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-06-29 08:10:01'),
(191, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:17:25'),
(192, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:17:33'),
(193, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:17:41'),
(194, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:17:48'),
(195, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:17:54'),
(196, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:18:00'),
(197, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:19:07'),
(198, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:21:19'),
(199, 5, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-06-29 08:21:23'),
(200, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:21:30'),
(201, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:21:38'),
(202, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:21:46'),
(203, 5, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-29 08:21:50'),
(204, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:21:58'),
(205, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:22:23'),
(206, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:24:11'),
(207, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:24:15'),
(208, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:24:19'),
(209, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:24:25'),
(210, 2, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:24:37'),
(211, 5, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 08:26:14'),
(212, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:26:39'),
(213, 5, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 08:26:42'),
(214, 2, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 08:26:55'),
(215, 2, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 08:28:41'),
(216, 2, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 08:28:45'),
(217, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:29:04'),
(218, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 08:30:57'),
(219, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:31:02'),
(220, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:31:44'),
(221, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:31:49'),
(222, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:31:56'),
(223, 2, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:34:15'),
(224, 2, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 08:34:27'),
(225, 2, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:34:37'),
(226, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:35:50'),
(227, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:35:54'),
(228, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:35:58'),
(229, 3, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:36:14'),
(230, 3, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:36:18'),
(231, 3, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:36:23'),
(232, 3, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:36:31'),
(233, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:37:37'),
(234, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:37:42'),
(235, 2, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:37:58'),
(236, 2, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:40:18'),
(237, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:40:29'),
(238, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:40:34'),
(239, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:40:40'),
(240, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:40:45'),
(241, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:40:52'),
(242, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 08:42:39'),
(243, 3, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 08:43:54'),
(244, 5, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-06-29 10:25:22'),
(245, 5, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-06-29 10:25:28'),
(246, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 10:26:47'),
(247, 5, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-06-29 10:26:54'),
(248, 5, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-29 10:27:10'),
(249, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 10:27:18'),
(250, 5, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-29 10:27:35'),
(251, 5, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-29 10:27:42'),
(252, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 10:29:50'),
(253, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 10:30:00'),
(254, 5, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 10:30:07'),
(255, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 10:32:23'),
(256, 2, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 10:36:42'),
(257, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 10:40:14'),
(258, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 10:41:46'),
(259, 5, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 10:42:22'),
(260, 5, 0.60, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 10:48:53'),
(261, 5, 0.60, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 10:48:58'),
(262, 5, 0.60, 'Hardcore', 'cashout', 1.6300, 0.98, 'null', '2025-06-29 10:49:04'),
(263, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 10:58:08'),
(264, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 10:58:13'),
(265, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 11:00:08'),
(266, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 11:00:13'),
(267, 5, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-06-29 11:06:46'),
(268, 5, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 11:08:57'),
(269, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 11:10:34'),
(270, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 11:10:43'),
(271, 5, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-29 11:14:00'),
(272, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 11:14:55'),
(273, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 11:15:46'),
(274, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 11:44:02'),
(275, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 12:17:25'),
(276, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 12:17:37'),
(277, 4, 100.00, 'Easy', 'cashout', 1.0700, 107.00, 'null', '2025-06-29 12:17:51'),
(278, 4, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 12:18:15'),
(279, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 12:18:27'),
(280, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 13:00:58'),
(281, 4, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-29 13:12:56'),
(282, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 13:20:54'),
(283, 1, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-06-29 13:21:01'),
(284, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 13:21:07'),
(285, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 13:37:46'),
(286, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 13:38:38'),
(287, 2, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 14:11:11'),
(288, 1, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 15:57:09'),
(289, 1, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 15:57:13'),
(290, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 16:09:52'),
(291, 1, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-06-29 16:10:39'),
(292, 4, 50.00, 'Easy', 'cashout', 1.1700, 58.50, 'null', '2025-06-29 17:21:17'),
(293, 4, 50.00, 'Easy', 'cashout', 1.0300, 51.50, 'null', '2025-06-29 17:21:44'),
(294, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 17:21:50'),
(295, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 17:21:54'),
(296, 4, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 17:22:13'),
(297, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 17:23:02'),
(298, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 17:35:56'),
(299, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-29 17:36:07'),
(300, 1, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 17:36:09'),
(301, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-29 17:37:51'),
(302, 2, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-29 17:41:11'),
(303, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 03:59:39'),
(304, 1, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-30 03:59:46'),
(305, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 04:18:03'),
(306, 1, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-30 04:18:06'),
(307, 1, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-30 04:19:03'),
(308, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-30 04:22:28'),
(309, 1, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-30 04:22:28'),
(310, 1, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-30 04:33:48'),
(311, 1, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-30 04:42:45'),
(312, 5, 500.00, 'Easy', 'cashout', 1.0700, 535.00, 'null', '2025-06-30 07:07:04'),
(313, 5, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-06-30 07:07:14'),
(314, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 07:07:31'),
(315, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 07:07:51'),
(316, 1, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 07:09:43'),
(317, 1, 10.00, 'Hard', 'crash', 0.0000, 0.00, 'null', '2025-06-30 07:14:21'),
(318, 1, 10.00, 'Hard', 'cashout', 1.2300, 12.30, 'null', '2025-06-30 07:14:30'),
(319, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 07:40:08'),
(320, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 07:41:50'),
(321, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 07:41:51'),
(322, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 07:41:55'),
(323, 1, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-30 07:55:29'),
(324, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 07:58:52'),
(325, 1, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-30 08:00:00'),
(326, 1, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-30 08:02:54'),
(327, 1, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-30 08:05:35'),
(328, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 09:33:45'),
(329, 1, 100.00, 'Easy', 'cashout', 1.1200, 112.00, 'null', '2025-06-30 09:53:56'),
(330, 1, 100.00, 'Hardcore', 'cashout', 2.8000, 280.00, 'null', '2025-06-30 09:54:02'),
(331, 1, 100.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-06-30 09:54:10'),
(332, 1, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-30 11:07:01'),
(333, 1, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-06-30 14:10:40'),
(334, 1, 10.00, 'Hardcore', 'cashout', 1.6300, 16.30, 'null', '2025-06-30 14:10:52'),
(335, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 14:14:22'),
(336, 5, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-30 14:14:33'),
(337, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 14:16:17'),
(338, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 14:17:27'),
(339, 5, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-30 14:17:30'),
(340, 2, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 14:54:13'),
(341, 2, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 14:54:18'),
(342, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 15:18:41'),
(343, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-30 15:45:27'),
(344, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 15:45:33'),
(345, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 16:47:04'),
(346, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 16:58:35'),
(347, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 16:58:39'),
(348, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 16:58:45'),
(349, 5, 200.00, 'Easy', 'cashout', 1.0300, 206.00, 'null', '2025-06-30 17:05:55'),
(350, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 17:21:59'),
(351, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 17:22:05'),
(352, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 17:25:16'),
(353, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 17:25:21'),
(354, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 17:25:29'),
(355, 5, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-30 17:25:32'),
(356, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 17:26:15'),
(357, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 17:30:55'),
(358, 5, 10.00, 'Easy', 'cashout', 1.2300, 12.30, 'null', '2025-06-30 17:32:09'),
(359, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 17:36:37'),
(360, 5, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 17:36:43'),
(361, 5, 50.00, 'Easy', 'cashout', 1.1700, 58.50, 'null', '2025-06-30 17:36:50'),
(362, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 17:46:31'),
(363, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 17:46:36'),
(364, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 17:46:42'),
(365, 1, 500.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-06-30 17:46:47'),
(366, 1, 500.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-06-30 17:47:40'),
(367, 1, 500.00, 'Hardcore', 'cashout', 9.0800, 4540.00, 'null', '2025-06-30 17:47:46'),
(368, 1, 500.00, 'Hardcore', 'cashout', 9.0800, 4540.00, 'null', '2025-06-30 17:47:53'),
(369, 1, 10000.00, 'Hardcore', 'cashout', 9.0800, 90800.00, 'null', '2025-06-30 17:48:13'),
(370, 9, 50.00, 'Easy', 'cashout', 1.2300, 61.50, 'null', '2025-06-30 18:33:39'),
(371, 9, 50.00, 'Easy', 'cashout', 1.0300, 51.50, 'null', '2025-06-30 18:33:44'),
(372, 9, 200.00, 'Easy', 'cashout', 1.2300, 246.00, 'null', '2025-06-30 18:33:59'),
(373, 9, 500.00, 'Easy', 'cashout', 1.1200, 560.00, 'null', '2025-06-30 18:34:06'),
(374, 5, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-06-30 18:55:09'),
(375, 5, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-06-30 18:55:31'),
(376, 5, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-06-30 18:55:36'),
(377, 3, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-07-01 03:42:25'),
(378, 1, 10.00, 'Easy', 'cashout', 1.2300, 12.30, 'null', '2025-07-01 03:53:07'),
(379, 1, 10000.00, 'Easy', 'cashout', 1.1200, 11200.00, 'null', '2025-07-01 03:53:17'),
(380, 1, 200.00, 'Easy', 'cashout', 1.4400, 288.00, 'null', '2025-07-01 03:54:14'),
(381, 1, 500.00, 'Easy', 'cashout', 1.4400, 720.00, 'null', '2025-07-01 03:54:31'),
(382, 1, 10.00, 'Hardcore', 'cashout', 4.9500, 49.50, 'null', '2025-07-01 03:56:16'),
(383, 4, 10.00, 'Easy', 'cashout', 1.2300, 12.30, 'null', '2025-07-01 03:56:30'),
(384, 1, 100.00, 'Easy', 'cashout', 1.2300, 123.00, 'null', '2025-07-01 03:56:43'),
(385, 4, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-07-01 03:56:46'),
(386, 4, 10.00, 'Easy', 'cashout', 1.2300, 12.30, 'null', '2025-07-01 03:57:06'),
(387, 4, 10.00, 'Easy', 'cashout', 1.2300, 12.30, 'null', '2025-07-01 03:57:58'),
(388, 4, 10.00, 'Easy', 'cashout', 1.2300, 12.30, 'null', '2025-07-01 03:58:47'),
(389, 1, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-07-01 03:58:48'),
(390, 1, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-07-01 03:58:53'),
(391, 1, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-07-01 03:58:59'),
(392, 1, 200.00, 'Easy', 'cashout', 9.8100, 1962.00, 'null', '2025-07-01 03:59:46'),
(393, 8, 10.00, 'Easy', 'cashout', 9.8100, 98.10, 'null', '2025-07-01 03:59:58'),
(394, 8, 10.00, 'Easy', 'cashout', 1.2300, 12.30, 'null', '2025-07-01 04:00:49'),
(395, 4, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-07-01 04:01:46'),
(396, 4, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-07-01 04:01:50'),
(397, 4, 10.00, 'Easy', 'cashout', 1.2900, 12.90, 'null', '2025-07-01 04:04:16'),
(398, 4, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-07-01 04:06:18'),
(399, 4, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:07:54'),
(400, 4, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:07:59'),
(401, 1, 100.00, 'Easy', 'cashout', 1.0300, 103.00, 'null', '2025-07-01 04:10:02'),
(402, 1, 100.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:10:13'),
(403, 1, 100.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:10:22'),
(404, 1, 100.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:10:31'),
(405, 4, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:13:04'),
(406, 4, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:13:22'),
(407, 10, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-07-01 04:45:16'),
(408, 10, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-07-01 04:45:25'),
(409, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:45:54'),
(410, 10, 100.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:45:58'),
(411, 10, 100.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:46:04'),
(412, 10, 100.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:46:07'),
(413, 10, 100.00, 'Easy', 'cashout', 1.1200, 112.00, 'null', '2025-07-01 04:46:11'),
(414, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:50:28'),
(415, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:56:32'),
(416, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:56:35'),
(417, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:56:36'),
(418, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:56:48'),
(419, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:57:05'),
(420, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:57:24'),
(421, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 04:57:44'),
(422, 10, 10.00, 'Easy', 'cashout', 1.2300, 12.30, 'null', '2025-07-01 05:02:08'),
(423, 10, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-07-01 05:02:10'),
(424, 10, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-07-01 05:02:11'),
(425, 10, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-07-01 05:16:55'),
(426, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 05:16:57'),
(427, 10, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-07-01 05:16:59'),
(428, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 05:17:44'),
(429, 10, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-07-01 05:30:15'),
(430, 10, 0.60, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 05:30:17'),
(431, 10, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-07-01 05:30:24'),
(432, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 05:30:31'),
(433, 10, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-07-01 05:30:33'),
(434, 8, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-07-01 05:37:07'),
(435, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 05:37:11'),
(436, 10, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-07-01 05:48:21'),
(437, 1, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-07-01 06:07:06'),
(438, 1, 500.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:07:11'),
(439, 1, 500.00, 'Easy', 'cashout', 1.2900, 645.00, 'null', '2025-07-01 06:07:25'),
(440, 1, 500.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:07:41'),
(441, 1, 500.00, 'Hardcore', 'cashout', 4.9500, 2475.00, 'null', '2025-07-01 06:07:51'),
(442, 1, 500.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:02'),
(443, 1, 500.00, 'Hardcore', 'cashout', 890.1900, 445095.00, 'null', '2025-07-01 06:08:12'),
(444, 1, 10000.00, 'Medium', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:21'),
(445, 1, 10000.00, 'Medium', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:22'),
(446, 1, 10000.00, 'Medium', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:23'),
(447, 1, 10000.00, 'Medium', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:26'),
(448, 1, 10000.00, 'Medium', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:28'),
(449, 1, 10000.00, 'Medium', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:29'),
(450, 1, 10000.00, 'Medium', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:31'),
(451, 1, 10000.00, 'Medium', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:33'),
(452, 1, 10000.00, 'Medium', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:37'),
(453, 1, 10000.00, 'Medium', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:38'),
(454, 1, 10000.00, 'Medium', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:40'),
(455, 1, 500.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:47'),
(456, 1, 500.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:50'),
(457, 1, 500.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:53'),
(458, 1, 500.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:57'),
(459, 1, 500.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:08:59'),
(460, 1, 10000.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:09:03'),
(461, 1, 10000.00, 'Hardcore', 'cashout', 62.9600, 629600.00, 'null', '2025-07-01 06:09:10'),
(462, 1, 10000.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:09:17'),
(463, 1, 10000.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:09:22'),
(464, 10, 10.00, 'Hardcore', 'cashout', 1.6300, 16.30, 'null', '2025-07-01 06:14:34'),
(465, 10, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 06:14:39'),
(466, 10, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:20:55'),
(467, 10, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:20:58'),
(468, 10, 10000.00, 'Easy', 'cashout', 1.1200, 11200.00, 'null', '2025-07-01 09:21:04'),
(469, 10, 10000.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:21:14'),
(470, 10, 10000.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:21:20'),
(471, 10, 10000.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:21:28'),
(472, 10, 10000.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:21:37'),
(473, 10, 10000.00, 'Hardcore', 'cashout', 2643.8900, 26438900.00, 'null', '2025-07-01 09:21:50'),
(474, 10, 10000.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:21:54'),
(475, 8, 0.60, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:40:32'),
(476, 8, 0.60, 'Easy', 'cashout', 1.0700, 0.64, 'null', '2025-07-01 09:40:35'),
(477, 8, 0.60, 'Easy', 'cashout', 1.0700, 0.64, 'null', '2025-07-01 09:40:50'),
(478, 8, 0.60, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:41:00'),
(479, 8, 0.60, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:41:05'),
(480, 8, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:45:54'),
(481, 8, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:45:57'),
(482, 8, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:45:58'),
(483, 8, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:51:32'),
(484, 8, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:51:36'),
(485, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:52:39'),
(486, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:52:42'),
(487, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:52:45'),
(488, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:52:48'),
(489, 14, 0.60, 'Easy', 'cashout', 1.8800, 1.13, 'null', '2025-07-01 09:53:41'),
(490, 8, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:53:43'),
(491, 8, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:53:50'),
(492, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:53:53'),
(493, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:53:56'),
(494, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:54:02'),
(495, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:57:46'),
(496, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:57:52'),
(497, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:58:50'),
(498, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:58:52'),
(499, 8, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:59:28'),
(500, 8, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 09:59:31'),
(501, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:01:43'),
(502, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:01:46'),
(503, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:02:12'),
(504, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:03:37'),
(505, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:03:55'),
(506, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:04:05'),
(507, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:04:13'),
(508, 1, 0.60, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:05:32'),
(509, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:05:52'),
(510, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:07:33'),
(511, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:07:42'),
(512, 1, 0.60, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:08:37'),
(513, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:08:47'),
(514, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:08:58'),
(515, 14, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:09:13'),
(516, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:10:42'),
(517, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:10:53'),
(518, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:12:41'),
(519, 10, 0.60, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:13:41'),
(520, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:18:06'),
(521, 10, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-07-01 10:18:24'),
(522, 10, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-07-01 10:18:28'),
(523, 10, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-07-01 10:18:35'),
(524, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:49:19'),
(525, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:49:30'),
(526, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 10:50:02'),
(527, 10, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-07-01 10:50:06'),
(528, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 11:25:02'),
(529, 10, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-07-01 11:28:27'),
(530, 10, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-07-01 11:29:49'),
(531, 16, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 11:59:01'),
(532, 16, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-08-02 11:59:10'),
(533, 16, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 11:59:16'),
(534, 16, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 11:59:18'),
(535, 16, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 11:59:18'),
(536, 16, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 11:59:20'),
(537, 16, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 11:59:21'),
(538, 1, 10.00, 'Easy', 'cashout', 1.1700, 11.70, 'null', '2025-08-02 12:24:27'),
(539, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 12:24:28'),
(540, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 12:24:29'),
(541, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 12:24:31'),
(542, 1, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-08-02 12:24:35'),
(543, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 12:24:36'),
(544, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 12:24:38'),
(545, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 12:24:41'),
(546, 1, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:15:37'),
(547, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:50:36'),
(548, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:50:58'),
(549, 17, 10.00, 'Easy', 'cashout', 1.1200, 11.20, 'null', '2025-08-02 14:51:29'),
(550, 17, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:51:46'),
(551, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:51:57'),
(552, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:52:03'),
(553, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:52:06'),
(554, 17, 10.00, 'Hardcore', 'cashout', 4.9500, 49.50, 'null', '2025-08-02 14:52:12'),
(555, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:52:16'),
(556, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:52:19'),
(557, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:52:23'),
(558, 17, 10.00, 'Hardcore', 'cashout', 1.6300, 16.30, 'null', '2025-08-02 14:52:26'),
(559, 17, 10.00, 'Hardcore', 'cashout', 2.8000, 28.00, 'null', '2025-08-02 14:52:27'),
(560, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:52:29'),
(561, 17, 10.00, 'Hardcore', 'cashout', 2.8000, 28.00, 'null', '2025-08-02 14:52:31'),
(562, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:52:35'),
(563, 17, 10.00, 'Hardcore', 'cashout', 2.8000, 28.00, 'null', '2025-08-02 14:52:40'),
(564, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:52:46'),
(565, 17, 10.00, 'Hardcore', 'cashout', 4.9500, 49.50, 'null', '2025-08-02 14:52:50'),
(566, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:52:53'),
(567, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:52:54'),
(568, 17, 10.00, 'Hardcore', 'cashout', 4.9500, 49.50, 'null', '2025-08-02 14:53:01'),
(569, 17, 10.00, 'Hardcore', 'cashout', 2.8000, 28.00, 'null', '2025-08-02 14:53:08'),
(570, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:53:11'),
(571, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:53:12'),
(572, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:53:15'),
(573, 17, 10.00, 'Hardcore', 'cashout', 2.8000, 28.00, 'null', '2025-08-02 14:53:19'),
(574, 17, 10.00, 'Hardcore', 'cashout', 2.8000, 28.00, 'null', '2025-08-02 14:53:23'),
(575, 17, 10.00, 'Hardcore', 'cashout', 4.9500, 49.50, 'null', '2025-08-02 14:53:25'),
(576, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:53:27'),
(577, 17, 10.00, 'Hardcore', 'cashout', 2.8000, 28.00, 'null', '2025-08-02 14:53:31'),
(578, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:53:34'),
(579, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:53:35'),
(580, 17, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:53:38'),
(581, 17, 10.00, 'Hardcore', 'cashout', 1.6300, 16.30, 'null', '2025-08-02 14:53:40'),
(582, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:54:01'),
(583, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:54:04'),
(584, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:54:07'),
(585, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:54:08'),
(586, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:54:10'),
(587, 1, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:54:14'),
(588, 1, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:54:16'),
(589, 1, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:54:17'),
(590, 1, 10.00, 'Hardcore', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:54:20'),
(591, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:55:10'),
(592, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:55:13'),
(593, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 14:55:19'),
(594, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 15:06:39'),
(595, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 15:06:43'),
(596, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 15:06:47'),
(597, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 15:06:49'),
(598, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 15:06:52'),
(599, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 15:07:13'),
(600, 1, 10.00, 'Easy', 'cashout', 19.4400, 194.40, 'null', '2025-08-02 15:07:30'),
(601, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 15:08:18'),
(602, 1, 0.60, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 15:08:41'),
(603, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 15:08:45'),
(604, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 15:08:49'),
(605, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 15:08:53'),
(606, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 15:09:09'),
(607, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 15:09:14'),
(608, 1, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-08-02 15:14:52'),
(609, 1, 0.60, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 15:15:00'),
(610, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 16:11:00'),
(611, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 16:11:03'),
(612, 1, 10.00, 'Easy', 'cashout', 1.0300, 10.30, 'null', '2025-08-02 16:11:08'),
(613, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-02 16:11:10'),
(614, 1, 10.00, 'Easy', 'cashout', 1.0700, 10.70, 'null', '2025-08-02 16:11:15'),
(615, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-05 13:20:50'),
(616, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-05 13:20:54'),
(617, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-05 13:21:05'),
(618, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-05 13:21:23'),
(619, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-05 13:23:34'),
(620, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-05 13:23:37'),
(621, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-05 13:23:41'),
(622, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-05 13:23:46'),
(623, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-05 13:23:47'),
(624, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-05 13:23:51'),
(625, 1, 10.00, 'Medium', 'crash', 0.0000, 0.00, 'null', '2025-08-05 13:24:04'),
(626, 1, 10.00, 'Medium', 'crash', 0.0000, 0.00, 'null', '2025-08-05 13:24:14');
INSERT INTO `bet_history` (`id`, `user_id`, `bet_amount`, `difficulty`, `result`, `multiplier`, `profit_loss`, `game_data`, `created_at`) VALUES
(627, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-06 04:48:02'),
(628, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-06 04:48:12'),
(629, 1, 10.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-06 04:48:16'),
(630, 1, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-06 04:48:23'),
(631, 1, 10000.00, 'Easy', 'cashout', 1.0700, 10700.00, 'null', '2025-08-06 06:52:42'),
(632, 1, 10000.00, 'Easy', 'cashout', 1.0700, 10700.00, 'null', '2025-08-06 06:53:01'),
(633, 1, 10000.00, 'Easy', 'cashout', 1.0700, 10700.00, 'null', '2025-08-06 06:53:19'),
(634, 1, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-06 06:53:25'),
(635, 1, 10000.00, 'Easy', 'crash', 0.0000, 0.00, 'null', '2025-08-06 06:53:29'),
(636, 1, 10000.00, 'Easy', 'cashout', 1.0700, 10700.00, 'null', '2025-08-06 06:53:34');

-- --------------------------------------------------------

--
-- Table structure for table `kyc_verification`
--

CREATE TABLE `kyc_verification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `selfie_path` varchar(500) NOT NULL,
  `id_path` varchar(500) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reason` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kyc_verification`
--

INSERT INTO `kyc_verification` (`id`, `user_id`, `full_name`, `date_of_birth`, `selfie_path`, `id_path`, `status`, `reason`, `admin_notes`, `created_at`, `updated_at`) VALUES
(1, 4, 'Dev', '2025-06-04', '../uploads/kyc/selfie_4_1751207809.png', '../uploads/kyc/id_4_1751207809.jpg', 'approved', NULL, '', '2025-06-29 14:36:49', '2025-06-29 14:39:56'),
(2, 5, 'Dev Tyagi', '2025-06-20', '../uploads/kyc/selfie_5_1751293229.jpg', '../uploads/kyc/id_5_1751293229.jpg', 'rejected', '', '', '2025-06-30 14:20:29', '2025-06-30 14:20:43'),
(3, 5, 'Hvvhvh', '2025-07-31', '../uploads/kyc/selfie_5_1751341714.png', '../uploads/kyc/id_5_1751341714.png', 'approved', NULL, '', '2025-07-01 03:48:34', '2025-07-01 03:49:14'),
(4, 16, 'mcfdxzsa', '2007-01-01', '../uploads/kyc/selfie_16_1754136112.jpg', '../uploads/kyc/id_16_1754136112.jpg', 'pending', NULL, NULL, '2025-08-02 12:01:52', '2025-08-02 12:01:52');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('deposit','withdrawal') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `method` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `deposit_method` varchar(32) DEFAULT NULL,
  `transaction_id` varchar(64) DEFAULT NULL,
  `screenshot` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `type`, `amount`, `status`, `method`, `description`, `admin_notes`, `processed_by`, `processed_at`, `created_at`, `deposit_method`, `transaction_id`, `screenshot`) VALUES
(1, 3, 'deposit', 1000.00, 'approved', 'qr', 'UPI QR', '', 1, '2025-06-28 08:28:58', '2025-06-28 08:28:34', NULL, NULL, NULL),
(2, 4, 'withdrawal', 100.00, 'pending', 'upi', 'UPI: devtyagi8433279900p@gmail.com', NULL, NULL, NULL, '2025-06-28 14:29:31', NULL, NULL, NULL),
(3, 4, 'deposit', 10000.00, 'approved', 'qr', 'UPI QR', '', 2, '2025-06-28 15:50:20', '2025-06-28 15:50:07', NULL, NULL, NULL),
(4, 2, 'deposit', 10000.00, 'approved', 'qr', 'UPI QR', '', 2, '2025-06-28 17:01:09', '2025-06-28 17:00:07', NULL, NULL, NULL),
(5, 2, 'deposit', 10000.00, 'approved', 'bank', 'Bank Transfer', '', 2, '2025-06-28 17:01:39', '2025-06-28 17:01:24', NULL, NULL, NULL),
(6, 5, 'deposit', 2000.00, 'approved', 'qr', 'UPI QR', '', 2, '2025-06-29 08:17:15', '2025-06-29 08:16:54', NULL, NULL, NULL),
(7, 7, 'deposit', 5000.00, 'pending', 'qr', 'UPI QR', NULL, NULL, NULL, '2025-06-29 12:07:44', NULL, NULL, NULL),
(8, 7, 'deposit', 5000.00, 'pending', 'qr', 'UPI QR', NULL, NULL, NULL, '2025-06-29 12:07:46', NULL, NULL, NULL),
(9, 4, 'deposit', 500.00, 'pending', 'bank', '', NULL, NULL, NULL, '2025-06-30 16:28:06', 'bank', 'Tyagiji', 'uploads/deposit_1751300886_6547.jpg'),
(10, 4, 'deposit', 500.00, 'pending', 'bank', '', NULL, NULL, NULL, '2025-06-30 16:28:07', 'bank', 'Tyagiji', 'uploads/deposit_1751300887_4806.jpg'),
(11, 5, 'deposit', 100.00, 'approved', 'bank', '', '', 1, '2025-06-30 17:00:26', '2025-06-30 17:00:12', 'bank', 'Gvfvgv', 'uploads/deposit_1751302811_1967.png'),
(12, 5, 'withdrawal', 500.00, 'pending', 'upi', 'UPI: Bbbb', NULL, NULL, NULL, '2025-06-30 17:01:08', NULL, NULL, NULL),
(13, 9, 'deposit', 5000.00, 'approved', 'bank', '', 'd', 1, '2025-06-30 18:32:58', '2025-06-30 18:32:29', 'bank', '77777777777777777777', 'uploads/deposit_1751308349_4173.png'),
(14, 8, 'deposit', 500.00, 'approved', 'qr', '', '', 1, '2025-06-30 18:48:12', '2025-06-30 18:47:08', 'qr', 'bzbd', NULL),
(15, 5, 'withdrawal', 500.00, 'pending', 'upi', 'UPI: devtyagi8433279900p@gmail.com', NULL, NULL, NULL, '2025-07-01 03:49:32', NULL, NULL, NULL),
(16, 1, 'deposit', 500.00, 'approved', 'qr', '', '', 1, '2025-07-01 03:52:38', '2025-07-01 03:51:43', 'qr', '66466777', 'uploads/deposit_1751341901_3573.jpg'),
(17, 1, 'deposit', 500.00, 'approved', 'qr', '', '', 1, '2025-07-01 03:52:32', '2025-07-01 03:51:46', 'qr', '66466777', 'uploads/deposit_1751341904_5122.jpg'),
(18, 10, 'deposit', 500.00, 'approved', 'qr', '', '', 1, '2025-07-01 04:09:52', '2025-07-01 04:09:13', 'qr', '664667777777', 'uploads/deposit_1751342951_3181.jpg'),
(19, 14, 'deposit', 500.00, 'approved', 'qr', '', '', 1, '2025-07-01 09:52:35', '2025-07-01 09:52:20', 'qr', 'devtyagi8433279900p@gmail.com', 'uploads/deposit_1751363540_8453.jpg'),
(20, 16, 'deposit', 5000.00, 'approved', 'qr', '', 'sucess', 2, '2025-08-02 11:58:03', '2025-08-02 11:55:49', 'qr', '98845448', 'uploads/deposit_1754135749_7700.jpg'),
(21, 17, 'deposit', 100.00, 'approved', 'qr', '', '', 1, '2025-08-02 14:51:16', '2025-08-02 14:51:04', 'qr', '7263736272827277', 'uploads/deposit_1754146264_8928.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `balance` decimal(10,2) DEFAULT 10000.00,
  `avatar` varchar(50) DEFAULT 'avatar/7.png',
  `referral_code` varchar(20) NOT NULL,
  `referred_by` int(11) DEFAULT NULL,
  `total_referrals` int(11) DEFAULT 0,
  `referral_earnings` decimal(10,2) DEFAULT 0.00,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_role` enum('admin','super_admin','moderator') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `phone`, `password`, `balance`, `avatar`, `referral_code`, `referred_by`, `total_referrals`, `referral_earnings`, `is_admin`, `created_at`, `updated_at`, `admin_role`) VALUES
(1, 'admin', '1234567890', '$2y$10$bBkz.mrnZu8.VqvRIuFPLu6GgUouXNb8dY2O2jwTsEgTAuYZNS0g2', 72580.40, 'https://luckeywin.fun/cheaken/avatar/2.png', 'ADMIN001', NULL, 0, 0.00, 1, '2025-06-28 06:53:34', '2025-08-06 06:53:58', 'super_admin'),
(2, 'admin2', '0987654321', '$2y$10$Wvyb7DL3m29afQDzgVB50OtLa4WHf/X/F8Y8fFjRntvVIp3OB/uqe', 804094.80, 'https://rumaxads.site/avatar/6.png', 'ADMIN002', NULL, 0, 0.00, 1, '2025-06-28 06:53:34', '2025-08-01 03:21:57', 'super_admin'),
(3, 'tyagidev999', 'temp_3_17513442', '$2y$10$WqOTjI.AWMrUZB/Hr9nBFe5V0/IvCT1LPJrerLV6/LdqtY./dbGqm', 389.90, 'https://rumaxads.site/avatar/2.png', 'REFROXVWC', NULL, 0, 0.00, 0, '2025-06-28 08:28:12', '2025-07-01 04:30:20', NULL),
(4, 'Tyagiji', 'temp_4_17513442', '$2y$10$xEIdlqgGayz3Ecw45/weVOV220CxuDfKEgmlW6ssQujTQ655C7Kc6', 9099.00, 'https://rumaxads.site/avatar/3.png', 'REF7QC2E1', NULL, 0, 0.00, 0, '2025-06-28 13:58:37', '2025-07-01 04:30:20', NULL),
(5, 'Bhaihutera', 'temp_5_17513442', '$2y$10$qjwjkxL4blREIYRN5Qp16ONmkM7foKcbuELfEUp4ZICY.lKy3LD6W', 1499.88, 'https://www.rumaxads.site/avatar/3.png', 'REFBDF0OP', NULL, 1, 0.00, 0, '2025-06-28 15:44:19', '2025-07-01 04:30:20', NULL),
(6, 'Srk', 'temp_6_17513442', '$2y$10$wsi6rkMaPDj7wARMIWJBr.RCS1TAswwPzlrWqByh0Fg8ZlEmqK7Z6', 0.00, 'avatar/7.png', 'REF5D7GNM', NULL, 0, 0.00, 0, '2025-06-28 16:58:43', '2025-07-01 04:30:20', NULL),
(7, 'hellobro', 'temp_7_17513442', '1212@dev', 0.00, 'avatar/7.png', 'REFYBXHME', NULL, 0, 0.00, 0, '2025-06-29 12:07:06', '2025-07-01 04:30:20', NULL),
(8, 'iphone', 'temp_8_17513442', '$2y$10$Uo.IZE43brHQeXc6og1ma.HcW1Hqj15Hz5j/MarlaMdINj3rZJPqm', 448.78, 'avatar/7.png', 'REFZQ2S4W', 5, 0, 0.00, 0, '2025-06-30 17:06:46', '2025-07-01 09:59:31', NULL),
(9, 'rahul2211', 'temp_9_17513442', 'rahul1122', 4619.00, 'avatar/7.png', 'REFV6VLUC', NULL, 0, 0.00, 0, '2025-06-30 18:29:55', '2025-07-01 04:30:20', NULL),
(10, 'demo', 'temp_10_1751344', '$2y$10$HjPHRl17ZWwPo96VuMSwVu44g9C1L1vc99aTq8P7p8Ate0Ft0TOLi', 99942.20, 'avatar/7.png', 'REFWT28BE', NULL, 0, 0.00, 0, '2025-07-01 01:26:25', '2025-07-01 11:29:49', NULL),
(11, 'sujeetv4@gmail.com', 'temp_11_1751344', '1234Qwer@', 0.00, 'avatar/7.png', 'REFIX1H2T', NULL, 0, 0.00, 0, '2025-07-01 02:31:54', '2025-07-01 04:30:20', NULL),
(12, 'devnew', '8433279900', '$2y$10$Ux9LFeG0LyPlkbIgvxbXAeEt0EnYTX5oR4wXiVG3.S.wZ5jX2HJXC', 0.00, 'avatar/7.png', 'REF0XPRT1', NULL, 0, 0.00, 0, '2025-07-01 04:35:56', '2025-07-01 04:35:56', NULL),
(13, 'saimm', '8194092853', '$2y$10$3o2HFw9pmsYeJjw2eh3hZuYW9eIMcb.kMDxfql0eysCRFLywQ2giG', 0.00, 'avatar/7.png', 'REFU7CMBA', NULL, 0, 0.00, 0, '2025-07-01 05:59:28', '2025-07-01 05:59:28', NULL),
(14, 'devneww', '8433279990', '$2y$10$FveZC2jI5pAWKZWhpeprkeT.IWfXB/iApEN9HslQX0NToqSSgWbsm', 99880.53, 'avatar/7.png', 'REFXYXMCW', NULL, 0, 0.00, 0, '2025-07-01 09:38:40', '2025-07-01 10:09:07', NULL),
(15, 'raju', '6393680693', '$2y$10$5KkMDhrocfdNk4ohWJaRYOv1UIF3RmKgsA0vaNhIvBsFStZvsyeU2', 0.00, 'avatar/7.png', 'REFH5FITX', NULL, 0, 0.00, 0, '2025-07-31 15:43:24', '2025-07-31 15:43:24', NULL),
(16, 'santosh', '9794241758', '$2y$10$XL9eixGidXQGiEpa7F509Oeowz1vdj/S7FfEXQp5si3WELPDWsEoq', 4941.20, 'avatar/7.png', 'REFEJIVAD', NULL, 0, 0.00, 0, '2025-08-02 11:55:10', '2025-08-02 11:59:21', NULL),
(17, 'suresh', '9879879879', '$2y$10$yfzVEL8pxZjkmzvMofiWEODFypeY0SIlEeI85wk/MOWGofMUjuVfS', 127.80, 'avatar/7.png', 'REFIRMZN4', NULL, 0, 0.00, 0, '2025-08-02 14:43:47', '2025-08-02 14:53:40', NULL),
(18, '8285785592', '8285785592', '$2y$10$hwriDsTulYt1M7FwijJEFehvUHwJYuKwQslKACfAOyQFP9eT.94t2', 0.00, 'avatar/7.png', 'REFGL9N2C', NULL, 0, 0.00, 0, '2025-08-05 14:15:36', '2025-08-05 14:15:36', NULL),
(19, 'Tegzggzvzg', '3483306501', '$2y$10$oQmHsMvxWYetvq.81tU1f.YZZzdQ49xkv9LjhHF83RpQu2xOsqVQG', 0.00, 'avatar/7.png', 'REF3SXMVB', NULL, 0, 0.00, 0, '2025-08-06 08:22:21', '2025-08-06 08:22:21', NULL),
(20, 'QS singh', '9876543210', '$2y$10$tdl6bIfQZnap7mWsKl/yc.xla8F5z2uKdNbodYmiEe2jRF25ner/y', 0.00, 'avatar/7.png', 'REFRLBFS8', NULL, 0, 0.00, 0, '2025-08-08 05:14:19', '2025-08-08 05:14:19', NULL),
(21, 'selim', '8287157975', '$2y$10$pibOrEK2O6UhrLPJGHWZweyF/n04o5eZ5q3BGlkgFA0v3SH2yWwn.', 0.00, 'avatar/7.png', 'REFR61U2A', NULL, 0, 0.00, 0, '2025-08-08 07:29:08', '2025-08-08 07:29:08', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bet_history`
--
ALTER TABLE `bet_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `kyc_verification`
--
ALTER TABLE `kyc_verification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `processed_by` (`processed_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `referral_code` (`referral_code`),
  ADD UNIQUE KEY `unique_phone` (`phone`),
  ADD KEY `referred_by` (`referred_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bet_history`
--
ALTER TABLE `bet_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=637;

--
-- AUTO_INCREMENT for table `kyc_verification`
--
ALTER TABLE `kyc_verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bet_history`
--
ALTER TABLE `bet_history`
  ADD CONSTRAINT `bet_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kyc_verification`
--
ALTER TABLE `kyc_verification`
  ADD CONSTRAINT `kyc_verification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`referred_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
