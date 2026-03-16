<?php 
require_once '../includes/admin-check.php'; 

if (!isset($_GET['id'])) {
    header("Location: transactions.php");
    exit();
}

$tx_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT t.*, u.name, u.lastname, u.email, u.account_number, u.account_type, u.profile_pic 
                      FROM transactions t 
                      JOIN users u ON t.user_id = u.id 
                      WHERE t.id = ?");
$stmt->execute([$tx_id]);
$tx = $stmt->fetch();

if (!$tx) {
    header("Location: transactions.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt - SwiftCapital Admin</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="admin-sidebar">
        <div class="brand-area">
            <div class="brand-icon"><i class="fa-solid fa-shield-halved"></i></div>
            <div class="brand-name">SwiftAdmin</div>
        </div>

        <div class="nav-links">
            <a href="index.php" class="nav-link">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="users.php" class="nav-link">
                <i class="fa-solid fa-users"></i> Users Management
            </a>
            <a href="transactions.php" class="nav-link active">
                <i class="fa-solid fa-money-bill-transfer"></i> Transactions
            </a>
            <a href="loans.php" class="nav-link">
                <i class="fa-solid fa-hand-holding-dollar"></i> Loan Requests
            </a>
            <a href="kyc.php" class="nav-link">
                <i class="fa-solid fa-id-card-clip"></i> KYC Verifications
            </a>
            <a href="support.php" class="nav-link">
                <i class="fa-solid fa-headset"></i> Support Tickets
            </a>
            <?php if (in_array($_SESSION['role'] ?? '', ['Super Admin', 'Admin'])): ?>
            <a href="cms.php" class="nav-link">
                <i class="fa-solid fa-pen-nib"></i> Frontend CMS
            </a>
            <a href="settings.php" class="nav-link">
                <i class="fa-solid fa-gear"></i> System Settings
            </a>
            <?php endif; ?>
            
            <div class="mt-auto" style="position: absolute; bottom: 20px; width: 100%;">
                <a href="../logout.php" class="nav-link text-danger">
                    <i class="fa-solid fa-power-off"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="breadcrumb-area">
                <h4 class="mb-0 fw-800">Financial Audit Artifact</h4>
            </div>

            <div class="user-nav">
                <a href="transactions.php" class="btn btn-light-indigo btn-sm fw-800 px-3" style="border-radius: 10px;"><i class="fa-solid fa-arrow-left me-2"></i> Back to History</a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-padding">
            
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <!-- Receipt Card -->
                    <div class="data-table-card border-0 bg-white overflow-hidden shadow-lg" style="border-radius: 30px;">
                        <div class="p-5">
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
                                <h1 class="fw-800 mb-2" style="letter-spacing: -2px; font-size: 3.5rem;">$<?php echo number_format($tx['amount'], 2); ?></h1>
                                <p class="<?php echo ($tx['status'] == 'Completed') ? 'text-success' : (($tx['status'] == 'Pending') ? 'text-warning' : 'text-danger'); ?> fw-800 text-uppercase mb-4" style="letter-spacing: 3px; font-size: 0.85rem;">
                                    <?php echo ($tx['status'] == 'Completed') ? 'Ledger Entry Confirmed' : (($tx['status'] == 'Pending') ? 'Transaction in Review' : 'Transaction Refused'); ?>
                                </p>
                                <div class="p-2 border rounded-3 d-inline-block bg-light-soft px-4">
                                    <span class="text-xs fw-800 text-muted">AUDIT HASH: <strong>#<?php echo $tx['txn_hash']; ?></strong></span>
                                </div>
                            </div>

                            <hr class="my-5" style="border-style: dashed; opacity: 0.1;">

                            <!-- Transaction Details -->
                            <div class="row g-5 pt-3">
                                <div class="col-md-6 text-start">
                                    <p class="text-xs text-muted text-uppercase fw-800 mb-2">Settlement Party</p>
                                    <div class="d-flex align-items-center">
                                        <div class="admin-avatar me-3 bg-indigo text-white shadow-sm" style="width: 40px; height: 40px; border-radius: 12px; font-weight: 800;">
                                            <?php if(!empty($tx['profile_pic'])): ?>
                                                <img src="../assets/uploads/profiles/<?php echo $tx['profile_pic']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
                                            <?php else: ?>
                                                <?php echo strtoupper(substr($tx['name'], 0, 1) . substr($tx['lastname'], 0, 1)); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <h6 class="fw-800 mb-0"><?php echo $tx['name'] . ' ' . $tx['lastname']; ?></h6>
                                            <p class="text-xs fw-600 text-muted mb-0"><?php echo $tx['account_type']; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end text-start">
                                    <p class="text-xs text-muted text-uppercase fw-800 mb-2">Protocol Applied</p>
                                    <h6 class="fw-800 mb-1"><?php echo $tx['method']; ?></h6>
                                    <p class="text-xs fw-600 text-muted mb-0"><?php echo $tx['type']; ?></p>
                                </div>

                                <div class="col-md-12">
                                    <div class="p-4 rounded-4 shadow-inner" style="background: #f8fafc;">
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="text-sm fw-600 text-muted">Velocity Type</span>
                                            <span class="text-sm fw-800"><?php echo $tx['type']; ?> Settlement</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="text-sm fw-600 text-muted">Account Number</span>
                                            <span class="text-sm fw-800"><?php echo $tx['account_number']; ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="text-sm fw-600 text-muted">Protocol Timestamp</span>
                                            <span class="text-sm fw-800"><?php echo date('M d, Y H:i', strtotime($tx['created_at'])); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="text-sm fw-600 text-muted">System Validation</span>
                                            <span class="text-sm fw-800 text-success"><i class="fa-solid fa-shield-check me-2"></i> Verified by Vanguard AI</span>
                                        </div>
                                        <div class="d-flex justify-content-between mt-4 pt-4 border-top border-light">
                                            <span class="fw-800 text-muted">Total Net Settlement</span>
                                            <span class="fw-800 text-2xl text-primary" style="font-size: 1.5rem;">$<?php echo number_format($tx['amount'], 2); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-5">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Compliance Narration</label>
                                    <div class="p-4 rounded-4 text-sm fw-500 italic text-muted border-0 bg-light-soft" style="background: #f1f5f9; border-radius: 15px;">
                                        "<?php echo $tx['narration']; ?>"
                                    </div>
                                </div>

                                <?php if (!empty($tx['proof'])): ?>
                                <div class="col-md-12 mt-4">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Uploaded Payment Proof</label>
                                    <div class="p-3 border rounded-4 bg-light text-center">
                                        <?php if (str_ends_with($tx['proof'], '.pdf')): ?>
                                            <a href="../<?php echo $tx['proof']; ?>" target="_blank" class="btn btn-indigo btn-sm">
                                                <i class="fa-solid fa-file-pdf me-2"></i> View PDF Proof
                                            </a>
                                        <?php else: ?>
                                            <img src="../<?php echo $tx['proof']; ?>" class="img-fluid rounded-3 shadow-sm" style="max-height: 500px; border: 1px solid #e2e8f0;" alt="Payment Proof">
                                            <div class="mt-3">
                                                <a href="../<?php echo $tx['proof']; ?>" target="_blank" class="text-xs fw-bold text-primary text-decoration-none">
                                                    <i class="fa-solid fa-expand me-1"></i> VIEW FULL IMAGE
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex gap-3 mt-5 pt-3">
                                <button onclick="window.print()" class="btn btn-outline-dark px-4 py-3 fw-800 flex-grow-1" style="border-radius: 15px;"><i class="fa-solid fa-print me-2"></i> Generate Archive</button>
                                <button class="btn btn-primary px-4 py-3 fw-800 flex-grow-1" style="border-radius: 15px;"><i class="fa-solid fa-paper-plane me-2"></i> Notify Client</button>
                                <button class="btn btn-rose-light text-danger px-4" title="Void Artifact" style="border-radius: 15px;"><i class="fa-solid fa-ban"></i></button>
                            </div>
                        </div>
                        <div class="bg-light-soft py-4 text-center text-xs fw-800 text-muted border-top border-light opacity-50">
                            OFFICIAL SWIFTCAPITAL INTERNAL AUDIT DOCUMENT â€¢ SECURITY CLASS A
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .shadow-inner {
            box-shadow: inset 0 2px 8px 0 rgba(0, 0, 0, 0.05);
        }
        .text-2xl {
            letter-spacing: -1px;
        }
        
        @media print {
            @page { 
                size: portrait;
                margin: 0mm; 
            }
            body { 
                margin: 0; 
                padding: 10mm !important; 
                background: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-size: 9pt !important;
            }
            .admin-sidebar, .top-bar, .btn, .dropdown, hr, .breadcrumb-area, .bg-light-soft.py-4 {
                display: none !important;
            }
            .main-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                width: 100% !important;
                height: auto !important;
                min-height: auto !important;
                overflow: visible !important;
            }
            .content-padding {
                padding: 0 !important;
                margin: 0 !important;
            }
            .data-table-card {
                box-shadow: none !important;
                border: 0 !important;
                margin: 0 auto !important;
                padding: 0 !important;
                page-break-inside: avoid;
            }
            .col-lg-7 {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }
            .shadow-lg, .shadow-inner, .shadow-sm {
                box-shadow: none !important;
            }
            h1 { font-size: 2.2rem !important; margin-bottom: 5mm !important; }
            .p-5 { padding: 0 !important; }
            .my-5 { margin-top: 5mm !important; margin-bottom: 5mm !important; }
            .receipt-header img { max-width: 80px !important; }
            .p-4.rounded-4 { padding: 3mm !important; }
        }
    </style>
</body>
</html>
