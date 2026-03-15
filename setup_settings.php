<?php
require_once 'includes/db.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Settings table created successfully.\n";

    $defaults = [
        'hero_headline' => 'Banking At Its Best',
        'hero_description' => 'Experience the future of digital finance with SwiftCapital. Secure, fast, and remarkably premium.',
        'hero_cta_primary' => 'Open Account',
        'hero_cta_secondary' => 'Learn More',
        'hero_bg' => 'assets/images/hero-bg.jpg',
        'about_heading' => 'Defining the Future of Wealth',
        'about_content' => 'SwiftCapital was founded on the principle that elite banking should be accessible to those who value precision and excellence. Our platform combines legacy security with modern velocity.',
        'active_users_display' => '50K+',
        'aum_display' => '$1.2B',
        'contact_email' => 'support@swiftcapital.com',
        'contact_phone' => '+1 (555) 000-1234',
        'contact_address' => '123 Finance Plaza, New York, NY',
    ];

    foreach ($defaults as $key => $val) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->execute([$key, $val]);
    }
    echo "Default settings populated successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
