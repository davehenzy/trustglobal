<?php
require_once 'db.php';
if(!defined('SWIFTCAP_SECURE')) exit('Direct access prohibited');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch user data if not in session to prevent "Undefined array key" warnings
if (!isset($_SESSION['balance']) || !isset($_SESSION['name'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['name'] = $user['name'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['account_number'] = $user['account_number'];
        $_SESSION['balance'] = $user['balance'];
        $_SESSION['kyc_status'] = $user['kyc_status'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['profile_pic'] = $user['profile_pic'];
    }
}

// Keep balance + kyc_status refreshed on every request
$stmt_bal = $pdo->prepare("SELECT balance, kyc_status, phone, profile_pic FROM users WHERE id = ?");
$stmt_bal->execute([$_SESSION['user_id']]);
$_fresh = $stmt_bal->fetch();
$_SESSION['balance']     = $_fresh['balance'];
$_SESSION['kyc_status']  = $_fresh['kyc_status'];
$_SESSION['phone']       = $_fresh['phone'];
$_SESSION['profile_pic'] = $_fresh['profile_pic'];

// PIN Verification Check for regular users
$current_script = basename($_SERVER['PHP_SELF']);
if ($_SESSION['role'] !== 'Admin' && (!isset($_SESSION['pin_verified']) || $_SESSION['pin_verified'] !== true)) {
    if ($current_script !== 'pin.php' && $current_script !== 'pin-process.php') {
        header("Location: pin.php");
        exit();
    }
}
?>
