<?php
require_once 'includes/db.php';
$admins = $pdo->query("SELECT id FROM users WHERE role IN ('Super Admin', 'Sub-Admin')")->fetchAll(PDO::FETCH_COLUMN);

$notifications = [
    ['New User Registered', 'A new client, David Ajibulu, has just registered.', 'System'],
    ['Loan Request', 'New loan application #L-9021 awaiting review.', 'Loan'],
    ['KYC Submission', 'Applicant Sarah Connor submitted documents for verification.', 'KYC'],
    ['Security Alert', 'Multiple failed login attempts detected from IP 192.168.1.5', 'System'],
    ['Transaction Success', 'Institutional wire transfer of $45,000.00 confirmed.', 'Transaction']
];

foreach ($admins as $admin_id) {
    foreach ($notifications as $n) {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, is_read) VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$admin_id, $n[0], $n[1], $n[2]]);
    }
}
echo "Notifications seeded for all admins.";
?>
