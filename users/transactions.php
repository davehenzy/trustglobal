<?php 
require_once '../includes/db.php';
require_once '../includes/user-check.php'; 

$user_id = $_SESSION['user_id'];

// Search logic
$search = $_GET['search'] ?? '';
$where_sql = "";
$params = [$user_id];

if ($search) {
    $where_sql = " AND (txn_hash LIKE ? OR narration LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? $where_sql ORDER BY created_at DESC");
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Fetch summary stats for user header
$balance = $pdo->query("SELECT balance FROM users WHERE id = $user_id")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>

        
        .search-container {
            position: relative;
            margin-bottom: 25px;
        }
        .search-container i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .search-input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s;
        }
        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .transactions-table-container {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .table-header-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1.5fr 2fr 1fr 1fr 1fr;
            padding: 15px 20px;
            background-color: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-empty-body {
            padding: 80px 20px;
            text-align: center;
        }
        .table-empty-body .icon {
            font-size: 3.5rem;
            color: #cbd5e1;
            margin-bottom: 15px;
        }
        .table-empty-body h5 {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        .table-empty-body p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .transactions-table-container .table-header-row:nth-child(even) {
            background-color: #f8fafc !important;
        }
        .transactions-table-container .table-header-row:nth-child(odd) {
            background-color: #ffffff !important;
        }
    </style>
</head>
<body>

<?php 
$page = 'transactions';
include '../includes/user-sidebar.php'; 
?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <?php include '../includes/user-navbar.php'; ?>

        <!-- Page Content -->
        <div class="page-container">

            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; border: none; background: #ecfdf5; color: #065f46;">
                    <i class="fa-solid fa-circle-check me-2"></i> <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; border: none; background: #fef2f2; color: #991b1b;">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="page-title">Transaction History</h1>
                    <div class="breadcrumb-text">
                        <a href="index.php">Dashboard</a> <i class="fa-solid fa-chevron-right mx-2" style="font-size: 0.7rem;"></i> Main Menu <i class="fa-solid fa-chevron-right mx-2" style="font-size: 0.7rem;"></i> Transactions
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary bg-white"><i class="fa-solid fa-filter me-2"></i> Filter</button>
                    <button class="btn btn-primary"><i class="fa-solid fa-download me-2"></i> Export</button>
                </div>
            </div>

            <div class="search-container">
                <form method="GET">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="search-input" placeholder="Search by transaction reference..." value="<?php echo htmlspecialchars($search); ?>">
                </form>
            </div>

            <div class="transactions-table-container">
                <div class="table-header-row text-center">
                    <div>AMOUNT</div>
                    <div>TYPE</div>
                    <div>STATUS</div>
                    <div>REFERENCE ID</div>
                    <div style="grid-column: span 2;">DESCRIPTION</div>
                    <div>CREATED</div>
                    <div>ACTION</div>
                </div>
                
                <?php if (empty($transactions)): ?>
                <div class="table-empty-body">
                    <i class="fa-solid fa-folder-open icon"></i>
                    <h5>No transactions found</h5>
                    <p>Try adjusting your search or filter parameters</p>
                </div>
                <?php else: ?>
                <?php foreach ($transactions as $tx): ?>
                <div class="table-header-row text-center" style="text-transform: none; font-weight: 400; color: #1e293b; padding: 20px;">
                    <div class="fw-bold <?php echo in_array($tx['type'], ['Deposit', 'Credit']) ? 'text-success' : 'text-danger'; ?>">
                        <?php echo in_array($tx['type'], ['Deposit', 'Credit']) ? '+' : '-'; ?>$<?php echo number_format($tx['amount'], 2); ?>
                    </div>
                    <div><span class="badge bg-light text-dark border"><?php echo $tx['type']; ?></span></div>
                    <div>
                        <?php 
                        $status_class = $tx['status'] == 'Completed' ? 'success' : ($tx['status'] == 'Pending' ? 'warning' : 'danger');
                        ?>
                        <span class="text-<?php echo $status_class; ?> fw-bold"><i class="fa-solid fa-circle-dot me-1" style="font-size: 8px;"></i> <?php echo $tx['status']; ?></span>
                    </div>
                    <div class="fw-mono text-xs opacity-75">#<?php echo $tx['txn_hash']; ?></div>
                    <div style="grid-column: span 2;" class="text-xs text-muted"><?php echo htmlspecialchars($tx['narration']); ?></div>
                    <div class="text-xs"><?php echo date('M d, Y', strtotime($tx['created_at'])); ?></div>
                    <div>
                        <a href="transaction-view.php?id=<?php echo $tx['id']; ?>" class="btn btn-sm btn-light p-1 px-2 border" title="View Detail"><i class="fa-solid fa-receipt"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>

        <!-- Footer -->
        <footer class="main-footer mt-auto">
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
