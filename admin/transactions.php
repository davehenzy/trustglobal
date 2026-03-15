<?php require_once '../includes/admin-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - SwiftCapital Admin</title>
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
            <a href="transactions.php" class="nav-link active">
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
                <h4 class="mb-0 fw-800">Financial Ledger</h4>
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
            
            <!-- Quick Stats Overview -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Today's Inflow</p>
                            <h4 class="mb-0 fw-800">$142,500.00</h4>
                        </div>
                        <div class="stat-icon bg-emerald-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-arrow-trend-up"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Total Volume (24h)</p>
                            <h4 class="mb-0 fw-800">$850,210.00</h4>
                        </div>
                        <div class="stat-icon bg-indigo-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-chart-line"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card" style="padding: 20px;">
                        <div>
                            <p class="text-xs text-muted fw-bold text-uppercase mb-1">Pending Requests</p>
                            <h4 class="mb-0 fw-800">12</h4>
                        </div>
                        <div class="stat-icon bg-amber-light" style="width: 45px; height: 45px; font-size: 1rem;"><i class="fa-solid fa-hourglass-half"></i></div>
                    </div>
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div class="d-flex gap-2 flex-wrap">
                    <div class="input-group" style="max-width: 320px;">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Hash, ID, or user email...">
                    </div>
                    <select class="form-select" style="max-width: 140px;">
                        <option selected>Type</option>
                        <option>Deposit</option>
                        <option>Withdrawal</option>
                        <option>Transfer</option>
                    </select>
                    <select class="form-select" style="max-width: 140px;">
                        <option selected>Status</option>
                        <option>Success</option>
                        <option>Pending</option>
                        <option>Failed</option>
                    </select>
                </div>
                <button class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="fa-solid fa-file-export"></i>
                    Export Records
                </button>
            </div>

            <!-- Transactions Table -->
            <div class="data-table-card mt-0">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Transaction Ref</th>
                                <th>Client Details</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Timestamp</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="text-xs fw-mono text-uppercase bg-light px-2 py-1 rounded">#TX-882194</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="admin-avatar" style="width: 32px; height: 32px; font-size: 0.7rem;">KC</div>
                                        <div>
                                            <div class="fw-bold">Kante Calm</div>
                                            <div class="text-xs text-muted">kante@mail.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-800 text-success">+$2,500.00</td>
                                <td>
                                    <div class="text-sm fw-600">Wire Transfer</div>
                                    <div class="text-xs text-muted">Deposit</div>
                                </td>
                                <td><span class="status-badge status-active">Completed</span></td>
                                <td class="text-sm">Today, 15:20</td>
                                <td><a href="transaction-view.php" class="action-btn"><i class="fa-solid fa-eye"></i></a></td>
                            </tr>
                            <tr>
                                <td><span class="text-xs fw-mono text-uppercase bg-light px-2 py-1 rounded">#TX-711023</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name=Alice+Jones" class="user-avatar-sm" style="width: 32px; height: 32px;" alt="">
                                        <div>
                                            <div class="fw-bold">Alice Jones</div>
                                            <div class="text-xs text-muted">alice.j@mail.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-800 text-danger">-$500.00</td>
                                <td>
                                    <div class="text-sm fw-600">Flash Transfer</div>
                                    <div class="text-xs text-muted">Withdrawal</div>
                                </td>
                                <td><span class="status-badge status-active">Completed</span></td>
                                <td class="text-sm">Today, 12:05</td>
                                <td><a href="transaction-view.php" class="action-btn"><i class="fa-solid fa-eye"></i></a></td>
                            </tr>
                            <tr>
                                <td><span class="text-xs fw-mono text-uppercase bg-light px-2 py-1 rounded">#TX-990122</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="admin-avatar" style="width: 32px; height: 32px; font-size: 0.7rem; background: #64748b;">RB</div>
                                        <div>
                                            <div class="fw-bold">Robert Bryan</div>
                                            <div class="text-xs text-muted">rbryan@company.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-800 text-dark">$10,000.00</td>
                                <td>
                                    <div class="text-sm fw-600">Crypto (USDT)</div>
                                    <div class="text-xs text-muted">Deposit</div>
                                </td>
                                <td><span class="status-badge status-pending">In Review</span></td>
                                <td class="text-sm">Mar 13, 21:44</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="action-btn dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li><button class="dropdown-item py-2 text-success fw-bold"><i class="fa-solid fa-check me-2"></i> Approve</button></li>
                                            <li><button class="dropdown-item py-2 text-danger fw-bold"><i class="fa-solid fa-xmark me-2"></i> Reject</button></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item py-2" href="transaction-view.php"><i class="fa-solid fa-circle-info me-2"></i> View Details</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer bg-white border-top p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-xs text-muted fw-bold">SHOWING 3 OF 2,480 TRANSACTIONS</div>
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
