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
    $username  = trim(htmlspecialchars($_POST['username'] ?? ''));
    $phone     = trim(htmlspecialchars($_POST['phone'] ?? ''));
    $full_name = trim(htmlspecialchars($_POST['full_name'] ?? ''));
    $memo      = trim(htmlspecialchars($_POST['memo'] ?? ''));
    $tx_pin    = $_POST['tx_pin'] ?? '';

    // Fetch sender
    $sender = $pdo->prepare("SELECT balance, pin FROM users WHERE id = ?");
    $sender->execute([$user_id]);
    $sender = $sender->fetch();

    if ($amount <= 0) {
        $error = 'Please enter a valid amount.';
    } elseif ($amount < 5) {
        $error = 'Minimum Venmo withdrawal is $5.00.';
    } elseif ($amount > $sender['balance']) {
        $error = 'Insufficient balance. Available: $' . number_format($sender['balance'], 2);
    } elseif (empty($username) || $username[0] !== '@') {
        $error = 'Please enter a valid Venmo username starting with @.';
    } elseif (empty($phone)) {
        $error = 'Please provide the phone number associated with the Venmo account.';
    } elseif (empty($tx_pin)) {
        $error = 'Transaction PIN is required.';
    } elseif ($tx_pin !== $sender['pin']) {
        $error = 'Incorrect transaction PIN.';
    } else {
        $narration = "Venmo Withdrawal to $username ($full_name) | Phone: $phone";
        if ($memo) $narration .= " | Note: $memo";
        
        $tx_ref = 'SCV' . strtoupper(substr(md5(uniqid()), 0, 10));

        try {
            // Pending status
            $pdo->prepare("INSERT INTO transactions (user_id, amount, type, method, status, txn_hash, narration, created_at)
                           VALUES (?, ?, 'Debit', 'Venmo', 'Pending', ?, ?, NOW())")
                ->execute([$user_id, $amount, $tx_ref, $narration]);
            $success = true;
        } catch (Exception $e) {
            $error = 'Failed to process request.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venmo Withdrawal - SwiftCapital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .vn-wrap { max-width: 1100px; margin: 0 auto; }
        .vn-hero {
            background: linear-gradient(135deg, #3d95ce, #0074de);
            border-radius: 24px 24px 0 0;
            padding: 46px 54px 56px;
            color: #fff; text-align: center; position: relative; overflow: hidden;
        }
        .vn-hero-icon {
            width: 78px; height: 78px;
            background: rgba(255,255,255,.2);
            border: 1px solid rgba(255,255,255,.3);
            border-radius: 22px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2.2rem; color: #fff; margin-bottom: 18px;
        }
        .vn-body {
            background: #fff; border: 1px solid #e5e7eb; border-top: none;
            border-radius: 0 0 24px 24px; padding: 48px 54px 54px;
            box-shadow: 0 20px 60px rgba(0,0,0,.05);
        }
        .vn-balance {
            background: #3d95ce; border-radius: 16px; padding: 20px 26px;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 36px; color: #fff;
        }
        .vn-amt-box {
            background: #f8fafc; border: 2px solid #e5e7eb;
            border-radius: 20px; padding: 26px 30px; margin-bottom: 36px;
        }
        .vn-amt-inp {
            border: none; background: transparent; font-size: 2.8rem;
            font-weight: 900; color: #1a202c; outline: none; width: 100%;
        }
        .vn-submit {
            width: 100%; background: #3d95ce; color: #fff; border: none;
            padding: 18px; border-radius: 14px; font-weight: 900;
            box-shadow: 0 12px 28px rgba(61, 149, 206, 0.3); transition: all .3s;
        }
        .vn-submit:hover { transform: translateY(-2px); box-shadow: 0 18px 40px rgba(61, 149, 206, 0.4); }
        .vn-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25);
            border-radius: 50px; padding: 7px 16px; font-size: .78rem; font-weight: 700;
        }
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
        <div class="vn-wrap">
            <div class="page-header mb-4">
                <h1 class="page-title">Venmo Withdrawal</h1>
                <div class="breadcrumb-text">
                    <a href="index.php">Dashboard</a> / International / Venmo
                </div>
            </div>

            <div class="vn-hero shadow-lg">
                <div class="vn-hero-icon"><i class="fa-brands fa-vimeo-v"></i></div>
                <h3>Venmo Withdrawal</h3>
                <p>Fast, social, and secure payments. Transfer your balance to your Venmo wallet instantly.</p>
                <div class="vn-hero-badges">
                    <span class="vn-badge"><i class="fa-solid fa-bolt"></i> Low Fees</span>
                    <span class="vn-badge"><i class="fa-solid fa-clock"></i> 24h Review</span>
                </div>
            </div>

            <?php if ($success): ?>
            <div class="vn-body text-center">
                <div class="mb-4 text-primary" style="font-size: 4rem;"><i class="fa-solid fa-circle-check"></i></div>
                <h3 class="fw-900">Request Received</h3>
                <p class="text-muted">Your Venmo withdrawal is currently pending admin review.</p>
                <div class="alert alert-light border border-primary-subtle d-inline-block px-5">Ref: <strong><?php echo $tx_ref; ?></strong></div>
                <div class="mt-4"><a href="transactions.php" class="btn btn-primary px-5 py-3 rounded-pill">Track Status</a></div>
            </div>
            <?php else: ?>
            <div class="vn-body">
                <?php if ($error): ?>
                <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="vn-balance shadow-sm">
                    <div>
                        <small class="text-uppercase opacity-75 fw-bold">Available Balance</small>
                        <div class="h2 fw-900 m-0">$<?php echo number_format($_SESSION['balance'], 2); ?></div>
                    </div>
                </div>

                <form method="POST">
                    <div class="vn-amt-box">
                        <label class="text-muted fw-bold small text-uppercase mb-2 d-block">Amount</label>
                        <div class="d-flex align-items-center">
                            <span class="h1 fw-900 m-0 me-2">$</span>
                            <input type="number" name="amount" class="vn-amt-inp" placeholder="0.00" step="0.01" required value="<?php echo $amount > 0 ? $amount : ''; ?>">
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold small text-uppercase text-muted mb-2 d-block">Venmo Username</label>
                            <input type="text" name="username" class="form-control form-control-lg bg-light border-0 py-3" placeholder="@username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-uppercase text-muted mb-2 d-block">Phone Number</label>
                            <input type="tel" name="phone" class="form-control form-control-lg bg-light border-0 py-3" placeholder="Associated phone number" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="fw-bold small text-uppercase text-muted mb-2 d-block">Account Holder Name</label>
                            <input type="text" name="full_name" class="form-control form-control-lg bg-light border-0 py-3" placeholder="Full legal name" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold small text-uppercase text-muted mb-2 d-block">Transaction PIN</label>
                        <input type="password" name="tx_pin" class="form-control form-control-lg bg-light border-0 py-3" placeholder="Enter your secret PIN" required>
                    </div>

                    <div class="mb-5">
                        <label class="fw-bold small text-uppercase text-muted mb-2 d-block">Memo</label>
                        <textarea name="memo" class="form-control bg-light border-0 py-3" rows="2" placeholder="What's this for?"><?php echo htmlspecialchars($_POST['memo'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="vn-submit py-4 h5 m-0 mt-2 rounded-pill">Confirm Withdrawal</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
