<?php
require_once 'includes/db.php';

ob_start();
$pw = 'admin123';
$hash = password_hash($pw, PASSWORD_DEFAULT);

echo "Testing string: $pw\n";
echo "Generated hash: $hash\n";
if (password_verify($pw, $hash)) {
    echo "Verification OK immediately after hashing.\n";
} else {
    echo "Verification FAILED immediately after hashing. SOMETHING IS WRONG WITH THE SYSTEM PHP.\n";
}

try {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hash]);
    echo "Database updated.\n";
    
    $stmt = $pdo->prepare("SELECT password FROM users WHERE username = 'admin'");
    $stmt->execute();
    $db_hash = $stmt->fetchColumn();
    echo "Retrieved from DB: $db_hash\n";
    
    if (password_verify($pw, $db_hash)) {
        echo "Verification OK after retrieving from DB.\n";
    } else {
        echo "Verification FAILED after retrieving from DB. Possible DB encoding/truncation issue.\n";
    }
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage();
}
$log = ob_get_clean();
file_put_contents('reset_log_plain.txt', $log);
echo "Log written to reset_log_plain.txt\n";
?>
