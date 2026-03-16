<?php
require_once '../includes/db.php';
require_once '../includes/user-check.php';

$user_id = $_SESSION['user_id'];
$error   = '';
$success = false;
$amount  = 0;
$tx_ref  = '';

// Full country list
$countries = [
    "Afghanistan","Albania","Algeria","Argentina","Armenia","Australia","Austria","Azerbaijan",
    "Bahrain","Bangladesh","Belarus","Belgium","Bolivia","Brazil","Bulgaria","Cambodia","Canada",
    "Chile","China","Colombia","Croatia","Cyprus","Czech Republic","Denmark","Ecuador","Egypt",
    "Estonia","Finland","France","Georgia","Germany","Ghana","Greece","Hungary","Iceland","India",
    "Indonesia","Ireland","Israel","Italy","Japan","Jordan","Kazakhstan","Kenya","Kuwait","Latvia",
    "Lebanon","Lithuania","Luxembourg","Malaysia","Malta","Mexico","Morocco","Netherlands",
    "New Zealand","Nigeria","Norway","Pakistan","Peru","Philippines","Poland","Portugal","Qatar",
    "Romania","Russia","Saudi Arabia","Senegal","Singapore","Slovakia","South Africa","South Korea",
    "Spain","Sri Lanka","Sweden","Switzerland","Taiwan","Tanzania","Thailand","Tunisia","Turkey",
    "Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay",
    "Uzbekistan","Vietnam","Zimbabwe"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount      = (float)($_POST['amount'] ?? 0);
    $full_name   = trim(htmlspecialchars($_POST['full_name'] ?? ''));
    $wise_email  = trim($_POST['wise_email'] ?? '');
    $country     = htmlspecialchars($_POST['country'] ?? '');
    $currency    = htmlspecialchars($_POST['currency'] ?? 'USD');
    $acct_type   = htmlspecialchars($_POST['acct_type'] ?? 'Personal');
    $wise_id     = trim(htmlspecialchars($_POST['wise_id'] ?? ''));
    $memo        = trim(htmlspecialchars($_POST['memo'] ?? ''));
    $tx_pin      = $_POST['tx_pin'] ?? '';

    // Fetch sender
    $sender = $pdo->prepare("SELECT balance, pin FROM users WHERE id = ?");
    $sender->execute([$user_id]);
    $sender = $sender->fetch();

    if ($amount <= 0) {
        $error = 'Please enter a valid withdrawal amount.';
    } elseif ($amount < 10) {
        $error = 'Minimum Wise withdrawal is $10.00.';
    } elseif ($amount > $sender['balance']) {
        $error = 'Insufficient balance. Available: $' . number_format($sender['balance'], 2) . '.';
    } elseif (empty($full_name)) {
        $error = 'Please enter your full name as on your Wise account.';
    } elseif (empty($wise_email) || !filter_var($wise_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid Wise account email address.';
    } elseif (empty($country)) {
        $error = 'Please select your destination country.';
    } elseif (empty($tx_pin)) {
        $error = 'Transaction PIN is required.';
    } elseif ($tx_pin !== $sender['pin']) {
        $error = 'Incorrect transaction PIN. Please try again.';
    } else {
        $narration = "Wise Transfer to $wise_email ($full_name) | Country: $country | Currency: $currency | Account Type: $acct_type";
        if ($wise_id)  $narration .= " | Wise ID: $wise_id";
        if ($memo)     $narration .= " | Note: $memo";
        $tx_ref = 'SCW' . strtoupper(substr(md5(uniqid()), 0, 10));

        try {
            // PENDING — admin must approve before balance deduction
            $pdo->prepare("INSERT INTO transactions (user_id, amount, type, method, status, txn_hash, narration, created_at)
                           VALUES (?, ?, 'Debit', 'Wise Transfer', 'Pending', ?, ?, NOW())")
                ->execute([$user_id, $amount, $tx_ref, $narration]);
            $success = true;
        } catch (Exception $e) {
            $error = 'Failed to submit request. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wise Transfer Withdrawal - SwiftCapital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Wise brand colors: #9fe870 (lime green), #163300 (dark green) */
        :root { --wise-green: #9fe870; --wise-dark: #163300; --wise-mid: #2d5a00; }

        .wt-wrap { max-width: 1100px; margin: 0 auto; }

        /* Hero */
        .wt-hero {
            background: linear-gradient(135deg,#0d2200,#163300,#2d5a00);
            border-radius: 24px 24px 0 0;
            padding: 46px 54px 56px;
            color: #fff;
            text-align: center;
            position: relative; overflow: hidden;
        }
        .wt-hero .orb { position: absolute; border-radius: 50%; pointer-events: none; }
        .wt-hero-icon {
            width: 78px; height: 78px;
            background: rgba(159,232,112,.12);
            border: 1px solid rgba(159,232,112,.25);
            border-radius: 22px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2.2rem; color: #9fe870;
            margin-bottom: 18px;
        }
        .wt-hero h3 { font-weight: 900; letter-spacing: -1.5px; font-size: 2rem; margin-bottom: 8px; }
        .wt-hero p  { opacity: .76; font-size: .97rem; max-width: 500px; margin: 0 auto 22px; }
        .wt-hero-badges { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
        .wt-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(159,232,112,.1); border: 1px solid rgba(159,232,112,.2);
            border-radius: 50px; padding: 7px 16px;
            font-size: .78rem; font-weight: 700;
        }
        .wt-badge i { color: #9fe870; }

        /* Body */
        .wt-body {
            background: #fff; border: 1px solid #e5e7eb; border-top: none;
            border-radius: 0 0 24px 24px;
            padding: 48px 54px 54px;
            box-shadow: 0 20px 60px rgba(0,0,0,.05);
        }

        /* Balance strip */
        .wt-balance {
            background: linear-gradient(135deg,#163300,#2d5a00);
            border-radius: 16px; padding: 20px 26px;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 36px; color: #fff;
        }
        .wt-balance .lbl { font-size: .7rem; text-transform: uppercase; letter-spacing:.07em; opacity:.8; font-weight: 700; margin-bottom: 3px; }
        .wt-balance .val { font-size: 1.7rem; font-weight: 900; letter-spacing: -.5px; }

        /* Account type */
        .wt-type-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 12px; margin-bottom: 28px; }
        .wt-type-card {
            background: #f8fafc; border: 2px solid #e5e7eb;
            border-radius: 14px; padding: 16px 12px;
            text-align: center; cursor: pointer; transition: all .2s;
        }
        .wt-type-card:hover { border-color: #2d5a00; background: #f0fdf4; transform: translateY(-2px); }
        .wt-type-card.active { border-color: #2d5a00; background: #dcfce7; box-shadow: 0 6px 18px rgba(22,51,0,.1); }
        .wt-type-card i { display: block; font-size: 1.5rem; color: #2d5a00; margin-bottom: 6px; }
        .wt-type-card span { font-size: .8rem; font-weight: 800; color: #14532d; }
        .wt-type-card .sub { font-size: .7rem; font-weight: 600; color: #64748b; margin-top: 2px; }

        /* Section */
        .wt-sec {
            font-size: .68rem; font-weight: 900; text-transform: uppercase;
            letter-spacing: .12em; color: #64748b;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px; margin-bottom: 22px;
            display: flex; align-items: center; gap: 10px;
        }
        .wt-sec i { color: #2d5a00; }

        /* Amount */
        .wt-amt-box {
            background: #f8fafc; border: 2px solid #e5e7eb;
            border-radius: 20px; padding: 26px 30px; margin-bottom: 36px; transition: all .25s;
        }
        .wt-amt-box:focus-within { border-color: #2d5a00; background: #fff; box-shadow: 0 0 0 5px rgba(45,90,0,.06); }
        .wt-amt-flex { display: flex; align-items: center; gap: 10px; }
        .wt-sym { font-size: 2.4rem; font-weight: 900; color: #1a202c; }
        .wt-amt-inp {
            flex: 1; border: none; background: transparent;
            font-size: 2.8rem; font-weight: 900; color: #1a202c; outline: none; min-width: 0;
        }
        .wt-quick { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 16px; }
        .wt-qbtn {
            background: #fff; border: 1.5px solid #e5e7eb;
            border-radius: 10px; padding: 7px 16px;
            font-weight: 700; font-size: .85rem; color: #4a5568; cursor: pointer; transition: all .15s;
        }
        .wt-qbtn:hover, .wt-qbtn.active { border-color: #2d5a00; color: #163300; background: #dcfce7; }

        /* Fields */
        .wt-field { margin-bottom: 20px; }
        .wt-label { font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing:.05em; color: #64748b; margin-bottom: 8px; display: block; }
        .wt-inp-wrap { position: relative; }
        .wt-inp-wrap .ico { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
        .wt-inp {
            width: 100%; background: #f8fafc; border: 1.5px solid #e5e7eb;
            border-radius: 12px; padding: 13px 16px 13px 44px;
            font-size: .97rem; font-weight: 600; color: #1a202c; transition: all .2s;
        }
        .wt-inp:focus { background: #fff; border-color: #2d5a00; outline: none; box-shadow: 0 0 0 4px rgba(45,90,0,.07); }
        .wt-pin-toggle { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; }

        /* Review */
        .wt-review {
            background: #f8fafc; border: 1.5px solid #e5e7eb;
            border-radius: 18px; padding: 24px 28px; margin-bottom: 28px;
        }
        .wt-review-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #e5e7eb; font-size: .9rem; }
        .wt-review-row:last-child { border-bottom: none; }
        .wt-review-row .rk { color: #64748b; font-weight: 600; }
        .wt-review-row .rv { font-weight: 800; color: #1a202c; }

        /* Submit */
        .wt-submit {
            width: 100%; background: linear-gradient(135deg,#163300,#2d5a00);
            color: #9fe870; border: none; padding: 18px;
            border-radius: 14px; font-weight: 900; font-size: 1.05rem;
            box-shadow: 0 12px 28px rgba(22,51,0,.3);
            transition: all .3s; display: flex; align-items: center; justify-content: center; gap: 12px;
        }
        .wt-submit:hover { transform: translateY(-2px); box-shadow: 0 18px 40px rgba(22,51,0,.4); color: #fff; }
        .wt-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        /* Error */
        .wt-error {
            background: #fef2f2; border: 1px solid #fca5a5; border-radius: 14px;
            padding: 16px 20px; margin-bottom: 24px;
            display: flex; align-items: center; gap: 14px;
            font-weight: 700; color: #991b1b; font-size: .94rem;
        }
        .wt-error i { font-size: 1.2rem; flex-shrink: 0; }

        /* Success */
        .wt-success {
            text-align: center; padding: 60px 40px;
            background: #fff; border: 1px solid #e5e7eb; border-top: none;
            border-radius: 0 0 24px 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,.05);
        }
        .wt-success-icon {
            width: 96px; height: 96px; border-radius: 50%;
            background: linear-gradient(135deg,#163300,#2d5a00);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2.5rem; color: #9fe870;
            box-shadow: 0 16px 40px rgba(22,51,0,.35);
            margin-bottom: 24px;
            animation: pop .5s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes pop { from{transform:scale(.3);opacity:0}to{transform:scale(1);opacity:1} }

        /* Secure */
        .wt-secure {
            display: flex; align-items: center; gap: 16px;
            background: #f8fafc; border: 1px solid #e5e7eb;
            border-radius: 16px; padding: 18px 22px; margin-top: 28px;
        }
        .wt-secure i { color: #10b981; font-size: 1.4rem; flex-shrink: 0; }
        .wt-secure h6 { font-weight: 800; font-size: .9rem; margin-bottom: 3px; }
        .wt-secure p  { font-size: .8rem; color: #6b7280; margin: 0; }
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
        <div class="wt-wrap">

            <div class="page-header mb-4">
                <div>
                    <h1 class="page-title">Wise Transfer Withdrawal</h1>
                    <div class="breadcrumb-text">
                        <a href="index.php">Dashboard</a>
                        <i class="fa-solid fa-chevron-right mx-2" style="font-size:.7rem;"></i>
                        International
                        <i class="fa-solid fa-chevron-right mx-2" style="font-size:.7rem;"></i>
                        Wise Transfer
                    </div>
                </div>
            </div>

            <!-- Hero -->
            <div class="wt-hero shadow-lg">
                <div class="orb" style="top:-80px;right:-80px;width:280px;height:280px;background:radial-gradient(circle,rgba(159,232,112,.15) 0%,transparent 70%);"></div>
                <div class="orb" style="bottom:-60px;left:-60px;width:180px;height:180px;background:radial-gradient(circle,rgba(159,232,112,.1) 0%,transparent 70%);"></div>
                <div class="position-relative" style="z-index:2;">
                    <div class="wt-hero-icon"><i class="fa-solid fa-bolt"></i></div>
                    <h3>Wise Transfer Withdrawal</h3>
                    <p>Send funds globally with transparent fees and real mid-market exchange rates via Wise.</p>
                    <div class="wt-hero-badges">
                        <span class="wt-badge"><i class="fa-solid fa-shield-check"></i> Secure Transfer</span>
                        <span class="wt-badge"><i class="fa-solid fa-lock"></i> PIN Protected</span>
                        <span class="wt-badge"><i class="fa-solid fa-globe"></i> 80+ Countries</span>
                        <span class="wt-badge"><i class="fa-solid fa-clock"></i> 1–2 Business Days</span>
                    </div>
                </div>
            </div>

            <?php if ($success): ?>
            <!-- SUCCESS -->
            <div class="wt-success">
                <div class="wt-success-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                <h3 class="fw-900 mb-2" style="letter-spacing:-.5px;">Request Submitted!</h3>
                <p class="text-muted mb-1">Your Wise transfer is <strong>pending admin review</strong>.</p>
                <p class="text-muted mb-4" style="font-size:.85rem;">Your balance will be deducted once approved. Funds typically arrive in <strong>1–2 business days</strong> after approval.</p>

                <div class="d-inline-flex align-items-center gap-2 px-4 py-3 rounded-pill mb-5 fw-800" style="background:#dcfce7;color:#14532d;font-size:.9rem;">
                    <i class="fa-solid fa-clock"></i> Awaiting Admin Approval
                </div>

                <div class="wt-review text-start" style="max-width:480px;margin:0 auto 32px;">
                    <div class="wt-review-row" style="font-size:1rem;">
                        <span class="rk">Amount</span>
                        <span class="rv text-danger">−$<?php echo number_format($amount, 2); ?></span>
                    </div>
                    <div class="wt-review-row">
                        <span class="rk">Wise Account</span>
                        <span class="rv"><?php echo htmlspecialchars($wise_email); ?></span>
                    </div>
                    <div class="wt-review-row">
                        <span class="rk">Account Holder</span>
                        <span class="rv"><?php echo htmlspecialchars($full_name); ?></span>
                    </div>
                    <div class="wt-review-row">
                        <span class="rk">Account Type</span>
                        <span class="rv"><?php echo htmlspecialchars($acct_type); ?></span>
                    </div>
                    <div class="wt-review-row">
                        <span class="rk">Destination</span>
                        <span class="rv"><?php echo htmlspecialchars($country); ?></span>
                    </div>
                    <div class="wt-review-row">
                        <span class="rk">Currency</span>
                        <span class="rv"><?php echo htmlspecialchars($currency); ?></span>
                    </div>
                    <?php if (!empty($wise_id)): ?>
                    <div class="wt-review-row">
                        <span class="rk">Wise Account ID</span>
                        <span class="rv" style="font-family:monospace;"><?php echo htmlspecialchars($wise_id); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="wt-review-row">
                        <span class="rk">Reference</span>
                        <span class="rv" style="font-family:monospace;"><?php echo $tx_ref; ?></span>
                    </div>
                    <div class="wt-review-row">
                        <span class="rk">Status</span>
                        <span class="rv"><span class="badge fw-800" style="background:#dcfce7;color:#14532d;">Pending Review</span></span>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="international-wise.php" class="btn fw-800 px-4 py-3" style="border-radius:12px;background:#163300;color:#9fe870;">New Transfer</a>
                    <a href="transactions.php" class="btn btn-outline-secondary fw-700 px-4 py-3" style="border-radius:12px;">View Transactions</a>
                    <a href="index.php" class="btn btn-light fw-700 px-4 py-3" style="border-radius:12px;">Dashboard</a>
                </div>
            </div>

            <?php else: ?>
            <!-- FORM -->
            <div class="wt-body">

                <?php if ($error): ?>
                <div class="wt-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <!-- Balance -->
                <div class="wt-balance">
                    <div>
                        <div class="lbl">Available Balance</div>
                        <div class="val">$<?php echo number_format($_SESSION['balance'], 2); ?></div>
                    </div>
                    <div style="background:rgba(159,232,112,.15);border-radius:12px;padding:10px 18px;font-weight:800;font-size:.85rem;display:flex;align-items:center;gap:8px;color:#9fe870;">
                        <i class="fa-solid fa-bolt"></i> Wise Ready
                    </div>
                </div>

                <form method="POST" id="wiseForm" autocomplete="off">
                    <input type="hidden" name="acct_type" id="acctTypeInput" value="<?php echo isset($_POST['acct_type']) ? htmlspecialchars($_POST['acct_type']) : 'Personal'; ?>">

                    <!-- Account type -->
                    <div class="wt-sec"><i class="fa-solid fa-bolt"></i> Wise Account Type</div>
                    <div class="wt-type-grid mb-4">
                        <div class="wt-type-card <?php echo (!isset($_POST['acct_type']) || $_POST['acct_type']==='Personal') ? 'active' : ''; ?>"
                             onclick="selectType(this,'Personal')">
                            <i class="fa-solid fa-user"></i>
                            <span>Personal</span>
                            <div class="sub">For individual accounts</div>
                        </div>
                        <div class="wt-type-card <?php echo (isset($_POST['acct_type']) && $_POST['acct_type']==='Business') ? 'active' : ''; ?>"
                             onclick="selectType(this,'Business')">
                            <i class="fa-solid fa-briefcase"></i>
                            <span>Business</span>
                            <div class="sub">For business accounts</div>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="wt-sec"><i class="fa-solid fa-dollar-sign"></i> Withdrawal Amount</div>
                    <div class="wt-amt-box">
                        <div class="wt-amt-flex">
                            <span class="wt-sym">$</span>
                            <input type="number" name="amount" id="amtInput" class="wt-amt-inp"
                                   placeholder="0.00" step="0.01" min="10"
                                   value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>"
                                   required>
                        </div>
                        <div class="wt-quick">
                            <button type="button" class="wt-qbtn" onclick="setAmt(100)">$100</button>
                            <button type="button" class="wt-qbtn" onclick="setAmt(500)">$500</button>
                            <button type="button" class="wt-qbtn" onclick="setAmt(1000)">$1,000</button>
                            <button type="button" class="wt-qbtn" onclick="setAmt(5000)">$5,000</button>
                            <button type="button" class="wt-qbtn" onclick="setAmt(<?php echo floor($_SESSION['balance']); ?>)">Max</button>
                        </div>
                    </div>

                    <!-- Wise Details -->
                    <div class="wt-sec"><i class="fa-solid fa-envelope"></i> Wise Account Details</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="wt-field">
                                <label class="wt-label">Full Name (on Wise account) *</label>
                                <div class="wt-inp-wrap">
                                    <i class="ico fa-solid fa-user"></i>
                                    <input type="text" name="full_name" class="wt-inp"
                                           placeholder="Legal full name" required
                                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="wt-field">
                                <label class="wt-label">Wise Email Address *</label>
                                <div class="wt-inp-wrap">
                                    <i class="ico fa-solid fa-envelope"></i>
                                    <input type="email" name="wise_email" id="wiseEmail" class="wt-inp"
                                           placeholder="email@wise.com" required autocomplete="off"
                                           value="<?php echo isset($_POST['wise_email']) ? htmlspecialchars($_POST['wise_email']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="wt-field">
                                <label class="wt-label">Destination Country *</label>
                                <div class="wt-inp-wrap">
                                    <i class="ico fa-solid fa-earth-americas"></i>
                                    <select name="country" class="wt-inp" required style="appearance:none;padding-right:40px;">
                                        <option value="" disabled <?php echo !isset($_POST['country']) ? 'selected' : ''; ?>>Select Country</option>
                                        <?php foreach ($countries as $c): ?>
                                        <option value="<?php echo $c; ?>" <?php echo (isset($_POST['country']) && $_POST['country']==$c) ? 'selected' : ''; ?>><?php echo $c; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="wt-field">
                                <label class="wt-label">Payout Currency</label>
                                <div class="wt-inp-wrap">
                                    <i class="ico fa-solid fa-coins"></i>
                                    <select name="currency" class="wt-inp" style="appearance:none;padding-right:40px;">
                                        <?php
                                        $currencies = ['USD'=>'US Dollar','EUR'=>'Euro','GBP'=>'British Pound','CAD'=>'Canadian Dollar','AUD'=>'Australian Dollar','JPY'=>'Japanese Yen','CHF'=>'Swiss Franc','SGD'=>'Singapore Dollar','HKD'=>'Hong Kong Dollar','NOK'=>'Norwegian Krone','SEK'=>'Swedish Krona','DKK'=>'Danish Krone'];
                                        foreach ($currencies as $code => $name): $sel = (isset($_POST['currency']) && $_POST['currency']==$code) || (!isset($_POST['currency']) && $code==='USD'); ?>
                                        <option value="<?php echo $code; ?>" <?php echo $sel?'selected':''; ?>><?php echo "$code — $name"; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="wt-field">
                                <label class="wt-label">Wise Account ID <span class="text-muted fw-600">(optional)</span></label>
                                <div class="wt-inp-wrap">
                                    <i class="ico fa-solid fa-id-badge"></i>
                                    <input type="text" name="wise_id" class="wt-inp"
                                           placeholder="e.g. P12345678"
                                           value="<?php echo isset($_POST['wise_id']) ? htmlspecialchars($_POST['wise_id']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="wt-field">
                                <label class="wt-label">Transfer Note <span class="text-muted fw-600">(optional)</span></label>
                                <div class="wt-inp-wrap">
                                    <i class="ico fa-solid fa-note-sticky"></i>
                                    <input type="text" name="memo" class="wt-inp"
                                           placeholder="Purpose of withdrawal"
                                           value="<?php echo isset($_POST['memo']) ? htmlspecialchars($_POST['memo']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Auth -->
                    <div class="wt-sec"><i class="fa-solid fa-shield-keyhole"></i> Authentication</div>
                    <div class="row g-3 mb-5">
                        <div class="col-md-6">
                            <div class="wt-field">
                                <label class="wt-label">Transaction PIN *</label>
                                <div class="wt-inp-wrap" style="position:relative;">
                                    <i class="ico fa-solid fa-lock"></i>
                                    <input type="password" name="tx_pin" id="pinInp" class="wt-inp"
                                           placeholder="Your secure PIN" required autocomplete="new-password">
                                    <i class="fa-solid fa-eye wt-pin-toggle" onclick="togglePin(this)"></i>
                                </div>
                                <small class="text-muted mt-1 d-block fw-600" style="font-size:.77rem;">Required to authorize this withdrawal.</small>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="wt-review w-100" style="margin-bottom:0;">
                                <div class="wt-review-row"><span class="rk">Amount</span><span class="rv" id="rvAmt">—</span></div>
                                <div class="wt-review-row"><span class="rk">Transfer Fee</span><span class="rv text-success">Included</span></div>
                                <div class="wt-review-row"><span class="rk fw-800 text-dark">Total</span><span class="rv text-danger fw-900" id="rvTotal">—</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="row g-3">
                        <div class="col-md-8">
                            <button type="submit" id="submitBtn" class="wt-submit">
                                <i class="fa-solid fa-bolt"></i> Submit Wise Transfer
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="international.php" class="btn btn-light fw-700 w-100 py-3" style="border-radius:14px;">Cancel</a>
                        </div>
                    </div>

                </form>

                <div class="wt-secure">
                    <i class="fa-solid fa-shield-check"></i>
                    <div>
                        <h6>Bank-Grade Encryption</h6>
                        <p>All withdrawals are reviewed by our admin team before processing. Wise uses real mid-market exchange rates with transparent fees.</p>
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
    function selectType(el, type) {
        document.querySelectorAll('.wt-type-card').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('acctTypeInput').value = type;
    }

    function setAmt(v) {
        document.getElementById('amtInput').value = v.toFixed(2);
        document.querySelectorAll('.wt-qbtn').forEach(b => b.classList.remove('active'));
        event.currentTarget.classList.add('active');
        updateReview();
    }

    function updateReview() {
        const v = parseFloat(document.getElementById('amtInput').value) || 0;
        const fmt = v > 0 ? '$' + v.toLocaleString('en-US', {minimumFractionDigits:2}) : '—';
        document.getElementById('rvAmt').textContent   = fmt;
        document.getElementById('rvTotal').textContent = fmt;
    }
    document.getElementById('amtInput')?.addEventListener('input', updateReview);
    updateReview();

    // Email validation
    document.getElementById('wiseEmail')?.addEventListener('blur', function() {
        const v = this.value.trim();
        const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
        this.style.borderColor = v ? (ok ? '#2d5a00' : '#ef4444') : '#e5e7eb';
    });

    function togglePin(icon) {
        const inp = document.getElementById('pinInp');
        if (inp.type === 'password') { inp.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
        else { inp.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
    }

    document.getElementById('wiseForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
    });
</script>
</body>
</html>
