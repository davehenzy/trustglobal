<?php
require_once 'includes/db.php';

$settings = [
    'hero_headline' => 'Experience the Future of Premium Banking',
    'hero_description' => 'SwiftCapital provides seamless, secure, and innovative financial luxury solutions for elite individuals and corporations worldwide.',
    'hero_cta_primary' => 'Open Elite Account',
    'hero_cta_secondary' => 'View Assets',
    'hero_bg' => '/assets/img/luxury-bg.jpg',
    'about_heading' => 'Our Mission: Redefining Trust',
    'about_content' => 'At SwiftCapital, we are dedicated to empowering our clients through transparent, accessible, and high-performance financial services. Built on a foundation of trust and technology, our vision is to lead the next generation of banking innovation.',
    'active_users_display' => '1.2M+ Active Users',
    'aum_display' => '$42B+ Assets Under Management',
    'site_name' => 'SwiftCapital',
    'primary_color' => '#6366f1',
    'accent_color' => '#0f172a'
];

foreach ($settings as $key => $value) {
    try {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    } catch (Exception $e) {
        echo "Error inserting $key: " . $e->getMessage() . "\n";
    }
}

echo "Settings seeded.\n";
?>
