<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pin = $_POST['pin'];
    $user_id = $_SESSION['user_id'];

    if (empty($pin)) {
        $_SESSION['pin_error'] = "Please enter your PIN.";
        header("Location: pin.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT pin FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $db_pin = $stmt->fetchColumn();

    if ($pin == $db_pin) {
        $_SESSION['pin_verified'] = true;
        
        // Success! Redirect to intended page or index
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['pin_error'] = "Invalid verification PIN. Please try again.";
        header("Location: pin.php");
        exit();
    }
} else {
    header("Location: pin.php");
    exit();
}
?>
