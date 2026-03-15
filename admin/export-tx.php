<?php
require_once '../includes/db.php';
require_once '../includes/admin-check.php';

$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';

$where_clauses = [];
$params = [];

if ($search) {
    $where_clauses[] = "(t.txn_hash LIKE ? OR u.email LIKE ? OR u.name LIKE ? OR u.lastname LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($type && $type != 'Type') {
    $where_clauses[] = "t.type = ?";
    $params[] = $type;
}

if ($status && $status != 'Status') {
    $status_val = $status == 'Success' ? 'Completed' : ($status == 'Cancelled' ? 'Cancelled' : 'Pending');
    $where_clauses[] = "t.status = ?";
    $params[] = $status_val;
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

$sql = "SELECT t.txn_hash, u.name, u.lastname, u.email, t.amount, t.type, t.status, t.method, t.created_at 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        $where_sql
        ORDER BY t.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filename = "SwiftCap_Transactions_Archive_" . date('Y-m-d_His') . ".csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Header row
fputcsv($output, ['TXN HASH', 'FIRST NAME', 'LAST NAME', 'EMAIL', 'AMOUNT', 'TYPE', 'STATUS', 'METHOD', 'DATE']);

// Data rows
foreach ($results as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
