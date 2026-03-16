<?php
require_once 'includes/db.php';

$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    // Reset Super Admin
    $stmt1 = $pdo->prepare("UPDATE users SET password = ? WHERE id = 1");
    $stmt1->execute([$hash]);
    
    // Reset Sub Admin
    $stmt2 = $pdo->prepare("UPDATE users SET password = ? WHERE id = 3");
    $stmt2->execute([$hash]);

    echo "Passwords successfully reset to 'admin123' for both admins.\n";
    echo "Hash used: " . $hash . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
