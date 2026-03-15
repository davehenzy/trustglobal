<?php 
require_once 'includes/db.php'; 

// Fetch all settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Helper function
function getSetting($key, $default = '') {
    global $settings;
    return $settings[$key] ?? $default;
}

// Fetch all services
$stmt = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC, id DESC LIMIT 4");
$db_services = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftCapital - Modern Banking Solutions</title>
    <meta name="google-site-verification" content="">
    <title>SwiftCapital</title>
    <meta name="description" content="SwiftCapital | We are here to serve you better and help save your money without charges..">
    <meta property="og:locale" content="en_EN">
    <meta property="og:type" content="website">
    <meta property="og:title" content="SwiftCapital - We are here to serve you better and help save your money without charges..">
    <meta property="og:description" content="SwiftCapital | We are here to serve you better and help save your money without charges">
    <meta property="og:image" content="/assets/images/SWC%20Icon%20Dark.png">
    <meta property="og:url" content="https://trustsglobal.com">
    <meta property="og:site_name" content="SwiftCapital">

    <!--favicon icon-->
    <link rel="icon" href="assets/images/SWC%20Icon%20Dark.png" type="image/png" sizes="16x16">
    <!-- Bootstrap CSS -->
    <link href="npm/bootstrap%405.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="npm/bootstrap-icons%401.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand" style="width: 150px" href="index.php">
                <img src="assets/images/SWC%20Secondary%20Logo%20Light.png" alt="SwiftCapital Logo" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
    <a class="nav-link" href="index.php">Home</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="about.php">About</a>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="services.php" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Services
    </a>
    <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
        <li><a class="dropdown-item" href="services.php#person-banking">Personal Banking</a></li>
        <li><a class="dropdown-item" href="services.php#business-banking">Business Banking</a></li>
        <li><a class="dropdown-item" href="services.php#corporate-banking">Corporate Banking</a></li>
        <li><a class="dropdown-item" href="services.php#loan-banking">Loans & Mortgages</a></li>
        <li><a class="dropdown-item" href="services.php#investment-banking">Investments</a></li>
    </ul>
</li>
<li class="nav-item">
    <a class="nav-link" href="careers.php">Careers</a>
</li> 
<li class="nav-item">
    <a class="nav-link" href="contact.php">Contact</a>
</li>
                </ul>
                <div class="d-flex ms-lg-4">
                                            <a href="logout.php" class="btn btn-outline-primary me-2">Log In</a>
                        <a href="register.php" class="btn btn-primary">Open Account</a>
                                    </div>
            </div>
        </div>
    </nav>
    <section class="hero" data-aos="fade-up">
    <div class="container">
        <div class="row align-items-center hero-content">
            <div class="col-lg-6">
                <h1><?php echo getSetting('hero_headline', 'Banking Made Simple, Secure, and Smart'); ?></h1>
                <p><?php echo getSetting('hero_description', 'Experience the next generation of banking with SwiftCapital. We combine cutting-edge technology with personalized service to provide you with the best banking experience.'); ?></p>
                <div class="d-flex flex-wrap">
                    <a href="register.php" class="btn btn-light btn-lg me-3 mb-3"><?php echo getSetting('hero_cta_primary', 'Open Account'); ?></a>
                    <a href="about.php" class="btn btn-outline-light btn-lg mb-3"><?php echo getSetting('hero_cta_secondary', 'Learn More'); ?></a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <img src="assets/images/photo-1563013544-824ae1b704d3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Banking App" class="img-fluid rounded-4 shadow-lg">
            </div>
        </div>
    </div>
</section>
<section class="py-5" data-aos="fade-up">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="fw-bold mb-3">Why Choose SwiftCapital?</h2>
                <p class="text-muted">We offer a comprehensive range of banking services designed to meet your financial
                    needs.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Secure Banking</h4>
                    <p class="text-muted mb-0">Your security is our priority. We use advanced encryption and
                        multi-factor authentication to keep your accounts safe.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-phone"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Mobile Banking</h4>
                    <p class="text-muted mb-0">Bank on the go with our award-winning mobile app. Check balances,
                        transfer funds, and pay bills from anywhere.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Financial Planning</h4>
                    <p class="text-muted mb-0">Our expert advisors help you plan for the future with personalized
                        financial advice and investment strategies.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-5 bg-light" data-aos="fade-up">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="fw-bold mb-3">Our Banking Services</h2>
                <p class="text-muted">Comprehensive financial solutions tailored to your needs</p>
            </div>
        </div>
        <div class="row">
            <?php if (!empty($db_services)): ?>
                <?php foreach ($db_services as $service): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="service-card card h-100 shadow-sm border-0" style="border-radius: 20px;">
                        <div class="card-body text-center p-4">
                            <div class="stat-icon <?php echo htmlspecialchars($service['color_class']); ?> mx-auto mb-4" style="width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                <i class="fa-solid <?php echo htmlspecialchars($service['icon']); ?>"></i>
                            </div>
                            <h5 class="fw-bold mb-3"><?php echo htmlspecialchars($service['title']); ?></h5>
                            <p class="text-muted small mb-0"><?php echo htmlspecialchars($service['description']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback to static if no services in DB -->
                <div class="col-lg-3 col-md-6">
                    <div class="service-card card h-100">
                        <img src="assets/images/photo-1556742049-0cfed4f6a45d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" class="card-img-top" alt="Personal Banking">
                        <div class="card-body">
                            <h5 class="card-title">Personal Banking</h5>
                            <p class="card-text">Everyday banking solutions designed to simplify your financial life.</p>
                            <a href="services.php#person-banking" class="btn btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="service-card card h-100">
                        <img src="assets/images/photo-1507679799987-c73779587ccf?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" class="card-img-top" alt="Business Banking">
                        <div class="card-body">
                            <h5 class="card-title">Business Banking</h5>
                            <p class="card-text">Specialized services to help your business grow and thrive.</p>
                            <a href="services.php#business-banking" class="btn btn-outline-primary">Learn More</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<section class="stats" data-aos="fade-up">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item">
                    <h2><span class="counter" data-target="<?php echo (float)filter_var(getSetting('active_users_display', '2M+'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); ?>">0</span>M+</h2>
                    <p>Happy Customers</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item">
                    <h2><span class="counter" data-target="500">0</span>+</h2>
                    <p>Branches</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <h2><span class="counter" data-target="50">0</span>+</h2>
                    <p>Countries</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <h2><span class="counter" data-target="99.9">0</span>%</h2>
                    <p>Uptime</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="security-features" data-aos="fade-up">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="fw-bold mb-3">Bank-Grade Security</h2>
                <p class="text-muted">Your security is our top priority. We employ multiple layers of protection to keep
                    your money and information safe.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="security-item">
                    <div class="security-icon">
                        <i class="bi bi-fingerprint"></i>
                    </div>
                    <h4>Biometric Authentication</h4>
                    <p>Use your fingerprint or face recognition to securely access your accounts.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="security-item">
                    <div class="security-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4>Fraud Monitoring</h4>
                    <p>24/7 monitoring systems detect and prevent suspicious activities on your accounts.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="security-item">
                    <div class="security-icon">
                        <i class="bi bi-lock"></i>
                    </div>
                    <h4>End-to-End Encryption</h4>
                    <p>All your transactions and personal information are encrypted with bank-level security.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="app-download" data-aos="fade-up">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2 mb-5 mb-lg-0">
                    <img src="assets/images/photo-1563013544-824ae1b704d3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="SwiftCapital Mobile App" class="app-img">
                </div>
                <div class="col-lg-6 order-lg-1">
                    <h2 class="fw-bold mb-4">Banking at Your Fingertips</h2>
                    <p class="text-muted mb-4">Download our mobile app and manage your finances anytime, anywhere. Our intuitive app puts the power of banking in your pocket.</p>
                    
                    <div class="app-features">
                        <div class="app-feature-item">
                            <div class="app-feature-icon">
                                <i class="bi bi-arrow-repeat"></i>
                            </div>
                            <div class="app-feature-text">
                                <h5>Instant Transfers</h5>
                                <p>Send money to anyone, anywhere, instantly.</p>
                            </div>
                        </div>
                        <div class="app-feature-item">
                            <div class="app-feature-icon">
                                <i class="bi bi-bell"></i>
                            </div>
                            <div class="app-feature-text">
                                <h5>Real-time Notifications</h5>
                                <p>Stay informed about all account activities.</p>
                            </div>
                        </div>
                        <div class="app-feature-item">
                            <div class="app-feature-icon">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <div class="app-feature-text">
                                <h5>Expense Tracking</h5>
                                <p>Monitor your spending habits with detailed analytics.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-download-buttons">
                        <a href="#"><img src="wikipedia/commons/thumb/7/78/Google_Play_Store_badge_EN.svg/2560px-Google_Play_Store_badge_EN.svg.png" alt="Get it on Google Play"></a>
                        <a href="#"><img src="wikipedia/commons/thumb/3/3c/Download_on_the_App_Store_Badge.svg/2560px-Download_on_the_App_Store_Badge.svg.png" alt="Download on the App Store"></a>
                    </div>
                </div>
            </div>
        </div>
    </section><section class="testimonial" data-aos="fade-up">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="fw-bold mb-3">What Our Customers Say</h2>
                    <p class="text-muted">Don't just take our word for it. Here's what our customers have to say about their experience with SwiftCapital.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-4">
                            <img src="api/portraits/women/32.jpg" alt="Customer">
                            <div>
                                <h5 class="mb-0">Sarah Johnson</h5>
                                <small class="text-muted">Small Business Owner</small>
                            </div>
                        </div>
                        <p class="mb-0">"SwiftCapital has been instrumental in helping my business grow. Their business banking services and dedicated support team have made managing my finances effortless."</p>
                        <div class="mt-3 text-warning">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-4">
                            <img src="api/portraits/men/45.jpg" alt="Customer">
                            <div>
                                <h5 class="mb-0">Michael Chen</h5>
                                <small class="text-muted">Software Engineer</small>
                            </div>
                        </div>
                        <p class="mb-0">"The mobile app is fantastic! I can do everything from checking my balance to paying bills and transferring money with just a few taps. Best banking experience I've ever had."</p>
                        <div class="mt-3 text-warning">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-4">
                            <img src="api/portraits/women/68.jpg" alt="Customer">
                            <div>
                                <h5 class="mb-0">Emily Rodriguez</h5>
                                <small class="text-muted">Homeowner</small>
                            </div>
                        </div>
                        <p class="mb-0">"Getting a mortgage through SwiftCapital was surprisingly easy. Their team guided me through every step of the process, and I got a great rate. I couldn't be happier with my experience."</p>
                        <div class="mt-3 text-warning">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><section class="cta" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-4">Ready to Experience Better Banking?</h2>
                    <p class="text-muted mb-5">Join millions of satisfied customers who have made the switch to SwiftCapital. Opening an account takes less than 10 minutes.</p>
                    <a href="register.php" class="btn btn-primary btn-lg px-5 py-3">Open an Account Today</a>
                </div>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <a class="navbar-brand" style="width: 150px" href="index.php">
                        <img src="assets/images/SWC%20Secondary%20Logo%20Dark.png" alt="SwiftCapital Logo" height="50">
                    </a>
                    <p class="text-muted">Providing innovative banking solutions since 1995. Our mission is to empower
                        our customers to achieve their financial goals.</p>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
                    <h5>Products</h5>
                    <ul>
                        <li><a href="#">Checking Accounts</a></li>
                        <li><a href="#">Savings Accounts</a></li>
                        <li><a href="#">Credit Cards</a></li>
                        <li><a href="#">Loans</a></li>
                        <li><a href="#">Mortgages</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
                    <h5>Company</h5>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Press</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
                    <h5>Support</h5>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Security</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5>Contact</h5>
                    <ul>
                        <li><a href="javascript::"><?php echo getSetting('contact_email', 'support@SwiftCapital.com'); ?></a></li>
                        <li><a href="javascript::"><?php echo getSetting('contact_address', '301 East Water Street, Charlottesville, VA 22904'); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="row copyright">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; 2026 SwiftCapital. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">
                        <a href="#" class="text-white-50 me-3">Privacy Policy</a>
                        <a href="#" class="text-white-50">Terms of Service</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="npm/bootstrap%405.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.8.0/countUp.umd.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });

        // Initialize CountUp.js
        const startCounters = () => {
            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                const target = parseFloat(counter.getAttribute('data-target'));
                const hasDecimals = target % 1 !== 0;
                const decimals = hasDecimals ? 1 : 0;
                
                // Use countUp global object exported by UMD script
                const counterAnim = new countUp.CountUp(counter, target, {
                    decimalPlaces: decimals,
                    duration: 2.5,
                    useEasing: true,
                    useGrouping: true,
                });
                
                if (!counterAnim.error) {
                    counterAnim.start();
                } else {
                    console.error(counterAnim.error);
                }
            });
        };

        // Use Intersection Observer to trigger counter when in view
        const observer = new IntersectionObserver((entries) => {
            if(entries[0].isIntersecting) {
                startCounters();
                observer.disconnect(); // counter runs only once
            }
        });
        
        const statsSection = document.querySelector('.stats');
        if(statsSection) {
            observer.observe(statsSection);
        }
    </script>
</body>

</html>
