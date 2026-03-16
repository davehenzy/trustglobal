<?php
require_once '../includes/db.php';
require_once '../includes/user-check.php';

$user_id = $_SESSION['user_id'];
$error   = '';
$success = false;
$amount  = 0;
$tx_ref  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount      = (float)($_POST['amount'] ?? 0);
    $coin        = htmlspecialchars($_POST['coin'] ?? 'BTC');
    $network     = htmlspecialchars($_POST['network'] ?? '');
    $wallet      = trim(htmlspecialchars($_POST['wallet'] ?? ''));
    $memo        = trim(htmlspecialchars($_POST['memo'] ?? ''));
    $tx_pin      = $_POST['tx_pin'] ?? '';

    // Fetch sender
    $sender = $pdo->prepare("SELECT balance, pin FROM users WHERE id = ?");
    $sender->execute([$user_id]);
    $sender = $sender->fetch();

    if ($amount <= 0) {
        $error = 'Please enter a valid withdrawal amount.';
    } elseif ($amount < 10) {
        $error = 'Minimum withdrawal amount is $10.00.';
    } elseif ($amount > $sender['balance']) {
        $error = 'Insufficient balance. Available: $' . number_format($sender['balance'], 2) . '.';
    } elseif (empty($network)) {
        $error = 'Please select a blockchain network.';
    } elseif (empty($wallet)) {
        $error = 'Please enter your destination wallet address.';
    } elseif (strlen($wallet) < 10) {
        $error = 'Please enter a valid wallet address.';
    } elseif (empty($tx_pin)) {
        $error = 'Transaction PIN is required.';
    } elseif ($tx_pin !== $sender['pin']) {
        $error = 'Incorrect transaction PIN. Please try again.';
    } else {
        $narration = "Crypto Withdrawal: $coin via $network to wallet $wallet";
        if ($memo) $narration .= " | Note: $memo";
        $tx_ref = 'SCW' . strtoupper(substr(md5(uniqid()), 0, 10));

        try {
            // Save as PENDING — balance deducted only on admin approval
            $pdo->prepare("INSERT INTO transactions (user_id, amount, type, method, status, txn_hash, narration, created_at)
                           VALUES (?, ?, 'Debit', 'Crypto Withdrawal', 'Pending', ?, ?, NOW())")
                ->execute([$user_id, $amount, $tx_ref, $narration]);
            $success = true;
        } catch (Exception $e) {
            $error = 'Failed to submit withdrawal request. Please try again.';
        }
    }
}

$coins = [
    'BTC'  => ['label' => 'Bitcoin',  'icon' => 'fa-brands fa-bitcoin',  'color' => '#f59e0b', 'networks' => ['Bitcoin (BTC)','Lightning Network']],
    'ETH'  => ['label' => 'Ethereum', 'icon' => 'fa-brands fa-ethereum', 'color' => '#6366f1', 'networks' => ['ERC20','Arbitrum','Optimism']],
    'USDT' => ['label' => 'Tether',   'icon' => 'fa-solid fa-dollar-sign','color' => '#10b981', 'networks' => ['ERC20','TRC20','BEP20','Solana']],
    'USDC' => ['label' => 'USD Coin', 'icon' => 'fa-solid fa-circle-dollar-to-slot','color' => '#2563eb', 'networks' => ['ERC20','Solana','Polygon']],
    'BNB'  => ['label' => 'BNB',      'icon' => 'fa-solid fa-circle-dot','color' => '#eab308', 'networks' => ['BEP20 (BSC)','BEP2']],
    'SOL'  => ['label' => 'Solana',   'icon' => 'fa-solid fa-sun',       'color' => '#9333ea', 'networks' => ['Solana (Native)']],
    'XRP'  => ['label' => 'Ripple',   'icon' => 'fa-solid fa-droplet',   'color' => '#0ea5e9', 'networks' => ['XRP Ledger']],
    'LTC'  => ['label' => 'Litecoin', 'icon' => 'fa-solid fa-coins',     'color' => '#64748b', 'networks' => ['Litecoin (LTC)']],
];

$selected_coin = isset($_POST['coin']) && isset($coins[$_POST['coin']]) ? $_POST['coin'] : 'BTC';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crypto Withdrawal - SwiftCapital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .cw-wrap { max-width: 1100px; margin: 0 auto; }

        /* Hero */
        .cw-hero {
            background: linear-gradient(135deg,#0d0d0d,#1a1a2e,#16213e);
            border-radius: 24px 24px 0 0;
            padding: 46px 54px 56px;
            color: #fff;
            text-align: center;
            position: relative; overflow: hidden;
        }
        .cw-hero .orb { position: absolute; border-radius: 50%; pointer-events: none; }
        .cw-hero-icon {
            width: 72px; height: 72px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 20px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #f59e0b;
            margin-bottom: 18px;
            transition: all .3s;
        }
        .cw-hero h3 { font-weight: 900; letter-spacing: -1.5px; font-size: 2rem; margin-bottom: 8px; }
        .cw-hero p  { opacity: .72; font-size: .97rem; max-width: 500px; margin: 0 auto 20px; }
        .cw-hero-badges { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
        .cw-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.15);
            border-radius: 50px; padding: 7px 16px;
            font-size: .78rem; font-weight: 700;
        }
        .cw-badge i { color: #f59e0b; }

        /* Body */
        .cw-body {
            background: #fff; border: 1px solid #e5e7eb; border-top: none;
            border-radius: 0 0 24px 24px;
            padding: 48px 54px 54px;
            box-shadow: 0 20px 60px rgba(0,0,0,.05);
        }

        /* Balance */
        .cw-balance {
            background: linear-gradient(135deg,#1a1a2e,#16213e);
            border-radius: 16px; padding: 20px 26px;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 36px; color: #fff;
        }
        .cw-balance .lbl { font-size: .7rem; text-transform: uppercase; letter-spacing:.07em; opacity:.7; font-weight: 700; margin-bottom: 3px; }
        .cw-balance .val { font-size: 1.7rem; font-weight: 900; letter-spacing: -.5px; }

        /* Warning */
        .cw-warn {
            background: #fffbeb; border: 1px solid #fde68a;
            border-radius: 14px; padding: 14px 20px;
            display: flex; align-items: flex-start; gap: 12px;
            margin-bottom: 36px; font-size: .88rem; font-weight: 600; color: #92400e;
        }
        .cw-warn i { color: #f59e0b; flex-shrink: 0; margin-top: 2px; }

        /* Section */
        .cw-sec {
            font-size: .68rem; font-weight: 900; text-transform: uppercase;
            letter-spacing: .12em; color: #64748b;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px; margin-bottom: 22px;
            display: flex; align-items: center; gap: 10px;
        }
        .cw-sec i { color: #f59e0b; }

        /* Coin grid */
        .cw-coin-grid {
            display: grid; grid-template-columns: repeat(8, 1fr);
            gap: 10px; margin-bottom: 28px;
        }
        @media(max-width:768px) { .cw-coin-grid { grid-template-columns: repeat(4, 1fr); } }
        .cw-coin {
            background: #f8fafc; border: 2px solid #e5e7eb;
            border-radius: 14px; padding: 14px 8px;
            text-align: center; cursor: pointer; transition: all .2s;
        }
        .cw-coin:hover { border-color: #f59e0b; transform: translateY(-2px); background: #fffbeb; }
        .cw-coin.active { border-color: #f59e0b; background: #fffbeb; box-shadow: 0 6px 18px rgba(245,158,11,.15); }
        .cw-coin i { display: block; font-size: 1.5rem; margin-bottom: 6px; }
        .cw-coin span { font-size: .72rem; font-weight: 800; color: #4a5568; letter-spacing: .03em; }
        .cw-coin.active span { color: #92400e; }

        /* Amount */
        .cw-amt-box {
            background: #f8fafc; border: 2px solid #e5e7eb;
            border-radius: 20px; padding: 26px 30px; margin-bottom: 36px; transition: all .25s;
        }
        .cw-amt-box:focus-within { border-color: #f59e0b; background: #fff; box-shadow: 0 0 0 5px rgba(245,158,11,.07); }
        .cw-amt-flex { display: flex; align-items: center; gap: 10px; }
        .cw-sym { font-size: 2.4rem; font-weight: 900; color: #1a202c; }
        .cw-amt-inp {
            flex: 1; border: none; background: transparent;
            font-size: 2.8rem; font-weight: 900; color: #1a202c; outline: none; min-width: 0;
        }
        .cw-quick { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 16px; }
        .cw-qbtn {
            background: #fff; border: 1.5px solid #e5e7eb;
            border-radius: 10px; padding: 7px 16px;
            font-weight: 700; font-size: .85rem; color: #4a5568; cursor: pointer; transition: all .15s;
        }
        .cw-qbtn:hover, .cw-qbtn.active { border-color: #f59e0b; color: #92400e; background: #fffbeb; }

        /* Fields */
        .cw-field { margin-bottom: 20px; }
        .cw-label { font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing:.05em; color: #64748b; margin-bottom: 8px; display: block; }
        .cw-inp-wrap { position: relative; }
        .cw-inp-wrap .ico { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
        .cw-inp {
            width: 100%; background: #f8fafc; border: 1.5px solid #e5e7eb;
            border-radius: 12px; padding: 13px 16px 13px 44px;
            font-size: .97rem; font-weight: 600; color: #1a202c; transition: all .2s;
        }
        .cw-inp:focus { background: #fff; border-color: #f59e0b; outline: none; box-shadow: 0 0 0 4px rgba(245,158,11,.08); }
        .cw-pin-toggle { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; }

        /* Review */
        .cw-review {
            background: #f8fafc; border: 1.5px solid #e5e7eb;
            border-radius: 18px; padding: 24px 28px; margin-bottom: 28px;
        }
        .cw-review-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #e5e7eb; font-size: .9rem; }
        .cw-review-row:last-child { border-bottom: none; }
        .cw-review-row .rk { color: #64748b; font-weight: 600; }
        .cw-review-row .rv { font-weight: 800; color: #1a202c; }

        /* Submit */
        .cw-submit {
            width: 100%; background: linear-gradient(135deg,#d97706,#f59e0b);
            color: #fff; border: none; padding: 18px;
            border-radius: 14px; font-weight: 900; font-size: 1.05rem;
            box-shadow: 0 12px 28px rgba(245,158,11,.3);
            transition: all .3s; display: flex; align-items: center; justify-content: center; gap: 12px;
        }
        .cw-submit:hover { transform: translateY(-2px); box-shadow: 0 18px 38px rgba(245,158,11,.4); }
        .cw-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        /* Error */
        .cw-error {
            background: #fef2f2; border: 1px solid #fca5a5; border-radius: 14px;
            padding: 16px 20px; margin-bottom: 24px;
            display: flex; align-items: center; gap: 14px;
            font-weight: 700; color: #991b1b; font-size: .94rem;
        }
        .cw-error i { font-size: 1.2rem; flex-shrink: 0; }

        /* Success */
        .cw-success {
            text-align: center; padding: 60px 40px;
            background: #fff; border: 1px solid #e5e7eb; border-top: none;
            border-radius: 0 0 24px 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,.05);
        }
        .cw-success-icon {
            width: 96px; height: 96px; border-radius: 50%;
            background: linear-gradient(135deg,#d97706,#f59e0b);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2.5rem; color: #fff;
            box-shadow: 0 16px 40px rgba(245,158,11,.35);
            margin-bottom: 24px;
            animation: pop .5s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes pop { from{transform:scale(.3);opacity:0}to{transform:scale(1);opacity:1} }

        /* Secure */
        .cw-secure {
            display: flex; align-items: center; gap: 16px;
            background: #f8fafc; border: 1px solid #e5e7eb;
            border-radius: 16px; padding: 18px 22px; margin-top: 28px;
        }
        .cw-secure i { color: #10b981; font-size: 1.4rem; flex-shrink: 0; }
        .cw-secure h6 { font-weight: 800; font-size: .9rem; margin-bottom: 3px; }
        .cw-secure p  { font-size: .8rem; color: #6b7280; margin: 0; }
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
        <div class="cw-wrap">

            <div class="page-header mb-4">
                <div>
                    <h1 class="page-title">Cryptocurrency Withdrawal</h1>
                    <div class="breadcrumb-text">
                        <a href="index.php">Dashboard</a>
                        <i class="fa-solid fa-chevron-right mx-2" style="font-size:.7rem;"></i>
                        International
                        <i class="fa-solid fa-chevron-right mx-2" style="font-size:.7rem;"></i>
                        Crypto Withdrawal
                    </div>
                </div>
            </div>

            <!-- Hero -->
            <div class="cw-hero shadow-lg">
                <div class="orb" style="top:-80px;right:-80px;width:260px;height:260px;background:radial-gradient(circle,rgba(245,158,11,.2) 0%,transparent 70%);"></div>
                <div class="orb" style="bottom:-60px;left:-60px;width:200px;height:200px;background:radial-gradient(circle,rgba(99,102,241,.15) 0%,transparent 70%);"></div>
                <div class="position-relative" style="z-index:2;">
                    <div class="cw-hero-icon" id="heroIcon"><i class="fa-brands fa-bitcoin"></i></div>
                    <h3>Crypto Withdrawal</h3>
                    <p>Withdraw your funds to any cryptocurrency wallet — BTC, ETH, USDT and more, across major networks.</p>
                    <div class="cw-hero-badges">
                        <span class="cw-badge"><i class="fa-solid fa-shield-check"></i> Blockchain Secured</span>
                        <span class="cw-badge"><i class="fa-solid fa-lock"></i> PIN Protected</span>
                        <span class="cw-badge"><i class="fa-solid fa-bolt"></i> 1–3 Hour Processing</span>
                        <span class="cw-badge"><i class="fa-solid fa-user-shield"></i> Admin Reviewed</span>
                    </div>
                </div>
            </div>

            <?php if ($success): ?>
            <!-- SUCCESS -->
            <div class="cw-success">
                <div class="cw-success-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                <h3 class="fw-900 mb-2" style="letter-spacing:-.5px;">Withdrawal Submitted!</h3>
                <p class="text-muted mb-1">Your crypto withdrawal is <strong>pending admin review</strong>.</p>
                <p class="text-muted mb-4" style="font-size:.85rem;">Your balance will be deducted once approved. Funds typically arrive within <strong>1–3 hours</strong> of approval.</p>
                <div class="d-inline-flex align-items-center gap-2 px-4 py-3 rounded-pill mb-5 fw-800" style="background:#fef3c7;color:#92400e;font-size:.9rem;">
                    <i class="fa-solid fa-clock"></i> Awaiting Admin Approval
                </div>

                <div class="cw-review text-start" style="max-width:480px;margin:0 auto 32px;">
                    <div class="cw-review-row" style="font-size:1rem;">
                        <span class="rk">Amount</span>
                        <span class="rv text-danger">$<?php echo number_format($amount, 2); ?></span>
                    </div>
                    <div class="cw-review-row">
                        <span class="rk">Cryptocurrency</span>
                        <span class="rv"><?php echo htmlspecialchars($coin ?? ''); ?></span>
                    </div>
                    <div class="cw-review-row">
                        <span class="rk">Network</span>
                        <span class="rv"><?php echo htmlspecialchars($network ?? ''); ?></span>
                    </div>
                    <div class="cw-review-row">
                        <span class="rk">Wallet</span>
                        <span class="rv" style="font-family:monospace;font-size:.8rem;word-break:break-all;"><?php echo htmlspecialchars($wallet ?? ''); ?></span>
                    </div>
                    <div class="cw-review-row">
                        <span class="rk">Reference</span>
                        <span class="rv" style="font-family:monospace;"><?php echo $tx_ref; ?></span>
                    </div>
                    <div class="cw-review-row">
                        <span class="rk">Status</span>
                        <span class="rv"><span class="badge bg-warning text-dark fw-800">Pending Review</span></span>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="international-crypto.php" class="btn btn-warning fw-800 px-4 py-3" style="border-radius:12px;">New Withdrawal</a>
                    <a href="transactions.php" class="btn btn-outline-secondary fw-700 px-4 py-3" style="border-radius:12px;">View Transactions</a>
                    <a href="index.php" class="btn btn-light fw-700 px-4 py-3" style="border-radius:12px;">Dashboard</a>
                </div>
            </div>

            <?php else: ?>
            <!-- FORM -->
            <div class="cw-body">

                <?php if ($error): ?>
                <div class="cw-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <!-- Balance -->
                <div class="cw-balance">
                    <div>
                        <div class="lbl">Available Balance</div>
                        <div class="val">$<?php echo number_format($_SESSION['balance'], 2); ?></div>
                    </div>
                    <div class="d-flex gap-2">
                        <span style="background:rgba(245,158,11,.2);color:#fbbf24;border-radius:10px;padding:8px 16px;font-weight:800;font-size:.78rem;display:flex;align-items:center;gap:6px;">
                            <i class="fa-brands fa-bitcoin"></i> Crypto Ready
                        </span>
                    </div>
                </div>

                <!-- Warning -->
                <div class="cw-warn">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>Ensure you enter the correct wallet address and select the right network.
                    <strong>Sending to the wrong address or incompatible network will result in permanent, unrecoverable loss of funds.</strong>
                    All withdrawals require admin approval before processing.</div>
                </div>

                <form method="POST" id="cryptoForm" autocomplete="off">
                    <!-- Hidden coin field -->
                    <input type="hidden" name="coin" id="coinInput" value="<?php echo htmlspecialchars($selected_coin); ?>">

                    <!-- Coin selector -->
                    <div class="cw-sec"><i class="fa-brands fa-bitcoin"></i> Select Cryptocurrency</div>
                    <div class="cw-coin-grid mb-4">
                        <?php foreach ($coins as $symbol => $info): ?>
                        <div class="cw-coin <?php echo $selected_coin === $symbol ? 'active' : ''; ?>"
                             onclick="selectCoin(this, '<?php echo $symbol; ?>', '<?php echo addslashes($info['icon']); ?>', '<?php echo $info['color']; ?>')">
                            <i class="<?php echo $info['icon']; ?>" style="color:<?php echo $info['color']; ?>;"></i>
                            <span><?php echo $symbol; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Amount -->
                    <div class="cw-sec"><i class="fa-solid fa-dollar-sign"></i> Withdrawal Amount</div>
                    <div class="cw-amt-box">
                        <div class="cw-amt-flex">
                            <span class="cw-sym">$</span>
                            <input type="number" name="amount" id="amtInput" class="cw-amt-inp"
                                   placeholder="0.00" step="0.01" min="10"
                                   value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>"
                                   required>
                        </div>
                        <div class="cw-quick">
                            <button type="button" class="cw-qbtn" onclick="setAmt(100)">$100</button>
                            <button type="button" class="cw-qbtn" onclick="setAmt(500)">$500</button>
                            <button type="button" class="cw-qbtn" onclick="setAmt(1000)">$1,000</button>
                            <button type="button" class="cw-qbtn" onclick="setAmt(5000)">$5,000</button>
                            <button type="button" class="cw-qbtn" onclick="setAmt(<?php echo floor($_SESSION['balance']); ?>)">Max</button>
                        </div>
                    </div>

                    <!-- Wallet Details -->
                    <div class="cw-sec"><i class="fa-solid fa-wallet"></i> Destination Wallet</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="cw-field">
                                <label class="cw-label">Blockchain Network *</label>
                                <div class="cw-inp-wrap">
                                    <i class="ico fa-solid fa-network-wired"></i>
                                    <select name="network" id="networkSelect" class="cw-inp" required style="appearance:none;padding-right:40px;">
                                        <option value="" disabled selected>Select Network</option>
                                        <?php foreach ($coins[$selected_coin]['networks'] as $net): ?>
                                        <option value="<?php echo $net; ?>" <?php echo (isset($_POST['network']) && $_POST['network']==$net)?'selected':''; ?>><?php echo $net; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="cw-field">
                                <label class="cw-label">Destination Wallet Address *</label>
                                <div class="cw-inp-wrap">
                                    <i class="ico fa-solid fa-wallet"></i>
                                    <input type="text" name="wallet" class="cw-inp" placeholder="Paste your wallet address"
                                           autocomplete="off" required
                                           value="<?php echo isset($_POST['wallet']) ? htmlspecialchars($_POST['wallet']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="cw-field">
                                <label class="cw-label">Memo / Tag <span class="text-muted fw-600">(if required by exchange)</span></label>
                                <div class="cw-inp-wrap">
                                    <i class="ico fa-solid fa-tag"></i>
                                    <input type="text" name="memo" class="cw-inp" placeholder="Optional — required for XRP, BNB, etc."
                                           value="<?php echo isset($_POST['memo']) ? htmlspecialchars($_POST['memo']) : ''; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PIN -->
                    <div class="cw-sec"><i class="fa-solid fa-shield-keyhole"></i> Authentication</div>
                    <div class="row g-3 mb-5">
                        <div class="col-md-6">
                            <div class="cw-field">
                                <label class="cw-label">Transaction PIN *</label>
                                <div class="cw-inp-wrap" style="position:relative;">
                                    <i class="ico fa-solid fa-lock"></i>
                                    <input type="password" name="tx_pin" id="pinInp" class="cw-inp" placeholder="Your secure PIN" required autocomplete="new-password">
                                    <i class="fa-solid fa-eye cw-pin-toggle" onclick="togglePin(this)"></i>
                                </div>
                                <small class="text-muted mt-1 d-block fw-600" style="font-size:.77rem;">Required to authorize this withdrawal.</small>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="cw-review w-100" style="margin-bottom:0;">
                                <div class="cw-review-row"><span class="rk">Amount</span><span class="rv" id="rvAmt">—</span></div>
                                <div class="cw-review-row"><span class="rk">Network Fee</span><span class="rv text-success">Included</span></div>
                                <div class="cw-review-row"><span class="rk fw-800 text-dark">Total</span><span class="rv text-danger fw-900" id="rvTotal">—</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="row g-3">
                        <div class="col-md-8">
                            <button type="submit" id="submitBtn" class="cw-submit">
                                <i class="fa-brands fa-bitcoin"></i> Submit Withdrawal Request
                            </button>
                        </div>
                        <div class="col-md-4">
                            <a href="international.php" class="btn btn-light fw-700 w-100 py-3" style="border-radius:14px;">Cancel</a>
                        </div>
                    </div>

                </form>

                <!-- Secure strip -->
                <div class="cw-secure">
                    <i class="fa-solid fa-shield-check"></i>
                    <div>
                        <h6>256-Bit End-to-End Encrypted</h6>
                        <p>Crypto withdrawals are irreversible once processed. All transactions are reviewed by our security team before dispatch.</p>
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
    // Coin networks map from PHP
    const networks = <?php echo json_encode(array_combine(array_keys($coins), array_column($coins, 'networks'))); ?>;
    const coinIcons = <?php echo json_encode(array_combine(array_keys($coins), array_column($coins, 'icon'))); ?>;
    const coinColors = <?php echo json_encode(array_combine(array_keys($coins), array_column($coins, 'color'))); ?>;

    function selectCoin(el, symbol, icon, color) {
        document.querySelectorAll('.cw-coin').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('coinInput').value = symbol;

        // Update hero icon
        const heroIcon = document.getElementById('heroIcon');
        heroIcon.querySelector('i').className = coinIcons[symbol];
        heroIcon.style.color = coinColors[symbol];

        // Update network dropdown
        const sel = document.getElementById('networkSelect');
        sel.innerHTML = '<option value="" disabled selected>Select Network</option>';
        (networks[symbol] || []).forEach(net => {
            const opt = document.createElement('option');
            opt.value = opt.textContent = net;
            sel.appendChild(opt);
        });
    }

    function setAmt(v) {
        document.getElementById('amtInput').value = v.toFixed(2);
        document.querySelectorAll('.cw-qbtn').forEach(b => b.classList.remove('active'));
        event.currentTarget.classList.add('active');
        updateReview();
    }

    function updateReview() {
        const v = parseFloat(document.getElementById('amtInput').value) || 0;
        const fmt = v > 0 ? '$' + v.toLocaleString('en-US',{minimumFractionDigits:2}) : '—';
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

    document.getElementById('cryptoForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
    });
</script>
</body>
</html>
