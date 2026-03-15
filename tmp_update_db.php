<?php
require 'c:/xampp/htdocs/swiftcap/includes/db.php';
try {
    $pdo->exec("ALTER TABLE transactions ADD COLUMN proof VARCHAR(255) DEFAULT NULL AFTER txn_hash");
    echo "Successfully added 'proof' column to transactions table.";
} catch (Exception $e) {
    echo "Error or column already exists: " . $e->getMessage();
}
?>
