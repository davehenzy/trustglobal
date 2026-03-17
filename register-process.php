<?php
// SwiftCapital Registration Logic
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || !verifyCSRF($_POST['csrf_token'])) {
        $_SESSION['errors'] = ['Security session expired. Please refresh the page.'];
        header("Location: register.php");
        exit;
    }

    // Collect and clean input
    $name = isset($_POST['name']) ? cleanInput($_POST['name']) : '';
    $middlename = isset($_POST['middlename']) ? cleanInput($_POST['middlename']) : '';
    $lastname = isset($_POST['lastname']) ? cleanInput($_POST['lastname']) : '';
    $username = isset($_POST['username']) ? cleanInput($_POST['username']) : '';
    $email = isset($_POST['email']) ? cleanInput($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? cleanInput($_POST['phone']) : '';
    $country = isset($_POST['country']) ? cleanInput($_POST['country']) : '';
    $account_type = isset($_POST['accounttype']) ? cleanInput($_POST['accounttype']) : 'Savings Account';
    $pin = isset($_POST['pin']) ? cleanInput($_POST['pin']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirmation']) ? $_POST['password_confirmation'] : '';

    // Basic Validation
    $errors = [];

    if (empty($name) || empty($lastname) || empty($username) || empty($email) || empty($password)) {
        $errors[] = "All required fields must be filled.";
    }

    if ($password !== $password_confirm) {
        $errors[] = "Passwords do not match.";
    }

    // Check if email or username exists
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);
            if ($stmt->fetch()) {
                $errors[] = "Email or Username already exists.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database check failed: " . $e->getMessage();
        }
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
            $errors[] = "A system error occurred: " . $e->getMessage();
        }
    }

    // If there are errors, we need to pass them back to the registration page
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST; // Keep data to refill form
        header("Location: register.php");
        exit();
    }
} else {
    // Redirect direct access to register page
    header("Location: register.php");
    exit();
}
