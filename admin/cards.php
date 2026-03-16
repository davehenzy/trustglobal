<?php
require_once '../includes/db.php';
require_once '../includes/admin-check.php';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $app_id  = (int)($_POST['app_id'] ?? 0);
    $action  = $_POST['action'] ?? '';

    if ($app_id) {
        if ($action === 'approve') {
            // Generate card details
            $card_number = implode(' ', str_split(sprintf('%016d', random_int(4000000000000000, 4999999999999999)), 4));
            $expiry = date('m/y', strtotime('+3 years'));
            $cvv    = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);
            $pdo->prepare("UPDATE card_applications SET status='Approved', card_number=?, expiry_date=?, cvv=? WHERE id=?")
                ->execute([$card_number, $expiry, $cvv, $app_id]);
            $flash = ['type'=>'success','msg'=>'Card application approved and card issued.'];
        } elseif ($action === 'reject') {
            $pdo->prepare("UPDATE card_applications SET status='Rejected' WHERE id=?")->execute([$app_id]);
            $flash = ['type'=>'danger','msg'=>'Card application rejected.'];
        }
        header('Location: cards.php');
        exit;
    }
}

// Stats
$pending_count  = $pdo->query("SELECT COUNT(*) FROM card_applications WHERE status='Pending'")->fetchColumn();
$approved_count = $pdo->query("SELECT COUNT(*) FROM card_applications WHERE status='Approved'")->fetchColumn();
$rejected_count = $pdo->query("SELECT COUNT(*) FROM card_applications WHERE status='Rejected'")->fetchColumn();
$total_count    = $pdo->query("SELECT COUNT(*) FROM card_applications")->fetchColumn();

// Filter
$filter = $_GET['status'] ?? 'all';
$where  = $filter !== 'all' ? "WHERE ca.status = '$filter'" : '';

// All applications with user info
$apps = $pdo->query("
    SELECT ca.*, u.name, u.lastname, u.email, u.account_number
    FROM card_applications ca
    JOIN users u ON ca.user_id = u.id
    $where
    ORDER BY ca.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Requests - SwiftCapital Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .card-visual-mini {
            width: 100%;
            border-radius: 14px;
            padding: 18px 20px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,.25);
            font-size: .8rem;
        }
        .card-visual-mini::before {
            content:'';
            position:absolute;top:-40px;right:-40px;width:120px;height:120px;
            background:radial-gradient(circle,rgba(255,255,255,.15) 0%,transparent 70%);
            border-radius:50%;
        }
        .tier-standard { background: linear-gradient(135deg,#3b82f6,#2563eb); }
        .tier-gold     { background: linear-gradient(135deg,#d97706,#92400e); }
        .tier-platinum { background: linear-gradient(135deg,#475569,#1e293b); }
        .tier-black    { background: linear-gradient(135deg,#1e293b,#000); }

        .filter-tab { border-radius: 50px; font-size: .8rem; font-weight: 700; padding: 7px 18px; border: 1.5px solid #e5e7eb; text-decoration: none; color: #64748b; transition: all .2s; }
        .filter-tab.active, .filter-tab:hover { background: var(--admin-primary); color: #fff; border-color: var(--admin-primary); }

        .modal-card-detail .modal-content { border-radius: 20px; border: none; box-shadow: 0 30px 80px rgba(0,0,0,.15); }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="admin-sidebar">
        <div class="brand-area">
            <div class="brand-icon"><i class="fa-solid fa-shield-halved"></i></div>
            <div class="brand-name">SwiftAdmin</div>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="users.php" class="nav-link"><i class="fa-solid fa-users"></i> Users Management</a>
            <a href="transactions.php" class="nav-link"><i class="fa-solid fa-money-bill-transfer"></i> Transactions</a>
            <a href="loans.php" class="nav-link"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Requests</a>
            <a href="cards.php" class="nav-link active"><i class="fa-solid fa-credit-card"></i> Card Requests
                <?php if($pending_count > 0): ?>
                <span class="badge bg-warning text-dark ms-auto" style="font-size:.6rem;"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="irs.php" class="nav-link"><i class="fa-solid fa-file-invoice-dollar"></i> IRS Refunds</a>
            <a href="kyc.php" class="nav-link"><i class="fa-solid fa-id-card-clip"></i> KYC Verifications</a>
            <a href="support.php" class="nav-link"><i class="fa-solid fa-headset"></i> Support Tickets</a>
            <?php if (in_array($_SESSION['role'] ?? '', ['Super Admin', 'Admin'])): ?>
            <a href="contacts.php" class="nav-link"><i class="fa-solid fa-envelope"></i> Contact Messages</a>
            <a href="cms.php" class="nav-link"><i class="fa-solid fa-pen-nib"></i> Frontend CMS</a>
            <a href="settings.php" class="nav-link"><i class="fa-solid fa-gear"></i> System Settings</a>
            <?php endif; ?>
            <div class="mt-auto" style="position: absolute; bottom: 20px; width: 100%;">
                <a href="../logout.php" class="nav-link text-danger"><i class="fa-solid fa-power-off"></i> Logout</a>
            </div>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="breadcrumb-area">
                <h4 class="mb-0 fw-800">Card Requests</h4>
                <p class="text-xs text-muted mb-0">Review and approve virtual card applications</p>
            </div>
            <div class="user-nav">
                <div class="notification-bell">
                    <i class="fa-solid fa-bell fs-5"></i>
                    <span class="notification-dot"></span>
                </div>
                <div class="admin-profile">
                    <div class="admin-avatar"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)); ?></div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold text-sm"><?php echo $_SESSION['user_name'] ?? 'Admin'; ?></div>
                        <div class="text-xs text-muted"><?php echo $_SESSION['role'] ?? 'Administrator'; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content-padding">

            <!-- Stats -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-indigo-light text-primary"><i class="fa-solid fa-credit-card"></i></div>
                        <div class="stat-info"><div class="stat-value"><?php echo $total_count; ?></div><div class="stat-label">Total Applications</div></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="<?php echo $pending_count > 0 ? 'border-left:4px solid #f59e0b;' : ''; ?>">
                        <div class="stat-icon bg-warning-light text-warning"><i class="fa-solid fa-hourglass-half"></i></div>
                        <div class="stat-info"><div class="stat-value"><?php echo $pending_count; ?></div><div class="stat-label">Pending Review</div></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-emerald-light text-success"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="stat-info"><div class="stat-value"><?php echo $approved_count; ?></div><div class="stat-label">Approved & Issued</div></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-rose-light text-danger"><i class="fa-solid fa-circle-xmark"></i></div>
                        <div class="stat-info"><div class="stat-value"><?php echo $rejected_count; ?></div><div class="stat-label">Rejected</div></div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs + Table -->
            <div class="data-table-card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="mb-0 fw-800">All Applications</h5>
                        <p class="text-xs text-muted mb-0"><?php echo count($apps); ?> records found</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="cards.php" class="filter-tab <?php echo $filter==='all'?'active':''; ?>">All</a>
                        <a href="cards.php?status=Pending" class="filter-tab <?php echo $filter==='Pending'?'active':''; ?>">Pending
                            <?php if($pending_count): ?><span class="badge bg-warning text-dark ms-1" style="font-size:.6rem;"><?php echo $pending_count; ?></span><?php endif; ?>
                        </a>
                        <a href="cards.php?status=Approved" class="filter-tab <?php echo $filter==='Approved'?'active':''; ?>">Approved</a>
                        <a href="cards.php?status=Rejected" class="filter-tab <?php echo $filter==='Rejected'?'active':''; ?>">Rejected</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Card Details</th>
                                <th>Preview</th>
                                <th>Limit / Currency</th>
                                <th>Applied</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($apps)): ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted"><i class="fa-solid fa-credit-card fa-2x d-block mb-2 opacity-25"></i>No applications found</td></tr>
                            <?php else: ?>
                            <?php foreach($apps as $app):
                                $initials = strtoupper(substr($app['name'],0,1).substr($app['lastname'],0,1));
                                $tier_cls = 'tier-' . $app['card_tier'];
                                $netIcons = ['visa'=>'fa-cc-visa','mastercard'=>'fa-cc-mastercard','amex'=>'fa-cc-amex'];
                                $ni = $netIcons[$app['card_type']] ?? 'fa-credit-card';
                                $card_num = $app['card_number'] ?? '•••• •••• •••• ????';
                                $status_cls = ['Pending'=>'bg-warning-light text-warning','Approved'=>'bg-emerald-light text-success','Rejected'=>'bg-rose-light text-danger','Cancelled'=>'bg-light text-muted'];
                                $sc = $status_cls[$app['status']] ?? 'bg-light text-muted';
                            ?>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="admin-avatar" style="width:36px;height:36px;font-size:.75rem;"><?php echo $initials; ?></div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($app['name'].' '.$app['lastname']); ?></div>
                                            <div class="text-xs text-muted"><?php echo htmlspecialchars($app['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-800 text-sm"><?php echo ucfirst($app['card_type']); ?> <span class="text-muted fw-600">·</span> <?php echo ucwords($app['card_tier']); ?></div>
                                    <div class="text-xs text-muted"><?php echo htmlspecialchars($app['cardholder_name']); ?></div>
                                </td>
                                <td>
                                    <div class="card-visual-mini <?php echo $tier_cls; ?>" style="width:180px;">
                                        <div style="font-size:.6rem;font-weight:800;letter-spacing:.5px;opacity:.9;margin-bottom:8px;">Swift Capital</div>
                                        <div style="font-size:.75rem;letter-spacing:2px;font-weight:700;margin-bottom:8px;"><?php echo htmlspecialchars($card_num); ?></div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span style="font-size:.6rem;opacity:.8;"><?php echo htmlspecialchars(strtoupper($app['cardholder_name'])); ?></span>
                                            <i class="fa-brands <?php echo $ni; ?>" style="font-size:1.2rem;"></i>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-800 text-sm">$<?php echo number_format($app['daily_limit'],0); ?>/day</div>
                                    <div class="text-xs text-muted"><?php echo $app['currency']; ?></div>
                                </td>
                                <td class="text-xs text-muted fw-600"><?php echo date('M d, Y',strtotime($app['created_at'])); ?><br><?php echo date('g:i A',strtotime($app['created_at'])); ?></td>
                                <td><span class="badge fw-800 px-3 py-2 <?php echo $sc; ?>" style="border-radius:50px;font-size:.7rem;"><?php echo $app['status']; ?></span></td>
                                <td>
                                    <button class="action-btn text-primary me-1" title="View Details"
                                        data-bs-toggle="modal" data-bs-target="#detailModal"
                                        data-id="<?php echo $app['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($app['name'].' '.$app['lastname']); ?>"
                                        data-email="<?php echo htmlspecialchars($app['email']); ?>"
                                        data-account="<?php echo htmlspecialchars($app['account_number']); ?>"
                                        data-cardholder="<?php echo htmlspecialchars($app['cardholder_name']); ?>"
                                        data-billing="<?php echo htmlspecialchars($app['billing_address']); ?>"
                                        data-type="<?php echo $app['card_type']; ?>"
                                        data-tier="<?php echo $app['card_tier']; ?>"
                                        data-currency="<?php echo $app['currency']; ?>"
                                        data-limit="<?php echo number_format($app['daily_limit'],0); ?>"
                                        data-status="<?php echo $app['status']; ?>"
                                        data-cardnum="<?php echo htmlspecialchars($card_num); ?>"
                                        data-expiry="<?php echo $app['expiry_date'] ?? '—'; ?>"
                                        data-created="<?php echo date('M d, Y g:i A',strtotime($app['created_at'])); ?>">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <?php if($app['status'] === 'Pending'): ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Approve this card application?')">
                                        <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="action-btn text-success" title="Approve"><i class="fa-solid fa-circle-check"></i></button>
                                    </form>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Reject this card application?')">
                                        <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="action-btn text-danger" title="Reject"><i class="fa-solid fa-circle-xmark"></i></button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <footer class="mt-auto py-4 px-4 border-top bg-white text-center text-muted" style="font-size:.85rem;">
            SwiftCapital Admin &copy; 2026. Internal System Only.
        </footer>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade modal-card-detail" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-1">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-800">Card Application Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <div class="row g-4">
                        <!-- Card Visual -->
                        <div class="col-md-5 d-flex flex-column align-items-center justify-content-center">
                            <div id="modalCardVisual" class="card-visual-mini tier-standard w-100 mb-3" style="padding:24px;">
                                <div style="font-size:.7rem;font-weight:800;letter-spacing:.5px;opacity:.9;margin-bottom:14px;">Swift Capital</div>
                                <div id="modalCardNum" style="font-size:1rem;letter-spacing:3px;font-weight:700;margin-bottom:18px;">•••• •••• •••• ????</div>
                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <div style="font-size:.5rem;text-transform:uppercase;opacity:.6;letter-spacing:1px;margin-bottom:4px;">Card Holder</div>
                                        <div id="modalCardHolder" style="font-size:.8rem;font-weight:700;"></div>
                                    </div>
                                    <div class="text-end">
                                        <div id="modalExpiry" style="font-size:.8rem;font-weight:600;opacity:.9;"></div>
                                        <i id="modalBrand" class="fa-brands fa-cc-visa" style="font-size:1.8rem;"></i>
                                    </div>
                                </div>
                            </div>
                            <div id="modalStatusBadge" class="badge px-4 py-2 fw-800" style="border-radius:50px;font-size:.85rem;"></div>
                        </div>
                        <!-- Info -->
                        <div class="col-md-7">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-xs text-muted fw-700 text-uppercase mb-1">Applicant</div>
                                    <div id="modalName" class="fw-800"></div>
                                </div>
                                <div class="col-6">
                                    <div class="text-xs text-muted fw-700 text-uppercase mb-1">Account No.</div>
                                    <div id="modalAccount" class="fw-800"></div>
                                </div>
                                <div class="col-12">
                                    <div class="text-xs text-muted fw-700 text-uppercase mb-1">Email</div>
                                    <div id="modalEmail" class="fw-700"></div>
                                </div>
                                <div class="col-6">
                                    <div class="text-xs text-muted fw-700 text-uppercase mb-1">Network</div>
                                    <div id="modalType" class="fw-800 text-capitalize"></div>
                                </div>
                                <div class="col-6">
                                    <div class="text-xs text-muted fw-700 text-uppercase mb-1">Tier</div>
                                    <div id="modalTier" class="fw-800 text-capitalize"></div>
                                </div>
                                <div class="col-6">
                                    <div class="text-xs text-muted fw-700 text-uppercase mb-1">Daily Limit</div>
                                    <div id="modalLimit" class="fw-800"></div>
                                </div>
                                <div class="col-6">
                                    <div class="text-xs text-muted fw-700 text-uppercase mb-1">Currency</div>
                                    <div id="modalCurrency" class="fw-800"></div>
                                </div>
                                <div class="col-12">
                                    <div class="text-xs text-muted fw-700 text-uppercase mb-1">Billing Address</div>
                                    <div id="modalBilling" class="fw-600 text-muted" style="font-size:.85rem;"></div>
                                </div>
                                <div class="col-12">
                                    <div class="text-xs text-muted fw-700 text-uppercase mb-1">Applied On</div>
                                    <div id="modalCreated" class="fw-600 text-muted" style="font-size:.85rem;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Action buttons for Pending -->
                    <div id="modalActions" class="d-flex gap-3 mt-4 pt-3 border-top" style="display:none!important;">
                        <form method="POST" onsubmit="return confirm('Approve?')">
                            <input type="hidden" name="app_id" id="modalAppId">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-success fw-800 px-4" style="border-radius:12px;"><i class="fa-solid fa-circle-check me-2"></i>Approve & Issue Card</button>
                        </form>
                        <form method="POST" onsubmit="return confirm('Reject?')">
                            <input type="hidden" name="app_id" id="modalAppId2">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-danger fw-800 px-4" style="border-radius:12px;"><i class="fa-solid fa-circle-xmark me-2"></i>Reject Application</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const brandMap = { visa: 'fa-brands fa-cc-visa', mastercard: 'fa-brands fa-cc-mastercard', amex: 'fa-brands fa-cc-amex' };
        const tierGrad = { standard: 'tier-standard', gold: 'tier-gold', platinum: 'tier-platinum', black: 'tier-black' };
        const statusBadge = { Pending: 'bg-warning-light text-warning', Approved: 'bg-emerald-light text-success', Rejected: 'bg-rose-light text-danger' };

        document.getElementById('detailModal').addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            const d = btn.dataset;

            document.getElementById('modalCardVisual').className = 'card-visual-mini w-100 mb-3 ' + (tierGrad[d.tier] || 'tier-standard');
            document.getElementById('modalCardNum').textContent    = d.cardnum;
            document.getElementById('modalCardHolder').textContent = d.cardholder.toUpperCase();
            document.getElementById('modalExpiry').textContent     = d.expiry !== '—' ? 'Exp: ' + d.expiry : '';
            document.getElementById('modalBrand').className        = brandMap[d.type] || 'fa-brands fa-cc-visa';
            document.getElementById('modalBrand').style.fontSize   = '1.8rem';

            const sb = document.getElementById('modalStatusBadge');
            sb.className = 'badge px-4 py-2 fw-800 ' + (statusBadge[d.status] || 'bg-light text-muted');
            sb.textContent = d.status;
            sb.style.borderRadius = '50px'; sb.style.fontSize = '.85rem';

            document.getElementById('modalName').textContent     = d.name;
            document.getElementById('modalAccount').textContent  = d.account;
            document.getElementById('modalEmail').textContent    = d.email;
            document.getElementById('modalType').textContent     = d.type;
            document.getElementById('modalTier').textContent     = d.tier;
            document.getElementById('modalLimit').textContent    = '$' + d.limit + '/day';
            document.getElementById('modalCurrency').textContent = d.currency;
            document.getElementById('modalBilling').textContent  = d.billing;
            document.getElementById('modalCreated').textContent  = d.created;

            const actions = document.getElementById('modalActions');
            document.getElementById('modalAppId').value  = d.id;
            document.getElementById('modalAppId2').value = d.id;
            actions.style.display = d.status === 'Pending' ? 'flex' : 'none';
        });
    </script>
</body>
</html>
