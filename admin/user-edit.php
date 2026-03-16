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

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $account_type = $_POST['account_type'];
    $status = $_POST['status'];
    $assigned_admin_id = (!empty($_POST['assigned_admin_id'])) ? $_POST['assigned_admin_id'] : null;
    $new_password = $_POST['password'];

    try {
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET name = ?, lastname = ?, email = ?, phone = ?, account_type = ?, status = ?, password = ?, assigned_admin_id = ? WHERE id = ?");
            $stmt->execute([$name, $lastname, $email, $phone, $account_type, $status, $hashed_password, $assigned_admin_id, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, lastname = ?, email = ?, phone = ?, account_type = ?, status = ?, assigned_admin_id = ? WHERE id = ?");
            $stmt->execute([$name, $lastname, $email, $phone, $account_type, $status, $assigned_admin_id, $user_id]);
        }
        $success_msg = "Profile updated successfully.";
        
        // Refresh user data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    } catch (PDOException $e) {
        $error_msg = "Error updating profile: " . $e->getMessage();
    }
}

// Handle Balance Adjustment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adjust_balance'])) {
    $action = $_POST['fundsAction']; // credit or debit
    $amount = (float)$_POST['amount'];
    $narration = $_POST['narration'];

    if ($amount > 0) {
        try {
            $pdo->beginTransaction();

            if ($action === 'credit') {
                $new_balance = $user['balance'] + $amount;
            } else {
                $new_balance = $user['balance'] - $amount;
            }

            // Generate unique reference
            $txn_hash = 'SWC-' . strtoupper(bin2hex(random_bytes(4)));

            // Update balance
            $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->execute([$new_balance, $user_id]);

            // Log Transaction
            $backdate = $_POST['backdate'] ?? '';
            if (!empty($backdate)) {
                $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, narration, txn_hash, created_at) VALUES (?, ?, ?, 'Completed', ?, ?, ?)");
                $stmt->execute([$user_id, ucfirst($action), $amount, $narration, $txn_hash, $backdate]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status, narration, txn_hash) VALUES (?, ?, ?, 'Completed', ?, ?)");
                $stmt->execute([$user_id, ucfirst($action), $amount, $narration, $txn_hash]);
            }

            $pdo->commit();
            $success_msg = "Balance adjusted successfully.";
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_msg = "Error adjusting balance: " . $e->getMessage();
        }
    } else {
        $error_msg = "Amount must be greater than zero.";
    }
}

// Fetch cumulative outflows
$stmt = $pdo->prepare("SELECT SUM(amount) FROM transactions WHERE user_id = ? AND type IN ('Withdrawal', 'Debit') AND status = 'Completed'");
$stmt->execute([$user_id]);
$cumulative_outflows = $stmt->fetchColumn() ?: 0;

// Fetch Sub-Admins for assignment (Super Admin only)
$sub_admins = [];
if (in_array($_SESSION['role'] ?? '', ['Super Admin', 'Admin'])) {
    $stmt = $pdo->query("SELECT id, name, lastname FROM users WHERE role = 'Sub-Admin' ORDER BY name ASC");
    $sub_admins = $stmt->fetchAll();
}

$initials = strtoupper(substr($user['name'], 0, 1) . substr($user['lastname'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - SwiftCapital Admin</title>
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
            <a href="loans.php" class="nav-link">
                <i class="fa-solid fa-hand-holding-dollar"></i> Loan Requests
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
                <h4 class="mb-0 fw-800"><a href="users.php" class="text-decoration-none text-muted opacity-50">Users</a> <i class="fa-solid fa-chevron-right mx-2 text-xs"></i> Profile Modulation</h4>
            </div>

            <div class="user-nav">
                <a href="users.php" class="btn btn-light-indigo btn-sm fw-800 px-3" style="border-radius: 10px;"><i class="fa-solid fa-arrow-left me-2"></i> User Directory</a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-padding">
            
            <div class="row g-4">
                <!-- User Basic Info -->
                <div class="col-lg-8">
                    <div class="data-table-card p-5 border-0 bg-white" style="border-radius: 24px;">
                        <?php if ($success_msg): ?>
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success_msg; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($error_msg): ?>
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $error_msg; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex align-items-center gap-4 mb-5">
                            <div class="admin-avatar bg-primary text-white shadow-lg" style="width: 70px; height: 70px; font-size: 1.8rem; font-weight: 800; border-radius: 20px;"><?php echo $initials; ?></div>
                            <div>
                                <h4 class="fw-800 mb-1"><?php echo htmlspecialchars($user['name'] . ' ' . $user['lastname']); ?></h4>
                                <p class="text-muted fw-600 mb-0">Artifact ID: #SC-<?php echo str_pad($user['id'], 4, '0', STR_PAD_LEFT); ?> â€¢ Registry: <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                            </div>
                            <div class="ms-auto">
                                <span class="status-badge status-<?php echo strtolower($user['status']); ?> px-4 py-2 fw-800" style="border-radius: 10px; font-size: 0.75rem;"><?php echo strtoupper($user['status']); ?> ACCOUNT</span>
                            </div>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="update_profile" value="1">
                            <h6 class="fw-800 text-xs text-uppercase text-muted mb-4" style="letter-spacing: 1px;">Primary Profile Variables</h6>
                            <div class="row g-4 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">First Name</label>
                                    <input type="text" name="name" class="form-control bg-light border-0 fw-600 p-3" style="border-radius: 12px;" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Last Name</label>
                                    <input type="text" name="lastname" class="form-control bg-light border-0 fw-600 p-3" style="border-radius: 12px;" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Email Address</label>
                                    <input type="email" name="email" class="form-control bg-light border-0 fw-600 p-3" style="border-radius: 12px;" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Secure Mobile Link</label>
                                    <input type="text" name="phone" class="form-control bg-light border-0 fw-600 p-3" style="border-radius: 12px;" value="<?php echo htmlspecialchars($user['phone']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Account Tier Archetype</label>
                                    <select name="account_type" class="form-select bg-light border-0 fw-600 p-3" style="border-radius: 12px;">
                                        <option value="Savings Account" <?php echo $user['account_type'] == 'Savings Account' ? 'selected' : ''; ?>>Savings Account</option>
                                        <option value="Checking Account" <?php echo $user['account_type'] == 'Checking Account' ? 'selected' : ''; ?>>Checking Account</option>
                                        <option value="Fixed Deposit Account" <?php echo $user['account_type'] == 'Fixed Deposit Account' ? 'selected' : ''; ?>>Fixed Deposit Account</option>
                                        <option value="Current Account" <?php echo $user['account_type'] == 'Current Account' ? 'selected' : ''; ?>>Current Account</option>
                                        <option value="Business Account" <?php echo $user['account_type'] == 'Business Account' ? 'selected' : ''; ?>>Business Account</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Authorization Status</label>
                                    <select name="status" class="form-select bg-light border-0 fw-800 p-3" style="border-radius: 12px;">
                                        <option value="Active" <?php echo $user['status'] == 'Active' ? 'selected' : ''; ?> class="text-success">Active</option>
                                        <option value="Blocked" <?php echo $user['status'] == 'Blocked' ? 'selected' : ''; ?> class="text-danger">Blocked</option>
                                        <option value="Pending" <?php echo $user['status'] == 'Pending' ? 'selected' : ''; ?> class="text-warning">Pending</option>
                                        <option value="Deactivated" <?php echo $user['status'] == 'Deactivated' ? 'selected' : ''; ?> class="text-muted">Deactivated</option>
                                    </select>
                                </div>
                                <?php if (in_array($_SESSION['role'] ?? '', ['Super Admin', 'Admin'])): ?>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Assigned Account Manager</label>
                                    <select name="assigned_admin_id" class="form-select bg-light border-0 fw-800 p-3" style="border-radius: 12px;">
                                        <option value="">Unassigned (General Queue)</option>
                                        <?php foreach ($sub_admins as $sa): ?>
                                            <option value="<?php echo $sa['id']; ?>" <?php echo ($user['assigned_admin_id'] == $sa['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($sa['name'] . ' ' . $sa['lastname']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endif; ?>
                                <div class="col-md-<?php echo (in_array($_SESSION['role'] ?? '', ['Super Admin', 'Admin'])) ? '12' : '6'; ?>">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Modulate Passcode (Leave blank to keep current)</label>
                                    <input type="password" name="password" class="form-control bg-light border-0 fw-600 p-3" style="border-radius: 12px;" placeholder="••••••••">
                                </div>
                            </div>

                            <div class="d-flex gap-3 justify-content-end border-top pt-5">
                                <a href="users.php" class="btn btn-light-soft px-5 py-3 fw-800" style="border-radius: 15px; background: #f1f5f9;">Discard Changes</a>
                                <button type="submit" class="btn btn-primary px-5 py-3 fw-800 shadow-lg" style="border-radius: 15px;">Commit Profile Updates</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Balance Management (Action Sidebar) -->
                <div class="col-lg-4">
                    <div class="data-table-card p-5 border-0 bg-white mb-4 shadow" style="border-radius: 24px;">
                        <h6 class="fw-800 text-xs text-uppercase text-muted mb-4" style="letter-spacing: 1px;">Manual Ledger Adjustment</h6>
                        
                        <div class="p-4 rounded-4 mb-5 shadow-inner" style="background: #f8fafc;">
                            <p class="text-xs text-muted fw-800 text-uppercase mb-2">Available Liquidity</p>
                            <h2 class="fw-800 mb-0 text-primary" style="letter-spacing: -1px;">$<?php echo number_format($user['balance'], 2); ?></h2>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="adjust_balance" value="1">
                            <div class="mb-4">
                                <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Adjustment Vector</label>
                                <div class="d-flex gap-3">
                                    <input type="radio" class="btn-check" name="fundsAction" id="credit" value="credit" checked>
                                    <label class="btn btn-outline-success border-0 bg-light px-4 py-3 fw-800 w-50" style="border-radius: 12px;" for="credit"><i class="fa-solid fa-arrow-trend-up me-2"></i> INBOUND</label>
                                    
                                    <input type="radio" class="btn-check" name="fundsAction" id="debit" value="debit">
                                    <label class="btn btn-outline-danger border-0 bg-light px-4 py-3 fw-800 w-50" style="border-radius: 12px;" for="debit"><i class="fa-solid fa-arrow-trend-down me-2"></i> OUTBOUND</label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Settlement Amount ($)</label>
                                <input type="number" step="0.01" name="amount" class="form-control bg-light border-0 fw-800 p-4 fs-4" style="border-radius: 15px; letter-spacing: -1px;" placeholder="0.00" required>
                            </div>

                            <div class="mb-5">
                                <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Protocol Narration</label>
                                <textarea name="narration" class="form-control bg-light border-0 fw-500 p-4" rows="3" style="border-radius: 15px; font-size: 0.85rem;" placeholder="Initialize administrative adjustment logic..."></textarea>
                            </div>

                            <div class="mb-5">
                                <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Backdate Entry (Optional)</label>
                                <input type="datetime-local" name="backdate" class="form-control bg-light border-0 fw-600 p-3" style="border-radius: 12px;">
                                <div class="form-text text-xs mt-2">Leave blank to use real-time timestamp</div>
                            </div>

                            <button type="submit" class="btn btn-dark w-100 py-3 fw-800 shadow-lg" style="border-radius: 15px;">Authorize Ledger Entry</button>
                        </form>
                    </div>

                    <!-- Risk Info -->
                    <div class="data-table-card p-5 border-0 bg-white shadow-soft" style="border-radius: 24px;">
                        <h6 class="fw-800 text-xs text-uppercase text-rose mb-4" style="letter-spacing: 1px;"><i class="fa-solid fa-shield-halved me-2"></i> Security Audit Trail</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-4 d-flex justify-content-between align-items-center">
                                <span class="text-xs fw-800 text-muted text-uppercase">KYC Evolution</span>
                                <strong class="text-sm fw-800 <?php echo $user['kyc_status'] == 'Verified' ? 'text-success' : 'text-warning'; ?>"><?php echo strtoupper($user['kyc_status']); ?></strong>
                            </li>
                            <li class="mb-4 d-flex justify-content-between align-items-center">
                                <span class="text-xs fw-800 text-muted text-uppercase">Account Number</span>
                                <strong class="text-sm fw-800"><?php echo $user['account_number']; ?></strong>
                            </li>
                            <li class="mb-0 d-flex justify-content-between align-items-center">
                                <span class="text-xs fw-800 text-muted text-uppercase">Cumulative Outflows</span>
                                <strong class="text-sm fw-800 text-rose">$<?php echo number_format($cumulative_outflows, 2); ?></strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="mt-auto py-5 px-4 border-top bg-white text-center text-muted" style="font-size: 0.85rem;">
            SwiftCapital Admin © 2026. Internal System Only.
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .shadow-inner {
            box-shadow: inset 0 2px 8px 0 rgba(0, 0, 0, 0.05);
        }
        .btn-check:checked + label {
            background-color: var(--admin-primary) !important;
            color: white !important;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.2);
        }
        .btn-check:checked + label.btn-outline-success {
            background-color: #10b981 !important;
        }
        .btn-check:checked + label.btn-outline-danger {
            background-color: #f43f5e !important;
        }
    </style>
</body>
</html>
