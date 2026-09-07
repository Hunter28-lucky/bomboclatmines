<?php
require_once 'config.php';

// Set page title
$page_title = 'Users Management';

// Get users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Get total users count
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0");
    $total_users = $stmt->fetchColumn();
    $total_pages = ceil($total_users / $limit);
} catch (Exception $e) {
    $total_users = 0;
    $total_pages = 0;
}

// Get users
try {
    $stmt = $pdo->prepare("
        SELECT * FROM users 
        WHERE is_admin = 0 
        ORDER BY created_at DESC 
        LIMIT " . (int)$limit . " OFFSET " . (int)$offset
    );
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $users = [];
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = (int)($_POST['user_id'] ?? 0);
    
    if ($action && $user_id) {
        try {
            switch ($action) {
                case 'block':
                    $stmt = $pdo->prepare("UPDATE users SET status = 'blocked' WHERE id = ? AND is_admin = 0");
                    $stmt->execute([$user_id]);
                    logAdminActivity('block_user', "Blocked user ID: $user_id");
                    break;
                    
                case 'unblock':
                    $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ? AND is_admin = 0");
                    $stmt->execute([$user_id]);
                    logAdminActivity('unblock_user', "Unblocked user ID: $user_id");
                    break;
                    
                case 'delete':
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND is_admin = 0");
                    $stmt->execute([$user_id]);
                    logAdminActivity('delete_user', "Deleted user ID: $user_id");
                    break;
            }
            
            // Redirect to refresh the page
            header('Location: users.php?page=' . $page);
            exit;
        } catch (Exception $e) {
            $error = 'Action failed. Please try again.';
        }
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">Users Management</h1>
        <p class="page-subtitle">Manage all registered users</p>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon users">👥</div>
                <div class="stat-info">
                    <h3>Total Users</h3>
                    <div class="stat-value"><?php echo number_format($total_users); ?></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon transactions">💰</div>
                <div class="stat-info">
                    <h3>Active Users</h3>
                    <div class="stat-value">
                        <?php 
                        try {
                            $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0 AND status = 'active'");
                            echo number_format($stmt->fetchColumn());
                        } catch (Exception $e) {
                            echo '0';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon pending">⏳</div>
                <div class="stat-info">
                    <h3>Blocked Users</h3>
                    <div class="stat-value">
                        <?php 
                        try {
                            $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0 AND status = 'blocked'");
                            echo number_format($stmt->fetchColumn());
                        } catch (Exception $e) {
                            echo '0';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Users Table -->
    <div class="table-container">
        <div class="content-header">
            <h2 class="content-title">All Users</h2>
        </div>
        
        <?php if (empty($users)): ?>
            <p style="color: var(--text_color_L2); text-align: center; padding: 40px;">No users found</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Phone</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                <?php if ($user['referral_code']): ?>
                                    <br><small>Ref: <?php echo htmlspecialchars($user['referral_code']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td class="amount-positive"><?php echo formatCurrency($user['balance']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $user['status'] === 'active' ? 'status-approved' : 'status-pending'; ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($user['created_at']); ?></td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <?php if ($user['status'] === 'active'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="block">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to block this user?')">
                                                Block
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="unblock">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-success" style="padding: 6px 12px; font-size: 0.8rem;">
                                                Unblock
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div style="display: flex; justify-content: center; margin-top: 30px; gap: 10px;">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="btn btn-secondary">Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-secondary'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="btn btn-secondary">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 