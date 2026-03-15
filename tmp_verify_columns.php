<?php
require 'c:/xampp/htdocs/swiftcap/includes/db.php';
$tables = ['transactions', 'loans', 'support_tickets'];
foreach($tables as $table) {
    echo "--- $table ---\n";
    $stmt = $pdo->query("DESCRIBE $table");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . "\n";
    }
}
?>
