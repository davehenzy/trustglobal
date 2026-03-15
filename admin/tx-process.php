<?php
require_once '../includes/admin-check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['id']) && isset($data['status'])) {
        $id = (int)$data['id'];
        $status = $data['status'];
        
        try {
            $pdo->beginTransaction();

            // Fetch transaction details
            $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ?");
            $stmt->execute([$id]);
            $tx = $stmt->fetch();

            if (!$tx) {
                throw new Exception("Transaction not found");
            }

            // If status is being changed to Completed and it wasn't already completed
            if ($status == 'Completed' && $tx['status'] != 'Completed') {
                if ($tx['type'] == 'Deposit' || $tx['type'] == 'Credit') {
                    // Update user balance
                    $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                    $stmt->execute([$tx['amount'], $tx['user_id']]);
                }
                // Note: For Withdrawals/Transfers, the balance is usually deducted at the time of request.
                // If it wasn't, we would deduct it here. For now, let's focus on Deposits.
            }

            // Update transaction status
            $stmt = $pdo->prepare("UPDATE transactions SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            
            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    }
    exit();
}
?>
