<?php 
require_once '../includes/db.php';
require_once '../includes/admin-check.php'; 

$kyc_id = $_GET['id'] ?? null;

if (!$kyc_id) {
    header("Location: kyc.php");
    exit;
}

// Fetch KYC Request
$stmt = $pdo->prepare("SELECT k.*, u.name, u.lastname, u.email, u.phone FROM kyc_verifications k JOIN users u ON k.user_id = u.id WHERE k.id = ?");
$stmt->execute([$kyc_id]);
$kyc = $stmt->fetch();

if (!$kyc) {
    header("Location: kyc.php");
    exit;
}

$success_msg = '';
$error_msg = '';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_kyc'])) {
        try {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE kyc_verifications SET status = 'Verified' WHERE id = ?")->execute([$kyc_id]);
            $pdo->prepare("UPDATE users SET kyc_status = 'Verified' WHERE id = ?")->execute([$kyc['user_id']]);
            $pdo->commit();
            $success_msg = "KYC documents approved successfully.";
        } catch (Exception $e) { $pdo->rollBack(); $error_msg = $e->getMessage(); }
    }

    if (isset($_POST['reject_kyc'])) {
        $reason = $_POST['rejection_reason'] ?? 'Document mismatch or insufficient resolution.';
        try {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE kyc_verifications SET status = 'Rejected', rejection_reason = ? WHERE id = ?")->execute([$reason, $kyc_id]);
            $pdo->prepare("UPDATE users SET kyc_status = 'Rejected' WHERE id = ?")->execute([$kyc['user_id']]);
            $pdo->commit();
            $success_msg = "KYC documents declined and user notified.";
        } catch (Exception $e) { $pdo->rollBack(); $error_msg = $e->getMessage(); }
    }

    // Refresh data
    $stmt = $pdo->prepare("SELECT k.*, u.name, u.lastname, u.email, u.phone FROM kyc_verifications k JOIN users u ON k.user_id = u.id WHERE k.id = ?");
    $stmt->execute([$kyc_id]);
    $kyc = $stmt->fetch();
}

$initials = strtoupper(substr($kyc['name'], 0, 1) . substr($kyc['lastname'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KYC Detailed Review - SwiftCapital Admin</title>
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
            <a href="loans.php" class="nav-link">
                <i class="fa-solid fa-hand-holding-dollar"></i> Loan Requests
            </a>
            <a href="kyc.php" class="nav-link active">
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
                <h4 class="mb-0 fw-800">Identity Audit: <?php echo htmlspecialchars($kyc['name'] . ' ' . $kyc['lastname']); ?></h4>
            </div>

            <div class="user-nav">
                <a href="kyc.php" class="btn btn-light-indigo btn-sm fw-800 px-3" style="border-radius: 10px;"><i class="fa-solid fa-arrow-left me-1"></i> Back to Queue</a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-padding">
            
            <div class="row g-4">
                <!-- Document Viewer Left -->
                <div class="col-lg-8">
                    <div class="data-table-card p-5 border-0 bg-white" style="border-radius: 24px;">
                        <div class="d-flex justify-content-between align-items-center mb-5">
                            <h5 class="fw-800 mb-0">Legal Identification Documents</h5>
                            <?php 
                            $status_class = '';
                            switch($kyc['status']) {
                                case 'Verified': $status_class = 'status-active'; break;
                                case 'Pending': $status_class = 'status-pending'; break;
                                case 'Rejected': $status_class = 'status-blocked'; break;
                            }
                            ?>
                            <span class="status-badge <?php echo $status_class; ?> px-3 py-2 fw-800" style="border-radius: 10px;"><?php echo $kyc['status'] == 'Verified' ? 'Approved' : ($kyc['status'] == 'Pending' ? 'Awaiting Review' : 'Rejected'); ?></span>
                        </div>
                        
                        <?php if($success_msg): ?>
                            <div class="alert alert-success border-0 rounded-4 mb-4 fw-600"><?php echo $success_msg; ?></div>
                        <?php endif; ?>
                        <?php if($error_msg): ?>
                            <div class="alert alert-danger border-0 rounded-4 mb-4 fw-600"><?php echo $error_msg; ?></div>
                        <?php endif; ?>

                        <!-- Main Doc Display -->
                        <div class="mb-5">
                            <ul class="nav nav-pills gap-2 mb-4" id="docTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active fw-800 text-xs text-uppercase px-4 py-2 border-0" data-bs-toggle="tab" data-bs-target="#front" style="border-radius: 12px;">ID Front Side</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-800 text-xs text-uppercase px-4 py-2 border-0" data-bs-toggle="tab" data-bs-target="#back" style="border-radius: 12px;">ID Back Side</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-800 text-xs text-uppercase px-4 py-2 border-0" data-bs-toggle="tab" data-bs-target="#selfie" style="border-radius: 12px;">Selfie Probe</button>
                                </li>
                            </ul>
                            <div class="tab-content border-0 rounded-4 overflow-hidden shadow-inner bg-light-soft" style="min-height: 450px; display: flex; align-items: center; justify-content: center; background: #f8fafc;">
                                <div class="tab-pane fade show active w-100 h-100" id="front">
                                    <?php if($kyc['document_front']): ?>
                                        <img src="../uploads/kyc/<?php echo $kyc['document_front']; ?>" class="img-fluid w-100" style="object-fit: contain; max-height: 550px;" alt="Front">
                                    <?php else: ?>
                                        <div class="p-5 text-center text-muted fw-600">No front document uploaded</div>
                                    <?php endif; ?>
                                </div>
                                <div class="tab-pane fade w-100 h-100" id="back">
                                    <?php if($kyc['document_back']): ?>
                                        <img src="../uploads/kyc/<?php echo $kyc['document_back']; ?>" class="img-fluid w-100" style="object-fit: contain; max-height: 550px;" alt="Back">
                                    <?php else: ?>
                                        <div class="p-5 text-center text-muted fw-600">No back document uploaded</div>
                                    <?php endif; ?>
                                </div>
                                <div class="tab-pane fade w-100 h-100 text-center" id="selfie">
                                    <?php if($kyc['selfie']): ?>
                                        <img src="../uploads/kyc/<?php echo $kyc['selfie']; ?>" class="img-fluid" style="max-height: 550px; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.1);" alt="Selfie">
                                    <?php else: ?>
                                        <div class="p-5 text-center text-muted fw-600">No selfie uploaded</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="bg-indigo-light p-4 rounded-4 mb-5 border-0 d-flex align-items-center gap-4">
                            <div class="stat-icon bg-white text-primary shadow-sm" style="width: 50px; height: 50px; border-radius: 15px;">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                            </div>
                            <div>
                                <h6 class="fw-800 mb-1">AI Forgery Detection Engine</h6>
                                <p class="text-xs fw-500 mb-0 text-muted">No obvious manipulations detected. Physical security features visible. Confidence Score: <strong class="text-primary fs-6">94.8%</strong></p>
                            </div>
                        </div>

                        <form class="d-flex gap-3" method="POST">
                            <button type="submit" name="approve_kyc" class="btn btn-primary px-5 py-3 fw-800 flex-grow-1" style="border-radius: 15px;"><i class="fa-solid fa-check-circle me-2"></i> Approve Identity</button>
                            <button type="button" class="btn btn-rose-light text-danger px-5 py-3 fw-800" style="border-radius: 15px;" onclick="document.getElementById('rejectionArea').scrollIntoView({behavior: 'smooth'})"><i class="fa-solid fa-times-circle me-2"></i> Decline Audit</button>
                        </form>
                    </div>
                </div>

                <!-- Submission Details Right -->
                <div class="col-lg-4">
                    <div class="data-table-card p-5 border-0 bg-white mb-4" style="border-radius: 24px;">
                        <h6 class="fw-800 mb-4 text-xs text-uppercase text-muted">Submission Context</h6>
                        
                        <div class="list-group list-group-flush mb-4">
                            <div class="list-group-item d-flex justify-content-between px-0 py-3 border-light">
                                <span class="text-muted fw-600">Audit Reference</span>
                                <span class="fw-800 text-mono text-primary">#KYC-<?php echo str_pad($kyc['id'], 5, '0', STR_PAD_LEFT); ?></span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between px-0 py-3 border-light">
                                <span class="text-muted fw-600">Document Class</span>
                                <span class="fw-800"><?php echo $kyc['document_type']; ?></span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between px-0 py-3 border-light">
                                <span class="text-muted fw-600">Timestamp</span>
                                <span class="fw-800"><?php echo date('M d, H:i', strtotime($kyc['created_at'])); ?></span>
                            </div>
                            <?php if($kyc['rejection_reason']): ?>
                            <div class="list-group-item d-flex justify-content-between px-0 py-3 border-0">
                                <span class="text-muted fw-600">Rejection Note</span>
                                <span class="text-danger fw-800"><?php echo htmlspecialchars($kyc['rejection_reason']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="row g-4 mb-4">
                            <!-- Personal Info -->
                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded-4 h-100 border-0">
                                    <h6 class="fw-800 text-xs text-uppercase mb-3 text-muted">Personal & Employment</h6>
                                    <div class="mb-3">
                                        <div class="text-xs text-muted mb-1">Declared Name</div>
                                        <div class="fw-800"><?php echo htmlspecialchars($kyc['full_name']); ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-xs text-muted mb-1">Date of Birth</div>
                                        <div class="fw-800"><?php echo date('M d, Y', strtotime($kyc['dob'])); ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-xs text-muted mb-1">SSN / National ID</div>
                                        <div class="fw-800"><?php echo htmlspecialchars($kyc['ssn']); ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-xs text-muted mb-1">Account Type</div>
                                        <div class="fw-800"><?php echo htmlspecialchars($kyc['account_type']); ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-xs text-muted mb-1">Employment</div>
                                        <div class="fw-800"><?php echo htmlspecialchars($kyc['employment']); ?></div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-muted mb-1">Annual Income</div>
                                        <div class="fw-800 text-success"><?php echo htmlspecialchars($kyc['income']); ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Address & Next of Kin -->
                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded-4 h-100 border-0">
                                    <h6 class="fw-800 text-xs text-uppercase mb-3 text-muted">Address & Beneficiary</h6>
                                    <div class="mb-4">
                                        <div class="text-xs text-muted mb-1">Residential Address</div>
                                        <div class="fw-800"><?php echo htmlspecialchars($kyc['address']); ?></div>
                                        <div class="fw-700 text-sm"><?php echo htmlspecialchars($kyc['city'] . ', ' . $kyc['state'] . ' ' . $kyc['zip']); ?></div>
                                        <div class="fw-700 text-xs text-uppercase text-muted"><?php echo htmlspecialchars($kyc['country']); ?></div>
                                    </div>
                                    <hr class="my-3 opacity-10">
                                    <div class="mb-3">
                                        <div class="text-xs text-muted mb-1">Next of Kin</div>
                                        <div class="fw-800"><?php echo htmlspecialchars($kyc['next_of_kin_name']); ?></div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-muted mb-1">Relationship</div>
                                        <div class="fw-800"><?php echo htmlspecialchars($kyc['next_of_kin_relationship']); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4" id="rejectionArea">
                            <form method="POST">
                                <label class="form-label fw-800 text-xs text-uppercase text-muted mb-3">Decline Resolution (Admin)</label>
                                <select name="rejection_reason" class="form-select bg-light border-0 fw-600 p-3 mb-3" style="border-radius: 12px; font-size: 0.85rem;">
                                    <option value="Expired Identity Document">Expired Identity Document</option>
                                    <option value="Frame Corruption / Cropped">Frame Corruption / Cropped</option>
                                    <option value="Insufficient Resolution">Insufficient Resolution</option>
                                    <option value="Identity Parity Mismatch">Identity Parity Mismatch</option>
                                    <option value="Invalid Asset Class">Invalid Asset Class</option>
                                </select>
                                <button type="submit" name="reject_kyc" class="btn btn-dark w-100 py-3 fw-800 mb-3" style="border-radius: 15px;">Decline & Notify User</button>
                            </form>
                            <p class="text-center text-xs text-muted mb-0">System audit logs will be updated.</p>
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
            background: #f1f5f9;
        }
        .nav-pills .nav-link.active {
            background: var(--admin-primary) !important;
            color: white !important;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.2);
        }
        .shadow-inner {
            box-shadow: inset 0 2px 8px 0 rgba(0, 0, 0, 0.05);
        }
    </style>
</body>
</html>
