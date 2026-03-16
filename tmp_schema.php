<?php
require_once 'includes/db.php';

echo "--- TABLES ---\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    echo "$table\n";
}

echo "\n--- USERS SCHEMA ---\n";
$columns = $pdo->query("DESC users")->fetchAll();
foreach ($columns as $col) {
    echo "{$col['Field']} - {$col['Type']} - {$col['Null']} - {$col['Key']} - {$col['Default']} - {$col['Extra']}\n";
}
?>
