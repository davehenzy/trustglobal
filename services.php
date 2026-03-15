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
$stmt = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC, id DESC");
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
    <meta property="og:image" content="http://trustsglobal.com/assets/images/SWC%20Icon%20Dark.png">
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
        <li><a class="dropdown-item" href="services.php#service-1">Personal Banking</a></li>
        <li><a class="dropdown-item" href="services.php#service-2">Business Banking</a></li>
        <li><a class="dropdown-item" href="services.php#service-3">Corporate Banking</a></li>
        <li><a class="dropdown-item" href="services.php#service-4">Loans & Mortgages</a></li>
        <li><a class="dropdown-item" href="services.php#service-5">Investments</a></li>
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
    <!-- Hero Section -->
    <section class="services-hero position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f0c29, #302b63, #24243e); min-height: 70vh; display: flex; align-items: center;">
        <!-- Animated Orbs -->
        <div class="position-absolute" style="top: 10%; left: 5%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(99,102,241,0.3) 0%, transparent 70%); border-radius: 50%;"></div>
        <div class="position-absolute" style="bottom: 10%; right: 5%; width: 250px; height: 250px; background: radial-gradient(circle, rgba(139,92,246,0.2) 0%, transparent 70%); border-radius: 50%;"></div>
        <div class="container py-5 position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-6 text-white mb-5 mb-lg-0" data-aos="fade-right">
                    <span class="badge rounded-pill bg-indigo-light text-primary px-4 py-2 fw-800 text-xs mb-4 text-uppercase">Premium Financial Services</span>
                    <h1 class="fw-800 display-3 mb-4 text-white">World-Class<br><span style="background: linear-gradient(90deg, #818cf8, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Banking Solutions</span></h1>
                    <p class="lead text-white-50 mb-5">From high-yield savings to sophisticated wealth management — our institutional-grade products give you the edge.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="register.php" class="btn btn-primary btn-lg px-5 py-3 fw-800 shadow-lg" style="border-radius: 12px;">Open an Account</a>
                        <a href="contact.php" class="btn btn-outline-light btn-lg px-4 py-3 fw-800" style="border-radius: 12px;">Talk to an Advisor</a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="row g-3">
                        <?php foreach ($db_services as $i => $s): ?>
                        <div class="col-6">
                            <div class="glass rounded-4 p-4 text-white h-100" style="border: 1px solid rgba(255,255,255,0.1);">
                                <div class="stat-icon <?php echo htmlspecialchars($s['color_class']); ?> mb-3" style="width: 48px; height: 48px; border-radius: 14px; font-size: 1.3rem;">
                                    <i class="fa-solid <?php echo htmlspecialchars($s['icon']); ?>"></i>
                                </div>
                                <h6 class="fw-800 text-white mb-1"><?php echo htmlspecialchars($s['title']); ?></h6>
                                <a href="#service-<?php echo $s['id']; ?>" class="text-white-50 text-xs fw-600" style="text-decoration: none;">Learn More →</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Detailed Services Showcase -->

    <div class="detailed-services">
        <?php foreach ($db_services as $index => $service): ?>
        <section class="py-5" id="service-<?php echo $service['id']; ?>" data-aos="fade-up">
            <div class="container py-lg-5">
                <div class="row align-items-center g-5 <?php echo ($index % 2 != 0) ? 'flex-lg-row-reverse' : ''; ?>">
                    <div class="col-lg-6">
                        <div class="position-relative">
                            <img src="<?php echo htmlspecialchars($service['image_url']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" class="img-fluid rounded-5 shadow-2xl" style="height: 450px; width: 100%; object-fit: cover;">
                            <div class="position-absolute bottom-0 start-0 m-4 glass p-3 rounded-4 border-0 d-none d-md-block shadow-lg" style="width: 200px;">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon <?php echo htmlspecialchars($service['color_class']); ?> me-3" style="width: 40px; height: 40px; border-radius: 12px; font-size: 1.2rem;">
                                        <i class="fa-solid <?php echo htmlspecialchars($service['icon']); ?>"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-800">Verified</h6>
                                        <small class="text-muted">Institutional Grade</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="badge rounded-pill <?php echo htmlspecialchars($service['color_class']); ?> px-4 py-2 text-xs fw-800 mb-4 text-uppercase">Exclusive Product</span>
                        <h2 class="fw-800 display-5 mb-4"><?php echo htmlspecialchars($service['title']); ?></h2>
                        <p class="lead text-muted mb-5">
                            <?php echo nl2br(htmlspecialchars($service['description'])); ?>
                        </p>
                        
                        <div class="row g-4 mb-5">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-indigo-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fa-solid fa-check text-xs"></i>
                                    </div>
                                    <span class="fw-700 text-sm">Global Accessibility</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-indigo-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fa-solid fa-check text-xs"></i>
                                    </div>
                                    <span class="fw-700 text-sm">Secure Custody</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-indigo-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fa-solid fa-check text-xs"></i>
                                    </div>
                                    <span class="fw-700 text-sm">Real-time Settlements</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-indigo-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fa-solid fa-check text-xs"></i>
                                    </div>
                                    <span class="fw-700 text-sm">Expert Consultation</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3">
                            <a href="register.php" class="btn btn-primary btn-lg px-5 py-3 fw-800 shadow-lg" style="border-radius: 12px;">Get Started</a>
                            <a href="contact.php" class="btn btn-light btn-lg px-4 py-3 fw-800" style="border-radius: 12px;">Speak with an Advisor</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php endforeach; ?>
    </div>

    <!-- CTA Section -->
    <section class="py-5 bg-dark position-relative overflow-hidden" style="border-radius: 50px 50px 0 0; margin-top: -50px; z-index: 10;">
        <div class="container py-5 text-center position-relative" style="z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="fw-800 display-4 text-white mb-4">Elevate Your Financial Strategy Today</h2>
                    <p class="lead text-white-50 mb-5">Our team of elite financial advisors is ready to craft a personalized solution tailored to your wealth management goals.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="register.php" class="btn btn-primary btn-lg px-5 py-3 fw-800 shadow-lg" style="border-radius: 12px;">Join SwiftCapital</a>
                        <a href="contact.php" class="btn btn-outline-light btn-lg px-5 py-3 fw-800" style="border-radius: 12px;">Contact Concierge</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Decorative Background Element -->
        <div class="position-absolute top-50 start-50 translate-middle" style="width: 100%; height: 100%; background: radial-gradient(circle, rgba(99,102,241,0.1) 0%, rgba(0,0,0,0) 70%);"></div>
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
                        <li><a href="javascript::"><?php echo getSetting('contact_email', 'support@trustsglobal.com'); ?></a></li>
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
    <style>
        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* Utilities */
        .text-xs { font-size: 0.75rem; }
        .text-sm { font-size: 0.875rem; }
        .fw-700 { font-weight: 700; }
        .fw-800 { font-weight: 800; }
        .stat-icon { display: flex; align-items: center; justify-content: center; }

        /* service-color classes matching admin */
        .bg-indigo-light { background-color: rgba(99,102,241,0.15); }
        .bg-emerald-light { background-color: rgba(16,185,129,0.15); }
        .bg-amber-light  { background-color: rgba(245,158,11,0.15); }
        .bg-rose-light   { background-color: rgba(239,68,68,0.15); }
        .text-primary { color: #6366f1 !important; }
        .text-success { color: #10b981 !important; }
        .text-warning { color: #f59e0b !important; }
        .text-danger  { color: #ef4444 !important; }

        /* Sticky Nav */
        .service-nav-bar { padding: 0; }
        .service-nav-btn:hover, .service-nav-btn.active {
            background-color: #6366f1;
            color: #fff !important;
            border-color: #6366f1 !important;
        }

        /* Alternating section backgrounds */
        .detailed-services section:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Hero image shadow */
        .rounded-5 { border-radius: 1.5rem !important; }
        .shadow-deep {
            box-shadow: 0 25px 60px rgba(0,0,0,0.2) !important;
        }

        /* Smooth scroll */
        html { scroll-behavior: smooth; }
    </style>
    <script>
        AOS.init({
            duration: 900,
            once: true,
            offset: 150
        });

        // Scroll-spy: highlight active service in sticky nav
        (function() {
            const sections = document.querySelectorAll('.detailed-services section[id]');
            const navLinks = document.querySelectorAll('.service-nav-btn');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        navLinks.forEach(l => l.classList.remove('active'));
                        const active = document.querySelector(`.service-nav-btn[href="#${entry.target.id}"]`);
                        if (active) active.classList.add('active');
                    }
                });
            }, { rootMargin: '-20% 0px -60% 0px' });

            sections.forEach(s => observer.observe(s));
        })();
    </script>
</body>

</html>
