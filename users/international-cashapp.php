<?php
require_once '../includes/db.php';
require_once '../includes/user-check.php';

$user_id = $_SESSION['user_id'];
$error   = '';
$success = false;
$amount  = 0;
$tx_ref  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount    = (float)($_POST['amount'] ?? 0);
    $cashtag   = trim(htmlspecialchars($_POST['cashtag'] ?? ''));
    $full_name = trim(htmlspecialchars($_POST['full_name'] ?? ''));
    $ca_email  = trim($_POST['ca_email'] ?? '');
    $ca_phone  = trim(htmlspecialchars($_POST['ca_phone'] ?? ''));
    $memo      = trim(htmlspecialchars($_POST['memo'] ?? ''));
    $tx_pin    = $_POST['tx_pin'] ?? '';

    // Fetch sender
    $sender = $pdo->prepare("SELECT balance, pin FROM users WHERE id = ?");
    $sender->execute([$user_id]);
    $sender = $sender->fetch();

    // Validate
    if ($amount <= 0) {
        $error = 'Please enter a valid withdrawal amount.';
    } elseif ($amount < 1) {
        $error = 'Minimum Cash App withdrawal is $1.00.';
    } elseif ($amount > 2000) {
        $error = 'Cash App maximum single transaction is $2,000.00.';
    } elseif ($amount > $sender['balance']) {
        $error = 'Insufficient balance. Available: $' . number_format($sender['balance'], 2) . '.';
    } elseif (empty($cashtag)) {
        $error = 'Please enter the recipient\'s $Cashtag.';
    } elseif (!preg_match('/^\$[a-zA-Z0-9_\-]{1,20}$/', $cashtag)) {
        $error = 'Invalid $Cashtag format. Must start with $ and contain only letters, numbers, underscores, or dashes (max 20 chars).';
    } elseif (empty($full_name)) {
        $error = 'Please enter the account holder\'s full name.';
    } elseif (empty($tx_pin)) {
        $error = 'Transaction PIN is required.';
    } elseif ($tx_pin !== $sender['pin']) {
        $error = 'Incorrect transaction PIN. Please try again.';
    } else {
        $narration = "Cash App Withdrawal to $cashtag ($full_name)";
        if ($ca_email) $narration .= " | Email: $ca_email";
        if ($ca_phone) $narration .= " | Phone: $ca_phone";
        if ($memo)     $narration .= " | Note: $memo";
        $tx_ref = 'SCC' . strtoupper(substr(md5(uniqid()), 0, 10));

        try {
            // PENDING — admin must approve before balance deduction
            $pdo->prepare("INSERT INTO transactions (user_id, amount, type, method, status, txn_hash, narration, created_at)
                           VALUES (?, ?, 'Debit', 'Cash App', 'Pending', ?, ?, NOW())")
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
    <title>Cash App Withdrawal - SwiftCapital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Cash App brand: #00d632 (lime green), #0a0a0a (black) */
        .ca-wrap { max-width: 1100px; margin: 0 auto; }

        /* Hero */
        .ca-hero {
            background: linear-gradient(135deg,#050505,#0a0a0a,#0d1f0d);
            border-radius: 24px 24px 0 0;
            padding: 46px 54px 56px;
            color: #fff;
            text-align: center;
            position: relative; overflow: hidden;
        }
        .ca-hero .orb { position: absolute; border-radius: 50%; pointer-events: none; }
        .ca-hero-icon {
            width: 78px; height: 78px;
            background: rgba(0,214,50,.12);
            border: 1px solid rgba(0,214,50,.25);
            border-radius: 22px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2.2rem; color: #00d632;
            margin-bottom: 18px;
        }
        .ca-hero h3 { font-weight: 900; letter-spacing: -1.5px; font-size: 2rem; margin-bottom: 8px; }
        .ca-hero p  { opacity: .72; font-size: .97rem; max-width: 500px; margin: 0 auto 22px; }
        .ca-hero-badges { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
        .ca-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(0,214,50,.1); border: 1px solid rgba(0,214,50,.2);
            border-radius: 50px; padding: 7px 16px;
            font-size: .78rem; font-weight: 700;
        }
        .ca-badge i { color: #00d632; }

        /* Body */
        .ca-body {
            background: #fff; border: 1px solid #e5e7eb; border-top: none;
            border-radius: 0 0 24px 24px;
            padding: 48px 54px 54px;
            box-shadow: 0 20px 60px rgba(0,0,0,.05);
        }

        /* Balance strip */
        .ca-balance {
            background: linear-gradient(135deg,#050505,#0a2e0a);
            border-radius: 16px; padding: 20px 26px;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 36px; color: #fff;
        }
        .ca-balance .lbl { font-size: .7rem; text-transform: uppercase; letter-spacing:.07em; opacity:.75; font-weight: 700; margin-bottom: 3px; }
        .ca-balance .val { font-size: 1.7rem; font-weight: 900; letter-spacing: -.5px; }

        /* Limit notice */
        .ca-limit {
            background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px;
            padding: 14px 20px; margin-bottom: 32px;
            display: flex; align-items: flex-start; gap: 12px;
            font-size: .88rem; font-weight: 600; color: #14532d;
        }
        .ca-limit i { color: #00d632; flex-shrink: 0; margin-top: 2px; font-size: 1rem; }

        /* Section */
        .ca-sec {
            font-size: .68rem; font-weight: 900; text-transform: uppercase;
            letter-spacing: .12em; color: #64748b;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px; margin-bottom: 22px;
            display: flex; align-items: center; gap: 10px;
        }
        .ca-sec i { color: #00d632; }

        /* Cashtag indicator */
        .ca-cashtag-preview {
            background: #0a0a0a; color: #00d632;
            border-radius: 12px; padding: 16px 22px;
            font-size: 1.6rem; font-weight: 900; letter-spacing: -.5px;
            text-align: center; margin-bottom: 20px;
            min-height: 64px; display: flex; align-items: center; justify-content: center;
            transition: all .2s;
        }

        /* Amount */
        .ca-amt-box {
            background: #f8fafc; border: 2px solid #e5e7eb;
            border-radius: 20px; padding: 26px 30px; margin-bottom: 36px; transition: all .25s;
        }
        .ca-amt-box:focus-within { border-color: #00d632; background: #fff; box-shadow: 0 0 0 5px rgba(0,214,50,.06); }
        .ca-amt-flex { display: flex; align-items: center; gap: 10px; }
        .ca-sym { font-size: 2.4rem; font-weight: 900; color: #1a202c; }
        .ca-amt-inp {
            flex: 1; border: none; background: transparent;
            font-size: 2.8rem; font-weight: 900; color: #1a202c; outline: none; min-width: 0;
        }
        .ca-quick { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 16px; }
        .ca-qbtn {
            background: #fff; border: 1.5px solid #e5e7eb;
            border-radius: 10px; padding: 7px 16px;
            font-weight: 700; font-size: .85rem; color: #4a5568; cursor: pointer; transition: all .15s;
        }
        .ca-qbtn:hover, .ca-qbtn.active { border-color: #00d632; color: #0a0a0a; background: #f0fdf4; }

        /* Fields */
        .ca-field { margin-bottom: 20px; }
        .ca-label { font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing:.05em; color: #64748b; margin-bottom: 8px; display: block; }
        .ca-inp-wrap { position: relative; }
        .ca-inp-wrap .ico { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
        .ca-inp {
            width: 100%; background: #f8fafc; border: 1.5px solid #e5e7eb;
            border-radius: 12px; padding: 13px 16px 13px 44px;
            font-size: .97rem; font-weight: 600; color: #1a202c; transition: all .2s;
        }
        .ca-inp:focus { background: #fff; border-color: #00d632; outline: none; box-shadow: 0 0 0 4px rgba(0,214,50,.07); }
        .ca-pin-toggle { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; }

        /* Cashtag field special */
        .ca-inp.cashtag-inp { font-family: monospace; font-weight: 900; font-size: 1.05rem; color: #0a0a0a; letter-spacing: .02em; }
        .ca-inp.cashtag-inp:focus { border-color: #00d632; }

        /* Review */
        .ca-review {
            background: #f8fafc; border: 1.5px solid #e5e7eb;
            border-radius: 18px; padding: 24px 28px; margin-bottom: 28px;
        }
        .ca-review-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #e5e7eb; font-size: .9rem; }
        .ca-review-row:last-child { border-bottom: none; }
        .ca-review-row .rk { color: #64748b; font-weight: 600; }
        .ca-review-row .rv { font-weight: 800; color: #1a202c; }

        /* Submit */
        .ca-submit {
            width: 100%; background: #00d632;
            color: #0a0a0a; border: none; padding: 18px;
            border-radius: 14px; font-weight: 900; font-size: 1.05rem;
            box-shadow: 0 12px 28px rgba(0,214,50,.3);
            transition: all .3s; display: flex; align-items: center; justify-content: center; gap: 12px;
        }
        .ca-submit:hover { transform: translateY(-2px); box-shadow: 0 18px 40px rgba(0,214,50,.4); background: #00f03a; }
        .ca-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        /* Error */
        .ca-error {
            background: #fef2f2; border: 1px solid #fca5a5; border-radius: 14px;
            padding: 16px 20px; margin-bottom: 24px;
            display: flex; align-items: center; gap: 14px;
            font-weight: 700; color: #991b1b; font-size: .94rem;
        }
        .ca-error i { font-size: 1.2rem; flex-shrink: 0; }

        /* Success */
        .ca-success {
            text-align: center; padding: 60px 40px;
            background: #fff; border: 1px solid #e5e7eb; border-top: none;
            border-radius: 0 0 24px 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,.05);
        }
        .ca-success-icon {
            width: 96px; height: 96px; border-radius: 50%;
            background: #0a0a0a;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2.5rem; color: #00d632;
            box-shadow: 0 16px 40px rgba(0,0,0,.3);
            margin-bottom: 24px;
            animation: pop .5s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes pop { from{transform:scale(.3);opacity:0}to{transform:scale(1);opacity:1} }

        /* Secure */
        .ca-secure {
            display: flex; align-items: center; gap: 16px;
            background: #f8fafc; border: 1px solid #e5e7eb;
            border-radius: 16px; padding: 18px 22px; margin-top: 28px;
        }
        .ca-secure i { color: #10b981; font-size: 1.4rem; flex-shrink: 0; }
        .ca-secure h6 { font-weight: 800; font-size: .9rem; margin-bottom: 3px; }
        .ca-secure p  { font-size: .8rem; color: #6b7280; margin: 0; }
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
        <div class="ca-wrap">

            <div class="page-header mb-4">
                <div>
                    <h1 class="page-title">Cash App Withdrawal</h1>
                    <div class="breadcrumb-text">
                        <a href="index.php">Dashboard</a>
                        <i class="fa-solid fa-chevron-right mx-2" style="font-size:.7rem;"></i>
                        International
                        <i class="fa-solid fa-chevron-right mx-2" style="font-size:.7rem;"></i>
                        Cash App
                    </div>
                </div>
            </div>

            <!-- Hero -->
            <div class="ca-hero shadow-lg">
                <div class="orb" style="top:-80px;right:-80px;width:280px;height:280px;background:radial-gradient(circle,rgba(0,214,50,.18) 0%,transparent 70%);"></div>
                <div class="orb" style="bottom:-60px;left:-60px;width:200px;height:200px;background:radial-gradient(circle,rgba(0,214,50,.1) 0%,transparent 70%);"></div>
                <div class="position-relative" style="z-index:2;">
                    <div class="ca-hero-icon"><i class="fa-solid fa-dollar-sign"></i></div>
                    <h3>Cash App Withdrawal</h3>
                    <p>Send money instantly to any $Cashtag. Fast, simple, and secure — powered by Cash App.</p>
                    <div class="ca-hero-badges">
                        <span class="ca-badge"><i class="fa-solid fa-shield-check"></i> Encrypted</span>
                        <span class="ca-badge"><i class="fa-solid fa-lock"></i> PIN Protected</span>
                        <span class="ca-badge"><i class="fa-solid fa-bolt"></i> Instant to $Cashtag</span>
                        <span class="ca-badge"><i class="fa-solid fa-user-shield"></i> Admin Reviewed</span>
                    </div>
                </div>
            </div>

            <?php if ($success): ?>
            <!-- SUCCESS -->
            <div class="ca-success">
                <div class="ca-success-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                <h3 class="fw-900 mb-2" style="letter-spacing:-.5px;">Request Submitted!</h3>
                <p class="text-muted mb-1">Your Cash App withdrawal is <strong>pending admin review</strong>.</p>
                <p class="text-muted mb-4" style="font-size:.85rem;">Your balance will be deducted once approved. Funds are typically dispatched within <strong>24 hours</strong> of approval.</p>

                <div class="d-inline-flex align-items-center gap-2 px-4 py-3 rounded-pill mb-5 fw-800" style="background:#f0fdf4;color:#14532d;font-size:.9rem;border:1.5px solid #bbf7d0;">
                    <i class="fa-solid fa-clock"></i> Awaiting Admin Approval
                </div>

                <div class="ca-review text-start" style="max-width:480px;margin:0 auto 32px;">
                    <div class="ca-review-row" style="font-size:1rem;">
                        <span class="rk">Amount</span>
                        <span class="rv text-danger">−$<?php echo number_format($amount, 2); ?></span>
                    </div>
                    <div class="ca-review-row">
                        <span class="rk">$Cashtag</span>
                        <span class="rv" style="font-family:monospace;color:#00d632;font-size:1.05rem;"><?php echo htmlspecialchars($cashtag); ?></span>
                    </div>
                    <div class="ca-review-row">
                        <span class="rk">Account Holder</span>
                        <span class="rv"><?php echo htmlspecialchars($full_name); ?></span>
                    </div>
                    <?php if (!empty($ca_email)): ?>
                    <div class="ca-review-row">
                        <span class="rk">Email</span>
                        <span class="rv"><?php echo htmlspecialchars($ca_email); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($ca_phone)): ?>
                    <div class="ca-review-row">
                        <span class="rk">Phone</span>
                        <span class="rv"><?php echo htmlspecialchars($ca_phone); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="ca-review-row">
                        <span class="rk">Reference</span>
                        <span class="rv" style="font-family:monospace;"><?php echo $tx_ref; ?></span>
                    </div>
                    <div class="ca-review-row">
                        <span class="rk">Status</span>
                        <span class="rv"><span class="badge fw-800" style="background:#f0fdf4;color:#14532d;border:1px solid #bbf7d0;">Pending Review</span></span>
                    </div>
                    <?php if (!empty($memo)): ?>
                    <div class="ca-review-row">
                        <span class="rk">Note</span>
                        <span class="rv"><?php echo htmlspecialchars($memo); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="international-cashapp.php" class="btn fw-800 px-4 py-3" style="border-radius:12px;background:#00d632;color:#0a0a0a;">New Withdrawal</a>
                    <a href="transactions.php" class="btn btn-outline-secondary fw-700 px-4 py-3" style="border-radius:12px;">View Transactions</a>
                    <a href="index.php" class="btn btn-light fw-700 px-4 py-3" style="border-radius:12px;">Dashboard</a>
                </div>
            </div>

            <?php else: ?>
            <!-- FORM -->
            <div class="ca-body">

                <?php if ($error): ?>
                <div class="ca-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <!-- Balance -->
                <div class="ca-balance">
                    <div>
                        <div class="lbl">Available Balance</div>
                        <div class="val">$<?php echo number_format($_SESSION['balance'], 2); ?></div>
                    </div>
                    <div style="background:rgba(0,214,50,.15);border-radius:12px;padding:10px 18px;font-weight:800;font-size:.85rem;display:flex;align-items:center;gap:8px;color:#00d632;">
                        <i class="fa-solid fa-dollar-sign"></i> Cash App Ready
                    </div>
                </div>

                <!-- Limit info -->
                <div class="ca-limit">
                    <i class="fa-solid fa-circle-info"></i>
                    <div>Cash App limit is <strong>$2,000 per transaction</strong>. All withdrawals require admin review before funds are dispatched to the $Cashtag recipient.</div>
                </div>

                <form method="POST" id="cashForm" autocomplete="off">

                    <!-- $Cashtag section -->
                    <div class="ca-sec"><i class="fa-solid fa-at"></i> Recipient $Cashtag</div>

                    <!-- Live cashtag preview -->
                    <div class="ca-cashtag-preview" id="cashtag-preview">$YourCashtag</div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="ca-field">
                                <label class="ca-label">$Cashtag *</label>
                                <div class="ca-inp-wrap">
                                    <i class="ico fa-solid fa-dollar-sign" style="color:#00d632;"></i>
                                    <input type="text" name="cashtag" id="cashtagInp" class="ca-inp cashtag-inp"
                                           placeholder="$Cashtag" required autocomplete="off"
                                           value="<?php echo isset($_POST['cashtag']) ? htmlspecialchars($_POST['cashtag']) : ''; ?>">
                                </div>
                                <small class="text-muted mt-1 d-block fw-600" style="font-size:.77rem;">Must start with $ — e.g. $JohnDoe</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ca-field">
                                <label class="ca-label">Account Holder Full Name *</label>
                                <div class="ca-inp-wrap">
                                    <i class="ico fa-solid fa-user"></i>
                                    <input type="text" name="full_name" class="ca-inp"
                                           placeholder="Name on Cash App" required
                                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ca-field">
                                <label class="ca-label">Email <span class="text-muted fw-600">(optional)</span></label>
                                <div class="ca-inp-wrap">
                                    <i class="ico fa-solid fa-envelope"></i>
                                    <input type="email" name="ca_email" class="ca-inp"
                                           placeholder="Associated email"
                                           value="<?php echo isset($_POST['ca_email']) ? htmlspecialchars($_POST['ca_email']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ca-field">
                                <label class="ca-label">Phone Number <span class="text-muted fw-600">(optional)</span></label>
                                <div class="ca-inp-wrap">
                                    <i class="ico fa-solid fa-phone"></i>
                                    <input type="tel" name="ca_phone" class="ca-inp"
                                           placeholder="+1 (555) 000-0000"
                                           value="<?php echo isset($_POST['ca_phone']) ? htmlspecialchars($_POST['ca_phone']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="ca-sec"><i class="fa-solid fa-dollar-sign"></i> Withdrawal Amount</div>
                    <div class="ca-amt-box">
                        <div class="ca-amt-flex">
                            <span class="ca-sym">$</span>
                            <input type="number" name="amount" id="amtInput" class="ca-amt-inp"
                                   placeholder="0.00" step="0.01" min="1" max="2000"
                                   value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>"
                                   required>
                        </div>
                        <div class="ca-quick">
                            <button type="button" class="ca-qbtn" onclick="setAmt(50)">$50</button>
                            <button type="button" class="ca-qbtn" onclick="setAmt(100)">$100</button>
                            <button type="button" class="ca-qbtn" onclick="setAmt(500)">$500</button>
                            <button type="button" class="ca-qbtn" onclick="setAmt(1000)">$1,000</button>
                            <button type="button" class="ca-qbtn" onclick="setAmt(Math.min(<?php echo floor($_SESSION['balance']); ?>, 2000))">Max</button>
                        </div>
                        <div class="mt-2" style="font-size:.78rem;color:#94a3b8;font-weight:600;">Max per transaction: $2,000</div>
                    </div>

                    <!-- Auth -->
                    <div class="ca-sec"><i class="fa-solid fa-shield-keyhole"></i> Authentication</div>
                    <div class="row g-3 mb-5">
                        <div class="col-md-6">
                            <div class="ca-field">
                                <label class="ca-label">Transaction PIN *</label>
                                <div class="ca-inp-wrap" style="position:relative;">
                                    <i class="ico fa-solid fa-lock"></i>
                                    <input type="password" name="tx_pin" id="pinInp" class="ca-inp"
                                           placeholder="Your secure PIN" required autocomplete="new-password">
                                    <i class="fa-solid fa-eye ca-pin-toggle" onclick="togglePin(this)"></i>
                                </div>
                                <small class="text-muted mt-1 d-block fw-600" style="font-size:.77rem;">Required to authorize this withdrawal.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ca-field">
                                <label class="ca-label">Note <span class="text-muted fw-600">(optional)</span></label>
                                <div class="ca-inp-wrap">
                                    <i class="ico fa-solid fa-note-sticky"></i>
                                    <input type="text" name="memo" class="ca-inp"
                                           placeholder="What's this for?"
                                           value="<?php echo isset($_POST['memo']) ? htmlspecialchars($_POST['memo']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live review -->
                    <div class="ca-review mb-5">
                        <div style="font-size:.7rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin-bottom:12px;">Transfer Summary</div>
                        <div class="ca-review-row"><span class="rk">Sending to</span><span class="rv" id="rvTag" style="font-family:monospace;color:#00d632;">—</span></div>
                        <div class="ca-review-row"><span class="rk">Amount</span><span class="rv" id="rvAmt">—</span></div>
                        <div class="ca-review-row"><span class="rk">Cash App Fee</span><span class="rv text-success">$0.00 (Free)</span></div>
                        <div class="ca-review-row"><span class="rk fw-800 text-dark">Total Deducted</span><span class="rv text-danger fw-900" id="rvTotal">—</span></div>
                    </div>

                    <!-- Submit -->
                    <div class="row g-3">
                        <div class="col-md-8">
                            <button type="submit" id="submitBtn" class="ca-submit">
                                <i class="fa-solid fa-dollar-sign"></i> Submit Cash App Withdrawal
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="international.php" class="btn btn-light fw-700 w-100 py-3" style="border-radius:14px;">Cancel</a>
                        </div>
                    </div>

                </form>

                <div class="ca-secure">
                    <i class="fa-solid fa-shield-check"></i>
                    <div>
                        <h6>Secure & Admin-Reviewed</h6>
                        <p>All Cash App withdrawals are reviewed by our security team before dispatch. Your balance is only deducted once approved.</p>
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
    // Live $Cashtag preview
    const cashtagInp = document.getElementById('cashtagInp');
    const preview    = document.getElementById('cashtag-preview');
    const rvTag      = document.getElementById('rvTag');

    function updateCashtag() {
        let v = cashtagInp.value.trim();
        if (v && !v.startsWith('$')) v = '$' + v;
        preview.textContent = v || '$YourCashtag';
        if (rvTag) rvTag.textContent = v || '—';

        // Validation hint
        const valid = /^\$[a-zA-Z0-9_\-]{1,20}$/.test(v);
        cashtagInp.style.borderColor = v.length > 1 ? (valid ? '#00d632' : '#ef4444') : '#e5e7eb';
    }
    cashtagInp?.addEventListener('input', updateCashtag);
    updateCashtag();

    function setAmt(v) {
        document.getElementById('amtInput').value = v.toFixed(2);
        document.querySelectorAll('.ca-qbtn').forEach(b => b.classList.remove('active'));
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

    function togglePin(icon) {
        const inp = document.getElementById('pinInp');
        if (inp.type === 'password') { inp.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
        else { inp.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
    }

    document.getElementById('cashForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
    });
</script>
</body>
</html>
