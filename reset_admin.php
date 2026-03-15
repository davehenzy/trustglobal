<?php
require_once 'includes/db.php';

$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hashed_password]);
    echo "Admin password updated successfully to: $new_password\n";
    echo "New Hash: $hashed_password\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
