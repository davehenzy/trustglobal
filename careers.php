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
            letter-spacing: -0.01em;
            font-weight: 800;
        }

        .career-hero {
            position: relative;
            padding: 180px 0 140px;
            background: var(--norby-blue);
            overflow: hidden;
            color: var(--brand-white);
        }

        .career-hero-img {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            object-fit: cover;
            opacity: 0.15;
            z-index: 0;
            filter: grayscale(100%);
        }

        .career-hero-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to right, var(--norby-blue) 0%, rgba(0, 45, 98, 0.7) 100%);
            z-index: 1;
        }

        .career-hero .container {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-block;
            padding: 8px 20px;
            background: var(--brand-red);
            border-radius: 0;
            font-weight: 900;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 2rem;
            color: var(--brand-white);
        }

        .benefit-card {
            background: var(--brand-white);
            border: 1px solid #f0f0f0;
            border-radius: 0;
            padding: 45px 35px;
            transition: all 0.4s ease;
            height: 100%;
            position: relative;
        }

        .benefit-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.08);
            border-bottom: 4px solid var(--brand-red);
        }

        .benefit-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--norby-blue);
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--brand-red);
            padding-bottom: 10px;
        }

        .job-card {
            background: var(--brand-white);
            border: 1px solid #eee;
            border-left: 5px solid var(--norby-blue);
            border-radius: 0;
            padding: 35px 40px;
            margin-bottom: 20px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .job-card:hover {
            border-left-color: var(--brand-red);
            background: #fafafa;
            transform: translateX(10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .job-info h5 {
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--norby-blue);
            font-size: 1.25rem;
        }

        .job-meta {
            display: flex;
            gap: 25px;
            font-size: 0.85rem;
            color: #777;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-apply-job {
            padding: 14px 35px;
            border-radius: 0;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.8rem;
            background: var(--norby-blue);
            color: var(--brand-white);
            border: none;
            transition: all 0.3s;
        }

        .btn-apply-job:hover {
            background: var(--brand-red);
            color: var(--brand-white);
        }

        .stat-banner {
            background: var(--brand-white);
            margin-top: -60px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.1);
            position: relative;
            z-index: 10;
        }

        .stat-col {
            padding: 50px 40px;
            text-align: center;
            border-right: 1px solid #f1f5f9;
        }

        .stat-col:last-child { border-right: none; }
        .stat-col h2 { font-size: 2.8rem; font-weight: 900; color: var(--norby-blue); margin-bottom: 5px; }
        .stat-col p { font-size: 0.75rem; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; color: var(--brand-red); margin: 0; }

        .section-title-premium {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-title-premium span {
            color: var(--brand-red);
            text-transform: uppercase;
            font-weight: 900;
            letter-spacing: 4px;
            font-size: 0.8rem;
            display: block;
            margin-bottom: 1.5rem;
        }

        .section-title-premium h2 {
            font-size: 2.8rem;
            font-weight: 900;
            color: var(--norby-blue);
        }

        .culture-section {
            background: var(--norby-blue);
            color: var(--brand-white);
            padding: 150px 0;
        }

        .culture-img-wrapper {
            position: relative;
            padding: 15px;
            background: var(--brand-white);
            box-shadow: 0 40px 100px rgba(0,0,0,0.3);
        }

        .culture-img-wrapper img { width: 100%; height: auto; display: block; filter: contrast(110%); }

        .btn-executive {
            padding: 18px 45px;
            border-radius: 0;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            transition: all 0.4s;
            display: inline-block;
            text-decoration: none;
        }

        .btn-red { background: var(--brand-red); color: var(--brand-white); }
        .btn-red:hover { background: var(--charcoal-gray); color: var(--brand-white); transform: translateY(-3px); }

        .btn-outline-white { border: 2px solid var(--brand-white); color: var(--brand-white); }
        .btn-outline-white:hover { background: var(--brand-white); color: var(--norby-blue); }

        .institutional-tagline {
            font-family: var(--primary-font);
            font-size: 1.1rem;
            color: #666;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.8;
        }
        
        .white-tagline { color: rgba(255,255,255,0.7); }

        footer {
            background: var(--norby-blue);
            color: var(--brand-white);
        }

        footer .nav-link { color: rgba(255,255,255,0.6) !important; font-size: 0.9rem; transition: all 0.3s; }
        footer .nav-link:hover { color: var(--brand-white) !important; padding-left: 5px; }

        .py-120 { padding: 120px 0; }

        @media (max-width: 768px) {
            .stat-col { border-right: none; border-bottom: 1px solid #f1f5f9; }
            .career-hero h1 { font-size: 3rem; }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom py-3">
        <div class="container">
            <a class="navbar-brand" style="width: 170px" href="index.php">
                <!-- Landscape Secondary Logo used in Navbar -->
                <img src="assets/images/SWC%20Secondary%20Logo%20Dark.png" alt="SwiftCapital Logo Landscape" height="55">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bi bi-list fs-1" style="color: var(--norby-blue);"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link fw-bold px-3 text-uppercase letter-spacing-1" href="index.php">Institutional</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold px-3 text-uppercase letter-spacing-1" href="about.php">Our Story</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-bold px-3 text-uppercase letter-spacing-1" href="#" id="expDropdown" data-bs-toggle="dropdown">Expertise</a>
                        <ul class="dropdown-menu border-0 shadow-lg p-0 rounded-0">
                            <li><a class="dropdown-item py-3 fw-bold border-bottom" href="services.php#person-banking">Private Banking</a></li>
                            <li><a class="dropdown-item py-3 fw-bold border-bottom" href="services.php#business-banking">Asset Management</a></li>
                            <li><a class="dropdown-item py-3 fw-bold" href="services.php#corporate-banking">Corporate Finance</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link fw-bold px-3 text-uppercase letter-spacing-1 active" href="careers.php">Careers</a></li> 
                    <li class="nav-item"><a class="nav-link fw-bold px-3 text-uppercase letter-spacing-1" href="contact.php">Advisory</a></li>
                    <li class="nav-item ms-lg-5">
                        <a href="login.php" class="btn btn-apply-job" style="padding: 12px 30px;">Client Access</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="career-hero">
        <img src="modern_office_workspace_1773622619884.png" alt="Institutional Workspace" class="career-hero-img">
        <div class="career-hero-overlay"></div>
        <div class="container">
            <div class="row align-items-center text-center text-lg-start">
                <div class="col-lg-8" data-aos="fade-up">
                    <span class="hero-badge">Careers at <?php echo $site_name; ?></span>
                    <h1 class="display-3 mb-4 lh-tight">Architecture of <br><span style="color: var(--brand-red);">Capital Excellence.</span></h1>
                    <p class="lead mb-5 white-tagline">Pursue a career where precision meets purpose. Join our collective of financial strategists and wealth advisors dedicated to the preservation of global assets.</p>
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-4">
                        <a href="#openings" class="btn-executive btn-red">Explore Tenures</a>
                        <a href="#culture" class="btn-executive btn-outline-white">The Standard</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stat Banner -->
    <section class="container">
        <div class="stat-banner row g-0">
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
                    <p>Governance</p>
                </div>
            </div>
        </div>
    </section>

    <!-- The Standard Section -->
    <section class="py-120">
        <div class="container">
            <div class="section-title-premium" data-aos="fade-up">
                <span>The Institutional Standard</span>
                <h2>Global Integrity.</h2>
                <p class="institutional-tagline mt-3">We believe in the power of stewardship. Every associate at <?php echo $site_name; ?> is a guardian of our clients' legacy and a driver of market stability.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="benefit-card">
                        <div class="benefit-icon"><i class="fa-solid fa-gem"></i></div>
                        <h4>Legacy Building</h4>
                        <p class="text-secondary mb-0">Navigate complex financial landscapes with a firm that values long-term stability over short-term gains.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="benefit-card">
                        <div class="benefit-icon"><i class="fa-solid fa-scale-balanced"></i></div>
                        <h4>Risk Governance</h4>
                        <p class="text-secondary mb-0">Integrity is our primary currency. We foster an environment of rigorous ethical standards and compliance.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="benefit-card">
                        <div class="benefit-icon"><i class="fa-solid fa-earth-europe"></i></div>
                        <h4>Global Network</h4>
                        <p class="text-secondary mb-0">Operate within a seamless network of international hubs, providing cross-border solutions for diverse portfolios.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Culture Section -->
    <section id="culture" class="culture-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="culture-img-wrapper">
                        <img src="diverse_professional_team_1773622633861.png" alt="Executive Leadership">
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <span class="hero-badge mb-3">Our Core Philosophy</span>
                    <h2 class="display-5 mb-4 lh-tight">Expertise, Ethics, <br>and Transparency.</h2>
                    <p class="lead opacity-80 mb-5">Our culture is built on the pillars of transparency, accountability, and fiduciary responsibility. We attract individuals who understand that in banking, trust is the highest form of capital.</p>
                    
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fa-solid fa-check-circle fs-5" style="color: var(--brand-red);"></i>
                                <span class="fw-bold text-uppercase small letter-spacing-1">Fiduciary Rigor</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fa-solid fa-check-circle fs-5" style="color: var(--brand-red);"></i>
                                <span class="fw-bold text-uppercase small letter-spacing-1">Market Agility</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 border-start border-4 border-danger" style="background: rgba(255,255,255,0.05);">
                        <p class="mb-0 fs-5 fw-bold font-italic" style="font-style: italic;">"Our associates are selected for their character as much as their competence. We don't manage money; we manage legacies."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Open Openings Section -->
    <section id="openings" class="py-120">
        <div class="container">
            <div class="section-title-premium" data-aos="fade-up">
                <span>Active Executive Search</span>
                <h2>Active Tenures</h2>
                <p class="institutional-tagline mt-3">We are seeking professionals who demonstrate exceptional analytical capabilities and a commitment to institutional standards.</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="mb-5">
                        <h6 class="fw-900 text-uppercase letter-spacing-2 mb-4" style="color: var(--brand-red);">Wealth Management</h6>
                        <div class="job-card" data-aos="fade-up">
                            <div class="job-info">
                                <h5>Senior Wealth Advisor (Private Banking)</h5>
                                <div class="job-meta">
                                    <span><i class="fa-solid fa-location-dot me-2"></i> Geneva / Zürich</span>
                                    <span><i class="fa-solid fa-briefcase me-2"></i> Partner Track</span>
                                </div>
                            </div>
                            <a href="mailto:<?php echo $site_email; ?>?subject=Inquiry: Wealth Advisor" class="btn-apply-job text-decoration-none">Initialize inquiry</a>
                        </div>
                        
                        <div class="job-card" data-aos="fade-up" data-aos-delay="100">
                            <div class="job-info">
                                <h5>Associate Portfolio Manager (Institutional)</h5>
                                <div class="job-meta">
                                    <span><i class="fa-solid fa-location-dot me-2"></i> London / Singapore</span>
                                    <span><i class="fa-solid fa-certificate me-2"></i> CFA Required</span>
                                </div>
                            </div>
                            <a href="mailto:<?php echo $site_email; ?>?subject=Inquiry: Portfolio Manager" class="btn-apply-job text-decoration-none">Initialize inquiry</a>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h6 class="fw-900 text-uppercase letter-spacing-2 mb-4" style="color: var(--brand-red);">Compliance & Risk</h6>
                        <div class="job-card" data-aos="fade-up">
                            <div class="job-info">
                                <h5>Chief Regulatory Officer (AML/KYC)</h5>
                                <div class="job-meta">
                                    <span><i class="fa-solid fa-location-dot me-2"></i> New York / Tokyo</span>
                                    <span><i class="fa-solid fa-shield me-2"></i> Executive Tenure</span>
                                </div>
                            </div>
                            <a href="mailto:<?php echo $site_email; ?>?subject=Inquiry: Compliance Officer" class="btn-apply-job text-decoration-none">Initialize inquiry</a>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <p class="text-muted fw-bold small text-uppercase mb-4">Interested in a bespoke career path?</p>
                        <a href="mailto:<?php echo $site_email; ?>?subject=Institutional General Inquiry" class="btn btn-outline-dark px-5 py-3 rounded-0 fw-bold border-2">SUBMIT INSTITUTIONAL CV</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-120">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <a class="navbar-brand mb-4 d-block" style="width: 170px" href="index.php">
                        <!-- Landscape Light Secondary Logo used on Blue Background -->
                        <img src="assets/images/SWC%20Secondary%20Logo%20Light.png" alt="SwiftCapital Logo Light" height="55">
                    </a>
                    <p class="opacity-60 small">SwiftCapital is a global financial institution specialized in private banking, asset management, and corporate financial advisory. Member FDIC. Equal Housing Lender.</p>
                </div>
                <div class="col-lg-2 ms-auto">
                    <h6 class="text-uppercase fw-900 letter-spacing-2 mb-4" style="color: var(--brand-red);">Expertise</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="#" class="nav-link px-0">Private Wealth</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0">Asset Strategy</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0">Equity Markets</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="text-uppercase fw-900 letter-spacing-2 mb-4" style="color: var(--brand-red);">Firm</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="about.php" class="nav-link px-0">Our Story</a></li>
                        <li class="nav-item"><a href="careers.php" class="nav-link px-0">Careers</a></li>
                        <li class="nav-item"><a href="contact.php" class="nav-link px-0">Global Network</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 text-center">
                    <img src="assets/images/SWC%20Primary%20Logo%20Light.png" alt="SwiftCapital Portrait" height="100" class="mb-4">
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="text-white opacity-50"><i class="bi bi-linkedin fs-5"></i></a>
                        <a href="#" class="text-white opacity-50"><i class="bi bi-twitter-x fs-5"></i></a>
                        <a href="#" class="text-white opacity-50"><i class="bi bi-globe fs-5"></i></a>
                    </div>
                </div>
            </div>
            <div class="mt-5 pt-5 border-top border-secondary text-center">
                <p class="opacity-50 small mb-0">&copy; 2026 <?php echo $site_name; ?>. Global Headquarters. Approved for Institutional Investors Only.</p>
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
