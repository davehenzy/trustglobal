<?php
// Disable session warnings for pure CLI output if possible, or just ignore them
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
require_once 'includes/db.php';

ob_start();
echo "--- DATABASE CHECK ---\n";
try {
    $stmt = $pdo->query("SELECT id, username, email, password, status, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "No users found in database.\n";
    } else {
        echo "Found " . count($users) . " users:\n";
        foreach ($users as $user) {
            echo "ID: " . $user['id'] . " | ";
            echo "Username: [" . $user['username'] . "] | ";
            echo "Email: [" . $user['email'] . "] | ";
            echo "Role: " . $user['role'] . " | ";
            echo "Status: " . $user['status'] . "\n";
            echo "Hash: " . $user['password'] . "\n";
            
            // Test common passwords
            $passwords_to_test = ['admin123', 'password', '123456'];
            foreach ($passwords_to_test as $pw) {
                if (password_verify($pw, $user['password'])) {
                    echo "  -> Verified with: '$pw'\n";
                }
            }
            echo "-----------------------------------\n";
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
$content = ob_get_clean();
file_put_contents('db_check_plain.txt', $content);
echo "Done. Results written to db_check_plain.txt\n";
?>
