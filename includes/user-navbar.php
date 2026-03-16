<?php
// Fetch Notifications for Navbar
$stmt_notif = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt_notif->execute([$_SESSION['user_id']]);
$notifs = $stmt_notif->fetchAll();

$unread_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$unread_stmt->execute([$_SESSION['user_id']]);
$unread_count = $unread_stmt->fetchColumn();

// Latest unread for popup
$latest_notif = null;
foreach($notifs as $n) {
    if(!$n['is_read']) {
        $latest_notif = $n;
        break;
    }
}
?>
<nav class="top-navbar">
    <div class="nav-date">
        <i class="fa-solid fa-calendar"></i>
        <span id="currentDate"></span>
    </div>
    
    <div class="nav-actions">
        <div class="balance-badge">
            <i class="fa-solid fa-wallet"></i> $<?php echo number_format($_SESSION['balance'], 2); ?>
        </div>
        
        <div class="dropdown">
            <button class="btn-icon-only position-relative" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-bell"></i>
                <?php if($unread_count > 0): ?>
                    <span class="notif-dot"></span>
                <?php endif; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0 border-0" aria-labelledby="notifDropdown">
                <div class="notif-header">
                    <h6>Notifications</h6>
                    <span class="badge bg-primary-soft text-primary"><?php echo $unread_count; ?> New</span>
                </div>
                <div class="notif-scroll">
                    <?php if(empty($notifs)): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fa-solid fa-bell-slash mb-2 d-block" style="font-size: 1.5rem;"></i>
                            <span class="text-xs">No notifications yet</span>
                        </div>
                    <?php else: ?>
                        <?php foreach($notifs as $n): ?>
                            <?php 
                                $icon = 'fa-bell';
                                $bg = 'bg-blue-light';
                                if($n['type'] == 'Transaction') { $icon = 'fa-exchange-alt'; $bg = 'bg-green-light'; }
                                elseif($n['type'] == 'Loan') { $icon = 'fa-hand-holding-usd'; $bg = 'bg-purple-light'; }
                                elseif($n['type'] == 'KYC') { $icon = 'fa-user-shield'; $bg = 'bg-warning-light'; }
                            ?>
                            <a href="#" class="notif-item <?php echo $n['is_read'] ? '' : 'unread'; ?>" onclick="markRead(<?php echo $n['id']; ?>)">
                                <div class="notif-icon <?php echo $bg; ?>">
                                    <i class="fa-solid <?php echo $icon; ?>"></i>
                                </div>
                                <div class="notif-content">
                                    <div class="title"><?php echo htmlspecialchars($n['title']); ?></div>
                                    <div class="msg"><?php echo htmlspecialchars($n['message']); ?></div>
                                    <div class="time"><?php echo date('M d, H:i', strtotime($n['created_at'])); ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="notif-footer">
                    <a href="#">Clear all</a>
                </div>
            </div>
        </div>

        <div class="dropdown">
            <div class="nav-avatar" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; overflow: hidden;">
                <?php if(!empty($_SESSION['profile_pic'])): ?>
                    <img src="../assets/uploads/profiles/<?php echo $_SESSION['profile_pic']; ?>" alt="Profile" style="width:100%; height:100%; object-fit:cover;">
                <?php else: ?>
                    <?php echo strtoupper(substr($_SESSION['name'], 0, 1) . substr($_SESSION['lastname'], 0, 1)); ?>
                <?php endif; ?>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 mt-2" aria-labelledby="profileDropdown" style="border-radius: 12px; min-width: 200px;">
                <li class="px-3 py-2 border-bottom mb-2">
                    <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?php echo $_SESSION['name'] . ' ' . $_SESSION['lastname']; ?></div>
                    <div class="text-muted small" style="font-size: 0.75rem;"><?php echo $_SESSION['email']; ?></div>
                </li>
                <li><a class="dropdown-item rounded-3 py-2" href="settings.php" style="font-size: 0.85rem;"><i class="fa-solid fa-user-gear me-2 text-muted"></i> Account Settings</a></li>
                <li><a class="dropdown-item rounded-3 py-2 text-danger" href="../logout.php" style="font-size: 0.85rem;"><i class="fa-solid fa-power-off me-2"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Toast Container -->
<div class="toast-container-custom" id="toastContainer"></div>

<script>
    // Show latest unread toast if exists
    <?php if($latest_notif): ?>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            if(window.showToast) {
                showToast(
                    "<?php echo addslashes($latest_notif['title']); ?>", 
                    "<?php echo addslashes($latest_notif['message']); ?>", 
                    "<?php echo $latest_notif['type']; ?>"
                );
            }
        }, 1500);
    });
    <?php endif; ?>
</script>
