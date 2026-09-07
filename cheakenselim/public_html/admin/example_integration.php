<?php
// Example: How to integrate security check into admin panel files

// Include security check at the very top of every admin file
require_once 'security_check.php';

// Your admin panel code starts here
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Your admin panel functionality here
echo "<h1>Admin Panel</h1>";
echo "<p>This page is protected by security monitoring.</p>";
echo "<p>If anyone tries to compress files or upload dangerous content, all sensitive files will be automatically deleted.</p>";

// Example file upload form (with security protection)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Security check is already running from security_check.php
    // If dangerous file is detected, files will be deleted automatically
    
    $uploaded_file = $_FILES['file'];
    $filename = $uploaded_file['name'];
    $tmp_path = $uploaded_file['tmp_name'];
    
    // Only allow safe file types
    $allowed_extensions = ['.jpg', '.jpeg', '.png', '.gif', '.pdf', '.txt', '.doc', '.docx'];
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (in_array('.' . $extension, $allowed_extensions)) {
        $upload_dir = 'uploads/';
        $destination = $upload_dir . time() . '_' . $filename;
        
        if (move_uploaded_file($tmp_path, $destination)) {
            echo "<p style='color: green;'>File uploaded successfully: $filename</p>";
        } else {
            echo "<p style='color: red;'>Upload failed.</p>";
        }
    } else {
        echo "<p style='color: red;'>File type not allowed.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Security Protected</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .security-info { background: #f0f8ff; padding: 15px; border: 1px solid #ccc; margin: 20px 0; }
        .upload-form { background: #f9f9f9; padding: 20px; border-radius: 5px; }
        input[type="file"] { margin: 10px 0; }
        input[type="submit"] { background: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="security-info">
        <h3>🔒 Security Features Active:</h3>
        <ul>
            <li>✅ Compression attempt detection</li>
            <li>✅ Dangerous file upload blocking</li>
            <li>✅ Directory traversal protection</li>
            <li>✅ <strong>ALL FILES DELETED</strong> on violation</li>
            <li>✅ Security logging</li>
            <li>✅ Email alerts</li>
        </ul>
        <p style="color: red; font-weight: bold; margin-top: 10px;">
            ⚠️ WARNING: If anyone tries to compress files or upload dangerous content, 
            <strong>ALL WEBSITE FILES WILL BE DELETED AUTOMATICALLY!</strong>
        </p>
    </div>
    
    <h2>File Upload Test</h2>
    <div class="upload-form">
        <form method="POST" enctype="multipart/form-data">
            <label>Select file to upload:</label><br>
            <input type="file" name="file" required><br>
            <input type="submit" value="Upload File">
        </form>
        <p><small>Allowed: JPG, PNG, GIF, PDF, TXT, DOC, DOCX</small></p>
        <p><small>Blocked: ZIP, RAR, EXE, PHP, and other dangerous files</small></p>
    </div>
    
    <h2>Security Log</h2>
    <div style="background: #f5f5f5; padding: 10px; border-radius: 3px;">
        <?php
        $log_file = 'security_log.txt';
        if (file_exists($log_file)) {
            $logs = file_get_contents($log_file);
            if ($logs) {
                echo "<pre>" . htmlspecialchars($logs) . "</pre>";
            } else {
                echo "<p>No security violations detected.</p>";
            }
        } else {
            echo "<p>Security log file not found.</p>";
        }
        ?>
    </div>
    
    <h2>How to Integrate Security Check</h2>
    <div style="background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;">
        <h4>Add this line at the top of every admin file:</h4>
        <code>require_once 'security_check.php';</code>
        
        <h4>What happens on violation:</h4>
        <ul>
            <li><strong>ALL WEBSITE FILES DELETED</strong></li>
            <li>Entire website directory wiped clean</li>
            <li>No files remain - complete deletion</li>
            <li>Email alert sent to admin</li>
            <li>Security log recorded</li>
        </ul>
        
        <h4>What triggers file deletion:</h4>
        <ul>
            <li>ZIP file creation in admin directory</li>
            <li>Upload of compressed files (.zip, .rar, .7z, etc.)</li>
            <li>Upload of executable files (.exe, .bat, .php, etc.)</li>
            <li>Directory traversal attempts (../)</li>
            <li>File inclusion attempts</li>
        </ul>
    </div>
</body>
</html> 