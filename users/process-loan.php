<?php
require_once '../includes/db.php';
require_once '../includes/user-check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_loan'])) {
    $user_id = $_SESSION['user_id'];
    $loan_type = $_POST['loan_type'];
    $amount = $_POST['amount'];
    $duration = $_POST['duration'];
    $purpose = $_POST['purpose'];
    $income = $_POST['income'];

    // Basic Validation
    if (empty($loan_type) || empty($amount) || empty($duration) || empty($purpose)) {
        $_SESSION['error_msg'] = "All fields are required.";
        header("Location: loan-application.php");
        exit();
    }

    // Fixed Interest Rate (6.5% as per UI)
    $interest_rate = 6.5;
    $monthly_rate = ($interest_rate / 100) / 12;
    
    // EMI calculation: P * r * (1+r)^n / ((1+r)^n - 1)
    $x = pow(1 + $monthly_rate, $duration);
    $monthly_payable = ($amount * $x * $monthly_rate) / ($x - 1);

    try {
        $stmt = $pdo->prepare("INSERT INTO loans (user_id, loan_type, amount, term_months, interest_rate, monthly_payable, purpose, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$user_id, $loan_type, $amount, $duration, $interest_rate, $monthly_payable, $purpose]);

        $_SESSION['success_msg'] = "Your loan application has been submitted successfully and is now under review.";
        header("Location: loan-history.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Database error: " . $e->getMessage();
        header("Location: loan-application.php");
        exit();
    }
} else {
    header("Location: loan.php");
    exit();
}
?>
