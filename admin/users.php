<?php require_once '../includes/admin-check.php'; 

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - SwiftCapital Admin</title>
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
            <a href="users.php" class="nav-link active">
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
                <h4>Users Management</h4>
            </div>

            <div class="user-nav">
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
            
            <!-- Filter Actions -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="d-flex gap-2">
                        <div class="input-group" style="max-width: 400px;">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Search by name, email or ID...">
                        </div>
                        <select class="form-select" style="max-width: 150px;">
                            <option selected>Status</option>
                            <option>Active</option>
                            <option>Pending</option>
                            <option>Blocked</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <a href="user-add.php" class="btn btn-primary px-4"><i class="fa-solid fa-plus me-2"></i> Add New User</a>
                </div>
            </div>

            <!-- Users Table -->
            <div class="data-table-card">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Client Name</th>
                                <th>Account Type</th>
                                <th>Account Balance</th>
                                <th>Status</th>
                                <?php if (!in_array($_SESSION['role'] ?? '', ['Sub-Admin'])): ?>
                                <th>Assigned To</th>
                                <?php endif; ?>
                                <th>Quick Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (in_array($_SESSION['role'] ?? '', ['Sub-Admin'])) {
                                $stmt = $pdo->prepare("SELECT * FROM users WHERE (role = 'User' OR role IS NULL OR role = '') AND assigned_admin_id = ? ORDER BY created_at DESC");
                                $stmt->execute([$_SESSION['user_id']]);
                            } else {
                                $stmt = $pdo->query("SELECT u.*, a.name as manager_name, a.lastname as manager_lastname 
                                                      FROM users u 
                                                      LEFT JOIN users a ON u.assigned_admin_id = a.id 
                                                      WHERE (u.role = 'User' OR u.role IS NULL OR u.role = '') 
                                                      ORDER BY u.created_at DESC");
                            }
                            while ($user = $stmt->fetch()) {
                                $status_class = '';
                                switch($user['status']) {
                                    case 'Active': $status_class = 'status-active'; break;
                                    case 'Pending': $status_class = 'status-pending'; break;
                                    case 'Blocked': $status_class = 'status-blocked'; break;
                                    case 'Deactivated': $status_class = 'status-blocked'; break;
                                }

                                // Handle KYC status overlay or separate column if needed
                                // For now, let's just use the status
                                
                                $initials = strtoupper(substr($user['name'], 0, 1) . substr($user['lastname'], 0, 1));
                            ?>
                            <tr>
                                <td><span class="text-muted fw-mono">#SC-<?php echo str_pad($user['id'], 4, '0', STR_PAD_LEFT); ?></span></td>
                                <td>
                                    <div class="user-cell">
                                        <div class="admin-avatar" style="width: 38px; height: 38px;">
                                            <?php if(!empty($user['profile_pic'])): ?>
                                                <img src="../assets/uploads/profiles/<?php echo $user['profile_pic']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
                                            <?php else: ?>
                                                <?php echo $initials; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($user['name'] . ' ' . $user['lastname']); ?></div>
                                            <div class="text-xs text-muted"><?php echo htmlspecialchars($user['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['account_type']); ?></td>
                                <td class="fw-bold text-dark">$<?php echo number_format($user['balance'], 2); ?></td>
                                <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $user['status']; ?></span></td>
                                <?php if (!in_array($_SESSION['role'] ?? '', ['Sub-Admin'])): ?>
                                <td>
                                    <?php if ($user['assigned_admin_id']): ?>
                                        <span class="text-xs fw-bold text-primary"><i class="fa-solid fa-user-shield me-1"></i> <?php echo htmlspecialchars($user['manager_name']); ?></span>
                                    <?php else: ?>
                                        <span class="text-xs text-muted">Unassigned</span>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                                <td>
                                    <div class="dropdown">
                                        <button class="action-btn dropdown-toggle no-caret" data-bs-toggle="dropdown">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li><a class="dropdown-item py-2" href="user-edit.php?id=<?php echo $user['id']; ?>"><i class="fa-solid fa-eye me-2 text-primary"></i> Full Profile</a></li>
                                            <li><a class="dropdown-item py-2" href="user-actions.php?id=<?php echo $user['id']; ?>"><i class="fa-solid fa-bolt me-2 text-warning"></i> Quick Actions</a></li>
                                            <li><a class="dropdown-item py-2" href="transactions.php?user_id=<?php echo $user['id']; ?>"><i class="fa-solid fa-list-ul me-2 text-info"></i> Transactions</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><button class="dropdown-item py-2 text-primary" onclick="alert('Password reset link sent')"><i class="fa-solid fa-key me-2"></i> Reset Password</button></li>
                                            <li><button class="dropdown-item py-2 text-danger" onclick="confirm('Are you sure you want to suspend this account?')"><i class="fa-solid fa-ban me-2"></i> Suspend Account</button></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                    <div class="text-xs text-muted">Showing 1 to 4 of 12,482 entries</div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="mt-auto py-3 px-4 border-top bg-white text-center text-muted" style="font-size: 0.85rem;">
            SwiftCapital Admin Â© 2026. Internal System Only.
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <div class="toast-container" id="toastContainer"></div>

    <script>
        // Notification & Toast System
        const bell = document.getElementById('notifBell');
        const dropdown = document.getElementById('notifDropdown');
        
        if(bell) {
            bell.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });
        }

        document.addEventListener('click', () => dropdown.classList.remove('show'));

        function showToast(title, message, type = 'success') {
            const container = document.getElementById('toastContainer');
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
    </script>
</body>
</html>
