<?php
require_once '../includes/db.php';
require_once '../includes/user-check.php';

$user_id = $_SESSION['user_id'];
$error   = '';
$success = false;
$amount  = 0;
$tx_ref  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount     = (float)($_POST['amount'] ?? 0);
    $identifier = trim(htmlspecialchars($_POST['identifier'] ?? ''));
    $full_name  = trim(htmlspecialchars($_POST['full_name'] ?? ''));
    $memo       = trim(htmlspecialchars($_POST['memo'] ?? ''));
    $tx_pin     = $_POST['tx_pin'] ?? '';

    // Fetch sender
    $sender = $pdo->prepare("SELECT balance, pin FROM users WHERE id = ?");
    $sender->execute([$user_id]);
    $sender = $sender->fetch();

    if ($amount <= 0) {
        $error = 'Please enter a valid amount.';
    } elseif ($amount < 1) {
        $error = 'Minimum Zelle withdrawal is $1.00.';
    } elseif ($amount > $sender['balance']) {
        $error = 'Insufficient balance. Available: $' . number_format($sender['balance'], 2);
    } elseif (empty($identifier)) {
        $error = 'Please enter a Zelle identifier (Email or Phone).';
    } elseif (empty($full_name)) {
        $error = 'Please enter the recipient\'s full name.';
    } elseif (empty($tx_pin)) {
        $error = 'Transaction PIN is required.';
    } elseif ($tx_pin !== $sender['pin']) {
        $error = 'Incorrect transaction PIN.';
    } else {
        $narration = "Zelle Withdrawal to $identifier ($full_name)";
        if ($memo) $narration .= " | Note: $memo";
        
        $tx_ref = 'SCZ' . strtoupper(substr(md5(uniqid()), 0, 10));

        try {
            // Pending status
            $pdo->prepare("INSERT INTO transactions (user_id, amount, type, method, status, txn_hash, narration, created_at)
                           VALUES (?, ?, 'Debit', 'Zelle', 'Pending', ?, ?, NOW())")
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
    <title>Zelle Withdrawal - SwiftCapital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .zl-wrap { max-width: 1100px; margin: 0 auto; }
        .zl-hero {
            background: linear-gradient(135deg, #6d1ed1, #4c12a1);
            border-radius: 24px 24px 0 0;
            padding: 46px 54px 56px;
            color: #fff; text-align: center; position: relative; overflow: hidden;
        }
        .zl-hero-icon {
            width: 78px; height: 78px;
            background: rgba(255,255,255,.2);
            border: 1px solid rgba(255,255,255,.3);
            border-radius: 22px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2.2rem; color: #fff; margin-bottom: 18px;
        }
        .zl-body {
            background: #fff; border: 1px solid #e5e7eb; border-top: none;
            border-radius: 0 0 24px 24px; padding: 48px 54px 54px;
            box-shadow: 0 20px 60px rgba(0,0,0,.05);
        }
        .zl-balance {
            background: #6d1ed1; border-radius: 16px; padding: 20px 26px;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 36px; color: #fff;
        }
        .zl-amt-box {
            background: #f8fafc; border: 2px solid #e5e7eb;
            border-radius: 20px; padding: 26px 30px; margin-bottom: 36px;
        }
        .zl-amt-inp {
            border: none; background: transparent; font-size: 2.8rem;
            font-weight: 900; color: #1a202c; outline: none; width: 100%;
        }
        .zl-submit {
            width: 100%; background: #6d1ed1; color: #fff; border: none;
            padding: 18px; border-radius: 14px; font-weight: 900;
            box-shadow: 0 12px 28px rgba(109, 30, 209, 0.3); transition: all .3s;
        }
        .zl-submit:hover { transform: translateY(-2px); box-shadow: 0 18px 40px rgba(109, 30, 209, 0.4); }
        .zl-badge {
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
        <div class="zl-wrap">
            <div class="page-header mb-4">
                <h1 class="page-title">Zelle Withdrawal</h1>
                <div class="breadcrumb-text">
                    <a href="index.php">Dashboard</a> / International / Zelle
                </div>
            </div>

            <div class="zl-hero shadow-lg">
                <div class="zl-hero-icon"><i class="fa-solid fa-bolt-lightning"></i></div>
                <h3>Zelle Withdrawal</h3>
                <p>Send money directly from your account to friends, family, and others you trust.</p>
                <div class="zl-hero-badges">
                    <span class="zl-badge"><i class="fa-solid fa-bolt"></i> Instant Transfer</span>
                    <span class="zl-badge"><i class="fa-solid fa-lock"></i> Secured by Zelle</span>
                </div>
            </div>

            <?php if ($success): ?>
            <div class="zl-body text-center">
                <div class="mb-4 text-purple" style="font-size: 4rem; color: #6d1ed1;"><i class="fa-solid fa-circle-check"></i></div>
                <h3 class="fw-900">Transfer Initiated</h3>
                <p class="text-muted">Your Zelle withdrawal request has been received and is pending approval.</p>
                <div class="alert alert-light border border-purple-subtle d-inline-block px-5">Reference ID: <strong><?php echo $tx_ref; ?></strong></div>
                <div class="mt-4"><a href="transactions.php" class="btn btn-primary px-5 py-3 rounded-pill" style="background: #6d1ed1; border: none;">View Status</a></div>
            </div>
            <?php else: ?>
            <div class="zl-body">
                <?php if ($error): ?>
                <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="zl-balance">
                    <div>
                        <small class="text-uppercase opacity-75 fw-bold">Available Balance</small>
                        <div class="h2 fw-900 m-0">$<?php echo number_format($_SESSION['balance'], 2); ?></div>
                    </div>
                </div>

                <form method="POST">
                    <div class="zl-amt-box">
                        <label class="text-muted fw-bold small text-uppercase mb-2 d-block">Withdrawal Amount</label>
                        <div class="d-flex align-items-center">
                            <span class="h1 fw-900 m-0 me-2">$</span>
                            <input type="number" name="amount" class="zl-amt-inp" placeholder="0.00" step="0.01" required value="<?php echo $amount > 0 ? $amount : ''; ?>">
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold small text-uppercase text-muted mb-2 d-block">Recipient Identifier (Email/Phone)</label>
                            <input type="text" name="identifier" class="form-control form-control-lg bg-light border-0 py-3" placeholder="email@example.com or phone" required value="<?php echo htmlspecialchars($_POST['identifier'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-uppercase text-muted mb-2 d-block">Recipient Full Name</label>
                            <input type="text" name="full_name" class="form-control form-control-lg bg-light border-0 py-3" placeholder="Full name of recipient" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold small text-uppercase text-muted mb-2 d-block">Transaction PIN</label>
                        <input type="password" name="tx_pin" class="form-control form-control-lg bg-light border-0 py-3" placeholder="Enter your secret PIN" required>
                    </div>

                    <div class="mb-5">
                        <label class="fw-bold small text-uppercase text-muted mb-2 d-block">Note (Optional)</label>
                        <textarea name="memo" class="form-control bg-light border-0 py-3" rows="2" placeholder="What is this for?"><?php echo htmlspecialchars($_POST['memo'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="zl-submit py-4 h5 m-0 mt-2 rounded-pill">Initialize Zelle Transfer</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
