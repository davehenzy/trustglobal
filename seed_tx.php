<?php
require_once 'includes/db.php';

$admin_id = 1;

$samples = [
    ['type' => 'Deposit', 'amount' => 5000.00, 'method' => 'Wire Transfer', 'status' => 'Completed', 'narration' => 'Initial deposit'],
    ['type' => 'Withdrawal', 'amount' => 200.00, 'method' => 'ATM Withdrawal', 'status' => 'Completed', 'narration' => 'Cash out'],
    ['type' => 'Deposit', 'amount' => 12500.00, 'method' => 'Crypto Transfer', 'status' => 'Pending', 'narration' => 'Crypto investment'],
    ['type' => 'Transfer', 'amount' => 1000.00, 'method' => 'Internal Transfer', 'status' => 'Completed', 'narration' => 'Payment to client'],
];

foreach ($samples as $s) {
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, method, status, narration, txn_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $hash = 'TX-' . strtoupper(substr(md5(uniqid()), 0, 8));
    $stmt->execute([$admin_id, $s['type'], $s['amount'], $s['method'], $s['status'], $s['narration'], $hash]);
}

echo "Inserted sample transactions.\n";
?>
