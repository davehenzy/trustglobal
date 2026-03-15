<?php
require_once '../includes/db.php';
require_once '../includes/user-check.php';

$user_id    = $_SESSION['user_id'];
$user_name  = trim(($_SESSION['name'] ?? '') . ' ' . ($_SESSION['lastname'] ?? ''));

// Redirect if already has a pending or active application
$existing = $pdo->prepare("SELECT * FROM card_applications WHERE user_id = ? AND status IN ('Pending','Approved') ORDER BY created_at DESC LIMIT 1");
$existing->execute([$user_id]);
$active_app = $existing->fetch();

$success_msg = '';
$error_msg   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$active_app) {
    $card_type      = in_array($_POST['card_type'] ?? '', ['visa','mastercard','amex']) ? $_POST['card_type'] : 'visa';
    $card_tier      = in_array($_POST['card_tier'] ?? '', ['standard','gold','platinum','black']) ? $_POST['card_tier'] : 'standard';
    $currency       = in_array($_POST['currency'] ?? '', ['USD','EUR','GBP']) ? $_POST['currency'] : 'USD';
    $daily_limit    = max(1000, min(50000, (float)($_POST['daily_limit'] ?? 5000)));
    $cardholder     = trim(htmlspecialchars($_POST['cardholder_name'] ?? $user_name));
    $billing_addr   = trim(htmlspecialchars($_POST['billing_address'] ?? ''));

    if (!$cardholder || !$billing_addr) {
        $error_msg = 'Please fill out all required fields.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO card_applications (user_id, card_type, card_tier, currency, daily_limit, cardholder_name, billing_address)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$user_id, $card_type, $card_tier, $currency, $daily_limit, $cardholder, $billing_addr]);
            $success_msg = 'Your card application has been submitted and is pending review.';
            // Refresh active app
            $existing->execute([$user_id]);
            $active_app = $existing->fetch();
        } catch (Exception $e) {
            $error_msg = 'An error occurred. Please try again.';
        }
    }
}

// Tier fee map
$tier_fees = ['standard' => 5, 'gold' => 15, 'platinum' => 25, 'black' => 50];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Virtual Card - SwiftCapital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* ── Hero ── */
        .apply-hero {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            border-radius: 28px;
            padding: 56px 60px;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: -40px;
            z-index: 2;
            box-shadow: 0 30px 70px rgba(0,0,0,.3);
        }
        .apply-hero .orb {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }
        .apply-hero h2 { font-weight: 900; letter-spacing: -1.5px; font-size: 2.5rem; }

        /* ── Form wrapper ── */
        .apply-form-wrap {
            background: #fff;
            border-radius: 28px;
            padding: 60px 54px 54px;
            box-shadow: 0 20px 60px rgba(0,0,0,.06);
            border: 1px solid #f1f5f9;
            position: relative;
            z-index: 3;
        }

        /* ── Card type selector ── */
        .card-type-grid { display: flex; flex-direction: column; gap: 14px; }
        .card-type-option { display: none; }
        .card-type-label {
            background: #f8fafc;
            border: 2px solid #e5e7eb;
            border-radius: 18px;
            padding: 22px 24px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all .25s;
            gap: 18px;
        }
        .card-type-label:hover { border-color: #6366f1; background: #f5f3ff; }
        .card-type-option:checked + .card-type-label {
            border-color: #6366f1;
            background: linear-gradient(135deg,#f5f3ff,#eef2ff);
            box-shadow: 0 0 0 4px rgba(99,102,241,.08);
        }
        .radio-dot {
            width: 22px; height: 22px;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            flex-shrink: 0;
            transition: all .2s;
            position: relative;
        }
        .card-type-option:checked + .card-type-label .radio-dot {
            border-color: #6366f1;
            background: #6366f1;
            box-shadow: inset 0 0 0 4px #fff;
        }
        .network-brand { font-size: 2.4rem; }
        .visa-c { color: #1a1f71; }
        .mc-c   { color: #eb001b; }
        .amex-c { color: #007bc1; }

        /* ── Tier cards ── */
        .tier-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .tier-option { display: none; }
        .tier-label {
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px 18px;
            cursor: pointer;
            transition: all .2s;
            display: block;
        }
        .tier-label:hover { border-color: #6366f1; }
        .tier-option:checked + .tier-label {
            border-color: #6366f1;
            background: #f5f3ff;
        }
        .tier-name { font-weight: 800; font-size: .95rem; }
        .tier-fee  { font-size: .8rem; color: #6b7280; font-weight: 600; }

        /* ── Input ── */
        .fi-group { margin-bottom: 22px; }
        .fi-label {
            font-size: .8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #64748b;
            margin-bottom: 8px;
            display: block;
        }
        .fi-input {
            width: 100%;
            background: #f8fafc;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: .97rem;
            font-weight: 600;
            color: #1a202c;
            transition: all .2s;
        }
        .fi-input:focus {
            background: #fff;
            border-color: #6366f1;
            outline: none;
            box-shadow: 0 0 0 4px rgba(99,102,241,.08);
        }
        .fi-prefix-wrap { position: relative; }
        .fi-prefix-wrap .fi-prefix {
            position: absolute; left: 16px; top: 50%;
            transform: translateY(-50%);
            font-weight: 800; color: #4a5568;
        }
        .fi-prefix-wrap .fi-input { padding-left: 34px; }

        /* ── Fee Summary ── */
        .fee-box {
            background: linear-gradient(135deg,#f0f9ff,#e0f2fe);
            border: 1px solid #bae6fd;
            border-radius: 18px;
            padding: 26px 28px;
            margin-bottom: 28px;
        }
        .fee-row {
            display: flex; justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed rgba(186,230,253,.7);
            font-size: .95rem; color: #0c4a6e;
        }
        .fee-row:last-child { border-bottom: none; }

        /* ── Submit btn ── */
        .btn-apply-now {
            width: 100%;
            background: linear-gradient(135deg,#6366f1,#8b5cf6);
            color: #fff;
            border: none;
            padding: 18px;
            border-radius: 14px;
            font-weight: 900;
            font-size: 1.1rem;
            box-shadow: 0 12px 30px rgba(99,102,241,.35);
            transition: all .3s;
            display: flex; align-items: center; justify-content: center; gap: 12px;
        }
        .btn-apply-now:hover { transform: translateY(-3px); box-shadow: 0 18px 40px rgba(99,102,241,.45); }

        /* ── Pending / Success state ── */
        .status-card {
            background: #fff;
            border-radius: 28px;
            padding: 60px 50px;
            box-shadow: 0 20px 60px rgba(0,0,0,.06);
            border: 1px solid #f1f5f9;
            text-align: center;
        }
        .status-icon-circle {
            width: 90px; height: 90px;
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 24px;
        }

        /* ── Range slider ── */
        input[type=range] {
            -webkit-appearance: none; width: 100%; height: 6px;
            border-radius: 3px; background: #e5e7eb; outline: none;
        }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none; width: 20px; height: 20px;
            background: #6366f1; border-radius: 50%; cursor: pointer;
            box-shadow: 0 2px 6px rgba(99,102,241,.4);
        }

        .back-lnk {
            display: inline-flex; align-items: center; gap: 8px;
            color: #64748b; font-weight: 700; text-decoration: none;
            margin-bottom: 24px; transition: color .2s;
        }
        .back-lnk:hover { color: #6366f1; }

        /* ── Info bullets ── */
        .info-bullets { display: flex; flex-direction: column; gap: 14px; }
        .ib { display: flex; align-items: flex-start; gap: 14px; padding: 16px; background: #f8fafc; border-radius: 14px; }
        .ib-icon { width: 38px; height: 38px; border-radius: 10px; background: rgba(99,102,241,.1); color: #6366f1; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
        .ib-title { font-weight: 800; font-size: .9rem; margin-bottom: 3px; }
        .ib-desc  { font-size: .8rem; color: #6b7280; line-height: 1.5; }
    </style>
</head>
<body>
<?php
$page = 'cards';
include '../includes/user-sidebar.php';
?>
<main class="main-content">
    <?php include '../includes/user-navbar.php'; ?>
    <div class="page-container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-xl-5"><!-- left form col -->

                <a href="cards.php" class="back-lnk"><i class="fa-solid fa-chevron-left"></i> Back to Cards</a>

                <!-- Hero -->
                <div class="apply-hero shadow-lg mb-0">
                    <div class="orb" style="top:-80px;right:-80px;width:250px;height:250px;background:radial-gradient(circle,rgba(99,102,241,.35) 0%,transparent 70%);"></div>
                    <div class="orb" style="bottom:-60px;left:-60px;width:180px;height:180px;background:radial-gradient(circle,rgba(139,92,246,.25) 0%,transparent 70%);"></div>
                    <div class="position-relative" style="z-index:2;">
                        <span class="badge rounded-pill px-3 py-2 mb-3 fw-800" style="background:rgba(99,102,241,.25);color:#c7d2fe;font-size:.7rem;letter-spacing:.06em;">VIRTUAL CARD ISSUANCE</span>
                        <h2>Apply for a<br>Virtual Card</h2>
                        <p class="mb-0" style="opacity:.8;">Provisioning advanced digital assets for secure, global commerce.</p>
                    </div>
                </div>

                <?php if ($active_app): ?>
                <!-- ── Already has application ── -->
                <div class="status-card mt-0" style="border-radius:0 0 28px 28px;">
                    <?php if ($active_app['status'] === 'Approved'): ?>
                    <div class="status-icon-circle" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="fa-solid fa-circle-check"></i></div>
                    <h4 class="fw-900 mb-2">Card Active!</h4>
                    <p class="text-muted mb-4">Your <?php echo strtoupper($active_app['card_type']); ?> <?php echo ucfirst($active_app['card_tier']); ?> card has been issued and is ready to use.</p>
                    <a href="cards.php" class="btn btn-success px-5 py-3 fw-800" style="border-radius:12px;">View My Cards</a>
                    <?php else: ?>
                    <div class="status-icon-circle" style="background:rgba(245,158,11,.1);color:#f59e0b;"><i class="fa-solid fa-hourglass-half"></i></div>
                    <h4 class="fw-900 mb-2">Application Under Review</h4>
                    <p class="text-muted mb-2">Your card application has been submitted and is currently being reviewed by our team.</p>
                    <div class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill mb-4" style="background:#fef3c7;color:#92400e;font-weight:700;font-size:.85rem;">
                        <i class="fa-solid fa-clock"></i>
                        Typically approved within 24 hours
                    </div>
                    <div class="p-4 rounded-3 text-start mb-4" style="background:#f8fafc;border:1px solid #e5e7eb;">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-xs text-muted fw-700 text-uppercase mb-1">Card Network</div>
                                <div class="fw-800"><?php echo strtoupper($active_app['card_type']); ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-xs text-muted fw-700 text-uppercase mb-1">Tier</div>
                                <div class="fw-800"><?php echo ucfirst($active_app['card_tier']); ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-xs text-muted fw-700 text-uppercase mb-1">Daily Limit</div>
                                <div class="fw-800">$<?php echo number_format($active_app['daily_limit'],0); ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-xs text-muted fw-700 text-uppercase mb-1">Currency</div>
                                <div class="fw-800"><?php echo $active_app['currency']; ?></div>
                            </div>
                            <div class="col-12">
                                <div class="text-xs text-muted fw-700 text-uppercase mb-1">Submitted</div>
                                <div class="fw-800"><?php echo date('M d, Y \a\t g:i A', strtotime($active_app['created_at'])); ?></div>
                            </div>
                        </div>
                    </div>
                    <a href="cards.php" class="btn btn-primary px-5 py-3 fw-800 me-2" style="border-radius:12px;">View Cards Page</a>
                    <?php endif; ?>
                </div>

                <?php else: ?>
                <!-- ── Application Form ── -->
                <div class="apply-form-wrap">

                    <?php if ($success_msg): ?>
                    <div class="alert border-0 fw-700 mb-4" style="background:rgba(16,185,129,.1);color:#065f46;border-radius:12px;padding:16px 20px;">
                        <i class="fa-solid fa-circle-check me-2"></i><?php echo $success_msg; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($error_msg): ?>
                    <div class="alert border-0 fw-700 mb-4" style="background:rgba(239,68,68,.1);color:#991b1b;border-radius:12px;padding:16px 20px;">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo $error_msg; ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" id="cardApplyForm">

                        <!-- Step 1: Card Network -->
                        <h5 class="fw-900 mb-4" style="color:#1a202c;letter-spacing:-.5px;">1. Card Network</h5>
                        <div class="card-type-grid mb-5">
                            <div>
                                <input class="card-type-option" type="radio" name="card_type" value="visa" id="typeVisa" checked>
                                <label class="card-type-label" for="typeVisa">
                                    <span class="radio-dot"></span>
                                    <div class="flex-grow-1">
                                        <div class="fw-800">Visa Infinite Virtual</div>
                                        <div class="text-muted" style="font-size:.85rem;">Universal subscription & retail coverage</div>
                                    </div>
                                    <i class="fa-brands fa-cc-visa network-brand visa-c"></i>
                                </label>
                            </div>
                            <div>
                                <input class="card-type-option" type="radio" name="card_type" value="mastercard" id="typeMC">
                                <label class="card-type-label" for="typeMC">
                                    <span class="radio-dot"></span>
                                    <div class="flex-grow-1">
                                        <div class="fw-800">Mastercard World Elite</div>
                                        <div class="text-muted" style="font-size:.85rem;">High-tier security for commercial spend</div>
                                    </div>
                                    <i class="fa-brands fa-cc-mastercard network-brand mc-c"></i>
                                </label>
                            </div>
                            <div>
                                <input class="card-type-option" type="radio" name="card_type" value="amex" id="typeAmex">
                                <label class="card-type-label" for="typeAmex">
                                    <span class="radio-dot"></span>
                                    <div class="flex-grow-1">
                                        <div class="fw-800">American Express Digital</div>
                                        <div class="text-muted" style="font-size:.85rem;">Exclusive Capital rewards integration</div>
                                    </div>
                                    <i class="fa-brands fa-cc-amex network-brand amex-c"></i>
                                </label>
                            </div>
                        </div>

                        <!-- Step 2: Tier -->
                        <h5 class="fw-900 mb-4" style="color:#1a202c;letter-spacing:-.5px;">2. Card Tier</h5>
                        <div class="tier-grid mb-5">
                            <div>
                                <input class="tier-option" type="radio" name="card_tier" value="standard" id="tierStd" checked>
                                <label class="tier-label" for="tierStd">
                                    <div class="tier-name">Standard Core</div>
                                    <div class="tier-fee">$5 activation fee</div>
                                </label>
                            </div>
                            <div>
                                <input class="tier-option" type="radio" name="card_tier" value="gold" id="tierGold">
                                <label class="tier-label" for="tierGold">
                                    <div class="tier-name" style="color:#b45309;">⭐ Gold Elite</div>
                                    <div class="tier-fee">$15 activation fee</div>
                                </label>
                            </div>
                            <div>
                                <input class="tier-option" type="radio" name="card_tier" value="platinum" id="tierPlt">
                                <label class="tier-label" for="tierPlt">
                                    <div class="tier-name" style="color:#475569;">💎 Platinum Prestige</div>
                                    <div class="tier-fee">$25 activation fee</div>
                                </label>
                            </div>
                            <div>
                                <input class="tier-option" type="radio" name="card_tier" value="black" id="tierBlack">
                                <label class="tier-label" for="tierBlack">
                                    <div class="tier-name" style="color:#0f172a;">🖤 Sovereign Black</div>
                                    <div class="tier-fee">$50 activation fee</div>
                                </label>
                            </div>
                        </div>

                        <!-- Step 3: Config -->
                        <h5 class="fw-900 mb-4" style="color:#1a202c;letter-spacing:-.5px;">3. Configuration</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <div class="fi-group">
                                    <label class="fi-label">Currency</label>
                                    <select class="fi-input" name="currency">
                                        <option value="USD" selected>USD — United States Dollar</option>
                                        <option value="EUR">EUR — European Euro</option>
                                        <option value="GBP">GBP — British Pound Sterling</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="fi-group">
                                    <label class="fi-label">Daily Spending Limit — <span id="limitDisplay">$5,000</span></label>
                                    <input type="range" name="daily_limit" id="limitSlider" min="1000" max="50000" step="500" value="5000">
                                    <div class="d-flex justify-content-between mt-2">
                                        <small class="text-muted fw-700">$1,000</small>
                                        <small class="text-muted fw-700">$50,000</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Verification -->
                        <h5 class="fw-900 mb-4" style="color:#1a202c;letter-spacing:-.5px;">4. Verification & Billing</h5>
                        <div class="fi-group">
                            <label class="fi-label">Legal Cardholder Name</label>
                            <input type="text" class="fi-input" name="cardholder_name" placeholder="Full name as on account" value="<?php echo htmlspecialchars($user_name); ?>" required>
                        </div>
                        <div class="fi-group">
                            <label class="fi-label">Billing Address</label>
                            <textarea class="fi-input" name="billing_address" rows="3" placeholder="Verified residential or business address" required style="height:auto;"></textarea>
                        </div>

                        <!-- Fee Summary -->
                        <div class="fee-box">
                            <div class="d-flex align-items-center gap-2 fw-800 mb-3" style="color:#0369a1;"><i class="fa-solid fa-receipt"></i> Issuance Summary</div>
                            <div class="fee-row">
                                <span>Activation Fee (based on tier)</span>
                                <b id="feeDisplay">$5.00</b>
                            </div>
                            <div class="fee-row">
                                <span>Monthly Management</span>
                                <b>$0.00</b>
                            </div>
                            <div class="fee-row">
                                <span>Transaction Interchange</span>
                                <b>0%</b>
                            </div>
                            <p class="mt-3 mb-0 text-muted" style="font-size:.8rem;font-weight:600;">Fee is deducted from your primary balance at card initialization.</p>
                        </div>

                        <!-- Agreement -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="termsCheck" required>
                            <label class="form-check-label text-muted fw-600" for="termsCheck" style="font-size:.85rem;">
                                I authorize issuance of this digital asset and agree to the
                                <a href="#" class="text-primary fw-700">Digital Banking Agreement</a> and
                                <a href="#" class="text-primary fw-700">Privacy Protocols</a>.
                            </label>
                        </div>

                        <button type="submit" class="btn-apply-now">
                            <i class="fa-solid fa-credit-card"></i> Submit Application
                        </button>
                    </form>
                </div>
                <?php endif; ?>

            </div>

            <!-- Right sidebar info -->
            <div class="col-lg-4 col-xl-4 d-none d-lg-block ps-4">
                <div class="mt-5 pt-3">
                    <!-- Floating card visual -->
                    <div class="mb-4">
                        <div style="background:linear-gradient(135deg,#302b63,#0f0c29);border-radius:22px;padding:28px;color:#fff;box-shadow:0 25px 60px rgba(0,0,0,.35);position:relative;overflow:hidden;">
                            <div style="position:absolute;top:-60px;right:-60px;width:180px;height:180px;background:radial-gradient(circle,rgba(99,102,241,.3) 0%,transparent 70%);border-radius:50%;"></div>
                            <div style="position:absolute;bottom:-40px;left:-40px;width:140px;height:140px;background:radial-gradient(circle,rgba(139,92,246,.2) 0%,transparent 70%);border-radius:50%;"></div>
                            <!-- Shine -->
                            <div style="position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(120deg,transparent,rgba(255,255,255,.07),transparent);animation:shine 4s infinite;"></div>
                            <style>@keyframes shine{0%{left:-100%}20%{left:100%}100%{left:100%}}</style>
                            <div style="position:relative;z-index:2;">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <span style="font-weight:800;letter-spacing:1px;font-size:.95rem;opacity:.9;">Swift Capital</span>
                                    <div style="width:42px;height:32px;background:linear-gradient(135deg,#ffd700,#b8860b);border-radius:6px;"></div>
                                </div>
                                <div style="font-size:1.3rem;letter-spacing:4px;font-weight:700;margin-bottom:22px;text-shadow:1px 1px 3px rgba(0,0,0,.3);">•••• •••• •••• ????</div>
                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <div style="font-size:.5rem;text-transform:uppercase;opacity:.6;letter-spacing:1px;margin-bottom:4px;">Card Holder</div>
                                        <div style="font-size:.9rem;font-weight:600;"><?php echo htmlspecialchars(strtoupper($user_name)); ?></div>
                                    </div>
                                    <div style="font-size:2.2rem;opacity:.85;"><i class="fa-brands fa-cc-visa" id="previewBrand"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-800 mb-3">Why Apply?</h6>
                    <div class="info-bullets mb-4">
                        <div class="ib">
                            <div class="ib-icon"><i class="fa-solid fa-bolt"></i></div>
                            <div><div class="ib-title">Instant Activation</div><div class="ib-desc">Ready to use within 60 seconds of approval.</div></div>
                        </div>
                        <div class="ib">
                            <div class="ib-icon"><i class="fa-solid fa-shield-halved"></i></div>
                            <div><div class="ib-title">Account Isolation</div><div class="ib-desc">Primary account stays hidden from merchants.</div></div>
                        </div>
                        <div class="ib">
                            <div class="ib-icon"><i class="fa-solid fa-globe"></i></div>
                            <div><div class="ib-title">200+ Countries</div><div class="ib-desc">Accepted on millions of websites globally.</div></div>
                        </div>
                        <div class="ib">
                            <div class="ib-icon"><i class="fa-solid fa-sliders"></i></div>
                            <div><div class="ib-title">Granular Control</div><div class="ib-desc">Adjust spending limits anytime from your dashboard.</div></div>
                        </div>
                    </div>

                    <!-- Tier comparison -->
                    <h6 class="fw-800 mb-3">Tier Comparison</h6>
                    <div class="p-4 rounded-4" style="background:#f8fafc;border:1px solid #e5e7eb;">
                        <table class="w-100" style="font-size:.82rem;">
                            <thead>
                                <tr style="border-bottom:1px solid #e5e7eb;">
                                    <th class="pb-2 fw-800 text-muted">Tier</th>
                                    <th class="pb-2 fw-800 text-muted text-end">Fee</th>
                                    <th class="pb-2 fw-800 text-muted text-end">Daily Max</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td class="py-2 fw-700">Standard</td><td class="text-end fw-700">$5</td><td class="text-end text-muted">$10K</td></tr>
                                <tr><td class="py-2 fw-700" style="color:#b45309;">Gold</td><td class="text-end fw-700">$15</td><td class="text-end text-muted">$25K</td></tr>
                                <tr><td class="py-2 fw-700" style="color:#475569;">Platinum</td><td class="text-end fw-700">$25</td><td class="text-end text-muted">$40K</td></tr>
                                <tr><td class="py-2 fw-700">Black</td><td class="text-end fw-700">$50</td><td class="text-end text-muted">$50K</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="main-footer mt-auto">
        <div class="brand">
            <span class="text-primary fw-bold" style="letter-spacing:-.5px;">Swift</span><span class="text-dark fw-bold" style="letter-spacing:-.5px;">Capital</span> © 2026 SwiftCapital. All rights reserved.
        </div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Contact Support</a>
        </div>
    </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ── Limit slider ──
    const slider = document.getElementById('limitSlider');
    const display = document.getElementById('limitDisplay');
    if (slider) {
        slider.addEventListener('input', () => {
            display.textContent = '$' + parseInt(slider.value).toLocaleString();
        });
    }

    // ── Tier fee display ──
    const tierFees = { standard: '$5.00', gold: '$15.00', platinum: '$25.00', black: '$50.00' };
    document.querySelectorAll('.tier-option').forEach(r => {
        r.addEventListener('change', () => {
            const fd = document.getElementById('feeDisplay');
            if (fd) fd.textContent = tierFees[r.value] || '$5.00';
        });
    });

    // ── Card brand preview ──
    const brandIcons = { visa: 'fa-brands fa-cc-visa visa-c', mastercard: 'fa-brands fa-cc-mastercard mc-c', amex: 'fa-brands fa-cc-amex amex-c' };
    document.querySelectorAll('.card-type-option').forEach(r => {
        r.addEventListener('change', () => {
            const pb = document.getElementById('previewBrand');
            if (pb) { pb.className = brandIcons[r.value] || 'fa-brands fa-cc-visa'; }
        });
    });
</script>
</body>
</html>
