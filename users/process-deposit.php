<?php
require_once '../includes/user-check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $method = htmlspecialchars($_POST['method']);
    $txn_hash = 'TXN' . strtoupper(substr(md5(time() . $user_id), 0, 10));
    
    // Handle File Upload
    $proof_path = '';
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] == 0) {
        $target_dir = "../uploads/proofs/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION);
        $file_name = "proof_" . time() . "_" . $user_id . "." . $file_ext;
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES['proof']['tmp_name'], $target_file)) {
            $proof_path = "uploads/proofs/" . $file_name;
        }
    }

    try {
        // Insert transaction with Pending status
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, type, method, status, txn_hash, narration, proof, created_at) VALUES (?, ?, 'Deposit', ?, 'Pending', ?, ?, ?, NOW())");
        $stmt->execute([
            $user_id,
            $amount,
            $method,
            $txn_hash,
            'Deposit via ' . $method,
            $proof_path
        ]);

        $_SESSION['success_msg'] = "Deposit initiated successfully. It will reflect in your balance once approved by the admin.";
        header("Location: transactions.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error_msg'] = "Error initiating deposit: " . $e->getMessage();
        header("Location: deposit.php");
        exit();
    }
}
?>
