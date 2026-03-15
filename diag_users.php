<?php
require_once 'includes/db.php';

try {
    $stmt = $pdo->query("SELECT id, username, email, password, status, role FROM users");
    $users = $stmt->fetchAll();
    echo "Users in database:\n";
    foreach ($users as $user) {
        echo "ID: " . $user['id'] . "\n";
        echo "Username: " . $user['username'] . "\n";
        echo "Hash: " . $user['password'] . "\n";
        
        $test_pw = 'admin123';
        if (password_verify($test_pw, $user['password'])) {
            echo "Password '$test_pw' VERIFIED!\n";
        } else {
            echo "Password '$test_pw' FAILED.\n";
        }
        echo "-------------------\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
