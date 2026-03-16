<?php
require_once 'c:/xampp/htdocs/swiftcap/includes/db.php';
$stmt = $pdo->prepare("SELECT id, name, lastname, profile_pic FROM users WHERE account_number = ?");
$stmt->execute(['3083493855']);
$u = $stmt->fetch();
echo "User found: " . ($u ? "Yes" : "No") . "\n";
print_r($u);
?>
