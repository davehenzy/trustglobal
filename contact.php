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

// Handle form submission
$sent = isset($_GET['sent']) && $_GET['sent'] == 1;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name']  ?? '');
    $email      = trim($_POST['email']      ?? '');
    $phone      = trim($_POST['phone']      ?? '');
    $subject    = trim($_POST['subject']    ?? '');
    $message    = trim($_POST['message']    ?? '');

    if ($first_name && $last_name && $email && $subject && $message) {
        $stmt = $pdo->prepare("
            INSERT INTO contact_messages (first_name, last_name, email, phone, subject, message)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$first_name, $last_name, $email, $phone, $subject, $message]);
    }
    header('Location: contact.php?sent=1#contact-form');
    exit;
}

$site_name = getSetting('site_name', 'SwiftCapital');
$contact_email = getSetting('contact_email', 'support@trustsglobal.com');
$contact_address = getSetting('contact_address', 'Global HQ | Zürich, Switzerland');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Private Advisory | <?php echo $site_name; ?> - Institutional Support</title>
    <meta name="description" content="Engage with the <?php echo $site_name; ?> advisory team. Dedicated support for institutional investors and private wealth management.">
    
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

        .contact-hero {
            position: relative;
            padding: 200px 0 140px;
            background: var(--norby-blue);
            color: var(--brand-white);
            overflow: hidden;
        }

        .contact-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('modern_office_workspace_1773622619884.png') center/cover;
            opacity: 0.15;
            z-index: 0;
            filter: grayscale(100%);
        }

        .contact-hero-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, var(--norby-blue) 0%, rgba(0, 45, 98, 0.7) 100%);
            z-index: 1;
        }

        .contact-hero .container { position: relative; z-index: 2; }

        .info-card-premium {
            background: var(--brand-white);
            padding: 40px;
            border: 1px solid #f0f0f0;
            height: 100%;
            transition: all 0.4s;
        }

        .info-card-premium:hover {
            box-shadow: 0 30px 60px rgba(0,0,0,0.06);
            transform: translateY(-5px);
            border-bottom: 4px solid var(--brand-red);
        }

        .info-icon-box {
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

        .contact-form-premium {
            background: var(--brand-white);
            padding: 60px;
            border: 1px solid #f0f0f0;
            box-shadow: 0 50px 100px rgba(0,0,0,0.05);
        }

        .form-label-premium {
            font-weight: 900;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1.5px;
            color: var(--norby-blue);
            margin-bottom: 0.8rem;
        }

        .form-control-premium {
            border-radius: 0;
            border: 1px solid #e2e8f0;
            padding: 15px 20px;
            font-family: var(--primary-font);
        }

        .form-control-premium:focus {
            border-color: var(--brand-red);
            box-shadow: none;
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
            border: none;
        }

        .btn-red { background: var(--brand-red); color: var(--brand-white); }
        .btn-red:hover { background: var(--charcoal-gray); transform: translateY(-3px); color: white; }

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

        footer { background: var(--norby-blue); color: var(--brand-white); }

        .py-120 { padding: 120px 0; }

        .accordion-premium .accordion-item {
            border: none;
            border-bottom: 1px solid #f0f0f0;
            border-radius: 0 !important;
        }

        .accordion-premium .accordion-button {
            padding: 30px 0;
            font-weight: 800;
            color: var(--norby-blue);
            background: transparent;
            box-shadow: none;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .accordion-premium .accordion-button:not(.collapsed) {
            color: var(--brand-red);
        }

        .accordion-premium .accordion-body {
            padding: 0 0 30px;
            color: #666;
            line-height: 1.8;
        }

        @media (max-width: 768px) {
            .contact-form-premium { padding: 30px; }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom py-3">
        <div class="container">
            <a class="navbar-brand" style="width: 170px" href="index.php">
                <img src="assets/images/SWC_Primary_Logo_Light.png" alt="SwiftCapital Logo" height="55">
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
                    <li class="nav-item"><a class="nav-link fw-bold px-3 text-uppercase letter-spacing-1" href="careers.php">Careers</a></li> 
                    <li class="nav-item"><a class="nav-link fw-bold px-3 text-uppercase letter-spacing-1 active" href="contact.php">Advisory</a></li>
                    <li class="nav-item ms-lg-5">
                        <a href="login.php" class="premium-btn btn-red" style="padding: 12px 30px;">Client Access</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contact Hero -->
    <section class="contact-hero">
        <div class="contact-hero-overlay"></div>
        <div class="container text-center" data-aos="fade-up">
            <span class="d-block mb-3 fw-900 text-uppercase" style="color:var(--brand-red); letter-spacing: 5px; font-size: 0.8rem;">Private Advisory</span>
            <h1 class="display-3 fw-900 mb-4">Engage the <br><span style="color:var(--brand-red);">Strategic Board.</span></h1>
            <p class="lead opacity-75 mx-auto" style="max-width: 750px;">Our relationship managers are available for institutional inquiries and private wealth consultations across all global timezones.</p>
        </div>
    </section>

    <!-- Info Section -->
    <section class="py-120">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="info-card-premium">
                        <div class="info-icon-box"><i class="fa-solid fa-envelope-open-text"></i></div>
                        <h5 class="fw-900 mb-3">Institutional Inquiries</h5>
                        <p class="text-secondary mb-4 small">For formal proposals and partnership opportunities.</p>
                        <a href="mailto:<?php echo $contact_email; ?>" class="fw-900 text-decoration-none" style="color: var(--brand-red); letter-spacing: 1px;"><?php echo $contact_email; ?></a>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="info-card-premium">
                        <div class="info-icon-box"><i class="fa-solid fa-building-columns"></i></div>
                        <h5 class="fw-900 mb-3">Global Headquarters</h5>
                        <p class="text-secondary mb-4 small">Our central hub for governance and strategic operations.</p>
                        <span class="fw-900" style="color: var(--norby-blue); letter-spacing: 1px;"><?php echo $contact_address; ?></span>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="info-card-premium">
                        <div class="info-icon-box"><i class="fa-solid fa-headset"></i></div>
                        <h5 class="fw-900 mb-3">Client Concierge</h5>
                        <p class="text-secondary mb-4 small">24/7 dedicated support for private banking clients.</p>
                        <a href="#" class="btn btn-outline-dark rounded-0 fw-900 px-4 py-2 small letter-spacing-1">INITIALIZE CHAT</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Form Section -->
    <section class="py-120 bg-light" id="contact-form">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="contact-form-premium" data-aos="fade-up">
                        <div class="row align-items-center mb-5">
                            <div class="col-md-8">
                                <h2 class="fw-900" style="color: var(--norby-blue);">Initialize Consultation</h2>
                                <p class="text-secondary mb-0">Our senior advisory board will review your inquiry within one business cycle.</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <span class="badge bg-danger rounded-pill px-3 py-2 fw-900 text-uppercase">Priority Handling</span>
                            </div>
                        </div>

                        <?php if ($sent): ?>
                        <div class="p-4 mb-5" style="background: rgba(226, 25, 54, 0.05); border-left: 5px solid var(--brand-red);">
                            <h6 class="fw-900 text-danger mb-1">Transmission Successful.</h6>
                            <p class="small text-danger mb-0">An advisor will communicate via your registered credentials shortly.</p>
                        </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="row g-4">
                                <div class="col-md-6 text-start">
                                    <label class="form-label-premium">Given Name</label>
                                    <input type="text" class="form-control form-control-premium" name="first_name" required>
                                </div>
                                <div class="col-md-6 text-start">
                                    <label class="form-label-premium">Surname</label>
                                    <input type="text" class="form-control form-control-premium" name="last_name" required>
                                </div>
                                <div class="col-md-6 text-start">
                                    <label class="form-label-premium">Institutional Email</label>
                                    <input type="email" class="form-control form-control-premium" name="email" required>
                                </div>
                                <div class="col-md-6 text-start">
                                    <label class="form-label-premium">Mobile/Satellite</label>
                                    <input type="tel" class="form-control form-control-premium" name="phone">
                                </div>
                                <div class="col-12 text-start">
                                    <label class="form-label-premium">Interest Area</label>
                                    <select class="form-select form-control-premium" name="subject" required>
                                        <option value="" disabled selected>Select Perspective</option>
                                        <option value="wealth">Private Wealth Management</option>
                                        <option value="asset">Institutional Asset Strategy</option>
                                        <option value="equity">Capital Markets & Equity</option>
                                        <option value="trust">Trust & Fiduciary Services</option>
                                        <option value="other">Other Institutional Inquiry</option>
                                    </select>
                                </div>
                                <div class="col-12 text-start">
                                    <label class="form-label-premium">Advisory Requirements</label>
                                    <textarea class="form-control form-control-premium" name="message" rows="5" required></textarea>
                                </div>
                                <div class="col-12 text-center mt-5">
                                    <button type="submit" class="premium-btn btn-red w-100 py-4">Transmit Inquiry</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-120">
        <div class="container">
            <div class="section-title-premium" data-aos="fade-up">
                <span>Governance FAQ</span>
                <h2>Operational Clarity.</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion accordion-premium" id="faqCenter">
                        <div class="accordion-item" data-aos="fade-up">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
                                    What is the standard response cycle?
                                </button>
                            </h2>
                            <div id="q1" class="accordion-collapse collapse show" data-bs-parent="#faqCenter">
                                <div class="accordion-body">
                                    Our firm operates under a strict 24-hour response mandate for all institutional inquiries. Private banking clients with dedicated managers receive real-time communication via secure satellite lines.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="100">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
                                    Are physical consultations available?
                                </button>
                            </h2>
                            <div id="q2" class="accordion-collapse collapse" data-bs-parent="#faqCenter">
                                <div class="accordion-body">
                                    Yes. We maintain physical presence in major financial hubs including Zürich, London, Singapore, and New York. Consultations at these locations are for accredited institutional investors only.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="200">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q3">
                                    How is communication secured?
                                </button>
                            </h2>
                            <div id="q3" class="accordion-collapse collapse" data-bs-parent="#faqCenter">
                                <div class="accordion-body">
                                    All digital interactions are protected by AES-256 encryption and FIPS 140-2 compliant protocols. We also offer PGP-encrypted email communication for highly sensitive documents.
                                </div>
                            </div>
                        </div>
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
                        <img src="assets/images/SWC_Primary_Logo_Dark.png" alt="SwiftCapital Logo" height="55">
                    </a>
                    <p class="opacity-60 small"><?php echo $site_name; ?> is a global financial institution specialized in private banking, asset management, and corporate financial advisory. Member FDIC. Equal Housing Lender.</p>
                </div>
                <div class="col-lg-2 ms-auto">
                    <h6 class="text-uppercase fw-900 letter-spacing-2 mb-4" style="color: var(--brand-red);">Governance</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-white-50 small py-1">Risk Policy</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-white-50 small py-1">Compliance</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-white-50 small py-1">Ethics Charter</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="text-uppercase fw-900 letter-spacing-2 mb-4" style="color: var(--brand-red);">The Firm</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="about.php" class="nav-link px-0 text-white-50 small py-1">Our Story</a></li>
                        <li class="nav-item"><a href="careers.php" class="nav-link px-0 text-white-50 small py-1">Careers</a></li>
                        <li class="nav-item"><a href="contact.php" class="nav-link px-0 text-white-50 small py-1">Advisory</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 text-center">
                    <img src="assets/images/SWC_Primary_Logo_Dark.png" alt="Portrait Logo" height="110" class="mb-3">
                    <p class="opacity-40 small mb-0">Registered Global HQ | Zürich</p>
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
