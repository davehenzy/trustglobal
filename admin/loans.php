<?php require_once '../includes/admin-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Requests - SwiftCapital Admin</title>
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
                <h4 class="mb-0 fw-800">Loan Requests</h4>
            </div>

            <div class="user-nav">
                <div class="notification-bell">
                    <i class="fa-solid fa-bell fs-5"></i>
                    <span class="notification-dot"></span>
                </div>
                
                <div class="admin-profile">
                    <div class="admin-avatar">
                        <?php if(!empty($_SESSION['profile_pic'])): ?>
                            <img src="../assets/uploads/profiles/<?php echo $_SESSION['profile_pic']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
                        <?php else: ?>
                            <?php echo strtoupper(substr($_SESSION["user_name"] ?? "A", 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold text-sm"><?php echo $_SESSION["user_name"] ?? "Admin"; ?></div>
                        <div class="text-xs text-muted"><?php echo $_SESSION["role"] ?? "Administrator"; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <?php 
        // Fetch Stats
        $total_active = $pdo->query("SELECT SUM(amount) FROM loans WHERE status='Disbursed'")->fetchColumn() ?: 0;
        $pending_review = $pdo->query("SELECT COUNT(*) FROM loans WHERE status='Pending'")->fetchColumn() ?: 0;
        $disbursed_today = $pdo->query("SELECT SUM(amount) FROM loans WHERE status='Disbursed' AND DATE(created_at) = CURDATE()")->fetchColumn() ?: 0;

        // Fetch Loans
        $stmt = $pdo->query("SELECT l.*, u.name, u.lastname, u.account_number, u.profile_pic 
                            FROM loans l 
                            JOIN users u ON l.user_id = u.id 
                            ORDER BY l.created_at DESC");
        $loans = $stmt->fetchAll();
        ?>

        <!-- Content Area -->
        <div class="content-padding">
            
            <!-- Loan Analytics -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Total Disbursed</p>
                            <h4 class="mb-0 fw-800">$<?php echo number_format($total_active, 2); ?></h4>
                        </div>
                        <div class="stat-icon bg-indigo-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-money-bill-wave"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Pending Review</p>
                            <h4 class="mb-0 fw-800"><?php echo $pending_review; ?> Cases</h4>
                        </div>
                        <div class="stat-icon bg-amber-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-hourglass-start"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Interest Revenue</p>
                            <h4 class="mb-0 fw-800">$<?php echo number_format($total_active * 0.065, 2); ?></h4>
                        </div>
                        <div class="stat-icon bg-emerald-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-chart-line"></i></div>
                    </div>
                </div>
                 <div class="col-md-3">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Disbursed Today</p>
                            <h4 class="mb-0 fw-800">$<?php echo number_format($disbursed_today, 2); ?></h4>
                        </div>
                        <div class="stat-icon bg-rose-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-paper-plane"></i></div>
                    </div>
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div class="d-flex gap-2 flex-wrap">
                    <div class="input-group" style="max-width: 320px;">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Client name or ID...">
                    </div>
                    <select class="form-select" style="max-width: 140px;">
                        <option selected>Status</option>
                        <option>Pending</option>
                        <option>Approved</option>
                        <option>Rejected</option>
                    </select>
                </div>
            </div>

            <!-- Loans Table -->
            <div class="data-table-card mt-0">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Client Profile</th>
                                <th>Amount & Term</th>
                                <th>Monthly Pay</th>
                                <th>Purpose</th>
                                <th>Status</th>
                                <th>Timestamp</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loans as $loan): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="admin-avatar" style="width: 38px; height: 38px; font-size: 0.8rem; background: #f1f5f9; color: #64748b;">
                                                <?php if(!empty($loan['profile_pic'])): ?>
                                                    <img src="../assets/uploads/profiles/<?php echo $loan['profile_pic']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
                                                <?php else: ?>
                                                    <?php echo strtoupper(substr($loan['name'], 0, 1)); ?>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold"><?php echo htmlspecialchars($loan['name'] . ' ' . $loan['lastname']); ?></div>
                                                <div class="text-xs text-muted">ACC: <?php echo $loan['account_number']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-800 text-dark">$<?php echo number_format($loan['amount'], 2); ?></div>
                                        <div class="text-xs text-muted"><?php echo $loan['term_months']; ?> Mos @ <?php echo $loan['interest_rate']; ?>%</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary">$<?php echo number_format($loan['monthly_payable'], 2); ?></div>
                                    </td>
                                    <td class="text-sm fw-500">
                                        <span title="<?php echo htmlspecialchars($loan['purpose']); ?>">
                                            <?php echo htmlspecialchars($loan['loan_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $s = $loan['status'];
                                        $s_cls = 'status-pending';
                                        if($s == 'Approved' || $s == 'Disbursed') $s_cls = 'status-active';
                                        if($s == 'Rejected') $s_cls = 'status-blocked';
                                        ?>
                                        <span class="status-badge <?php echo $s_cls; ?>"><?php echo $s; ?></span>
                                    </td>
                                    <td class="text-sm"><?php echo date('M d, Y', strtotime($loan['created_at'])); ?></td>
                                    <td>
                                        <a href="loan-view.php?id=<?php echo $loan['id']; ?>" class="btn btn-primary btn-sm px-3 fw-bold text-xs" style="border-radius: 8px;">Review Case</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="mt-auto py-4 px-4 border-top bg-white text-center text-muted" style="font-size: 0.85rem;">
            SwiftCapital Admin © 2026. Internal System Only.
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
