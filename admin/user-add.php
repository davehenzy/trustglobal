<?php 
require_once '../includes/admin-check.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $balance = $_POST['balance'];
    $account_type = $_POST['account_type'];
    $status = 'Active'; // Default for admin-added users

    // Generate a username if none provided
    $username = strtolower(str_replace(' ', '', $name)) . rand(100, 999);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, lastname, username, email, password, phone, balance, account_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, '', $username, $email, $password, $phone, $balance, $account_type, $status]);
        $success = "User enrolled successfully!";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - SwiftCapital Admin</title>
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
                <h4 class="mb-0 fw-800">Elite User Enrollment</h4>
            </div>

            <div class="user-nav">
                <a href="users.php" class="btn btn-light-indigo btn-sm fw-800 px-3" style="border-radius: 10px;"><i class="fa-solid fa-arrow-left me-2"></i> User Directory</a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-padding">
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="data-table-card p-5 border-0 bg-white shadow-lg" style="border-radius: 30px;">
                        <div class="row mb-5 align-items-center">
                            <div class="col-md-7">
                                <h3 class="fw-800 mb-2" style="letter-spacing: -1px;">Onboard New Client</h3>
                                <p class="text-muted fw-500 mb-0">Initialize premium account parameters for manual institutional onboarding.</p>
                            </div>
                            <div class="col-md-5 text-md-end">
                                <div class="bg-indigo-light d-inline-block px-4 py-2 rounded-4">
                                    <span class="text-xs fw-800 text-primary">MANUAL ENROLLMENT PROTOCOL</span>
                                </div>
                            </div>
                        </div>

                        <?php if(isset($success)): ?>
                            <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="row g-5">
                                <!-- Step 1: Identity -->
                                <div class="col-md-12">
                                    <h6 class="fw-800 text-xs text-uppercase text-muted mb-4" style="letter-spacing: 1px;">Core Identity & Credentials</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Legal Entity Full Name</label>
                                    <input type="text" name="name" class="form-control bg-light border-0 fw-600 p-3" style="border-radius: 12px;" placeholder="e.g. Johnathan Bryan" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Institutional Email Address</label>
                                    <input type="email" name="email" class="form-control bg-light border-0 fw-600 p-3" style="border-radius: 12px;" placeholder="client@vanguard.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Secure Access Key (Password)</label>
                                    <div class="input-group">
                                        <input type="password" name="password" class="form-control bg-light border-0 fw-600 p-3" style="border-radius: 12px 0 0 12px;" placeholder="Set initial passcode" required>
                                        <button class="btn btn-light border-0 px-4" type="button" style="border-radius: 0 12px 12px 0;"><i class="fa-solid fa-eye-slash"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Primary Contact Sequence</label>
                                    <input type="text" name="phone" class="form-control bg-light border-0 fw-600 p-3" style="border-radius: 12px;" placeholder="+1 (Vanguard) 000-000">
                                </div>

                                <!-- Step 2: Account Config -->
                                <div class="col-md-12 mt-5">
                                    <h6 class="fw-800 text-xs text-uppercase text-muted mb-4" style="letter-spacing: 1px;">Account Archetype Configuration</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Initial Liquidity Entry ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 fw-800" style="border-radius: 12px 0 0 12px;">$</span>
                                        <input type="number" step="0.01" name="balance" class="form-control bg-light border-0 fw-600 p-3" style="border-radius: 0 12px 12px 0;" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Select Asset Category</label>
                                    <select name="account_type" class="form-select bg-light border-0 fw-600 p-3" style="border-radius: 12px;">
                                        <option value="Savings Account">Elite Savings (Standard)</option>
                                        <option value="Investment Account">Vanguard High-Yield</option>
                                        <option value="Checking Account">Institutional Checking</option>
                                        <option value="Business Account">Private Wealth Portfolio</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Auto-Generated Ledger Reference</label>
                                    <input type="text" class="form-control bg-light-soft border-0 fw-800 p-3 opacity-50" style="border-radius: 12px;" value="SWIFT-AUTO-GEN" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs fw-800 text-muted text-uppercase mb-3">Initial Compliance Status</label>
                                    <div class="d-flex gap-4 mt-2">
                                        <div class="form-check custom-check">
                                            <input class="form-check-input" type="radio" name="kycStat" id="kyc1" checked>
                                            <label class="form-check-label fw-800 text-sm text-muted" for="kyc1">Awaiting Docs</label>
                                        </div>
                                        <div class="form-check custom-check">
                                            <input class="form-check-input" type="radio" name="kycStat" id="kyc2">
                                            <label class="form-check-label fw-800 text-sm text-primary" for="kyc2">Priority Verified</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-5">
                                    <div class="p-4 rounded-4 border-0 d-flex align-items-center gap-4 bg-indigo-light">
                                        <div class="stat-icon bg-white text-primary shadow-sm" style="width: 50px; height: 50px; border-radius: 15px;">
                                            <i class="fa-solid fa-envelope-shield"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-800 mb-1">System Notification Protocol</h6>
                                            <p class="text-xs fw-500 mb-0">An automated welcome artifacts and access keys will be dispatched to the client upon commitment. Proceed with caution.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 text-end mt-5 pt-4">
                                    <hr class="mb-5 opacity-10">
                                    <button type="reset" class="btn btn-light-soft px-5 py-3 fw-800 me-3" style="border-radius: 15px; background: #f1f5f9;">Reset Artifact</button>
                                    <button type="submit" class="btn btn-primary px-5 py-3 fw-800 shadow-lg" style="border-radius: 15px;">Commit Enrollment</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="mt-auto py-5 px-4 border-top bg-white text-center text-muted" style="font-size: 0.85rem;">
            SwiftCapital Admin Â© 2026. Internal System Only.
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .custom-check .form-check-input:checked {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
        }
        .form-control:focus, .form-select:focus {
            background-color: #fff !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
    </style>
</body>
</html>
