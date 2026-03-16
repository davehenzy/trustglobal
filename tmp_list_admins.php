<?php
require_once 'includes/db.php';
$stmt = $pdo->query("SELECT id, name, lastname, email, role FROM users WHERE role LIKE '%Admin%'");
while($row = $stmt->fetch()) {
    echo "ID: {$row['id']} | Name: {$row['name']} {$row['lastname']} | Role: {$row['role']} | Email: {$row['email']}\n";
}
?>
