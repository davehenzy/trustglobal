<?php
require_once '../includes/user-check.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
}

// Redirect back if not AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header("Location: index.php");
    exit();
}
?>
