<aside class="sidebar">
    <div class="brand-section">
        <a href="index.php" class="d-block py-2">
            <img src="../assets/images/SWC%20Secondary%20Logo%20Light.png" alt="SwiftCapital" height="40">
        </a>
    </div>

    <div class="user-profile-widget">
        <div class="avatar-circle" style="overflow: hidden;">
            <?php if(!empty($_SESSION['profile_pic'])): ?>
                <img src="../assets/uploads/profiles/<?php echo $_SESSION['profile_pic']; ?>" alt="Profile" style="width:100%; height:100%; object-fit:cover;">
            <?php else: ?>
                <?php echo strtoupper(substr($_SESSION['name'], 0, 1) . substr($_SESSION['lastname'], 0, 1)); ?>
            <?php endif; ?>
        </div>
        <div class="user-name"><?php echo htmlspecialchars($_SESSION['name'] . ' ' . $_SESSION['lastname']); ?></div>
        <div class="user-id">ID: <?php echo $_SESSION['account_number']; ?></div>
        <?php if($_SESSION['kyc_status'] != 'Verified' && $_SESSION['kyc_status'] != 'Pending'): ?>
            <button class="btn btn-kyc" onclick="location.href='kyc.php'"><i class="fa-solid fa-circle-exclamation"></i> Verify KYC</button>
        <?php elseif($_SESSION['kyc_status'] == 'Pending'): ?>
            <span class="badge bg-warning text-dark w-100 py-2 mb-2">Audit Pending</span>
        <?php endif; ?>
        <div class="user-actions">
            <a href="settings.php" class="btn btn-outline"><i class="fa-solid fa-user"></i> Profile</a>
            <a href="../logout.php" class="btn btn-primary-soft text-decoration-none"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <div class="nav-section">
        <div class="nav-category">Main Menu</div>
        <a href="index.php" class="nav-item-link <?php echo $page == 'dashboard' ? 'active' : ''; ?>"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="transactions.php" class="nav-item-link <?php echo $page == 'transactions' ? 'active' : ''; ?>"><i class="fa-solid fa-chart-line"></i> Transactions</a>
        <a href="cards.php" class="nav-item-link <?php echo $page == 'cards' ? 'active' : ''; ?>"><i class="fa-solid fa-credit-card"></i> Cards</a>

        <div class="nav-category">Transfers</div>
        <a href="local.php" class="nav-item-link <?php echo $page == 'local' ? 'active' : ''; ?>"><i class="fa-solid fa-paper-plane"></i> Local Transfer</a>
        <a href="international.php" class="nav-item-link <?php echo $page == 'international' ? 'active' : ''; ?>"><i class="fa-solid fa-globe"></i> International Wire</a>
        <a href="deposit.php" class="nav-item-link <?php echo $page == 'deposit' ? 'active' : ''; ?>"><i class="fa-solid fa-download"></i> Deposit</a>

        <div class="nav-category">Services</div>
        <a href="loan.php" class="nav-item-link <?php echo $page == 'loan' ? 'active' : ''; ?>"><i class="fa-solid fa-boxes-stacked"></i> Loan Request</a>
        <a href="irs.php" class="nav-item-link <?php echo $page == 'irs' ? 'active' : ''; ?>"><i class="fa-solid fa-file-invoice-dollar"></i> IRS Tax Refund</a>
        <a href="loan-history.php" class="nav-item-link <?php echo $page == 'loan-history' ? 'active' : ''; ?>"><i class="fa-solid fa-clock-rotate-left"></i> Loan History</a>

        <div class="nav-category">Account</div>
        <a href="security.php" class="nav-item-link <?php echo $page == 'security' ? 'active' : ''; ?>"><i class="fa-solid fa-gear"></i> Settings</a>
        <a href="support.php" class="nav-item-link <?php echo $page == 'support' ? 'active' : ''; ?>"><i class="fa-solid fa-circle-question"></i> Support Ticket</a>
    </div>

    <div class="sidebar-footer">
        <span><i class="fa-solid fa-shield-halved me-1"></i> Secure Banking</span>
        <span class="version">v1.2.0</span>
    </div>
</aside>
