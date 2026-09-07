<?php
require_once 'config.php';
requireAdminLogin();

// Set page title
$page_title = 'Activity Logs';

// Get filters
$action_filter = $_GET['action'] ?? '';
$admin_filter = $_GET['admin'] ?? '';
$date_filter = $_GET['date'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

// Build query conditions
$where_conditions = [];
$params = [];

if ($action_filter) {
    $where_conditions[] = "l.action = ?";
    $params[] = $action_filter;
}

if ($admin_filter) {
    $where_conditions[] = "a.username LIKE ?";
    $params[] = "%$admin_filter%";
}

if ($date_filter) {
    $where_conditions[] = "DATE(l.created_at) = ?";
    $params[] = $date_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total logs count
try {
    $count_query = "
        SELECT COUNT(*) 
        FROM admin_activity_logs l 
        LEFT JOIN users a ON l.admin_id = a.id 
        $where_clause
    ";
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_logs = $stmt->fetchColumn();
    $total_pages = ceil($total_logs / $limit);
} catch (Exception $e) {
    $total_logs = 0;
    $total_pages = 0;
}

// Get logs
try {
    $query = "
        SELECT l.*, a.username as admin_username 
        FROM admin_activity_logs l 
        LEFT JOIN users a ON l.admin_id = a.id 
        $where_clause
        ORDER BY l.created_at DESC 
        LIMIT $limit OFFSET $offset
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $logs = $stmt->fetchAll();
} catch (Exception $e) {
    $logs = [];
    $error = 'Failed to load logs: ' . $e->getMessage();
}

// Get unique actions for filter
try {
    $stmt = $pdo->query("SELECT DISTINCT action FROM admin_activity_logs ORDER BY action");
    $actions = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $actions = [];
}

// Get unique admins for filter
try {
    $stmt = $pdo->query("SELECT DISTINCT username FROM users WHERE is_admin = 1 ORDER BY username");
    $admins = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $admins = [];
}

// Handle log export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="activity_logs_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Admin', 'Action', 'Description', 'IP Address', 'User Agent', 'Created At']);
    
    try {
        $export_query = "
            SELECT l.*, a.username as admin_username 
            FROM admin_activity_logs l 
            LEFT JOIN users a ON l.admin_id = a.id 
            $where_clause
            ORDER BY l.created_at DESC
        ";
        $stmt = $pdo->prepare($export_query);
        $stmt->execute($params);
        $export_logs = $stmt->fetchAll();
        
        foreach ($export_logs as $log) {
            fputcsv($output, [
                $log['id'],
                $log['admin_username'] ?? 'Unknown',
                $log['action'],
                $log['description'],
                $log['ip_address'],
                $log['user_agent'],
                $log['created_at']
            ]);
        }
    } catch (Exception $e) {
        fputcsv($output, ['Error', 'Failed to export logs', $e->getMessage()]);
    }
    
    fclose($output);
    exit;
}

// Include header
include 'includes/header.php';
?>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">Activity Logs</h1>
        <p class="page-subtitle">Monitor admin activities and system events</p>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <!-- Filters -->
    <div class="table-container" style="margin-bottom: 20px;">
        <div class="content-header">
            <h2 class="content-title">Filters</h2>
        </div>
        
        <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="action" class="form-label">Action</label>
                <select name="action" id="action" class="form-control" style="width: 150px;">
                    <option value="">All Actions</option>
                    <?php foreach ($actions as $action): ?>
                        <option value="<?php echo htmlspecialchars($action); ?>" <?php echo $action_filter === $action ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($action); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="admin" class="form-label">Admin</label>
                <select name="admin" id="admin" class="form-control" style="width: 150px;">
                    <option value="">All Admins</option>
                    <?php foreach ($admins as $admin): ?>
                        <option value="<?php echo htmlspecialchars($admin); ?>" <?php echo $admin_filter === $admin ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($admin); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control" value="<?php echo $date_filter; ?>" style="width: 150px;">
            </div>
            
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="logs.php" class="btn btn-secondary">Clear</a>
            <a href="logs.php?<?php echo http_build_query(array_merge($_GET, ['export' => 'csv'])); ?>" class="btn btn-success">Export CSV</a>
        </form>
    </div>
    
    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon logs">📝</div>
                <div class="stat-info">
                    <h3>Total Logs</h3>
                    <div class="stat-value"><?php echo number_format($total_logs); ?></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon users">👥</div>
                <div class="stat-info">
                    <h3>Active Admins</h3>
                    <div class="stat-value"><?php echo number_format(count($admins)); ?></div>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon actions">⚡</div>
                <div class="stat-info">
                    <h3>Unique Actions</h3>
                    <div class="stat-value"><?php echo number_format(count($actions)); ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Logs Table -->
    <div class="table-container">
        <div class="content-header">
            <h2 class="content-title">Activity Logs</h2>
            <div class="content-actions">
                <span class="text-muted">Showing <?php echo number_format($offset + 1); ?> - <?php echo number_format(min($offset + $limit, $total_logs)); ?> of <?php echo number_format($total_logs); ?> logs</span>
            </div>
        </div>
        
        <?php if (empty($logs)): ?>
            <p style="color: var(--text_color_L2); text-align: center; padding: 40px;">No logs found</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Admin</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Created</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?php echo $log['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($log['admin_username'] ?? 'Unknown'); ?></strong>
                                </td>
                                <td>
                                    <span class="status-badge status-pending">
                                        <?php echo htmlspecialchars($log['action']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="max-width: 300px; word-wrap: break-word;">
                                        <?php echo htmlspecialchars($log['description']); ?>
                                    </div>
                                </td>
                                <td>
                                    <code><?php echo htmlspecialchars($log['ip_address']); ?></code>
                                </td>
                                <td><?php echo formatDate($log['created_at']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-secondary" 
                                            onclick="showLogDetails(<?php echo htmlspecialchars(json_encode($log)); ?>)">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div style="display: flex; justify-content: center; margin-top: 30px; gap: 10px;">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&action=<?php echo $action_filter; ?>&admin=<?php echo $admin_filter; ?>&date=<?php echo $date_filter; ?>" class="btn btn-secondary">Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>&action=<?php echo $action_filter; ?>&admin=<?php echo $admin_filter; ?>&date=<?php echo $date_filter; ?>" class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-secondary'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&action=<?php echo $action_filter; ?>&admin=<?php echo $admin_filter; ?>&date=<?php echo $date_filter; ?>" class="btn btn-secondary">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Log Details Modal -->
<div id="logModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background: var(--bg_color_L2); margin: 10% auto; padding: 25px; border-radius: 15px; width: 90%; max-width: 600px; border: 2px solid var(--Dividing-line_color);">
        <h3 style="color: var(--text_color_L1); margin-bottom: 20px;">Log Details</h3>
        
        <div id="logDetails" style="color: var(--text_color_L1);">
            <!-- Log details will be populated here -->
        </div>
        
        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
            <button type="button" class="btn btn-secondary" onclick="closeLogModal()">Close</button>
        </div>
    </div>
</div>

<script>
function showLogDetails(log) {
    const modal = document.getElementById('logModal');
    const details = document.getElementById('logDetails');
    
    details.innerHTML = `
        <div style="margin-bottom: 15px;">
            <strong>ID:</strong> ${log.id}
        </div>
        <div style="margin-bottom: 15px;">
            <strong>Admin:</strong> ${log.admin_username || 'Unknown'}
        </div>
        <div style="margin-bottom: 15px;">
            <strong>Action:</strong> <span class="status-badge status-pending">${log.action}</span>
        </div>
        <div style="margin-bottom: 15px;">
            <strong>Description:</strong><br>
            <div style="background: var(--bg_color_L1); padding: 10px; border-radius: 8px; margin-top: 5px;">
                ${log.description || 'No description'}
            </div>
        </div>
        <div style="margin-bottom: 15px;">
            <strong>IP Address:</strong> <code>${log.ip_address}</code>
        </div>
        <div style="margin-bottom: 15px;">
            <strong>User Agent:</strong><br>
            <div style="background: var(--bg_color_L1); padding: 10px; border-radius: 8px; margin-top: 5px; font-size: 0.9em; word-wrap: break-word;">
                ${log.user_agent || 'Unknown'}
            </div>
        </div>
        <div style="margin-bottom: 15px;">
            <strong>Created At:</strong> ${new Date(log.created_at).toLocaleString()}
        </div>
    `;
    
    modal.style.display = 'block';
}

function closeLogModal() {
    document.getElementById('logModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('logModal');
    if (event.target === modal) {
        closeLogModal();
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

.text-muted {
    color: var(--text_color_L2);
    font-size: 0.9em;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 0.8rem;
}
</style>

<?php include 'includes/footer.php'; ?> 