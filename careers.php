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
$site_email = getSetting('site_email', 'support@trustsglobal.com');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careers | <?php echo $site_name; ?> - Shaping the Future of Global Finance</title>
    <meta name="description" content="Pursue a career in institutional banking and wealth management at <?php echo $site_name; ?>. Join our global network of financial experts.">
    
    <!-- Favicon -->
    <link rel="icon" href="assets/images/SWC%20Icon%20Dark.png" type="image/png" sizes="16x16">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.3);
            --primary-gradient: linear-gradient(135deg, #001f44 0%, #003366 100%);
            --accent-gold: #c5a059;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fcfcfc;
            color: #2D3748;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.02em;
        }

        .career-hero {
            position: relative;
            padding: 180px 0 140px;
            background: #000b1a;
            overflow: hidden;
            color: white;
        }

        .career-hero-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.4;
            z-index: 0;
        }

        .career-hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(0, 11, 26, 0.95) 0%, rgba(0, 11, 26, 0.6) 100%);
            z-index: 1;
        }

        .career-hero .container {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-block;
            padding: 10px 24px;
            background: rgba(197, 160, 89, 0.15);
            border: 1px solid rgba(197, 160, 89, 0.3);
            backdrop-filter: blur(10px);
            border-radius: 4px;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 2rem;
            color: var(--accent-gold);
        }

        .benefit-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 45px 35px;
            transition: all 0.4s ease;
            height: 100%;
            position: relative;
        }

        .benefit-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.08);
            border-color: var(--accent-gold);
        }

        .benefit-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--accent-gold);
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--accent-gold);
            padding-bottom: 10px;
        }

        .job-card {
            background: white;
            border-left: 4px solid #e2e8f0;
            border-radius: 0;
            padding: 35px 40px;
            margin-bottom: 20px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }

        .job-card:hover {
            border-left-color: var(--accent-gold);
            background: #fcfcfc;
            transform: translateX(12px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.05);
        }

        .job-info h5 {
            font-weight: 700;
            margin-bottom: 8px;
            color: #1a202c;
            font-size: 1.25rem;
        }

        .job-meta {
            display: flex;
            gap: 25px;
            font-size: 0.85rem;
            color: #718096;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-apply-job {
            padding: 12px 30px;
            border-radius: 0;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
            background: #001f44;
            color: white;
            border: none;
        }

        .btn-apply-job:hover {
            background: var(--accent-gold);
            color: white;
        }

        .culture-img-wrapper {
            position: relative;
            padding: 10px;
            background: white;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.1);
        }

        .culture-img-wrapper img {
            width: 100%;
            height: auto;
        }

        .stat-glass-card {
            background: white;
            padding: 40px;
            border-radius: 0;
            text-align: center;
            border: 1px solid #edf2f7;
            position: relative;
        }

        .stat-glass-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 3px;
            background: var(--accent-gold);
        }

        .stat-glass-card h2 {
            font-size: 3rem;
            font-weight: 800;
            color: #001f44;
            margin-bottom: 8px;
        }

        .stat-glass-card p {
            color: #a0aec0;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.75rem;
            margin-bottom: 0;
        }

        .section-title-premium {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-title-premium span {
            color: var(--accent-gold);
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 3px;
            font-size: 0.8rem;
            display: block;
            margin-bottom: 1.5rem;
        }

        .section-title-premium h2 {
            font-size: 2.8rem;
            font-weight: 900;
            color: #001f44;
        }

        .institutional-tagline {
            font-size: 1.1rem;
            color: #718096;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.8;
        }
        
        .py-120 { padding: 120px 0; }
        
        /* Typography Scale */
        .fw-900 { font-weight: 900; }
        .fw-800 { font-weight: 800; }
        .letter-spacing-1 { letter-spacing: 1px; }
        .letter-spacing-2 { letter-spacing: 2px; }

        @media (max-width: 768px) {
            .job-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 25px;
                padding: 30px;
            }
            .career-hero {
                padding: 140px 0 100px;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
        <div class="container">
            <a class="navbar-brand" style="width: 150px" href="index.php">
                <img src="assets/images/SWC%20Secondary%20Logo%20Light.png" alt="SwiftCapital Logo" height="50">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">Services</a>
                        <ul class="dropdown-menu shadow-lg border-0">
                            <li><a class="dropdown-item py-2" href="services.php#person-banking">Personal Banking</a></li>
                            <li><a class="dropdown-item py-2" href="services.php#business-banking">Business Banking</a></li>
                            <li><a class="dropdown-item py-2" href="services.php#corporate-banking">Corporate Banking</a></li>
                            <li><a class="dropdown-item py-2" href="services.php#loan-banking">Loans & Mortgages</a></li>
                            <li><a class="dropdown-item py-2" href="services.php#investment-banking">Investments</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link active" href="careers.php">Careers</a></li> 
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                <div class="d-flex ms-lg-4">
                    <a href="logout.php" class="btn btn-outline-primary me-2">Log In</a>
                    <a href="register.php" class="btn btn-primary">Open Account</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="career-hero">
        <img src="modern_office_workspace_1773622619884.png" alt="Global Financial Center" class="career-hero-img">
        <div class="career-hero-overlay"></div>
        <div class="container text-center text-lg-start">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-up">
                    <span class="hero-badge">Institutional Excellence</span>
                    <h1 class="display-3 fw-900 mb-4 lh-tight">Architecture of <br><span style="color: var(--accent-gold);">Global Wealth.</span></h1>
                    <p class="lead mb-5 opacity-90 fs-5 institutional-tagline ms-0 text-white-50">Pursue a career where precision meets purpose. Join our collective of financial strategists, risk analysts, and wealth advisors dedicated to the preservation and growth of global assets.</p>
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-4">
                        <a href="#openings" class="btn btn-primary btn-lg px-5 py-3 fw-bold rounded-0" style="background: var(--accent-gold); border: none;">Explore Tenures</a>
                        <a href="#about-culture" class="btn btn-outline-light btn-lg px-5 py-3 fw-bold rounded-0">Institutional Standard</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="container" style="margin-top: -60px;">
        <div class="row g-0 shadow-lg">
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-glass-card">
                    <h2>$42B+</h2>
                    <p>Assets Managed</p>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-glass-card">
                    <h2>140+</h2>
                    <p>Global Markets</p>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-glass-card">
                    <h2>30yr+</h2>
                    <p>Banking Legacy</p>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-glass-card">
                    <h2>AA+</h2>
                    <p>Credit Rating</p>
                </div>
            </div>
        </div>
    </section>

    <!-- The Standard Section -->
    <section class="py-120">
        <div class="container">
            <div class="section-title-premium" data-aos="fade-up">
                <span>The SwiftCapital Philosophy</span>
                <h2>Institutional Integrity.</h2>
                <p class="institutional-tagline mt-3">We believe in the power of stewardship. Every associate at <?php echo $site_name; ?> is a guardian of our clients' legacy and a driver of market stability.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="benefit-card">
                        <div class="benefit-icon"><i class="fa-solid fa-gem"></i></div>
                        <h4 class="fw-800">Legacy Building</h4>
                        <p class="text-secondary mb-0">Navigate complex financial landscapes with a firm that values long-term stability over short-term gains. Build a career that endures.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="benefit-card">
                        <div class="benefit-icon"><i class="fa-solid fa-scale-balanced"></i></div>
                        <h4 class="fw-800">Risk & Compliance</h4>
                        <p class="text-secondary mb-0">Integrity is our primary currency. We foster an environment of rigorous ethical standards and regulatory excellence.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="benefit-card">
                        <div class="benefit-icon"><i class="fa-solid fa-earth-europe"></i></div>
                        <h4 class="fw-800">Global Connectivity</h4>
                        <p class="text-secondary mb-0">Operate within a seamless network of international hubs, providing cross-border solutions for diverse portfolio requirements.</p>
                    </div>
                </div>
                <div class="col-lg-4 mt-lg-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="benefit-card">
                        <div class="benefit-icon"><i class="fa-solid fa-award"></i></div>
                        <h4 class="fw-800">Expert Mentorship</h4>
                        <p class="text-secondary mb-0">Work alongside veterans of the financial world. Our leadership is committed to the growth of our junior partners and advisors.</p>
                    </div>
                </div>
                <div class="col-lg-4 mt-lg-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="benefit-card">
                        <div class="benefit-icon"><i class="fa-solid fa-vault"></i></div>
                        <h4 class="fw-800">Wealth Stewardship</h4>
                        <p class="text-secondary mb-0">Join our mission to provide unparalleled security for family offices and institutional investors around the world.</p>
                    </div>
                </div>
                <div class="col-lg-4 mt-lg-4" data-aos="fade-up" data-aos-delay="600">
                    <div class="benefit-card">
                        <div class="benefit-icon"><i class="fa-solid fa-chart-line"></i></div>
                        <h4 class="fw-800">Capital Performance</h4>
                        <p class="text-secondary mb-0">Harness advanced quantitative data and market insights to deliver consistent value for our global stakeholders.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Culture Section -->
    <section id="about-culture" class="py-120 bg-light">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="culture-img-wrapper">
                        <img src="diverse_professional_team_1773622633861.png" alt="Institutional Leadership Team">
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <span class="text-dark fw-900 text-uppercase mb-3 d-block letter-spacing-2">Governance & Culture</span>
                    <h2 class="display-5 fw-900 mb-4 lh-tight">The intersection of <br>expertise and ethics.</h2>
                    <p class="lead text-secondary mb-4">Our culture is built on the pillars of transparency, accountability, and fiduciary responsibility. We attract individuals who understand that in banking, trust is the highest form of capital.</p>
                    <div class="row g-4 mb-5">
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fa-solid fa-check-double" style="color: var(--accent-gold);"></i>
                                <span class="fw-800 text-dark small text-uppercase">Fiduciary Rigor</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fa-solid fa-check-double" style="color: var(--accent-gold);"></i>
                                <span class="fw-800 text-dark small text-uppercase">Market Agility</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fa-solid fa-check-double" style="color: var(--accent-gold);"></i>
                                <span class="fw-800 text-dark small text-uppercase">Client Privacy</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fa-solid fa-check-double" style="color: var(--accent-gold);"></i>
                                <span class="fw-800 text-dark small text-uppercase">Ethical Gains</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-white border-start border-4" style="border-color: var(--accent-gold) !important;">
                        <p class="mb-0 fs-5 text-dark fw-600">"We don't manage money; we manage futures. Our associates are selected for their character as much as their competence."</p>
                        <footer class="mt-3 fs-7 text-uppercase fw-800 text-muted">— Robert V. Stern, Managing Partner</footer>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Open Tenures Section -->
    <section id="openings" class="py-120 bg-white">
        <div class="container">
            <div class="section-title-premium" data-aos="fade-up">
                <span>Executive Search</span>
                <h2>Active Tenures</h2>
                <p class="institutional-tagline mt-3">Join our global network. We are seeking professionals who demonstrate exceptional analytical capabilities and a commitment to institutional standards.</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Advisory -->
                    <div class="mb-5">
                        <h4 class="fw-900 mb-4 d-flex align-items-center gap-3 text-uppercase fs-6 letter-spacing-1"><i class="fa-solid fa-user-tie" style="color: var(--accent-gold);"></i> Wealth Management & Advisory</h4>
                        <div class="job-card" data-aos="fade-up">
                            <div class="job-info">
                                <h5>Senior Wealth Advisor (Private Banking)</h5>
                                <div class="job-meta">
                                    <span><i class="fa-solid fa-location-dot"></i> Geneva / Zürich</span>
                                    <span><i class="fa-solid fa-briefcase"></i> Partner Track</span>
                                    <span><i class="fa-solid fa-certificate"></i> CFA/ChFC Required</span>
                                </div>
                            </div>
                            <a href="mailto:<?php echo $site_email; ?>?subject=Inquiry: Senior Wealth Advisor" class="btn btn-apply-job">Initialize Inquiry</a>
                        </div>
                        <div class="job-card" data-aos="fade-up" data-aos-delay="100">
                            <div class="job-info">
                                <h5>Associate Portfolio Manager (International Assets)</h5>
                                <div class="job-meta">
                                    <span><i class="fa-solid fa-location-dot"></i> London / Hong Kong</span>
                                    <span><i class="fa-solid fa-briefcase"></i> Full Tenure</span>
                                    <span><i class="fa-solid fa-user-shield"></i> Finra Series 7/66</span>
                                </div>
                            </div>
                            <a href="mailto:<?php echo $site_email; ?>?subject=Inquiry: Portfolio Manager" class="btn btn-apply-job">Initialize Inquiry</a>
                        </div>
                    </div>

                    <!-- Operations -->
                    <div class="mb-5">
                        <h4 class="fw-900 mb-4 d-flex align-items-center gap-3 text-uppercase fs-6 letter-spacing-1"><i class="fa-solid fa-shield-halved" style="color: var(--accent-gold);"></i> Risk, Compliance & Governance</h4>
                        <div class="job-card" data-aos="fade-up">
                            <div class="job-info">
                                <h5>Chief Compliance Officer (Anti-Money Laundering)</h5>
                                <div class="job-meta">
                                    <span><i class="fa-solid fa-location-dot"></i> New York / Singapore</span>
                                    <span><i class="fa-solid fa-briefcase"></i> Executive Tenure</span>
                                    <span><i class="fa-solid fa-gavel"></i> JD or Advanced Legal Degree</span>
                                </div>
                            </div>
                            <a href="mailto:<?php echo $site_email; ?>?subject=Inquiry: Compliance Officer" class="btn btn-apply-job">Initialize Inquiry</a>
                        </div>
                    </div>

                    <!-- Fintech -->
                    <div class="mb-5">
                        <h4 class="fw-900 mb-4 d-flex align-items-center gap-3 text-uppercase fs-6 letter-spacing-1"><i class="fa-solid fa-microchip" style="color: var(--accent-gold);"></i> Financial Engineering & Infrastructure</h4>
                        <div class="job-card" data-aos="fade-up">
                            <div class="job-info">
                                <h5>Quantitative Systems Architect (High-Frequency)</h5>
                                <div class="job-meta">
                                    <span><i class="fa-solid fa-location-dot"></i> Tokyo / Remote</span>
                                    <span><i class="fa-solid fa-briefcase"></i> Technical Tenure</span>
                                    <span><i class="fa-solid fa-code"></i> C++ / Python Expert</span>
                                </div>
                            </div>
                            <a href="mailto:<?php echo $site_email; ?>?subject=Inquiry: Quantitative Architect" class="btn btn-apply-job">Initialize Inquiry</a>
                        </div>
                    </div>

                    <div class="text-center mt-5" data-aos="fade-up">
                        <p class="text-muted mb-4 small text-uppercase fw-800 letter-spacing-1">Interested in a bespoke career path?</p>
                        <a href="mailto:<?php echo $site_email; ?>?subject=Institutional General Inquiry" class="btn btn-outline-dark px-5 py-3 rounded-0 fw-bold border-2">Submit Institutional CV</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer py-5" style="background: #000c1d !important;">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <a class="navbar-brand mb-4 d-block" style="width: 150px" href="index.php">
                        <img src="assets/images/SWC%20Secondary%20Logo%20Dark.png" alt="SwiftCapital Logo" height="50">
                    </a>
                    <p class="text-muted small">SwiftCapital is a global financial institution specialized in private banking, asset management, and corporate financial advisory. Member FDIC. Equal Housing Lender.</p>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
                    <h5 class="text-white mb-4">Products</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Checking Accounts</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Savings Accounts</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Credit Cards</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Loans</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
                    <h5 class="text-white mb-4">Company</h5>
                    <ul class="list-unstyled">
                        <li><a href="about.php" class="text-muted text-decoration-none">About Us</a></li>
                        <li><a href="careers.php" class="text-muted text-decoration-none">Careers</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Press</a></li>
                        <li><a href="contact.php" class="text-muted text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h5 class="text-white mb-4">Compliance</h5>
                    <ul class="list-unstyled">
                        <li><a href="javascript::" class="text-muted text-decoration-none"><?php echo getSetting('contact_email', 'support@SwiftCapital.com'); ?></a></li>
                        <li><a href="javascript::" class="text-muted text-decoration-none"><?php echo getSetting('contact_address', '301 East Water Street, Charlottesville, VA 22904'); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="row copyright mt-5 pt-4 border-top border-secondary opacity-50">
                <div class="col-md-12 text-center">
                    <p class="mb-0 small text-muted">&copy; 2026 SwiftCapital. Precision Engineering for Global Finance.</p>
                </div>
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
