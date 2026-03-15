<?php
// SwiftCapital Login Logic
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = cleanInput($_POST['email']); // This is the field for username/email
    $password = $_POST['password'];

    $errors = [];

    if (empty($login_input) || empty($password)) {
        $errors[] = "Please enter your credentials.";
    }

    if (empty($errors)) {
        // Find user by email or username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$login_input, $login_input]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Check status
            if ($user['status'] == 'Blocked' || $user['status'] == 'Deactivated') {
                $errors[] = "Your account is restricted. Contact support.";
            } else {
                // Login Success!
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['lastname'] = $user['lastname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['account_number'] = $user['account_number'];
                $_SESSION['balance'] = $user['balance'];
                $_SESSION['kyc_status'] = $user['kyc_status'];
                $_SESSION['user_name'] = $user['name']; // Legacy support
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] == 'Admin') {
                    header("Location: admin/index.php");
                } else {
                    $_SESSION['pin_verified'] = false;
                    header("Location: users/pin.php");
                }
                exit();
            }
        } else {
            $errors[] = "Invalid login credentials.";
        }
    }

    // If there are errors, redirect back to login
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: login.php");
        exit();
    }
}
?>
