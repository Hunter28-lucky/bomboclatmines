<?php
require_once 'config.php';

echo "<h2>SQL Query Debug</h2>";

// Test 1: Simple query without JOIN
echo "<h3>Test 1: Simple transactions query (no JOIN)</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM transactions ORDER BY created_at DESC LIMIT 5");
    $results = $stmt->fetchAll();
    echo "<p><strong>Found:</strong> " . count($results) . " transactions</p>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>User ID</th><th>Type</th><th>Amount</th><th>Status</th></tr>";
    foreach ($results as $row) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . $row['type'] . "</td>";
        echo "<td>" . $row['amount'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
}

// Test 2: Check if users exist for these user_ids
echo "<h3>Test 2: Check users table</h3>";
try {
    $stmt = $pdo->query("SELECT DISTINCT user_id FROM transactions");
    $user_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p><strong>User IDs in transactions:</strong> " . implode(', ', $user_ids) . "</p>";
    
    foreach ($user_ids as $user_id) {
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        if ($user) {
            echo "<p>User ID $user_id: " . $user['username'] . " ✅</p>";
        } else {
            echo "<p>User ID $user_id: NOT FOUND ❌</p>";
        }
    }
} catch (Exception $e) {
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
}

// Test 3: Test the actual query with JOIN
echo "<h3>Test 3: Query with LEFT JOIN</h3>";
try {
    $query = "
        SELECT t.*, u.username, u.phone 
        FROM transactions t 
        LEFT JOIN users u ON t.user_id = u.id 
        ORDER BY t.created_at DESC 
        LIMIT 5
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll();
    echo "<p><strong>Found:</strong> " . count($results) . " transactions with JOIN</p>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>User ID</th><th>Username</th><th>Type</th><th>Amount</th><th>Status</th></tr>";
    foreach ($results as $row) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . ($row['username'] ?? 'NULL') . "</td>";
        echo "<td>" . $row['type'] . "</td>";
        echo "<td>" . $row['amount'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='transactions.php'>Back to Transactions</a></p>";
?> 