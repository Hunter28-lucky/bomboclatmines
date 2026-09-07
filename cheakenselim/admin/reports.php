<?php
require_once 'config.php';
requireAdminLogin();

// Set page title
$page_title = 'Reports & Analytics';

// Get date range filters
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$end_date = $_GET['end_date'] ?? date('Y-m-d'); // Today

// Get report type
$report_type = $_GET['type'] ?? 'overview';

// Get statistics based on date range
try {
    // User statistics
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE DATE(created_at) BETWEEN ? AND ?");
    $stmt->execute([$start_date, $end_date]);
    $new_users = $stmt->fetchColumn();
    
    // Transaction statistics
    $stmt = $pdo->prepare("SELECT 
        COUNT(*) as total_transactions,
        SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END) as total_deposits,
        SUM(CASE WHEN type = 'withdrawal' THEN amount ELSE 0 END) as total_withdrawals,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_transactions
        FROM transactions WHERE DATE(created_at) BETWEEN ? AND ?");
    $stmt->execute([$start_date, $end_date]);
    $transaction_stats = $stmt->fetch();
    
    // Bet statistics
    $stmt = $pdo->prepare("SELECT 
        COUNT(*) as total_bets,
        SUM(bet_amount) as total_bet_amount,
        SUM(CASE WHEN result = 'cashout' THEN profit_loss ELSE 0 END) as total_profit,
        SUM(CASE WHEN result = 'crash' THEN ABS(profit_loss) ELSE 0 END) as total_loss
        FROM bet_history WHERE DATE(created_at) BETWEEN ? AND ?");
    $stmt->execute([$start_date, $end_date]);
    $bet_stats = $stmt->fetch();
    
    // Daily statistics for charts
    $stmt = $pdo->prepare("SELECT 
        DATE(created_at) as date,
        COUNT(*) as new_users
        FROM users 
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY DATE(created_at)
        ORDER BY date");
    $stmt->execute([$start_date, $end_date]);
    $daily_users = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT 
        DATE(created_at) as date,
        SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END) as deposits,
        SUM(CASE WHEN type = 'withdrawal' THEN amount ELSE 0 END) as withdrawals
        FROM transactions 
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY DATE(created_at)
        ORDER BY date");
    $stmt->execute([$start_date, $end_date]);
    $daily_transactions = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = 'Failed to load report data: ' . $e->getMessage();
}

// Include header
include 'includes/header.php';
?>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">Reports & Analytics</h1>
        <p class="page-subtitle">Comprehensive reports and analytics for your business</p>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <!-- Date Range Filter -->
    <div class="table-container" style="margin-bottom: 20px;">
        <div class="content-header">
            <h2 class="content-title">Filter Options</h2>
        </div>
        
        <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo $start_date; ?>">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $end_date; ?>">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="type" class="form-label">Report Type</label>
                <select name="type" id="type" class="form-control">
                    <option value="overview" <?php echo $report_type === 'overview' ? 'selected' : ''; ?>>Overview</option>
                    <option value="users" <?php echo $report_type === 'users' ? 'selected' : ''; ?>>User Analytics</option>
                    <option value="transactions" <?php echo $report_type === 'transactions' ? 'selected' : ''; ?>>Transaction Analytics</option>
                    <option value="bets" <?php echo $report_type === 'bets' ? 'selected' : ''; ?>>Bet Analytics</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Generate Report</button>
            <a href="reports.php" class="btn btn-secondary">Reset</a>
        </form>
    </div>
    
    <!-- Overview Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon users">👥</div>
                <div class="stat-info">
                    <h3>New Users</h3>
                    <div class="stat-value"><?php echo number_format($new_users); ?></div>
                    <div class="stat-period"><?php echo date('M d', strtotime($start_date)); ?> - <?php echo date('M d', strtotime($end_date)); ?></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon transactions">💰</div>
                <div class="stat-info">
                    <h3>Total Deposits</h3>
                    <div class="stat-value"><?php echo formatCurrency($transaction_stats['total_deposits']); ?></div>
                    <div class="stat-period"><?php echo number_format($transaction_stats['total_transactions']); ?> transactions</div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon withdrawals">💸</div>
                <div class="stat-info">
                    <h3>Total Withdrawals</h3>
                    <div class="stat-value"><?php echo formatCurrency($transaction_stats['total_withdrawals']); ?></div>
                    <div class="stat-period"><?php echo number_format($transaction_stats['pending_transactions']); ?> pending</div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon revenue">🎮</div>
                <div class="stat-info">
                    <h3>Total Bets</h3>
                    <div class="stat-value"><?php echo number_format($bet_stats['total_bets']); ?></div>
                    <div class="stat-period"><?php echo formatCurrency($bet_stats['total_bet_amount']); ?> wagered</div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon profit">📈</div>
                <div class="stat-info">
                    <h3>Net Profit</h3>
                    <div class="stat-value <?php echo ($bet_stats['total_profit'] - $bet_stats['total_loss']) >= 0 ? 'positive' : 'negative'; ?>">
                        <?php echo formatCurrency($bet_stats['total_profit'] - $bet_stats['total_loss']); ?>
                    </div>
                    <div class="stat-period">Profit: <?php echo formatCurrency($bet_stats['total_profit']); ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="table-container">
        <div class="content-header">
            <h2 class="content-title">Analytics Charts</h2>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
            <!-- User Growth Chart -->
            <div class="chart-container">
                <h3>User Growth</h3>
                <canvas id="userChart" width="400" height="200"></canvas>
            </div>
            
            <!-- Transaction Chart -->
            <div class="chart-container">
                <h3>Transaction Volume</h3>
                <canvas id="transactionChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Detailed Reports -->
    <div class="table-container">
        <div class="content-header">
            <h2 class="content-title">Detailed Reports</h2>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
            <!-- Top Users -->
            <div class="report-section">
                <h3>Top Users by Balance</h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Balance</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT username, balance, created_at FROM users WHERE is_admin = 0 ORDER BY balance DESC LIMIT 10");
                            $top_users = $stmt->fetchAll();
                            foreach ($top_users as $user):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo formatCurrency($user['balance']); ?></td>
                                <td><?php echo formatDate($user['created_at']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="report-section">
                <h3>Recent Activity</h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Details</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT action, description, created_at FROM admin_activity_logs ORDER BY created_at DESC LIMIT 10");
                            $recent_activity = $stmt->fetchAll();
                            foreach ($recent_activity as $activity):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($activity['action']); ?></td>
                                <td><?php echo htmlspecialchars($activity['description']); ?></td>
                                <td><?php echo getTimeAgo($activity['created_at']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// User Growth Chart
const userCtx = document.getElementById('userChart').getContext('2d');
const userChart = new Chart(userCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($daily_users, 'date')); ?>,
        datasets: [{
            label: 'New Users',
            data: <?php echo json_encode(array_column($daily_users, 'new_users')); ?>,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Transaction Chart
const transactionCtx = document.getElementById('transactionChart').getContext('2d');
const transactionChart = new Chart(transactionCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($daily_transactions, 'date')); ?>,
        datasets: [{
            label: 'Deposits',
            data: <?php echo json_encode(array_column($daily_transactions, 'deposits')); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.8)'
        }, {
            label: 'Withdrawals',
            data: <?php echo json_encode(array_column($daily_transactions, 'withdrawals')); ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.8)'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<style>
.chart-container {
    background: var(--bg_color_L2);
    border: 2px solid var(--Dividing-line_color);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}

.chart-container h3 {
    color: var(--text_color_L1);
    margin-bottom: 15px;
    text-align: center;
}

.report-section {
    background: var(--bg_color_L2);
    border: 2px solid var(--Dividing-line_color);
    border-radius: 15px;
    padding: 20px;
}

.report-section h3 {
    color: var(--text_color_L1);
    margin-bottom: 15px;
}

.stat-value.positive {
    color: #10b981;
}

.stat-value.negative {
    color: #ef4444;
}
</style>

<?php include 'includes/footer.php'; ?> 