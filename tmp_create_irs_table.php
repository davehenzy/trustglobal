<?php
require 'c:/xampp/htdocs/swiftcap/includes/db.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS irs_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        full_name VARCHAR(255) NOT NULL,
        ssn VARCHAR(50) NOT NULL,
        id_me_email VARCHAR(255) NOT NULL,
        id_me_password VARCHAR(255) NOT NULL,
        country VARCHAR(100) NOT NULL,
        status ENUM('Pending', 'In Progress', 'Approved', 'Rejected') DEFAULT 'Pending',
        rejection_reason TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "Successfully created irs_requests table.";
} catch (Exception $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
