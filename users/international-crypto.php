<?php require_once '../includes/user-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cryptocurrency Withdrawal - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .payment-card-premium {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.04);
            margin-bottom: 30px;
        }

        .payment-card-header {
            padding: 25px 30px;
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .method-info {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            color: #1a202c;
            font-size: 1.1rem;
        }

        .method-icon-box {
            width: 42px;
            height: 42px;
            background: #fff7ed;
            color: #f59e0b;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .amount-display-box {
            background: #f8fafc;
            border: 2px solid #edf2f7;
            border-radius: 16px;
            padding: 30px;
            margin: 30px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .amount-display-box:focus-within {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.08);
            transform: translateY(-2px);
        }

        .amount-label {
            font-size: 0.85rem;
            color: #718096;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            display: block;
        }

        .amount-input-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .currency-symbol {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a202c;
        }

        .amount-input-field {
            border: none;
            background: transparent;
            font-size: 3rem;
            font-weight: 800;
            color: #1a202c;
            width: 100%;
            outline: none;
            padding: 0;
        }

        .balance-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #edf2f7;
        }

        .available-balance {
            font-size: 0.9rem;
            color: #718096;
        }

        .quick-amounts {
            display: flex;
            gap: 8px;
        }

        .btn-quick {
            padding: 6px 14px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
            font-size: 0.8rem;
            font-weight: 700;
            color: #4a5568;
            transition: all 0.2s;
        }

        .btn-quick:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            background: #eff6ff;
        }

        .form-section {
            padding: 0 30px 30px;
        }

        .form-label-premium {
            font-size: 0.95rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
            display: block;
        }

        .coin-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 25px;
        }

        .coin-option {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .coin-option:hover {
            border-color: #3b82f6;
            background: #f8fafc;
            transform: translateY(-2px);
        }

        .coin-option.active {
            border-color: #3b82f6;
            background: #eff6ff;
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.1);
        }

        .coin-option i {
            display: block;
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .coin-option span {
            font-size: 0.8rem;
            font-weight: 800;
            color: #4a5568;
        }

        .coin-option.active span {
            color: #3b82f6;
        }

        .coin-option i.fa-bitcoin { color: #f59e0b; }
        .coin-option i.fa-ethereum { color: #6366f1; }
        .coin-option i.fa-dollar-sign { color: #10b981; }
        .coin-option i.fa-circle-dot { color: #eab308; }

        .btn-submit-premium {
            width: 100%;
            background: var(--primary-gradient);
            color: #fff;
            border: none;
            padding: 18px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 10px;
        }

        .btn-submit-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.35);
            filter: brightness(1.05);
        }

        .secure-badge {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f8fafc;
            padding: 15px 20px;
            border-radius: 12px;
            margin-top: 25px;
            border: 1px solid #edf2f7;
        }

        .secure-badge i {
            color: #10b981;
            font-size: 1.2rem;
        }

        .secure-badge p {
            margin: 0;
            font-size: 0.85rem;
            color: #64748b;
            line-height: 1.4;
        }
        
        .pin-wrapper {
            position: relative;
        }
        
        .pin-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            z-index: 10;
        }

        .warning-alert-premium {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 12px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
        }

        .warning-alert-premium i {
            color: #d97706;
            font-size: 1.1rem;
        }

        .warning-alert-premium p {
            margin-bottom: 0;
            font-size: 0.85rem;
            color: #92400e;
            font-weight: 600;
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
            <a href="international.php" class="nav-item-link active"><i class="fa-solid fa-globe"></i> International Wire</a>
            <a href="deposit.php" class="nav-item-link"><i class="fa-solid fa-download"></i> Deposit</a>

            <div class="nav-category">Services</div>
            <a href="loan.php" class="nav-item-link"><i class="fa-solid fa-boxes-stacked"></i> Loan Request</a>
            <a href="irs.php" class="nav-item-link"><i class="fa-solid fa-file-invoice-dollar"></i> IRS Tax Refund</a>
            <a href="loan-history.php" class="nav-item-link"><i class="fa-solid fa-clock-rotate-left"></i> Loan History</a>

            <div class="nav-category">Account</div>
            <a href="security.php" class="nav-item-link"><i class="fa-solid fa-gear"></i> Settings</a>
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
            
            <div class="page-header">
                <div>
                    <h1 class="page-title">International Transfer</h1>
                    <div class="breadcrumb-text">
                        <a href="index.php">Dashboard</a> <i class="fa-solid fa-chevron-right mx-2" style="font-size: 0.7rem;"></i> International <i class="fa-solid fa-chevron-right mx-2" style="font-size: 0.7rem;"></i> Cryptocurrency
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <div class="payment-card-premium">
                        <div class="payment-card-header">
                            <div class="method-info">
                                <div class="method-icon-box">
                                    <i class="fa-brands fa-bitcoin"></i>
                                </div>
                                Cryptocurrency Withdrawal
                            </div>
                            <div class="text-muted small">
                                <i class="fa-solid fa-bolt me-1"></i> Fast: 1-3 Hours
                            </div>
                        </div>

                        <form action="transactions.php">
                            <!-- Amount Selection -->
                            <div class="amount-display-box">
                                <span class="amount-label">Amount to Transfer</span>
                                <div class="amount-input-wrapper">
                                    <span class="currency-symbol">$</span>
                                    <input type="number" class="amount-input-field" value="0.00" id="amountInput" step="0.01" min="0">
                                </div>
                                <div class="balance-info">
                                    <div class="available-balance">
                                        Available: <span class="fw-bold text-dark">$0.00</span>
                                    </div>
                                    <div class="quick-amounts">
                                        <button type="button" class="btn-quick" onclick="setAmount(100)">$100</button>
                                        <button type="button" class="btn-quick" onclick="setAmount(500)">$500</button>
                                        <button type="button" class="btn-quick" onclick="setAmount(1000)">$1000</button>
                                        <button type="button" class="btn-quick" onclick="setAmount(0)">Max</button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <label class="form-label-premium">Select Cryptocurrency</label>
                                <div class="coin-grid">
                                    <div class="coin-option active" onclick="selectCoin(this, 'BTC')">
                                        <i class="fa-brands fa-bitcoin"></i>
                                        <span>BITCOIN</span>
                                    </div>
                                    <div class="coin-option" onclick="selectCoin(this, 'ETH')">
                                        <i class="fa-brands fa-ethereum"></i>
                                        <span>ETHEREUM</span>
                                    </div>
                                    <div class="coin-option" onclick="selectCoin(this, 'USDT')">
                                        <i class="fa-solid fa-dollar-sign"></i>
                                        <span>USDT</span>
                                    </div>
                                    <div class="coin-option" onclick="selectCoin(this, 'BNB')">
                                        <i class="fa-solid fa-circle-dot"></i>
                                        <span>BNB</span>
                                    </div>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label-premium">Network</label>
                                        <div class="custom-input-group">
                                            <select required>
                                                <option value="" disabled selected>Select Network</option>
                                                <option value="native">Native (Recommended)</option>
                                                <option value="erc20">ERC20</option>
                                                <option value="bep20">BEP20</option>
                                                <option value="trc20">TRC20</option>
                                            </select>
                                            <i class="fa-solid fa-network-wired left-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-premium">Destination Wallet</label>
                                        <div class="custom-input-group">
                                            <input type="text" placeholder="Paste address here" required>
                                            <i class="fa-solid fa-wallet left-icon"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="warning-alert-premium">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <p>Ensure you select the correct network. Sending to the wrong network will result in permanent loss of funds.</p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label-premium">Transaction PIN</label>
                                        <div class="custom-input-group pin-wrapper">
                                            <input type="password" id="pinInput" placeholder="Enter your secret PIN" required>
                                            <i class="fa-solid fa-key left-icon"></i>
                                            <i class="fa-solid fa-eye-slash pin-toggle" onclick="togglePin()"></i>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn-submit-premium">
                                    <i class="fa-solid fa-paper-plane"></i> Initialize Crypto Transfer
                                </button>

                                <div class="secure-badge">
                                    <i class="fa-solid fa-shield-check"></i>
                                    <p>Your transfer is secured by 256-bit encryption. Crypto withdrawals are final and non-reversible.</p>
                                </div>
                            </div>
                        </form>
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

        function setAmount(val) {
            document.getElementById('amountInput').value = val.toFixed(2);
        }

        function selectCoin(element, symbol) {
            document.querySelectorAll('.coin-option').forEach(opt => opt.classList.remove('active'));
            element.classList.add('active');
            
            // Update card header icon
            const mainIcon = document.querySelector('.method-icon-box i');
            mainIcon.className = '';
            
            if(symbol === 'BTC') mainIcon.className = 'fa-brands fa-bitcoin';
            if(symbol === 'ETH') mainIcon.className = 'fa-brands fa-ethereum';
            if(symbol === 'USDT') mainIcon.className = 'fa-solid fa-dollar-sign';
            if(symbol === 'BNB') mainIcon.className = 'fa-solid fa-circle-dot';
        }

        function togglePin() {
            const pinInput = document.getElementById('pinInput');
            const pinToggle = document.querySelector('.pin-toggle');
            if (pinInput.type === 'password') {
                pinInput.type = 'text';
                pinToggle.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                pinInput.type = 'password';
                pinToggle.classList.replace('fa-eye', 'fa-eye-slash');
            }
        }
    </script>
</body>
</html>

