<?php
require_once '../includes/db.php';
require_once '../includes/user-check.php';

$user_id  = $_SESSION['user_id'];
$error    = '';
$success  = false;
$tx_ref   = '';

// ── AJAX: account number lookup ──────────────────────────────
if (isset($_GET['lookup'])) {
    $acct = trim($_GET['lookup']);
    $stmt = $pdo->prepare("SELECT name, lastname, account_number FROM users WHERE account_number = ? AND id != ?");
    $stmt->execute([$acct, $user_id]);
    $found = $stmt->fetch();
    header('Content-Type: application/json');
    if ($found) {
        echo json_encode(['status'=>'found','name'=>htmlspecialchars($found['name'].' '.$found['lastname'])]);
    } else {
        echo json_encode(['status'=>'not_found']);
    }
    exit;
}

// ── Form submission ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount      = (float)($_POST['amount'] ?? 0);
    $acct_number = trim($_POST['recipient_account'] ?? '');
    $bank_name   = trim(htmlspecialchars($_POST['bank_name'] ?? 'SwiftCapital'));
    $method      = htmlspecialchars($_POST['transfer_method'] ?? 'Online Internal Banking');
    $memo        = trim(htmlspecialchars($_POST['memo'] ?? ''));
    $tx_pin      = $_POST['tx_pin'] ?? '';

    // -- Fetch sender current data
    $sender = $pdo->prepare("SELECT balance, pin, name, lastname, account_number FROM users WHERE id = ?");
    $sender->execute([$user_id]);
    $sender = $sender->fetch();

    // -- Validate
    if ($amount <= 0) {
        $error = 'Please enter a valid transfer amount.';
    } elseif ($amount > $sender['balance']) {
        $error = 'Insufficient balance. Your available balance is $'.number_format($sender['balance'], 2).'.';
    } elseif (empty($acct_number)) {
        $error = 'Please enter the recipient account number.';
    } elseif ($acct_number === $sender['account_number']) {
        $error = 'You cannot transfer to your own account.';
    } elseif (empty($tx_pin)) {
        $error = 'Transaction PIN is required.';
    } elseif ($tx_pin !== $sender['pin']) {
        $error = 'Incorrect transaction PIN. Please try again.';
    } else {
        // -- Recipient lookup
        $recip_stmt = $pdo->prepare("SELECT id, name, lastname, account_number FROM users WHERE account_number = ?");
        $recip_stmt->execute([$acct_number]);
        $recipient = $recip_stmt->fetch();

        $narration_out = "Local Transfer to " . ($recipient ? $recipient['name'].' '.$recipient['lastname'] : $acct_number) . " | " . $bank_name;
        $narration_in  = "Local Transfer from " . $sender['name'] . ' ' . $sender['lastname'] . " (Acc: " . $sender['account_number'] . ")";
        if ($memo) { $narration_out .= " | Note: $memo"; $narration_in .= " | Note: $memo"; }

        $tx_ref = 'SCL' . strtoupper(substr(md5(uniqid()), 0, 10));

        try {
            $pdo->beginTransaction();

            // Debit sender
            $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?")->execute([$amount, $user_id]);
            $pdo->prepare("INSERT INTO transactions (user_id, amount, type, method, status, txn_hash, narration, created_at)
                           VALUES (?, ?, 'Debit', ?, 'Completed', ?, ?, NOW())")
                ->execute([$user_id, $amount, $method, $tx_ref, $narration_out]);

            // Credit recipient (only if internal user found)
            if ($recipient) {
                $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?")->execute([$amount, $recipient['id']]);
                $pdo->prepare("INSERT INTO transactions (user_id, amount, type, method, status, txn_hash, narration, created_at)
                               VALUES (?, ?, 'Credit', ?, 'Completed', ?, ?, NOW())")
                    ->execute([$recipient['id'], $amount, $method, $tx_ref, $narration_in]);
            }

            $pdo->commit();
            $success = true;

            // Refresh session balance
            $new_bal = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
            $new_bal->execute([$user_id]);
            $_SESSION['balance'] = $new_bal->fetchColumn();

        } catch(Exception $e) {
            $pdo->rollBack();
            $error = 'Transaction failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Transfer - SwiftCapital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* ── Layout ── */
        .lt-wrap { max-width: 1100px; margin: 0 auto; }

        /* ── Hero ── */
        .lt-hero {
            background: linear-gradient(135deg,#0f0c29,#302b63,#24243e);
            border-radius: 24px 24px 0 0;
            padding: 48px 50px 56px;
            color: #fff;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .lt-hero .orb {
            position: absolute; border-radius: 50%; pointer-events: none;
        }
        .lt-hero-icon {
            width: 68px; height: 68px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 20px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.8rem; color: #818cf8;
            margin-bottom: 18px;
        }
        .lt-hero h3 { font-weight: 900; letter-spacing: -1px; font-size: 1.9rem; margin-bottom: 8px; }
        .lt-hero p { opacity: .75; font-size: .97rem; max-width: 500px; margin: 0 auto; }

        /* ── Body ── */
        .lt-body {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 24px 24px;
            padding: 48px 50px 50px;
            box-shadow: 0 20px 50px rgba(0,0,0,.05);
        }

        /* ── Balance row ── */
        .lt-balance-strip {
            background: linear-gradient(135deg,#f0f9ff,#e0f2fe);
            border: 1px solid #bae6fd;
            border-radius: 16px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 36px;
        }
        .lt-balance-strip .lbl { font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: #0369a1; font-weight: 700; margin-bottom: 4px; }
        .lt-balance-strip .val { font-size: 1.6rem; font-weight: 900; color: #0c4a6e; letter-spacing: -.5px; }

        /* ── Section headers ── */
        .lt-sec-title {
            font-size: .7rem; font-weight: 900; text-transform: uppercase;
            letter-spacing: .1em; color: #64748b;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px; margin-bottom: 22px;
            display: flex; align-items: center; gap: 10px;
        }
        .lt-sec-title i { color: #6366f1; font-size: .85rem; }

        /* ── Amount box ── */
        .lt-amount-box {
            background: #f8fafc;
            border: 2px solid #e5e7eb;
            border-radius: 20px;
            padding: 28px 30px;
            margin-bottom: 36px;
            transition: all .25s;
        }
        .lt-amount-box:focus-within {
            border-color: #6366f1;
            background: #fff;
            box-shadow: 0 0 0 5px rgba(99,102,241,.07);
        }
        .lt-amount-flex { display: flex; align-items: center; gap: 10px; }
        .lt-currency { font-size: 2.4rem; font-weight: 900; color: #1a202c; }
        .lt-amount-input {
            flex: 1; border: none; background: transparent;
            font-size: 2.8rem; font-weight: 900; color: #1a202c; outline: none; width: 0;
        }
        .lt-quick { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 18px; }
        .lt-quick-btn {
            background: #fff; border: 1.5px solid #e5e7eb;
            border-radius: 10px; padding: 7px 16px;
            font-weight: 700; font-size: .85rem; color: #4a5568;
            cursor: pointer; transition: all .15s;
        }
        .lt-quick-btn:hover { border-color: #6366f1; color: #6366f1; background: #f5f3ff; }
        .lt-quick-btn.active { border-color: #6366f1; color: #6366f1; background: #f5f3ff; }

        /* ── Inputs ── */
        .lt-field { margin-bottom: 20px; }
        .lt-label { font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: #64748b; margin-bottom: 8px; display: block; }
        .lt-input-wrap { position: relative; }
        .lt-input-wrap i.icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1rem; pointer-events: none; }
        .lt-input {
            width: 100%; background: #f8fafc; border: 1.5px solid #e5e7eb;
            border-radius: 12px; padding: 13px 16px 13px 44px;
            font-size: .97rem; font-weight: 600; color: #1a202c; transition: all .2s;
        }
        .lt-input:focus { background: #fff; border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99,102,241,.08); }
        .lt-input.success-border { border-color: #10b981; }
        .lt-input.error-border  { border-color: #ef4444; }
        .lt-input-verified {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            font-size: .8rem; font-weight: 800; display: none;
        }

        /* ── Recipient name tag ── */
        .lt-recipient-tag {
            display: none; margin-top: 10px;
            background: #ecfdf5; border: 1px solid #a7f3d0;
            border-radius: 10px; padding: 10px 14px;
            display: none; align-items: center; gap: 10px;
        }
        .lt-recipient-tag .ricon { width: 32px; height: 32px; background: #10b981; border-radius: 50%; color: #fff; display: flex; align-items: center; justify-content: center; font-size: .8rem; font-weight: 800; flex-shrink: 0; }
        .lt-recipient-tag .rname { font-weight: 800; font-size: .9rem; color: #065f46; }
        .lt-recipient-tag .rinfo { font-size: .75rem; color: #6b7280; }
        .lt-not-found { display: none; margin-top: 10px; background: #fef2f2; border: 1px solid #fca5a5; border-radius: 10px; padding: 10px 14px; font-size: .85rem; color: #991b1b; font-weight: 700; }

        /* ── PIN field ── */
        .lt-pin-wrap { position: relative; }
        .lt-pin-wrap .toggle-vis { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; }

        /* ── Submit ── */
        .lt-submit {
            width: 100%; background: linear-gradient(135deg,#6366f1,#8b5cf6);
            color: #fff; border: none; padding: 18px;
            border-radius: 14px; font-weight: 900; font-size: 1.05rem;
            box-shadow: 0 12px 28px rgba(99,102,241,.3);
            transition: all .3s; display: flex; align-items: center; justify-content: center; gap: 12px;
        }
        .lt-submit:hover { transform: translateY(-2px); box-shadow: 0 16px 36px rgba(99,102,241,.4); }
        .lt-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        /* ── Review step ── */
        .lt-review {
            background: #f8fafc; border: 1.5px solid #e5e7eb;
            border-radius: 18px; padding: 28px 30px; margin-bottom: 28px;
        }
        .lt-review-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px dashed #e5e7eb; font-size: .92rem; }
        .lt-review-row:last-child { border-bottom: none; }
        .lt-review-row .rkey { color: #64748b; font-weight: 600; }
        .lt-review-row .rval { font-weight: 800; color: #1a202c; }

        /* ── Success ── */
        .lt-success {
            text-align: center; padding: 56px 40px;
            background: #fff; border: 1px solid #e5e7eb;
            border-top: none; border-radius: 0 0 24px 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,.05);
        }
        .success-circle {
            width: 96px; height: 96px; border-radius: 50%;
            background: linear-gradient(135deg,#10b981,#059669);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2.5rem; color: #fff;
            box-shadow: 0 16px 40px rgba(16,185,129,.35);
            margin-bottom: 24px; animation: popIn .5s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes popIn { from { transform: scale(.3); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        /* ── Error alert ── */
        .lt-error {
            background: #fef2f2; border: 1px solid #fca5a5; border-radius: 14px;
            padding: 16px 20px; margin-bottom: 28px;
            display: flex; align-items: center; gap: 14px;
            font-weight: 700; color: #991b1b; font-size: .94rem;
        }
        .lt-error i { font-size: 1.2rem; flex-shrink: 0; }

        /* ── Security strip ── */
        .lt-secure {
            display: flex; align-items: center; gap: 16px;
            background: #f8fafc; border: 1px solid #e5e7eb;
            border-radius: 16px; padding: 18px 22px; margin-top: 28px;
        }
        .lt-secure i { color: #10b981; font-size: 1.4rem; flex-shrink: 0; }
        .lt-secure h6 { font-weight: 800; font-size: .9rem; margin-bottom: 3px; }
        .lt-secure p { font-size: .8rem; color: #6b7280; margin: 0; }
    </style>
</head>
<body>
<?php
$page = 'local';
include '../includes/user-sidebar.php';
?>
<main class="main-content">
    <?php include '../includes/user-navbar.php'; ?>
    <div class="page-container">
        <div class="lt-wrap">

            <div class="page-header mb-4">
                <div>
                    <h1 class="page-title">Local Transfer</h1>
                    <div class="breadcrumb-text">
                        <a href="index.php">Dashboard</a>
                        <i class="fa-solid fa-chevron-right mx-2" style="font-size:.7rem;"></i>
                        Local Transfer
                    </div>
                </div>
            </div>

            <!-- ── Hero ── -->
            <div class="lt-hero shadow-lg">
                <div class="orb" style="top:-80px;right:-80px;width:250px;height:250px;background:radial-gradient(circle,rgba(99,102,241,.3) 0%,transparent 70%);"></div>
                <div class="orb" style="bottom:-60px;left:-60px;width:180px;height:180px;background:radial-gradient(circle,rgba(139,92,246,.2) 0%,transparent 70%);"></div>
                <div class="position-relative" style="z-index:2;">
                    <div class="lt-hero-icon"><i class="fa-solid fa-paper-plane"></i></div>
                    <h3>Domestic Bank Transfer</h3>
                    <p>Move funds to any local account instantly with zero transaction fees and real-time processing.</p>
                </div>
            </div>

            <?php if ($success): ?>
            <!-- ── SUCCESS STATE ── -->
            <div class="lt-success">
                <div class="success-circle"><i class="fa-solid fa-check"></i></div>
                <h3 class="fw-900 mb-2" style="letter-spacing:-.5px;">Transfer Successful!</h3>
                <p class="text-muted mb-4">Your funds have been sent successfully.</p>

                <div class="lt-review text-start" style="max-width:480px;margin:0 auto 32px;">
                    <div class="lt-review-row">
                        <span class="rkey">Amount Sent</span>
                        <span class="rval text-danger">−$<?php echo number_format($amount, 2); ?></span>
                    </div>
                    <div class="lt-review-row">
                        <span class="rkey">Recipient Account</span>
                        <span class="rval"><?php echo htmlspecialchars($acct_number); ?></span>
                    </div>
                    <div class="lt-review-row">
                        <span class="rkey">Bank</span>
                        <span class="rval"><?php echo htmlspecialchars($bank_name); ?></span>
                    </div>
                    <div class="lt-review-row">
                        <span class="rkey">Method</span>
                        <span class="rval"><?php echo htmlspecialchars($method); ?></span>
                    </div>
                    <div class="lt-review-row">
                        <span class="rkey">Reference</span>
                        <span class="rval" style="font-family:monospace;"><?php echo $tx_ref; ?></span>
                    </div>
                    <div class="lt-review-row">
                        <span class="rkey">New Balance</span>
                        <span class="rval text-success">$<?php echo number_format($_SESSION['balance'], 2); ?></span>
                    </div>
                    <?php if ($memo): ?>
                    <div class="lt-review-row">
                        <span class="rkey">Memo</span>
                        <span class="rval"><?php echo htmlspecialchars($memo); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="local.php" class="btn btn-primary fw-800 px-4 py-3" style="border-radius:12px;">New Transfer</a>
                    <a href="transactions.php" class="btn btn-outline-secondary fw-700 px-4 py-3" style="border-radius:12px;">View Transactions</a>
                    <a href="index.php" class="btn btn-light fw-700 px-4 py-3" style="border-radius:12px;">Back to Dashboard</a>
                </div>
            </div>

            <?php else: ?>
            <!-- ── TRANSFER FORM ── -->
            <div class="lt-body">

                <?php if ($error): ?>
                <div class="lt-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <!-- Balance strip -->
                <div class="lt-balance-strip">
                    <div>
                        <div class="lbl">Available Balance</div>
                        <div class="val">$<?php echo number_format($_SESSION['balance'], 2); ?></div>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="background:rgba(16,185,129,.1);color:#10b981;border-radius:10px;padding:8px 16px;font-weight:800;font-size:.8rem;">
                        <i class="fa-solid fa-circle" style="font-size:.5rem;"></i> Active Account
                    </div>
                </div>

                <form method="POST" id="localTransferForm" autocomplete="off">

                    <!-- Amount -->
                    <div class="lt-sec-title"><i class="fa-solid fa-dollar-sign"></i> Transfer Amount</div>
                    <div class="lt-amount-box">
                        <div class="lt-amount-flex">
                            <span class="lt-currency">$</span>
                            <input type="number" name="amount" id="amountInput" class="lt-amount-input"
                                   placeholder="0.00" step="0.01" min="1"
                                   value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>"
                                   required>
                        </div>
                        <div class="lt-quick">
                            <button type="button" class="lt-quick-btn" onclick="setAmt(100)">$100</button>
                            <button type="button" class="lt-quick-btn" onclick="setAmt(500)">$500</button>
                            <button type="button" class="lt-quick-btn" onclick="setAmt(1000)">$1,000</button>
                            <button type="button" class="lt-quick-btn" onclick="setAmt(5000)">$5,000</button>
                            <button type="button" class="lt-quick-btn" onclick="setAmt(<?php echo floor($_SESSION['balance']); ?>)">Max</button>
                        </div>
                    </div>

                    <!-- Beneficiary -->
                    <div class="lt-sec-title"><i class="fa-solid fa-user-check"></i> Beneficiary Information</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="lt-field">
                                <label class="lt-label">Recipient Account Number</label>
                                <div class="lt-input-wrap">
                                    <i class="icon fa-solid fa-hashtag"></i>
                                    <input type="text" name="recipient_account" id="recipientAcct" class="lt-input"
                                           placeholder="Enter account number"
                                           value="<?php echo isset($_POST['recipient_account']) ? htmlspecialchars($_POST['recipient_account']) : ''; ?>"
                                           required autocomplete="off">
                                    <span class="lt-input-verified text-success" id="verifiedMark"><i class="fa-solid fa-circle-check"></i></span>
                                </div>
                                <!-- Recipient name tag (shows on lookup) -->
                                <div class="lt-recipient-tag" id="recipientTag">
                                    <div class="ricon" id="recipientInitial"></div>
                                    <div>
                                        <div class="rname" id="recipientName"></div>
                                        <div class="rinfo">SwiftCapital Internal Account</div>
                                    </div>
                                </div>
                                <div class="lt-not-found" id="notFound">
                                    <i class="fa-solid fa-circle-xmark me-2"></i> Account not found in SwiftCapital. Transfer will proceed as external.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="lt-field">
                                <label class="lt-label">Receiving Bank Name</label>
                                <div class="lt-input-wrap">
                                    <i class="icon fa-solid fa-building-columns"></i>
                                    <input type="text" name="bank_name" class="lt-input" placeholder="e.g. SwiftCapital, Chase, Wells Fargo"
                                           value="<?php echo isset($_POST['bank_name']) ? htmlspecialchars($_POST['bank_name']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="lt-field">
                                <label class="lt-label">Transfer Method</label>
                                <div class="lt-input-wrap">
                                    <i class="icon fa-solid fa-arrows-rotate"></i>
                                    <select name="transfer_method" class="lt-input" style="appearance:none;padding-right:40px;">
                                        <option>Online Internal Banking</option>
                                        <option>Standard Wire Transfer</option>
                                        <option>Instant ACH Transfer</option>
                                        <option>RTGS Transfer</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="lt-field">
                                <label class="lt-label">Transaction Memo (Optional)</label>
                                <div class="lt-input-wrap">
                                    <i class="icon fa-solid fa-note-sticky"></i>
                                    <input type="text" name="memo" class="lt-input" placeholder="Purpose of transfer"
                                           value="<?php echo isset($_POST['memo']) ? htmlspecialchars($_POST['memo']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Authentication -->
                    <div class="lt-sec-title"><i class="fa-solid fa-shield-keyhole"></i> Transaction Authentication</div>
                    <div class="row g-3 mb-5">
                        <div class="col-md-6">
                            <div class="lt-field">
                                <label class="lt-label">Transaction PIN</label>
                                <div class="lt-input-wrap lt-pin-wrap">
                                    <i class="icon fa-solid fa-lock"></i>
                                    <input type="password" name="tx_pin" id="txPin" class="lt-input" placeholder="Enter your PIN" required autocomplete="new-password">
                                    <i class="fa-solid fa-eye toggle-vis" onclick="togglePin(this)"></i>
                                </div>
                                <small class="text-muted mt-1 d-block fw-600" style="font-size:.78rem;">Required to authorize this transaction.</small>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <!-- Review summary card -->
                            <div class="w-100 p-3 rounded-3" style="background:#f8fafc;border:1px solid #e5e7eb;font-size:.85rem;">
                                <div class="d-flex justify-content-between mb-2"><span class="text-muted fw-600">Amount</span><span class="fw-800" id="reviewAmt">—</span></div>
                                <div class="d-flex justify-content-between mb-2"><span class="text-muted fw-600">Fee</span><span class="fw-800 text-success">Free</span></div>
                                <div class="d-flex justify-content-between pt-2 border-top"><span class="fw-800">Total Deducted</span><span class="fw-900 text-danger" id="reviewTotal">—</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="row g-3">
                        <div class="col-md-8">
                            <button type="submit" class="lt-submit" id="submitBtn">
                                <i class="fa-solid fa-paper-plane-top"></i> Send Transfer
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="index.php" class="btn btn-light fw-700 w-100 py-3" style="border-radius:14px;">Cancel</a>
                        </div>
                    </div>

                </form>

                <!-- Security strip -->
                <div class="lt-secure">
                    <i class="fa-solid fa-shield-check"></i>
                    <div>
                        <h6>256-Bit AES Encrypted</h6>
                        <p>This transaction is protected end-to-end. Your financial data remains private and secure.</p>
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
    // ── Quick amount ──
    function setAmt(v) {
        const inp = document.getElementById('amountInput');
        inp.value = v.toFixed(2);
        document.querySelectorAll('.lt-quick-btn').forEach(b => b.classList.remove('active'));
        event.currentTarget.classList.add('active');
        updateReview();
    }

    // ── Review summary ──
    function updateReview() {
        const raw = parseFloat(document.getElementById('amountInput').value) || 0;
        document.getElementById('reviewAmt').textContent   = raw > 0 ? '$' + raw.toLocaleString('en-US',{minimumFractionDigits:2}) : '—';
        document.getElementById('reviewTotal').textContent = raw > 0 ? '$' + raw.toLocaleString('en-US',{minimumFractionDigits:2}) : '—';
    }
    document.getElementById('amountInput')?.addEventListener('input', updateReview);
    updateReview();

    // ── Account lookup ──
    let lookupTimer;
    const acctInput  = document.getElementById('recipientAcct');
    const rTag       = document.getElementById('recipientTag');
    const rName      = document.getElementById('recipientName');
    const rInitial   = document.getElementById('recipientInitial');
    const nfDiv      = document.getElementById('notFound');
    const vMark      = document.getElementById('verifiedMark');

    acctInput?.addEventListener('input', function() {
        clearTimeout(lookupTimer);
        const val = this.value.trim();
        rTag.style.display    = 'none';
        nfDiv.style.display   = 'none';
        vMark.style.display   = 'none';
        acctInput.classList.remove('success-border','error-border');

        if (val.length < 6) return;
        lookupTimer = setTimeout(() => {
            fetch(`local.php?lookup=${encodeURIComponent(val)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'found') {
                        rName.textContent    = data.name;
                        rInitial.textContent = data.name.split(' ').map(w=>w[0]).join('').toUpperCase().slice(0,2);
                        rTag.style.display   = 'flex';
                        nfDiv.style.display  = 'none';
                        acctInput.classList.add('success-border');
                        acctInput.classList.remove('error-border');
                        vMark.style.display  = 'inline';
                    } else {
                        rTag.style.display   = 'none';
                        nfDiv.style.display  = 'block';
                        acctInput.classList.add('error-border');
                        acctInput.classList.remove('success-border');
                        vMark.style.display  = 'none';
                    }
                });
        }, 600);
    });

    // ── PIN visibility ──
    function togglePin(icon) {
        const inp = document.getElementById('txPin');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.classList.replace('fa-eye','fa-eye-slash');
        } else {
            inp.type = 'password';
            icon.classList.replace('fa-eye-slash','fa-eye');
        }
    }

    // ── Submit loader ──
    document.getElementById('localTransferForm')?.addEventListener('submit', function(e) {
        const amt = parseFloat(document.getElementById('amountInput').value);
        if (!amt || amt <= 0) { e.preventDefault(); alert('Please enter a valid amount.'); return; }
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
    });
</script>
</body>
</html>
