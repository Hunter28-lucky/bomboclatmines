<?php
require_once 'config.php';

// Set page title
$page_title = 'Transactions Management';

// Get transactions with pagination and filters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$status_filter = $_GET['status'] ?? '';
$type_filter = $_GET['type'] ?? '';
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query conditions
$where_conditions = [];
$params = [];

if ($status_filter) {
    $where_conditions[] = "t.status = ?";
    $params[] = $status_filter;
}

if ($type_filter) {
    $where_conditions[] = "t.type = ?";
    $params[] = $type_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total transactions count
try {
    $count_query = "SELECT COUNT(*) FROM transactions t $where_clause";
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_transactions = $stmt->fetchColumn();
    $total_pages = ceil($total_transactions / $limit);
} catch (Exception $e) {
    $total_transactions = 0;
    $total_pages = 0;
}

    // Get transactions
    try {
        $query = "
            SELECT t.*, u.username, u.phone 
            FROM transactions t 
            LEFT JOIN users u ON t.user_id = u.id 
            $where_clause
            ORDER BY t.created_at DESC 
            LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $transactions = $stmt->fetchAll();
    } catch (Exception $e) {
        $transactions = [];
    }

// Handle transaction actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $transaction_id = (int)($_POST['transaction_id'] ?? 0);
    $admin_note = sanitize($_POST['admin_note'] ?? '');
    
    if ($action && $transaction_id) {
        try {
            switch ($action) {
                case 'approve':
                    $stmt = $pdo->prepare("UPDATE transactions SET status = 'approved', admin_notes = ?, processed_by = ?, processed_at = NOW() WHERE id = ?");
                    $stmt->execute([$admin_note, getCurrentAdminId(), $transaction_id]);
                    
                    // Update user balance for deposits
                    $stmt = $pdo->prepare("SELECT type, amount, user_id FROM transactions WHERE id = ?");
                    $stmt->execute([$transaction_id]);
                    $transaction = $stmt->fetch();
                    
                    if ($transaction && $transaction['type'] === 'deposit') {
                        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                        $stmt->execute([$transaction['amount'], $transaction['user_id']]);
                    }
                    
                    logAdminActivity('approve_transaction', "Approved transaction ID: $transaction_id");
                    break;
                    
                case 'reject':
                    $stmt = $pdo->prepare("UPDATE transactions SET status = 'rejected', admin_notes = ?, processed_by = ?, processed_at = NOW() WHERE id = ?");
                    $stmt->execute([$admin_note, getCurrentAdminId(), $transaction_id]);
                    logAdminActivity('reject_transaction', "Rejected transaction ID: $transaction_id");
                    break;
            }
            
            // Redirect to refresh the page
            $redirect_url = "transactions.php?page=$page";
            if ($status_filter) $redirect_url .= "&status=$status_filter";
            if ($type_filter) $redirect_url .= "&type=$type_filter";
            header('Location: ' . $redirect_url);
            exit;
        } catch (Exception $e) {
            $error = 'Action failed. Please try again.';
        }
    }
}

// Get statistics
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status = 'pending'");
    $pending_count = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status = 'approved'");
    $approved_count = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status = 'rejected'");
    $rejected_count = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT SUM(amount) FROM transactions WHERE status = 'approved' AND type = 'deposit'");
    $total_deposits = $stmt->fetchColumn() ?: 0;
    
    $stmt = $pdo->query("SELECT SUM(amount) FROM transactions WHERE status = 'approved' AND type = 'withdrawal'");
    $total_withdrawals = $stmt->fetchColumn() ?: 0;
} catch (Exception $e) {
    $pending_count = $approved_count = $rejected_count = 0;
    $total_deposits = $total_withdrawals = 0;
}

// Include header
include 'includes/header.php';
?>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">Transactions Management</h1>
        <p class="page-subtitle">Approve or reject user deposits and withdrawals</p>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon pending">⏳</div>
                <div class="stat-info">
                    <h3>Pending</h3>
                    <div class="stat-value"><?php echo number_format($pending_count); ?></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon transactions">✅</div>
                <div class="stat-info">
                    <h3>Approved</h3>
                    <div class="stat-value"><?php echo number_format($approved_count); ?></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon withdrawals">❌</div>
                <div class="stat-info">
                    <h3>Rejected</h3>
                    <div class="stat-value"><?php echo number_format($rejected_count); ?></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon revenue">💰</div>
                <div class="stat-info">
                    <h3>Total Deposits</h3>
                    <div class="stat-value"><?php echo formatCurrency($total_deposits); ?></div>
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
    
    <!-- Filters -->
    <div class="table-container" style="margin-bottom: 20px;">
        <div class="content-header">
            <h2 class="content-title">Filters</h2>
        </div>
        
        <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control" style="width: 150px;">
                    <option value="">All Status</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="type" class="form-label">Type</label>
                <select name="type" id="type" class="form-control" style="width: 150px;">
                    <option value="">All Types</option>
                    <option value="deposit" <?php echo $type_filter === 'deposit' ? 'selected' : ''; ?>>Deposit</option>
                    <option value="withdrawal" <?php echo $type_filter === 'withdrawal' ? 'selected' : ''; ?>>Withdrawal</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="transactions.php" class="btn btn-secondary">Clear</a>
        </form>
    </div>
    
    <!-- Transactions Table -->
    <div class="table-container">
        <div class="content-header">
            <h2 class="content-title">All Transactions</h2>
        </div>
        
        <?php if (empty($transactions)): ?>
            <p style="color: var(--text_color_L2); text-align: center; padding: 40px;">No transactions found</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Method</th>
                        <th>Transaction ID</th>
                        <th>Screenshot</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?php echo $transaction['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($transaction['username'] ?? 'Unknown User'); ?></strong>
                                <br><small><?php echo htmlspecialchars($transaction['phone'] ?? 'N/A'); ?></small>
                            </td>
                            <td>
                                <span class="status-badge 
                                    <?php 
                                    switch($transaction['type']) {
                                        case 'deposit': echo 'status-approved'; break;
                                        case 'withdrawal': echo 'status-pending'; break;
                                        default: echo 'status-pending';
                                    }
                                    ?>">
                                    <?php echo ucfirst($transaction['type']); ?>
                                </span>
                            </td>
                            <td class="<?php echo $transaction['type'] === 'deposit' ? 'amount-positive' : 'amount-negative'; ?>">
                                <?php echo formatCurrency($transaction['amount']); ?>
                            </td>
                            <td>
                                <span class="status-badge 
                                    <?php 
                                    switch($transaction['status']) {
                                        case 'approved': echo 'status-approved'; break;
                                        case 'rejected': echo 'status-rejected'; break;
                                        default: echo 'status-pending';
                                    }
                                    ?>">
                                    <?php echo ucfirst($transaction['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($transaction['method'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($transaction['transaction_id'] ?? 'N/A'); ?></td>
                            <td>
                                <?php if (!empty($transaction['screenshot'])): ?>
                                    <a href="<?php echo htmlspecialchars($transaction['screenshot']); ?>" target="_blank" class="btn btn-sm btn-secondary">View</a>
                                <?php else: ?>
                                    <span style="color: var(--text_color_L3);">No screenshot</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo formatDate($transaction['created_at']); ?></td>
                            <td>
                                <?php if ($transaction['status'] === 'pending'): ?>
                                    <div style="display: flex; gap: 5px;">
                                        <button type="button" class="btn btn-success" style="padding: 6px 12px; font-size: 0.8rem;" 
                                                onclick="showActionModal('approve', <?php echo $transaction['id']; ?>, '<?php echo htmlspecialchars($transaction['username'] ?? 'Unknown'); ?>', '<?php echo $transaction['type']; ?>', <?php echo $transaction['amount']; ?>)">
                                            Approve
                                        </button>
                                        <button type="button" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.8rem;" 
                                                onclick="showActionModal('reject', <?php echo $transaction['id']; ?>, '<?php echo htmlspecialchars($transaction['username'] ?? 'Unknown'); ?>', '<?php echo $transaction['type']; ?>', <?php echo $transaction['amount']; ?>)">
                                            Reject
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div style="font-size: 0.8rem; color: var(--text_color_L3);">
                                        <?php if ($transaction['admin_notes']): ?>
                                            <div><strong>Note:</strong> <?php echo htmlspecialchars($transaction['admin_notes']); ?></div>
                                        <?php endif; ?>
                                        <?php if ($transaction['processed_at']): ?>
                                            <div><strong>Processed:</strong> <?php echo formatDate($transaction['processed_at']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div style="display: flex; justify-content: center; margin-top: 30px; gap: 10px;">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>&type=<?php echo $type_filter; ?>" class="btn btn-secondary">Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&type=<?php echo $type_filter; ?>" class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-secondary'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>&type=<?php echo $type_filter; ?>" class="btn btn-secondary">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Action Modal -->
<div id="actionModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background: var(--bg_color_L2); margin: 15% auto; padding: 25px; border-radius: 15px; width: 90%; max-width: 500px; border: 2px solid var(--Dividing-line_color);">
        <h3 id="modalTitle" style="color: var(--text_color_L1); margin-bottom: 20px;"></h3>
        
        <form method="POST" id="actionForm">
            <input type="hidden" name="action" id="modalAction">
            <input type="hidden" name="transaction_id" id="modalTransactionId">
            
            <div class="form-group">
                <label for="admin_note" class="form-label">Admin Note (Optional)</label>
                <textarea name="admin_note" id="admin_note" class="form-control" rows="3" placeholder="Add a note about this action..."></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn" id="modalSubmitBtn">Confirm</button>
            </div>
        </form>
    </div>
</div>

<script>
function showActionModal(action, transactionId, username, type, amount) {
    const modal = document.getElementById('actionModal');
    const title = document.getElementById('modalTitle');
    const actionInput = document.getElementById('modalAction');
    const transactionIdInput = document.getElementById('modalTransactionId');
    const submitBtn = document.getElementById('modalSubmitBtn');
    
    actionInput.value = action;
    transactionIdInput.value = transactionId;
    
    if (action === 'approve') {
        title.textContent = `Approve ${type} for ${username}`;
        title.innerHTML += `<br><small style="color: var(--text_color_L2);">Amount: ${formatCurrency(amount)}</small>`;
        submitBtn.textContent = 'Approve';
        submitBtn.className = 'btn btn-success';
    } else {
        title.textContent = `Reject ${type} for ${username}`;
        title.innerHTML += `<br><small style="color: var(--text_color_L2);">Amount: ${formatCurrency(amount)}</small>`;
        submitBtn.textContent = 'Reject';
        submitBtn.className = 'btn btn-danger';
    }
    
    modal.style.display = 'block';
}

function closeModal() {
    document.getElementById('actionModal').style.display = 'none';
    document.getElementById('admin_note').value = '';
}

function formatCurrency(amount) {
    return '₹' + parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('actionModal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>

<style>
.modal-content {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<?php include 'includes/footer.php'; ?> 