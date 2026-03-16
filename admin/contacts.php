<?php
require_once '../includes/db.php';
require_once '../includes/admin-check.php'; 
if ($_SESSION['role'] !== 'Super Admin') {
    header("Location: index.php");
    exit();
}

// Mark as read when viewing
if (isset($_GET['id'])) {
    $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$_GET['id']]);
}

// Delete
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$_GET['delete']]);
    header('Location: contacts.php');
    exit;
}

// Fetch all messages
$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();

// Unread count
$unread = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();

// Active message
$active = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $active = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - SwiftCapital Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
            <a href="index.php" class="nav-link"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="users.php" class="nav-link"><i class="fa-solid fa-users"></i> Users Management</a>
            <a href="transactions.php" class="nav-link"><i class="fa-solid fa-money-bill-transfer"></i> Transactions</a>
            <a href="loans.php" class="nav-link"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Requests</a>
            <a href="irs.php" class="nav-link"><i class="fa-solid fa-file-invoice-dollar"></i> IRS Refunds</a>
            <a href="kyc.php" class="nav-link"><i class="fa-solid fa-id-card-clip"></i> KYC Verifications</a>
            <a href="support.php" class="nav-link"><i class="fa-solid fa-headset"></i> Support Tickets</a>
            <a href="contacts.php" class="nav-link active">
                <i class="fa-solid fa-envelope"></i> Contact Messages
                <?php if ($unread > 0): ?>
                <span class="badge bg-danger ms-auto" style="font-size:.6rem;"><?php echo $unread; ?></span>
                <?php endif; ?>
            </a>
            <?php if (in_array($_SESSION['role'] ?? '', ['Super Admin', 'Admin'])): ?>
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
                <h4 class="mb-0 fw-800">Contact Messages</h4>
                <p class="text-muted text-xs mb-0">Messages from the website contact form</p>
            </div>
            <div class="user-nav">
                <div class="notification-bell">
                    <i class="fa-solid fa-bell fs-5"></i>
                    <span class="notification-dot"></span>
                </div>
                <div class="admin-profile">
                    <div class="admin-avatar">
                        <?php if(!empty($_SESSION['profile_pic'])): ?>
                            <img src="../assets/uploads/profiles/<?php echo $_SESSION['profile_pic']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
                        <?php else: ?>
                            <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold text-sm"><?php echo $_SESSION['user_name'] ?? 'Admin'; ?></div>
                        <div class="text-xs text-muted"><?php echo $_SESSION['role'] ?? 'Administrator'; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-padding">

            <!-- Stats Row -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-indigo-light text-primary"><i class="fa-solid fa-envelope"></i></div>
                        <div class="stat-info">
                            <div class="stat-value"><?php echo count($messages); ?></div>
                            <div class="stat-label">Total Messages</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-rose-light text-danger"><i class="fa-solid fa-envelope-open"></i></div>
                        <div class="stat-info">
                            <div class="stat-value"><?php echo $unread; ?></div>
                            <div class="stat-label">Unread</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-emerald-light text-success"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="stat-info">
                            <div class="stat-value"><?php echo count($messages) - $unread; ?></div>
                            <div class="stat-label">Read</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-amber-light text-warning"><i class="fa-solid fa-calendar-day"></i></div>
                        <div class="stat-info">
                            <div class="stat-value"><?php
                                $today = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at) = CURDATE()")->fetchColumn();
                                echo $today;
                            ?></div>
                            <div class="stat-label">Today</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Messages List -->
                <div class="col-lg-5">
                    <div class="data-table-card mt-0" style="height: 650px; display: flex; flex-direction: column;">
                        <div class="card-header border-0 border-bottom bg-transparent py-4 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-800">Inbox</h5>
                            <?php if ($unread > 0): ?>
                            <span class="badge bg-rose-light text-danger fw-800 px-3"><?php echo $unread; ?> NEW</span>
                            <?php endif; ?>
                        </div>
                        <div class="list-group list-group-flush flex-grow-1" style="overflow-y: auto;">
                            <?php if (empty($messages)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fa-solid fa-inbox fa-3x mb-3 d-block" style="opacity:.3;"></i>
                                <p class="fw-600">No messages yet</p>
                            </div>
                            <?php else: ?>
                            <?php foreach ($messages as $msg): 
                                $is_active = isset($_GET['id']) && $_GET['id'] == $msg['id'];
                                $is_unread = !$msg['is_read'];
                            ?>
                            <a href="contacts.php?id=<?php echo $msg['id']; ?>" 
                               class="list-group-item list-group-item-action p-4 border-0 mb-1"
                               style="<?php echo $is_active ? 'background:#f1f5f9;border-left:5px solid var(--admin-primary)!important;' : ''; ?> <?php echo $is_unread ? 'font-weight:700;' : ''; ?>">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if ($is_unread): ?>
                                        <span class="rounded-circle bg-primary" style="width:8px;height:8px;display:inline-block;flex-shrink:0;"></span>
                                        <?php endif; ?>
                                        <span class="fw-800 text-dark text-sm"><?php echo htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']); ?></span>
                                    </div>
                                    <span class="text-xs text-muted"><?php echo date('M d', strtotime($msg['created_at'])); ?></span>
                                </div>
                                <div class="text-xs fw-700 text-primary mb-1"><?php echo htmlspecialchars(ucfirst($msg['subject'])); ?></div>
                                <div class="text-xs text-muted" style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;max-width:100%;">
                                    <?php echo htmlspecialchars(substr($msg['message'], 0, 80)); ?>...
                                </div>
                            </a>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Message Detail -->
                <div class="col-lg-7">
                    <?php if ($active): ?>
                    <div class="data-table-card mt-0" style="min-height: 650px; border-radius: 24px;">
                        <!-- Header -->
                        <div class="card-header border-0 border-bottom bg-white d-flex justify-content-between align-items-center py-4 px-4" style="border-radius:24px 24px 0 0;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="admin-avatar" style="width:48px;height:48px;font-size:1rem;background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                                    <?php echo strtoupper(substr($active['first_name'], 0, 1) . substr($active['last_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-800"><?php echo htmlspecialchars($active['first_name'] . ' ' . $active['last_name']); ?></h5>
                                    <div class="text-xs text-muted fw-600"><?php echo htmlspecialchars($active['email']); ?> <?php if($active['phone']): ?> · <?php echo htmlspecialchars($active['phone']); ?><?php endif; ?></div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="mailto:<?php echo htmlspecialchars($active['email']); ?>" class="btn btn-sm btn-primary fw-800 px-3" style="border-radius:10px;">
                                    <i class="fa-solid fa-reply me-1"></i> Reply
                                </a>
                                <a href="contacts.php?delete=<?php echo $active['id']; ?>" class="btn btn-sm btn-light border fw-800 px-3" style="border-radius:10px;" onclick="return confirm('Delete this message?')">
                                    <i class="fa-solid fa-trash text-danger"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="px-4 py-3 border-bottom" style="background:#f8fafc;">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-indigo-light text-primary fw-800 px-3 py-2">
                                    <?php echo htmlspecialchars(ucfirst($active['subject'])); ?>
                                </span>
                                <span class="text-xs text-muted fw-600">
                                    <i class="fa-solid fa-clock me-1"></i>
                                    <?php echo date('F j, Y \a\t g:i A', strtotime($active['created_at'])); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Message Body -->
                        <div class="p-5">
                            <div class="p-4 bg-light rounded-4 mb-4">
                                <p class="mb-0 text-dark" style="line-height:1.8;white-space:pre-wrap;"><?php echo htmlspecialchars($active['message']); ?></p>
                            </div>

                            <!-- Quick Reply via Email -->
                            <div class="p-4 border rounded-4" style="border-color:#e5e7eb!important;">
                                <h6 class="fw-800 mb-3"><i class="fa-solid fa-reply me-2 text-primary"></i>Quick Reply</h6>
                                <p class="text-muted text-sm mb-3">Click "Reply via Email" to open your email client with this contact's address pre-filled.</p>
                                <a href="mailto:<?php echo htmlspecialchars($active['email']); ?>?subject=Re: <?php echo urlencode(ucfirst($active['subject'])); ?>%20— SwiftCapital&body=Dear%20<?php echo urlencode($active['first_name']); ?>,%0A%0AThank%20you%20for%20contacting%20SwiftCapital.%0A%0A" 
                                   class="btn btn-primary fw-800 px-4" style="border-radius:12px;">
                                    <i class="fa-solid fa-envelope me-2"></i>Reply via Email
                                </a>
                                <a href="contacts.php?delete=<?php echo $active['id']; ?>" class="btn btn-light border fw-800 px-4 ms-2" style="border-radius:12px;" onclick="return confirm('Delete this message?')">
                                    <i class="fa-solid fa-trash text-danger me-2"></i>Delete
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php else: ?>
                    <div class="data-table-card mt-0 d-flex flex-column align-items-center justify-content-center text-center p-5" style="height:650px;border-radius:24px;border:2px dashed #e2e8f0;background:transparent;">
                        <div class="bg-indigo-light p-4 rounded-circle mb-4">
                            <i class="fa-solid fa-envelope-open text-primary" style="font-size:3rem;"></i>
                        </div>
                        <h4 class="fw-800">Select a Message</h4>
                        <p class="text-muted" style="max-width:300px;">Choose a message from the inbox on the left to read it here.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <footer class="mt-auto py-4 px-4 border-top bg-white text-center text-muted" style="font-size:.85rem;">
            SwiftCapital Admin &copy; 2026. Internal System Only.
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
