<?php 
require_once '../includes/db.php';
require_once '../includes/admin-check.php'; 

$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    header("Location: users.php");
    exit;
}

// Fetch User
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: users.php");
    exit;
}

$success_msg = '';
$error_msg = '';

// Handle Actions (Credit, Debit, Status)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['credit_user'])) {
        $amount = (float)$_POST['amount'];
        $narration = $_POST['narration'];
        $backdate = $_POST['backdate'] ?? '';
        try {
            $pdo->beginTransaction();
            $new_balance = $user['balance'] + $amount;
            $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?")->execute([$new_balance, $user_id]);
            $ref = 'SWC-' . strtoupper(bin2hex(random_bytes(4)));
            
            if (!empty($backdate)) {
                $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, narration, txn_hash, method, created_at) VALUES (?, 'Credit', ?, 'Completed', ?, ?, 'Admin Credit', ?)");
                $stmt->execute([$user_id, $amount, $narration, $ref, $backdate]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, narration, txn_hash, method) VALUES (?, 'Credit', ?, 'Completed', ?, ?, 'Admin Credit')");
                $stmt->execute([$user_id, $amount, $narration, $ref]);
            }
            
            $pdo->commit();
            $success_msg = "Account credited successfully.";
        } catch (Exception $e) { $pdo->rollBack(); $error_msg = $e->getMessage(); }
    }
    
    if (isset($_POST['debit_user'])) {
        $amount = (float)$_POST['amount'];
        $narration = $_POST['narration'];
        $backdate = $_POST['backdate'] ?? '';
        try {
            $pdo->beginTransaction();
            $new_balance = $user['balance'] - $amount;
            $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?")->execute([$new_balance, $user_id]);
            $ref = 'SWC-' . strtoupper(bin2hex(random_bytes(4)));

            if (!empty($backdate)) {
                $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, narration, txn_hash, method, created_at) VALUES (?, 'Debit', ?, 'Completed', ?, ?, 'Admin Debit', ?)");
                $stmt->execute([$user_id, $amount, $narration, $ref, $backdate]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, narration, txn_hash, method) VALUES (?, 'Debit', ?, 'Completed', ?, ?, 'Admin Debit')");
                $stmt->execute([$user_id, $amount, $narration, $ref]);
            }

            $pdo->commit();
            $success_msg = "Account debited successfully.";
        } catch (Exception $e) { $pdo->rollBack(); $error_msg = $e->getMessage(); }
    }

    if (isset($_POST['update_status'])) {
        $status = $_POST['status'];
        $pdo->prepare("UPDATE users SET status = ? WHERE id = ?")->execute([$status, $user_id]);
        $success_msg = "User status updated to $status.";
    }

    // Refresh user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}

// Fetch Recent Activities
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$activities = $stmt->fetchAll();

$initials = strtoupper(substr($user['name'], 0, 1) . substr($user['lastname'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Quick Actions - SwiftCapital Admin</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .quick-action-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 15px;
        }

        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border-color: var(--admin-primary);
        }

        .action-icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 5px;
        }

        .action-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text-main);
            margin: 0;
        }

        .action-desc {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin: 0;
        }

        .user-profile-header {
            background: linear-gradient(135deg, var(--admin-sidebar) 0%, #1e293b 100%);
            border-radius: 16px;
            padding: 30px;
            color: #fff;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .user-profile-header::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }

        .profile-large-avatar {
            width: 80px;
            height: 80px;
            background: var(--admin-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 800;
            border: 4px solid rgba(255,255,255,0.1);
        }

        .header-stat {
            background: rgba(255,255,255,0.05);
            padding: 10px 20px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .header-stat-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.7;
            margin-bottom: 2px;
        }

        .header-stat-value {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .modal-content {
            border-radius: 16px;
            border: none;
            overflow: hidden;
        }

        .modal-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            padding: 20px 25px;
        }

        .modal-title {
            font-weight: 700;
            color: var(--text-main);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-main);
            font-size: 0.85rem;
        }

        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 8px;
            border-color: var(--border-color);
            font-size: 0.9rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            border-color: var(--admin-primary);
        }

        .btn-action {
            padding: 12px 25px;
            font-weight: 700;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .input-group-text {
            background: #f8fafc;
            border-color: var(--border-color);
            color: var(--text-muted);
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
            <a href="index.php" class="nav-link">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="users.php" class="nav-link active">
                <i class="fa-solid fa-users"></i> Users Management
            </a>
            <a href="transactions.php" class="nav-link">
                <i class="fa-solid fa-money-bill-transfer"></i> Transactions
            </a>
            <a href="loans.php" class="nav-link">
                <i class="fa-solid fa-hand-holding-dollar"></i> Loan Requests
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
                <div class="d-flex align-items-center gap-3">
                    <a href="users.php" class="action-btn"><i class="fa-solid fa-arrow-left"></i></a>
                    <h4>User Quick Actions</h4>
                </div>
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
            
            <!-- User Summary Header -->
            <?php if ($success_msg): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 12px;">
                    <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 12px;">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $error_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="user-profile-header">
                <div class="row align-items-center">
                    <div class="col-md-auto">
                        <div class="profile-large-avatar"><?php echo $initials; ?></div>
                    </div>
                    <div class="col-md mt-3 mt-md-0">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h2 class="mb-0 fw-bold"><?php echo htmlspecialchars($user['name'] . ' ' . $user['lastname']); ?></h2>
                            <?php 
                                $s_class = 'status-pending';
                                if ($user['status'] == 'Active') $s_class = 'status-active';
                                if ($user['status'] == 'Blocked') $s_class = 'status-blocked';
                            ?>
                            <span class="status-badge <?php echo $s_class; ?>"><?php echo $user['status']; ?></span>
                        </div>
                        <p class="mb-3 opacity-75"><i class="fa-solid fa-envelope me-2"></i> <?php echo htmlspecialchars($user['email']); ?> | <i class="fa-solid fa-hashtag ms-2 me-1"></i> #SC-<?php echo str_pad($user['id'], 4, '0', STR_PAD_LEFT); ?></p>
                        
                        <div class="d-flex flex-wrap gap-3">
                            <div class="header-stat">
                                <div class="header-stat-label">Available Balance</div>
                                <div class="header-stat-value">$<?php echo number_format($user['balance'], 2); ?></div>
                            </div>
                            <div class="header-stat">
                                <div class="header-stat-label">Account Type</div>
                                <div class="header-stat-value"><?php echo $user['account_type']; ?></div>
                            </div>
                            <div class="header-stat">
                                <div class="header-stat-label">Joined Date</div>
                                <div class="header-stat-value"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-auto mt-4 mt-lg-0">
                        <div class="d-flex gap-2">
                            <a href="user-edit.php?id=<?php echo $user['id']; ?>" class="btn btn-light px-4 fw-bold shadow-sm">View Full Profile</a>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-4">Financial & Status Controls</h5>
            
            <!-- Quick Actions Grid -->
            <div class="row g-4">
                <!-- Action: Credit -->
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="quick-action-card" data-bs-toggle="modal" data-bs-target="#creditModal">
                        <div class="action-icon-circle bg-emerald-light">
                            <i class="fa-solid fa-plus-circle"></i>
                        </div>
                        <div>
                            <p class="action-title">Credit Account</p>
                            <p class="action-desc">Add funds to user balance</p>
                        </div>
                    </div>
                </div>

                <!-- Action: Debit -->
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="quick-action-card" data-bs-toggle="modal" data-bs-target="#debitModal">
                        <div class="action-icon-circle bg-rose-light">
                            <i class="fa-solid fa-minus-circle"></i>
                        </div>
                        <div>
                            <p class="action-title">Debit Account</p>
                            <p class="action-desc">Deduct funds from user</p>
                        </div>
                    </div>
                </div>

                <!-- Action: Change Status -->
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="quick-action-card" data-bs-toggle="modal" data-bs-target="#statusModal">
                        <div class="action-icon-circle bg-amber-light">
                            <i class="fa-solid fa-user-gear"></i>
                        </div>
                        <div>
                            <p class="action-title">User Status</p>
                            <p class="action-desc">Change account state</p>
                        </div>
                    </div>
                </div>

                <!-- Action: Verify KYC -->
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="quick-action-card" onclick="location.href='kyc-view.php'">
                        <div class="action-icon-circle bg-indigo-light">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <div>
                            <p class="action-title">Verify KYC</p>
                            <p class="action-desc">Approve or reject ID</p>
                        </div>
                    </div>
                </div>

                <!-- Action: Reset Password -->
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="quick-action-card" onclick="alert('Password reset link sent to kante@example.com')">
                        <div class="action-icon-circle" style="background: #f1f5f9; color: #475569;">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <div>
                            <p class="action-title">Reset Password</p>
                            <p class="action-desc">Send reset instructions</p>
                        </div>
                    </div>
                </div>

                <!-- Action: Login as User -->
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="quick-action-card" onclick="window.open('../users/index.php', '_blank')">
                        <div class="action-icon-circle" style="background: #f1f5f9; color: #475569;">
                            <i class="fa-solid fa-right-to-bracket"></i>
                        </div>
                        <div>
                            <p class="action-title">Login as User</p>
                            <p class="action-desc">Access user dashboard</p>
                        </div>
                    </div>
                </div>

                <!-- Action: Transactions -->
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="quick-action-card" onclick="location.href='transactions.php'">
                        <div class="action-icon-circle" style="background: #f1f5f9; color: #475569;">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <div>
                            <p class="action-title">Transactions</p>
                            <p class="action-desc">View financial logs</p>
                        </div>
                    </div>
                </div>

                <!-- Action: Send Message -->
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="quick-action-card" data-bs-toggle="modal" data-bs-target="#messageModal">
                        <div class="action-icon-circle" style="background: #f1f5f9; color: #475569;">
                            <i class="fa-solid fa-paper-plane"></i>
                        </div>
                        <div>
                            <p class="action-title">Send Message</p>
                            <p class="action-desc">Email or internal alert</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-lg-12">
                    <div class="data-table-card mt-0">
                        <div class="card-header">
                            <h5 class="mb-0 fw-bold">Recent Activities</h5>
                            <a href="transactions.php?search=<?php echo urlencode($user['email']); ?>" class="btn btn-sm btn-outline-primary fw-bold">View History</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Activity</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($activities)): ?>
                                        <tr><td colspan="5" class="text-center py-4 text-muted">No recent activities found</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($activities as $act): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold"><?php echo $act['type']; ?> <?php echo $act['method'] ? "($act[method])" : ""; ?></div>
                                            </td>
                                            <td class="fw-bold <?php echo in_array($act['type'], ['Deposit', 'Credit']) ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo in_array($act['type'], ['Deposit', 'Credit']) ? '+' : '-'; ?>$<?php echo number_format($act['amount'], 2); ?>
                                            </td>
                                            <td class="text-sm"><?php echo date('M d, H:i', strtotime($act['created_at'])); ?></td>
                                            <td>
                                                <?php 
                                                $ast_class = $act['status'] == 'Completed' ? 'status-active' : ($act['status'] == 'Pending' ? 'status-pending' : 'status-blocked');
                                                ?>
                                                <span class="status-badge <?php echo $ast_class; ?>"><?php echo $act['status']; ?></span>
                                            </td>
                                            <td><span class="text-xs fw-mono opacity-75">#<?php echo $act['txn_hash']; ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="mt-auto py-3 px-4 border-top bg-white text-center text-muted" style="font-size: 0.85rem;">
            SwiftCapital Admin © 2026. Internal System Only.
        </footer>
    </div>

    <!-- Modals -->
    <!-- Credit Modal -->
    <div class="modal fade" id="creditModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-plus-circle text-success me-2"></i> Credit User Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="POST">
                        <input type="hidden" name="credit_user" value="1">
                        <div class="mb-3">
                            <label class="form-label">Amount to Credit ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description / Narrative</label>
                            <input type="text" name="narration" class="form-control" placeholder="e.g. Bonus Credit, Wire Deposit" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Backdate Transaction (Optional)</label>
                            <input type="datetime-local" name="backdate" class="form-control">
                            <div class="form-text text-xs">Leave empty for current time</div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-action">Process Credit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Debit Modal -->
    <div class="modal fade" id="debitModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-minus-circle text-danger me-2"></i> Debit User Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="POST">
                        <input type="hidden" name="debit_user" value="1">
                        <div class="mb-3">
                            <label class="form-label">Amount to Debit ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason for Debit</label>
                            <input type="text" name="narration" class="form-control" placeholder="e.g. Service Fee, Reversal" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Backdate Transaction (Optional)</label>
                            <input type="datetime-local" name="backdate" class="form-control">
                            <div class="form-text text-xs">Leave empty for current time</div>
                        </div>
                        <div class="mb-4 text-warning">
                            <div class="d-flex gap-2 align-items-center">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span class="text-xs fw-bold">Funds will be immediately removed from user balance.</span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 btn-action">Process Debit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-user-gear text-warning me-2"></i> Update User Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="POST">
                        <input type="hidden" name="update_status" value="1">
                        <div class="mb-4">
                            <label class="form-label">Current Status: <span class="badge bg-success"><?php echo $user['status']; ?></span></label>
                            <select name="status" class="form-select">
                                <option value="Active" <?php echo $user['status'] == 'Active' ? 'selected' : ''; ?>>Active (Full Access)</option>
                                <option value="Pending" <?php echo $user['status'] == 'Pending' ? 'selected' : ''; ?>>Pending KYC (Restricted action)</option>
                                <option value="Blocked" <?php echo $user['status'] == 'Blocked' ? 'selected' : ''; ?>>Blocked (Permanent ban)</option>
                                <option value="Deactivated" <?php echo $user['status'] == 'Deactivated' ? 'selected' : ''; ?>>Deactivated</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 btn-action fw-bold">Update Account Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-paper-plane text-primary me-2"></i> Send Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Notification Type</label>
                            <select class="form-select">
                                <option>Internal Alert (Dashboard Notification)</option>
                                <option>Email Message</option>
                                <option>Both Email & Internal</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-control" placeholder="Enter message subject">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Message Body</label>
                            <textarea class="form-control" rows="5" placeholder="Write your message here..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-action">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
