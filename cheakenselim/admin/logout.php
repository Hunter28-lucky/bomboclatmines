<?php
require_once 'config.php';

// Log activity before destroying session
if (isAdminLoggedIn()) {
    logAdminActivity('logout', 'Admin logged out');
}

// Destroy all session data
session_destroy();

// Redirect to login page
header('Location: index.php');
exit;
?> 