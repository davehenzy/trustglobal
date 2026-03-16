<?php
require_once '../includes/db.php';
require_once '../includes/admin-check.php';

// ── Handle approve / reject ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tx_id  = (int)($_POST['tx_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($tx_id && in_array($action, ['approve', 'reject'])) {
        try {
            $pdo->beginTransaction();

            $tx = $pdo->prepare("SELECT * FROM transactions WHERE id = ? AND method = 'International Wire'");
            $tx->execute([$tx_id]);
            $tx = $tx->fetch();

            if (!$tx) throw new Exception("Wire transfer not found.");

            if ($action === 'approve' && $tx['status'] === 'Pending') {
                // Check user still has enough balance
                $bal = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
                $bal->execute([$tx['user_id']]);
                $bal = $bal->fetchColumn();
                if ($bal < $tx['amount']) throw new Exception("Insufficient user balance.");

                $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?")->execute([$tx['amount'], $tx['user_id']]);
                $pdo->prepare("UPDATE transactions SET status = 'Completed' WHERE id = ?")->execute([$tx_id]);
                $flash = ['type' => 'success', 'msg' => 'Wire transfer approved and funds deducted.'];

            } elseif ($action === 'reject' && $tx['status'] === 'Pending') {
                $pdo->prepare("UPDATE transactions SET status = 'Cancelled' WHERE id = ?")->execute([$tx_id]);
                $flash = ['type' => 'danger', 'msg' => 'Wire transfer rejected. No funds were deducted.'];
            }

            $pdo->commit();
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $flash = ['type' => 'warning', 'msg' => 'Error: ' . $e->getMessage()];
        }

        header('Location: wire-transfers.php');
        exit;
    }
}

// ── Stats ────────────────────────────────────────────────────
$pending_count   = $pdo->query("SELECT COUNT(*) FROM transactions WHERE method='International Wire' AND status='Pending'")->fetchColumn();
$approved_count  = $pdo->query("SELECT COUNT(*) FROM transactions WHERE method='International Wire' AND status='Completed'")->fetchColumn();
$rejected_count  = $pdo->query("SELECT COUNT(*) FROM transactions WHERE method='International Wire' AND status='Cancelled'")->fetchColumn();
$pending_volume  = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE method='International Wire' AND status='Pending'")->fetchColumn();
$total_volume    = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE method='International Wire' AND status='Completed'")->fetchColumn();

// ── Filter ───────────────────────────────────────────────────
$filter = $_GET['status'] ?? 'all';
$where  = '';
if ($filter === 'Pending')   $where = "WHERE t.method='International Wire' AND t.status='Pending'";
elseif ($filter === 'Completed') $where = "WHERE t.method='International Wire' AND t.status='Completed'";
elseif ($filter === 'Cancelled') $where = "WHERE t.method='International Wire' AND t.status='Cancelled'";
else $where = "WHERE t.method='International Wire'";

$wires = $pdo->query("
    SELECT t.*, u.name, u.lastname, u.email, u.account_number
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    $where
    ORDER BY t.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wire Transfers - SwiftCapital Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .filter-tab { border-radius: 50px; font-size: .8rem; font-weight: 700; padding: 7px 18px; border: 1.5px solid #e5e7eb; text-decoration: none; color: #64748b; transition: all .2s; }
        .filter-tab.active, .filter-tab:hover { background: var(--admin-primary); color: #fff; border-color: var(--admin-primary); }
        .narration-cell { font-size: .8rem; color: #475569; max-width: 280px; }
        .narration-line { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .detail-pill { display: inline-flex; align-items: center; gap: 5px; background: #f1f5f9; border-radius: 8px; padding: 3px 10px; font-size: .75rem; font-weight: 700; color: #475569; margin: 2px; }
        .wire-modal .modal-content { border-radius: 22px; border: none; box-shadow: 0 30px 80px rgba(0,0,0,.15); }
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
            <a href="wire-transfers.php" class="nav-link active"><i class="fa-solid fa-earth-americas"></i> Wire Transfers
                <?php if ($pending_count > 0): ?>
                <span class="badge bg-warning text-dark ms-auto" style="font-size:.6rem;"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="loans.php" class="nav-link"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Requests</a>
            <a href="cards.php" class="nav-link"><i class="fa-solid fa-credit-card"></i> Card Requests</a>
            <a href="irs.php" class="nav-link"><i class="fa-solid fa-file-invoice-dollar"></i> IRS Refunds</a>
            <a href="kyc.php" class="nav-link"><i class="fa-solid fa-id-card-clip"></i> KYC Verifications</a>
            <a href="support.php" class="nav-link"><i class="fa-solid fa-headset"></i> Support Tickets</a>
            <a href="contacts.php" class="nav-link"><i class="fa-solid fa-envelope"></i> Contact Messages</a>
            <a href="cms.php" class="nav-link"><i class="fa-solid fa-pen-nib"></i> Frontend CMS</a>
            <a href="settings.php" class="nav-link"><i class="fa-solid fa-gear"></i> System Settings</a>
            <div style="position:absolute;bottom:20px;width:100%;">
                <a href="../logout.php" class="nav-link text-danger"><i class="fa-solid fa-power-off"></i> Logout</a>
            </div>
        </div>
    </div>

    <!-- Main -->
    <div class="main-wrapper">
        <div class="top-bar">
            <div class="breadcrumb-area">
                <h4 class="mb-0 fw-800">International Wire Transfers</h4>
                <p class="text-xs text-muted mb-0">Review and approve SWIFT wire transfer requests</p>
            </div>
            <div class="user-nav">
                <div class="notification-bell"><i class="fa-solid fa-bell fs-5"></i><span class="notification-dot"></span></div>
                <div class="admin-profile">
                    <div class="admin-avatar"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)); ?></div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold text-sm"><?php echo $_SESSION['user_name'] ?? 'Admin'; ?></div>
                        <div class="text-xs text-muted"><?php echo $_SESSION['role'] ?? 'Administrator'; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-padding">

            <?php if (isset($flash)): ?>
            <div class="alert alert-<?php echo $flash['type']; ?> fw-700 border-0 rounded-3 mb-4" style="font-size:.9rem;">
                <i class="fa-solid <?php echo $flash['type']==='success'?'fa-circle-check':'fa-triangle-exclamation'; ?> me-2"></i>
                <?php echo $flash['msg']; ?>
            </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card" style="<?php echo $pending_count > 0 ? 'border-left:4px solid #f59e0b;' : ''; ?>">
                        <div class="stat-icon bg-warning-light text-warning"><i class="fa-solid fa-hourglass-half"></i></div>
                        <div class="stat-info"><div class="stat-value"><?php echo $pending_count; ?></div><div class="stat-label">Pending Review</div></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-emerald-light text-success"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="stat-info"><div class="stat-value"><?php echo $approved_count; ?></div><div class="stat-label">Approved</div></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-rose-light text-danger"><i class="fa-solid fa-circle-xmark"></i></div>
                        <div class="stat-info"><div class="stat-value"><?php echo $rejected_count; ?></div><div class="stat-label">Rejected</div></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-indigo-light text-primary"><i class="fa-solid fa-earth-americas"></i></div>
                        <div class="stat-info"><div class="stat-value">$<?php echo number_format($total_volume, 0); ?></div><div class="stat-label">Total Approved Volume</div></div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="data-table-card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="mb-0 fw-800">Wire Transfer Requests</h5>
                        <p class="text-xs text-muted mb-0"><?php echo count($wires); ?> records · pending volume $<?php echo number_format($pending_volume, 2); ?></p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="wire-transfers.php" class="filter-tab <?php echo $filter==='all'?'active':''; ?>">All</a>
                        <a href="wire-transfers.php?status=Pending" class="filter-tab <?php echo $filter==='Pending'?'active':''; ?>">
                            Pending <?php if ($pending_count): ?><span class="badge bg-warning text-dark ms-1" style="font-size:.6rem;"><?php echo $pending_count; ?></span><?php endif; ?>
                        </a>
                        <a href="wire-transfers.php?status=Completed" class="filter-tab <?php echo $filter==='Completed'?'active':''; ?>">Approved</a>
                        <a href="wire-transfers.php?status=Cancelled" class="filter-tab <?php echo $filter==='Cancelled'?'active':''; ?>">Rejected</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Beneficiary / Routing</th>
                                <th>Reference</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($wires)): ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted"><i class="fa-solid fa-earth-americas fa-2x d-block mb-2 opacity-25"></i>No wire transfers found</td></tr>
                            <?php else: ?>
                            <?php foreach ($wires as $w):
                                $initials = strtoupper(substr($w['name'],0,1).substr($w['lastname'],0,1));
                                $sc = ['Pending'=>'status-pending','Completed'=>'status-active','Cancelled'=>'status-blocked'];
                                $badge = $sc[$w['status']] ?? 'status-pending';

                                // Parse narration for quick preview
                                preg_match('/International Wire to ([^|]+)/', $w['narration'], $bname);
                                preg_match('/SWIFT: ([A-Z0-9]+)/', $w['narration'], $bswift);
                                preg_match('/\| ([^|]+), ([A-Z][^|]+) \|/', $w['narration'], $bcountry);
                            ?>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="admin-avatar" style="width:36px;height:36px;font-size:.75rem;background:linear-gradient(135deg,#1d4ed8,#3b82f6);"><?php echo $initials; ?></div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($w['name'].' '.$w['lastname']); ?></div>
                                            <div class="text-xs text-muted"><?php echo htmlspecialchars($w['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="fw-900 text-danger" style="font-size:1.05rem;">$<?php echo number_format($w['amount'], 2); ?></span></td>
                                <td>
                                    <div class="narration-cell">
                                        <?php if (!empty($bname[1])): ?>
                                        <div class="fw-800 text-dark mb-1" style="font-size:.85rem;"><?php echo htmlspecialchars(trim($bname[1])); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($bswift[1])): ?>
                                        <span class="detail-pill"><i class="fa-solid fa-code" style="font-size:.65rem;"></i> <?php echo $bswift[1]; ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($bcountry[2])): ?>
                                        <span class="detail-pill"><i class="fa-solid fa-location-dot" style="font-size:.65rem;"></i> <?php echo trim($bcountry[2]); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><code style="font-size:.78rem;color:#6366f1;"><?php echo htmlspecialchars($w['txn_hash']); ?></code></td>
                                <td class="text-xs text-muted fw-600"><?php echo date('M d, Y', strtotime($w['created_at'])); ?><br><?php echo date('g:i A', strtotime($w['created_at'])); ?></td>
                                <td><span class="status-badge <?php echo $badge; ?>"><?php echo $w['status'] === 'Cancelled' ? 'Rejected' : $w['status']; ?></span></td>
                                <td>
                                    <button class="action-btn text-primary me-1" title="View Details"
                                        data-bs-toggle="modal" data-bs-target="#wireModal"
                                        data-id="<?php echo $w['id']; ?>"
                                        data-user="<?php echo htmlspecialchars($w['name'].' '.$w['lastname']); ?>"
                                        data-email="<?php echo htmlspecialchars($w['email']); ?>"
                                        data-account="<?php echo htmlspecialchars($w['account_number']); ?>"
                                        data-amount="<?php echo number_format($w['amount'], 2); ?>"
                                        data-ref="<?php echo htmlspecialchars($w['txn_hash']); ?>"
                                        data-narration="<?php echo htmlspecialchars($w['narration']); ?>"
                                        data-status="<?php echo htmlspecialchars($w['status']); ?>"
                                        data-date="<?php echo date('M d, Y g:i A', strtotime($w['created_at'])); ?>">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <?php if ($w['status'] === 'Pending'): ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Approve this wire transfer?\n\n$<?php echo number_format($w['amount'],2); ?> will be deducted from user balance.')">
                                        <input type="hidden" name="tx_id" value="<?php echo $w['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="action-btn text-success" title="Approve"><i class="fa-solid fa-circle-check"></i></button>
                                    </form>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Reject and cancel this wire transfer?')">
                                        <input type="hidden" name="tx_id" value="<?php echo $w['id']; ?>">
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
    <div class="modal fade wire-modal" id="wireModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-2">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-800">Wire Transfer Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">

                    <!-- Amount hero -->
                    <div class="text-center p-4 rounded-3 mb-4" style="background:linear-gradient(135deg,#0a0a2e,#1d4ed8);color:#fff;">
                        <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;opacity:.7;margin-bottom:8px;">Wire Amount</div>
                        <div id="mdAmt" style="font-size:2.5rem;font-weight:900;letter-spacing:-1px;"></div>
                        <div id="mdRef" style="font-family:monospace;font-size:.8rem;opacity:.7;margin-top:6px;"></div>
                        <div id="mdStatusBadge" class="mt-3 d-inline-block px-4 py-1 rounded-pill fw-800" style="font-size:.8rem;"></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e5e7eb;">
                                <div class="text-xs text-muted fw-700 text-uppercase mb-2">Sender</div>
                                <div id="mdUser" class="fw-800 mb-1"></div>
                                <div id="mdEmail" class="text-muted fw-600" style="font-size:.85rem;"></div>
                                <div id="mdAccount" class="text-muted fw-600" style="font-size:.8rem;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e5e7eb;">
                                <div class="text-xs text-muted fw-700 text-uppercase mb-2">Submitted</div>
                                <div id="mdDate" class="fw-800 mb-1"></div>
                                <div class="text-muted fw-600" style="font-size:.85rem;">Via SWIFT Network</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 rounded-3" style="background:#f8fafc;border:1px solid #e5e7eb;">
                                <div class="text-xs text-muted fw-700 text-uppercase mb-2">Transfer Details</div>
                                <div id="mdNarration" class="fw-600" style="font-size:.88rem;line-height:1.6;word-break:break-all;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal actions for Pending -->
                    <div id="mdActions" class="d-flex gap-3 mt-4 pt-3 border-top" style="display:none!important;">
                        <form method="POST" onsubmit="return confirm('Approve this wire?')">
                            <input type="hidden" name="tx_id" id="mdAppId">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-success fw-800 px-4" style="border-radius:12px;"><i class="fa-solid fa-circle-check me-2"></i>Approve & Execute</button>
                        </form>
                        <form method="POST" onsubmit="return confirm('Reject this wire?')">
                            <input type="hidden" name="tx_id" id="mdRejId">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-danger fw-800 px-4" style="border-radius:12px;"><i class="fa-solid fa-circle-xmark me-2"></i>Reject Transfer</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('wireModal').addEventListener('show.bs.modal', function(e) {
            const d = e.relatedTarget.dataset;
            document.getElementById('mdAmt').textContent  = '$' + d.amount;
            document.getElementById('mdRef').textContent  = d.ref;
            document.getElementById('mdUser').textContent = d.user;
            document.getElementById('mdEmail').textContent   = d.email;
            document.getElementById('mdAccount').textContent = 'Acc: ' + d.account;
            document.getElementById('mdDate').textContent    = d.date;
            document.getElementById('mdNarration').textContent = d.narration;

            const sb = document.getElementById('mdStatusBadge');
            const statusMap = { Pending: ['bg-warning-light text-warning','Pending Review'], Completed: ['bg-emerald-light text-success','Approved'], Cancelled: ['bg-rose-light text-danger','Rejected'] };
            const [cls, lbl] = statusMap[d.status] || ['bg-light text-muted', d.status];
            sb.className = 'mt-3 d-inline-block px-4 py-1 rounded-pill fw-800 ' + cls;
            sb.textContent = lbl;

            document.getElementById('mdAppId').value = d.id;
            document.getElementById('mdRejId').value = d.id;
            document.getElementById('mdActions').style.display = d.status === 'Pending' ? 'flex' : 'none';
        });
    </script>
</body>
</html>
