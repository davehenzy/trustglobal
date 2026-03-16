<?php 
require_once '../includes/admin-check.php'; 

// Notification Center Logic
$notifs_stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$notifs_stmt->execute([$_SESSION['user_id']]);
$all_notifs = $notifs_stmt->fetchAll();
$unread_count = 0;
foreach($all_notifs as $n) if(!$n['is_read']) $unread_count++;

function time_ago($timestamp) {
    if(!$timestamp) return '';
    $time = time() - strtotime($timestamp);
    if ($time < 1) return 'Just now';
    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );
    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'').' ago';
    }
}

// Fetch Stats
$today = date('Y-m-d');
$is_sub = ($_SESSION['role'] === 'Sub-Admin');
$admin_id = (int)$_SESSION['user_id'];

if ($is_sub) {
    $today_inflow = $pdo->prepare("SELECT SUM(t.amount) FROM transactions t JOIN users u ON t.user_id = u.id WHERE DATE(t.created_at) = ? AND (t.type='Deposit' OR t.type='Credit') AND t.status='Completed' AND u.assigned_admin_id = ?");
    $today_inflow->execute([$today, $admin_id]);
    $today_inflow = $today_inflow->fetchColumn() ?: 0;

    $total_volume_24h = $pdo->prepare("SELECT SUM(t.amount) FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.created_at >= NOW() - INTERVAL 1 DAY AND t.status='Completed' AND u.assigned_admin_id = ?");
    $total_volume_24h->execute([$admin_id]);
    $total_volume_24h = $total_volume_24h->fetchColumn() ?: 0;

    $pending_requests = $pdo->prepare("SELECT COUNT(*) FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.status='Pending' AND u.assigned_admin_id = ?");
    $pending_requests->execute([$admin_id]);
    $pending_requests = $pending_requests->fetchColumn();
} else {
    $today_inflow = $pdo->query("SELECT SUM(amount) FROM transactions WHERE DATE(created_at) = '$today' AND (type='Deposit' OR type='Credit') AND status='Completed'")->fetchColumn() ?: 0;
    $total_volume_24h = $pdo->query("SELECT SUM(amount) FROM transactions WHERE created_at >= NOW() - INTERVAL 1 DAY AND status='Completed'")->fetchColumn() ?: 0;
    $pending_requests = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status='Pending'")->fetchColumn();
}

// Search and Filter logic
$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';

$where_clauses = [];
$params = [];

if ($search) {
    $where_clauses[] = "(t.txn_hash LIKE ? OR u.email LIKE ? OR u.name LIKE ? OR u.lastname LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($type && $type != 'Type') {
    $where_clauses[] = "t.type = ?";
    $params[] = $type;
}

if ($status && $status != 'Status') {
    $status_val = $status == 'Success' ? 'Completed' : ($status == 'Cancelled' ? 'Cancelled' : 'Pending');
    $where_clauses[] = "t.status = ?";
    $params[] = $status_val;
}

if ($is_sub) {
    $where_clauses[] = "u.assigned_admin_id = ?";
    $params[] = $admin_id;
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT t.*, u.name, u.lastname, u.email, u.profile_pic 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        $where_sql
        ORDER BY t.created_at DESC 
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

$total_transactions = $pdo->prepare("SELECT COUNT(*) FROM transactions t JOIN users u ON t.user_id = u.id $where_sql");
$total_transactions->execute($params);
$total_count = $total_transactions->fetchColumn();
$total_pages = ceil($total_count / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - SwiftCapital Admin</title>
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
            <a href="credits.php" class="nav-link">
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
                <h4 class="mb-0 fw-800">Financial Ledger</h4>
            </div>

            <div class="user-nav" style="gap:20px;">
                <div class="notification-bell" id="notifBell" style="cursor:pointer;">
                    <i class="fa-solid fa-bell fs-5"></i>
                    <?php if($unread_count > 0): ?>
                    <span class="notification-dot"></span>
                    <?php endif; ?>

                    <div class="notification-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <h6>Notifications</h6>
                            <span class="badge bg-indigo-light text-primary text-xs unread-badge"><?php echo $unread_count; ?> New</span>
                        </div>
                        <div class="notif-list">
                            <?php if(empty($all_notifs)): ?>
                                <div class="p-4 text-center text-muted">
                                    <i class="fa-solid fa-bell-slash d-block mb-2 opacity-25"></i>
                                    <span class="text-xs fw-600">No notifications yet</span>
                                </div>
                            <?php else: ?>
                                <?php foreach($all_notifs as $n): 
                                    $icon = 'fa-bell';
                                    $bg = 'bg-light';
                                    if($n['type'] == 'Transaction') { $icon = 'fa-money-bill-transfer'; $bg = 'bg-emerald-light'; }
                                    if($n['type'] == 'Loan') { $icon = 'fa-hand-holding-dollar'; $bg = 'bg-indigo-light'; }
                                    if($n['type'] == 'KYC') { $icon = 'fa-id-card-shield'; $bg = 'bg-amber-light'; }
                                    if($n['type'] == 'System') { $icon = 'fa-triangle-exclamation'; $bg = 'bg-rose-light'; }
                                ?>
                                <a href="#" class="notif-item <?php echo !$n['is_read'] ? 'unread' : ''; ?>">
                                    <div class="notif-icon <?php echo $bg; ?>"><i class="fa-solid <?php echo $icon; ?>"></i></div>
                                    <div class="notif-content text-start">
                                        <b class="title"><?php echo htmlspecialchars($n['title']); ?></b>
                                        <span class="msg"><?php echo htmlspecialchars($n['message']); ?></span>
                                        <span class="time"><?php echo time_ago($n['created_at']); ?></span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="notif-footer">
                            <a href="#">Clear all notifications</a>
                        </div>
                    </div>
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

        <!-- Content Area -->
        <div class="content-padding">
            
            <!-- Quick Stats Overview -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Today's Inflow</p>
                            <h4 class="mb-0 fw-800">$<?php echo number_format($today_inflow, 2); ?></h4>
                        </div>
                        <div class="stat-icon bg-emerald-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-arrow-trend-up"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Total Volume (24h)</p>
                            <h4 class="mb-0 fw-800">$<?php echo number_format($total_volume_24h, 2); ?></h4>
                        </div>
                        <div class="stat-icon bg-indigo-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-chart-line"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Pending Requests</p>
                            <h4 class="mb-0 fw-800"><?php echo $pending_requests; ?></h4>
                        </div>
                        <div class="stat-icon bg-amber-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-hourglass-half"></i></div>
                    </div>
                </div>
            </div>

            <!-- Filter Actions -->
            <form method="GET" class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div class="d-flex gap-2 flex-wrap">
                    <div class="input-group" style="max-width: 320px;">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Reference, Email, or Name..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <select name="type" class="form-select" style="max-width: 140px;" onchange="this.form.submit()">
                        <option selected>Type</option>
                        <option <?php echo $type == 'Deposit' ? 'selected' : ''; ?>>Deposit</option>
                        <option <?php echo $type == 'Withdrawal' ? 'selected' : ''; ?>>Withdrawal</option>
                        <option <?php echo $type == 'Transfer' ? 'selected' : ''; ?>>Transfer</option>
                        <option <?php echo $type == 'Credit' ? 'selected' : ''; ?>>Credit</option>
                        <option <?php echo $type == 'Debit' ? 'selected' : ''; ?>>Debit</option>
                    </select>
                    <select name="status" class="form-select" style="max-width: 140px;" onchange="this.form.submit()">
                        <option selected>Status</option>
                        <option <?php echo $status == 'Success' ? 'selected' : ''; ?>>Success</option>
                        <option <?php echo $status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option <?php echo $status == 'Failed' ? 'selected' : ''; ?>>Failed</option>
                    </select>
                    <?php if ($search || ($type && $type != 'Type') || ($status && $status != 'Status')): ?>
                        <a href="transactions.php" class="btn btn-outline-secondary">Clear</a>
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        Apply Filters
                    </button>
                    <button type="button" onclick="window.print()" class="btn btn-outline-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-file-csv"></i>
                        Generate Archive
                    </button>
                </div>
            </form>

            <!-- Transactions Table -->
            <div class="data-table-card mt-0">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Transaction Ref</th>
                                <th>Client Details</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Timestamp</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted fw-600">No transactions found.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td><span class="text-xs fw-mono text-uppercase bg-light px-2 py-1 rounded">#<?php echo $tx['txn_hash']; ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="admin-avatar" style="width: 32px; height: 32px; font-size: 0.7rem;">
                                            <?php if(!empty($tx['profile_pic'])): ?>
                                                <img src="../assets/uploads/profiles/<?php echo $tx['profile_pic']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
                                            <?php else: ?>
                                                <?php echo strtoupper(substr($tx['name'], 0, 1) . substr($tx['lastname'], 0, 1)); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo $tx['name'] . ' ' . $tx['lastname']; ?></div>
                                            <div class="text-xs text-muted"><?php echo $tx['email']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-800 <?php echo in_array($tx['type'], ['Deposit', 'Credit']) ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo in_array($tx['type'], ['Deposit', 'Credit']) ? '+' : '-'; ?>$<?php echo number_format($tx['amount'], 2); ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            <div class="text-sm fw-600"><?php echo $tx['method']; ?></div>
                                            <div class="text-xs text-muted"><?php echo $tx['type']; ?></div>
                                        </div>
                                        <?php if (!empty($tx['proof'])): ?>
                                            <i class="fa-solid fa-file-invoice text-primary" title="Payment Proof Attached" style="cursor:help;"></i>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                    $status_class = '';
                                    switch($tx['status']) {
                                        case 'Completed': $status_class = 'status-active'; break;
                                        case 'Pending': $status_class = 'status-pending'; break;
                                        case 'Failed': 
                                        case 'Cancelled': $status_class = 'status-blocked'; break;
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>"><?php echo $tx['status']; ?></span>
                                </td>
                                <td class="text-sm"><?php echo date('M d, H:i', strtotime($tx['created_at'])); ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="action-btn dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <?php if ($tx['status'] == 'Pending'): ?>
                                            <li><button class="dropdown-item py-2 text-success fw-bold" onclick="updateStatus(<?php echo $tx['id']; ?>, 'Completed')"><i class="fa-solid fa-check me-2"></i> Approve</button></li>
                                            <li><button class="dropdown-item py-2 text-danger fw-bold" onclick="updateStatus(<?php echo $tx['id']; ?>, 'Cancelled')"><i class="fa-solid fa-xmark me-2"></i> Reject</button></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <?php endif; ?>
                                            <li><a class="dropdown-item py-2" href="transaction-view.php?id=<?php echo $tx['id']; ?>"><i class="fa-solid fa-circle-info me-2"></i> View Details</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer bg-white border-top p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-xs text-muted fw-bold">SHOWING <?php echo count($transactions); ?> OF <?php echo $total_count; ?> TRANSACTIONS</div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>&type=<?php echo $type; ?>&status=<?php echo $status; ?>">Previous</a>
                                </li>
                                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&type=<?php echo $type; ?>&status=<?php echo $status; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>&type=<?php echo $type; ?>&status=<?php echo $status; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
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
    <script>
    function updateStatus(id, status) {
        if (!confirm('Are you sure you want to ' + (status === 'Completed' ? 'approve' : 'reject') + ' this transaction?')) return;

        fetch('tx-process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: id,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('A system error occurred.');
        });
    }
    </script>
    <style>
        @media print {
            @page { margin: 10mm; size: landscape; }
            .admin-sidebar, .top-bar, .breadcrumb-area, .stat-card, .filter-form, .btn, .pagination, .dropdown, .top-bar-shadow, th:last-child, td:last-child {
                display: none !important;
            }
            .main-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                position: absolute;
                top: 0;
                left: 0;
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
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .table {
                width: 100% !important;
                font-size: 8.5pt !important;
            }
            .table td, .table th {
                padding: 4px 8px !important;
                vertical-align: middle !important;
            }
            .admin-avatar { display: none !important; }
            .status-badge {
                border: 1px solid #ddd !important;
                padding: 2px 6px !important;
                font-size: 7.5pt !important;
                color: #000 !important;
                background: none !important;
            }
            .text-xs { font-size: 7.5pt !important; }
            .fw-800 { font-weight: 700 !important; }
        }
    </style>
    <div class="toast-container" id="toastContainer"></div>

    <script>
        // Notification & Toast System
        const bell = document.getElementById('notifBell');
        const dropdown = document.getElementById('notifDropdown');
        const container = document.getElementById('toastContainer');
        
        if(bell) {
            bell.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });
        }

        if(dropdown) {
            dropdown.addEventListener('click', (e) => e.stopPropagation());
        }

        document.addEventListener('click', () => dropdown.classList.remove('show'));

        function showToast(title, message, type = 'success') {
            if(!container) return;
            
            const toast = document.createElement('div');
            toast.className = `toast-notification ${type}`;
            
            let icon = 'fa-circle-check';
            if(type === 'error') icon = 'fa-circle-xmark';
            if(type === 'warning') icon = 'fa-triangle-exclamation';

            toast.innerHTML = `
                <div class="toast-icon ${type === 'success' ? 'bg-emerald-light' : (type === 'error' ? 'bg-rose-light' : 'bg-amber-light')}">
                    <i class="fa-solid ${icon}"></i>
                </div>
                <div class="toast-body">
                    <b>${title}</b>
                    <span>${message}</span>
                </div>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'fadeOut 0.5s forwards';
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        }

        // Status update feedback via Toast
        function updateStatus(id, status) {
            fetch('tx-process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `tx_id=${id}&status=${status}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    showToast('Transaction Updated', `Entry #${id} status set to ${status}.`, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('Update Failed', data.message || 'System error occurred.', 'error');
                }
            })
            .catch(err => {
                showToast('Network Error', 'Could not connect to gateway.', 'error');
            });
        }
    </script>
</body>
</html>
