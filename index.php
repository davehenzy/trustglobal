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

$site_name = getSetting('site_name', 'SwiftCapital');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_name; ?> - Institutional Private Banking & Capital Management</title>
    <meta name="description" content="Elite financial services for institutional investors and private wealth. Experience global banking excellence with <?php echo $site_name; ?>.">
    
    <!-- Favicon -->
    <link rel="icon" href="assets/images/SWC%20Icon%20Dark.png" type="image/png" sizes="16x16">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="npm/bootstrap%405.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="npm/bootstrap-icons%401.11.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        :root {
            --norby-blue: #002d62;
            --brand-red: #E21936;
            --charcoal-gray: #101010;
            --brand-white: #ffffff;
            --primary-font: 'Lato', sans-serif;
            --secondary-font: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--primary-font);
            background-color: var(--brand-white);
            color: var(--charcoal-gray);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--secondary-font);
            letter-spacing: -0.02em;
        }

        .hero {
            position: relative;
            padding: 220px 0 160px;
            background: var(--norby-blue);
            color: var(--brand-white);
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('modern_office_workspace_1773622619884.png') center/cover;
            opacity: 0.15;
            z-index: 0;
            filter: grayscale(100%);
        }

        .hero-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to right, var(--norby-blue) 0%, rgba(0, 45, 98, 0.4) 100%);
            z-index: 1;
        }

        .hero .container { position: relative; z-index: 2; }

        .hero h1 {
            font-size: 4rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 2rem;
        }

        .hero p {
            font-size: 1.25rem;
            opacity: 0.8;
            max-width: 600px;
            line-height: 1.8;
            margin-bottom: 3rem;
        }

        .premium-btn {
            padding: 18px 45px;
            border-radius: 0;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            transition: all 0.3s;
        }

        .btn-red { background: var(--brand-red); color: var(--brand-white); border: none; }
        .btn-red:hover { background: var(--charcoal-gray); transform: translateY(-3px); box-shadow: 0 10px 30px rgba(226, 25, 54, 0.3); color: white; }

        .btn-outline-executive { border: 2px solid var(--brand-white); color: var(--brand-white); }
        .btn-outline-executive:hover { background: var(--brand-white); color: var(--norby-blue); }

        .feature-card-premium {
            background: var(--brand-white);
            padding: 50px 40px;
            border: 1px solid #edf2f7;
            height: 100%;
            transition: all 0.4s;
            position: relative;
        }

        .feature-card-premium:hover {
            border-bottom: 4px solid var(--brand-red);
            transform: translateY(-10px);
            box-shadow: 0 30px 70px rgba(0,0,0,0.06);
        }

        .feature-icon-box {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--norby-blue);
            margin-bottom: 2.5rem;
            border-left: 3px solid var(--brand-red);
            padding-left: 15px;
        }

        .stat-banner {
            background: var(--brand-white);
            margin-top: -60px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.08);
            position: relative;
            z-index: 10;
        }

        .stat-col {
            padding: 50px 40px;
            text-align: center;
            border-right: 1px solid #f1f5f9;
        }

        .stat-col:last-child { border-right: none; }

        .stat-col h2 { font-size: 2.5rem; font-weight: 900; color: var(--norby-blue); margin-bottom: 5px; }
        .stat-col p { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; color: var(--brand-red); margin: 0; }

        .section-title-executive {
            text-align: center;
            margin-bottom: 6rem;
        }

        .section-title-executive span {
            color: var(--brand-red);
            text-transform: uppercase;
            font-weight: 900;
            letter-spacing: 4px;
            font-size: 0.8rem;
            display: block;
            margin-bottom: 1.5rem;
        }

        .section-title-executive h2 { font-size: 3rem; font-weight: 900; color: var(--norby-blue); }

        .testimonial-executive {
            background: var(--brand-white);
            padding: 60px;
            border-left: 5px solid var(--brand-red);
            box-shadow: 0 20px 50px rgba(0,0,0,0.03);
            height: 100%;
        }

        .testimonial-text {
            font-size: 1.15rem;
            font-weight: 600;
            line-height: 1.8;
            color: #4a5568;
            margin-bottom: 2rem;
            font-style: italic;
        }

        .testimonial-author h6 { font-weight: 900; color: var(--norby-blue); margin: 0; font-size: 1rem; }
        .testimonial-author p { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--brand-red); margin: 0; letter-spacing: 1px; }

        .py-150 { padding: 150px 0; }

        @media (max-width: 991px) {
            .hero h1 { font-size: 2.8rem; }
            .py-150 { padding: 100px 0; }
        }

        .ls-2 { letter-spacing: 2px; }
        .fw-900 { font-weight: 900; }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom py-3">
        <div class="container">
            <a class="navbar-brand" style="width: 170px" href="index.php">
                <!-- Landscape Secondary Logo used in Navbar -->
                <img src="assets/images/SWC%20Secondary%20Logo%20Dark.png" alt="<?php echo $site_name; ?> Logo" height="55">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bi bi-list fs-1" style="color: var(--norby-blue);"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link fw-bold px-3 text-uppercase letter-spacing-1" href="index.php">Institutional</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold px-3 text-uppercase letter-spacing-1" href="about.php">Our Story</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-bold px-3 text-uppercase letter-spacing-1" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">Expertise</a>
                        <ul class="dropdown-menu shadow-lg border-0 p-0 rounded-0">
                            <li><a class="dropdown-item py-3 fw-bold border-bottom" href="services.php#person-banking">Private Banking</a></li>
                            <li><a class="dropdown-item py-3 fw-bold border-bottom" href="services.php#business-banking">Asset Management</a></li>
                            <li><a class="dropdown-item py-3 fw-bold" href="services.php#corporate-banking">Corporate Finance</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link fw-bold px-3 text-uppercase letter-spacing-1" href="careers.php">Careers</a></li> 
                    <li class="nav-item"><a class="nav-link fw-bold px-3 text-uppercase letter-spacing-1" href="contact.php">Advisory</a></li>
                    <li class="nav-item ms-lg-5">
                        <a href="login.php" class="premium-btn btn-red text-decoration-none">Client Access</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero shadow-2xl">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-lg-8" data-aos="fade-right">
                    <span class="d-block mb-3 fw-900 text-uppercase" style="color:var(--brand-red); letter-spacing: 5px; font-size: 0.8rem;">World-Class Financial Governance</span>
                    <h1 class="display-3 fw-900">Institutional <br><span style="color:var(--brand-red);">Private Banking.</span></h1>
                    <p class="lead">Experience a higher standard of capital management. Our firm provides the strategic infrastructure required for global asset preservation and institutional wealth growth.</p>
                    <div class="d-flex flex-wrap gap-4 mt-5">
                        <a href="register.php" class="premium-btn btn-red text-decoration-none">Initialize Relationship</a>
                        <a href="about.php" class="premium-btn btn-outline-executive text-decoration-none">The Strategy</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="container">
        <div class="stat-banner row g-0 text-center">
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-col">
                    <h2>$42B+</h2>
                    <p>Capital Managed</p>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-col">
                    <h2>AA+</h2>
                    <p>Credit Rating</p>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-col">
                    <h2>140+</h2>
                    <p>Global Markets</p>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-col">
                    <h2>24/7</h2>
                    <p>Advisory Support</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Expertise Section -->
    <section class="py-150">
        <div class="container">
            <div class="section-title-executive" data-aos="fade-up">
                <span>Core Capabilities</span>
                <h2>Financial Architecture.</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card-premium">
                        <div class="feature-icon-box"><i class="fa-solid fa-vault"></i></div>
                        <h4 class="fw-900 mb-3">Fiduciary Stewardship</h4>
                        <p class="text-secondary">Our mandate is absolute. We employ tactical asset allocation models to preserve and grow intergenerational wealth.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card-premium">
                        <div class="feature-icon-box"><i class="fa-solid fa-chart-pie"></i></div>
                        <h4 class="fw-900 mb-3">Strategic Architecture</h4>
                        <p class="text-secondary">Bespoke algorithmic portfolio modeling designed for tax-neutral growth in complex global jurisdictions.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card-premium">
                        <div class="feature-icon-box"><i class="fa-solid fa-earth-americas"></i></div>
                        <h4 class="fw-900 mb-3">Global Arbitrage</h4>
                        <p class="text-secondary">Direct market access to Tier-1 liquidity pools with zero-latency execution across all major asset classes.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Global Reach Section -->
    <section class="py-150 bg-light border-top">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <span class="text-uppercase fw-900 mb-3 d-block ls-2" style="color:var(--brand-red);">Global Sovereignty</span>
                    <h2 class="display-5 fw-900 mb-4">A Network of Global Hubs.</h2>
                    <p class="lead mb-5 text-secondary">Our institutional footprint spans the world's most critical financial centers, providing our partners with localized expertise and cross-border agility.</p>
                    
                    <div class="row g-4 mb-2">
                        <div class="col-md-6">
                            <ul class="list-unstyled fw-bold text-uppercase small ls-1">
                                <li class="mb-3"><i class="fa-solid fa-location-arrow text-danger me-2"></i> Zurich Headquarters</li>
                                <li class="mb-3"><i class="fa-solid fa-location-arrow text-danger me-2"></i> London / The City</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled fw-bold text-uppercase small ls-1">
                                <li class="mb-3"><i class="fa-solid fa-location-arrow text-danger me-2"></i> Singapore / Marina Bay</li>
                                <li class="mb-3"><i class="fa-solid fa-location-arrow text-danger me-2"></i> New York / Wall St.</li>
                            </ul>
                        </div>
                    </div>
                    <hr class="my-4 opacity-10">
                    <div class="d-flex align-items-center gap-4">
                        <div class="d-flex flex-column">
                            <span class="fw-900 h4 mb-0 text-dark">Tier-1</span>
                            <span class="text-uppercase x-small ls-1 text-danger fw-bold">Capital Adequacy Ratio</span>
                        </div>
                        <div class="vr"></div>
                        <div class="d-flex flex-column">
                            <span class="fw-900 h4 mb-0 text-dark">AAA</span>
                            <span class="text-uppercase x-small ls-1 text-danger fw-bold">Liquidity Coverage</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="p-5 bg-white border shadow-2xl position-relative overflow-hidden">
                        <div class="position-absolute top-0 end-0 p-4 opacity-10">
                            <i class="fa-solid fa-building-columns display-1"></i>
                        </div>
                        <h4 class="fw-900 mb-4">Governance Charter</h4>
                        <div class="mb-4">
                            <h6 class="text-uppercase x-small fw-900 text-danger ls-2 mb-2">Internal Controls</h6>
                            <p class="small text-secondary m-0">Rigorous audit trails and multi-signature verification flows for all institutional settlements.</p>
                        </div>
                        <div class="mb-4">
                            <h6 class="text-uppercase x-small fw-900 text-danger ls-2 mb-2">Basel III Compliance</h6>
                            <p class="small text-secondary m-0">Maintaining capitalization levels significantly above standard regulatory requirements.</p>
                        </div>
                        <div class="mb-0">
                            <h6 class="text-uppercase x-small fw-900 text-danger ls-2 mb-2">Fiduciary Mandate</h6>
                            <p class="small text-secondary m-0">Legally binding commitment to act solely in the best interest of our institutional partners.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Focus -->
    <section class="py-150 bg-dark text-white" style="background: var(--norby-blue) !important;">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <img src="modern_office_workspace_1773622619884.png" alt="Institutional Security" class="img-fluid border-start border-danger border-4 shadow-xl">
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <span class="text-uppercase fw-900 mb-3 d-block ls-2" style="color:var(--brand-red);">FIPS 140-2 Infrastructure</span>
                    <h2 class="display-5 fw-900 mb-4 text-white">Uncompromising Security Architecture.</h2>
                    <p class="lead opacity-75 mb-5">At <?php echo $site_name; ?>, your security is our primary mandate. We employ military-grade encryption and decentralized synchronization protocols to protect your assets 24/7/365.</p>
                    <div class="row g-4">
                        <div class="col-6">
                            <h6 class="fw-900 text-uppercase small" style="color:var(--brand-red);"><i class="bi bi-shield-check me-2"></i> Quantum-Ready</h6>
                        </div>
                        <div class="col-6">
                            <h6 class="fw-900 text-uppercase small" style="color:var(--brand-red);"><i class="bi bi-fingerprint me-2"></i> Multi-Sig Flow</h6>
                        </div>
                        <div class="col-6">
                            <h6 class="fw-900 text-uppercase small" style="color:var(--brand-red);"><i class="bi bi-radar me-2"></i> 24/7 SOC Monitoring</h6>
                        </div>
                        <div class="col-6">
                            <h6 class="fw-900 text-uppercase small" style="color:var(--brand-red);"><i class="bi bi-lock me-2"></i> Vault Storage</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-150 bg-light">
        <div class="container">
            <div class="section-title-executive" data-aos="fade-up">
                <span>Stakeholder Trust</span>
                <h2>Institutional Perspective.</h2>
            </div>
            <div class="row g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="testimonial-executive">
                        <p class="testimonial-text">"The precision and institutional rigor of <?php echo $site_name; ?> is unmatched. Their ability to navigate complex cross-border regulations while maintaining absolute discretion is why our family office trusts them exclusively."</p>
                        <div class="testimonial-author">
                            <h6>Elena D. Moretti</h6>
                            <p>Managing Director | Global Capital Group</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="testimonial-executive">
                        <p class="testimonial-text">"Transitioning our corporate treasury to SwiftCapital's infrastructure improved our operational agility by 40%. Their quant-driven insights provide a competitive edge in volatile markets."</p>
                        <div class="testimonial-author">
                            <h6>Marcus V. Sterling</h6>
                            <p>Chief Investment Officer | Sterling & Co.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-150 bg-white border-top border-bottom">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="zoom-in">
                    <h2 class="display-4 fw-900 mb-4" style="color: var(--norby-blue);">Secure Your Capital Legacy.</h2>
                    <p class="lead text-secondary mb-5">Open an institutional account today and gain access to our global advisory network. Relationship initialization takes less than 10 minutes.</p>
                    <a href="register.php" class="premium-btn btn-red btn-lg text-decoration-none">Initialize Relationship Today</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-white py-150" style="background: var(--norby-blue) !important;">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <a class="navbar-brand mb-4 d-block" style="width: 170px" href="index.php">
                        <!-- Landscape Light Secondary Logo used on Blue Background -->
                        <img src="assets/images/SWC%20Secondary%20Logo%20Light.png" alt="<?php echo $site_name; ?> Logo" height="55">
                    </a>
                    <p class="text-white-50 small mb-4"><?php echo $site_name; ?> is an international financial institution providing bespoke private banking and corporate advisory. Member FDIC. Equal Housing Lender.</p>
                    <div class="social-icons">
                        <a href="#" class="me-3 text-white-50"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="me-3 text-white-50"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="text-white-50"><i class="bi bi-globe"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-6 ms-auto">
                    <h6 class="fw-900 mb-4 text-uppercase small ls-2" style="color: var(--brand-red);">Expertise</h6>
                    <ul class="list-unstyled text-white-50 small">
                        <li class="mb-2">Private Wealth</li>
                        <li class="mb-2">Asset Strategy</li>
                        <li class="mb-2">Equity Markets</li>
                        <li class="nav-item"><a href="#" class="text-white-50 text-decoration-none">Fixed Income</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="fw-900 mb-4 text-uppercase small ls-2" style="color: var(--brand-red);">Governance</h6>
                    <ul class="list-unstyled text-white-50 small">
                        <li class="mb-2">Compliance</li>
                        <li class="mb-2">Privacy Charter</li>
                        <li class="mb-2">Ethics Hotline</li>
                        <li class="mb-2">Risk Policy</li>
                    </ul>
                </div>
                <div class="col-lg-3 text-center">
                    <!-- Primary portrait logo in centered position -->
                    <img src="assets/images/SWC%20Primary%20Logo%20Light.png" alt="SwiftCapital Primary Logo" height="110" class="mb-4">
                    <p class="text-white-50 small"><i class="bi bi-geo-alt me-2"></i> Global Headquarters, Zurich</p>
                </div>
            </div>
            <div class="mt-5 pt-5 border-top border-secondary opacity-50">
                <p class="mb-0 small">&copy; 2026 <?php echo $site_name; ?> Group. Approved for Institutional Investors Only.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="npm/bootstrap%405.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1200,
            once: true,
            offset: 100,
            easing: 'ease-out-quint'
        });
    </script>
</body>

</html>
