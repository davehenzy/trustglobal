<?php
require_once '../includes/db.php';
require_once '../includes/user-check.php';

$user_id = $_SESSION['user_id'];
$error   = '';
$success = false;
$amount  = 0;
$tx_ref  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount       = (float)($_POST['amount'] ?? 0);
    $skrill_email = trim($_POST['skrill_email'] ?? '');
    $full_name    = trim(htmlspecialchars($_POST['full_name'] ?? ''));
    $currency     = htmlspecialchars($_POST['currency'] ?? 'USD');
    $country      = htmlspecialchars($_POST['country'] ?? '');
    $skrill_id    = trim(htmlspecialchars($_POST['skrill_id'] ?? ''));
    $memo         = trim(htmlspecialchars($_POST['memo'] ?? ''));
    $tx_pin       = $_POST['tx_pin'] ?? '';

    // Fetch sender
    $sender = $pdo->prepare("SELECT balance, pin FROM users WHERE id = ?");
    $sender->execute([$user_id]);
    $sender = $sender->fetch();

    if ($amount <= 0) {
        $error = 'Please enter a valid withdrawal amount.';
    } elseif ($amount < 5) {
        $error = 'Minimum Skrill withdrawal is $5.00.';
    } elseif ($amount > $sender['balance']) {
        $error = 'Insufficient balance. Available: $' . number_format($sender['balance'], 2) . '.';
    } elseif (empty($skrill_email) || !filter_var($skrill_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid Skrill account email address.';
    } elseif (empty($full_name)) {
        $error = 'Please enter the full name on the Skrill account.';
    } elseif (empty($tx_pin)) {
        $error = 'Transaction PIN is required.';
    } elseif ($tx_pin !== $sender['pin']) {
        $error = 'Incorrect transaction PIN. Please try again.';
    } else {
        $narration = "Skrill Withdrawal to $skrill_email ($full_name) | Currency: $currency";
        if ($country)   $narration .= " | Country: $country";
        if ($skrill_id) $narration .= " | Skrill ID: $skrill_id";
        if ($memo)      $narration .= " | Note: $memo";
        $tx_ref = 'SCS' . strtoupper(substr(md5(uniqid()), 0, 10));

        try {
            // PENDING — balance deducted only on admin approval
            $pdo->prepare("INSERT INTO transactions (user_id, amount, type, method, status, txn_hash, narration, created_at)
                           VALUES (?, ?, 'Debit', 'Skrill', 'Pending', ?, ?, NOW())")
                ->execute([$user_id, $amount, $tx_ref, $narration]);
            $success = true;
        } catch (Exception $e) {
            $error = 'Failed to submit request. Please try again.';
        }
    }
}

$currencies = [
    'USD'=>'US Dollar','EUR'=>'Euro','GBP'=>'British Pound','CAD'=>'Canadian Dollar',
    'AUD'=>'Australian Dollar','CHF'=>'Swiss Franc','JPY'=>'Japanese Yen',
    'NOK'=>'Norwegian Krone','SEK'=>'Swedish Krona','DKK'=>'Danish Krone',
    'PLN'=>'Polish Zloty','CZK'=>'Czech Koruna','HUF'=>'Hungarian Forint',
    'RON'=>'Romanian Leu','BGN'=>'Bulgarian Lev',
];

$countries = [
    "Afghanistan","Albania","Algeria","Argentina","Armenia","Australia","Austria","Azerbaijan",
    "Bahrain","Bangladesh","Belarus","Belgium","Bolivia","Brazil","Bulgaria","Cambodia","Canada",
    "Chile","China","Colombia","Croatia","Cyprus","Czech Republic","Denmark","Ecuador","Egypt",
    "Estonia","Finland","France","Georgia","Germany","Ghana","Greece","Hungary","Iceland","India",
    "Indonesia","Ireland","Israel","Italy","Japan","Jordan","Kazakhstan","Kenya","Kuwait","Latvia",
    "Lebanon","Lithuania","Luxembourg","Malaysia","Malta","Mexico","Morocco","Netherlands",
    "New Zealand","Nigeria","Norway","Pakistan","Peru","Philippines","Poland","Portugal","Qatar",
    "Romania","Russia","Saudi Arabia","Singapore","Slovakia","South Africa","South Korea",
    "Spain","Sri Lanka","Sweden","Switzerland","Taiwan","Thailand","Tunisia","Turkey","Ukraine",
    "United Arab Emirates","United Kingdom","United States","Vietnam",
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skrill Withdrawal - SwiftCapital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Skrill brand: #862165 (deep magenta/purple) + #c11f7d */
        .sk-wrap { max-width: 1100px; margin: 0 auto; }

        /* Hero ───────────────────────────────── */
        .sk-hero {
            background: linear-gradient(135deg,#1a0030,#3b0060,#7b1fa2);
            border-radius: 24px 24px 0 0;
            padding: 46px 54px 56px;
            color: #fff; text-align: center;
            position: relative; overflow: hidden;
        }
        .sk-hero .orb { position: absolute; border-radius: 50%; pointer-events: none; }
        .sk-hero-icon {
            width: 78px; height: 78px;
            background: rgba(193,31,125,.15);
            border: 1px solid rgba(193,31,125,.3);
            border-radius: 22px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2.2rem; color: #f48fb1;
            margin-bottom: 18px;
        }
        .sk-hero h3 { font-weight: 900; letter-spacing: -1.5px; font-size: 2rem; margin-bottom: 8px; }
        .sk-hero p  { opacity: .75; font-size: .97rem; max-width: 500px; margin: 0 auto 22px; }
        .sk-hero-badges { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
        .sk-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(193,31,125,.12); border: 1px solid rgba(193,31,125,.25);
            border-radius: 50px; padding: 7px 16px;
            font-size: .78rem; font-weight: 700;
        }
        .sk-badge i { color: #f48fb1; }

        /* Body ───────────────────────────────── */
        .sk-body {
            background: #fff; border: 1px solid #e5e7eb; border-top: none;
            border-radius: 0 0 24px 24px;
            padding: 48px 54px 54px;
            box-shadow: 0 20px 60px rgba(0,0,0,.05);
        }

        /* Balance strip */
        .sk-balance {
            background: linear-gradient(135deg,#1a0030,#7b1fa2);
            border-radius: 16px; padding: 20px 26px;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 36px; color: #fff;
        }
        .sk-balance .lbl { font-size: .7rem; text-transform: uppercase; letter-spacing:.07em; opacity:.75; font-weight: 700; margin-bottom: 3px; }
        .sk-balance .val { font-size: 1.7rem; font-weight: 900; letter-spacing: -.5px; }

        /* Skrill ID preview */
        .sk-id-preview {
            background: linear-gradient(135deg,#f3e5f5,#e1bee7);
            border: 1.5px solid #ce93d8;
            border-radius: 14px; padding: 16px 22px; margin-bottom: 24px;
            display: flex; align-items: center; gap: 14px;
        }
        .sk-id-preview i { color: #7b1fa2; font-size: 1.4rem; flex-shrink: 0; }
        .sk-id-preview p { margin: 0; font-size: .88rem; font-weight: 600; color: #4a148c; line-height: 1.5; }

        /* Section */
        .sk-sec {
            font-size: .68rem; font-weight: 900; text-transform: uppercase;
            letter-spacing: .12em; color: #64748b;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px; margin-bottom: 22px;
            display: flex; align-items: center; gap: 10px;
        }
        .sk-sec i { color: #7b1fa2; }

        /* Amount */
        .sk-amt-box {
            background: #f8fafc; border: 2px solid #e5e7eb;
            border-radius: 20px; padding: 26px 30px; margin-bottom: 36px; transition: all .25s;
        }
        .sk-amt-box:focus-within { border-color: #7b1fa2; background: #fff; box-shadow: 0 0 0 5px rgba(123,31,162,.06); }
        .sk-amt-flex { display: flex; align-items: center; gap: 10px; }
        .sk-sym { font-size: 2.4rem; font-weight: 900; color: #1a202c; }
        .sk-amt-inp {
            flex: 1; border: none; background: transparent;
            font-size: 2.8rem; font-weight: 900; color: #1a202c; outline: none; min-width: 0;
        }
        .sk-quick { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 16px; }
        .sk-qbtn {
            background: #fff; border: 1.5px solid #e5e7eb;
            border-radius: 10px; padding: 7px 16px;
            font-weight: 700; font-size: .85rem; color: #4a5568; cursor: pointer; transition: all .15s;
        }
        .sk-qbtn:hover, .sk-qbtn.active { border-color: #7b1fa2; color: #4a148c; background: #f3e5f5; }

        /* Fields */
        .sk-field { margin-bottom: 20px; }
        .sk-label { font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing:.05em; color: #64748b; margin-bottom: 8px; display: block; }
        .sk-inp-wrap { position: relative; }
        .sk-inp-wrap .ico { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
        .sk-inp {
            width: 100%; background: #f8fafc; border: 1.5px solid #e5e7eb;
            border-radius: 12px; padding: 13px 16px 13px 44px;
            font-size: .97rem; font-weight: 600; color: #1a202c; transition: all .2s;
        }
        .sk-inp:focus { background: #fff; border-color: #7b1fa2; outline: none; box-shadow: 0 0 0 4px rgba(123,31,162,.07); }
        .sk-pin-toggle { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; }

        /* Review */
        .sk-review {
            background: #f8fafc; border: 1.5px solid #e5e7eb;
            border-radius: 18px; padding: 24px 28px; margin-bottom: 28px;
        }
        .sk-review-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #e5e7eb; font-size: .9rem; }
        .sk-review-row:last-child { border-bottom: none; }
        .sk-review-row .rk { color: #64748b; font-weight: 600; }
        .sk-review-row .rv { font-weight: 800; color: #1a202c; }

        /* Submit */
        .sk-submit {
            width: 100%; background: linear-gradient(135deg,#7b1fa2,#c11f7d);
            color: #fff; border: none; padding: 18px;
            border-radius: 14px; font-weight: 900; font-size: 1.05rem;
            box-shadow: 0 12px 28px rgba(123,31,162,.3);
            transition: all .3s; display: flex; align-items: center; justify-content: center; gap: 12px;
        }
        .sk-submit:hover { transform: translateY(-2px); box-shadow: 0 18px 40px rgba(123,31,162,.4); }
        .sk-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        /* Error */
        .sk-error {
            background: #fef2f2; border: 1px solid #fca5a5; border-radius: 14px;
            padding: 16px 20px; margin-bottom: 24px;
            display: flex; align-items: center; gap: 14px;
            font-weight: 700; color: #991b1b; font-size: .94rem;
        }
        .sk-error i { font-size: 1.2rem; flex-shrink: 0; }

        /* Success */
        .sk-success {
            text-align: center; padding: 60px 40px;
            background: #fff; border: 1px solid #e5e7eb; border-top: none;
            border-radius: 0 0 24px 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,.05);
        }
        .sk-success-icon {
            width: 96px; height: 96px; border-radius: 50%;
            background: linear-gradient(135deg,#7b1fa2,#c11f7d);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2.5rem; color: #fff;
            box-shadow: 0 16px 40px rgba(123,31,162,.35);
            margin-bottom: 24px;
            animation: pop .5s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes pop { from{transform:scale(.3);opacity:0}to{transform:scale(1);opacity:1} }

        /* Secure */
        .sk-secure {
            display: flex; align-items: center; gap: 16px;
            background: #f8fafc; border: 1px solid #e5e7eb;
            border-radius: 16px; padding: 18px 22px; margin-top: 28px;
        }
        .sk-secure i { color: #10b981; font-size: 1.4rem; flex-shrink: 0; }
        .sk-secure h6 { font-weight: 800; font-size: .9rem; margin-bottom: 3px; }
        .sk-secure p  { font-size: .8rem; color: #6b7280; margin: 0; }
    </style>
</head>
<body>
<?php
$page = 'international';
include '../includes/user-sidebar.php';
?>
<main class="main-content">
    <?php include '../includes/user-navbar.php'; ?>
    <div class="page-container">
        <div class="sk-wrap">

            <div class="page-header mb-4">
                <div>
                    <h1 class="page-title">Skrill Withdrawal</h1>
                    <div class="breadcrumb-text">
                        <a href="index.php">Dashboard</a>
                        <i class="fa-solid fa-chevron-right mx-2" style="font-size:.7rem;"></i>
                        International
                        <i class="fa-solid fa-chevron-right mx-2" style="font-size:.7rem;"></i>
                        Skrill
                    </div>
                </div>
            </div>

            <!-- Hero -->
            <div class="sk-hero shadow-lg">
                <div class="orb" style="top:-80px;right:-80px;width:280px;height:280px;background:radial-gradient(circle,rgba(193,31,125,.2) 0%,transparent 70%);"></div>
                <div class="orb" style="bottom:-60px;left:-60px;width:200px;height:200px;background:radial-gradient(circle,rgba(123,31,162,.15) 0%,transparent 70%);"></div>
                <div class="position-relative" style="z-index:2;">
                    <div class="sk-hero-icon"><i class="fa-solid fa-wallet"></i></div>
                    <h3>Skrill Withdrawal</h3>
                    <p>Send funds globally to any Skrill account. Available in 40+ currencies across 200+ countries.</p>
                    <div class="sk-hero-badges">
                        <span class="sk-badge"><i class="fa-solid fa-shield-check"></i> PCI-DSS Secured</span>
                        <span class="sk-badge"><i class="fa-solid fa-lock"></i> PIN Protected</span>
                        <span class="sk-badge"><i class="fa-solid fa-globe"></i> 200+ Countries</span>
                        <span class="sk-badge"><i class="fa-solid fa-user-shield"></i> Admin Reviewed</span>
                    </div>
                </div>
            </div>

            <?php if ($success): ?>
            <!-- SUCCESS -->
            <div class="sk-success">
                <div class="sk-success-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                <h3 class="fw-900 mb-2" style="letter-spacing:-.5px;">Request Submitted!</h3>
                <p class="text-muted mb-1">Your Skrill withdrawal is <strong>pending admin review</strong>.</p>
                <p class="text-muted mb-4" style="font-size:.85rem;">Your balance will be deducted once approved. Skrill transfers typically arrive within <strong>24 hours</strong> of approval.</p>

                <div class="d-inline-flex align-items-center gap-2 px-4 py-3 rounded-pill mb-5 fw-800" style="background:#f3e5f5;color:#4a148c;font-size:.9rem;border:1.5px solid #ce93d8;">
                    <i class="fa-solid fa-clock"></i> Awaiting Admin Approval
                </div>

                <div class="sk-review text-start" style="max-width:480px;margin:0 auto 32px;">
                    <div class="sk-review-row" style="font-size:1rem;">
                        <span class="rk">Amount</span>
                        <span class="rv text-danger">−$<?php echo number_format($amount, 2); ?></span>
                    </div>
                    <div class="sk-review-row">
                        <span class="rk">Skrill Email</span>
                        <span class="rv"><?php echo htmlspecialchars($skrill_email); ?></span>
                    </div>
                    <div class="sk-review-row">
                        <span class="rk">Account Holder</span>
                        <span class="rv"><?php echo htmlspecialchars($full_name); ?></span>
                    </div>
                    <div class="sk-review-row">
                        <span class="rk">Currency</span>
                        <span class="rv"><?php echo htmlspecialchars($currency); ?></span>
                    </div>
                    <?php if (!empty($country)): ?>
                    <div class="sk-review-row">
                        <span class="rk">Country</span>
                        <span class="rv"><?php echo htmlspecialchars($country); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($skrill_id)): ?>
                    <div class="sk-review-row">
                        <span class="rk">Skrill Customer ID</span>
                        <span class="rv" style="font-family:monospace;"><?php echo htmlspecialchars($skrill_id); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="sk-review-row">
                        <span class="rk">Reference</span>
                        <span class="rv" style="font-family:monospace;"><?php echo $tx_ref; ?></span>
                    </div>
                    <div class="sk-review-row">
                        <span class="rk">Status</span>
                        <span class="rv"><span class="badge fw-800" style="background:#f3e5f5;color:#4a148c;border:1px solid #ce93d8;">Pending Review</span></span>
                    </div>
                    <?php if (!empty($memo)): ?>
                    <div class="sk-review-row">
                        <span class="rk">Note</span>
                        <span class="rv"><?php echo htmlspecialchars($memo); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="international-skrill.php" class="btn fw-800 px-4 py-3" style="border-radius:12px;background:linear-gradient(135deg,#7b1fa2,#c11f7d);color:#fff;">New Withdrawal</a>
                    <a href="transactions.php" class="btn btn-outline-secondary fw-700 px-4 py-3" style="border-radius:12px;">View Transactions</a>
                    <a href="index.php" class="btn btn-light fw-700 px-4 py-3" style="border-radius:12px;">Dashboard</a>
                </div>
            </div>

            <?php else: ?>
            <!-- FORM -->
            <div class="sk-body">

                <?php if ($error): ?>
                <div class="sk-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <!-- Balance -->
                <div class="sk-balance">
                    <div>
                        <div class="lbl">Available Balance</div>
                        <div class="val">$<?php echo number_format($_SESSION['balance'], 2); ?></div>
                    </div>
                    <div style="background:rgba(193,31,125,.15);border-radius:12px;padding:10px 18px;font-weight:800;font-size:.85rem;display:flex;align-items:center;gap:8px;color:#f48fb1;">
                        <i class="fa-solid fa-wallet"></i> Skrill Ready
                    </div>
                </div>

                <!-- Info tip -->
                <div class="sk-id-preview">
                    <i class="fa-solid fa-circle-info"></i>
                    <p>Skrill uses your <strong>registered email address</strong> as your wallet ID. Optionally provide your Skrill Customer ID for faster processing. All withdrawals require admin approval before funds are released.</p>
                </div>

                <form method="POST" id="skrillForm" autocomplete="off">

                    <!-- Account details -->
                    <div class="sk-sec"><i class="fa-solid fa-wallet"></i> Skrill Account Details</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="sk-field">
                                <label class="sk-label">Skrill Email Address *</label>
                                <div class="sk-inp-wrap">
                                    <i class="ico fa-solid fa-envelope"></i>
                                    <input type="email" name="skrill_email" id="skrillEmail" class="sk-inp"
                                           placeholder="email@skrill.com" required autocomplete="off"
                                           value="<?php echo isset($_POST['skrill_email']) ? htmlspecialchars($_POST['skrill_email']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="sk-field">
                                <label class="sk-label">Account Holder Full Name *</label>
                                <div class="sk-inp-wrap">
                                    <i class="ico fa-solid fa-user"></i>
                                    <input type="text" name="full_name" class="sk-inp"
                                           placeholder="Legal name on Skrill account" required
                                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="sk-field">
                                <label class="sk-label">Payout Currency</label>
                                <div class="sk-inp-wrap">
                                    <i class="ico fa-solid fa-coins"></i>
                                    <select name="currency" class="sk-inp" style="appearance:none;padding-right:40px;">
                                        <?php foreach ($currencies as $code => $name):
                                            $sel = (isset($_POST['currency']) && $_POST['currency']==$code) || (!isset($_POST['currency']) && $code==='USD'); ?>
                                        <option value="<?php echo $code; ?>" <?php echo $sel?'selected':''; ?>><?php echo "$code — $name"; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="sk-field">
                                <label class="sk-label">Country <span class="text-muted fw-600">(optional)</span></label>
                                <div class="sk-inp-wrap">
                                    <i class="ico fa-solid fa-earth-europe"></i>
                                    <select name="country" class="sk-inp" style="appearance:none;padding-right:40px;">
                                        <option value="">Select Country</option>
                                        <?php foreach ($countries as $c): ?>
                                        <option value="<?php echo $c; ?>" <?php echo (isset($_POST['country']) && $_POST['country']==$c)?'selected':''; ?>><?php echo $c; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="sk-field">
                                <label class="sk-label">Skrill Customer ID <span class="text-muted fw-600">(optional)</span></label>
                                <div class="sk-inp-wrap">
                                    <i class="ico fa-solid fa-id-badge"></i>
                                    <input type="text" name="skrill_id" class="sk-inp"
                                           placeholder="e.g. 12345678"
                                           value="<?php echo isset($_POST['skrill_id']) ? htmlspecialchars($_POST['skrill_id']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="sk-sec"><i class="fa-solid fa-dollar-sign"></i> Withdrawal Amount</div>
                    <div class="sk-amt-box">
                        <div class="sk-amt-flex">
                            <span class="sk-sym">$</span>
                            <input type="number" name="amount" id="amtInput" class="sk-amt-inp"
                                   placeholder="0.00" step="0.01" min="5"
                                   value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>"
                                   required>
                        </div>
                        <div class="sk-quick">
                            <button type="button" class="sk-qbtn" onclick="setAmt(50)">$50</button>
                            <button type="button" class="sk-qbtn" onclick="setAmt(100)">$100</button>
                            <button type="button" class="sk-qbtn" onclick="setAmt(500)">$500</button>
                            <button type="button" class="sk-qbtn" onclick="setAmt(1000)">$1,000</button>
                            <button type="button" class="sk-qbtn" onclick="setAmt(<?php echo floor($_SESSION['balance']); ?>)">Max</button>
                        </div>
                    </div>

                    <!-- Auth + Review -->
                    <div class="sk-sec"><i class="fa-solid fa-shield-keyhole"></i> Authentication</div>
                    <div class="row g-3 mb-5">
                        <div class="col-md-6">
                            <div class="sk-field">
                                <label class="sk-label">Transaction PIN *</label>
                                <div class="sk-inp-wrap" style="position:relative;">
                                    <i class="ico fa-solid fa-lock"></i>
                                    <input type="password" name="tx_pin" id="pinInp" class="sk-inp"
                                           placeholder="Your secure PIN" required autocomplete="new-password">
                                    <i class="fa-solid fa-eye sk-pin-toggle" onclick="togglePin(this)"></i>
                                </div>
                                <small class="text-muted mt-1 d-block fw-600" style="font-size:.77rem;">Required to authorize this withdrawal.</small>
                            </div>
                            <div class="sk-field">
                                <label class="sk-label">Note <span class="text-muted fw-600">(optional)</span></label>
                                <div class="sk-inp-wrap">
                                    <i class="ico fa-solid fa-note-sticky"></i>
                                    <input type="text" name="memo" class="sk-inp"
                                           placeholder="Purpose of withdrawal"
                                           value="<?php echo isset($_POST['memo']) ? htmlspecialchars($_POST['memo']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-start">
                            <div class="sk-review w-100" style="margin-bottom:0;">
                                <div style="font-size:.68rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:#7b1fa2;margin-bottom:10px;">Transfer Summary</div>
                                <div class="sk-review-row"><span class="rk">Recipient</span><span class="rv" id="rvEmail" style="font-size:.82rem;">—</span></div>
                                <div class="sk-review-row"><span class="rk">Amount</span><span class="rv" id="rvAmt">—</span></div>
                                <div class="sk-review-row"><span class="rk">Skrill Fee</span><span class="rv text-success">Included</span></div>
                                <div class="sk-review-row"><span class="rk fw-800 text-dark">Total</span><span class="rv text-danger fw-900" id="rvTotal">—</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="row g-3">
                        <div class="col-md-8">
                            <button type="submit" id="submitBtn" class="sk-submit">
                                <i class="fa-solid fa-wallet"></i> Submit Skrill Withdrawal
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="international.php" class="btn btn-light fw-700 w-100 py-3" style="border-radius:14px;">Cancel</a>
                        </div>
                    </div>

                </form>

                <div class="sk-secure">
                    <i class="fa-solid fa-shield-check"></i>
                    <div>
                        <h6>PCI-DSS Compliant & Admin-Reviewed</h6>
                        <p>All Skrill withdrawals are reviewed by our security team before processing. Available in 200+ countries across 40+ currencies.</p>
                    </div>
                </div>

            </div>
            <?php endif; ?>

        </div>
    </div>

    <footer class="main-footer mt-auto">
        <div class="brand">
            <span class="text-primary fw-bold" style="letter-spacing:-.5px;">Swift</span><span class="text-dark fw-bold" style="letter-spacing:-.5px;">Capital</span> © 2026 SwiftCapital. All rights reserved.
        </div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a><a href="#">Terms of Service</a><a href="#">Contact Support</a>
        </div>
    </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function setAmt(v) {
        document.getElementById('amtInput').value = v.toFixed(2);
        document.querySelectorAll('.sk-qbtn').forEach(b => b.classList.remove('active'));
        event.currentTarget.classList.add('active');
        updateReview();
    }

    function updateReview() {
        const v   = parseFloat(document.getElementById('amtInput').value) || 0;
        const fmt = v > 0 ? '$' + v.toLocaleString('en-US', {minimumFractionDigits:2}) : '—';
        document.getElementById('rvAmt').textContent   = fmt;
        document.getElementById('rvTotal').textContent = fmt;
    }
    document.getElementById('amtInput')?.addEventListener('input', updateReview);

    // Live email → review
    const emailInp = document.getElementById('skrillEmail');
    const rvEmail  = document.getElementById('rvEmail');
    emailInp?.addEventListener('input', function() {
        rvEmail.textContent = this.value.trim() || '—';
    });
    emailInp?.addEventListener('blur', function() {
        const v  = this.value.trim();
        const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
        this.style.borderColor = v ? (ok ? '#7b1fa2' : '#ef4444') : '#e5e7eb';
    });

    updateReview();

    function togglePin(icon) {
        const inp = document.getElementById('pinInp');
        if (inp.type === 'password') { inp.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
        else { inp.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
    }

    document.getElementById('skrillForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
    });
</script>
</body>
</html>
