<?php
require 'c:/xampp/htdocs/swiftcap/includes/db.php';
try {
    $pdo->exec("ALTER TABLE kyc_verifications 
        ADD COLUMN full_name VARCHAR(255) AFTER user_id,
        ADD COLUMN dob DATE AFTER full_name,
        ADD COLUMN ssn VARCHAR(100) AFTER dob,
        ADD COLUMN account_type VARCHAR(100) AFTER ssn,
        ADD COLUMN employment VARCHAR(100) AFTER account_type,
        ADD COLUMN income VARCHAR(100) AFTER employment,
        ADD COLUMN address TEXT AFTER income,
        ADD COLUMN city VARCHAR(100) AFTER address,
        ADD COLUMN state VARCHAR(100) AFTER city,
        ADD COLUMN zip VARCHAR(20) AFTER state,
        ADD COLUMN country VARCHAR(100) AFTER zip,
        ADD COLUMN next_of_kin_name VARCHAR(255) AFTER country,
        ADD COLUMN next_of_kin_relationship VARCHAR(100) AFTER next_of_kin_name
    ");
    echo "Successfully updated kyc_verifications table with all fields.";
} catch (Exception $e) {
    echo "Error updating table: " . $e->getMessage();
}
?>
