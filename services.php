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

$site_name = getSetting('site_name', 'SwiftCapital');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expertise | <?php echo $site_name; ?> - Institutional Financial Solutions</title>
    <meta name="description" content="Explore the range of institutional financial services offered by <?php echo $site_name; ?>. Custom wealth strategies, asset preservation, and corporate advisory.">
    
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

        .services-hero {
            position: relative;
            padding: 200px 0 140px;
            background: var(--norby-blue);
            color: var(--brand-white);
            overflow: hidden;
        }

        .services-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('modern_office_workspace_1773622619884.png') center/cover;
            opacity: 0.15;
            z-index: 0;
            filter: grayscale(100%);
        }

        .services-hero-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, var(--norby-blue) 0%, rgba(0, 45, 98, 0.7) 100%);
            z-index: 1;
        }

        .services-hero .container { position: relative; z-index: 2; }

        .service-nav-bar {
            background: var(--brand-white);
            border-bottom: 2px solid #f0f0f0;
            position: sticky;
            top: 85px;
            z-index: 100;
            box-shadow: 0 15px 30px rgba(0,0,0,0.03);
        }

        .service-nav-link {
            color: var(--charcoal-gray);
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 2px;
            padding: 25px 30px;
            display: inline-block;
            text-decoration: none;
            transition: all 0.3s;
            border-bottom: 4px solid transparent;
        }

        .service-nav-link:hover, .service-nav-link.active {
            color: var(--brand-red);
            border-bottom-color: var(--brand-red);
        }

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

        .service-showcase-box {
            background: var(--brand-white);
            border: 1px solid #f0f0f0;
            padding: 60px;
            transition: all 0.4s;
            position: relative;
        }

        .service-showcase-box:hover {
            box-shadow: 0 40px 100px rgba(0,0,0,0.06);
            transform: translateY(-5px);
        }

        .service-icon-box {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--norby-blue);
            margin-bottom: 2.5rem;
            border-bottom: 3px solid var(--brand-red);
            padding-bottom: 10px;
        }

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

        .btn-outline-norby { border: 2px solid var(--norby-blue); color: var(--norby-blue); }
        .btn-outline-norby:hover { background: var(--norby-blue); color: var(--brand-white); }

        footer { background: var(--norby-blue); color: var(--brand-white); }

        .py-120 { padding: 120px 0; }

        .service-img-wrapper {
            position: relative;
            padding: 15px;
            background: var(--brand-white);
            box-shadow: 0 50px 100px rgba(0,0,0,0.1);
        }

        .service-img-wrapper img { width: 100%; height: 450px; object-fit: cover; filter: grayscale(100%) contrast(110%); transition: all 0.5s; }
        .service-img-wrapper:hover img { filter: grayscale(0%) contrast(110%); }

        @media (max-width: 991px) {
            .service-nav-bar { display: none; }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom py-3">
        <div class="container">
            <a class="navbar-brand" style="width: 170px" href="index.php">
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
                        <a class="nav-link dropdown-toggle fw-bold px-3 text-uppercase letter-spacing-1 active" href="#" id="expDropdown" data-bs-toggle="dropdown">Expertise</a>
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

    <!-- Services Hero -->
    <section class="services-hero">
        <div class="services-hero-overlay"></div>
        <div class="container" data-aos="fade-up">
            <div class="row">
                <div class="col-lg-8">
                    <span class="d-block mb-3 fw-900 text-uppercase" style="color:var(--brand-red); letter-spacing: 5px; font-size: 0.8rem;">Core Capabilities</span>
                    <h1 class="display-3 fw-900 mb-4">Sophisticated<br><span style="color:var(--brand-red);">Capital Solutions.</span></h1>
                    <p class="lead opacity-75 mb-5">From multi-generational wealth preservation to institutional asset management, our Expertise is designed to navigate the complexities of global finance with absolute precision.</p>
                    <div class="d-flex flex-wrap gap-4">
                        <a href="register.php" class="premium-btn btn-red">Initialize Relationship</a>
                        <a href="contact.php" class="premium-btn btn-outline-white">Consult Advisory</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sticky Service Nav -->
    <div class="service-nav-bar">
        <div class="container text-center">
            <?php foreach ($db_services as $s): ?>
            <a href="#service-<?php echo $s['id']; ?>" class="service-nav-link"><?php echo htmlspecialchars($s['title']); ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Expertise Content -->
    <div class="expertise-content">
        <?php foreach ($db_services as $index => $service): ?>
        <section id="service-<?php echo $service['id']; ?>" class="py-120 <?php echo ($index % 2 != 0) ? 'bg-light' : ''; ?>">
            <div class="container">
                <div class="row align-items-center g-5 <?php echo ($index % 2 != 0) ? 'flex-row-reverse' : ''; ?>">
                    <div class="col-lg-6" data-aos="fade-right">
                        <div class="service-img-wrapper">
                            <img src="<?php echo htmlspecialchars($service['image_url']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>">
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-left">
                        <span class="text-uppercase fw-900 mb-3 d-block ls-2" style="color:var(--brand-red);">Premium Product</span>
                        <h2 class="display-5 fw-900 mb-4" style="color: var(--norby-blue);"><?php echo htmlspecialchars($service['title']); ?>.</h2>
                        <p class="lead mb-5 text-secondary">
                            <?php echo nl2br(htmlspecialchars($service['description'])); ?>
                        </p>
                        
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fa-solid fa-check-circle" style="color: var(--brand-red);"></i>
                                    <span class="fw-bold text-uppercase small letter-spacing-1">Institutional Custody</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fa-solid fa-check-circle" style="color: var(--brand-red);"></i>
                                    <span class="fw-bold text-uppercase small letter-spacing-1">Global Settlement</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fa-solid fa-check-circle" style="color: var(--brand-red);"></i>
                                    <span class="fw-bold text-uppercase small letter-spacing-1">Quant-Analytics</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fa-solid fa-check-circle" style="color: var(--brand-red);"></i>
                                    <span class="fw-bold text-uppercase small letter-spacing-1">Tax Sovereignty</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-4">
                            <a href="register.php" class="premium-btn btn-red">Learn More</a>
                            <a href="contact.php" class="premium-btn btn-outline-norby">Enquire</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php endforeach; ?>
    </div>

    <!-- CTA Section -->
    <section class="py-120 bg-dark text-white text-center" style="background: var(--norby-blue) !important;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="zoom-in">
                    <h2 class="display-4 fw-900 mb-4">Architecting Your <br><span style="color: var(--brand-red);">Financial Sovereignty.</span></h2>
                    <p class="lead opacity-75 mb-5">Our senior advisory board is ready to craft a bespoke capital framework tailored to your institutional requirements.</p>
                    <a href="contact.php" class="premium-btn btn-red btn-lg">Consult Private Advisory</a>
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
                    <p class="opacity-60 small"><?php echo $site_name; ?> is an international financial institution providing bespoke private banking and corporate advisory. Member FDIC. Equal Housing Lender.</p>
                </div>
                <div class="col-lg-2 col-6 ms-auto">
                    <h6 class="text-uppercase fw-900 letter-spacing-2 mb-4" style="color: var(--brand-red);">Governance</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-white-50 small py-1">Risk Policy</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-white-50 small py-1">Compliance</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-white-50 small py-1">Ethics Hotline</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-6">
                    <h6 class="text-uppercase fw-900 letter-spacing-2 mb-4" style="color: var(--brand-red);">Firm</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="about.php" class="nav-link px-0 text-white-50 small py-1">Our Story</a></li>
                        <li class="nav-item"><a href="careers.php" class="nav-link px-0 text-white-50 small py-1">Careers</a></li>
                        <li class="nav-item"><a href="contact.php" class="nav-link px-0 text-white-50 small py-1">Global Network</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 text-center">
                    <img src="assets/images/SWC%20Primary%20Logo%20Light.png" alt="Portrait Logo" height="110" class="mb-3">
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="text-white opacity-50"><i class="bi bi-linkedin fs-5"></i></a>
                        <a href="#" class="text-white opacity-50"><i class="bi bi-twitter-x fs-5"></i></a>
                        <a href="#" class="text-white opacity-50"><i class="bi bi-globe fs-5"></i></a>
                    </div>
                </div>
            </div>
            <div class="mt-5 pt-5 border-top border-secondary opacity-40 text-center">
                <p class="small mb-0">&copy; 2026 <?php echo $site_name; ?>. Global Headquarters. Approved for Institutional Investors Only.</p>
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

        // Scrollspy-like logic for active link
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.service-nav-link');
            
            let current = "";
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 150) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>
