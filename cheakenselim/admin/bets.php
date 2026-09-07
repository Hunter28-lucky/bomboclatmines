<?php
require_once 'config.php';

// Set page title
$page_title = 'Bet History';

// Get bets with pagination and filters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$result_filter = $_GET['result'] ?? '';
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query conditions
$where_conditions = [];
$params = [];

if ($result_filter) {
    $where_conditions[] = "b.result = ?";
    $params[] = $result_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total bets count
try {
    $count_query = "SELECT COUNT(*) FROM bet_history b $where_clause";
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_bets = $stmt->fetchColumn();
    $total_pages = ceil($total_bets / $limit);
} catch (Exception $e) {
    $total_bets = 0;
    $total_pages = 0;
}

// Get bets
try {
    $query = "
        SELECT b.*, u.username, u.phone 
        FROM bet_history b 
        JOIN users u ON b.user_id = u.id 
        $where_clause
        ORDER BY b.created_at DESC 
        LIMIT ? OFFSET ?
    ";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $bets = $stmt->fetchAll();
} catch (Exception $e) {
    $bets = [];
}

// Get statistics
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM bet_history");
    $total_bets_count = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM bet_history WHERE result = 'win'");
    $wins_count = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM bet_history WHERE result = 'loss'");
    $losses_count = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT SUM(bet_amount) FROM bet_history");
    $total_bet_amount = $stmt->fetchColumn() ?: 0;
    
    $stmt = $pdo->query("SELECT SUM(profit_loss) FROM bet_history");
    $total_profit_loss = $stmt->fetchColumn() ?: 0;
    
    $stmt = $pdo->query("SELECT SUM(profit_loss) FROM bet_history WHERE result = 'win'");
    $total_wins_amount = $stmt->fetchColumn() ?: 0;
    
    $stmt = $pdo->query("SELECT SUM(bet_amount) FROM bet_history WHERE result = 'loss'");
    $total_losses_amount = $stmt->fetchColumn() ?: 0;
} catch (Exception $e) {
    $total_bets_count = $wins_count = $losses_count = 0;
    $total_bet_amount = $total_profit_loss = $total_wins_amount = $total_losses_amount = 0;
}

// Include header
include 'includes/header.php';
?>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">Bet History</h1>
        <p class="page-subtitle">View and analyze all betting activities</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon bets">🎮</div>
                <div class="stat-info">
                    <h3>Total Bets</h3>
                    <div class="stat-value"><?php echo number_format($total_bets_count); ?></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon transactions">✅</div>
                <div class="stat-info">
                    <h3>Wins</h3>
                    <div class="stat-value"><?php echo number_format($wins_count); ?></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon withdrawals">❌</div>
                <div class="stat-info">
                    <h3>Losses</h3>
                    <div class="stat-value"><?php echo number_format($losses_count); ?></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon revenue">💰</div>
                <div class="stat-info">
                    <h3>Total Bet Amount</h3>
                    <div class="stat-value"><?php echo formatCurrency($total_bet_amount); ?></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon withdrawals">📈</div>
                <div class="stat-info">
                    <h3>Net Profit/Loss</h3>
                    <div class="stat-value <?php echo $total_profit_loss >= 0 ? 'amount-positive' : 'amount-negative'; ?>">
                        <?php echo formatCurrency($total_profit_loss); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon users">🎯</div>
                <div class="stat-info">
                    <h3>Win Rate</h3>
                    <div class="stat-value">
                        <?php echo $total_bets_count > 0 ? round(($wins_count / $total_bets_count) * 100, 1) : 0; ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="table-container" style="margin-bottom: 20px;">
        <div class="content-header">
            <h2 class="content-title">Filters</h2>
        </div>
        
        <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="result" class="form-label">Result</label>
                <select name="result" id="result" class="form-control" style="width: 150px;">
                    <option value="">All Results</option>
                    <option value="win" <?php echo $result_filter === 'win' ? 'selected' : ''; ?>>Wins</option>
                    <option value="loss" <?php echo $result_filter === 'loss' ? 'selected' : ''; ?>>Losses</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="bets.php" class="btn btn-secondary">Clear</a>
        </form>
    </div>
    
    <!-- Bets Table -->
    <div class="table-container">
        <div class="content-header">
            <h2 class="content-title">All Bets</h2>
        </div>
        
        <?php if (empty($bets)): ?>
            <p style="color: var(--text_color_L2); text-align: center; padding: 40px;">No bets found</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Bet Amount</th>
                        <th>Multiplier</th>
                        <th>Cashout</th>
                        <th>Profit/Loss</th>
                        <th>Result</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bets as $bet): ?>
                        <tr>
                            <td><?php echo $bet['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($bet['username']); ?></strong>
                                <br><small><?php echo htmlspecialchars($bet['phone']); ?></small>
                            </td>
                            <td class="amount-positive"><?php echo formatCurrency($bet['bet_amount']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $bet['multiplier'] >= 2 ? 'status-approved' : 'status-pending'; ?>">
                                    <?php echo number_format($bet['multiplier'], 2); ?>x
                                </span>
                            </td>
                            <td>
                                <?php if ($bet['cashout_multiplier']): ?>
                                    <span class="status-badge status-approved">
                                        <?php echo number_format($bet['cashout_multiplier'], 2); ?>x
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--text_color_L3);">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="<?php echo $bet['profit_loss'] >= 0 ? 'amount-positive' : 'amount-negative'; ?>">
                                <?php echo formatCurrency($bet['profit_loss']); ?>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $bet['result'] === 'win' ? 'status-approved' : 'status-rejected'; ?>">
                                    <?php echo ucfirst($bet['result']); ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($bet['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div style="display: flex; justify-content: center; margin-top: 30px; gap: 10px;">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&result=<?php echo $result_filter; ?>" class="btn btn-secondary">Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>&result=<?php echo $result_filter; ?>" class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-secondary'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&result=<?php echo $result_filter; ?>" class="btn btn-secondary">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <!-- Analytics Section -->
    <div class="content-grid" style="margin-top: 30px;">
        <!-- Win/Loss Distribution -->
        <div class="content-card">
            <div class="content-header">
                <h2 class="content-title">Win/Loss Distribution</h2>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div>
                    <h4 style="color: var(--Secondary_green_color); margin-bottom: 5px;">Wins</h4>
                    <p style="color: var(--text_color_L2); font-size: 0.9rem;">
                        <?php echo number_format($wins_count); ?> bets • <?php echo formatCurrency($total_wins_amount); ?>
                    </p>
                </div>
                <div style="text-align: right;">
                    <h4 style="color: var(--Secondary_red_color); margin-bottom: 5px;">Losses</h4>
                    <p style="color: var(--text_color_L2); font-size: 0.9rem;">
                        <?php echo number_format($losses_count); ?> bets • <?php echo formatCurrency($total_losses_amount); ?>
                    </p>
                </div>
            </div>
            
            <div style="background: var(--bg_color_L3); border-radius: 10px; height: 20px; overflow: hidden;">
                <?php if ($total_bets_count > 0): ?>
                    <div style="background: var(--Secondary_green_color); height: 100%; width: <?php echo ($wins_count / $total_bets_count) * 100; ?>%;"></div>
                <?php endif; ?>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 0.8rem; color: var(--text_color_L2);">
                <span>Wins: <?php echo $total_bets_count > 0 ? round(($wins_count / $total_bets_count) * 100, 1) : 0; ?>%</span>
                <span>Losses: <?php echo $total_bets_count > 0 ? round(($losses_count / $total_bets_count) * 100, 1) : 0; ?>%</span>
            </div>
        </div>
        
        <!-- Profit Analysis -->
        <div class="content-card">
            <div class="content-header">
                <h2 class="content-title">Profit Analysis</h2>
            </div>
            
            <div style="display: grid; gap: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: var(--bg_color_L3); border-radius: 8px;">
                    <span style="color: var(--text_color_L2);">Total Bet Amount:</span>
                    <span style="color: var(--text_color_L1); font-weight: 600;"><?php echo formatCurrency($total_bet_amount); ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: var(--bg_color_L3); border-radius: 8px;">
                    <span style="color: var(--text_color_L2);">Total Wins:</span>
                    <span style="color: var(--Secondary_green_color); font-weight: 600;"><?php echo formatCurrency($total_wins_amount); ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: var(--bg_color_L3); border-radius: 8px;">
                    <span style="color: var(--text_color_L2);">Total Losses:</span>
                    <span style="color: var(--Secondary_red_color); font-weight: 600;"><?php echo formatCurrency($total_losses_amount); ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--main-color); border-radius: 8px;">
                    <span style="color: var(--text_color_L4); font-weight: 600;">Net Profit/Loss:</span>
                    <span style="color: var(--text_color_L4); font-weight: 600; font-size: 1.1rem;">
                        <?php echo formatCurrency($total_profit_loss); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 