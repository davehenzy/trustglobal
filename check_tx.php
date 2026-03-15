<?php
require_once 'includes/db.php';

$count = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
echo "Total Transactions: $count\n";

if ($count > 0) {
    $stmt = $pdo->query("SELECT t.*, u.name, u.lastname FROM transactions t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC LIMIT 5");
    $rows = $stmt->fetchAll();
    print_r($rows);
} else {
    echo "No transactions found.\n";
}

// Stats
$today = date('Y-m-d');
$today_inflow = $pdo->query("SELECT SUM(amount) FROM transactions WHERE DATE(created_at) = '$today' AND (type='Deposit' OR type='Credit') AND status='Completed'")->fetchColumn() ?: 0;
echo "Today's Inflow: $today_inflow\n";

$pending_count = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status='Pending'")->fetchColumn();
echo "Pending Transactions: $pending_count\n";
?>
