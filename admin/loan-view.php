<?php require_once '../includes/admin-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Audit - SwiftCapital Admin</title>
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
            <a href="transactions.php" class="nav-link">
                <i class="fa-solid fa-money-bill-transfer"></i> Transactions
            </a>
            <a href="credits.php" class="nav-link">
                <i class="fa-solid fa-circle-dollar-to-slot"></i> Credit Requests
            </a>
            <a href="loans.php" class="nav-link active">
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
                <h4 class="mb-0 fw-800">Credit Audit: #LOR-88219</h4>
            </div>

            <div class="user-nav">
                <a href="loans.php" class="btn btn-light-indigo btn-sm fw-800 px-3" style="border-radius: 10px;"><i class="fa-solid fa-arrow-left me-1"></i> Back to Ledger</a>
            </div>
        </div>

        <?php 
        $loan_id = $_GET['id'] ?? null;
        if (!$loan_id) {
            header("Location: loans.php");
            exit();
        }

        $stmt = $pdo->prepare("SELECT l.*, u.name, u.lastname, u.email, u.account_number, u.id as user_id 
                            FROM loans l 
                            JOIN users u ON l.user_id = u.id 
                            WHERE l.id = ?");
        $stmt->execute([$loan_id]);
        $loan = $stmt->fetch();

        if (!$loan) {
            header("Location: loans.php");
            exit();
        }
        ?>

        <!-- Content Area -->
        <div class="content-padding">
            
            <div class="row g-4">
                <!-- Loan Summary Left -->
                <div class="col-lg-8">
                    <div class="data-table-card p-5 border-0 bg-white" style="border-radius: 24px;">
                        <div class="d-flex justify-content-between align-items-start mb-5">
                            <div>
                                <h5 class="fw-800 mb-2"><?php echo htmlspecialchars($loan['loan_type']); ?></h5>
                                <?php 
                                $s = $loan['status'];
                                $s_cls = 'status-pending';
                                if($s == 'Approved' || $s == 'Disbursed') $s_cls = 'status-active';
                                if($s == 'Rejected') $s_cls = 'status-blocked';
                                ?>
                                <span class="status-badge <?php echo $s_cls; ?> px-3 py-2 fw-800" style="border-radius: 10px;"><?php echo $s; ?></span>
                            </div>
                            <div class="text-end">
                                <h2 class="fw-800 mb-0 text-primary" style="letter-spacing: -1px;">$<?php echo number_format($loan['amount'], 2); ?></h2>
                                <p class="text-xs fw-600 text-muted mb-0">Requested Principal Amount</p>
                            </div>
                        </div>

                        <div class="row g-4 mb-5">
                            <div class="col-md-4">
                                <div class="p-4 bg-light-soft rounded-4 border-0" style="background: #f8fafc;">
                                    <p class="text-xs text-muted text-uppercase fw-800 mb-2">Loan Term</p>
                                    <h6 class="fw-800 mb-0"><?php echo $loan['term_months']; ?> Months</h6>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-4 bg-light-soft rounded-4 border-0" style="background: #f8fafc;">
                                    <p class="text-xs text-muted text-uppercase fw-800 mb-2">APR Percentage</p>
                                    <h6 class="fw-800 mb-0"><?php echo $loan['interest_rate']; ?>% Fixed</h6>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-4 bg-light-soft rounded-4 border-0" style="background: #f8fafc;">
                                    <p class="text-xs text-muted text-uppercase fw-800 mb-2">Monthly Payable</p>
                                    <h6 class="fw-800 mb-0 text-dark">$<?php echo number_format($loan['monthly_payable'], 2); ?></h6>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h6 class="fw-800 text-xs text-uppercase text-muted mb-3">Enterprise Narrative / Purpose</h6>
                            <div class="p-4 rounded-4 text-sm fw-500 shadow-inner" style="background: #f8fafc; line-height: 1.6;">
                                "<?php echo nl2br(htmlspecialchars($loan['purpose'])); ?>"
                            </div>
                        </div>

                        <?php if ($loan['status'] == 'Pending'): ?>
                        <div class="d-flex gap-3">
                            <button class="btn btn-primary px-5 py-3 fw-800 flex-grow-1" style="border-radius: 15px;" onclick="updateLoanStatus(<?php echo $loan['id']; ?>, 'Disbursed')"><i class="fa-solid fa-check-circle me-2"></i> Authorize Disbursement</button>
                            <button class="btn btn-rose-light text-danger px-5 py-3 fw-800 flex-grow-1" style="border-radius: 15px;" onclick="updateLoanStatus(<?php echo $loan['id']; ?>, 'Rejected')"><i class="fa-solid fa-times-circle me-2"></i> Reject Proposal</button>
                        </div>
                        <?php else: ?>
                            <div class="alert alert-info rounded-4 border-0 py-3">
                                <i class="fa-solid fa-info-circle me-2"></i> This loan application has already been <b><?php echo $loan['status']; ?></b>.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Client Side Info Right -->
                <div class="col-lg-4">
                    <div class="data-table-card p-5 border-0 bg-white mb-4" style="border-radius: 24px;">
                        <h6 class="fw-800 mb-4 text-xs text-uppercase text-muted">Client Credit Profile</h6>
                        <div class="d-flex align-items-center mb-5">
                            <div class="admin-avatar me-4 bg-indigo text-white shadow" style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: 800; border-radius: 18px;">
                                <?php echo strtoupper(substr($loan['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h6 class="fw-800 mb-1"><?php echo htmlspecialchars($loan['name'] . ' ' . $loan['lastname']); ?></h6>
                                <p class="text-xs fw-600 text-muted mb-0"><?php echo htmlspecialchars($loan['email']); ?></p>
                            </div>
                        </div>

                        <div class="list-group list-group-flush mb-5">
                            <div class="list-group-item d-flex justify-content-between px-0 py-3 border-light">
                                <span class="text-muted fw-600">Account No</span>
                                <span class="fw-800 text-mono"><?php echo $loan['account_number']; ?></span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between px-0 py-3 border-light">
                                <span class="text-muted fw-600">Application Date</span>
                                <span class="fw-800"><?php echo date('M d, Y', strtotime($loan['created_at'])); ?></span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between px-0 py-3 border-light">
                                <span class="text-muted fw-600">Loan Reference</span>
                                <span class="fw-800">#LOR-<?php echo str_pad($loan['id'], 6, '0', STR_PAD_LEFT); ?></span>
                            </div>
                        </div>

                        <h6 class="fw-800 mb-4 text-xs text-uppercase text-muted">Internal Audit Notes</h6>
                        <form method="POST" action="loan-notes.php">
                            <input type="hidden" name="loan_id" value="<?php echo $loan['id']; ?>">
                            <textarea name="notes" class="form-control bg-light border-0 fw-500 p-4 mb-4" rows="4" style="border-radius: 15px; font-size: 0.85rem;" placeholder="Initialize administrative risk assessment..."><?php echo htmlspecialchars($loan['admin_notes']); ?></textarea>
                            <button type="submit" class="btn btn-dark w-100 py-3 fw-800" style="border-radius: 15px;">Commit Audit Entry</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    <script>
    function updateLoanStatus(id, status) {
        if (!confirm('Are you sure you want to change status to ' + status + '?')) return;

        fetch('loan-process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, status: status })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Success: ' + data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
    </script>

        <!-- Footer -->
        <footer class="mt-auto py-5 px-4 border-top bg-white text-center text-muted" style="font-size: 0.85rem;">
            SwiftCapital Admin © 2026. Internal System Only.
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .shadow-inner {
            box-shadow: inset 0 2px 8px 0 rgba(0, 0, 0, 0.05);
        }
    </style>
</body>
</html>
