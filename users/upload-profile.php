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
    
    if ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
        $_SESSION['error_msg'] = "File too large. Maximum 2MB allowed.";
        header("Location: settings.php");
        exit();
    }
    
    // Create uploads directory if not exists
    $upload_dir = '../assets/uploads/profiles/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique name
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
    $target_file = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        // Update database
        $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $stmt->execute([$filename, $user_id]);
        
        // Update session
        $_SESSION['profile_pic'] = $filename;
        
        $_SESSION['success_msg'] = "Profile picture updated successfully.";
    } else {
        $_SESSION['error_msg'] = "Failed to upload file.";
    }
    
    header("Location: settings.php");
    exit();
} else {
    header("Location: settings.php");
    exit();
}
?>
