<?php
require_once 'includes/db.php';
try {
    $stmt = $pdo->query("DESCRIBE loans");
    echo "Loans table exists. Columns:\n";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Loans table does not exist or error: " . $e->getMessage();
}
?>
