<?php
// SwiftCapital Registration Logic
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and clean input
    $name = cleanInput($_POST['name']);
    $middlename = cleanInput($_POST['middlename']);
    $lastname = cleanInput($_POST['lastname']);
    $username = cleanInput($_POST['username']);
    $email = cleanInput($_POST['email']);
    $phone = cleanInput($_POST['phone']);
    $country = cleanInput($_POST['country']);
    $account_type = cleanInput($_POST['accounttype']);
    $pin = cleanInput($_POST['pin']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirmation'];

    // Basic Validation
    $errors = [];

    if (empty($name) || empty($lastname) || empty($username) || empty($email) || empty($password)) {
        $errors[] = "All required fields must be filled.";
    }

    if ($password !== $password_confirm) {
        $errors[] = "Passwords do not match.";
    }

    // Check if email or username exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        $errors[] = "Email or Username already exists.";
    }

    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Generate a random account number (e.g., 10 digits)
        $account_number = '30' . mt_rand(10000000, 99999999);

        // Insert into database
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, middlename, lastname, username, email, password, pin, phone, country, account_type, account_number, balance, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0.00, 'Pending')");
            $stmt->execute([$name, $middlename, $lastname, $username, $email, $hashed_password, $pin, $phone, $country, $account_type, $account_number]);

            // Success! Set session or redirect
            $_SESSION['success_message'] = "Registration successful! Your account is pending activation.";
            header("Location: login.php");
            exit();

        } catch (PDOException $e) {
            $errors[] = "A system error occurred. Please try again later.";
        }
    }

    // If there are errors, we need to pass them back to the registration page
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Keep data to refill form
        header("Location: register.php");
        exit();
    }
}
?>
