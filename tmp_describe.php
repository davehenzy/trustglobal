<?php
error_reporting(0);
require 'c:/xampp/htdocs/swiftcap/includes/db.php';
$tables = ['loans', 'support_tickets'];
$out = "";
foreach($tables as $table) {
    $out .= "--- $table ---\n";
    $stmt = $pdo->query("DESCRIBE $table");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $out .= $row['Field'] . "\n";
    }
}
file_put_contents('c:/xampp/htdocs/swiftcap/tmp_describe_output.txt', $out);
?>
