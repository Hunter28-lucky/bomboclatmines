<?php
require_once 'config.php';

// Set page title
$page_title = 'Dashboard';

// Get statistics
$total_users = getTotalUsers();
$total_transactions = getTotalTransactions();
$pending_transactions = getPendingTransactions();
$total_bets = getTotalBets();
$total_revenue = getTotalRevenue();
$total_withdrawals = getTotalWithdrawals();

// Get recent transactions
try {
    $stmt = $pdo->query("
        SELECT t.*, u.username 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        ORDER BY t.created_at DESC 
        LIMIT 5
    ");
    $recent_transactions = $stmt->fetchAll();
} catch (Exception $e) {
    $recent_transactions = [];
}

// Get recent users
try {
    $stmt = $pdo->query("
        SELECT * FROM users 
        WHERE is_admin = 0 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $recent_users = $stmt->fetchAll();
} catch (Exception $e) {
    $recent_users = [];
}

// Get unread notifications
$notifications = getUnreadNotifications();

// Include header
include 'includes/header.php';
?>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars(getCurrentAdminUsername()); ?>! Here's what's happening today.</p>
            </div>
            
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
                            <h3>Total Transactions</h3>
                            <div class="stat-value"><?php echo number_format($total_transactions); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon pending">⏳</div>
                        <div class="stat-info">
                            <h3>Pending Transactions</h3>
                            <div class="stat-value"><?php echo number_format($pending_transactions); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon bets">🎮</div>
                        <div class="stat-info">
                            <h3>Total Bets</h3>
                            <div class="stat-value"><?php echo number_format($total_bets); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon revenue">📈</div>
                        <div class="stat-info">
                            <h3>Total Revenue</h3>
                            <div class="stat-value"><?php echo formatCurrency($total_revenue); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon withdrawals">💸</div>
                        <div class="stat-info">
                            <h3>Total Withdrawals</h3>
                            <div class="stat-value"><?php echo formatCurrency($total_withdrawals); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content Sections -->
            <div class="content-grid">
                <!-- Recent Transactions -->
                <div class="content-card">
                    <div class="content-header">
                        <h2 class="content-title">Recent Transactions</h2>
                        <a href="transactions.php" class="content-link">View All</a>
                    </div>
                    
                    <?php if (empty($recent_transactions)): ?>
                        <p style="color: var(--text_color_L2); text-align: center; padding: 20px;">No recent transactions</p>
                    <?php else: ?>
                        <?php foreach ($recent_transactions as $transaction): ?>
                            <div class="list-item">
                                <div class="list-info">
                                    <h4><?php echo htmlspecialchars($transaction['username']); ?></h4>
                                    <p><?php echo formatDate($transaction['created_at']); ?></p>
                                </div>
                                <div class="list-amount <?php echo $transaction['type'] === 'deposit' ? 'amount-positive' : 'amount-negative'; ?>">
                                    <?php echo formatCurrency($transaction['amount']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Recent Users -->
                <div class="content-card">
                    <div class="content-header">
                        <h2 class="content-title">Recent Users</h2>
                        <a href="users.php" class="content-link">View All</a>
                    </div>
                    
                    <?php if (empty($recent_users)): ?>
                        <p style="color: var(--text_color_L2); text-align: center; padding: 20px;">No recent users</p>
                    <?php else: ?>
                        <?php foreach ($recent_users as $user): ?>
                            <div class="list-item">
                                <div class="list-info">
                                    <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                                    <p>Balance: <?php echo formatCurrency($user['balance']); ?></p>
                                </div>
                                <div class="list-amount">
                                    <?php echo formatDate($user['created_at']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?> 