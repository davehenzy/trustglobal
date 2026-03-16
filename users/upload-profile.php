<?php
require_once '../includes/db.php';
require_once '../includes/user-check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['profile_pic'];
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['error_msg'] = "Invalid file type. Only JPG, PNG, and WebP allowed.";
        header("Location: settings.php");
        exit();
    }
    
    if ($file['size'] > 20 * 1024 * 1024) { // 20MB limit
        $_SESSION['error_msg'] = "File too large. Maximum 20MB allowed.";
        header("Location: settings.php");
        exit();
    }
    
    // Create uploads directory if not exists
    $upload_dir = '../assets/uploads/profiles/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            $_SESSION['error_msg'] = "Critical: Could not create upload directory. Check permissions.";
            header("Location: settings.php");
            exit();
        }
    }
    
    // Generate unique name
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
    $target_path = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        try {
            // Update database
            $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
            $stmt->execute([$filename, $user_id]);
            
            // Update session
            $_SESSION['profile_pic'] = $filename;
            $_SESSION['success_msg'] = "Profile picture updated successfully.";
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Database error: " . $e->getMessage();
        }
    } else {
        $error = error_get_last();
        $_SESSION['error_msg'] = "Failed to move uploaded file. " . ($error['message'] ?? "");
    }
    
    header("Location: settings.php");
    exit();
} else {
    header("Location: settings.php");
    exit();
}
?>
