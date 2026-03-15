<?php require_once '../includes/user-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan History - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .table-premium th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            color: #475569;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            padding: 1rem;
        }
        .table-premium td {
            vertical-align: middle;
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            font-size: 0.9rem;
        }
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 50rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-pending { background-color: #fef9c3; color: #854d0e; }
        .status-approved { background-color: #dcfce7; color: #166534; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        .status-completed { background-color: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>

<?php 
$page = 'loan-history';
include '../includes/user-sidebar.php'; 
?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <?php include '../includes/user-navbar.php'; ?>

        <?php 
        $user_id = $_SESSION['user_id'];
        $stmt = $pdo->prepare("SELECT * FROM loans WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        $loans = $stmt->fetchAll();
        ?>

        <!-- Page Content -->
        <div class="page-container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">

                    <?php if (isset($_SESSION['success_msg'])): ?>
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; border: none; background: #ecfdf5; color: #065f46;">
                            <i class="fa-solid fa-circle-check me-2"></i> <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="page-header-centered">
                        <div class="header-icon-circle">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <h1 class="page-title-centered">Loan History</h1>
                        <p class="page-subtitle-centered">Review your past and current loan applications, tracking their status and disbursement history.</p>
                    </div>

                    <!-- History Card -->
                    <div class="card card-premium overflow-hidden border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom p-4">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                <h5 class="fw-bold mb-0">My Loan Applications</h5>
                                <div class="d-flex gap-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0 bg-light fs-7" placeholder="Search loans...">
                                    </div>
                                    <button class="btn btn-outline-light btn-sm flex-shrink-0 text-dark border"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-premium mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Loan ID</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Duration</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($loans)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <i class="fa-solid fa-folder-open text-muted fs-1 mb-3 d-block"></i>
                                                    <p class="text-muted mb-0">You haven't applied for any loans yet.</p>
                                                    <a href="loan-application.php" class="btn btn-primary btn-sm mt-3">Apply Now</a>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($loans as $loan): ?>
                                                <tr>
                                                    <td><?php echo date('M d, Y', strtotime($loan['created_at'])); ?></td>
                                                    <td><span class="fw-semibold text-primary">#LN-<?php echo str_pad($loan['id'], 5, '0', STR_PAD_LEFT); ?></span></td>
                                                    <td><?php echo htmlspecialchars($loan['loan_type']); ?></td>
                                                    <td><span class="fw-bold">$<?php echo number_format($loan['amount'], 2); ?></span></td>
                                                    <td><?php echo $loan['term_months']; ?> Mo</td>
                                                    <td>
                                                        <?php 
                                                        $status = $loan['status'];
                                                        $cls = 'status-pending';
                                                        if($status == 'Approved' || $status == 'Disbursed') $cls = 'status-approved';
                                                        if($status == 'Rejected') $cls = 'status-rejected';
                                                        if($status == 'Completed') $cls = 'status-completed';
                                                        ?>
                                                        <span class="status-badge <?php echo $cls; ?>"><?php echo $status; ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted fs-7">Showing <?php echo count($loans); ?> results</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Info -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm p-4 bg-primary text-white">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">Need assistance?</h6>
                                </div>
                                <p class="fs-7 mb-0 opacity-75">If you have questions about your loan status, contact our support team anytime.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm p-4 bg-light">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fa-solid fa-plus"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 text-dark">New Loan</h6>
                                </div>
                                <p class="fs-7 mb-2 text-muted">Ready for another loan? Apply in minutes.</p>
                                <a href="loan-application.php" class="stretched-link text-primary fw-bold text-decoration-none fs-7">Apply Now <i class="fa-solid fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <div class="brand">
                <span class="text-primary fw-bold" style="letter-spacing: -0.5px;">Swift</span><span class="text-dark fw-bold" style="letter-spacing: -0.5px;">Capital</span> © 2026 SwiftCapital. All rights reserved.
            </div>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Support</a>
            </div>
        </footer>
    </main>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set dynamic times
        document.addEventListener('DOMContentLoaded', function() {
            const dateNodes = document.querySelectorAll('#currentDate');
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = now.toLocaleDateString('en-US', options);
            dateNodes.forEach(node => node.textContent = formattedDate);
        });
    </script>
</body>
</html>
