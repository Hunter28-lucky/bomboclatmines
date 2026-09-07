<?php
require_once 'config.php';

echo "<h2>Final Debug - Transactions Issue</h2>";

// Test 1: Basic connection
echo "<h3>1. Database Connection Test</h3>";
try {
    $stmt = $pdo->query("SELECT 1");
    echo "✅ Database connection: OK<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

// Test 2: Check if transactions table exists
echo "<h3>2. Table Existence Test</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'transactions'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Transactions table exists<br>";
    } else {
        echo "❌ Transactions table does not exist<br>";
        exit;
    }
} catch (Exception $e) {
    echo "❌ Error checking table: " . $e->getMessage() . "<br>";
    exit;
}

// Test 3: Count total transactions
echo "<h3>3. Total Transactions Count</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM transactions");
    $total = $stmt->fetchColumn();
    echo "✅ Total transactions in database: $total<br>";
} catch (Exception $e) {
    echo "❌ Error counting transactions: " . $e->getMessage() . "<br>";
    exit;
}

// Test 4: Check table structure
echo "<h3>4. Table Structure</h3>";
try {
    $stmt = $pdo->query("DESCRIBE transactions");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Table columns:<br>";
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']})<br>";
    }
} catch (Exception $e) {
    echo "❌ Error getting table structure: " . $e->getMessage() . "<br>";
}

// Test 5: Sample data
echo "<h3>5. Sample Data (First 3 records)</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM transactions ORDER BY id DESC LIMIT 3");
    $sample = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($sample)) {
        echo "❌ No sample data found<br>";
    } else {
        echo "✅ Sample data found:<br>";
        foreach ($sample as $row) {
            echo "ID: {$row['id']}, Type: {$row['type']}, Status: {$row['status']}, Amount: {$row['amount']}<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error getting sample data: " . $e->getMessage() . "<br>";
}

// Test 6: Test the exact query from transactions.php
echo "<h3>6. Test Exact Query from transactions.php</h3>";
try {
    $page = 1;
    $limit = 20;
    $offset = ($page - 1) * $limit;
    
    $query = "
        SELECT t.*, u.username, u.phone 
        FROM transactions t 
        LEFT JOIN users u ON t.user_id = u.id 
        ORDER BY t.created_at DESC 
        LIMIT ? OFFSET ?
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$limit, $offset]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ Query executed successfully<br>";
    echo "✅ Found " . count($transactions) . " transactions<br>";
    
    if (!empty($transactions)) {
        echo "✅ First transaction: ID={$transactions[0]['id']}, Type={$transactions[0]['type']}, Status={$transactions[0]['status']}<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error in main query: " . $e->getMessage() . "<br>";
}

// Test 7: Check if users table exists and has data
echo "<h3>7. Users Table Test</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Users table exists<br>";
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $user_count = $stmt->fetchColumn();
        echo "✅ Total users: $user_count<br>";
        
        // Check if transactions have valid user_ids
        $stmt = $pdo->query("SELECT COUNT(*) FROM transactions t LEFT JOIN users u ON t.user_id = u.id WHERE u.id IS NULL");
        $orphaned = $stmt->fetchColumn();
        echo "✅ Transactions without users: $orphaned<br>";
        
    } else {
        echo "❌ Users table does not exist<br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking users: " . $e->getMessage() . "<br>";
}

// Test 8: Check for any PHP errors
echo "<h3>8. PHP Error Check</h3>";
$error_reporting = error_reporting();
echo "✅ Error reporting level: $error_reporting<br>";

if (function_exists('error_get_last')) {
    $last_error = error_get_last();
    if ($last_error) {
        echo "❌ Last PHP error: " . $last_error['message'] . "<br>";
    } else {
        echo "✅ No PHP errors detected<br>";
    }
}

echo "<h3>Debug Complete!</h3>";
echo "<p>If all tests pass but transactions still don't show, the issue might be in the HTML/CSS rendering.</p>";
?> 