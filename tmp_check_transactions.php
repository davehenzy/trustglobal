<?php
require_once 'includes/db.php';
try {
    $stmt = $pdo->query("DESCRIBE transactions");
    echo "Transactions table exists. Columns:\n";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Transactions table does not exist or error: " . $e->getMessage();
}
?>
