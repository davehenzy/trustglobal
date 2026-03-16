<?php
require_once 'c:/xampp/htdocs/swiftcap/includes/db.php';
$pdo->exec("ALTER TABLE users ADD COLUMN profile_pic VARCHAR(255) DEFAULT NULL AFTER email");
echo "Column profile_pic added successfully.";
?>
