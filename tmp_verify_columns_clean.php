<?php
error_reporting(0);
require 'c:/xampp/htdocs/swiftcap/includes/db.php';
$tables = ['transactions', 'loans', 'support_tickets'];
foreach($tables as $table) {
    echo "[$table]: ";
    $stmt = $pdo->query("DESCRIBE $table");
    $cols = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cols[] = $row['Field'];
    }
    echo implode(", ", $cols) . "\n";
}
?>
