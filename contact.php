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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us — SwiftCapital</title>
    <meta name="description" content="Get in touch with SwiftCapital. Reach our 24/7 support team via email, phone, or visit our headquarters.">
    <!--favicon icon-->
    <link rel="icon" href="assets/images/SWC%20Icon%20Dark.png" type="image/png" sizes="16x16">
    <!-- Bootstrap CSS -->
    <link href="npm/bootstrap%405.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="npm/bootstrap-icons%401.11.0/font/bootstrap-icons.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --indigo: #6366f1;
            --indigo-dark: #4f46e5;
        }
        html { scroll-behavior: smooth; }
        .fw-700 { font-weight: 700; }
        .fw-800 { font-weight: 800; }
        .text-xs  { font-size: .75rem; }
        .text-sm  { font-size: .875rem; }

        /* ── Hero ── */
        .contact-hero {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 55vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        .contact-hero .orb {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        /* ── Info Cards ── */
        .info-card {
            background: #fff;
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid #f1f5f9;
            transition: transform .3s, box-shadow .3s;
        }
        .info-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(99,102,241,0.12);
        }
        .info-icon {
            width: 56px; height: 56px;
            border-radius: 16px;
            background: rgba(99,102,241,0.12);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            color: var(--indigo);
            margin-bottom: 1rem;
        }

        /* ── Contact Form ── */
        .contact-form-wrap {
            background: #fff;
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            border: 1px solid #f1f5f9;
        }
        .contact-form-wrap .form-control,
        .contact-form-wrap .form-select {
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            padding: .75rem 1rem;
            font-size: .95rem;
            background: #f8fafc;
            transition: border-color .2s, box-shadow .2s;
        }
        .contact-form-wrap .form-control:focus,
        .contact-form-wrap .form-select:focus {
            border-color: var(--indigo);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
        }
        .contact-form-wrap .form-label {
            font-size: .8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #64748b;
            margin-bottom: .4rem;
        }
        .btn-submit {
            background: linear-gradient(135deg, var(--indigo), #8b5cf6);
            border: none;
            border-radius: 12px;
            padding: .85rem 2.5rem;
            font-weight: 800;
            font-size: 1rem;
            color: #fff;
            transition: opacity .2s, transform .2s;
        }
        .btn-submit:hover { opacity: .9; transform: translateY(-2px); }

        /* ── Support Options ── */
        .support-card {
            border-radius: 20px;
            background: #fff;
            padding: 2rem;
            border: 1.5px solid #f1f5f9;
            transition: transform .3s, box-shadow .3s;
            height: 100%;
        }
        .support-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(99,102,241,0.1);
        }
        .support-icon-wrap {
            width: 64px; height: 64px;
            border-radius: 18px;
            background: rgba(99,102,241,0.1);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.7rem;
            color: var(--indigo);
            margin-bottom: 1.2rem;
        }

        /* ── Accordion ── */
        .accordion-item { border-radius: 14px !important; overflow: hidden; border: 1.5px solid #f1f5f9 !important; }
        .accordion-button { font-weight: 700; }
        .accordion-button:not(.collapsed) { color: var(--indigo); background: rgba(99,102,241,0.05); box-shadow: none; }
        .accordion-button:focus { box-shadow: none; }

        /* ── CTA ── */
        .contact-cta {
            background: linear-gradient(135deg, #0f0c29, #302b63);
            border-radius: 32px;
            margin: 0 1rem 4rem;
        }

        /* Success alert */
        .alert-success-custom {
            background: rgba(16,185,129,0.1);
            border: 1.5px solid rgba(16,185,129,0.3);
            border-radius: 14px;
            color: #065f46;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand" style="width: 150px" href="index.php">
                <img src="assets/images/SWC%20Secondary%20Logo%20Light.png" alt="SwiftCapital Logo" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="services.php" id="servicesDropdown" role="button" data-bs-toggle="dropdown">Services</a>
                        <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                            <li><a class="dropdown-item" href="services.php#service-1">Personal Banking</a></li>
                            <li><a class="dropdown-item" href="services.php#service-2">Business Banking</a></li>
                            <li><a class="dropdown-item" href="services.php#service-3">Corporate Banking</a></li>
                            <li><a class="dropdown-item" href="services.php#service-4">Loans &amp; Mortgages</a></li>
                            <li><a class="dropdown-item" href="services.php#service-5">Investments</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="careers.php">Careers</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
                </ul>
                <div class="d-flex ms-lg-4">
                    <a href="login.php" class="btn btn-outline-primary me-2">Log In</a>
                    <a href="register.php" class="btn btn-primary">Open Account</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ── Hero ── -->
    <section class="contact-hero">
        <div class="orb" style="top:5%;left:3%;width:350px;height:350px;background:radial-gradient(circle,rgba(99,102,241,.35) 0%,transparent 70%);"></div>
        <div class="orb" style="bottom:0;right:5%;width:280px;height:280px;background:radial-gradient(circle,rgba(139,92,246,.2) 0%,transparent 70%);"></div>
        <div class="container py-5 position-relative" style="z-index:2;">
            <div class="row align-items-center">
                <div class="col-lg-6 text-white mb-5 mb-lg-0" data-aos="fade-right">
                    <span class="badge rounded-pill px-4 py-2 fw-800 text-xs mb-4 text-uppercase" style="background:rgba(99,102,241,.2);color:#a5b4fc;">24/7 Concierge Support</span>
                    <h1 class="fw-800 display-3 text-white mb-4">We're Here<br><span style="background:linear-gradient(90deg,#818cf8,#c084fc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">to Help You</span></h1>
                    <p class="lead text-white-50 mb-5">Whether you have a question, need guidance, or want to speak with a dedicated financial advisor — our team is always just a message away.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#contact-form" class="btn btn-primary btn-lg px-5 py-3 fw-800" style="border-radius:12px;">Send a Message</a>
                        <a href="mailto:<?php echo getSetting('contact_email','support@trustsglobal.com'); ?>" class="btn btn-outline-light btn-lg px-4 py-3 fw-800" style="border-radius:12px;">
                            <i class="fa-solid fa-envelope me-2"></i>Email Us
                        </a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="rounded-4 p-4 text-white" style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);">
                                <i class="fa-solid fa-envelope fa-2x mb-3" style="color:#a5b4fc;"></i>
                                <h6 class="fw-800 mb-1">Email Support</h6>
                                <p class="text-white-50 text-xs mb-0"><?php echo getSetting('contact_email','support@trustsglobal.com'); ?></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rounded-4 p-4 text-white" style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);">
                                <i class="fa-solid fa-clock fa-2x mb-3" style="color:#86efac;"></i>
                                <h6 class="fw-800 mb-1">Available 24/7</h6>
                                <p class="text-white-50 text-xs mb-0">Round-the-clock support for all clients</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rounded-4 p-4 text-white" style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);">
                                <i class="fa-solid fa-location-dot fa-2x mb-3" style="color:#fca5a5;"></i>
                                <h6 class="fw-800 mb-1">Headquarters</h6>
                                <p class="text-white-50 text-xs mb-0"><?php echo getSetting('contact_address','301 East Water St, VA 22904'); ?></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rounded-4 p-4 text-white" style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);">
                                <i class="fa-solid fa-shield-halved fa-2x mb-3" style="color:#fde68a;"></i>
                                <h6 class="fw-800 mb-1">Secure & Private</h6>
                                <p class="text-white-50 text-xs mb-0">Bank-grade encryption on all communications</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Contact Info Cards ── -->
    <section class="py-5" data-aos="fade-up">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="info-card h-100">
                        <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                        <h5 class="fw-800 mb-2">Email Us</h5>
                        <p class="text-muted text-sm mb-3">For general inquiries and account support.</p>
                        <a href="mailto:<?php echo getSetting('contact_email','support@trustsglobal.com'); ?>" class="fw-700 text-decoration-none" style="color:var(--indigo);">
                            <?php echo getSetting('contact_email','support@trustsglobal.com'); ?>
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card h-100">
                        <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <h5 class="fw-800 mb-2">Visit Us</h5>
                        <p class="text-muted text-sm mb-3">Our global headquarters is open Mon – Fri, 9 AM – 5 PM.</p>
                        <span class="fw-700" style="color:var(--indigo);"><?php echo getSetting('contact_address','301 East Water Street, Charlottesville, VA 22904'); ?></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card h-100">
                        <div class="info-icon"><i class="fa-solid fa-comments"></i></div>
                        <h5 class="fw-800 mb-2">Live Chat</h5>
                        <p class="text-muted text-sm mb-3">Chat with a member of our support team in real-time, anytime.</p>
                        <a href="#" class="fw-700 text-decoration-none" style="color:var(--indigo);">Start a Conversation →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Contact Form ── -->
    <section class="py-5 bg-light" id="contact-form" data-aos="fade-up">
        <div class="container">
            <div class="row g-5 align-items-start">
                <div class="col-lg-7">
                    <div class="contact-form-wrap">
                        <h2 class="fw-800 mb-1">Send Us a Message</h2>
                        <p class="text-muted mb-4">Fill out the form and our team will get back to you within 24 hours.</p>

                        <?php if ($sent): ?>
                        <div class="alert-success-custom p-4 mb-4">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            <strong>Message sent!</strong> We'll respond within 24 business hours.
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="#contact-form">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="first_name" placeholder="John" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="last_name" placeholder="Smith" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" placeholder="john@example.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phone" placeholder="+1 (000) 000-0000">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Subject</label>
                                    <select class="form-select" name="subject" required>
                                        <option value="" disabled selected>Select a topic</option>
                                        <option value="general">General Inquiry</option>
                                        <option value="account">Account Support</option>
                                        <option value="loan">Loan Information</option>
                                        <option value="investment">Investment Advisory</option>
                                        <option value="feedback">Feedback</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Message</label>
                                    <textarea class="form-control" name="message" rows="5" placeholder="Tell us how we can help you..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="privacy" required>
                                        <label class="form-check-label text-muted text-sm" for="privacy">
                                            I agree to the <a href="#" style="color:var(--indigo);">Privacy Policy</a> and consent to the processing of my personal data.
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 pt-2">
                                    <button type="submit" class="btn-submit">
                                        <i class="fa-solid fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar info -->
                <div class="col-lg-5" data-aos="fade-left">
                    <h4 class="fw-800 mb-4">Why Contact Us?</h4>
                    <div class="d-flex flex-column gap-3">
                        <?php
                        $reasons = [
                            ['fa-shield-halved','Account Security','Report suspicious activity or request account verification.'],
                            ['fa-hand-holding-dollar','Loan & Credit','Get personalised rate quotes and eligibility checks.'],
                            ['fa-chart-line','Investment Advisory','Speak to a portfolio manager about wealth management.'],
                            ['fa-headset','24/7 Concierge','Dedicated relationship managers for premium clients.'],
                        ];
                        foreach($reasons as $r): ?>
                        <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-4 border" style="border-color:#f1f5f9 !important;">
                            <div class="info-icon flex-shrink-0" style="margin-bottom:0;">
                                <i class="fa-solid <?php echo $r[0]; ?>"></i>
                            </div>
                            <div>
                                <h6 class="fw-800 mb-1"><?php echo $r[1]; ?></h6>
                                <p class="text-muted text-sm mb-0"><?php echo $r[2]; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Support Options ── -->
    <section class="py-5" data-aos="fade-up">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-800">Multiple Ways to Reach Us</h2>
                <p class="text-muted">Choose the channel that works best for you.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="support-card text-center">
                        <div class="support-icon-wrap mx-auto"><i class="fa-solid fa-comments"></i></div>
                        <h5 class="fw-800 mb-2">Live Chat</h5>
                        <p class="text-muted text-sm mb-4">Chat with our representatives in real-time for immediate assistance.</p>
                        <a href="#" class="btn btn-outline-primary fw-700 px-5" style="border-radius:50px;">Start Chat</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="support-card text-center">
                        <div class="support-icon-wrap mx-auto"><i class="fa-solid fa-phone"></i></div>
                        <h5 class="fw-800 mb-2">Phone Support</h5>
                        <p class="text-muted text-sm mb-4">Call our 24/7 customer service line for banking assistance.</p>
                        <a href="tel:18008765432" class="btn btn-outline-primary fw-700 px-5" style="border-radius:50px;">Call Now</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="support-card text-center">
                        <div class="support-icon-wrap mx-auto"><i class="fa-solid fa-calendar-check"></i></div>
                        <h5 class="fw-800 mb-2">Book Appointment</h5>
                        <p class="text-muted text-sm mb-4">Schedule a session with a dedicated financial advisor at your convenience.</p>
                        <a href="#" class="btn btn-outline-primary fw-700 px-5" style="border-radius:50px;">Book Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── FAQ ── -->
    <section class="py-5 bg-light" data-aos="fade-up">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-800">Frequently Asked Questions</h2>
                <p class="text-muted">Quick answers to common support questions.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion d-flex flex-column gap-3" id="faqAccordion">
                        <?php
                        $faqs = [
                            ['headingOne','collapseOne','What are your customer service hours?','Our customer service phone line is available 24/7 for urgent matters. For general inquiries, representatives are available Monday – Friday 8 AM to 8 PM, and Saturday 9 AM to 5 PM. Live chat is available 24/7.', true],
                            ['headingTwo','collapseTwo','How quickly will I receive an email response?','We respond to all email inquiries within 24 business hours. For urgent matters, use our live chat or phone support for immediate help.', false],
                            ['headingThree','collapseThree','How do I report a lost or stolen card?','Call our 24/7 Card Support line immediately, or report it through the mobile app under "Card Services". Your card will be frozen instantly.', false],
                            ['headingFour','collapseFour','How do I schedule an appointment with a financial advisor?','Click "Book Now" in the Support Options section above, use our mobile app, or call your local branch. Virtual appointments are also available.', false],
                        ];
                        foreach($faqs as $f): ?>
                        <div class="accordion-item border shadow-sm">
                            <h2 class="accordion-header" id="<?php echo $f[0]; ?>">
                                <button class="accordion-button <?php echo $f[4] ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $f[1]; ?>" aria-expanded="<?php echo $f[4] ? 'true' : 'false'; ?>">
                                    <?php echo $f[2]; ?>
                                </button>
                            </h2>
                            <div id="<?php echo $f[1]; ?>" class="accordion-collapse collapse <?php echo $f[4] ? 'show' : ''; ?>" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted"><?php echo $f[3]; ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── CTA ── -->
    <section class="py-5" data-aos="fade-up">
        <div class="container">
            <div class="contact-cta p-5 text-center text-white position-relative overflow-hidden">
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:100%;height:100%;background:radial-gradient(circle,rgba(99,102,241,.15) 0%,transparent 65%);pointer-events:none;"></div>
                <h2 class="fw-800 display-5 mb-3 position-relative">Ready to Get Started?</h2>
                <p class="lead text-white-50 mb-5 position-relative">Join thousands of clients who trust SwiftCapital with their financial future.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3 position-relative">
                    <a href="register.php" class="btn btn-primary btn-lg px-5 py-3 fw-800" style="border-radius:12px;">Open an Account</a>
                    <a href="#contact-form" class="btn btn-outline-light btn-lg px-5 py-3 fw-800" style="border-radius:12px;">Contact Us</a>
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
                    <p class="text-muted">Providing innovative banking solutions since 1995. Our mission is to empower our customers to achieve their financial goals.</p>
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
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="careers.php">Careers</a></li>
                        <li><a href="#">Press</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
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
                        <li><a href="mailto:<?php echo getSetting('contact_email','support@trustsglobal.com'); ?>"><?php echo getSetting('contact_email','support@trustsglobal.com'); ?></a></li>
                        <li><a href="#"><?php echo getSetting('contact_address','301 East Water Street, Charlottesville, VA 22904'); ?></a></li>
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
    <script>
        AOS.init({ duration: 900, once: true, offset: 120 });
    </script>
</body>
</html>
