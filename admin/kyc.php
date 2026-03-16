<?php 
require_once '../includes/db.php';
require_once '../includes/admin-check.php'; 

// Fetch Stats
$pending_count = $pdo->query("SELECT COUNT(*) FROM kyc_verifications WHERE status = 'Pending'")->fetchColumn();
$approved_24h = $pdo->query("SELECT COUNT(*) FROM kyc_verifications WHERE status = 'Verified' AND created_at >= NOW() - INTERVAL 1 DAY")->fetchColumn();
$rejected_today = $pdo->query("SELECT COUNT(*) FROM kyc_verifications WHERE status = 'Rejected' AND DATE(created_at) = CURDATE()")->fetchColumn();
$total_vetted = $pdo->query("SELECT COUNT(*) FROM kyc_verifications WHERE status != 'Pending'")->fetchColumn();
$total_requests = $pdo->query("SELECT COUNT(*) FROM kyc_verifications")->fetchColumn();
$compliance_rate = $total_requests > 0 ? round(($total_vetted / $total_requests) * 100, 1) : 100;

// Fetch KYC Queue
$search = $_GET['search'] ?? '';
$where_sql = "";
$params = [];
if ($search) {
    $where_sql = "WHERE u.email LIKE ? OR u.name LIKE ? OR u.lastname LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
}

$stmt = $pdo->prepare("SELECT k.*, u.name, u.lastname, u.email FROM kyc_verifications k JOIN users u ON k.user_id = u.id $where_sql ORDER BY k.created_at DESC");
$stmt->execute($params);
$kyc_queue = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KYC Queue - SwiftCapital Admin</title>
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
            <a href="loans.php" class="nav-link">
                <i class="fa-solid fa-hand-holding-dollar"></i> Loan Requests
            </a>
            <a href="irs.php" class="nav-link">
                <i class="fa-solid fa-file-invoice-dollar"></i> IRS Refunds
            </a>
            <a href="kyc.php" class="nav-link active">
                <i class="fa-solid fa-id-card-clip"></i> KYC Verifications
            </a>
            <a href="support.php" class="nav-link">
                <i class="fa-solid fa-headset"></i> Support Tickets
            </a>
            <?php if ($_SESSION['role'] === 'Super Admin'): ?>
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
                <h4 class="mb-0 fw-800">Compliance & KYC</h4>
            </div>

            <div class="user-nav">
                <div class="notification-bell">
                    <i class="fa-solid fa-bell fs-5"></i>
                    <span class="notification-dot"></span>
                </div>
                
                <div class="admin-profile">
                    <div class="admin-avatar"><?php echo strtoupper(substr($_SESSION["user_name"] ?? "A", 0, 1)); ?></div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold text-sm"><?php echo $_SESSION["user_name"] ?? "Admin"; ?></div>
                        <div class="text-xs text-muted"><?php echo $_SESSION["role"] ?? "Administrator"; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-padding">
            
            <!-- KYC Dash -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Pending Approval</p>
                            <h4 class="mb-0 fw-800"><?php echo $pending_count; ?></h4>
                        </div>
                        <div class="stat-icon bg-amber-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-hourglass-start"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Approved (24h)</p>
                            <h4 class="mb-0 fw-800"><?php echo $approved_24h; ?></h4>
                        </div>
                        <div class="stat-icon bg-emerald-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-check-double"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Rejected Today</p>
                            <h4 class="mb-0 fw-800"><?php echo str_pad($rejected_today, 2, '0', STR_PAD_LEFT); ?></h4>
                        </div>
                        <div class="stat-icon bg-rose-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-ban"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Compliance Rate</p>
                            <h4 class="mb-0 fw-800"><?php echo $compliance_rate; ?>%</h4>
                        </div>
                        <div class="stat-icon bg-indigo-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-shield-halved"></i></div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- KYC Table -->
                <div class="col-lg-12">
                    <div class="data-table-card mt-0">
                        <div class="card-header border-0 bg-transparent py-4 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-800">Verification Queue</h5>
                            <form class="d-flex gap-2" method="GET">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0" placeholder="Filter by email or name..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">Filter</button>
                                <?php if($search): ?>
                                    <a href="kyc.php" class="btn btn-outline-secondary btn-sm px-3 fw-bold">Clear</a>
                                <?php endif; ?>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Applicant Profile</th>
                                        <th>Document Type</th>
                                        <th>Verification Status</th>
                                        <th>Submission Date</th>
                                        <th>Resolution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($kyc_queue)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted fw-600">No verification requests found.</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach($kyc_queue as $k): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="admin-avatar" style="width: 36px; height: 36px; font-size: 0.8rem;"><?php echo strtoupper(substr($k['name'], 0, 1) . substr($k['lastname'], 0, 1)); ?></div>
                                                <div>
                                                    <div class="fw-bold"><?php echo $k['name'] . ' ' . $k['lastname']; ?></div>
                                                    <div class="text-xs text-muted"><?php echo $k['email']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-600"><?php echo $k['document_type']; ?></div>
                                            <div class="text-xs text-primary">#KYC-<?php echo str_pad($k['id'], 4, '0', STR_PAD_LEFT); ?></div>
                                        </td>
                                        <td>
                                            <?php 
                                            $status_class = '';
                                            switch($k['status']) {
                                                case 'Verified': $status_class = 'status-active'; break;
                                                case 'Pending': $status_class = 'status-pending'; break;
                                                case 'Rejected': $status_class = 'status-blocked'; break;
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $status_class; ?>"><?php echo $k['status'] == 'Verified' ? 'Approved' : ($k['status'] == 'Pending' ? 'In Review' : 'Rejected'); ?></span>
                                        </td>
                                        <td class="text-sm"><?php echo date('M d, Y (H:i)', strtotime($k['created_at'])); ?></td>
                                        <td><a href="kyc-view.php?id=<?php echo $k['id']; ?>" class="btn btn-primary btn-sm px-3 fw-bold text-xs" style="border-radius: 8px;">Audit Review</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white border-top p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-xs text-muted fw-bold uppercase">Showing <?php echo count($kyc_queue); ?> verification requests</div>
                                <a href="#" class="text-primary text-xs fw-bold text-decoration-none">View Archive <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
                            </div>
                        </div>
                    </div>
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
