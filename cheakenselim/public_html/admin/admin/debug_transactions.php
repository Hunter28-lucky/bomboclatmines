<?php
require_once 'config.php';

echo "<h2>Transaction Debug Information</h2>";

// Check if transactions table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'transactions'");
    $table_exists = $stmt->rowCount() > 0;
    echo "<p><strong>Transactions table exists:</strong> " . ($table_exists ? 'Yes' : 'No') . "</p>";
} catch (Exception $e) {
    echo "<p><strong>Error checking table:</strong> " . $e->getMessage() . "</p>";
}

if ($table_exists) {
    // Get total count
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM transactions");
        $total_count = $stmt->fetchColumn();
        echo "<p><strong>Total transactions:</strong> " . $total_count . "</p>";
    } catch (Exception $e) {
        echo "<p><strong>Error counting transactions:</strong> " . $e->getMessage() . "</p>";
    }

    // Get unique transaction types
    try {
        $stmt = $pdo->query("SELECT DISTINCT type FROM transactions ORDER BY type");
        $types = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p><strong>Transaction types found:</strong></p>";
        echo "<ul>";
        foreach ($types as $type) {
            echo "<li>" . htmlspecialchars($type) . "</li>";
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "<p><strong>Error getting types:</strong> " . $e->getMessage() . "</p>";
    }

    // Get unique statuses
    try {
        $stmt = $pdo->query("SELECT DISTINCT status FROM transactions ORDER BY status");
        $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p><strong>Transaction statuses found:</strong></p>";
        echo "<ul>";
        foreach ($statuses as $status) {
            echo "<li>" . htmlspecialchars($status) . "</li>";
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "<p><strong>Error getting statuses:</strong> " . $e->getMessage() . "</p>";
    }

    // Show sample transactions
    try {
        $stmt = $pdo->query("SELECT * FROM transactions ORDER BY created_at DESC LIMIT 5");
        $sample_transactions = $stmt->fetchAll();
        echo "<p><strong>Sample transactions (latest 5):</strong></p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>User ID</th><th>Type</th><th>Amount</th><th>Status</th><th>Method</th><th>Created</th></tr>";
        foreach ($sample_transactions as $transaction) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($transaction['id']) . "</td>";
            echo "<td>" . htmlspecialchars($transaction['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($transaction['type']) . "</td>";
            echo "<td>" . htmlspecialchars($transaction['amount']) . "</td>";
            echo "<td>" . htmlspecialchars($transaction['status']) . "</td>";
            echo "<td>" . htmlspecialchars($transaction['method']) . "</td>";
            echo "<td>" . htmlspecialchars($transaction['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "<p><strong>Error getting sample transactions:</strong> " . $e->getMessage() . "</p>";
    }

    // Check if users table exists and has data
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $user_count = $stmt->fetchColumn();
        echo "<p><strong>Total users:</strong> " . $user_count . "</p>";
    } catch (Exception $e) {
        echo "<p><strong>Error counting users:</strong> " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='transactions.php'>Back to Transactions</a></p>";
?> 