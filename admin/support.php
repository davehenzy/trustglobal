<?php 
require_once '../includes/db.php';
require_once '../includes/admin-check.php'; 

$active_ticket_id = $_GET['id'] ?? null;
$active_ticket = null;
$messages = [];

// Handle Admin Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_reply']) && $active_ticket_id) {
        $message = cleanInput($_POST['message']);
        if (!empty($message)) {
            $stmt = $pdo->prepare("INSERT INTO messages (ticket_id, sender_id, message, is_admin) VALUES (?, ?, ?, 1)");
            $stmt->execute([$active_ticket_id, $_SESSION['user_id'], $message]);
            
            // Mark as Pending (waiting for user)
            $pdo->prepare("UPDATE support_tickets SET status = 'Pending' WHERE id = ?")->execute([$active_ticket_id]);
            
            header("Location: support.php?id=" . $active_ticket_id);
            exit;
        }
    }
    
    if (isset($_POST['resolve_ticket']) && $active_ticket_id) {
        $pdo->prepare("UPDATE support_tickets SET status = 'Resolved' WHERE id = ?")->execute([$active_ticket_id]);
        header("Location: support.php?id=" . $active_ticket_id);
        exit;
    }

    if (isset($_POST['archive_ticket']) && $active_ticket_id) {
        $pdo->prepare("UPDATE support_tickets SET status = 'Closed' WHERE id = ?")->execute([$active_ticket_id]);
        header("Location: support.php");
        exit;
    }
}

// Fetch Inbox (Active Tickets)
$stmt = $pdo->query("SELECT t.*, u.name, u.lastname FROM support_tickets t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC");
$inbox_tickets = $stmt->fetchAll();

// Count active
$active_count = 0;
foreach($inbox_tickets as $t) {
    if ($t['status'] == 'Open' || $t['status'] == 'Pending') $active_count++;
}

// Fetch Active Ticket Details
if ($active_ticket_id) {
    $stmt = $pdo->prepare("SELECT t.*, u.name, u.lastname, u.email, u.account_number FROM support_tickets t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
    $stmt->execute([$active_ticket_id]);
    $active_ticket = $stmt->fetch();
    
    if ($active_ticket) {
        $stmt = $pdo->prepare("SELECT * FROM messages WHERE ticket_id = ? ORDER BY created_at ASC");
        $stmt->execute([$active_ticket_id]);
        $messages = $stmt->fetchAll();
    }
}

$admin_initials = strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1) . substr($_SESSION['user_lastname'] ?? 'D', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Center - SwiftCapital Admin</title>
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
            <a href="irs.php" class="nav-link">
                <i class="fa-solid fa-file-invoice-dollar"></i> IRS Refunds
            </a>
            <a href="kyc.php" class="nav-link">
                <i class="fa-solid fa-id-card-clip"></i> KYC Verifications
            </a>
            <a href="support.php" class="nav-link active">
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
                <h4 class="mb-0 fw-800">Support Command Center</h4>
            </div>

            <div class="user-nav">
                <div class="notification-bell">
                    <i class="fa-solid fa-bell fs-5"></i>
                    <span class="notification-dot"></span>
                </div>
                
                <div class="admin-profile">
                    <div class="admin-avatar"><?php echo strtoupper(substr($_SESSION["user_name"] ?? "A", 0, 1)); ?></div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold text-sm"><?php echo $_SESSION["user_name"] ?? "Admin"; ?></div>
                        <div class="text-xs text-muted"><?php echo $_SESSION["role"] ?? "Administrator"; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-padding">
            
            <div class="row g-4">
                <!-- Support Tickets List -->
                <div class="col-lg-4">
                    <div class="data-table-card mt-0" style="height: 700px; display: flex; flex-direction: column;">
                        <div class="card-header border-0 border-bottom bg-transparent py-4 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-800">Inbox</h5>
                            <span class="badge bg-indigo-light text-primary fw-800 px-3"><?php echo $active_count; ?> ACTIVE</span>
                        </div>
                        <div class="p-3 border-bottom">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-search text-muted"></i></span>
                                <input type="text" class="form-control bg-light border-0" placeholder="Search conversations...">
                            </div>
                        </div>
                        <div class="list-group list-group-flush flex-grow-1" style="overflow-y: auto;">
                            <?php foreach($inbox_tickets as $t): ?>
                                <?php 
                                    $is_active = ($active_ticket_id == $t['id']);
                                    $status_class = 'bg-light text-muted';
                                    if($t['status'] == 'Open') $status_class = 'bg-rose-light text-danger';
                                    elseif($t['status'] == 'Pending') $status_class = 'bg-warning-light text-warning';
                                    elseif($t['status'] == 'Resolved') $status_class = 'bg-success-light text-success';
                                    
                                    $t_initials = strtoupper(substr($t['name'], 0, 1) . substr($t['lastname'], 0, 1));
                                ?>
                                <a href="support.php?id=<?php echo $t['id']; ?>" class="list-group-item list-group-item-action p-4 border-0 mb-1 <?php echo $is_active ? 'active-ticket' : ''; ?>" style="<?php echo $is_active ? 'background: #f1f5f9; border-left: 5px solid var(--admin-primary) !important;' : ''; ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge <?php echo $status_class; ?> fw-800 text-uppercase" style="font-size: 0.6rem;"><?php echo $t['status']; ?></span>
                                        <span class="text-xs text-muted fw-600"><?php echo date('H:i', strtotime($t['created_at'])); ?></span>
                                    </div>
                                    <h6 class="mb-1 fw-800 text-dark"><?php echo htmlspecialchars($t['subject']); ?></h6>
                                    <div class="d-flex align-items-center gap-2 mt-3">
                                        <div class="admin-avatar" style="width: 24px; height: 24px; font-size: 0.6rem;"><?php echo $t_initials; ?></div>
                                        <span class="text-xs fw-800 text-dark"><?php echo htmlspecialchars($t['name'] . ' ' . $t['lastname']); ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <?php if($active_ticket): ?>
                    <div class="data-table-card mt-0 d-flex flex-column shadow-sm" style="height: 700px; border-radius: 24px;">
                        <div class="card-header border-0 border-bottom bg-white d-flex justify-content-between align-items-center py-4 px-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="admin-avatar" style="width: 48px; height: 48px; font-size: 1rem;"><?php echo strtoupper(substr($active_ticket['name'], 0, 1) . substr($active_ticket['lastname'], 0, 1)); ?></div>
                                <div>
                                    <h5 class="mb-1 fw-800 text-dark"><?php echo htmlspecialchars($active_ticket['name'] . ' ' . $active_ticket['lastname']); ?></h5>
                                    <div class="text-xs text-muted fw-600"><span class="text-success pulse-dot me-1"></span> ONLINE â€¢ Ticket #TK-<?php echo str_pad($active_ticket['id'], 5, '0', STR_PAD_LEFT); ?></div>
                                </div>
                            </div>
                            <form method="POST" class="d-flex gap-2">
                                <button type="submit" name="archive_ticket" class="btn btn-light btn-sm fw-bold border-0 px-3" style="border-radius: 10px;">Archive</button>
                                <button type="submit" name="resolve_ticket" class="btn btn-primary btn-sm fw-800 px-4" style="border-radius: 10px;">Resolve</button>
                            </form>
                        </div>

                        <!-- Messages Container -->
                        <div class="p-4 flex-grow-1" id="messageBox" style="overflow-y: auto; background: #fcfdfe;">
                            <div class="text-center my-4">
                                <span class="badge bg-light text-muted fw-bold px-3 py-2 text-xs" style="border-radius: 20px;"><?php echo date('M d, Y', strtotime($active_ticket['created_at'])); ?></span>
                            </div>

                            <?php foreach($messages as $msg): ?>
                                <?php if($msg['is_admin']): ?>
                                    <div class="mb-5 text-end">
                                        <div class="d-flex gap-3 mb-2 justify-content-end">
                                            <div class="p-4 text-white shadow-lg border-0 rounded-4" style="max-width: 75%; background: linear-gradient(135deg, var(--admin-primary), #6366f1); border-top-right-radius: 4px !important;">
                                                <p class="text-sm mb-0 fw-500"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                            </div>
                                            <div class="admin-avatar shadow-sm" style="width: 32px; height: 32px; background: #0f172a;"><?php echo $admin_initials; ?></div>
                                        </div>
                                        <span class="text-xs text-muted me-5 fw-600"><?php echo date('H:i A', strtotime($msg['created_at'])); ?></span>
                                    </div>
                                <?php else: ?>
                                    <div class="mb-5">
                                        <div class="d-flex gap-3 mb-2">
                                            <div class="admin-avatar shadow-sm" style="width: 32px; height: 32px;"><?php echo strtoupper(substr($active_ticket['name'], 0, 1) . substr($active_ticket['lastname'], 0, 1)); ?></div>
                                            <div class="p-4 bg-white shadow-sm border-0 rounded-4" style="max-width: 75%; border-top-left-radius: 4px !important;">
                                                <p class="text-sm mb-0 fw-500 text-dark"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                            </div>
                                        </div>
                                        <span class="text-xs text-muted ms-5 fw-600"><?php echo date('H:i A', strtotime($msg['created_at'])); ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <!-- Reply Input -->
                        <div class="p-4 bg-white border-top">
                            <form method="POST">
                                <div class="input-group gap-2 p-1 bg-light rounded-4" style="border: 2px solid transparent; transition: 0.3s;">
                                    <textarea name="message" class="form-control border-0 bg-transparent py-3 px-3 shadow-none" rows="1" placeholder="Type your response to <?php echo $active_ticket['name']; ?>..." style="resize: none;"></textarea>
                                    <button type="submit" name="send_reply" class="btn btn-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-radius: 12px;"><i class="fa-solid fa-paper-plane"></i></button>
                                </div>
                            </form>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="d-flex gap-4">
                                    <button class="btn btn-link btn-xs p-0 text-decoration-none text-muted fw-800" style="font-size: 0.7rem;"><i class="fa-solid fa-paperclip me-2"></i> ATTACH</button>
                                    <button class="btn btn-link btn-xs p-0 text-decoration-none text-muted fw-800" style="font-size: 0.7rem;"><i class="fa-solid fa-image me-2"></i> IMAGE</button>
                                </div>
                                <div class="text-xs text-muted fw-600">Secure Audit Channel</div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                        <div class="data-table-card mt-0 d-flex flex-column align-items-center justify-content-center text-center p-5" style="height: 700px; border-radius: 24px; border: 2px dashed #e2e8f0; background: transparent;">
                            <div class="bg-indigo-light p-4 rounded-circle mb-4">
                                <i class="fa-solid fa-headset text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="fw-800">Select a Conversation</h4>
                            <p class="text-muted" style="max-width: 300px;">Choose a ticket from the inbox on the left to start assisting our customers.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="mt-auto py-4 px-4 border-top bg-white text-center text-muted" style="font-size: 0.85rem;">
            SwiftCapital Admin © 2026. Internal System Only.
        </footer>
    </div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        .active-ticket {
            background: #f1f5f9;
            border-left: 5px solid var(--admin-primary) !important;
        }
    </style>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageBox = document.getElementById('messageBox');
            if(messageBox) {
                messageBox.scrollTop = messageBox.scrollHeight;
            }

            // Update Breadcrumb/Title if active
            const activeTitle = "<?php echo $active_ticket ? htmlspecialchars($active_ticket['subject']) : 'Support Command Center'; ?>";
            const breadcrumb = document.querySelector('.breadcrumb-area h4');
            if(breadcrumb) breadcrumb.textContent = activeTitle;
        });
    </script>
</body>
</html>
