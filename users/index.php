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
    <style>
        .ledger-hash { font-family: 'Courier New', Courier, monospace; letter-spacing: -0.5px; opacity: 0.6; }
        .ledger-narration { font-size: 0.7rem; color: #64748b; line-height: 1.2; }
    </style>
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

// Fetch User Cards (Institutional Highlight)
$stmt_cards = $pdo->prepare("SELECT * FROM card_applications WHERE user_id = ? AND status = 'Approved' ORDER BY created_at DESC LIMIT 2");
$stmt_cards->execute([$user_id]);
$dashboard_cards = $stmt_cards->fetchAll();

// Fetch Recent Transactions
$stmt_last_tx = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt_last_tx->execute([$user_id]);
$recent_transactions = $stmt_last_tx->fetchAll() ?: [];

// Fetch Pending Settlements Count/Volume
$stmt_pending = $pdo->prepare("SELECT COUNT(*) AS cnt, SUM(amount) AS vol FROM transactions WHERE user_id = ? AND status = 'Pending'");
$stmt_pending->execute([$user_id]);
$pending_data = $stmt_pending->fetch() ?: ['cnt' => 0, 'vol' => 0];
$pending_count = $pending_data['cnt'] ?: 0;
$pending_volume = $pending_data['vol'] ?: 0;

// Fetch Total Transaction Volume (All time)
$stmt_vol = $pdo->prepare("SELECT SUM(amount) AS total_vol FROM transactions WHERE user_id = ? AND status = 'Completed'");
$stmt_vol->execute([$user_id]);
$total_volume = $stmt_vol->fetchColumn() ?: 0;
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
                                <div class="avatar-circle text-primary bg-white" style="width: 45px; height: 45px; font-weight:800; overflow: hidden;">
                                    <?php if(!empty($_SESSION['profile_pic'])): ?>
                                        <img src="../assets/uploads/profiles/<?php echo $_SESSION['profile_pic']; ?>" alt="Profile" style="width:100%; height:100%; object-fit:cover;">
                                    <?php else: ?>
                                        <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                                    <?php endif; ?>
                                </div>
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

                    <!-- Institutional Portfolio Actions -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-1">Portfolio Operations</h5>
                        <p class="text-muted text-sm mb-3">Initialize strategic capital movements or account governance</p>
                        
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="quick-action-card qa-gray" onclick="window.location.href='settings.php'">
                                    <div class="icon-wrapper"><i class="fa-solid fa-file-contract"></i></div>
                                    <div class="action-title">Governance</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="quick-action-card qa-blue" onclick="window.location.href='local.php'">
                                    <div class="icon-wrapper"><i class="fa-solid fa-vault"></i></div>
                                    <div class="action-title">Settlements</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="quick-action-card qa-green" onclick="window.location.href='deposit.php'">
                                    <div class="icon-wrapper"><i class="fa-solid fa-plus"></i></div>
                                    <div class="action-title">Capital Inflow</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="quick-action-card qa-purple" onclick="window.location.href='transactions.php'">
                                    <div class="icon-wrapper"><i class="fa-solid fa-clock-rotate-left"></i></div>
                                    <div class="action-title">Ledger</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Your Cards -->
                    <div class="card card-premium mb-4">
                        <div class="card-body p-4">
                            <div class="section-header">
                                <div class="section-title"><i class="fa-solid fa-credit-card"></i> Governance Cards</div>
                                <a href="cards.php" class="view-all-link">Manage Vault <i class="fa-solid fa-chevron-right ms-1"></i></a>
                            </div>
                            
                            <?php if (empty($dashboard_cards)): ?>
                            <div class="empty-state">
                                <i class="fa-solid fa-credit-card icon"></i>
                                <h5>No active cards</h5>
                                <p>Authorize a virtual card to begin secure global settlements and private procurement.</p>
                                <a href="cards.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Apply for Card</a>
                            </div>
                            <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($dashboard_cards as $card): 
                                    $gradients = ['standard'=>'135deg,#375987,#002d62', 'gold'=>'135deg,#d97706,#b45309', 'platinum'=>'135deg,#475569,#1e293b', 'black'=>'135deg,#1e293b,#000'];
                                    $grad = $gradients[$card['card_tier']] ?? $gradients['standard'];
                                    $netIcons = ['visa'=>'fa-brands fa-cc-visa', 'mastercard'=>'fa-brands fa-cc-mastercard', 'amex'=>'fa-brands fa-cc-amex'];
                                    $netIcon = $netIcons[$card['card_type']] ?? 'fa-solid fa-credit-card';
                                ?>
                                <div class="col-md-6">
                                    <div class="p-3 shadow-lg" style="background: linear-gradient(<?php echo $grad; ?>); border-radius: 16px; color: #fff; position: relative; overflow: hidden; height: 165px; border: 1px solid rgba(255,255,255,0.1);">
                                        <div style="position:absolute; top:-20%; right:-10%; width:150px; height:150px; background:radial-gradient(circle,rgba(255,255,255,0.1) 0%,transparent 75%); border-radius:50%;"></div>
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <span class="fw-900 x-small tracking-widest opacity-75">SWIFT CAPITAL</span>
                                            <i class="<?php echo $netIcon; ?> fs-4"></i>
                                        </div>
                                        <div class="mb-3 fs-5 tracking-widest fw-bold opacity-90" style="letter-spacing: 2px;">
                                            •••• •••• •••• <?php echo substr($card['card_number'], -4); ?>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-end mt-2">
                                            <div>
                                                <div class="x-small text-uppercase opacity-50" style="font-size: 0.6rem;">Fiduciary Holder</div>
                                                <div class="small fw-bold"><?php echo strtoupper($card['cardholder_name']); ?></div>
                                            </div>
                                            <div class="text-end">
                                                <div class="x-small text-uppercase opacity-50" style="font-size: 0.6rem;">Limit Status</div>
                                                <div class="px-2 py-0 bg-white text-dark rounded-pill fw-bold" style="font-size: 0.6rem;"><?php echo $card['card_tier']; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="card card-premium">
                        <div class="card-body p-4">
                            <div class="section-header">
                                <div class="section-title"><i class="fa-solid fa-list-ul"></i> Transaction Ledger</div>
                                <a href="transactions.php" class="view-all-link">View full history <i class="fa-solid fa-chevron-right ms-1"></i></a>
                            </div>

                            <?php if (empty($recent_transactions)): ?>
                            <div class="empty-state">
                                <i class="fa-solid fa-inbox icon"></i>
                                <h5>No settlements recorded</h5>
                                <p>Your historical asset movements will appear in this ledger once initialized.</p>
                                <a href="deposit.php" class="btn btn-primary">Initialize first top-up</a>
                            </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <tbody>
                                        <?php foreach ($recent_transactions as $tx): 
                                            $is_credit = in_array($tx['type'], ['Credit', 'Deposit']);
                                            $type_icon = $is_credit ? 'fa-arrow-down-left' : 'fa-arrow-up-right';
                                            $type_color = $is_credit ? 'text-success' : 'text-danger';
                                            $type_bg = $is_credit ? 'bg-success-light' : 'bg-danger-light';
                                            $disp_remark = !empty($tx['narration']) ? $tx['narration'] : (!empty($tx['remark']) ? $tx['remark'] : 'Institutional Settlement');
                                        ?>
                                        <tr>
                                            <td style="width: 45px;">
                                                <div class="avatar-circle <?php echo $type_color; ?> <?php echo $type_bg; ?>" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                                    <i class="fa-solid <?php echo $type_icon; ?>"></i>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold small mb-0"><?php echo htmlspecialchars($disp_remark); ?></div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="ledger-hash x-small">#<?php echo substr($tx['txn_hash'] ?? '0x'.bin2hex(random_bytes(4)), 0, 12); ?></span>
                                                    <span class="text-muted x-small">· <?php echo date('M d', strtotime($tx['created_at'])); ?></span>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="fw-900 <?php echo $type_color; ?>"><?php echo $is_credit ? '+' : '-'; ?>$<?php echo number_format($tx['amount'], 2); ?></div>
                                                <span class="badge rounded-pill <?php echo $tx['status'] == 'Completed' ? 'bg-success' : 'bg-warning'; ?> x-small" style="font-size: 0.5rem; letter-spacing: 0.5px;"><?php echo strtoupper($tx['status']); ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <!-- Right Column -->
                <div class="col-lg-4">
                    
                    <!-- Account Statistics -->
                    <div class="widget-card">
                        <div class="widget-title">Governance Metrics</div>
                        
                        <div class="stat-item">
                            <div class="icon bg-blue-light"><i class="fa-solid fa-shield-halved"></i></div>
                            <div class="info">
                                <div class="label">Sovereign Limit</div>
                                <div class="value">$500,000.00</div>
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="icon bg-warning-light text-warning" style="background: rgba(245, 158, 11, 0.1) !important;"><i class="fa-solid fa-clock-rotate-left"></i></div>
                            <div class="info">
                                <div class="label">Pending Settlements</div>
                                <div class="value"><?php echo $pending_count; ?> <span class="text-xs text-muted fw-normal">($<?php echo number_format($pending_volume, 2); ?>)</span></div>
                            </div>
                        </div>

                        <div class="stat-item">
                            <div class="icon bg-green-light"><i class="fa-solid fa-chart-line"></i></div>
                            <div class="info">
                                <div class="label">Aggregated Volume</div>
                                <div class="value">$<?php echo number_format($total_volume, 2); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Global Asset Settlements -->
                    <div class="widget-card">
                        <div class="widget-title">Asset Settlements</div>
                        
                        <div class="transfer-item" onclick="location.href='local.php'">
                            <div class="icon text-primary"><i class="fa-solid fa-building-columns"></i></div>
                            <div class="details">
                                <div class="title small fw-bold">Intra-Network Settlement</div>
                                <div class="desc x-small">Zero-latency capital movement</div>
                            </div>
                            <i class="fa-solid fa-chevron-right chevron fs-xs"></i>
                        </div>
                        
                        <div class="transfer-item" onclick="location.href='wire.php'">
                            <div class="icon text-danger"><i class="fa-solid fa-globe"></i></div>
                            <div class="details">
                                <div class="title small fw-bold">Cross-Border Wire</div>
                                <div class="desc x-small">SWIFT / SEPA / FedWire</div>
                            </div>
                            <i class="fa-solid fa-chevron-right chevron fs-xs"></i>
                        </div>
                    </div>

                    <!-- Institutional Market Insights -->
                    <div class="widget-card">
                        <div class="widget-title">Institutional Insights</div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold">S&P 500</span>
                                <span class="text-success small">+0.42% <i class="fa-solid fa-arrow-up"></i></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold">XAU/USD (Gold)</span>
                                <span class="text-danger small">-0.15% <i class="fa-solid fa-arrow-down"></i></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small fw-bold">BTC/USD</span>
                                <span class="text-success small">+2.18% <i class="fa-solid fa-arrow-up"></i></span>
                            </div>
                        </div>
                        <hr class="opacity-10 my-2">
                        <div class="small text-muted p-2 bg-light rounded">
                            <i class="fa-solid fa-circle-info me-1"></i> Market yields are currently showing stabilization in secondary bond markets. Contact your advisor for tactical shifts.
                        </div>
                    </div>

                    <!-- Strategic Advisory -->
                    <div class="widget-card help-widget" style="background: var(--primary-color) !important;">
                        <div class="icon"><i class="fa-solid fa-user-tie"></i></div>
                        <h4 class="text-white">Strategic Advisory</h4>
                        <p class="text-white opacity-75">Your dedicated board of advisors is on standby for capital settlement and strategic inquiries.</p>
                        <button class="btn btn-light w-100" onclick="location.href='support.php'"><i class="fa-solid fa-comment-dots me-2"></i> Contact Advisor</button>
                    </div>

                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="main-footer border-top bg-white">
            <div class="container-fluid py-4">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="brand">
                            <span class="fw-bold" style="color: var(--norby-blue);">Swift</span><span class="fw-bold" style="color: var(--brand-red);">Capital</span> 
                            <span class="text-muted small ms-2">© 2026. Global HQ: Zurich.</span>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <p class="x-small text-muted mb-0 font-italic text-uppercase ls-1">Approved for Institutional & Private Clients Only</p>
                    </div>
                    <div class="col-md-4 text-end footer-links">
                        <li class="list-inline-item"><a href="#" class="small text-muted text-decoration-none">Privacy Policy</a></li>
                        <li class="list-inline-item ms-3"><a href="#" class="small text-muted text-decoration-none">Governance Charter</a></li>
                        <li class="list-inline-item ms-3"><a href="#" class="small text-muted text-decoration-none">Contact Support</a></li>
                    </div>
                </div>
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
