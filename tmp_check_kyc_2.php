<?php
require 'c:/xampp/htdocs/swiftcap/includes/db.php';
$stmt = $pdo->query("DESCRIBE kyc_verifications");
$out = "";
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $out .= $row['Field'] . " - " . $row['Type'] . "\n";
}
file_put_contents('c:/xampp/htdocs/swiftcap/tmp_kyc_desc.txt', $out);
?>
