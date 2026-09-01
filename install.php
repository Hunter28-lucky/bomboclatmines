<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: text/html; charset=utf-8');

$status = [];
$error = null;

try {
    // 1. PHP Version
    $status[] = ['title' => 'PHP Version', 'ok' => version_compare(PHP_VERSION, '7.4.0', '>='), 'msg' => PHP_VERSION];

    // 2. PDO Support
    $hasPdo = extension_loaded('pdo');
    $status[] = ['title' => 'PDO Extension', 'ok' => $hasPdo, 'msg' => $hasPdo ? 'Installed' : 'Missing PDO extension'];

    // 3. Uploads directory write permissions
    $uploadOk = is_writable(UPLOADS_DIR);
    $status[] = ['title' => 'Uploads Directory', 'ok' => $uploadOk, 'msg' => $uploadOk ? 'Writable' : 'Permission needed on ' . UPLOADS_DIR];

    // 4. Connect to database
    $db = getDB();
    $status[] = ['title' => 'Database Connection', 'ok' => true, 'msg' => 'Connected successfully using PDO (' . $db->getAttribute(PDO::ATTR_DRIVER_NAME) . ')'];

    // 5. Seed Admin Account if doesn't exist
    $checkAdmin = $db->prepare("SELECT id FROM users WHERE LOWER(email) = :email");
    $checkAdmin->execute([':email' => strtolower(ADMIN_EMAIL)]);
    if (!$checkAdmin->fetch()) {
        $adminId = generateUUID();
        $adminPass = password_hash('Admin@12345', PASSWORD_BCRYPT);
        
        $db->beginTransaction();
        try {
            $stmt = $db->prepare("
                INSERT INTO users (id, email, password_hash, full_name, avatar_url, is_admin, created_at)
                VALUES (:id, :email, :pass, 'Admin', '', 1, datetime('now'))
            ");
            $stmt->execute([':id' => $adminId, ':email' => strtolower(ADMIN_EMAIL), ':pass' => $adminPass]);
        } catch (Exception $fallback) {
            $stmt = $db->prepare("
                INSERT INTO users (id, email, password_hash, full_name, avatar_url, is_admin, created_at)
                VALUES (:id, :email, :pass, 'Admin', '', 1, NOW())
            ");
            $stmt->execute([':id' => $adminId, ':email' => strtolower(ADMIN_EMAIL), ':pass' => $adminPass]);
        }

        $balStmt = $db->prepare("INSERT INTO users_balance (user_id, balance, topups) VALUES (:uid, 10000.00, 0)");
        $balStmt->execute([':uid' => $adminId]);
        $db->commit();

        $status[] = ['title' => 'Default Admin Account', 'ok' => true, 'msg' => 'Created (Email: ' . ADMIN_EMAIL . ' / Password: Admin@12345)'];
    } else {
        $status[] = ['title' => 'Default Admin Account', 'ok' => true, 'msg' => 'Admin already exists (' . ADMIN_EMAIL . ')'];
    }

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Installation & Health Check - <?= htmlspecialchars(APP_NAME) ?></title>
  <link rel="stylesheet" href="assets/css/app.css">
  <style>
    .install-box {
      max-width: 600px;
      margin: 40px auto;
      background: var(--bg-card);
      backdrop-filter: blur(16px);
      border: 1px solid var(--border-card);
      border-radius: 20px;
      padding: 24px;
    }
    .check-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px;
      border-bottom: 1px solid rgba(71, 85, 105, 0.4);
    }
  </style>
</head>
<body>
  <div class="install-box">
    <h2 style="font-size: 1.5rem; font-weight:800; color: #38bdf8; margin-bottom: 16px;">🚀 Health Check & Database Setup</h2>
    
    <?php if ($error): ?>
      <div style="padding: 14px; background: rgba(239,68,68,0.2); border: 1px solid #ef4444; border-radius: 12px; color: #fca5a5; margin-bottom: 18px;">
        <strong>Error:</strong> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div style="display:flex; flex-direction:column;">
      <?php foreach ($status as $s): ?>
        <div class="check-item">
          <div>
            <div style="font-weight: 700; color: #f8fafc;"><?= htmlspecialchars($s['title']) ?></div>
            <div style="font-size: 0.8rem; color: #94a3b8;"><?= htmlspecialchars($s['msg']) ?></div>
          </div>
          <div>
            <?php if ($s['ok']): ?>
              <span style="color: #34d399; font-weight: 800;">PASS ✔</span>
            <?php else: ?>
              <span style="color: #ef4444; font-weight: 800;">FAIL ✖</span>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div style="margin-top: 24px; display:flex; gap:10px;">
      <a href="index.php" class="btn-primary-action" style="text-decoration:none;">Go To Game</a>
      <a href="admin.php" class="btn-secondary" style="text-decoration:none;">Go To Admin Panel</a>
    </div>
  </div>
</body>
</html>
