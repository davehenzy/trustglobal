<?php 
require_once '../includes/user-check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
$page = 'dashboard';
include '../includes/user-sidebar.php'; 

// Fetch Monthly Stats
$start_of_month = date('Y-m-01 00:00:00');
$user_id = $_SESSION['user_id'];

// Monthly Income (Credits/Deposits)
$stmt_inc = $pdo->prepare("SELECT SUM(amount) FROM transactions WHERE user_id = ? AND type IN ('Credit', 'Deposit') AND status = 'Completed' AND created_at >= ?");
$stmt_inc->execute([$user_id, $start_of_month]);
$monthly_income = $stmt_inc->fetchColumn() ?: 0;

// Monthly Outgoing (Debits/Transfers/Wires)
$stmt_out = $pdo->prepare("SELECT SUM(amount) FROM transactions WHERE user_id = ? AND type NOT IN ('Credit', 'Deposit') AND status = 'Completed' AND created_at >= ?");
$stmt_out->execute([$user_id, $start_of_month]);
$monthly_outgoing = $stmt_out->fetchColumn() ?: 0;
?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <?php include '../includes/user-navbar.php'; ?>

        <!-- Page Content -->
        <div class="page-container">
            
            <!-- Top Stats Row -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card card-premium stat-card">
                        <div>
                            <div class="title">Current Balance</div>
                            <div class="value">$<?php echo number_format($_SESSION['balance'], 2); ?></div>
                        </div>
                        <div class="icon-box bg-blue-light">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-premium stat-card">
                        <div>
                            <div class="title">Monthly Income</div>
                            <div class="value text-success">$<?php echo number_format($monthly_income, 2); ?></div>
                        </div>
                        <div class="icon-box bg-green-light">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-premium stat-card">
                        <div>
                            <div class="title">Monthly Outgoing</div>
                            <div class="value text-danger">$<?php echo number_format($monthly_outgoing, 2); ?></div>
                        </div>
                        <div class="icon-box bg-red-light">
                            <i class="fa-solid fa-arrow-trend-down"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-premium stat-card">
                        <div>
                            <div class="title">Transaction Limit</div>
                            <div class="value text-primary">$500,000.00</div>
                        </div>
                        <div class="icon-box bg-purple-light">
                            <i class="fa-solid fa-gauge-high"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-lg-8">
                    
                    <!-- Main Balance Card -->
                    <div class="main-balance-card mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-circle text-primary bg-white" style="width: 45px; height: 45px; font-weight:800;"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></div>
                                <div>
                                    <div class="greeting">Verified Account</div>
                                    <div class="user-name-large mb-0"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
                                </div>
                            </div>
                            <div class="text-end text-white-50 text-sm">
                                <div id="currentTime"></div>
                                <div id="currentDate2"></div>
                            </div>
                        </div>

                        <div class="mt-4 mb-4 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="balance-label">Available Balance</div>
                                <div class="balance-amount">$<?php echo number_format($_SESSION['balance'], 2); ?> USD</div>
                            </div>
                            <div>
                                <i class="fa-solid fa-eye text-white-50" style="font-size: 1.2rem; cursor: pointer;"></i>
                            </div>
                        </div>

                        <div class="acc-details-box">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fa-solid fa-shield-halved" style="font-size: 1.5rem; opacity: 0.8;"></i>
                                <div>
                                    <div class="d-flex align-items-center">
                                        <span class="label">Your Account Number</span>
                                        <span class="status-badge" style="background: rgba(255,255,255,0.2)"><?php echo $_SESSION['role']; ?></span>
                                    </div>
                                    <div class="number"><?php echo $_SESSION['account_number']; ?></div>
                                </div>
                            </div>
                            <div class="action-buttons-overlay">
                                <a href="transactions.php" class="btn btn-glass text-decoration-none"><i class="fa-solid fa-chart-line me-2"></i> Ledger</a>
                                <a href="deposit.php" class="btn btn-glass text-decoration-none"><i class="fa-solid fa-download me-2"></i> Top up</a>
                            </div>
                        </div>
                    </div>

                    <!-- What would you like to do -->
                    <div class="mb-4">
                        <h5 class="font-weight-bold mb-1">What would you like to do today?</h5>
                        <p class="text-muted text-sm mb-3">Choose from our popular actions below</p>
                        
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="quick-action-card qa-gray" onclick="window.location.href='settings.php'">
                                    <div class="icon-wrapper"><i class="fa-solid fa-building"></i></div>
                                    <div class="action-title">Account Info</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="quick-action-card qa-blue">
                                    <div class="icon-wrapper"><i class="fa-solid fa-paper-plane"></i></div>
                                    <div class="action-title">Send Money</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="quick-action-card qa-green" onclick="window.location.href='deposit.php'">
                                    <div class="icon-wrapper"><i class="fa-solid fa-plus"></i></div>
                                    <div class="action-title">Deposit</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="quick-action-card qa-purple" onclick="window.location.href='transactions.php'">
                                    <div class="icon-wrapper"><i class="fa-solid fa-clock-rotate-left"></i></div>
                                    <div class="action-title">History</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Your Cards -->
                    <div class="card card-premium mb-4">
                        <div class="card-body p-4">
                            <div class="section-header">
                                <div class="section-title"><i class="fa-solid fa-credit-card"></i> Your Cards</div>
                                <a href="cards.php" class="view-all-link">View all <i class="fa-solid fa-chevron-right ms-1"></i></a>
                            </div>
                            <div class="empty-state">
                                <i class="fa-solid fa-credit-card icon"></i>
                                <h5>No cards yet</h5>
                                <p>You haven't applied for any virtual cards yet. Apply for a new card to get started with secure online payments.</p>
                                <a href="cards.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Apply for Card</a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="card card-premium">
                        <div class="card-body p-4">
                            <div class="section-header">
                                <div class="section-title"><i class="fa-solid fa-list-ul"></i> Recent Transactions</div>
                                <a href="transactions.php" class="view-all-link">View all <i class="fa-solid fa-chevron-right ms-1"></i></a>
                            </div>
                            <div class="empty-state">
                                <i class="fa-solid fa-inbox icon"></i>
                                <h5>No transactions yet</h5>
                                <p>Your transaction history will appear here</p>
                                <a href="deposit.php" class="btn btn-primary">Make your first deposit</a>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column -->
                <div class="col-lg-4">
                    
                    <!-- Account Statistics -->
                    <div class="widget-card">
                        <div class="widget-title">Account Statistics</div>
                        
                        <div class="stat-item">
                            <div class="icon bg-blue-light"><i class="fa-solid fa-credit-card"></i></div>
                            <div class="info">
                                <div class="label">Transaction Limit</div>
                                <div class="value">$500,000.00</div>
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="icon bg-warning text-white" style="background-color: #fcd34d !important;"><i class="fa-solid fa-clock"></i></div>
                            <div class="info">
                                <div class="label">Pending Transactions</div>
                                <div class="value">$0.00</div>
                            </div>
                        </div>

                        <div class="stat-item">
                            <div class="icon bg-green-light"><i class="fa-solid fa-chart-column"></i></div>
                            <div class="info">
                                <div class="label">Transaction Volume</div>
                                <div class="value">$0.00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Transfer -->
                    <div class="widget-card">
                        <div class="widget-title">Quick Transfer</div>
                        
                        <div class="transfer-item">
                            <div class="icon"><i class="fa-solid fa-user"></i></div>
                            <div class="details">
                                <div class="title">Local Transfer</div>
                                <div class="desc">0% Handling charges</div>
                            </div>
                            <i class="fa-solid fa-chevron-right chevron"></i>
                        </div>
                        
                        <div class="transfer-item">
                            <div class="icon"><i class="fa-solid fa-globe"></i></div>
                            <div class="details">
                                <div class="title">International Transfer</div>
                                <div class="desc">Capital reach, 0% fee</div>
                            </div>
                            <i class="fa-solid fa-chevron-right chevron"></i>
                        </div>
                    </div>

                    <!-- Need Help -->
                    <div class="widget-card help-widget">
                        <div class="icon"><i class="fa-solid fa-circle-question"></i></div>
                        <h4>Need Help?</h4>
                        <p>Our support team is here to assist you 24/7</p>
                        <button class="btn w-100" onclick="location.href='support.php'"><i class="fa-solid fa-comment-dots me-2"></i> Contact Support</button>
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

        <!-- Toast Container -->
        <div class="toast-container-custom" id="toastContainer"></div>
    </main>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set dynamic times
        document.addEventListener('DOMContentLoaded', function() {
            function updateTime() {
                const now = new Date();
                
                // Update Date
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const formattedDate = now.toLocaleDateString('en-US', options);
                
                document.querySelectorAll('#currentDate, #currentDate2').forEach(el => {
                    if(el) el.textContent = formattedDate;
                });

                // Update Time
                const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
                const formattedTime = now.toLocaleTimeString('en-US', timeOptions);
                
                const timeEl = document.getElementById('currentTime');
                if(timeEl) timeEl.textContent = formattedTime;
            }

            updateTime();
            setInterval(updateTime, 1000);

            // Show latest unread toast if exists
            <?php if($latest_unread): ?>
                setTimeout(() => {
                    showToast(
                        "<?php echo addslashes($latest_unread['title']); ?>", 
                        "<?php echo addslashes($latest_unread['message']); ?>", 
                        "<?php echo $latest_unread['type']; ?>"
                    );
                }, 1500);
            <?php endif; ?>
        });

        function showToast(title, msg, type = 'System') {
            const container = document.getElementById('toastContainer');
            if(!container) return;

            const toast = document.createElement('div');
            toast.className = 'premium-toast';
            
            let icon = 'fa-bell';
            let bg = 'bg-sky-soft';
            if(type == 'Transaction') { icon = 'fa-exchange-alt'; bg = 'bg-emerald-soft'; }
            else if(type == 'Loan') { icon = 'fa-hand-holding-usd'; bg = 'bg-rose-soft'; }
            else if(type == 'KYC') { icon = 'fa-user-shield'; bg = 'bg-amber-soft'; }
            
            toast.innerHTML = `
                <div class="toast-icon ${bg}">
                    <i class="fa-solid ${icon}"></i>
                </div>
                <div class="toast-body">
                    <div class="toast-title">${title}</div>
                    <div class="toast-msg">${msg}</div>
                </div>
                <i class="fa-solid fa-xmark toast-close-btn"></i>
                <div class="toast-progress-bar"></div>
            `;
            
            container.appendChild(toast);

            // Close button click
            toast.querySelector('.toast-close-btn').onclick = () => {
                toast.classList.add('closing');
                setTimeout(() => toast.remove(), 500);
            };
            
            // Auto remove after 6s
            setTimeout(() => {
                if(toast.parentElement) {
                    toast.classList.add('closing');
                    setTimeout(() => toast.remove(), 500);
                }
            }, 6000);
        }

        function markRead(id) {
            fetch(`mark-read.php?id=${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                // Update UI: hide the dot if it was the last unread, or decrease count
                console.log("Notification marked as read");
            })
            .catch(err => console.error(err));
        }
    </script>
</body>
</html>
