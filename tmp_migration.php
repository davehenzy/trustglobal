<?php
require_once 'includes/db.php';

echo "Adding assigned_admin_id column...\n";
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN assigned_admin_id INT NULL");
    echo "Column added.\n";
} catch (Exception $e) {
    echo "Notice: " . $e->getMessage() . "\n";
}

echo "Updating current admins to Super Admin...\n";
$stmt = $pdo->prepare("UPDATE users SET role = 'Super Admin' WHERE role = 'Admin'");
$stmt->execute();
echo "Updated " . $stmt->rowCount() . " admins.\n";

echo "Admins found:\n";
$stmt = $pdo->query("SELECT id, name, lastname, email, role FROM users WHERE role IN ('Admin', 'Super Admin', 'Sub-Admin')");
while($row = $stmt->fetch()) {
    print_r($row);
}
?>
