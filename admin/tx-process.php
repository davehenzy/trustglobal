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

            // ── Approve: credit deposits / debit pending wires ──
            if ($status == 'Completed' && $tx['status'] != 'Completed') {
                if ($tx['type'] == 'Deposit' || $tx['type'] == 'Credit') {
                    // Credit user balance for deposits
                    $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?")
                        ->execute([$tx['amount'], $tx['user_id']]);
                } elseif (in_array($tx['type'], ['Debit', 'Withdrawal']) && in_array($tx['method'], ['International Wire', 'Crypto Withdrawal', 'PayPal Withdrawal', 'Wise Transfer', 'Cash App', 'Skrill', 'Revolut', 'Venmo', 'Zelle', 'Alipay', 'WeChat'])) {
                    // Deduct balance for international wire (was pending — not deducted yet)
                    $user_bal = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
                    $user_bal->execute([$tx['user_id']]);
                    $current_balance = $user_bal->fetchColumn();
                    if ($current_balance < $tx['amount']) {
                        throw new Exception("Insufficient user balance to approve this wire.");
                    }
                    $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?")
                        ->execute([$tx['amount'], $tx['user_id']]);
                }
            }

            // ── Cancel/Reject: if a pending wire is cancelled, no balance was ever deducted ──


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
