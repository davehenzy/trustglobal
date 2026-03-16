<?php
require_once 'includes/db.php';
$stmt = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5");
$results = $stmt->fetchAll();
print_r($results);
?>
