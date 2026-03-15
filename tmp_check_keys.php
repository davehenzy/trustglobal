<?php
error_reporting(0);
require 'c:/xampp/htdocs/swiftcap/includes/db.php';
$tables = ['transactions', 'loans', 'support_tickets', 'users'];
$out = "";
foreach($tables as $table) {
    $out .= "[$table]: ";
    $stmt = $pdo->query("SELECT * FROM $table LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $out .= implode(", ", array_keys($row)) . "\n";
    } else {
        $out .= "EMPTY TABLE\n";
    }
}
file_put_contents('c:/xampp/htdocs/swiftcap/tmp_keys_output_clean.txt', $out);
?>
