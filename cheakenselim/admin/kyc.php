<?php
require_once 'config.php';

// Set page title
$page_title = 'KYC Management';

// Get KYC requests with pagination and filters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$status_filter = $_GET['status'] ?? '';
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query conditions
$where_conditions = [];
$params = [];

if ($status_filter) {
    $where_conditions[] = "k.status = ?";
    $params[] = $status_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total KYC count
try {
    $count_query = "SELECT COUNT(*) FROM kyc_verification k $where_clause";
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_kyc = $stmt->fetchColumn();
    $total_pages = ceil($total_kyc / $limit);
} catch (Exception $e) {
    $total_kyc = 0;
    $total_pages = 0;
}

// Get KYC requests
try {
    $query = "
        SELECT k.*, u.username, u.phone, u.email 
        FROM kyc_verification k 
        JOIN users u ON k.user_id = u.id 
        $where_clause
        ORDER BY k.created_at DESC 
        LIMIT ? OFFSET ?
    ";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $kyc_requests = $stmt->fetchAll();
} catch (Exception $e) {
    $kyc_requests = [];
}

// Handle KYC actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $kyc_id = (int)($_POST['kyc_id'] ?? 0);
    $admin_note = sanitize($_POST['admin_note'] ?? '');
    
    if ($action && $kyc_id) {
        try {
            switch ($action) {
                case 'approve':
                    $stmt = $pdo->prepare("UPDATE kyc_verification SET status = 'approved', admin_note = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$admin_note, $kyc_id]);
                    
                    // Update user KYC status
                    $stmt = $pdo->prepare("UPDATE users SET kyc_verified = 1 WHERE id = (SELECT user_id FROM kyc_verification WHERE id = ?)");
                    $stmt->execute([$kyc_id]);
                    
                    logAdminActivity('approve_kyc', "Approved KYC ID: $kyc_id");
                    break;
                    
                case 'reject':
                    $stmt = $pdo->prepare("UPDATE kyc_verification SET status = 'rejected', admin_note = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$admin_note, $kyc_id]);
                    
                    // Update user KYC status
                    $stmt = $pdo->prepare("UPDATE users SET kyc_verified = 0 WHERE id = (SELECT user_id FROM kyc_verification WHERE id = ?)");
                    $stmt->execute([$kyc_id]);
                    
                    logAdminActivity('reject_kyc', "Rejected KYC ID: $kyc_id");
                    break;
            }
            
            // Redirect to refresh the page
            $redirect_url = "kyc.php?page=$page";
            if ($status_filter) $redirect_url .= "&status=$status_filter";
            header('Location: ' . $redirect_url);
            exit;
        } catch (Exception $e) {
            $error = 'Action failed. Please try again.';
        }
    }
}

// Get statistics
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM kyc_verification WHERE status = 'pending'");
    $pending_count = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM kyc_verification WHERE status = 'approved'");
    $approved_count = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM kyc_verification WHERE status = 'rejected'");
    $rejected_count = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE kyc_verified = 1");
    $verified_users = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE kyc_verified = 0");
    $unverified_users = $stmt->fetchColumn();
} catch (Exception $e) {
    $pending_count = $approved_count = $rejected_count = 0;
    $verified_users = $unverified_users = 0;
}

// Include header
include 'includes/header.php';
?>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">KYC Management</h1>
        <p class="page-subtitle">Review and approve user identity verification requests</p>
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
                <div class="stat-icon users">👤</div>
                <div class="stat-info">
                    <h3>Verified Users</h3>
                    <div class="stat-value"><?php echo number_format($verified_users); ?></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon users">🔒</div>
                <div class="stat-info">
                    <h3>Unverified Users</h3>
                    <div class="stat-value"><?php echo number_format($unverified_users); ?></div>
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
            
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="kyc.php" class="btn btn-secondary">Clear</a>
        </form>
    </div>
    
    <!-- KYC Table -->
    <div class="table-container">
        <div class="content-header">
            <h2 class="content-title">All KYC Requests</h2>
        </div>
        
        <?php if (empty($kyc_requests)): ?>
            <p style="color: var(--text_color_L2); text-align: center; padding: 40px;">No KYC requests found</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Document Type</th>
                        <th>Document Number</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kyc_requests as $kyc): ?>
                        <tr>
                            <td><?php echo $kyc['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($kyc['username']); ?></strong>
                                <br><small><?php echo htmlspecialchars($kyc['phone']); ?></small>
                                <br><small><?php echo htmlspecialchars($kyc['email']); ?></small>
                            </td>
                            <td>
                                <span class="status-badge status-pending">
                                    <?php echo ucfirst($kyc['document_type'] ?? 'N/A'); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($kyc['document_number'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="status-badge 
                                    <?php 
                                    switch($kyc['status']) {
                                        case 'approved': echo 'status-approved'; break;
                                        case 'rejected': echo 'status-rejected'; break;
                                        default: echo 'status-pending';
                                    }
                                    ?>">
                                    <?php echo ucfirst($kyc['status']); ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($kyc['created_at']); ?></td>
                            <td>
                                <?php if ($kyc['status'] === 'pending'): ?>
                                    <div style="display: flex; gap: 5px;">
                                        <button type="button" class="btn btn-success" style="padding: 6px 12px; font-size: 0.8rem;" 
                                                onclick="showActionModal('approve', <?php echo $kyc['id']; ?>, '<?php echo htmlspecialchars($kyc['username']); ?>')">
                                            Approve
                                        </button>
                                        <button type="button" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.8rem;" 
                                                onclick="showActionModal('reject', <?php echo $kyc['id']; ?>, '<?php echo htmlspecialchars($kyc['username']); ?>')">
                                            Reject
                                        </button>
                                        <button type="button" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;" 
                                                onclick="viewKYCDetails(<?php echo $kyc['id']; ?>)">
                                            View
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <small style="color: var(--text_color_L3);">
                                        <?php echo $kyc['admin_note'] ? 'Note: ' . htmlspecialchars($kyc['admin_note']) : 'No note'; ?>
                                    </small>
                                    <br>
                                    <button type="button" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.7rem; margin-top: 5px;" 
                                            onclick="viewKYCDetails(<?php echo $kyc['id']; ?>)">
                                        View Details
                                    </button>
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
                        <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>" class="btn btn-secondary">Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>" class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-secondary'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>" class="btn btn-secondary">Next</a>
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
            <input type="hidden" name="kyc_id" id="modalKycId">
            
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

<!-- KYC Details Modal -->
<div id="kycDetailsModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background: var(--bg_color_L2); margin: 5% auto; padding: 25px; border-radius: 15px; width: 90%; max-width: 800px; border: 2px solid var(--Dividing-line_color); max-height: 80vh; overflow-y: auto;">
        <h3 style="color: var(--text_color_L1); margin-bottom: 20px;">KYC Details</h3>
        
        <div id="kycDetailsContent">
            <!-- Content will be loaded here -->
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <button type="button" class="btn btn-secondary" onclick="closeKYCDetailsModal()">Close</button>
        </div>
    </div>
</div>

<script>
function showActionModal(action, kycId, username) {
    const modal = document.getElementById('actionModal');
    const title = document.getElementById('modalTitle');
    const actionInput = document.getElementById('modalAction');
    const kycIdInput = document.getElementById('modalKycId');
    const submitBtn = document.getElementById('modalSubmitBtn');
    
    actionInput.value = action;
    kycIdInput.value = kycId;
    
    if (action === 'approve') {
        title.textContent = `Approve KYC for ${username}`;
        submitBtn.textContent = 'Approve';
        submitBtn.className = 'btn btn-success';
    } else {
        title.textContent = `Reject KYC for ${username}`;
        submitBtn.textContent = 'Reject';
        submitBtn.className = 'btn btn-danger';
    }
    
    modal.style.display = 'block';
}

function closeModal() {
    document.getElementById('actionModal').style.display = 'none';
    document.getElementById('admin_note').value = '';
}

function viewKYCDetails(kycId) {
    // This would typically make an AJAX call to get KYC details
    // For now, we'll show a placeholder
    const modal = document.getElementById('kycDetailsModal');
    const content = document.getElementById('kycDetailsContent');
    
    content.innerHTML = `
        <div style="color: var(--text_color_L2); text-align: center; padding: 40px;">
            <p>KYC Details for ID: ${kycId}</p>
            <p>This would show the actual KYC documents and details.</p>
            <p>In a real implementation, this would load via AJAX.</p>
        </div>
    `;
    
    modal.style.display = 'block';
}

function closeKYCDetailsModal() {
    document.getElementById('kycDetailsModal').style.display = 'none';
}

// Close modals when clicking outside
window.onclick = function(event) {
    const actionModal = document.getElementById('actionModal');
    const kycDetailsModal = document.getElementById('kycDetailsModal');
    
    if (event.target === actionModal) {
        closeModal();
    }
    if (event.target === kycDetailsModal) {
        closeKYCDetailsModal();
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