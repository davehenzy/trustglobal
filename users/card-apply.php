<?php require_once '../includes/user-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Virtual Card - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        /* Premium Card Selector */
        .premium-type-group {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .premium-type-card {
            background: #fff;
            border: 2px solid #edf2f7;
            border-radius: 18px;
            padding: 24px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .premium-type-card:hover {
            border-color: #3b82f6;
            background: #f8fafc;
        }

        .premium-type-card input[type="radio"] {
            display: none;
        }

        .premium-type-card input[type="radio"]:checked + .custom-radio-mark {
            border-color: #3b82f6;
            background: #3b82f6;
            box-shadow: inset 0 0 0 4px #fff;
        }

        .custom-radio-mark {
            width: 24px;
            height: 24px;
            border: 2px solid #cbd5e0;
            border-radius: 50%;
            margin-right: 20px;
            flex-shrink: 0;
            transition: all 0.2s;
        }

        .type-content-flex {
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .type-meta h6 {
            font-weight: 800;
            font-size: 1.1rem;
            color: #1a202c;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .type-meta p {
            font-size: 0.9rem;
            color: #718096;
            margin-bottom: 0;
        }

        .brand-icon-heavy {
            font-size: 2rem;
            opacity: 0.8;
        }

        .visa-brand { color: #1a1f71; }
        .master-brand { color: #eb001b; }
        .amex-brand { color: #007bc1; }

        /* Premium Form Elements */
        .form-section-premium {
            background: #fff;
            border-radius: 24px;
            padding: 50px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.04);
            border: 1px solid #edf2f7;
        }

        .form-label-premium {
            font-weight: 800;
            font-size: 0.95rem;
            color: #2d3748;
            margin-bottom: 12px;
            display: block;
        }

        .input-premium-styled {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 14px;
            padding: 15px 20px;
            font-size: 1rem;
            font-weight: 600;
            color: #1a202c;
            transition: all 0.2s;
        }

        .input-premium-styled:focus {
            background: #fff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .select-premium-styled {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23a0aec0'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='C19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 20px center;
            background-size: 18px;
        }

        .input-group-premium {
            position: relative;
        }

        .input-group-premium .prefix {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-weight: 800;
            color: #4a5568;
        }

        .input-group-premium .input-premium-styled {
            padding-left: 45px;
        }

        /* Fee Summary Box */
        .fee-glance-box {
            background: #f0f9ff;
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 35px;
            border: 1px solid #bae6fd;
        }

        .fee-glance-header {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            color: #0369a1;
            margin-bottom: 15px;
        }

        .fee-item-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(186, 230, 253, 0.5);
            font-size: 0.95rem;
            color: #0c4a6e;
        }

        .fee-item-row:last-child {
            border-bottom: none;
        }

        .fee-item-row b {
            font-weight: 800;
        }

        .btn-action-premium {
            width: 100%;
            background: var(--primary-gradient);
            color: #fff;
            border: none;
            padding: 20px;
            border-radius: 16px;
            font-weight: 800;
            font-size: 1.1rem;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .btn-action-premium:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.4);
        }

        .back-link-premium {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #718096;
            font-weight: 700;
            text-decoration: none;
            margin-bottom: 25px;
            transition: color 0.2s;
        }

        .back-link-premium:hover {
            color: #3b82f6;
        }

        /* Hero Header */
        .hero-banner-premium {
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            border-radius: 24px;
            padding: 50px 60px;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: -30px;
            z-index: 2;
        }

        .hero-banner-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at top right, rgba(59, 130, 246, 0.2), transparent 60%);
        }

        .hero-banner-premium h2 {
            font-weight: 900;
            margin-bottom: 10px;
            letter-spacing: -1px;
        }

        .hero-banner-premium p {
            opacity: 0.8;
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .hero-card-icon {
            position: absolute;
            right: 50px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 5rem;
            opacity: 0.1;
        }
    </style>
</head>
<body>

<?php 
$page = 'cards';
include '../includes/user-sidebar.php'; 
?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <?php include '../includes/user-navbar.php'; ?>

        <!-- Page Content -->
        <div class="page-container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    
                    <a href="cards.php" class="back-link-premium">
                        <i class="fa-solid fa-chevron-left"></i> Return to Card Management
                    </a>

                    <div class="hero-banner-premium shadow-lg">
                        <div class="position-relative z-2">
                            <h2>New Card Issuance</h2>
                            <p>Provisioning advanced digital assets for secure cross-border commerce.</p>
                        </div>
                        <i class="fa-solid fa-credit-card-front hero-card-icon"></i>
                    </div>

                    <div class="form-section-premium shadow-sm">
                        <form action="cards.php">
                            
                            <div class="mb-5">
                                <h4 class="fw-900 mb-4" style="color: #1a202c; letter-spacing: -0.5px;">Asset Configuration</h4>
                                
                                <div class="mb-5">
                                    <label class="form-label-premium">Select Card Network Provider</label>
                                    <div class="premium-type-group">
                                        <label class="premium-type-card">
                                            <input type="radio" name="card_type" value="visa" checked>
                                            <div class="custom-radio-mark"></div>
                                            <div class="type-content-flex">
                                                <div class="type-meta">
                                                    <h6>Visa Infinite Virtual</h6>
                                                    <p>Optimal for universal subscription management and retail</p>
                                                </div>
                                                <i class="fa-brands fa-cc-visa brand-icon-heavy visa-brand"></i>
                                            </div>
                                        </label>
                                        <label class="premium-type-card">
                                            <input type="radio" name="card_type" value="mastercard">
                                            <div class="custom-radio-mark"></div>
                                            <div class="type-content-flex">
                                                <div class="type-meta">
                                                    <h6>Mastercard World Elite</h6>
                                                    <p>High-tier security for commercial and corporate expenditure</p>
                                                </div>
                                                <i class="fa-brands fa-cc-mastercard brand-icon-heavy master-brand"></i>
                                            </div>
                                        </label>
                                        <label class="premium-type-card">
                                            <input type="radio" name="card_type" value="amex">
                                            <div class="custom-radio-mark"></div>
                                            <div class="type-content-flex">
                                                <div class="type-meta">
                                                    <h6>American Express Digital</h6>
                                                    <p>Proprietary network with exclusive Capital rewards integration</p>
                                                </div>
                                                <i class="fa-brands fa-cc-amex brand-icon-heavy amex-brand"></i>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label-premium">Account Tier Selection</label>
                                        <select class="input-premium-styled select-premium-styled" required>
                                            <option value="standard" selected>Standard Core - $5.00 Fee</option>
                                            <option value="gold">Gold Elite - $15.00 Fee</option>
                                            <option value="platinum">Platinum Prestige - $25.00 Fee</option>
                                            <option value="black">Sovereign Black - $50.00 Fee</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-premium">Denominated Currency</label>
                                        <select class="input-premium-styled select-premium-styled">
                                            <option value="usd" selected>USD - United States Dollar</option>
                                            <option value="eur">EUR - European Euro</option>
                                            <option value="gbp">GBP - British Pound Sterling</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label-premium">Authorized Daily Spending Threshold</label>
                                    <div class="input-group-premium">
                                        <span class="prefix">$</span>
                                        <input type="number" class="input-premium-styled" placeholder="1000.00" min="1000" max="50000" step="100">
                                    </div>
                                    <div class="mt-3 d-flex justify-content-between align-items-center">
                                        <small class="text-muted fw-bold">Configurable Range: $1,000 - $50,000</small>
                                        <span class="badge bg-primary-soft text-primary px-3" style="border-radius: 8px;">Recommended: $5,000</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-5 border-top pt-5">
                                <h4 class="fw-900 mb-4" style="color: #1a202c; letter-spacing: -0.5px;">Verification & Billing</h4>
                                
                                <div class="mb-4">
                                    <label class="form-label-premium">Legal Cardholder Name</label>
                                    <input type="text" class="input-premium-styled" placeholder="Full name as registered on account">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label-premium">Associated Billing Address</label>
                                    <textarea class="input-premium-styled" rows="3" style="height: auto;" placeholder="Provide verified residential or business address"></textarea>
                                </div>
                            </div>

                            <div class="fee-glance-box">
                                <div class="fee-glance-header">
                                    <i class="fa-solid fa-receipt"></i> Issuance Summary
                                </div>
                                <div class="fee-item-row">
                                    <span>One-time Activation Fee</span>
                                    <b>$5.00 - $50.00</b>
                                </div>
                                <div class="fee-item-row">
                                    <span>Monthly Management Fee</span>
                                    <b>$0.00 (Standard)</b>
                                </div>
                                <div class="fee-item-row">
                                    <span>Transaction Interchange</span>
                                    <b>0%</b>
                                </div>
                                <p class="mt-3 mb-0 fs-7 text-muted" style="font-weight: 600;">Fees are deducted from your primary balance at the moment of card initialization.</p>
                            </div>

                            <div class="mb-5">
                                <div class="form-check custom-check-premium">
                                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                    <label class="form-check-label fs-7 fw-bold text-muted" for="termsCheck" style="cursor: pointer;">
                                        I formally authorize the issuance of this digital asset and agree to the <a href="#" class="text-primary text-decoration-none">Digital Banking Agreement</a> and <a href="#" class="text-primary text-decoration-none">Privacy Protocols</a>.
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn-action-premium">
                                <i class="fa-solid fa-sparkles"></i> Finalize Application & Issue Card
                            </button>
                        </form>
                    </div>

                    <div class="mt-5 mb-5 px-4">
                        <h5 class="fw-900 mb-4">Critical Information</h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="text-primary mt-1"><i class="fa-solid fa-circle-bolt"></i></div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Instant Activation</h6>
                                        <p class="text-muted fs-7 mb-0">Unlike physical cards, your SwiftCapital virtual credential is ready for use within 120 seconds of approval.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="text-primary mt-1"><i class="fa-solid fa-shield-halved"></i></div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Total Isolation</h6>
                                        <p class="text-muted fs-7 mb-0">Your primary account routing number remains invisible to all online merchants for enhanced asset protection.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <div class="brand">
                <span class="text-primary fw-bold" style="letter-spacing: -0.5px;">Swift</span><span class="text-dark fw-bold" style="letter-spacing: -0.5px;">Capital</span> © 2026 SwiftCapital. All rights reserved.
            </div>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Support</a>
            </div>
        </footer>
    </main>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateNodes = document.querySelectorAll('#currentDate');
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = now.toLocaleDateString('en-US', options);
            dateNodes.forEach(node => node.textContent = formattedDate);
        });
    </script>
</body>
</html>

