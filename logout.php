<?php
// SwiftCapital Logout Script - Enhanced Stability
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Start the session to clear it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session data
$_SESSION = array();

// Expire the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect with multiple fallbacks
if (!headers_sent()) {
    header("Location: login.php");
}

// If headers already sent or Location failed, use HTML/JS fallbacks
echo '<!DOCTYPE html><html><head>';
echo '<meta http-equiv="refresh" content="0;url=login.php">';
echo '<script>window.location.href="login.php";</script>';
echo '</head><body>';
echo 'Redirecting to <a href="login.php">Login Page</a>...';
echo '</body></html>';
exit();
