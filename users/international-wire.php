<?php
require_once '../includes/db.php';
require_once '../includes/user-check.php';

$user_id = $_SESSION['user_id'];
$error   = '';
$success = false;
$tx_ref  = '';
$amount  = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount       = (float)($_POST['amount'] ?? 0);
    $acct_name    = trim(htmlspecialchars($_POST['acct_name'] ?? ''));
    $acct_number  = trim(htmlspecialchars($_POST['acct_number'] ?? ''));
    $bank_name    = trim(htmlspecialchars($_POST['bank_name'] ?? ''));
    $bank_address = trim(htmlspecialchars($_POST['bank_address'] ?? ''));
    $acct_type    = htmlspecialchars($_POST['acct_type'] ?? 'Checking Account');
    $country      = htmlspecialchars($_POST['country'] ?? '');
    $swift        = strtoupper(trim(htmlspecialchars($_POST['swift'] ?? '')));
    $iban         = strtoupper(trim(htmlspecialchars($_POST['iban'] ?? '')));
    $memo         = trim(htmlspecialchars($_POST['memo'] ?? ''));
    $tx_pin       = $_POST['tx_pin'] ?? '';

    // Fetch sender
    $sender = $pdo->prepare("SELECT balance, pin, name, lastname, account_number FROM users WHERE id = ?");
    $sender->execute([$user_id]);
    $sender = $sender->fetch();

    if ($amount <= 0) {
        $error = 'Please enter a valid amount greater than $0.';
    } elseif ($amount > $sender['balance']) {
        $error = 'Insufficient balance. Available: $'.number_format($sender['balance'], 2).'.';
    } elseif (!$acct_name || !$acct_number || !$bank_name || !$country || !$swift) {
        $error = 'Please fill in all required beneficiary and transfer fields.';
    } elseif (strlen($swift) < 8 || strlen($swift) > 11) {
        $error = 'SWIFT/BIC code must be 8 or 11 characters.';
    } elseif (empty($tx_pin)) {
        $error = 'Transaction PIN is required.';
    } elseif ($tx_pin !== $sender['pin']) {
        $error = 'Incorrect transaction PIN. Please try again.';
    } else {
        $narration = "International Wire to $acct_name | $bank_name, $country | SWIFT: $swift | IBAN: $iban";
        if ($memo) $narration .= " | Note: $memo";
        $tx_ref = 'SCI' . strtoupper(substr(md5(uniqid()), 0, 10));

        try {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?")->execute([$amount, $user_id]);
            $pdo->prepare("INSERT INTO transactions (user_id, amount, type, method, status, txn_hash, narration, created_at)
                           VALUES (?, ?, 'Debit', 'International Wire', 'Completed', ?, ?, NOW())")
                ->execute([$user_id, $amount, $tx_ref, $narration]);
            $pdo->commit();
            $success = true;

            $new_bal = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
            $new_bal->execute([$user_id]);
            $_SESSION['balance'] = $new_bal->fetchColumn();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Transaction failed. Please try again.';
        }
    }
}

// Country list
$countries = [
    "Afghanistan","Albania","Algeria","Andorra","Angola","Argentina","Armenia","Australia",
    "Austria","Azerbaijan","Bahrain","Bangladesh","Belarus","Belgium","Bolivia","Bosnia",
    "Brazil","Bulgaria","Cambodia","Canada","Chile","China","Colombia","Croatia","Cuba",
    "Cyprus","Czech Republic","Denmark","Ecuador","Egypt","Estonia","Ethiopia","Finland",
    "France","Georgia","Germany","Ghana","Greece","Guatemala","Honduras","Hungary",
    "Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica",
    "Japan","Jordan","Kazakhstan","Kenya","Kuwait","Latvia","Lebanon","Libya","Lithuania",
    "Luxembourg","Malaysia","Malta","Mexico","Moldova","Mongolia","Morocco","Netherlands",
    "New Zealand","Nigeria","Norway","Pakistan","Panama","Peru","Philippines","Poland",
    "Portugal","Qatar","Romania","Russia","Saudi Arabia","Senegal","Serbia","Singapore",
    "Slovakia","Slovenia","South Africa","South Korea","Spain","Sri Lanka","Sudan","Sweden",
    "Switzerland","Syria","Taiwan","Tanzania","Thailand","Tunisia","Turkey","Uganda",
    "Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan",
    "Venezuela","Vietnam","Yemen","Zimbabwe"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>International Wire Transfer - SwiftCapital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* ── Layout ── */
        .iw-wrap { max-width: 1100px; margin: 0 auto; }

        /* ── Hero ── */
        .iw-hero {
            background: linear-gradient(135deg,#0a0a2e,#1a1060,#0d1b4b);
            border-radius: 24px 24px 0 0;
            padding: 50px 54px 60px;
            color: #fff;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .iw-hero .orb { position: absolute; border-radius: 50%; pointer-events: none; }
        .iw-hero-icon {
            width: 72px; height: 72px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 20px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #60a5fa;
            margin-bottom: 18px;
        }
        .iw-hero h3 { font-weight: 900; letter-spacing: -1.5px; font-size: 2rem; margin-bottom: 8px; }
        .iw-hero p { opacity: .75; font-size: .97rem; max-width: 520px; margin: 0 auto 20px; }
        .iw-hero-badges { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
        .iw-hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 50px; padding: 7px 16px;
            font-size: .78rem; font-weight: 700; letter-spacing: .03em;
        }
        .iw-hero-badge i { color: #60a5fa; }

        /* ── Body ── */
        .iw-body {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 24px 24px;
            padding: 50px 54px 54px;
            box-shadow: 0 20px 60px rgba(0,0,0,.05);
        }

        /* ── Balance strip ── */
        .iw-balance {
            background: linear-gradient(135deg,#eff6ff,#dbeafe);
            border: 1px solid #bfdbfe;
            border-radius: 16px;
            padding: 20px 26px;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 38px;
        }
        .iw-balance .lbl { font-size: .7rem; text-transform: uppercase; letter-spacing:.07em; color: #1d4ed8; font-weight: 700; margin-bottom: 3px; }
        .iw-balance .val { font-size: 1.7rem; font-weight: 900; color: #1e3a8a; letter-spacing: -.5px; }

        /* ── Fee info strip ── */
        .iw-fee-strip {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 14px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 36px;
            font-size: .88rem;
            font-weight: 600;
            color: #92400e;
        }
        .iw-fee-strip i { color: #f59e0b; flex-shrink: 0; }

        /* ── Section titles ── */
        .iw-sec {
            font-size: .68rem; font-weight: 900; text-transform: uppercase;
            letter-spacing: .12em; color: #64748b;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px; margin-bottom: 24px;
            display: flex; align-items: center; gap: 10px;
        }
        .iw-sec i { color: #3b82f6; font-size: .85rem; }

        /* ── Amount box ── */
        .iw-amt-box {
            background: #f8fafc;
            border: 2px solid #e5e7eb;
            border-radius: 20px;
            padding: 28px 32px;
            margin-bottom: 36px;
            transition: all .25s;
        }
        .iw-amt-box:focus-within {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 5px rgba(59,130,246,.07);
        }
        .iw-amt-flex { display: flex; align-items: center; gap: 10px; }
        .iw-amt-sym { font-size: 2.5rem; font-weight: 900; color: #1a202c; }
        .iw-amt-input {
            flex: 1; border: none; background: transparent;
            font-size: 2.8rem; font-weight: 900; color: #1a202c; outline: none; min-width: 0;
        }
        .iw-quick { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 16px; }
        .iw-qbtn {
            background: #fff; border: 1.5px solid #e5e7eb;
            border-radius: 10px; padding: 7px 16px;
            font-weight: 700; font-size: .85rem; color: #4a5568;
            cursor: pointer; transition: all .15s;
        }
        .iw-qbtn:hover, .iw-qbtn.active { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }

        /* ── Form fields ── */
        .iw-field { margin-bottom: 20px; }
        .iw-label { font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing:.05em; color: #64748b; margin-bottom: 8px; display: block; }
        .iw-inp-wrap { position: relative; }
        .iw-inp-wrap .ico { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
        .iw-inp-wrap .ico-top { position: absolute; left: 16px; top: 16px; color: #94a3b8; pointer-events: none; }
        .iw-inp {
            width: 100%; background: #f8fafc; border: 1.5px solid #e5e7eb;
            border-radius: 12px; padding: 13px 16px 13px 44px;
            font-size: .97rem; font-weight: 600; color: #1a202c; transition: all .2s;
        }
        .iw-inp:focus { background: #fff; border-color: #3b82f6; outline: none; box-shadow: 0 0 0 4px rgba(59,130,246,.08); }
        textarea.iw-inp { padding-top: 13px; padding-left: 44px; resize: none; }

        /* ── PIN field ── */
        .iw-pin-wrap { position: relative; }
        .iw-pin-toggle { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; }

        /* ── Review box ── */
        .iw-review {
            background: #f8fafc; border: 1.5px solid #e5e7eb;
            border-radius: 18px; padding: 26px 28px; margin-bottom: 30px;
        }
        .iw-review-row { display: flex; justify-content: space-between; align-items: center; padding: 11px 0; border-bottom: 1px dashed #e5e7eb; font-size: .9rem; }
        .iw-review-row:last-child { border-bottom: none; }
        .iw-review-row .rk { color: #64748b; font-weight: 600; }
        .iw-review-row .rv { font-weight: 800; color: #1a202c; }
        .iw-review-total { font-size: 1.05rem !important; }

        /* ── Submit ── */
        .iw-submit {
            width: 100%; background: linear-gradient(135deg,#1d4ed8,#2563eb);
            color: #fff; border: none; padding: 18px;
            border-radius: 14px; font-weight: 900; font-size: 1.1rem;
            box-shadow: 0 12px 30px rgba(29,78,216,.3);
            transition: all .3s; display: flex; align-items: center; justify-content: center; gap: 12px;
        }
        .iw-submit:hover { transform: translateY(-2px); box-shadow: 0 18px 40px rgba(29,78,216,.4); }
        .iw-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        /* ── Error ── */
        .iw-error {
            background: #fef2f2; border: 1px solid #fca5a5; border-radius: 14px;
            padding: 16px 20px; margin-bottom: 28px;
            display: flex; align-items: center; gap: 14px;
            font-weight: 700; color: #991b1b; font-size: .94rem;
        }
        .iw-error i { font-size: 1.2rem; flex-shrink: 0; }

        /* ── Success ── */
        .iw-success {
            text-align: center; padding: 60px 40px;
            background: #fff; border: 1px solid #e5e7eb;
            border-top: none; border-radius: 0 0 24px 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,.05);
        }
        .iw-success-icon {
            width: 96px; height: 96px; border-radius: 50%;
            background: linear-gradient(135deg,#1d4ed8,#3b82f6);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2.5rem; color: #fff;
            box-shadow: 0 16px 40px rgba(29,78,216,.35);
            margin-bottom: 24px;
            animation: pop .5s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes pop { from{transform:scale(.3);opacity:0}to{transform:scale(1);opacity:1} }

        /* ── Secure strip ── */
        .iw-secure {
            display: flex; align-items: center; gap: 16px;
            background: #f8fafc; border: 1px solid #e5e7eb;
            border-radius: 16px; padding: 18px 22px; margin-top: 28px;
        }
        .iw-secure i { color: #10b981; font-size: 1.4rem; flex-shrink: 0; }
        .iw-secure h6 { font-weight: 800; font-size: .9rem; margin-bottom: 3px; }
        .iw-secure p { font-size: .8rem; color: #6b7280; margin: 0; }

        /* ── SWIFT helper ── */
        .swift-valid { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: .8rem; font-weight: 800; display: none; }
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
        <div class="iw-wrap">

            <div class="page-header mb-4">
                <div>
                    <h1 class="page-title">International Wire Transfer</h1>
                    <div class="breadcrumb-text">
                        <a href="index.php">Dashboard</a>
                        <i class="fa-solid fa-chevron-right mx-2" style="font-size:.7rem;"></i>
                        International
                        <i class="fa-solid fa-chevron-right mx-2" style="font-size:.7rem;"></i>
                        Wire Transfer
                    </div>
                </div>
            </div>

            <!-- Hero -->
            <div class="iw-hero shadow-lg">
                <div class="orb" style="top:-100px;right:-80px;width:280px;height:280px;background:radial-gradient(circle,rgba(59,130,246,.2) 0%,transparent 70%);"></div>
                <div class="orb" style="bottom:-60px;left:-60px;width:200px;height:200px;background:radial-gradient(circle,rgba(99,102,241,.15) 0%,transparent 70%);"></div>
                <div class="position-relative" style="z-index:2;">
                    <div class="iw-hero-icon"><i class="fa-solid fa-earth-americas"></i></div>
                    <h3>International SWIFT Wire</h3>
                    <p>Send funds globally via the SWIFT network to any bank account worldwide with bank-grade security.</p>
                    <div class="iw-hero-badges">
                        <span class="iw-hero-badge"><i class="fa-solid fa-shield-check"></i> SWIFT Network</span>
                        <span class="iw-hero-badge"><i class="fa-solid fa-lock"></i> 256-Bit Encrypted</span>
                        <span class="iw-hero-badge"><i class="fa-solid fa-clock"></i> 24–72 Hour Delivery</span>
                        <span class="iw-hero-badge"><i class="fa-solid fa-globe"></i> 180+ Countries</span>
                    </div>
                </div>
            </div>

            <?php if ($success): ?>
            <!-- SUCCESS STATE -->
            <div class="iw-success">
                <div class="iw-success-icon"><i class="fa-solid fa-paper-plane"></i></div>
                <h3 class="fw-900 mb-2" style="letter-spacing:-.5px;">Wire Initiated!</h3>
                <p class="text-muted mb-1">Your international wire transfer has been dispatched via the SWIFT network.</p>
                <p class="text-muted mb-5" style="font-size:.85rem;">Estimated delivery: <strong>24–72 business hours</strong></p>

                <div class="iw-review text-start" style="max-width:530px;margin:0 auto 34px;">
                    <div class="iw-review-row iw-review-total">
                        <span class="rk">Amount Sent</span>
                        <span class="rv text-danger">−$<?php echo number_format($amount, 2); ?></span>
                    </div>
                    <div class="iw-review-row">
                        <span class="rk">Beneficiary</span>
                        <span class="rv"><?php echo htmlspecialchars($acct_name); ?></span>
                    </div>
                    <div class="iw-review-row">
                        <span class="rk">Account No.</span>
                        <span class="rv" style="font-family:monospace;"><?php echo htmlspecialchars($acct_number); ?></span>
                    </div>
                    <div class="iw-review-row">
                        <span class="rk">Bank</span>
                        <span class="rv"><?php echo htmlspecialchars($bank_name); ?></span>
                    </div>
                    <div class="iw-review-row">
                        <span class="rk">Destination</span>
                        <span class="rv"><?php echo htmlspecialchars($country); ?></span>
                    </div>
                    <div class="iw-review-row">
                        <span class="rk">SWIFT/BIC</span>
                        <span class="rv" style="font-family:monospace;"><?php echo htmlspecialchars($swift); ?></span>
                    </div>
                    <?php if ($iban): ?>
                    <div class="iw-review-row">
                        <span class="rk">IBAN</span>
                        <span class="rv" style="font-family:monospace;"><?php echo htmlspecialchars($iban); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="iw-review-row">
                        <span class="rk">Reference</span>
                        <span class="rv" style="font-family:monospace;"><?php echo $tx_ref; ?></span>
                    </div>
                    <div class="iw-review-row">
                        <span class="rk">New Balance</span>
                        <span class="rv text-success">$<?php echo number_format($_SESSION['balance'], 2); ?></span>
                    </div>
                    <?php if ($memo): ?>
                    <div class="iw-review-row">
                        <span class="rk">Memo</span>
                        <span class="rv"><?php echo htmlspecialchars($memo); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="international-wire.php" class="btn btn-primary fw-800 px-4 py-3" style="border-radius:12px;">New Wire</a>
                    <a href="transactions.php" class="btn btn-outline-secondary fw-700 px-4 py-3" style="border-radius:12px;">View Transactions</a>
                    <a href="index.php" class="btn btn-light fw-700 px-4 py-3" style="border-radius:12px;">Dashboard</a>
                </div>
            </div>

            <?php else: ?>
            <!-- TRANSFER FORM -->
            <div class="iw-body">

                <?php if ($error): ?>
                <div class="iw-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <!-- Balance strip -->
                <div class="iw-balance">
                    <div>
                        <div class="lbl">Available Balance</div>
                        <div class="val">$<?php echo number_format($_SESSION['balance'], 2); ?></div>
                    </div>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <span style="background:rgba(29,78,216,.1);color:#1d4ed8;border-radius:10px;padding:8px 16px;font-weight:800;font-size:.78rem;display:flex;align-items:center;gap:6px;">
                            <i class="fa-solid fa-circle" style="font-size:.4rem;"></i> SWIFT Ready
                        </span>
                        <span style="background:rgba(16,185,129,.1);color:#10b981;border-radius:10px;padding:8px 16px;font-weight:800;font-size:.78rem;display:flex;align-items:center;gap:6px;">
                            <i class="fa-solid fa-globe"></i> 180+ Countries
                        </span>
                    </div>
                </div>

                <!-- Fee notice -->
                <div class="iw-fee-strip">
                    <i class="fa-solid fa-circle-info"></i>
                    International wire transfers are processed via the SWIFT network. Delivery typically takes <strong class="ms-1 me-1">1–3 business days</strong>. Correspondent bank fees may apply on the recipient's end.
                </div>

                <form method="POST" id="wireForm" autocomplete="off">

                    <!-- Amount -->
                    <div class="iw-sec"><i class="fa-solid fa-dollar-sign"></i> Transfer Amount</div>
                    <div class="iw-amt-box">
                        <div class="iw-amt-flex">
                            <span class="iw-amt-sym">$</span>
                            <input type="number" name="amount" id="amtInput" class="iw-amt-input"
                                   placeholder="0.00" step="0.01" min="1"
                                   value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>"
                                   required>
                        </div>
                        <div class="iw-quick">
                            <button type="button" class="iw-qbtn" onclick="setAmt(500)">$500</button>
                            <button type="button" class="iw-qbtn" onclick="setAmt(1000)">$1,000</button>
                            <button type="button" class="iw-qbtn" onclick="setAmt(5000)">$5,000</button>
                            <button type="button" class="iw-qbtn" onclick="setAmt(10000)">$10,000</button>
                            <button type="button" class="iw-qbtn" onclick="setAmt(<?php echo floor($_SESSION['balance']); ?>)">Max</button>
                        </div>
                    </div>

                    <!-- Beneficiary -->
                    <div class="iw-sec"><i class="fa-solid fa-user-tag"></i> Beneficiary Details</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="iw-field">
                                <label class="iw-label">Beneficiary Full Name *</label>
                                <div class="iw-inp-wrap">
                                    <i class="ico fa-solid fa-user"></i>
                                    <input type="text" name="acct_name" class="iw-inp" placeholder="Legal full name" required
                                           value="<?php echo isset($_POST['acct_name']) ? htmlspecialchars($_POST['acct_name']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="iw-field">
                                <label class="iw-label">Account Number *</label>
                                <div class="iw-inp-wrap">
                                    <i class="ico fa-solid fa-hashtag"></i>
                                    <input type="text" name="acct_number" class="iw-inp" placeholder="Recipient's account number" required
                                           value="<?php echo isset($_POST['acct_number']) ? htmlspecialchars($_POST['acct_number']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="iw-field">
                                <label class="iw-label">Receiving Bank Name *</label>
                                <div class="iw-inp-wrap">
                                    <i class="ico fa-solid fa-building-columns"></i>
                                    <input type="text" name="bank_name" class="iw-inp" placeholder="e.g. Deutsche Bank" required
                                           value="<?php echo isset($_POST['bank_name']) ? htmlspecialchars($_POST['bank_name']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="iw-field">
                                <label class="iw-label">Bank Branch Address</label>
                                <div class="iw-inp-wrap">
                                    <i class="ico fa-solid fa-location-dot"></i>
                                    <input type="text" name="bank_address" class="iw-inp" placeholder="Branch city or address"
                                           value="<?php echo isset($_POST['bank_address']) ? htmlspecialchars($_POST['bank_address']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Parameters -->
                    <div class="iw-sec"><i class="fa-solid fa-globe"></i> Transfer Parameters</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="iw-field">
                                <label class="iw-label">Account Type *</label>
                                <div class="iw-inp-wrap">
                                    <i class="ico fa-solid fa-list-ul"></i>
                                    <select name="acct_type" class="iw-inp" style="appearance:none;padding-right:40px;">
                                        <option>Checking Account</option>
                                        <option>Savings Account</option>
                                        <option>Business Account</option>
                                        <option>Investment Account</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="iw-field">
                                <label class="iw-label">Destination Country *</label>
                                <div class="iw-inp-wrap">
                                    <i class="ico fa-solid fa-earth-americas"></i>
                                    <select name="country" class="iw-inp" required style="appearance:none;padding-right:40px;">
                                        <option value="" disabled <?php echo !isset($_POST['country'])?'selected':''; ?>>Select Country</option>
                                        <?php foreach ($countries as $c): ?>
                                        <option value="<?php echo $c; ?>" <?php echo (isset($_POST['country']) && $_POST['country']==$c)?'selected':''; ?>><?php echo $c; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="iw-field">
                                <label class="iw-label">SWIFT / BIC Code *</label>
                                <div class="iw-inp-wrap" style="position:relative;">
                                    <i class="ico fa-solid fa-code"></i>
                                    <input type="text" name="swift" id="swiftInput" class="iw-inp" placeholder="e.g. DEUTDEDB" required
                                           maxlength="11" style="text-transform:uppercase;letter-spacing:1px;"
                                           value="<?php echo isset($_POST['swift']) ? htmlspecialchars($_POST['swift']) : ''; ?>">
                                    <span class="swift-valid text-success" id="swiftOk"><i class="fa-solid fa-circle-check"></i></span>
                                    <span class="swift-valid text-danger" id="swiftBad"><i class="fa-solid fa-circle-xmark"></i></span>
                                </div>
                                <small class="text-muted mt-1 d-block fw-600" style="font-size:.75rem;">8 or 11 characters</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="iw-field">
                                <label class="iw-label">IBAN Number <span class="text-muted fw-600">(if applicable)</span></label>
                                <div class="iw-inp-wrap">
                                    <i class="ico fa-solid fa-id-card"></i>
                                    <input type="text" name="iban" class="iw-inp" placeholder="International Bank Account Number"
                                           style="text-transform:uppercase;letter-spacing:1px;"
                                           value="<?php echo isset($_POST['iban']) ? htmlspecialchars($_POST['iban']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Auth & Memo -->
                    <div class="iw-sec"><i class="fa-solid fa-shield-keyhole"></i> Authentication & Memo</div>
                    <div class="row g-3 mb-5">
                        <div class="col-md-6">
                            <div class="iw-field">
                                <label class="iw-label">Transaction PIN *</label>
                                <div class="iw-inp-wrap iw-pin-wrap">
                                    <i class="ico fa-solid fa-lock"></i>
                                    <input type="password" name="tx_pin" id="pinInp" class="iw-inp" placeholder="Your transaction PIN" required autocomplete="new-password">
                                    <i class="fa-solid fa-eye iw-pin-toggle" onclick="togglePin(this)"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="iw-field">
                                <label class="iw-label">Transfer Memo <span class="text-muted fw-600">(optional)</span></label>
                                <div class="iw-inp-wrap">
                                    <i class="ico fa-solid fa-note-sticky"></i>
                                    <input type="text" name="memo" class="iw-inp" placeholder="Purpose of transfer"
                                           value="<?php echo isset($_POST['memo']) ? htmlspecialchars($_POST['memo']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Review Card -->
                    <div class="iw-review">
                        <h6 class="fw-900 mb-3" style="font-size:.85rem; text-transform:uppercase; letter-spacing:.06em; color:#64748b;">Transfer Summary</h6>
                        <div class="iw-review-row"><span class="rk">Amount</span><span class="rv" id="rvAmt">—</span></div>
                        <div class="iw-review-row"><span class="rk">SWIFT Fee</span><span class="rv text-success">Included</span></div>
                        <div class="iw-review-row"><span class="rk">Estimated Delivery</span><span class="rv">1–3 Business Days</span></div>
                        <div class="iw-review-row iw-review-total">
                            <span class="rk fw-800 text-dark">Total Deducted</span>
                            <span class="rv text-danger fw-900" id="rvTotal">—</span>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="row g-3">
                        <div class="col-md-8">
                            <button type="submit" id="submitBtn" class="iw-submit">
                                <i class="fa-solid fa-paper-plane"></i> Initialize Wire Transfer
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="international.php" class="btn btn-light fw-700 w-100 py-3" style="border-radius:14px;">Cancel</a>
                        </div>
                    </div>

                </form>

                <!-- Secure strip -->
                <div class="iw-secure">
                    <i class="fa-solid fa-shield-check"></i>
                    <div>
                        <h6>SwiftCapital Grade-A Security</h6>
                        <p>Your wire transfer is protected by end-to-end 256-bit AES encryption. All transfers are processed via authenticated SWIFT channels.</p>
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
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Contact Support</a>
        </div>
    </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Quick amounts
    function setAmt(v) {
        document.getElementById('amtInput').value = v.toFixed(2);
        document.querySelectorAll('.iw-qbtn').forEach(b => b.classList.remove('active'));
        event.currentTarget.classList.add('active');
        updateReview();
    }

    // Review summary
    function updateReview() {
        const v = parseFloat(document.getElementById('amtInput').value) || 0;
        const fmt = v > 0 ? '$' + v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}) : '—';
        document.getElementById('rvAmt').textContent   = fmt;
        document.getElementById('rvTotal').textContent = fmt;
    }
    document.getElementById('amtInput')?.addEventListener('input', updateReview);
    updateReview();

    // SWIFT validation
    document.getElementById('swiftInput')?.addEventListener('input', function() {
        const v = this.value.trim();
        const ok = (v.length === 8 || v.length === 11) && /^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/i.test(v);
        document.getElementById('swiftOk').style.display = ok ? 'block' : 'none';
        document.getElementById('swiftBad').style.display = (!ok && v.length > 0) ? 'block' : 'none';
    });

    // PIN toggle
    function togglePin(icon) {
        const inp = document.getElementById('pinInp');
        if (inp.type === 'password') { inp.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
        else { inp.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
    }

    // Submit loader
    document.getElementById('wireForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing Wire...';
    });
</script>
</body>
</html>
