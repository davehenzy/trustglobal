<?php require_once '../includes/admin-check.php'; ?>
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
                <h4>Users Management</h4>
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
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Client Name</th>
                                <th>Account Type</th>
                                <th>Account Balance</th>
                                <th>Status</th>
                                <th>Quick Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
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
                                        <div class="admin-avatar" style="width: 38px; height: 38px;"><?php echo $initials; ?></div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($user['name'] . ' ' . $user['lastname']); ?></div>
                                            <div class="text-xs text-muted"><?php echo htmlspecialchars($user['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['account_type']); ?></td>
                                <td class="fw-bold text-dark">$<?php echo number_format($user['balance'], 2); ?></td>
                                <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $user['status']; ?></span></td>
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
</body>
</html>
