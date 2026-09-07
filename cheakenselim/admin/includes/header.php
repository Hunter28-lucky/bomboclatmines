<?php
// Check if user is logged in as admin
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Panel'; ?> - Chicken Road Game</title>
    <?php include 'includes/styles.php'; ?>
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?> 