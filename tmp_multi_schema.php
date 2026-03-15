<?php
require 'c:/xampp/htdocs/swiftcap/includes/db.php';
$tables = ['kyc_verifications', 'loans', 'support_tickets', 'transactions', 'users'];
foreach($tables as $table) {
    echo "--- $table ---\n";
    $stmt = $pdo->query("DESCRIBE $table");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}
?>
