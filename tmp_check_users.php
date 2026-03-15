<?php
require_once 'includes/db.php';
try {
    $stmt = $pdo->query("DESCRIBE users");
    echo "Users table exists. Columns:\n";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Users table does not exist or error: " . $e->getMessage();
}
?>
