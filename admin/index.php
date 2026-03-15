<?php 
require_once '../includes/admin-check.php'; 

// Fetch Stats
$total_assets = $pdo->query("SELECT SUM(balance) FROM users")->fetchColumn() ?: 0;
$active_users = $pdo->query("SELECT COUNT(*) FROM users WHERE status='Active'")->fetchColumn();
$pending_loans_amount = $pdo->query("SELECT SUM(amount) FROM loans WHERE status='Pending'")->fetchColumn() ?: 0;
$pending_loans_count = $pdo->query("SELECT COUNT(*) FROM loans WHERE status='Pending'")->fetchColumn();
$support_tickets = $pdo->query("SELECT COUNT(*) FROM support_tickets WHERE status='Open'")->fetchColumn();

// Recent Users
$recent_users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

// System Activity Feed
$activity_sql = "
    (SELECT 'Transaction' as type, created_at, narration as detail, status FROM transactions)
    UNION ALL
    (SELECT 'New User' as type, created_at, CONCAT(name, ' ', lastname) as detail, status FROM users)
    UNION ALL
    (SELECT 'Loan' as type, created_at, loan_type as detail, status FROM loans)
    UNION ALL
    (SELECT 'Support' as type, created_at, subject as detail, status FROM support_tickets)
    ORDER BY created_at DESC LIMIT 6
";
$activities = $pdo->query($activity_sql)->fetchAll();

function time_ago($timestamp) {
    $time = time() - strtotime($timestamp);
    if ($time < 1) return 'Just now';
    $units = [
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    ];
    foreach ($units as $unit => $text) {
        if ($time < $unit) continue;
        $num = floor($time / $unit);
        return $num . ' ' . $text . (($num > 1) ? 's' : '') . ' ago';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SwiftCapital</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .stat-trend {
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 8px;
        }
        .trend-up { color: var(--success); }
        .trend-down { color: var(--danger); }

        .search-wrapper {
            position: relative;
            max-width: 300px;
        }
        .search-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }
        .search-wrapper .form-control {
            padding-left: 40px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid var(--border-color);
        }

        .quick-link-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            text-decoration: none;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.2s;
        }
        .quick-link-card:hover {
            transform: translateY(-3px);
            border-color: var(--admin-primary);
            color: var(--admin-primary);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .quick-link-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .activity-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .premium-list-item {
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .premium-list-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
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
            <a href="index.php" class="nav-link active">
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
            <a href="irs.php" class="nav-link">
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
            <div class="d-flex align-items-center gap-4">
                <div class="breadcrumb-area d-none d-md-block">
                    <h4 class="mb-0">Analytics Dashboard</h4>
                </div>
                <div class="search-wrapper">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" class="form-control" placeholder="Search data...">
                </div>
            </div>

            <div class="user-nav">
                <div class="notification-bell">
                    <i class="fa-solid fa-bell fs-5"></i>
                    <span class="notification-dot"></span>
                </div>
                
                <div class="admin-profile ms-2">
                    <div class="admin-avatar"><?php echo strtoupper(substr($_SESSION["user_name"] ?? "A", 0, 1)); ?></div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold text-sm lh-1"><?php echo $_SESSION["user_name"] ?? "Admin"; ?></div>
                        <div class="text-xs text-muted"><?php echo $_SESSION["role"] ?? "Administrator"; ?></div>
                    </div>
                    <i class="fa-solid fa-chevron-down text-muted text-xs ms-1"></i>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-padding">
            
            <!-- Quick Management Row -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <a href="user-add.php" class="quick-link-card">
                        <div class="quick-link-icon bg-indigo-light"><i class="fa-solid fa-user-plus"></i></div>
                        <div>
                            <div class="fw-bold text-sm">Enroll User</div>
                            <div class="text-xs text-muted">Register Client</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="transactions.php" class="quick-link-card">
                        <div class="quick-link-icon bg-emerald-light"><i class="fa-solid fa-circle-dollar-to-slot"></i></div>
                        <div>
                            <div class="fw-bold text-sm">Credit User</div>
                            <div class="text-xs text-muted">Fund Account</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="credits.php" class="quick-link-card">
                        <div class="quick-link-icon bg-blue-light text-primary"><i class="fa-solid fa-hourglass-half"></i></div>
                        <div>
                            <div class="fw-bold text-sm">Review Deposits</div>
                            <div class="text-xs text-muted">Approve Credits</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="kyc.php" class="quick-link-card">
                        <div class="quick-link-icon bg-amber-light"><i class="fa-solid fa-file-shield"></i></div>
                        <div>
                            <div class="fw-bold text-sm">Review KYC</div>
                            <div class="text-xs text-muted">Verify Identity</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="settings.php" class="quick-link-card">
                        <div class="quick-link-icon bg-rose-light"><i class="fa-solid fa-sliders"></i></div>
                        <div>
                            <div class="fw-bold text-sm">Settings</div>
                            <div class="text-xs text-muted">System Config</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-info">
                            <p>Total Assets</p>
                            <h3>$<?php echo number_format($total_assets / 1000000, 1); ?>M</h3>
                            <div class="stat-trend trend-up">
                                <i class="fa-solid fa-arrow-up-long"></i> +0% <span class="text-muted fw-normal ms-1">vs last month</span>
                            </div>
                        </div>
                        <div class="stat-icon bg-indigo-light">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-info">
                            <p>Active Users</p>
                            <h3><?php echo number_format($active_users); ?></h3>
                            <div class="stat-trend trend-up">
                                <i class="fa-solid fa-arrow-up-long"></i> <?php echo count($recent_users); ?> <span class="text-muted fw-normal ms-1">new today</span>
                            </div>
                        </div>
                        <div class="stat-icon bg-emerald-light">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-info">
                            <p>Pending Loans</p>
                            <h3>$<?php echo number_format($pending_loans_amount / 1000, 1); ?>k</h3>
                            <div class="stat-trend trend-down">
                                <i class="fa-solid fa-clock"></i> <?php echo $pending_loans_count; ?> <span class="text-muted fw-normal ms-1">needs review</span>
                            </div>
                        </div>
                        <div class="stat-icon bg-amber-light">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-info">
                            <p>Support Tickets</p>
                            <h3><?php echo $support_tickets; ?></h3>
                            <div class="stat-trend trend-up">
                                <i class="fa-solid fa-bolt"></i> 0 <span class="text-muted fw-normal ms-1">high priority</span>
                            </div>
                        </div>
                        <div class="stat-icon bg-rose-light">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-4 mt-1">
                <div class="col-lg-8">
                    <div class="data-table-card mt-0 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="fw-bold mb-1">Financial Inflow</h5>
                                <p class="text-xs text-muted mb-0">Daily deposit vs withdrawal volume</p>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary active">7 Days</button>
                                <button class="btn btn-outline-secondary">30 Days</button>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="data-table-card mt-0 p-4 h-100">
                        <h5 class="fw-bold mb-4">System Activity</h5>
                        <?php foreach($activities as $act): 
                            $dot_color = 'bg-primary';
                            if ($act['type'] == 'Transaction') $dot_color = 'bg-success';
                            if ($act['type'] == 'New User') $dot_color = 'bg-info';
                            if ($act['type'] == 'Loan') $dot_color = 'bg-warning';
                            if ($act['type'] == 'Support') $dot_color = 'bg-rose';
                        ?>
                        <div class="premium-list-item">
                            <div class="d-flex align-items-center me-3" style="min-width: 0;">
                                <span class="activity-dot <?php echo $dot_color; ?>"></span>
                                <div class="text-sm fw-600 text-truncate">
                                    <span class="text-muted fw-normal text-xs d-block"><?php echo strtoupper($act['type']); ?></span>
                                    <?php echo htmlspecialchars($act['detail']); ?>
                                </div>
                            </div>
                            <div class="text-xs text-muted text-nowrap"><?php echo time_ago($act['created_at']); ?></div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="mt-4">
                            <button class="btn btn-light w-100 text-xs fw-bold py-2" onclick="location.href='transactions.php'">VIEW FULL SYSTEM LOGS</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Users Table -->
            <div class="data-table-card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Recent User Registrations</h5>
                    <a href="users.php" class="btn btn-primary btn-sm px-3">View All Users</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Account Type</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_users as $ru): ?>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="admin-avatar" style="width: 32px; height: 32px; font-size: 0.7rem;"><?php echo strtoupper(substr($ru['name'], 0, 1) . substr($ru['lastname'], 0, 1)); ?></div>
                                        <div>
                                            <div class="fw-bold"><?php echo $ru['name'] . ' ' . $ru['lastname']; ?></div>
                                            <div class="text-xs text-muted"><?php echo $ru['email']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $ru['account_type']; ?></td>
                                <td class="fw-bold">$<?php echo number_format($ru['balance'], 2); ?></td>
                                <td>
                                    <?php 
                                    $s_class = 'status-pending';
                                    if ($ru['status'] == 'Active') $s_class = 'status-active';
                                    if ($ru['status'] == 'Blocked') $s_class = 'status-blocked';
                                    ?>
                                    <span class="status-badge <?php echo $s_class; ?>"><?php echo $ru['status']; ?></span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($ru['created_at'])); ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="user-actions.php?id=<?php echo $ru['id']; ?>" class="action-btn text-warning" title="Quick Actions"><i class="fa-solid fa-bolt"></i></a>
                                        <a href="user-edit.php?id=<?php echo $ru['id']; ?>" class="action-btn" title="View Profile"><i class="fa-solid fa-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="mt-auto py-3 px-4 border-top bg-white text-center text-muted" style="font-size: 0.85rem;">
            SwiftCapital Admin © 2026. Internal System Only.
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart Logic -->
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const gradient1 = ctx.createLinearGradient(0, 0, 0, 300);
        gradient1.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
        gradient1.addColorStop(1, 'rgba(99, 102, 241, 0)');

        const gradient2 = ctx.createLinearGradient(0, 0, 0, 300);
        gradient2.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
        gradient2.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Deposits',
                    data: [65000, 59000, 80000, 81000, 56000, 55000, 40000],
                    borderColor: '#6366f1',
                    backgroundColor: gradient1,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 2
                }, {
                    label: 'Withdrawals',
                    data: [28000, 48000, 40000, 19000, 86000, 27000, 90000],
                    borderColor: '#10b981',
                    backgroundColor: gradient2,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: 'Inter', size: 12, weight: '600' }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: {
                            font: { family: 'Inter' },
                            callback: function(value) { return '$' + value.toLocaleString(); }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter' } }
                    }
                }
            }
        });
    </script>
</body>
</html>
