<?php
require_once 'config.php';
requireAdminLogin();

// Set page title
$page_title = 'Admin Profile';

// Get current admin data
$admin_id = getCurrentAdminId();
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND is_admin = 1");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        header('Location: logout.php');
        exit;
    }
} catch (Exception $e) {
    $error = 'Failed to load profile data: ' . $e->getMessage();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        
        // Validate input
        if (empty($username)) {
            $error = 'Username is required';
        } elseif (strlen($username) < 3) {
            $error = 'Username must be at least 3 characters long';
        } else {
            try {
                // Check if username already exists (excluding current user)
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
                $stmt->execute([$username, $admin_id]);
                if ($stmt->fetch()) {
                    $error = 'Username already exists';
                } else {
                    // Update profile
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, phone = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$username, $email, $phone, $admin_id]);
                    
                    // Update session
                    $_SESSION['admin_username'] = $username;
                    
                    logAdminActivity('update_profile', "Updated profile information");
                    $success = 'Profile updated successfully';
                    
                    // Refresh admin data
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND is_admin = 1");
                    $stmt->execute([$admin_id]);
                    $admin = $stmt->fetch();
                }
            } catch (Exception $e) {
                $error = 'Failed to update profile: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validate input
        if (empty($current_password)) {
            $error = 'Current password is required';
        } elseif (empty($new_password)) {
            $error = 'New password is required';
        } elseif (strlen($new_password) < 6) {
            $error = 'New password must be at least 6 characters long';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } else {
            try {
                // Verify current password
                if (!password_verify($current_password, $admin['password'])) {
                    $error = 'Current password is incorrect';
                } else {
                    // Update password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$hashed_password, $admin_id]);
                    
                    logAdminActivity('change_password', "Changed password");
                    $success = 'Password changed successfully';
                }
            } catch (Exception $e) {
                $error = 'Failed to change password: ' . $e->getMessage();
            }
        }
    }
}

// Get recent activity
try {
    $stmt = $pdo->prepare("SELECT * FROM admin_activity_logs WHERE admin_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$admin_id]);
    $recent_activity = $stmt->fetchAll();
} catch (Exception $e) {
    $recent_activity = [];
}

// Get login statistics
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_activity_logs WHERE admin_id = ? AND action = 'login'");
    $stmt->execute([$admin_id]);
    $total_logins = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT created_at FROM admin_activity_logs WHERE admin_id = ? AND action = 'login' ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$admin_id]);
    $last_login = $stmt->fetchColumn();
} catch (Exception $e) {
    $total_logins = 0;
    $last_login = null;
}

// Include header
include 'includes/header.php';
?>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">Admin Profile</h1>
        <p class="page-subtitle">Manage your account settings and preferences</p>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="message success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Profile Information -->
        <div class="table-container">
            <div class="content-header">
                <h2 class="content-title">Profile Information</h2>
            </div>
            
            <form method="POST" style="margin-top: 20px;">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" 
                           value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" 
                           value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" 
                           value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(getCurrentAdminRole()); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Member Since</label>
                    <input type="text" class="form-control" value="<?php echo formatDate($admin['created_at']); ?>" readonly>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
        
        <!-- Account Statistics -->
        <div class="table-container">
            <div class="content-header">
                <h2 class="content-title">Account Statistics</h2>
            </div>
            
            <div style="margin-top: 20px;">
                <div class="stat-item">
                    <div class="stat-label">Total Logins</div>
                    <div class="stat-value"><?php echo number_format($total_logins); ?></div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-label">Last Login</div>
                    <div class="stat-value"><?php echo $last_login ? formatDate($last_login) : 'Never'; ?></div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-label">Account Status</div>
                    <div class="stat-value">
                        <span class="status-badge status-approved">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Change Password -->
    <div class="table-container">
        <div class="content-header">
            <h2 class="content-title">Change Password</h2>
        </div>
        
        <form method="POST" style="margin-top: 20px;">
            <input type="hidden" name="action" value="change_password">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-warning">Change Password</button>
        </form>
    </div>
    
    <!-- Recent Activity -->
    <div class="table-container">
        <div class="content-header">
            <h2 class="content-title">Recent Activity</h2>
        </div>
        
        <?php if (empty($recent_activity)): ?>
            <p style="color: var(--text_color_L2); text-align: center; padding: 40px;">No recent activity</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_activity as $activity): ?>
                            <tr>
                                <td>
                                    <span class="status-badge status-pending">
                                        <?php echo htmlspecialchars($activity['action']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($activity['description']); ?></td>
                                <td><code><?php echo htmlspecialchars($activity['ip_address']); ?></code></td>
                                <td><?php echo formatDate($activity['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid var(--Dividing-line_color);
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-label {
    color: var(--text_color_L2);
    font-weight: 500;
}

.stat-value {
    color: var(--text_color_L1);
    font-weight: 600;
}

.message.success {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid #10b981;
    color: #10b981;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.btn-warning {
    background: #f59e0b;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.3s;
}

.btn-warning:hover {
    background: #d97706;
}
</style>

<?php include 'includes/footer.php'; ?> 