<?php
// Get current page for active navigation
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Get pending transactions count for badge
$pending_transactions = 0;
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status = 'pending'");
    $pending_transactions = $stmt->fetchColumn();
} catch (Exception $e) {
    $pending_transactions = 0;
}
?>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="sidebar-chicken-icon">🐔</div>
            <div>
                <div class="sidebar-title">CHICKEN ROAD</div>
                <div class="sidebar-subtitle">Admin Panel</div>
            </div>
        </div>
    </div>
    
    <div class="admin-info">
        <div class="admin-name"><?php echo htmlspecialchars(getCurrentAdminUsername()); ?></div>
        <div class="admin-role"><?php echo htmlspecialchars(getCurrentAdminRole()); ?></div>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Dashboard</div>
            <a href="dashboard.php" class="nav-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                <span class="nav-icon">📊</span>
                <span class="nav-text">Dashboard</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Management</div>
            <a href="users.php" class="nav-item <?php echo $current_page === 'users' ? 'active' : ''; ?>">
                <span class="nav-icon">👥</span>
                <span class="nav-text">Users</span>
            </a>
            <a href="transactions.php" class="nav-item <?php echo $current_page === 'transactions' ? 'active' : ''; ?>">
                <span class="nav-icon">💰</span>
                <span class="nav-text">Transactions</span>
                <?php if ($pending_transactions > 0): ?>
                    <span class="nav-badge"><?php echo $pending_transactions; ?></span>
                <?php endif; ?>
            </a>
            <a href="bets.php" class="nav-item <?php echo $current_page === 'bets' ? 'active' : ''; ?>">
                <span class="nav-icon">🎮</span>
                <span class="nav-text">Bet History</span>
            </a>
            <a href="kyc.php" class="nav-item <?php echo $current_page === 'kyc' ? 'active' : ''; ?>">
                <span class="nav-icon">📋</span>
                <span class="nav-text">KYC Management</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">System</div>
            <a href="settings.php" class="nav-item <?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                <span class="nav-icon">⚙️</span>
                <span class="nav-text">Settings</span>
            </a>
            <a href="reports.php" class="nav-item <?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                <span class="nav-icon">📈</span>
                <span class="nav-text">Reports</span>
            </a>
            <a href="logs.php" class="nav-item <?php echo $current_page === 'logs' ? 'active' : ''; ?>">
                <span class="nav-icon">📝</span>
                <span class="nav-text">Activity Logs</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Account</div>
            <a href="profile.php" class="nav-item <?php echo $current_page === 'profile' ? 'active' : ''; ?>">
                <span class="nav-icon">👤</span>
                <span class="nav-text">Profile</span>
            </a>
            <a href="logout.php" class="nav-item">
                <span class="nav-icon">🚪</span>
                <span class="nav-text">Logout</span>
            </a>
        </div>
    </nav>
</div> 