<?php
// SwiftCapital Database Connection

$host = 'localhost';
$dbname = 'swiftcapital_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to Associative Array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // In a production environment, you should log this error and show a generic message
    die("Database connection failed: " . $e->getMessage());
}

// Function to safely clean input
function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Session start for authentication
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
