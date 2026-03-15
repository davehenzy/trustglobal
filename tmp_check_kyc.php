<?php
require 'c:/xampp/htdocs/swiftcap/includes/db.php';
$stmt = $pdo->query("SHOW TABLES LIKE 'kyc_verifications'");
if ($stmt->fetch()) {
    echo "TABLE EXISTS\n";
    $stmt = $pdo->query("DESCRIBE kyc_verifications");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} else {
    echo "TABLE MISSING\n";
}
?>
