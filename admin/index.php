<?php 
require_once '../includes/admin-check.php'; 

// Fetch Stats
$is_sub = ($_SESSION['role'] === 'Sub-Admin');
$admin_id = (int)$_SESSION['user_id'];

if ($is_sub) {
    $total_assets = $pdo->prepare("SELECT SUM(balance) FROM users WHERE assigned_admin_id = ?");
    $total_assets->execute([$admin_id]);
    $total_assets = $total_assets->fetchColumn() ?: 0;

    $active_users = $pdo->prepare("SELECT COUNT(*) FROM users WHERE status='Active' AND assigned_admin_id = ?");
    $active_users->execute([$admin_id]);
    $active_users = $active_users->fetchColumn();

    $pending_loans_amount = $pdo->prepare("SELECT SUM(l.amount) FROM loans l JOIN users u ON l.user_id = u.id WHERE l.status='Pending' AND u.assigned_admin_id = ?");
    $pending_loans_amount->execute([$admin_id]);
    $pending_loans_amount = $pending_loans_amount->fetchColumn() ?: 0;

    $pending_loans_count = $pdo->prepare("SELECT COUNT(*) FROM loans l JOIN users u ON l.user_id = u.id WHERE l.status='Pending' AND u.assigned_admin_id = ?");
    $pending_loans_count->execute([$admin_id]);
    $pending_loans_count = $pending_loans_count->fetchColumn();

    $support_tickets = $pdo->prepare("SELECT COUNT(*) FROM support_tickets s JOIN users u ON s.user_id = u.id WHERE s.status='Open' AND u.assigned_admin_id = ?");
    $support_tickets->execute([$admin_id]);
    $support_tickets = $support_tickets->fetchColumn();

    $unread_contacts  = 0; // Contacts are usually general, or we could link them?
    
    $pending_cards = $pdo->prepare("SELECT COUNT(*) FROM card_applications ca JOIN users u ON ca.user_id = u.id WHERE ca.status='Pending' AND u.assigned_admin_id = ?");
    $pending_cards->execute([$admin_id]);
    $pending_cards = $pending_cards->fetchColumn();

    $pending_wires = $pdo->prepare("SELECT COUNT(*) FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.method='International Wire' AND t.status='Pending' AND u.assigned_admin_id = ?");
    $pending_wires->execute([$admin_id]);
    $pending_wires = $pending_wires->fetchColumn();

    // Recent data
    $recent_contacts = []; // General support normally sees all
    
    $recent_cards = $pdo->prepare("SELECT ca.*, u.name, u.lastname, u.email, u.profile_pic FROM card_applications ca JOIN users u ON ca.user_id = u.id WHERE u.assigned_admin_id = ? ORDER BY ca.created_at DESC LIMIT 5");
    $recent_cards->execute([$admin_id]);
    $recent_cards = $recent_cards->fetchAll();

    $recent_wires = $pdo->prepare("SELECT t.*, u.name, u.lastname, u.email, u.profile_pic FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.method='International Wire' AND u.assigned_admin_id = ? ORDER BY t.created_at DESC LIMIT 5");
    $recent_wires->execute([$admin_id]);
    $recent_wires = $recent_wires->fetchAll();

    $recent_users = $pdo->prepare("SELECT * FROM users WHERE assigned_admin_id = ? ORDER BY created_at DESC LIMIT 5");
    $recent_users->execute([$admin_id]);
    $recent_users = $recent_users->fetchAll();

    $activity_sql = "
        (SELECT 'Transaction' as type, t.created_at, t.narration as detail, t.status FROM transactions t JOIN users u ON t.user_id = u.id WHERE u.assigned_admin_id = $admin_id)
        UNION ALL
        (SELECT 'New User' as type, created_at, CONCAT(name, ' ', lastname) as detail, status FROM users WHERE assigned_admin_id = $admin_id)
        UNION ALL
        (SELECT 'Loan' as type, l.created_at, l.loan_type as detail, l.status FROM loans l JOIN users u ON l.user_id = u.id WHERE u.assigned_admin_id = $admin_id)
        UNION ALL
        (SELECT 'Support' as type, s.created_at, s.subject as detail, s.status FROM support_tickets s JOIN users u ON s.user_id = u.id WHERE u.assigned_admin_id = $admin_id)
        UNION ALL
        (SELECT 'Card' as type, ca.created_at, CONCAT(ca.card_type, ' ', ca.card_tier) as detail, ca.status FROM card_applications ca JOIN users u ON ca.user_id = u.id WHERE u.assigned_admin_id = $admin_id)
        ORDER BY created_at DESC LIMIT 6
    ";
    $activities = $pdo->query($activity_sql)->fetchAll();

} else {
    // Super Admin - Sees All
    $total_assets = $pdo->query("SELECT SUM(balance) FROM users")->fetchColumn() ?: 0;
    $active_users = $pdo->query("SELECT COUNT(*) FROM users WHERE status='Active'")->fetchColumn();
    $pending_loans_amount = $pdo->query("SELECT SUM(amount) FROM loans WHERE status='Pending'")->fetchColumn() ?: 0;
    $pending_loans_count = $pdo->query("SELECT COUNT(*) FROM loans WHERE status='Pending'")->fetchColumn();
    $support_tickets = $pdo->query("SELECT COUNT(*) FROM support_tickets WHERE status='Open'")->fetchColumn();
    $unread_contacts  = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
    $pending_cards    = $pdo->query("SELECT COUNT(*) FROM card_applications WHERE status='Pending'")->fetchColumn();
    $pending_wires    = $pdo->query("SELECT COUNT(*) FROM transactions WHERE method='International Wire' AND status='Pending'")->fetchColumn();
    $recent_contacts = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
    $recent_cards = $pdo->query("SELECT ca.*, u.name, u.lastname, u.email, u.profile_pic FROM card_applications ca JOIN users u ON ca.user_id = u.id ORDER BY ca.created_at DESC LIMIT 5")->fetchAll();
    $recent_wires = $pdo->query("SELECT t.*, u.name, u.lastname, u.email, u.profile_pic FROM transactions t JOIN users u ON t.user_id = u.id WHERE t.method='International Wire' ORDER BY t.created_at DESC LIMIT 5")->fetchAll();
    $recent_users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

    $activity_sql = "
        (SELECT 'Transaction' as type, created_at, narration as detail, status FROM transactions)
        UNION ALL
        (SELECT 'New User' as type, created_at, CONCAT(name, ' ', lastname) as detail, status FROM users)
        UNION ALL
        (SELECT 'Loan' as type, created_at, loan_type as detail, status FROM loans)
        UNION ALL
        (SELECT 'Support' as type, created_at, subject as detail, status FROM support_tickets)
        UNION ALL
        (SELECT 'Contact' as type, created_at, CONCAT(first_name, ' ', last_name) as detail, 'New' as status FROM contact_messages)
        UNION ALL
        (SELECT 'Card' as type, created_at, CONCAT(card_type, ' ', card_tier) as detail, status FROM card_applications)
        ORDER BY created_at DESC LIMIT 6
    ";
    $activities = $pdo->query($activity_sql)->fetchAll();
}

// Notification Center Logic
$notifs_stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$notifs_stmt->execute([$_SESSION['user_id']]);
$all_notifs = $notifs_stmt->fetchAll();
$unread_count = 0;
foreach($all_notifs as $n) if(!$n['is_read']) $unread_count++;

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
            <div class="brand-name">
                 <img src="../assets/images/SWC_Primary_Logo_Dark.png" alt="SwiftCapital" height="40">
            </div>
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
            <a href="wire-transfers.php" class="nav-link d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-earth-americas"></i> Wire Transfers</span>
                <?php if ($pending_wires > 0): ?>
                <span class="badge bg-warning text-dark" style="font-size:.6rem;"><?php echo $pending_wires; ?></span>
                <?php endif; ?>
            </a>
            <a href="loans.php" class="nav-link">
                <i class="fa-solid fa-hand-holding-dollar"></i> Loan Requests
            </a>
            <a href="cards.php" class="nav-link d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-credit-card"></i> Card Requests</span>
                <?php if ($pending_cards > 0): ?>
                <span class="badge bg-warning text-dark" style="font-size:.6rem;"><?php echo $pending_cards; ?></span>
                <?php endif; ?>
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
            <a href="contacts.php" class="nav-link d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-envelope"></i> Contact Messages</span>
                <?php if ($unread_contacts > 0): ?>
                <span class="badge bg-danger" style="font-size:.6rem;"><?php echo $unread_contacts; ?></span>
                <?php endif; ?>
            </a>
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
                <div class="notification-bell" id="notifBell">
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
                                    <div class="notif-content">
                                        <b class="title"><?php echo htmlspecialchars($n['title']); ?></b>
                                        <span class="msg"><?php echo htmlspecialchars($n['message']); ?></span>
                                        <span class="time"><?php echo time_ago($n['created_at']); ?></span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="notif-footer">
                            <a href="#" id="clearNotifs">Clear all notifications</a>
                        </div>
                    </div>
                </div>
                
                <div class="admin-profile ms-2">
                    <div class="admin-avatar">
                        <?php if(!empty($_SESSION['profile_pic'])): ?>
                            <img src="../assets/uploads/profiles/<?php echo $_SESSION['profile_pic']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
                        <?php else: ?>
                            <?php echo strtoupper(substr($_SESSION["user_name"] ?? "A", 0, 1)); ?>
                        <?php endif; ?>
                    </div>
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
                    <a href="contacts.php" style="text-decoration:none;">
                    <div class="stat-card" style="<?php echo $unread_contacts > 0 ? 'border-left:4px solid #ef4444;' : ''; ?>">
                        <div class="stat-info">
                            <p>Contact Messages</p>
                            <h3><?php echo $unread_contacts; ?></h3>
                            <div class="stat-trend <?php echo $unread_contacts > 0 ? 'trend-down' : 'trend-up'; ?>">
                                <i class="fa-solid fa-envelope"></i> <?php echo $unread_contacts; ?> <span class="text-muted fw-normal ms-1">unread</span>
                            </div>
                        </div>
                        <div class="stat-icon bg-rose-light">
                            <i class="fa-solid fa-envelope-open-text"></i>
                        </div>
                    </div>
                    </a>
                </div>
                <!-- Card Requests stat -->
                <div class="col-md-3">
                    <a href="cards.php" style="text-decoration:none;">
                    <div class="stat-card" style="<?php echo $pending_cards > 0 ? 'border-left:4px solid #f59e0b;' : ''; ?>">
                        <div class="stat-info">
                            <p>Card Requests</p>
                            <h3><?php echo $pending_cards; ?></h3>
                            <div class="stat-trend <?php echo $pending_cards > 0 ? 'trend-down' : 'trend-up'; ?>">
                                <i class="fa-solid fa-credit-card"></i> <?php echo $pending_cards; ?> <span class="text-muted fw-normal ms-1">pending</span>
                            </div>
                        </div>
                        <div class="stat-icon bg-amber-light">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                    </div>
                    </a>
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

            <!-- Recent Contact Messages -->
            <div class="data-table-card">
                <div class="card-header">
                    <div>
                        <h5 class="mb-0 fw-bold">Recent Contact Messages</h5>
                        <p class="text-xs text-muted mb-0">Website contact form submissions</p>
                    </div>
                    <a href="contacts.php" class="btn btn-primary btn-sm px-3">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Sender</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_contacts)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4"><i class="fa-solid fa-inbox fa-2x d-block mb-2" style="opacity:.3;"></i>No messages yet</td></tr>
                            <?php else: ?>
                            <?php foreach ($recent_contacts as $cm): ?>
                            <tr style="<?php echo !$cm['is_read'] ? 'background:#fafbff;font-weight:600;' : ''; ?>">
                                <td>
                                    <div class="user-cell">
                                        <div class="admin-avatar" style="width:32px;height:32px;font-size:.7rem;background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                                            <?php echo strtoupper(substr($cm['first_name'],0,1).substr($cm['last_name'],0,1)); ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($cm['first_name'].' '.$cm['last_name']); ?></div>
                                            <div class="text-xs text-muted"><?php echo htmlspecialchars($cm['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-indigo-light text-primary fw-700"><?php echo htmlspecialchars(ucfirst($cm['subject'])); ?></span></td>
                                <td class="text-muted text-sm" style="max-width:220px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;"><?php echo htmlspecialchars(substr($cm['message'],0,60)).'...'; ?></td>
                                <td class="text-xs text-muted"><?php echo date('M d, g:i A', strtotime($cm['created_at'])); ?></td>
                                <td>
                                    <?php if (!$cm['is_read']): ?>
                                    <span class="status-badge status-pending">Unread</span>
                                    <?php else: ?>
                                    <span class="status-badge status-active">Read</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="contacts.php?id=<?php echo $cm['id']; ?>" class="action-btn" title="View Message"><i class="fa-solid fa-eye"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Card Requests -->
            <div class="data-table-card">
                <div class="card-header">
                    <div>
                        <h5 class="mb-0 fw-bold">Recent Card Requests</h5>
                        <p class="text-xs text-muted mb-0">Latest virtual card applications from users</p>
                    </div>
                    <a href="cards.php" class="btn btn-primary btn-sm px-3">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Network / Tier</th>
                                <th>Daily Limit</th>
                                <th>Applied</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_cards)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4"><i class="fa-solid fa-credit-card fa-2x d-block mb-2" style="opacity:.3;"></i>No card applications yet</td></tr>
                            <?php else: ?>
                            <?php foreach ($recent_cards as $ca):
                                $initials = strtoupper(substr($ca['name'],0,1).substr($ca['lastname'],0,1));
                                $sc = ['Pending'=>'status-pending','Approved'=>'status-active','Rejected'=>'status-blocked'];
                                $badge_cls = $sc[$ca['status']] ?? 'status-pending';
                            ?>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="admin-avatar" style="width:32px;height:32px;font-size:.7rem;background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                                            <?php if(!empty($ca['profile_pic'])): ?>
                                                <img src="../assets/uploads/profiles/<?php echo $ca['profile_pic']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
                                            <?php else: ?>
                                                <?php echo $initials; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($ca['name'].' '.$ca['lastname']); ?></div>
                                            <div class="text-xs text-muted"><?php echo htmlspecialchars($ca['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-800 text-sm"><?php echo ucfirst($ca['card_type']); ?></div>
                                    <div class="text-xs text-muted"><?php echo ucwords($ca['card_tier']); ?> tier</div>
                                </td>
                                <td class="fw-700">$<?php echo number_format($ca['daily_limit'],0); ?>/day</td>
                                <td class="text-xs text-muted"><?php echo date('M d, Y', strtotime($ca['created_at'])); ?></td>
                                <td><span class="status-badge <?php echo $badge_cls; ?>"><?php echo $ca['status']; ?></span></td>
                                <td><a href="cards.php" class="action-btn text-primary" title="View"><i class="fa-solid fa-eye"></i></a></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Wire Transfers -->
            <div class="data-table-card">
                <div class="card-header">
                    <div>
                        <h5 class="mb-0 fw-bold">Recent Wire Transfers</h5>
                        <p class="text-xs text-muted mb-0">International SWIFT wire requests requiring review</p>
                    </div>
                    <a href="wire-transfers.php" class="btn btn-primary btn-sm px-3">
                        View All <?php if ($pending_wires > 0): ?><span class="badge bg-warning text-dark ms-1"><?php echo $pending_wires; ?></span><?php endif; ?>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_wires)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4"><i class="fa-solid fa-earth-americas fa-2x d-block mb-2" style="opacity:.3;"></i>No wire transfers yet</td></tr>
                            <?php else: ?>
                            <?php foreach ($recent_wires as $wr):
                                $winitials = strtoupper(substr($wr['name'],0,1).substr($wr['lastname'],0,1));
                                $wsc = ['Pending'=>'status-pending','Completed'=>'status-active','Cancelled'=>'status-blocked'];
                                $wbadge = $wsc[$wr['status']] ?? 'status-pending';
                                $wlbl = $wr['status'] === 'Cancelled' ? 'Rejected' : $wr['status'];
                            ?>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="admin-avatar" style="width:32px;height:32px;font-size:.7rem;background:linear-gradient(135deg,#1d4ed8,#3b82f6);">
                                            <?php if(!empty($wr['profile_pic'])): ?>
                                                <img src="../assets/uploads/profiles/<?php echo $wr['profile_pic']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
                                            <?php else: ?>
                                                <?php echo $winitials; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($wr['name'].' '.$wr['lastname']); ?></div>
                                            <div class="text-xs text-muted"><?php echo htmlspecialchars($wr['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-900 text-danger">$<?php echo number_format($wr['amount'], 2); ?></td>
                                <td><code style="font-size:.75rem;color:#6366f1;"><?php echo htmlspecialchars($wr['txn_hash']); ?></code></td>
                                <td class="text-xs text-muted"><?php echo date('M d, Y', strtotime($wr['created_at'])); ?></td>
                                <td><span class="status-badge <?php echo $wbadge; ?>"><?php echo $wlbl; ?></span></td>
                                <td><a href="wire-transfers.php" class="action-btn text-primary" title="View"><i class="fa-solid fa-eye"></i></a></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Users Table -->
            <div class="data-table-card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Recent User Registrations</h5>
                    <a href="users.php" class="btn btn-primary btn-sm px-3">View All Users</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
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
                                        <div class="admin-avatar" style="width: 32px; height: 32px; font-size: 0.7rem;">
                                            <?php if(!empty($ru['profile_pic'])): ?>
                                                <img src="../assets/uploads/profiles/<?php echo $ru['profile_pic']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
                                            <?php else: ?>
                                                <?php echo strtoupper(substr($ru['name'], 0, 1) . substr($ru['lastname'], 0, 1)); ?>
                                            <?php endif; ?>
                                        </div>
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
                layout: {
                    padding: {
                        left: 15,
                        right: 15,
                        top: 10,
                        bottom: 0
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 25,
                            font: { family: 'Inter', size: 13, weight: '600' },
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { family: 'Inter', size: 14 },
                        bodyFont: { family: 'Inter', size: 13 },
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#f1f5f9', drawBorder: false },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            color: '#94a3b8',
                            callback: function(value) { return '$' + value.toLocaleString(); },
                            padding: 10
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { 
                            font: { family: 'Inter', size: 11 },
                            color: '#94a3b8',
                            padding: 10
                        }
                    }
                }
            }
        });
    </script>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        // Notification & Toast System
        const bell = document.getElementById('notifBell');
        const dropdown = document.getElementById('notifDropdown');
        
        bell.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });

        dropdown.addEventListener('click', (e) => e.stopPropagation());

        document.addEventListener('click', () => dropdown.classList.remove('show'));

        const clearBtn = document.getElementById('clearNotifs');
        if(clearBtn) {
            clearBtn.addEventListener('click', (e) => {
                e.preventDefault();
                fetch('clear-notifs.php', { method: 'POST' })
                .then(() => {
                    const dot = document.querySelector('.notification-dot');
                    if(dot) dot.remove();
                    const badge = document.querySelector('.unread-badge');
                    if(badge) badge.innerText = '0 New';
                    document.querySelectorAll('.notif-item.unread').forEach(item => item.classList.remove('unread'));
                    showToast('Notifications Cleared', 'All alerts have been marked as read.', 'success');
                });
            });
        }

        function showToast(title, message, type = 'success') {
            const container = document.getElementById('toastContainer');
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

        // Auto-show a welcome toast
        setTimeout(() => {
            showToast('System Online', 'Elite Control Panel is monitoring all gateways.', 'success');
        }, 1500);

        <?php if(isset($_GET['login_success'])): ?>
            showToast('Welcome Back', 'Authorized administrative session established.', 'success');
        <?php endif; ?>
    </script>
</body>
</html>
