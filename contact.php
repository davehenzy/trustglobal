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
    <section class="py-5 bg-dark text-white text-center">
        <div class="container py-5">
            <h1 class="fw-800 display-4">Contact Us</h1>
            <p class="lead text-white-50">We are here to help you 24/7. Get in touch with our team.</p>
        </div>
    </section>
    <!-- Contact Form Section -->
    <section class="py-5" data-aos="fade-up">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 mb-5 mb-lg-0">
                    <div class="contact-form">
                        <h2 class="fw-bold mb-4">Send Us a Message</h2>
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" required="">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" required="">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" required="">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone">
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <select class="form-select" id="subject" required="">
                                    <option value="" selected="" disabled="">Select a subject</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="account">Account Support</option>
                                    <option value="loan">Loan Information</option>
                                    <option value="feedback">Feedback</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" rows="5" required=""></textarea>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="privacyPolicy" required="">
                                <label class="form-check-label" for="privacyPolicy">I agree to the <a href="#">Privacy Policy</a> and consent to the processing of my personal data.</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Message</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="row">
                        
                        <div class="col-md-12 mb-4">
                            <div class="contact-info-card">
                                <div class="contact-icon">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <h4 class="fw-bold mb-3">Email Us</h4>
                                <p class="mb-2">General Inquiries: <a href="mailto:support@trustsglobal.com" class="text-primary">support@trustsglobal.com</a></p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="contact-info-card">
                                <div class="contact-icon">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <h4 class="fw-bold mb-3">Headquarters</h4>
                                <p class="mb-2">301 East Water Street, Charlottesville, VA 22904 Virginia</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Branch Locator -->
    

    <!-- Customer Support Options -->
    <section class="py-5 bg-light" data-aos="fade-up">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="fw-bold mb-3">Customer Support Options</h2>
                    <p class="text-muted">We offer multiple ways to get the help you need, when you need it.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="support-option">
                        <div class="support-icon">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Live Chat</h4>
                        <p class="text-muted mb-4">Chat with our customer service representatives in real-time for immediate assistance.</p>
                        <a href="#" class="btn btn-outline-primary">Start Chat</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="support-option">
                        <div class="support-icon">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Phone Support</h4>
                        <p class="text-muted mb-4">Call our 24/7 customer service line for assistance with your banking needs.</p>
                        <a href="tel:18008765432" class="btn btn-outline-primary">Call Now</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="support-option">
                        <div class="support-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Schedule Appointment</h4>
                        <p class="text-muted mb-4">Book an appointment with a financial advisor at your preferred branch.</p>
                        <a href="#" class="btn btn-outline-primary">Book Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5" data-aos="fade-up">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="fw-bold mb-3">Frequently Asked Questions</h2>
                    <p class="text-muted">Find quick answers to common questions about contacting us.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item mb-3 border-0 rounded shadow-sm">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    What are your customer service hours?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Our customer service phone line is available 24/7 for urgent matters. For general inquiries, our representatives are available Monday through Friday from 8 AM to 8 PM, and Saturday from 9 AM to 5 PM (local time). Our online chat support is available 24/7.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item mb-3 border-0 rounded shadow-sm">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    How quickly will I receive a response to my email inquiry?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We strive to respond to all email inquiries within 24 hours during business days. For urgent matters, we recommend calling our customer service line or using the live chat feature for immediate assistance.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item mb-3 border-0 rounded shadow-sm">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    How do I report a lost or stolen card?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    If your card is lost or stolen, please call our 24/7 Card Support line immediately. You can also report a lost or stolen card through our mobile app or online banking portal under the "Card Services" section.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 rounded shadow-sm">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    How do I schedule an appointment with a financial advisor?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    You can schedule an appointment with a financial advisor through our website by clicking on the "Book Now" button in the Customer Support Options section, through our mobile app, or by calling your local branch directly. Virtual appointments are also available for your convenience.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="cta" data-aos="fade-up">
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
                        
                        <li><a href="javascript::">support@trustsglobal.com</a></li>
                        <li><a href="javascript::">301 East Water Street, Charlottesville, VA 22904 Virginia</a></li>
                        
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
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>
</body>

</html>
