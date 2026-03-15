<?php require_once '../includes/admin-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Audit - SwiftCapital Admin</title>
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
            <a href="loans.php" class="nav-link active">
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
                <h4 class="mb-0 fw-800">Credit Audit: #LOR-88219</h4>
            </div>

            <div class="user-nav">
                <a href="loans.php" class="btn btn-light-indigo btn-sm fw-800 px-3" style="border-radius: 10px;"><i class="fa-solid fa-arrow-left me-1"></i> Back to Ledger</a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-padding">
            
            <div class="row g-4">
                <!-- Loan Summary Left -->
                <div class="col-lg-8">
                    <div class="data-table-card p-5 border-0 bg-white" style="border-radius: 24px;">
                        <div class="d-flex justify-content-between align-items-start mb-5">
                            <div>
                                <h5 class="fw-800 mb-2">Strategic Expansion Credit</h5>
                                <span class="status-badge status-pending px-3 py-2 fw-800" style="border-radius: 10px;">Vetting Phase</span>
                            </div>
                            <div class="text-end">
                                <h2 class="fw-800 mb-0 text-primary" style="letter-spacing: -1px;">$45,000.00</h2>
                                <p class="text-xs fw-600 text-muted mb-0">Requested Principal Amount</p>
                            </div>
                        </div>

                        <div class="row g-4 mb-5">
                            <div class="col-md-4">
                                <div class="p-4 bg-light-soft rounded-4 border-0" style="background: #f8fafc;">
                                    <p class="text-xs text-muted text-uppercase fw-800 mb-2">Vanguard Term</p>
                                    <h6 class="fw-800 mb-0">36 Months</h6>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-4 bg-light-soft rounded-4 border-0" style="background: #f8fafc;">
                                    <p class="text-xs text-muted text-uppercase fw-800 mb-2">APR Percentage</p>
                                    <h6 class="fw-800 mb-0">6.25% (Premium)</h6>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-4 bg-light-soft rounded-4 border-0" style="background: #f8fafc;">
                                    <p class="text-xs text-muted text-uppercase fw-800 mb-2">Monthly Velocity</p>
                                    <h6 class="fw-800 mb-0 text-dark">$1,374.20</h6>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h6 class="fw-800 text-xs text-uppercase text-muted mb-3">Enterprise Narrative</h6>
                            <div class="p-4 rounded-4 text-sm fw-500 shadow-inner" style="background: #f8fafc; line-height: 1.6;">
                                "The capital injection is designated for the implementation of high-frequency trading infrastructure and digital asset settlement protocols. Expected scalability milestones within the 12-month fiscal period with a projected ROI of 24%."
                            </div>
                        </div>

                        <div class="mb-5">
                            <h6 class="fw-800 text-xs text-uppercase text-muted mb-4">Verification Artifacts</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3 border-0 rounded-4 shadow-sm bg-white">
                                        <div class="stat-icon bg-rose-light text-danger me-3" style="width: 45px; height: 45px; border-radius: 12px;">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="fw-800 text-sm text-truncate">Strategic_Plan_2026.pdf</div>
                                            <div class="text-xs fw-600 text-muted uppercase">Deployment Roadmap</div>
                                        </div>
                                        <button class="btn btn-light-soft border-0 shadow-none btn-sm ms-auto text-primary" style="background: #f1f5f9; border-radius: 8px;"><i class="fa-solid fa-download"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3 border-0 rounded-4 shadow-sm bg-white">
                                        <div class="stat-icon bg-indigo-light text-primary me-3" style="width: 45px; height: 45px; border-radius: 12px;">
                                            <i class="fa-solid fa-file-invoice-dollar"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="fw-800 text-sm text-truncate">Revenue_Audit_Q4.png</div>
                                            <div class="text-xs fw-600 text-muted uppercase">Proof of Liquidity</div>
                                        </div>
                                        <button class="btn btn-light-soft border-0 shadow-none btn-sm ms-auto text-primary" style="background: #f1f5f9; border-radius: 8px;"><i class="fa-solid fa-eye"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button class="btn btn-primary px-5 py-3 fw-800 flex-grow-1" style="border-radius: 15px;"><i class="fa-solid fa-check-circle me-2"></i> Authorize Disbursement</button>
                            <button class="btn btn-rose-light text-danger px-5 py-3 fw-800 flex-grow-1" style="border-radius: 15px;"><i class="fa-solid fa-times-circle me-2"></i> Reject Proposal</button>
                        </div>
                    </div>
                </div>

                <!-- Client Side Info Right -->
                <div class="col-lg-4">
                    <div class="data-table-card p-5 border-0 bg-white mb-4" style="border-radius: 24px;">
                        <h6 class="fw-800 mb-4 text-xs text-uppercase text-muted">Client Credit Profile</h6>
                        <div class="d-flex align-items-center mb-5">
                            <div class="admin-avatar me-4 bg-indigo text-white shadow" style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: 800; border-radius: 18px;">KC</div>
                            <div>
                                <h6 class="fw-800 mb-1">Kante Calm</h6>
                                <p class="text-xs fw-600 text-muted mb-0">Elite Merchant Account</p>
                            </div>
                        </div>

                        <div class="row text-center g-3 mb-5">
                            <div class="col-6">
                                <div class="p-3 border-0 rounded-4" style="background: #f8fafc;">
                                    <div class="text-xs text-muted text-uppercase fw-800 mb-2">Trust Score</div>
                                    <div class="h4 fw-800 text-success mb-0">820</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border-0 rounded-4" style="background: #f8fafc;">
                                    <div class="text-xs text-muted text-uppercase fw-800 mb-2">Exposure</div>
                                    <div class="h4 fw-800 text-primary mb-0">Negl.</div>
                                </div>
                            </div>
                        </div>

                        <div class="list-group list-group-flush mb-5">
                            <div class="list-group-item d-flex justify-content-between px-0 py-3 border-light">
                                <span class="text-muted fw-600">ID Reference</span>
                                <span class="fw-800 text-mono">0039912201</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between px-0 py-3 border-light">
                                <span class="text-muted fw-600">Merchant Since</span>
                                <span class="fw-800">Feb 2024</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between px-0 py-3 border-light">
                                <span class="text-muted fw-600">Active Liabilities</span>
                                <span class="fw-800">None</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between px-0 py-3 border-0">
                                <span class="text-muted fw-600">Verified Status</span>
                                <span class="status-badge status-active py-1 px-3 fw-800" style="font-size: 0.7rem; border-radius: 6px;">Platinum</span>
                            </div>
                        </div>

                        <h6 class="fw-800 mb-4 text-xs text-uppercase text-muted">Internal Audit Notes</h6>
                        <textarea class="form-control bg-light border-0 fw-500 p-4 mb-4" rows="4" style="border-radius: 15px; font-size: 0.85rem;" placeholder="Initialize administrative risk assessment..."></textarea>
                        <button class="btn btn-dark w-100 py-3 fw-800" style="border-radius: 15px;">Commit Audit Entry</button>
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
    </style>
</body>
</html>
