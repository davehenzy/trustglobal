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
    <title>Our Story | <?php echo $site_name; ?> - A Heritage of Financial Excellence</title>
    <meta name="description" content="Learn about the heritage and institutional values of <?php echo $site_name; ?>. Our commitment to global asset preservation and strategic wealth management.">
    
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

        .about-hero {
            position: relative;
            padding: 200px 0 140px;
            background: var(--norby-blue);
            color: var(--brand-white);
            overflow: hidden;
        }

        .about-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('modern_office_workspace_1773622619884.png') center/cover;
            opacity: 0.15;
            z-index: 0;
            filter: grayscale(100%);
        }

        .about-hero-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, var(--norby-blue) 0%, rgba(0, 45, 98, 0.6) 100%);
            z-index: 1;
        }

        .about-hero .container { position: relative; z-index: 2; }

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

        .heritage-img-box {
            position: relative;
            padding: 20px;
            background: var(--brand-white);
            box-shadow: 0 50px 100px rgba(0,0,0,0.1);
        }

        .heritage-img-box img { width: 100%; height: auto; display: block; filter: contrast(110%); }

        .value-card-premium {
            background: var(--brand-white);
            padding: 50px 40px;
            border: 1px solid #f0f0f0;
            height: 100%;
            transition: all 0.4s;
            position: relative;
        }

        .value-card-premium:hover {
            border-bottom: 4px solid var(--brand-red);
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.06);
        }

        .value-icon-box {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--norby-blue);
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--brand-red);
            padding-bottom: 10px;
        }

        .timeline-premium {
            position: relative;
            padding: 80px 0;
        }

        .timeline-premium::before {
            content: '';
            position: absolute;
            top: 0; left: 50%;
            width: 2px; height: 100%;
            background: #e2e8f0;
            transform: translateX(-50%);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 100px;
            width: 50%;
        }

        .timeline-item:last-child { margin-bottom: 0; }

        .timeline-item:nth-child(odd) { padding-right: 80px; text-align: right; }
        .timeline-item:nth-child(even) { margin-left: 50%; padding-left: 80px; }

        .timeline-dot {
            position: absolute;
            top: 10px; right: -8px;
            width: 16px; height: 16px;
            background: var(--brand-red);
            border: 4px solid var(--brand-white);
            border-radius: 50%;
            z-index: 2;
            box-shadow: 0 0 0 4px rgba(226, 25, 54, 0.1);
        }

        .timeline-item:nth-child(even) .timeline-dot { left: -8px; }

        .timeline-year {
            font-size: 1.2rem;
            font-weight: 900;
            color: var(--brand-red);
            margin-bottom: 0.5rem;
            display: block;
        }

        .timeline-content h4 { font-weight: 900; color: var(--norby-blue); margin-bottom: 1rem; }
        .timeline-content p { color: #666; line-height: 1.8; }

        .premium-btn {
            padding: 18px 45px;
            border-radius: 0;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            transition: all 0.3s;
            display: inline-block;
            text-decoration: none;
        }

        .btn-red { background: var(--brand-red); color: var(--brand-white); border: none; }
        .btn-red:hover { background: var(--charcoal-gray); transform: translateY(-3px); box-shadow: 0 10px 30px rgba(226, 25, 54, 0.3); color: white; }

        footer { background: var(--norby-blue); color: var(--brand-white); }

        .py-120 { padding: 120px 0; }

        @media (max-width: 991px) {
            .timeline-premium::before { left: 0; }
            .timeline-item { width: 100%; text-align: left !important; padding-left: 40px !important; margin-left: 0 !important; }
            .timeline-dot { left: -8px !important; }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom py-3">
        <div class="container">
            <a class="navbar-brand" style="width: 170px" href="index.php">
                <img src="assets/images/SWC%20Secondary%20Logo%20Dark.png" alt="SwiftCapital Logo" height="55">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bi bi-list fs-1" style="color: var(--norby-blue);"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link fw-bold px-3 text-uppercase letter-spacing-1" href="index.php">Institutional</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold px-3 text-uppercase letter-spacing-1 active" href="about.php">Our Story</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-bold px-3 text-uppercase letter-spacing-1" href="#" id="expDropdown" data-bs-toggle="dropdown">Expertise</a>
                        <ul class="dropdown-menu border-0 shadow-lg p-0 rounded-0">
                            <li><a class="dropdown-item py-3 fw-bold border-bottom" href="services.php#person-banking">Private Banking</a></li>
                            <li><a class="dropdown-item py-3 fw-bold border-bottom" href="services.php#business-banking">Asset Management</a></li>
                            <li><a class="dropdown-item py-3 fw-bold" href="services.php#corporate-banking">Corporate Finance</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link fw-bold px-3 text-uppercase letter-spacing-1" href="careers.php">Careers</a></li> 
                    <li class="nav-item"><a class="nav-link fw-bold px-3 text-uppercase letter-spacing-1" href="contact.php">Advisory</a></li>
                    <li class="nav-item ms-lg-5">
                        <a href="login.php" class="premium-btn btn-red" style="padding: 12px 30px;">Client Access</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- About Hero -->
    <section class="about-hero">
        <div class="about-hero-overlay"></div>
        <div class="container text-center" data-aos="fade-up">
            <span class="d-block mb-3 fw-900 text-uppercase" style="color:var(--brand-red); letter-spacing: 5px; font-size: 0.8rem;">Institutional Heritage</span>
            <h1 class="display-3 fw-900 mb-4">A Legacy of <br><span style="color:var(--brand-red);">Capital Stewardship.</span></h1>
            <p class="lead opacity-75 mx-auto" style="max-width: 700px;">Since our inception, <?php echo $site_name; ?> has been defined by a commitment to the preservation and growth of institutional wealth through rigorous governance and strategic insight.</p>
        </div>
    </section>

    <!-- Heritage Section -->
    <section class="py-120">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="heritage-img-box">
                        <img src="modern_office_workspace_1773622619884.png" alt="Institutional Heritage">
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <span class="text-uppercase fw-900 mb-3 d-block ls-2" style="color:var(--brand-red);">Our Foundation</span>
                    <h2 class="display-5 fw-900 mb-4" style="color: var(--norby-blue);">Governance Beyond <br>Standard Banking.</h2>
                    <p class="lead mb-4">At <?php echo $site_name; ?>, we don't just manage assets; we protect legacies. Our foundational philosophy is built on the pillars of absolute discretion, precision in execution, and unwavering fiduciary responsibility.</p>
                    <p class="text-secondary mb-5">Founded to serve the complex needs of institutional investors and noble family offices, our firm has evolved into a global benchmark for private banking excellence. We combine traditional values with a quant-driven approach to navigate the complexities of modern capital markets.</p>
                    <div class="d-flex gap-4">
                        <div class="text-center p-4 bg-light border-bottom border-danger border-4">
                            <h2 class="fw-900 text-danger mb-0">25+</h2>
                            <p class="text-uppercase small fw-800 ls-1 mb-0">Years Tenure</p>
                        </div>
                        <div class="text-center p-4 bg-light border-bottom border-danger border-4">
                            <h2 class="fw-900 text-danger mb-0">140</h2>
                            <p class="text-uppercase small fw-800 ls-1 mb-0">Global Hubs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-120 bg-light">
        <div class="container">
            <div class="section-title-premium" data-aos="fade-up">
                <span>The Institutional Standard</span>
                <h2>Our Core Sovereignty.</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="value-card-premium">
                        <div class="value-icon-box"><i class="fa-solid fa-scale-balanced"></i></div>
                        <h4>Absolute Integrity</h4>
                        <p class="text-secondary mb-0">Professional ethics is our primary currency. Every decision is measured against the highest standards of regulatory compliance and moral rigor.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="value-card-premium">
                        <div class="value-icon-box"><i class="fa-solid fa-chess-knight"></i></div>
                        <h4>Strategic Precision</h4>
                        <p class="text-secondary mb-0">We utilize sophisticated analytical frameworks to identify value and mitigate risk, ensuring capital stability in volatile global markets.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="value-card-premium">
                        <div class="value-icon-box"><i class="fa-solid fa-handshake"></i></div>
                        <h4>Stewardship</h4>
                        <p class="text-secondary mb-0">As trustees of significant wealth, we treat our clients' assets with the same attention and foresight as our own institutional capital.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Journey Section -->
    <section class="py-120">
        <div class="container">
            <div class="section-title-premium" data-aos="fade-up">
                <span>Milestones of Growth</span>
                <h2>The Institutional Journey.</h2>
            </div>

            <div class="timeline-premium">
                <div class="timeline-item" data-aos="fade-right">
                    <div class="timeline-dot"></div>
                    <span class="timeline-year">1995</span>
                    <div class="timeline-content">
                        <h4>Genesis of Excellence</h4>
                        <p>Established as a private advisory firm in Zurich, focused on specialized capital preservation for European family offices.</p>
                    </div>
                </div>
                <div class="timeline-item" data-aos="fade-left">
                    <div class="timeline-dot"></div>
                    <span class="timeline-year">2005</span>
                    <div class="timeline-content">
                        <h4>Global Expansion</h4>
                        <p>Expansion into London and Singapore, establishing a seamless 24/7 global execution desk for primary capital markets.</p>
                    </div>
                </div>
                <div class="timeline-item" data-aos="fade-right">
                    <div class="timeline-dot"></div>
                    <span class="timeline-year">2015</span>
                    <div class="timeline-content">
                        <h4>Technological Shift</h4>
                        <p>Integration of FIPS 140-2 security standards and quantum-resistant encryption across all institutional infrastructure.</p>
                    </div>
                </div>
                <div class="timeline-item" data-aos="fade-left">
                    <div class="timeline-dot"></div>
                    <span class="timeline-year">Today</span>
                    <div class="timeline-content">
                        <h4>The Global Standard</h4>
                        <p>Serving millions of clients with over $42B in managed capital, setting the standard for institutional private banking.</p>
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
                        <img src="assets/images/SWC%20Secondary%20Logo%20Light.png" alt="SwiftCapital Logo" height="55">
                    </a>
                    <p class="opacity-60 small"><?php echo $site_name; ?> is an international financial institution Providing bespoke private banking and corporate advisory. Member FDIC. Equal Housing Lender.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="text-white opacity-50"><i class="bi bi-linkedin fs-5"></i></a>
                        <a href="#" class="text-white opacity-50"><i class="bi bi-twitter-x fs-5"></i></a>
                        <a href="#" class="text-white opacity-50"><i class="bi bi-globe fs-5"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-6 ms-auto">
                    <h6 class="text-uppercase fw-900 letter-spacing-2 mb-4" style="color: var(--brand-red);">Governance</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-white-50 small py-1">Risk Management</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-white-50 small py-1">Compliance</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-white-50 small py-1">Ethics Charter</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="text-uppercase fw-900 letter-spacing-2 mb-4" style="color: var(--brand-red);">The Firm</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="about.php" class="nav-link px-0 text-white-50 small py-1">Our Story</a></li>
                        <li class="nav-item"><a href="careers.php" class="nav-link px-0 text-white-50 small py-1">Careers</a></li>
                        <li class="nav-item"><a href="contact.php" class="nav-link px-0 text-white-50 small py-1">Advisory</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 text-center">
                    <img src="assets/images/SWC%20Primary%20Logo%20Light.png" alt="Portrait Logo" height="110" class="mb-3">
                    <p class="opacity-40 small mb-0">Global HQ | Zürich, Switzerland</p>
                </div>
            </div>
            <div class="mt-5 pt-5 border-top border-secondary opacity-40 text-center">
                <p class="small mb-0">&copy; 2026 <?php echo $site_name; ?>. Approved for Institutional Investors Only.</p>
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
