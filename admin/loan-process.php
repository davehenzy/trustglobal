<?php
require_once '../includes/admin-check.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
    exit();
}

$loan_id = $data['id'];
$status = $data['status'];

try {
    $pdo->beginTransaction();

    // 1. Fetch loan details
    $stmt = $pdo->prepare("SELECT * FROM loans WHERE id = ?");
    $stmt->execute([$loan_id]);
    $loan = $stmt->fetch();

    if (!$loan) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Loan not found.']);
        exit();
    }

    if ($loan['status'] != 'Pending') {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Loan already processed.']);
        exit();
    }

    // 2. Update loan status
    $stmt = $pdo->prepare("UPDATE loans SET status = ? WHERE id = ?");
    $stmt->execute([$status, $loan_id]);

    // 3. If Disbursed, credit user balance and add transaction
    if ($status == 'Disbursed') {
        $amount = $loan['amount'];
        $user_id = $loan['user_id'];

        // Credit User
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $user_id]);

        // Add Transaction
        $narration = "Loan Disbursement (#LN-" . str_pad($loan_id, 5, '0', STR_PAD_LEFT) . ")";
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, narration) VALUES (?, 'Credit', ?, 'Completed', ?)");
        $stmt->execute([$user_id, $amount, $narration]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Loan status updated to ' . $status]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
