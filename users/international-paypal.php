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
    $paypal_email = trim($_POST['paypal_email'] ?? '');
    $full_name    = trim(htmlspecialchars($_POST['full_name'] ?? ''));
    $account_type = htmlspecialchars($_POST['account_type'] ?? 'Personal');
    $currency     = htmlspecialchars($_POST['currency'] ?? 'USD');
    $memo         = trim(htmlspecialchars($_POST['memo'] ?? ''));
    $tx_pin       = $_POST['tx_pin'] ?? '';

    // Fetch sender
    $sender = $pdo->prepare("SELECT balance, pin FROM users WHERE id = ?");
    $sender->execute([$user_id]);
    $sender = $sender->fetch();

    // Validate
    if ($amount <= 0) {
        $error = 'Please enter a valid withdrawal amount.';
    } elseif ($amount < 5) {
        $error = 'Minimum PayPal withdrawal is $5.00.';
    } elseif ($amount > $sender['balance']) {
        $error = 'Insufficient balance. Available: $' . number_format($sender['balance'], 2) . '.';
    } elseif (empty($paypal_email) || !filter_var($paypal_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid PayPal email address.';
    } elseif (empty($full_name)) {
        $error = 'Please enter the full name of the PayPal account holder.';
    } elseif (empty($tx_pin)) {
        $error = 'Transaction PIN is required.';
    } elseif ($tx_pin !== $sender['pin']) {
        $error = 'Incorrect transaction PIN. Please try again.';
    } else {
        $narration = "PayPal Withdrawal to $paypal_email ($full_name) | Account Type: $account_type | Currency: $currency";
        if ($memo) $narration .= " | Note: $memo";
        $tx_ref = 'SCP' . strtoupper(substr(md5(uniqid()), 0, 10));

        try {
            // Save as PENDING — balance deducted only on admin approval
            $pdo->prepare("INSERT INTO transactions (user_id, amount, type, method, status, txn_hash, narration, created_at)
                           VALUES (?, ?, 'Debit', 'PayPal Withdrawal', 'Pending', ?, ?, NOW())")
                ->execute([$user_id, $amount, $tx_ref, $narration]);
            $success = true;
        } catch (Exception $e) {
            $error = 'Failed to submit withdrawal request. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Withdrawal - SwiftCapital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .pp-wrap { max-width: 1100px; margin: 0 auto; }

        /* Hero */
        .pp-hero {
            background: linear-gradient(135deg,#001c64,#003087,#009cde);
            border-radius: 24px 24px 0 0;
            padding: 46px 54px 56px;
            color: #fff;
            text-align: center;
            position: relative; overflow: hidden;
        }
        .pp-hero .orb { position: absolute; border-radius: 50%; pointer-events: none; }
        .pp-hero-icon {
            width: 78px; height: 78px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 22px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2.2rem; color: #fff;
            margin-bottom: 18px;
        }
        .pp-hero h3 { font-weight: 900; letter-spacing: -1.5px; font-size: 2rem; margin-bottom: 8px; }
        .pp-hero p  { opacity: .78; font-size: .97rem; max-width: 500px; margin: 0 auto 22px; }
        .pp-hero-badges { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
        .pp-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
            border-radius: 50px; padding: 7px 16px;
            font-size: .78rem; font-weight: 700;
        }
        .pp-badge i { color: #52d9ff; }

        /* Body */
        .pp-body {
            background: #fff; border: 1px solid #e5e7eb; border-top: none;
            border-radius: 0 0 24px 24px;
            padding: 48px 54px 54px;
            box-shadow: 0 20px 60px rgba(0,0,0,.05);
        }

        /* Balance */
        .pp-balance {
            background: linear-gradient(135deg,#003087,#009cde);
            border-radius: 16px; padding: 20px 26px;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 36px; color: #fff;
        }
        .pp-balance .lbl { font-size: .7rem; text-transform: uppercase; letter-spacing:.07em; opacity:.8; font-weight: 700; margin-bottom: 3px; }
        .pp-balance .val { font-size: 1.7rem; font-weight: 900; letter-spacing: -.5px; }

        /* PayPal account type pills */
        .pp-type-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 28px; }
        .pp-type-card {
            background: #f8fafc; border: 2px solid #e5e7eb;
            border-radius: 14px; padding: 16px 12px;
            text-align: center; cursor: pointer; transition: all .2s;
        }
        .pp-type-card:hover { border-color: #003087; background: #eff6ff; transform: translateY(-2px); }
        .pp-type-card.active { border-color: #003087; background: #dbeafe; box-shadow: 0 6px 18px rgba(0,48,135,.1); }
        .pp-type-card i { display: block; font-size: 1.6rem; color: #003087; margin-bottom: 6px; }
        .pp-type-card span { font-size: .8rem; font-weight: 800; color: #1e3a8a; }
        .pp-type-card .pp-type-desc { font-size: .7rem; font-weight: 600; color: #64748b; margin-top: 2px; }

        /* Section */
        .pp-sec {
            font-size: .68rem; font-weight: 900; text-transform: uppercase;
            letter-spacing: .12em; color: #64748b;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px; margin-bottom: 22px;
            display: flex; align-items: center; gap: 10px;
        }
        .pp-sec i { color: #003087; }

        /* Amount */
        .pp-amt-box {
            background: #f8fafc; border: 2px solid #e5e7eb;
            border-radius: 20px; padding: 26px 30px; margin-bottom: 36px; transition: all .25s;
        }
        .pp-amt-box:focus-within { border-color: #003087; background: #fff; box-shadow: 0 0 0 5px rgba(0,48,135,.06); }
        .pp-amt-flex { display: flex; align-items: center; gap: 10px; }
        .pp-sym { font-size: 2.4rem; font-weight: 900; color: #1a202c; }
        .pp-amt-inp {
            flex: 1; border: none; background: transparent;
            font-size: 2.8rem; font-weight: 900; color: #1a202c; outline: none; min-width: 0;
        }
        .pp-quick { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 16px; }
        .pp-qbtn {
            background: #fff; border: 1.5px solid #e5e7eb;
            border-radius: 10px; padding: 7px 16px;
            font-weight: 700; font-size: .85rem; color: #4a5568; cursor: pointer; transition: all .15s;
        }
        .pp-qbtn:hover, .pp-qbtn.active { border-color: #003087; color: #003087; background: #dbeafe; }

        /* Fields */
        .pp-field { margin-bottom: 20px; }
        .pp-label { font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing:.05em; color: #64748b; margin-bottom: 8px; display: block; }
        .pp-inp-wrap { position: relative; }
        .pp-inp-wrap .ico { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
        .pp-inp {
            width: 100%; background: #f8fafc; border: 1.5px solid #e5e7eb;
            border-radius: 12px; padding: 13px 16px 13px 44px;
            font-size: .97rem; font-weight: 600; color: #1a202c; transition: all .2s;
        }
        .pp-inp:focus { background: #fff; border-color: #003087; outline: none; box-shadow: 0 0 0 4px rgba(0,48,135,.07); }
        .pp-pin-toggle { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; }

        /* Review */
        .pp-review {
            background: #f8fafc; border: 1.5px solid #e5e7eb;
            border-radius: 18px; padding: 24px 28px; margin-bottom: 28px;
        }
        .pp-review-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #e5e7eb; font-size: .9rem; }
        .pp-review-row:last-child { border-bottom: none; }
        .pp-review-row .rk { color: #64748b; font-weight: 600; }
        .pp-review-row .rv { font-weight: 800; color: #1a202c; }

        /* Submit */
        .pp-submit {
            width: 100%; background: linear-gradient(135deg,#001c64,#003087);
            color: #fff; border: none; padding: 18px;
            border-radius: 14px; font-weight: 900; font-size: 1.05rem;
            box-shadow: 0 12px 28px rgba(0,48,135,.3);
            transition: all .3s; display: flex; align-items: center; justify-content: center; gap: 12px;
        }
        .pp-submit:hover { transform: translateY(-2px); box-shadow: 0 18px 38px rgba(0,48,135,.4); }
        .pp-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        /* Error */
        .pp-error {
            background: #fef2f2; border: 1px solid #fca5a5; border-radius: 14px;
            padding: 16px 20px; margin-bottom: 24px;
            display: flex; align-items: center; gap: 14px;
            font-weight: 700; color: #991b1b; font-size: .94rem;
        }
        .pp-error i { font-size: 1.2rem; flex-shrink: 0; }

        /* Success */
        .pp-success {
            text-align: center; padding: 60px 40px;
            background: #fff; border: 1px solid #e5e7eb; border-top: none;
            border-radius: 0 0 24px 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,.05);
        }
        .pp-success-icon {
            width: 96px; height: 96px; border-radius: 50%;
            background: linear-gradient(135deg,#003087,#009cde);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2.5rem; color: #fff;
            box-shadow: 0 16px 40px rgba(0,48,135,.35);
            margin-bottom: 24px;
            animation: pop .5s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes pop { from{transform:scale(.3);opacity:0}to{transform:scale(1);opacity:1} }

        /* Secure */
        .pp-secure {
            display: flex; align-items: center; gap: 16px;
            background: #f8fafc; border: 1px solid #e5e7eb;
            border-radius: 16px; padding: 18px 22px; margin-top: 28px;
        }
        .pp-secure i { color: #10b981; font-size: 1.4rem; flex-shrink: 0; }
        .pp-secure h6 { font-weight: 800; font-size: .9rem; margin-bottom: 3px; }
        .pp-secure p  { font-size: .8rem; color: #6b7280; margin: 0; }
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
        <div class="pp-wrap">

            <div class="page-header mb-4">
                <div>
                    <h1 class="page-title">PayPal Withdrawal</h1>
                    <div class="breadcrumb-text">
                        <a href="index.php">Dashboard</a>
                        <i class="fa-solid fa-chevron-right mx-2" style="font-size:.7rem;"></i>
                        International
                        <i class="fa-solid fa-chevron-right mx-2" style="font-size:.7rem;"></i>
                        PayPal Withdrawal
                    </div>
                </div>
            </div>

            <!-- Hero -->
            <div class="pp-hero shadow-lg">
                <div class="orb" style="top:-80px;right:-80px;width:280px;height:280px;background:radial-gradient(circle,rgba(0,156,222,.25) 0%,transparent 70%);"></div>
                <div class="orb" style="bottom:-60px;left:-60px;width:200px;height:200px;background:radial-gradient(circle,rgba(255,255,255,.08) 0%,transparent 70%);"></div>
                <div class="position-relative" style="z-index:2;">
                    <div class="pp-hero-icon"><i class="fa-brands fa-paypal"></i></div>
                    <h3>PayPal Withdrawal</h3>
                    <p>Withdraw funds directly to your PayPal account. Fast, secure, and globally accepted.</p>
                    <div class="pp-hero-badges">
                        <span class="pp-badge"><i class="fa-solid fa-shield-check"></i> SSL Secured</span>
                        <span class="pp-badge"><i class="fa-solid fa-lock"></i> PIN Protected</span>
                        <span class="pp-badge"><i class="fa-solid fa-clock"></i> 24hr Processing</span>
                        <span class="pp-badge"><i class="fa-solid fa-user-shield"></i> Admin Reviewed</span>
                    </div>
                </div>
            </div>

            <?php if ($success): ?>
            <!-- SUCCESS -->
            <div class="pp-success">
                <div class="pp-success-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                <h3 class="fw-900 mb-2" style="letter-spacing:-.5px;">Withdrawal Submitted!</h3>
                <p class="text-muted mb-1">Your PayPal withdrawal is <strong>pending admin review</strong>.</p>
                <p class="text-muted mb-4" style="font-size:.85rem;">Your balance will be deducted once approved. Funds typically arrive in your PayPal within <strong>24 hours</strong> of approval.</p>

                <div class="d-inline-flex align-items-center gap-2 px-4 py-3 rounded-pill mb-5 fw-800" style="background:#dbeafe;color:#1e40af;font-size:.9rem;">
                    <i class="fa-solid fa-clock"></i> Awaiting Admin Approval
                </div>

                <div class="pp-review text-start" style="max-width:480px;margin:0 auto 32px;">
                    <div class="pp-review-row" style="font-size:1rem;">
                        <span class="rk">Amount</span>
                        <span class="rv text-danger">−$<?php echo number_format($amount, 2); ?></span>
                    </div>
                    <div class="pp-review-row">
                        <span class="rk">PayPal Email</span>
                        <span class="rv"><?php echo htmlspecialchars($paypal_email); ?></span>
                    </div>
                    <div class="pp-review-row">
                        <span class="rk">Account Holder</span>
                        <span class="rv"><?php echo htmlspecialchars($full_name); ?></span>
                    </div>
                    <div class="pp-review-row">
                        <span class="rk">Account Type</span>
                        <span class="rv"><?php echo htmlspecialchars($account_type); ?></span>
                    </div>
                    <div class="pp-review-row">
                        <span class="rk">Currency</span>
                        <span class="rv"><?php echo htmlspecialchars($currency); ?></span>
                    </div>
                    <div class="pp-review-row">
                        <span class="rk">Reference</span>
                        <span class="rv" style="font-family:monospace;"><?php echo $tx_ref; ?></span>
                    </div>
                    <div class="pp-review-row">
                        <span class="rk">Status</span>
                        <span class="rv"><span class="badge fw-800" style="background:#dbeafe;color:#1e40af;">Pending Review</span></span>
                    </div>
                    <?php if ($memo): ?>
                    <div class="pp-review-row">
                        <span class="rk">Note</span>
                        <span class="rv"><?php echo htmlspecialchars($memo); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="international-paypal.php" class="btn fw-800 px-4 py-3" style="border-radius:12px;background:#003087;color:#fff;">New Withdrawal</a>
                    <a href="transactions.php" class="btn btn-outline-secondary fw-700 px-4 py-3" style="border-radius:12px;">View Transactions</a>
                    <a href="index.php" class="btn btn-light fw-700 px-4 py-3" style="border-radius:12px;">Dashboard</a>
                </div>
            </div>

            <?php else: ?>
            <!-- FORM -->
            <div class="pp-body">

                <?php if ($error): ?>
                <div class="pp-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <!-- Balance -->
                <div class="pp-balance">
                    <div>
                        <div class="lbl">Available Balance</div>
                        <div class="val">$<?php echo number_format($_SESSION['balance'], 2); ?></div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.15);border-radius:12px;padding:10px 18px;font-weight:800;font-size:.85rem;">
                        <i class="fa-brands fa-paypal fa-lg"></i> PayPal Ready
                    </div>
                </div>

                <form method="POST" id="paypalForm" autocomplete="off">
                    <input type="hidden" name="account_type" id="accountTypeInput" value="<?php echo isset($_POST['account_type']) ? htmlspecialchars($_POST['account_type']) : 'Personal'; ?>">

                    <!-- Account type selector -->
                    <div class="pp-sec"><i class="fa-brands fa-paypal"></i> Account Type</div>
                    <div class="pp-type-grid mb-4">
                        <div class="pp-type-card <?php echo (!isset($_POST['account_type']) || $_POST['account_type']==='Personal') ? 'active' : ''; ?>"
                             onclick="selectType(this, 'Personal')">
                            <i class="fa-solid fa-user"></i>
                            <span>Personal</span>
                            <div class="pp-type-desc">For individual accounts</div>
                        </div>
                        <div class="pp-type-card <?php echo (isset($_POST['account_type']) && $_POST['account_type']==='Business') ? 'active' : ''; ?>"
                             onclick="selectType(this, 'Business')">
                            <i class="fa-solid fa-briefcase"></i>
                            <span>Business</span>
                            <div class="pp-type-desc">For business / merchant accounts</div>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="pp-sec"><i class="fa-solid fa-dollar-sign"></i> Withdrawal Amount</div>
                    <div class="pp-amt-box">
                        <div class="pp-amt-flex">
                            <span class="pp-sym">$</span>
                            <input type="number" name="amount" id="amtInput" class="pp-amt-inp"
                                   placeholder="0.00" step="0.01" min="5"
                                   value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>"
                                   required>
                        </div>
                        <div class="pp-quick">
                            <button type="button" class="pp-qbtn" onclick="setAmt(50)">$50</button>
                            <button type="button" class="pp-qbtn" onclick="setAmt(100)">$100</button>
                            <button type="button" class="pp-qbtn" onclick="setAmt(500)">$500</button>
                            <button type="button" class="pp-qbtn" onclick="setAmt(1000)">$1,000</button>
                            <button type="button" class="pp-qbtn" onclick="setAmt(<?php echo floor($_SESSION['balance']); ?>)">Max</button>
                        </div>
                    </div>

                    <!-- PayPal Details -->
                    <div class="pp-sec"><i class="fa-solid fa-envelope"></i> PayPal Account Details</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="pp-field">
                                <label class="pp-label">PayPal Email Address *</label>
                                <div class="pp-inp-wrap">
                                    <i class="ico fa-solid fa-envelope"></i>
                                    <input type="email" name="paypal_email" id="ppEmail" class="pp-inp"
                                           placeholder="your@paypal.com" required autocomplete="off"
                                           value="<?php echo isset($_POST['paypal_email']) ? htmlspecialchars($_POST['paypal_email']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pp-field">
                                <label class="pp-label">Account Holder Full Name *</label>
                                <div class="pp-inp-wrap">
                                    <i class="ico fa-solid fa-user"></i>
                                    <input type="text" name="full_name" class="pp-inp"
                                           placeholder="Name on PayPal account" required
                                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pp-field">
                                <label class="pp-label">Payout Currency</label>
                                <div class="pp-inp-wrap">
                                    <i class="ico fa-solid fa-coins"></i>
                                    <select name="currency" class="pp-inp" style="appearance:none;padding-right:40px;">
                                        <option value="USD" <?php echo (!isset($_POST['currency']) || $_POST['currency']==='USD') ? 'selected' : ''; ?>>USD — US Dollar</option>
                                        <option value="EUR" <?php echo (isset($_POST['currency']) && $_POST['currency']==='EUR') ? 'selected' : ''; ?>>EUR — Euro</option>
                                        <option value="GBP" <?php echo (isset($_POST['currency']) && $_POST['currency']==='GBP') ? 'selected' : ''; ?>>GBP — British Pound</option>
                                        <option value="CAD" <?php echo (isset($_POST['currency']) && $_POST['currency']==='CAD') ? 'selected' : ''; ?>>CAD — Canadian Dollar</option>
                                        <option value="AUD" <?php echo (isset($_POST['currency']) && $_POST['currency']==='AUD') ? 'selected' : ''; ?>>AUD — Australian Dollar</option>
                                        <option value="JPY" <?php echo (isset($_POST['currency']) && $_POST['currency']==='JPY') ? 'selected' : ''; ?>>JPY — Japanese Yen</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="pp-field">
                                <label class="pp-label">Transfer Note <span class="text-muted fw-600">(optional)</span></label>
                                <div class="pp-inp-wrap">
                                    <i class="ico fa-solid fa-note-sticky"></i>
                                    <input type="text" name="memo" class="pp-inp" placeholder="Purpose of withdrawal"
                                           value="<?php echo isset($_POST['memo']) ? htmlspecialchars($_POST['memo']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Authentication -->
                    <div class="pp-sec"><i class="fa-solid fa-shield-keyhole"></i> Authentication</div>
                    <div class="row g-3 mb-5">
                        <div class="col-md-6">
                            <div class="pp-field">
                                <label class="pp-label">Transaction PIN *</label>
                                <div class="pp-inp-wrap" style="position:relative;">
                                    <i class="ico fa-solid fa-lock"></i>
                                    <input type="password" name="tx_pin" id="pinInp" class="pp-inp"
                                           placeholder="Your secure PIN" required autocomplete="new-password">
                                    <i class="fa-solid fa-eye pp-pin-toggle" onclick="togglePin(this)"></i>
                                </div>
                                <small class="text-muted mt-1 d-block fw-600" style="font-size:.77rem;">Required to authorize this withdrawal.</small>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <!-- Live review summary -->
                            <div class="pp-review w-100" style="margin-bottom:0;">
                                <div class="pp-review-row"><span class="rk">Amount</span><span class="rv" id="rvAmt">—</span></div>
                                <div class="pp-review-row"><span class="rk">PayPal Fee</span><span class="rv text-success">$0.00 (Waived)</span></div>
                                <div class="pp-review-row"><span class="rk fw-800 text-dark">Total Deducted</span><span class="rv text-danger fw-900" id="rvTotal">—</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="row g-3">
                        <div class="col-md-8">
                            <button type="submit" id="submitBtn" class="pp-submit">
                                <i class="fa-brands fa-paypal"></i> Submit PayPal Withdrawal
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="international.php" class="btn btn-light fw-700 w-100 py-3" style="border-radius:14px;">Cancel</a>
                        </div>
                    </div>

                </form>

                <!-- Secure strip -->
                <div class="pp-secure">
                    <i class="fa-solid fa-shield-check"></i>
                    <div>
                        <h6>Bank-Grade Security</h6>
                        <p>All withdrawals are reviewed by our admin team before processing. Your funds are fully protected until the transfer is approved.</p>
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
    // Account type selection
    function selectType(el, type) {
        document.querySelectorAll('.pp-type-card').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('accountTypeInput').value = type;
    }

    // Quick amounts
    function setAmt(v) {
        document.getElementById('amtInput').value = v.toFixed(2);
        document.querySelectorAll('.pp-qbtn').forEach(b => b.classList.remove('active'));
        event.currentTarget.classList.add('active');
        updateReview();
    }

    // Live review
    function updateReview() {
        const v = parseFloat(document.getElementById('amtInput').value) || 0;
        const fmt = v > 0 ? '$' + v.toLocaleString('en-US', {minimumFractionDigits:2}) : '—';
        document.getElementById('rvAmt').textContent   = fmt;
        document.getElementById('rvTotal').textContent = fmt;
    }
    document.getElementById('amtInput')?.addEventListener('input', updateReview);
    updateReview();

    // Email validation indicator
    document.getElementById('ppEmail')?.addEventListener('blur', function() {
        const v = this.value.trim();
        const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
        this.style.borderColor = v ? (valid ? '#10b981' : '#ef4444') : '#e5e7eb';
    });

    // PIN toggle
    function togglePin(icon) {
        const inp = document.getElementById('pinInp');
        if (inp.type === 'password') { inp.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
        else { inp.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
    }

    // Submit loader
    document.getElementById('paypalForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
    });
</script>
</body>
</html>
