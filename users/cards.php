<?php
require_once '../includes/db.php';
require_once '../includes/user-check.php';

$user_id = $_SESSION['user_id'];

// Stats
$active_cards  = $pdo->prepare("SELECT COUNT(*) FROM card_applications WHERE user_id = ? AND status = 'Approved'");
$active_cards->execute([$user_id]);
$active_count = $active_cards->fetchColumn();

$pending_cards = $pdo->prepare("SELECT COUNT(*) FROM card_applications WHERE user_id = ? AND status = 'Pending'");
$pending_cards->execute([$user_id]);
$pending_count = $pending_cards->fetchColumn();

// All user cards
$stmt_cards = $pdo->prepare("SELECT * FROM card_applications WHERE user_id = ? ORDER BY created_at DESC");
$stmt_cards->execute([$user_id]);
$my_cards = $stmt_cards->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Cards - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        /* Modern Stats Premium */
        .stat-card-premium {
            background: #fff;
            border: 1px solid #edf2f7;
            border-radius: 20px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02);
            transition: all 0.3s;
        }

        .stat-card-premium:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        }

        .stat-icon-box {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-info h6 {
            font-size: 0.85rem;
            font-weight: 700;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .stat-info h4 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 0;
        }

        /* Premium Promo Banner */
        .premium-promo-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            border-radius: 24px;
            padding: 60px;
            color: #fff;
            display: flex;
            position: relative;
            overflow: hidden;
            margin-bottom: 40px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .premium-promo-banner::before {
            content: '';
            position: absolute;
            top: -150px;
            right: -150px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .promo-content-premium {
            flex: 1;
            position: relative;
            z-index: 2;
        }

        .promo-content-premium h2 {
            font-weight: 800;
            font-size: 2.25rem;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .promo-content-premium p {
            font-size: 1.1rem;
            opacity: 0.8;
            margin-bottom: 40px;
            max-width: 600px;
            line-height: 1.7;
        }

        .promo-grid-premium {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 45px;
            max-width: 700px;
        }

        .promo-item-premium {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .promo-item-premium i {
            font-size: 1.25rem;
            color: #3b82f6;
            margin-top: 3px;
        }

        .promo-item-premium h6 {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .promo-item-premium p {
            font-size: 0.85rem;
            margin-bottom: 0;
            line-height: 1.5;
        }

        .btn-apply-premium {
            background: #fff;
            color: #1e3a8a;
            border: none;
            padding: 16px 35px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 1rem;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-apply-premium:hover {
            transform: translateY(-3px);
            background: #f8fafc;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        /* Enhanced CSS Card */
        .premium-card-visual {
            width: 340px;
            height: 210px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 25px 55px rgba(0, 0, 0, 0.4);
            color: #fff;
            position: relative;
            overflow: hidden;
            transform: rotate(-3deg) perspective(1000px) rotateY(-10deg);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .premium-card-visual::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("https://www.transparenttextures.com/patterns/carbon-fibre.png");
            opacity: 0.1;
        }

        .card-glass-shine {
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(120deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: shine 4s infinite;
        }

        @keyframes shine {
            0% { left: -100%; }
            20% { left: 100%; }
            100% { left: 100%; }
        }

        .card-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            position: relative;
        }

        .card-logo-text {
            font-weight: 800;
            font-size: 1rem;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.9);
        }

        .card-chip-premium {
            width: 45px;
            height: 35px;
            background: linear-gradient(135deg, #ffd700 0%, #b8860b 100%);
            border-radius: 8px;
            position: relative;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .card-number-premium {
            font-size: 1.5rem;
            letter-spacing: 4px;
            font-family: 'OCR A Extended', monospace;
            margin-bottom: 25px;
            position: relative;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .card-footer-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            position: relative;
        }

        .card-info-box .label {
            font-size: 0.55rem;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .card-info-box .value {
            font-size: 0.95rem;
            font-weight: 600;
        }

        .card-brand-icon {
            font-size: 2.5rem;
            opacity: 0.9;
        }

        /* How it works Premium */
        .step-card-premium {
            background: #fff;
            border: 1px solid #edf2f7;
            border-radius: 20px;
            padding: 35px;
            height: 100%;
            transition: all 0.3s;
        }

        .step-card-premium:hover {
            border-color: #3b82f6;
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.05);
        }

        .step-number-box {
            width: 55px;
            height: 55px;
            background: #eff6ff;
            color: #3b82f6;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 25px;
        }

        .step-card-premium h5 {
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 15px;
        }

        .step-card-premium p {
            color: #718096;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 0;
        }

        /* FAQ Premium */
        .faq-card-premium {
            background: #fff;
            border: 1px solid #edf2f7;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
        }

        .faq-item-premium {
            padding: 25px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .faq-item-premium:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .faq-q-premium {
            font-weight: 800;
            font-size: 1.1rem;
            color: #1a202c;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .faq-q-premium::before {
            content: 'Q';
            color: #3b82f6;
            font-weight: 900;
        }

        .faq-a-premium {
            color: #718096;
            font-size: 1rem;
            line-height: 1.7;
            padding-left: 28px;
        }

        .empty-state-premium {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon-premium {
            font-size: 4rem;
            color: #e2e8f0;
            margin-bottom: 25px;
        }

        .btn-primary-premium {
            background: var(--primary-gradient);
            color: #fff;
            border: none;
            padding: 14px 30px;
            border-radius: 12px;
            font-weight: 700;
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2);
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
            
            <div class="page-header-centered">
                <div class="header-icon-circle">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <h1 class="page-title-centered">Virtual Card Management</h1>
                <p class="page-subtitle-centered">Instant, secure, and Capitally-accepted digital cards for all your online financial needs.</p>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="stat-card-premium">
                        <div class="stat-icon-box" style="background: #eff6ff; color: #3b82f6;"><i class="fa-solid fa-credit-card"></i></div>
                        <div class="stat-info">
                            <h6>Active Cards</h6>
                            <h4><?php echo $active_count; ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card-premium">
                        <div class="stat-icon-box" style="background: #fff7ed; color: #f97316;"><i class="fa-solid fa-hourglass-clock"></i></div>
                        <div class="stat-info">
                            <h6>Pending Approval</h6>
                            <h4><?php echo $pending_count; ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card-premium">
                        <div class="stat-icon-box" style="background: #f0fdf4; color: #10b981;"><i class="fa-solid fa-layer-group"></i></div>
                        <div class="stat-info">
                            <h6>Total Applications</h6>
                            <h4><?php echo count($my_cards); ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Premium Promo Banner -->
            <div class="premium-promo-banner shadow-lg">
                <div class="promo-content-premium">
                    <h2>Next-Gen Virtual Cards</h2>
                    <p>SwiftCapital virtual cards provide an extra layer of privacy and control, allowing you to pay online without revealing your primary account details.</p>
                    
                    <div class="promo-grid-premium">
                        <div class="promo-item-premium">
                            <i class="fa-solid fa-shield-check"></i>
                            <div>
                                <h6>Enhanced Isolation</h6>
                                <p>Separate your primary funds from online merchants</p>
                            </div>
                        </div>
                        <div class="promo-item-premium">
                            <i class="fa-solid fa-globe-americas"></i>
                            <div>
                                <h6>Universal Coverage</h6>
                                <p>Acceptance across 200+ countries and millions of sites</p>
                            </div>
                        </div>
                        <div class="promo-item-premium">
                            <i class="fa-solid fa-chart-radar"></i>
                            <div>
                                <h6>Granular Limits</h6>
                                <p>Set daily, monthly, or per-transaction spending caps</p>
                            </div>
                        </div>
                        <div class="promo-item-premium">
                            <i class="fa-solid fa-sparkles"></i>
                            <div>
                                <h6>On-Demand Issuance</h6>
                                <p>Generate new cards instantly whenever needed</p>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn btn-apply-premium" onclick="location.href='card-apply.php'">
                        Apply for New Card <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </div>
                
                <div class="promo-visual d-none d-xl-flex align-items-center justify-content-center" style="width: 400px;">
                    <div class="premium-card-visual">
                        <div class="card-glass-shine"></div>
                        <div class="card-header-flex">
                            <div class="card-logo-text">Swift Capital</div>
                            <div class="card-chip-premium"></div>
                        </div>
                        <div class="card-number-premium">4412 8821 5562 1342</div>
                        <div class="card-footer-flex">
                            <div class="d-flex gap-4">
                                <div class="card-info-box">
                                    <div class="label">Valid Thru</div>
                                    <div class="value">12/28</div>
                                </div>
                                <div class="card-info-box">
                                    <div class="label">CVV</div>
                                    <div class="value">***</div>
                                </div>
                            </div>
                            <div class="card-brand-icon"><i class="fa-brands fa-cc-visa"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-5" style="border-radius: 24px;">
                <div class="card-body p-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-800 m-0">My Cards</h4>
                        <a href="card-apply.php" class="btn btn-primary-soft btn-sm">
                            <i class="fa-solid fa-plus-circle me-1"></i> New Application
                        </a>
                    </div>

                    <?php if (empty($my_cards)): ?>
                    <div class="empty-state-premium">
                        <div class="empty-icon-premium"><i class="fa-solid fa-credit-card"></i></div>
                        <h5 class="fw-800">No Cards Yet</h5>
                        <p class="text-muted mb-4 mx-auto" style="max-width: 400px;">You haven't applied for a virtual card yet. Start your application to access secure digital payments.</p>
                        <a href="card-apply.php" class="btn btn-primary-premium px-5">Apply for Your First Card</a>
                    </div>
                    <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($my_cards as $card):
                            $netIcons = ['visa'=>'fa-cc-visa visa-c','mastercard'=>'fa-cc-mastercard mc-c','amex'=>'fa-cc-amex amex-c'];
                            $netIcon  = $netIcons[$card['card_type']] ?? 'fa-credit-card';
                            $is_approved = $card['status'] === 'Approved';
                            $card_num = $is_approved && $card['card_number'] ? $card['card_number'] : '•••• •••• •••• ????';
                            $gradients = ['standard'=>'135deg,#3b82f6,#2563eb','gold'=>'135deg,#d97706,#92400e','platinum'=>'135deg,#475569,#1e293b','black'=>'135deg,#1e293b,#000'];
                            $grad = $gradients[$card['card_tier']] ?? $gradients['standard'];
                        ?>
                        <div class="col-md-6">
                            <!-- Card Visual -->
                            <div style="background:linear-gradient(<?php echo $grad; ?>);border-radius:20px;padding:26px;color:#fff;box-shadow:0 20px 45px rgba(0,0,0,.25);position:relative;overflow:hidden;margin-bottom:14px;">
                                <div style="position:absolute;top:-50px;right:-50px;width:150px;height:150px;background:radial-gradient(circle,rgba(255,255,255,.12) 0%,transparent 70%);border-radius:50%;"></div>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <span style="font-weight:800;letter-spacing:1px;font-size:.9rem;">Swift Capital</span>
                                    <div style="width:40px;height:30px;background:linear-gradient(135deg,#ffd700,#b8860b);border-radius:6px;"></div>
                                </div>
                                <div style="font-size:1.2rem;letter-spacing:4px;font-weight:700;margin-bottom:20px;"><?php echo htmlspecialchars($card_num); ?></div>
                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <div style="font-size:.5rem;text-transform:uppercase;opacity:.6;letter-spacing:1px;margin-bottom:4px;">Card Holder</div>
                                        <div style="font-size:.85rem;font-weight:600;"><?php echo htmlspecialchars(strtoupper($card['cardholder_name'])); ?></div>
                                    </div>
                                    <div>
                                        <?php if ($is_approved && $card['expiry_date']): ?>
                                        <div style="font-size:.5rem;text-transform:uppercase;opacity:.6;letter-spacing:1px;margin-bottom:4px;">Expires</div>
                                        <div style="font-size:.85rem;font-weight:600;"><?php echo $card['expiry_date']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <i class="fa-brands <?php echo $netIcon; ?>" style="font-size:2.2rem;"></i>
                                </div>
                            </div>
                            <!-- Card Detail Row -->
                            <div class="d-flex justify-content-between align-items-center px-1">
                                <div>
                                    <div class="fw-800" style="font-size:.85rem;"><?php echo ucfirst($card['card_type']); ?> <?php echo ucfirst($card['card_tier']); ?></div>
                                    <div class="text-muted" style="font-size:.75rem;"><?php echo $card['currency']; ?> · $<?php echo number_format($card['daily_limit'],0); ?>/day</div>
                                </div>
                                <?php
                                $sc = ['Pending'=>'status-pending','Approved'=>'status-active','Rejected'=>'status-blocked','Cancelled'=>'status-blocked'];
                                ?>
                                <span class="status-badge <?php echo $sc[$card['status']] ?? 'status-pending'; ?>"><?php echo $card['status']; ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <h5 class="fw-800 mb-4">Streamlined Activation Process</h5>
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="step-card-premium">
                        <div class="step-number-box">01</div>
                        <h5>Apply Online</h5>
                        <p>Fill out the application form with your preferred currency and initial spending threshold.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card-premium">
                        <div class="step-number-box">02</div>
                        <h5>Instant Validation</h5>
                        <p>Our automated systems verify your account status and provision your digital card in minutes.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card-premium">
                        <div class="step-number-box">03</div>
                        <h5>Start Spending</h5>
                        <p>Access your card credentials and begin making secure Capital transactions immediately.</p>
                    </div>
                </div>
            </div>

            <div class="faq-card-premium shadow-sm">
                <h4 class="fw-800 mb-4">Support & FAQ</h4>
                
                <div class="faq-item-premium">
                    <div class="faq-q-premium">What makes SwiftCapital virtual cards unique?</div>
                    <div class="faq-a-premium">Unlike standard bank cards, our virtual cards are detached from your main banking ledger, providing a fortified barrier against merchant breaches and unauthorized recurring charges.</div>
                </div>
                <div class="faq-item-premium">
                    <div class="faq-q-premium">Are there specific merchant restrictions?</div>
                    <div class="faq-a-premium">SwiftCapital cards are compatible with any online vendor that displays the Visa or Mastercard logo. This includes major streaming services, e-commerce platforms, and digital subscriptions.</div>
                </div>
                <div class="faq-item-premium">
                    <div class="faq-q-premium">How do I manage my card limits?</div>
                    <div class="faq-a-premium">You can adjust your daily and weekly spending limits in real-time through your card management dashboard, giving you total control over your digital footprint.</div>
                </div>
                <div class="faq-item-premium">
                    <div class="faq-q-premium">What happens if a card is compromised?</div>
                    <div class="faq-a-premium">You can freeze or permanently terminate a virtual card with a single click. Our 24/7 security protocol ensures that any suspicious activity is blocked before it reaches your balance.</div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="main-footer mt-auto">
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
>
