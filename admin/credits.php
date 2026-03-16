<?php 
require_once '../includes/db.php';
require_once '../includes/admin-check.php'; 

// Fetch Stats for Credits
$pending_credits = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE type='Deposit' AND status='Pending'");
$pending_credits->execute();
$pending_count = $pending_credits->fetchColumn();

// Search and Filter logic
$search = $_GET['search'] ?? '';

$where_clauses = ["t.type = 'Deposit'"];
$params = [];

if ($search) {
    $where_clauses[] = "(t.txn_hash LIKE ? OR u.email LIKE ? OR u.name LIKE ? OR u.lastname LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$status_filter = $_GET['status'] ?? 'Pending';
if ($status_filter != 'All') {
    $where_clauses[] = "t.status = ?";
    $params[] = $status_filter;
}

$where_sql = "WHERE " . implode(" AND ", $where_clauses);

$limit = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT t.*, u.name, u.lastname, u.email, u.account_number 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        $where_sql
        ORDER BY t.created_at DESC 
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$credits = $stmt->fetchAll();

$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM transactions t JOIN users u ON t.user_id = u.id $where_sql");
$total_stmt->execute($params);
$total_count = $total_stmt->fetchColumn();
$total_pages = ceil($total_count / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit/Deposit Requests - SwiftCapital Admin</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .proof-thumb {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.2s;
            border: 1px solid #e2e8f0;
        }
        .proof-thumb:hover {
            transform: scale(1.1);
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-pending { background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; }
        .status-active { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
        .status-blocked { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }
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
            <a href="credits.php" class="nav-link active">
                <i class="fa-solid fa-circle-dollar-to-slot"></i> Credit Requests
            </a>
            <a href="loans.php" class="nav-link">
                <i class="fa-solid fa-hand-holding-dollar"></i> Loan Requests
            </a>
            <a href="irs.php" class="nav-link">
                <i class="fa-solid fa-file-invoice-dollar"></i> IRS Refunds
            </a>
            <a href="kyc.php" class="nav-link">
                <i class="fa-solid fa-id-card-clip"></i> KYC Verifications
            </a>
            <a href="support.php" class="nav-link">
                <i class="fa-solid fa-headset"></i> Support Tickets
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
                <h4 class="mb-0 fw-800">Deposit & Credit Requests</h4>
            </div>

            <div class="user-nav">
                <div class="admin-profile">
                    <div class="admin-avatar">A</div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold">Compliance Officer</div>
                        <div class="text-xs text-muted">Auditor</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-padding">
            
            <div class="row mb-4 align-items-center">
                <div class="col-md-6">
                    <div class="d-flex gap-3 align-items-center">
                        <h5 class="mb-0 fw-800">Pending Approvals</h5>
                        <div class="badge bg-danger rounded-pill fw-800 px-3 py-2"><?php echo $pending_count; ?> Awaiting</div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <form class="d-flex gap-2 justify-content-md-end" method="GET">
                        <select name="status" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                            <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Completed" <?php echo $status_filter == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="Cancelled" <?php echo $status_filter == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            <option value="All" <?php echo $status_filter == 'All' ? 'selected' : ''; ?>>All History</option>
                        </select>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search hash, user..." value="<?php echo htmlspecialchars($search); ?>" style="width: 200px;">
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Search</button>
                    </form>
                </div>
            </div>

            <div class="data-table-card">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Submission Date</th>
                                <th>Client Details</th>
                                <th>Amount (USD)</th>
                                <th>Method</th>
                                <th>Proof</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($credits as $tx): ?>
                            <tr>
                                <td class="text-sm fw-600 text-muted"><?php echo date('M d, Y H:i', strtotime($tx['created_at'])); ?></td>
                                <td>
                                    <div class="fw-800"><?php echo htmlspecialchars($tx['name'] . ' ' . $tx['lastname']); ?></div>
                                    <div class="text-xs text-muted"><?php echo htmlspecialchars($tx['email']); ?></div>
                                    <div class="text-xs text-primary fw-bold">ACC: <?php echo $tx['account_number']; ?></div>
                                </td>
                                <td>
                                    <div class="fw-900 text-dark" style="font-size: 1.1rem;">$<?php echo number_format($tx['amount'], 2); ?></div>
                                </td>
                                <td>
                                    <div class="text-xs fw-800 text-uppercase"><?php echo $tx['method']; ?></div>
                                    <div class="text-xs text-muted">Hash: #<?php echo substr($tx['txn_hash'], 0, 8); ?>...</div>
                                </td>
                                <td>
                                    <?php if($tx['proof']): ?>
                                        <a href="../<?php echo $tx['proof']; ?>" target="_blank">
                                            <img src="../<?php echo $tx['proof']; ?>" class="proof-thumb shadow-sm" alt="Payment Proof">
                                        </a>
                                    <?php else: ?>
                                        <span class="text-xs text-muted fw-bold italic">No Proof</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $s_cls = 'status-pending';
                                    if($tx['status'] == 'Completed') $s_cls = 'status-active';
                                    if($tx['status'] == 'Cancelled') $s_cls = 'status-blocked';
                                    ?>
                                    <span class="status-badge <?php echo $s_cls; ?>"><?php echo $tx['status']; ?></span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm dropdown-toggle no-caret" data-bs-toggle="dropdown">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <?php if($tx['status'] == 'Pending'): ?>
                                                <li><button class="dropdown-item py-2 text-success fw-bold" onclick="updateStatus(<?php echo $tx['id']; ?>, 'Completed')"><i class="fa-solid fa-check me-2"></i> Approve & Credit</button></li>
                                                <li><button class="dropdown-item py-2 text-danger fw-bold" onclick="updateStatus(<?php echo $tx['id']; ?>, 'Cancelled')"><i class="fa-solid fa-xmark me-2"></i> Decline Transaction</button></li>
                                                <li><hr class="dropdown-divider"></li>
                                            <?php endif; ?>
                                            <li><a class="dropdown-item py-2" href="transaction-view.php?id=<?php echo $tx['id']; ?>"><i class="fa-solid fa-eye me-2"></i> Audit Details</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer bg-white border-top p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-xs text-muted fw-800">PAGINATION: PAGE <?php echo $page; ?> OF <?php echo $total_pages; ?></div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>">Previous</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#"><?php echo $page; ?></a></li>
                                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

        </div>

        <footer class="mt-auto py-4 px-4 border-top bg-white text-center text-muted" style="font-size: 0.85rem;">
            SwiftCapital Compliance Unit &copy; 2026. Internal System.
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function updateStatus(id, status) {
        if (!confirm('Proceed with ' + (status === 'Completed' ? 'approval and account crediting' : 'rejection') + '?')) return;

        fetch('tx-process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, status: status })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('Error: ' + data.message);
        });
    }
    </script>
</body>
</html>
