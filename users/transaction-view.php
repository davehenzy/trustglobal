<?php 
require_once '../includes/db.php';
require_once '../includes/user-check.php'; 

$txn_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$txn_id) {
    header("Location: transactions.php");
    exit;
}

// Fetch Transaction and ensure it belongs to the user
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ?");
$stmt->execute([$txn_id, $user_id]);
$tx = $stmt->fetch();

if (!$tx) {
    echo "Transaction not found or access denied.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Details - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .receipt-card {
            background: #fff;
            border-radius: 30px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
            overflow: hidden;
            max-width: 600px;
            margin: 40px auto;
        }
        .bg-emerald-light { background: #ecfdf5; }
        .bg-rose-light { background: #fff1f2; }
        .bg-amber-light { background: #fffbeb; }
        .fw-800 { font-weight: 800; }
        .fw-900 { font-weight: 900; }
        .text-xs { font-size: 0.75rem; }

        @media print {
            @page { margin: 0; }
            body { 
                margin: 0; 
                padding: 0; 
                background: white !important;
            }
            .container {
                max-width: 100% !important;
                width: 100% !important;
                padding: 2cm !important;
                margin: 0 !important;
            }
            .btn, .mt-5.pt-4 {
                display: none !important;
            }
            .receipt-card {
                box-shadow: none !important;
                border: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            .bg-light {
                background: white !important;
            }
        }
    </style>
</head>
<body class="bg-light">

    <div class="container py-5">
        <div class="receipt-card">
            <div class="p-4 p-md-5">
                <!-- Receipt Header -->
                <div class="text-center mb-5">
                    <div class="d-inline-flex mb-4" style="width: 120px; height: 120px; align-items: center; justify-content: center;">
                        <?php if ($tx['status'] == 'Completed'): ?>
                            <img src="../assets/images/SWC_Primary_Logo_Light.png" alt="Logo" style="max-width: 100%; height: auto;">
                        <?php elseif ($tx['status'] == 'Pending'): ?>
                            <div class="bg-amber-light" style="width: 90px; height: 90px; border-radius: 25px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-hourglass-half text-warning fa-3x"></i>
                            </div>
                        <?php else: ?>
                            <div class="bg-rose-light" style="width: 90px; height: 90px; border-radius: 25px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-xmark text-danger fa-3x"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h1 class="fw-800 mb-2" style="letter-spacing: -2px; font-size: 3rem;">$<?php echo number_format($tx['amount'], 2); ?></h1>
                    <p class="<?php echo ($tx['status'] == 'Completed') ? 'text-success' : (($tx['status'] == 'Pending') ? 'text-warning' : 'text-danger'); ?> fw-800 text-uppercase mb-4" style="letter-spacing: 3px; font-size: 0.85rem;">
                        <?php echo ($tx['status'] == 'Completed') ? 'Ledger Entry Confirmed' : (($tx['status'] == 'Pending') ? 'Transaction in Review' : 'Transaction Refused'); ?>
                    </p>
                    <div class="p-2 border rounded-3 d-inline-block bg-light px-4">
                        <span class="text-xs fw-800 text-muted">REFERENCE: <strong>#<?php echo $tx['txn_hash']; ?></strong></span>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="border-top pt-4">
                    <div class="d-flex justify-content-between mb-3 text-sm">
                        <span class="text-muted">Transaction Type</span>
                        <span class="fw-bold"><?php echo $tx['type']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-sm">
                        <span class="text-muted">Payment Method</span>
                        <span class="fw-bold"><?php echo $tx['method'] ?: 'Internal Transfer'; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-sm">
                        <span class="text-muted">Date & Time</span>
                        <span class="fw-bold"><?php echo date('M d, Y - H:i', strtotime($tx['created_at'])); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-sm">
                        <span class="text-muted">Status</span>
                        <span class="badge <?php echo $tx['status'] == 'Completed' ? 'bg-success' : ($tx['status'] == 'Pending' ? 'bg-warning' : 'bg-danger'); ?>"><?php echo $tx['status']; ?></span>
                    </div>
                </div>

                <div class="bg-light p-4 rounded-4 mt-4">
                    <label class="text-xs fw-800 text-muted text-uppercase mb-2 d-block">Transaction Narration</label>
                    <p class="mb-0 text-sm fw-500 text-dark"><?php echo htmlspecialchars($tx['narration'] ?: 'No additional narration provided.'); ?></p>
                </div>

                <div class="mt-5 pt-4 text-center">
                    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm px-4 fw-bold mx-1"><i class="fa-solid fa-print me-2"></i> Print Receipt</button>
                    <a href="transactions.php" class="btn btn-primary btn-sm px-4 fw-bold mx-1">Back to History</a>
                </div>
            </div>
            <div class="bg-light p-4 text-center">
                <p class="text-xs text-muted mb-0 fw-bold">SwiftCapital Secure Transaction Protocol v1.4.2</p>
            </div>
        </div>
    </div>

</body>
</html>
