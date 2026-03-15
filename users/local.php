<?php require_once '../includes/user-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Transfer - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        /* Hero Header Premium */
        .transfer-hero-premium {
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            border-radius: 20px 20px 0 0;
            padding: 50px 40px;
            color: #fff;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .transfer-hero-premium::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .transfer-hero-icon-box {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #3b82f6;
            margin: 0 auto 20px;
        }

        .transfer-hero-premium h4 {
            font-weight: 800;
            font-size: 1.75rem;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .transfer-hero-premium p {
            font-size: 1rem;
            opacity: 0.8;
            margin-bottom: 0;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Form Body Premium */
        .transfer-body-premium {
            background: #fff;
            padding: 40px 50px;
            border: 1px solid #edf2f7;
            border-top: none;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.03);
        }
        
        .balance-info-premium {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 16px;
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .balance-meta {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .balance-icon-circle {
            width: 48px;
            height: 48px;
            background: #3b82f6;
            color: #fff;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .balance-text h6 {
            font-size: 0.85rem;
            font-weight: 700;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .balance-text h4 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 0;
        }

        .status-badge-premium {
            background: #dcfce7;
            color: #10b981;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        /* Amount Section */
        .premium-amount-box {
            background: #f8fafc;
            border: 2px solid #edf2f7;
            border-radius: 18px;
            padding: 30px;
            margin-bottom: 35px;
            transition: all 0.3s;
        }

        .premium-amount-box:focus-within {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.08);
        }

        .amount-input-flex {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .currency-symbol-premium {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1a202c;
        }

        .amount-field-premium {
            width: 100%;
            border: none;
            background: transparent;
            font-size: 3rem;
            font-weight: 800;
            color: #1a202c;
            outline: none;
            padding: 0;
        }

        .quick-amounts-flex {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-quick-val {
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #4a5568;
            font-weight: 700;
            padding: 8px 18px;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-quick-val:hover, .btn-quick-val.active {
            border-color: #3b82f6;
            color: #3b82f6;
            background: #f0f7ff;
        }

        /* Form Components */
        .section-header-premium {
            font-size: 1.1rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-header-premium .icon-box {
            width: 36px;
            height: 36px;
            background: #eff6ff;
            color: #3b82f6;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .form-group-premium {
            margin-bottom: 25px;
        }

        .label-premium {
            font-size: 0.9rem;
            font-weight: 800;
            color: #4a5568;
            margin-bottom: 10px;
            display: block;
        }

        .input-wrapper-premium {
            position: relative;
        }

        .input-wrapper-premium i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 1.1rem;
        }

        .input-premium {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 14px;
            padding: 14px 20px 14px 50px;
            font-size: 1rem;
            font-weight: 600;
            color: #1a202c;
            transition: all 0.2s;
        }

        .input-premium:focus {
            background: #fff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .input-premium.readonly-bg {
            background: #f1f5f9;
            color: #64748b;
        }

        .textarea-premium {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 14px;
            padding: 15px 20px;
            font-size: 1rem;
            font-weight: 600;
            color: #1a202c;
            transition: all 0.2s;
            resize: none;
        }

        .textarea-premium:focus {
            background: #fff;
            border-color: #3b82f6;
            outline: none;
        }

        .select-premium {
            padding-left: 50px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23a0aec0'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='C19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 18px center;
            background-size: 18px;
        }

        .btn-submit-premium {
            width: 100%;
            background: var(--primary-gradient);
            color: #fff;
            border: none;
            padding: 20px;
            border-radius: 16px;
            font-weight: 800;
            font-size: 1.1rem;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .btn-submit-premium:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.4);
        }

        .btn-cancel-premium {
            width: 100%;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #4a5568;
            padding: 18px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-cancel-premium:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .secure-badge-premium {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 16px;
            padding: 20px 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 30px;
        }

        .secure-badge-premium i {
            font-size: 1.5rem;
            color: #10b981;
        }

        .secure-badge-premium h6 {
            font-size: 0.95rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 4px;
        }

        .secure-badge-premium p {
            font-size: 0.85rem;
            color: #718096;
            margin-bottom: 0;
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
            <a href="local.php" class="nav-item-link active"><i class="fa-solid fa-paper-plane"></i> Local Transfer</a>
            <a href="international.php" class="nav-item-link"><i class="fa-solid fa-globe"></i> International Wire</a>
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
                    <h1 class="page-title">Local Transfer</h1>
                    <div class="breadcrumb-text">
                        <a href="index.php">Dashboard</a> <i class="fa-solid fa-chevron-right mx-2" style="font-size: 0.7rem;"></i> Local Transfer
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-11">
                    
                    <div class="transfer-hero-premium shadow-sm">
                        <div class="transfer-hero-icon-box"><i class="fa-solid fa-paper-plane"></i></div>
                        <h4>Domestic Bank Transfer</h4>
                        <p>Move funds to any local institution with zero transaction fees and instant processing.</p>
                    </div>

                    <div class="transfer-body-premium shadow-sm">
                        
                        <div class="balance-info-premium">
                            <div class="balance-meta">
                                <div class="balance-icon-circle"><i class="fa-solid fa-wallet"></i></div>
                                <div class="balance-text">
                                    <h6>Available Account Balance</h6>
                                    <h4>$0.00</h4>
                                </div>
                            </div>
                            <div class="status-badge-premium">Active</div>
                        </div>

                        <div class="premium-amount-box">
                            <span class="label-premium">Amount to Transfer (USD)</span>
                            <div class="amount-input-flex">
                                <span class="currency-symbol-premium">$</span>
                                <input type="number" class="amount-field-premium" placeholder="0.00" id="transferAmount" step="0.01">
                            </div>
                            <div class="quick-amounts-flex">
                                <button type="button" class="btn-quick-val" onclick="setAmount(100)">$100.00</button>
                                <button type="button" class="btn-quick-val" onclick="setAmount(500)">$500.00</button>
                                <button type="button" class="btn-quick-val" onclick="setAmount(1000)">$1,000.00</button>
                                <button type="button" class="btn-quick-val" onclick="setAmount(0)">Max Balance</button>
                            </div>
                        </div>

                        <div class="section-header-premium">
                            <div class="icon-box"><i class="fa-solid fa-user-check"></i></div> 
                            Beneficiary Information
                        </div>

                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="form-group-premium">
                                    <label class="label-premium">Beneficiary Account Name</label>
                                    <div class="input-wrapper-premium">
                                        <i class="fa-solid fa-user"></i>
                                        <input type="text" class="input-premium readonly-bg" value="Kante213" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-premium">
                                    <label class="label-premium">Beneficiary Account Number</label>
                                    <div class="input-wrapper-premium">
                                        <i class="fa-solid fa-hashtag"></i>
                                        <input type="text" class="input-premium" placeholder="Enter recipient's account number">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-premium">
                                    <label class="label-premium">Receiving Bank Name</label>
                                    <div class="input-wrapper-premium">
                                        <i class="fa-solid fa-building-columns"></i>
                                        <input type="text" class="input-premium" placeholder="Enter bank name">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-premium">
                                    <label class="label-premium">Transfer Method</label>
                                    <div class="input-wrapper-premium">
                                        <i class="fa-solid fa-credit-card"></i>
                                        <select class="input-premium select-premium">
                                            <option>Online Internal Banking</option>
                                            <option>Standard Wire Transfer</option>
                                            <option>Instant ACH Transfer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-header-premium">
                            <div class="icon-box"><i class="fa-solid fa-shield-keyhole"></i></div> 
                            Transaction Authentication
                        </div>

                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-group-premium">
                                    <label class="label-premium">Transaction Memo (Optional)</label>
                                    <textarea class="textarea-premium" rows="3" placeholder="Enter purpose of transfer for your records"></textarea>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group-premium">
                                    <label class="label-premium">Transaction PIN</label>
                                    <div class="input-wrapper-premium">
                                        <i class="fa-solid fa-lock"></i>
                                        <input type="password" class="input-premium" value="123456" id="txPin">
                                        <i class="fa-solid fa-eye" style="left: auto; right: 18px; cursor: pointer;" onclick="togglePin()"></i>
                                    </div>
                                    <small class="text-muted mt-2 d-block" style="font-weight: 600; font-size: 0.8rem;">Required to authorize and encrypt this transaction.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-4">
                            <div class="col-md-8">
                                <button type="button" class="btn-submit-premium">
                                    <i class="fa-solid fa-paper-plane-top"></i> Finalize & Send Transfer
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn-cancel-premium" onclick="location.href='index.php'">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="secure-badge-premium">
                        <i class="fa-solid fa-shield-check"></i>
                        <div>
                            <h6>SwiftCapital Secured Ecosystem</h6>
                            <p>This transaction is protected by end-to-end 256-bit AES encryption. Your financial data remains private and secure.</p>
                        </div>
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
            document.getElementById('transferAmount').value = val.toFixed(2);
            // Highlight the quick amount button
            document.querySelectorAll('.btn-quick-val').forEach(btn => btn.classList.remove('active'));
            if(event.currentTarget.classList.contains('btn-quick-val')) {
                event.currentTarget.classList.add('active');
            }
        }

        function togglePin() {
            const input = document.getElementById('txPin');
            const icon = event.currentTarget;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>

