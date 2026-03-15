<?php require_once '../includes/admin-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KYC Queue - SwiftCapital Admin</title>
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
                <h4 class="mb-0 fw-800">Compliance & KYC</h4>
            </div>

            <div class="user-nav">
                <div class="notification-bell">
                    <i class="fa-solid fa-bell fs-5"></i>
                    <span class="notification-dot"></span>
                </div>
                
                <div class="admin-profile">
                    <div class="admin-avatar">AD</div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold text-sm">Admin Master</div>
                        <div class="text-xs text-muted">Super Administrator</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-padding">
            
            <!-- KYC Dash -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Pending Approval</p>
                            <h4 class="mb-0 fw-800">24</h4>
                        </div>
                        <div class="stat-icon bg-amber-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-hourglass-start"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Approved (24h)</p>
                            <h4 class="mb-0 fw-800">142</h4>
                        </div>
                        <div class="stat-icon bg-emerald-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-check-double"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Rejected Today</p>
                            <h4 class="mb-0 fw-800">08</h4>
                        </div>
                        <div class="stat-icon bg-rose-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-ban"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Compliance Rate</p>
                            <h4 class="mb-0 fw-800">94.2%</h4>
                        </div>
                        <div class="stat-icon bg-indigo-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-shield-halved"></i></div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- KYC Table -->
                <div class="col-lg-12">
                    <div class="data-table-card mt-0">
                        <div class="card-header border-0 bg-transparent py-4 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-800">Verification Queue</h5>
                            <div class="d-flex gap-2">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search"></i></span>
                                    <input type="text" class="form-control border-start-0" placeholder="Filter by email or name...">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Applicant Profile</th>
                                        <th>Document Type</th>
                                        <th>Verification Status</th>
                                        <th>Submission Date</th>
                                        <th>Resolution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="admin-avatar" style="width: 36px; height: 36px; font-size: 0.8rem;">RB</div>
                                                <div>
                                                    <div class="fw-bold">Robert Bryan</div>
                                                    <div class="text-xs text-muted">rbryan@company.com</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-600">International Passport</div>
                                            <div class="text-xs text-primary">#DOC-9210-PAS</div>
                                        </td>
                                        <td><span class="status-badge status-pending">In Review</span></td>
                                        <td class="text-sm">Mar 15, 2026 (12:45)</td>
                                        <td><a href="kyc-view.php" class="btn btn-primary btn-sm px-3 fw-bold text-xs" style="border-radius: 8px;">Audit Review</a></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="https://ui-avatars.com/api/?name=Sarah+Smith" class="user-avatar-sm" style="width: 36px; height: 36px;" alt="">
                                                <div>
                                                    <div class="fw-bold">Sarah Smith</div>
                                                    <div class="text-xs text-muted">s.smith@mail.com</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-600">National ID Card</div>
                                            <div class="text-xs text-primary">#DOC-8112-NID</div>
                                        </td>
                                        <td><span class="status-badge status-pending">In Review</span></td>
                                        <td class="text-sm">Mar 14, 2026 (16:20)</td>
                                        <td><a href="kyc-view.php" class="btn btn-primary btn-sm px-3 fw-bold text-xs" style="border-radius: 8px;">Audit Review</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white border-top p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-xs text-muted fw-bold uppercase">Showing the last 2 verification requests</div>
                                <a href="#" class="text-primary text-xs fw-bold text-decoration-none">View Archive <i class="fa-solid fa-arrow-right-long ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="mt-auto py-4 px-4 border-top bg-white text-center text-muted" style="font-size: 0.85rem;">
            SwiftCapital Admin © 2026. Internal System Only.
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
