<?php
require_once '../includes/admin-check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['loan_id'])) {
    $loan_id = $_POST['loan_id'];
    $notes = $_POST['notes'];

    try {
        $stmt = $pdo->prepare("UPDATE loans SET admin_notes = ? WHERE id = ?");
        $stmt->execute([$notes, $loan_id]);
        
        header("Location: loan-view.php?id=" . $loan_id . "&noted=1");
        exit();
    } catch (PDOException $e) {
        die("Error updating notes: " . $e->getMessage());
    }
} else {
    header("Location: loans.php");
    exit();
}
?>
