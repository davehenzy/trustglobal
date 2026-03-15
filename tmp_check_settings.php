<?php
require_once 'includes/db.php';
try {
    $stmt = $pdo->query("DESCRIBE settings");
    echo "Settings table exists. Columns:\n";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Settings table does not exist or error: " . $e->getMessage();
}
?>
