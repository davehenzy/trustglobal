<?php require_once '../includes/user-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Settings - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .security-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            margin-bottom: 40px;
        }

        .security-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .security-section-title i {
            color: #38bdf8;
            font-size: 1.1rem;
        }

        .security-subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .security-input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .security-input-group i.left-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .security-input-group i.right-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.9rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .security-input-group i.right-icon:hover {
            color: var(--primary-color);
        }

        .security-input-group input {
            width: 100%;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 14px 45px;
            font-size: 0.95rem;
            color: var(--text-dark);
            outline: none;
            transition: all 0.2s;
            background: #fff;
        }

        .security-input-group input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
        }

        .requirement-box {
            background-color: #f0f9ff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .requirement-box-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1e40af;
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 12px;
        }

        .requirement-box-title i {
            color: #3b82f6;
            font-size: 1.1rem;
        }

        .requirement-box p {
            color: #1e40af;
            font-size: 0.85rem;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .requirement-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .requirement-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #3b82f6;
            font-size: 0.85rem;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .requirement-item::before {
            content: 'â—';
            font-size: 0.6rem;
            color: #0ea5e9;
        }

        .reminder-box {
            background-color: #fefce8;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            border: 1px solid #fef08a;
        }

        .reminder-box i {
            color: #eab308;
            font-size: 1.1rem;
            margin-top: 3px;
        }

        .reminder-content h6 {
            color: #854d0e;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 6px;
        }

        .reminder-content p {
            color: #854d0e;
            font-size: 0.85rem;
            margin-bottom: 0;
            line-height: 1.5;
            opacity: 0.9;
        }

        .btn-change-password {
            background-color: #0ea5e9;
            color: #fff;
            border: none;
            padding: 14px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
        }

        .btn-change-password:hover {
            background-color: #0284c7;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="brand-section">
            <div class="brand-logo">
                <i class="fa-solid fa-chart-simple text-primary me-2"></i>
                <span class="swift">Swift</span><span class="capital">Capital</span>
            </div>
            <div class="brand-tagline">Banking At Its Best</div>
        </div>

        <div class="user-profile-widget">
            <div class="avatar-circle">KC</div>
            <div class="user-name">Kante Calm</div>
            <div class="user-id">ID: 0537658047</div>
            <button class="btn btn-kyc" onclick="location.href='verification.php'"><i class="fa-solid fa-circle-exclamation"></i> Verify KYC</button>
            <div class="user-actions">
                <a href="settings.php" class="btn btn-outline"><i class="fa-solid fa-user"></i> Profile</a>
                <a href="#" class="btn btn-primary-soft"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-category">Main Menu</div>
            <a href="index.php" class="nav-item-link"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a href="transactions.php" class="nav-item-link"><i class="fa-solid fa-chart-line"></i> Transactions</a>
            <a href="cards.php" class="nav-item-link"><i class="fa-solid fa-credit-card"></i> Cards</a>

            <div class="nav-category">Transfers</div>
            <a href="local.php" class="nav-item-link"><i class="fa-solid fa-paper-plane"></i> Local Transfer</a>
            <a href="international.php" class="nav-item-link"><i class="fa-solid fa-globe"></i> International Wire</a>
            <a href="deposit.php" class="nav-item-link"><i class="fa-solid fa-download"></i> Deposit</a>

            <div class="nav-category">Services</div>
            <a href="loan.php" class="nav-item-link"><i class="fa-solid fa-boxes-stacked"></i> Loan Request</a>
            <a href="irs.php" class="nav-item-link"><i class="fa-solid fa-file-invoice-dollar"></i> IRS Tax Refund</a>
            <a href="loan-history.php" class="nav-item-link"><i class="fa-solid fa-clock-rotate-left"></i> Loan History</a>

            <div class="nav-category">Account</div>
            <a href="settings.php" class="nav-item-link active"><i class="fa-solid fa-gear"></i> Settings</a>
            <a href="support.php" class="nav-item-link"><i class="fa-solid fa-circle-question"></i> Support Ticket</a>
        </div>

        <div class="sidebar-footer">
            <span><i class="fa-solid fa-shield-halved me-1"></i> Secure Banking</span>
            <span class="version">v1.2.0</span>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="nav-date">
                <i class="fa-solid fa-calendar"></i>
                <span id="currentDate">Thursday, March 12, 2026</span>
            </div>
            
            <div class="nav-actions">
                <div class="balance-badge">
                    <i class="fa-solid fa-wallet"></i> $0
                </div>
                <button class="btn-icon-only">
                    <i class="fa-solid fa-bell"></i>
                </button>
                <div class="nav-avatar">KC</div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="page-container">
            
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    
                    <div class="page-header mb-4">
                        <div>
                            <h1 class="page-title">Security Settings</h1>
                            <div class="breadcrumb-text">
                                <a href="index.php">Dashboard</a> <i class="fa-solid fa-chevron-right mx-2" style="font-size: 0.7rem;"></i> Settings <i class="fa-solid fa-chevron-right mx-2" style="font-size: 0.7rem;"></i> Security
                            </div>
                        </div>
                    </div>

                    <div class="security-card">
                        <div class="security-section-title">
                            <i class="fa-solid fa-shield"></i> Change Password
                        </div>
                        <p class="security-subtitle">Update your account password to maintain security</p>

                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Current Password</label>
                            <div class="security-input-group">
                                <i class="fa-solid fa-lock left-icon"></i>
                                <input type="password" placeholder="Enter your current password">
                                <i class="fa-solid fa-eye right-icon"></i>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">New Password</label>
                            <div class="security-input-group">
                                <i class="fa-solid fa-key left-icon"></i>
                                <input type="password" placeholder="Enter your new password">
                                <i class="fa-solid fa-eye right-icon"></i>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Confirm Password</label>
                            <div class="security-input-group">
                                <i class="fa-solid fa-circle-check left-icon"></i>
                                <input type="password" placeholder="Confirm your new password">
                                <i class="fa-solid fa-eye right-icon"></i>
                            </div>
                        </div>

                        <div class="requirement-box">
                            <div class="requirement-box-title">
                                <i class="fa-solid fa-shield-check"></i> Password Requirements
                            </div>
                            <p>Ensure that these requirements are met:</p>
                            <ul class="requirement-list">
                                <li class="requirement-item">Minimum 8 characters long - the more, the better</li>
                                <li class="requirement-item">At least one lowercase character</li>
                                <li class="requirement-item">At least one uppercase character</li>
                                <li class="requirement-item">At least one number</li>
                            </ul>
                        </div>

                        <div class="reminder-box">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <div class="reminder-content">
                                <h6>Security Reminder</h6>
                                <p>After changing your password, you'll be required to log in again with your new credentials. Make sure to remember your new password or store it in a secure password manager.</p>
                            </div>
                        </div>

                        <button class="btn-change-password">
                            <i class="fa-solid fa-lock"></i> Change Password
                        </button>
                    </div>

                </div>
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
