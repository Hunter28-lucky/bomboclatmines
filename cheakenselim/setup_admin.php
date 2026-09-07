<?php
// Admin Panel Setup Script
// Run this script to set up the admin panel

require_once 'config.php';

// Check if setup has already been completed
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'admin_activity_logs'");
    if ($stmt->rowCount() > 0) {
        echo "<h1>Setup Already Completed</h1>";
        echo "<p>The admin panel has already been set up. You can now:</p>";
        echo "<ul>";
        echo "<li><a href='http://localhost:8000/public_html/admin/login.php'>Login to Admin Panel</a></li>";
        echo "<li><a href='http://localhost:8000/public_html/admin/dashboard.php'>Go to Dashboard</a></li>";
        echo "</ul>";
        echo "<p><strong>For security reasons, this setup script should be deleted after use.</strong></p>";
        exit;
    }
} catch (Exception $e) {
    // Continue with setup if table doesn't exist
}

echo "<h1>Chicken Road Admin Panel Setup</h1>";

try {
    // Check database connection
    echo "<h2>1. Database Connection</h2>";
    $stmt = $pdo->query("SELECT 1");
    echo "✅ Database connection successful<br>";
    
    // Create admin tables
    echo "<h2>2. Creating Admin Tables</h2>";
    
    // Admin Activity Logs Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `admin_activity_logs` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "✅ Admin activity logs table created<br>";
    
    // Admin Notifications Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `admin_notifications` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "✅ Admin notifications table created<br>";
    
    // Admin Reports Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `admin_reports` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "✅ Admin reports table created<br>";
    
    // Add admin_role column to users table
    $pdo->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `admin_role` enum('admin','super_admin','moderator') DEFAULT NULL");
    echo "✅ Admin role column added to users table<br>";
    
    // Update existing admin users
    $pdo->exec("UPDATE `users` SET `admin_role` = 'super_admin' WHERE `is_admin` = 1 AND `admin_role` IS NULL");
    echo "✅ Existing admin users updated with roles<br>";
    
    // Check if admin user exists
    echo "<h2>3. Admin User Setup</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 1");
    $admin_count = $stmt->fetchColumn();
    
    if ($admin_count == 0) {
        // Create default admin user
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, phone, is_admin, admin_role, created_at) VALUES (?, ?, ?, 1, 'super_admin', NOW())");
        $stmt->execute(['admin', $admin_password, '1234567890']);
        echo "✅ Default admin user created:<br>";
        echo "   Username: admin<br>";
        echo "   Password: admin123<br>";
    } else {
        echo "✅ Admin user(s) already exist<br>";
    }
    
    // Insert sample data
    echo "<h2>4. Sample Data</h2>";
    
    // Sample activity logs
    $stmt = $pdo->prepare("INSERT IGNORE INTO admin_activity_logs (admin_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([1, 'setup_complete', 'Admin panel setup completed', '127.0.0.1', 'Setup Script']);
    echo "✅ Sample activity logs created<br>";
    
    // Sample notifications
    $stmt = $pdo->prepare("INSERT IGNORE INTO admin_notifications (admin_id, title, message, type) VALUES (?, ?, ?, ?)");
    $stmt->execute([1, 'Setup Complete', 'Admin panel has been successfully set up with all required tables and features.', 'success']);
    echo "✅ Sample notifications created<br>";
    
    echo "<h2>5. Setup Complete!</h2>";
    echo "✅ All admin panel components have been set up successfully.<br><br>";
    
    echo "<h3>Next Steps:</h3>";
    echo "1. <a href='http://localhost:8000/public_html/admin/login.php' target='_blank'>Access Admin Panel</a><br>";
    echo "2. Login with admin credentials<br>";
    echo "3. Explore the new features:<br>";
    echo "   - <a href='http://localhost:8000/public_html/admin/reports.php' target='_blank'>Reports & Analytics</a><br>";
    echo "   - <a href='http://localhost:8000/public_html/admin/logs.php' target='_blank'>Activity Logs</a><br>";
    echo "   - <a href='http://localhost:8000/public_html/admin/profile.php' target='_blank'>Admin Profile</a><br>";
    
} catch (Exception $e) {
    echo "<h2>❌ Setup Failed</h2>";
    echo "Error: " . $e->getMessage();
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: #f5f5f5;
}

h1 {
    color: #333;
    text-align: center;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

h2 {
    color: #007bff;
    margin-top: 30px;
}

h3 {
    color: #28a745;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style> 