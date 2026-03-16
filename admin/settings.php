<?php 
require_once '../includes/admin-check.php'; 
if ($_SESSION['role'] !== 'Super Admin') {
    header("Location: index.php");
    exit();
} 

// Handle Updates
$success_msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
    foreach ($_POST as $key => $value) {
        if ($key == 'update_settings') continue;
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    $success_msg = 'System settings updated successfully!';
}

// Fetch settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings_raw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

function getSetting($key, $default = '') {
    global $settings_raw;
    return $settings_raw[$key] ?? $default;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - SwiftCapital Admin</title>
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
            <a href="transactions.php" class="nav-link">
                <i class="fa-solid fa-money-bill-transfer"></i> Transactions
            </a>
            <a href="credits.php" class="nav-link">
                <i class="fa-solid fa-circle-dollar-to-slot"></i> Credit Requests
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
            <a href="settings.php" class="nav-link active">
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
                <h4 class="mb-0 fw-800">System Configuration</h4>
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
            
            <div class="row g-4">
                <!-- Sidebar Tabs -->
                <div class="col-lg-3">
                    <div class="data-table-card p-3 border-0 bg-white" style="border-radius: 20px;">
                        <div class="nav flex-column nav-pills gap-2" id="settingsTabs" role="tablist">
                            <button class="nav-link active text-start py-3 fw-800 border-0" data-bs-toggle="pill" data-bs-target="#general" type="button" style="border-radius: 12px;"><i class="fa-solid fa-globe me-2"></i> Global Config</button>
                            <button class="nav-link text-start py-3 fw-800 border-0" data-bs-toggle="pill" data-bs-target="#banking" type="button" style="border-radius: 12px;"><i class="fa-solid fa-building-columns me-2"></i> Financial Logic</button>
                            <button class="nav-link text-start py-3 fw-800 border-0" data-bs-toggle="pill" data-bs-target="#security" type="button" style="border-radius: 12px;"><i class="fa-solid fa-lock me-2"></i> Encryption & API</button>
                            <button class="nav-link text-start py-3 fw-800 border-0" data-bs-toggle="pill" data-bs-target="#notifications" type="button" style="border-radius: 12px;"><i class="fa-solid fa-envelope-open-text me-2"></i> Communications</button>
                        </div>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="col-lg-9">
                    <div class="data-table-card p-5 border-0 bg-white" style="border-radius: 24px;">
                        <div class="tab-content">
                            <!-- General -->
                            <div class="tab-pane fade show active" id="general">
                                <form method="POST">
                                <input type="hidden" name="update_settings" value="1">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h4 class="fw-800 mb-0">Global Configuration</h4>
                                    <button type="submit" class="btn btn-primary px-4 fw-800" style="border-radius: 10px;">Save Settings</button>
                                </div>
                                <?php if ($success_msg): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; font-weight: 600;">
                                        <?php echo $success_msg; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Platform Identity Name</label>
                                            <input type="text" name="site_name" class="form-control bg-light border-0 fw-600" value="<?php echo htmlspecialchars(getSetting('site_name', 'SwiftCapital Online Banking')); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Core System Email</label>
                                            <input type="email" name="system_email" class="form-control bg-light border-0 fw-600" value="<?php echo htmlspecialchars(getSetting('system_email', 'noreply@trustsglobal.com')); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Settlement Currency</label>
                                            <select name="base_currency" class="form-select bg-light border-0 fw-600">
                                                <option value="USD" <?php echo getSetting('base_currency') == 'USD' ? 'selected' : ''; ?>>USD - US Dollar ($)</option>
                                                <option value="EUR" <?php echo getSetting('base_currency') == 'EUR' ? 'selected' : ''; ?>>EUR - Euro (€)</option>
                                                <option value="GBP" <?php echo getSetting('base_currency') == 'GBP' ? 'selected' : ''; ?>>GBP - British Pound (£)</option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Banking -->
                            <div class="tab-pane fade" id="banking">
                                <form method="POST">
                                <input type="hidden" name="update_settings" value="1">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h4 class="fw-800 mb-0">Financial Velocity Logic</h4>
                                    <button type="submit" class="btn btn-primary px-4 fw-800" style="border-radius: 10px;">Deploy Rates</button>
                                </div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Minimum Asset Entry (Deposit)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0 fw-800">$</span>
                                                <input type="number" name="min_deposit" class="form-control bg-light border-0 fw-600" value="<?php echo htmlspecialchars(getSetting('min_deposit', '100')); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Maximum Daily Outflow</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0 fw-800">$</span>
                                                <input type="number" name="max_withdrawal" class="form-control bg-light border-0 fw-600" value="<?php echo htmlspecialchars(getSetting('max_withdrawal', '500000')); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Loan APR (%)</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" name="loan_apr" class="form-control bg-light border-0 fw-600" value="<?php echo htmlspecialchars(getSetting('loan_apr', '6.5')); ?>">
                                                <span class="input-group-text bg-light border-0 fw-800">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Security -->
                            <div class="tab-pane fade" id="security">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h4 class="fw-800 mb-0">Encryption & API Matrix</h4>
                                    <button class="btn btn-danger px-4 fw-800" style="border-radius: 10px;">Wipe Sessions</button>
                                </div>
                                <div class="alert bg-rose-light text-danger border-0 p-4 mb-5" style="border-radius: 15px;">
                                    <div class="d-flex gap-3 align-items-center">
                                        <i class="fa-solid fa-triangle-exclamation fs-3"></i>
                                        <div>
                                            <div class="fw-800">Critical Access Area</div>
                                            <div class="text-xs fw-600 opacity-75">Modifying these keys will immediately affect live transactions and payment routing.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-800 text-xs text-uppercase text-muted">Vanguard Security Token</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control bg-light border-0 fw-600" value="vanguard_live_82910xkjs91">
                                        <button class="btn btn-light border-0 fw-800 px-4">VIEW</button>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-800 text-xs text-uppercase text-muted">Settlement Gateway Secret</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control bg-light border-0 fw-600" value="gateway_sk_t8219012903">
                                        <button class="btn btn-light border-0 fw-800 px-4">VIEW</button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        .nav-pills .nav-link {
            color: #64748b;
            transition: 0.3s;
        }
        .nav-pills .nav-link:hover {
            background: #f8fafc;
            color: var(--admin-primary);
        }
        .nav-pills .nav-link.active {
            background: #eef2ff !important;
            color: var(--admin-primary) !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
        }
        .form-control:focus, .form-select:focus {
            background-color: #fff !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
    </style>
</body>
</html>
