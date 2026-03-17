<?php
// SwiftCapital Database Connection

// Define Security Constant
if (!defined('SWIFTCAP_SECURE')) {
    define('SWIFTCAP_SECURE', true);
}

// Set Security Headers
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

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

// Session Security: Regenerate ID occasionally (e.g., every 30 mins)
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Global CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Function to verify CSRF token
function verifyCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
