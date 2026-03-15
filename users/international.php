<?php require_once '../includes/user-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>International Transfer - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        /* Transfer Method Cards */
        .transfer-method-card {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 30px 25px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }
        
        .transfer-method-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary-gradient);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .transfer-method-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.08);
            transform: translateY(-5px);
        }
        
        .transfer-method-card:hover::before {
            opacity: 1;
        }

        .transfer-icon-wrapper {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .method-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            margin-right: 15px;
            font-size: 1.4rem;
            transition: transform 0.3s;
        }
        
        .transfer-method-card:hover .method-icon {
            transform: scale(1.1);
        }

        .method-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1a202c;
            margin: 0;
        }

        .method-desc {
            font-size: 0.9rem;
            color: #718096;
            line-height: 1.6;
            margin-bottom: 0;
        }
        
        /* Icon Colors */
        .icon-wire { background: #eff6ff; color: #2563eb; }
        .icon-crypto { background: #fff7ed; color: #f59e0b; }
        .icon-paypal { background: #eef2ff; color: #4338ca; }
        .icon-wise { background: #f0fdf4; color: #16a34a; }
        .icon-cashapp { background: #f8fafc; color: #1a202c; }
        .icon-more { background: #fffbeb; color: #d97706; }
        
        .icon-skrill { background: #fdf2f8; color: #db2777; }
        .icon-venmo { background: #eff6ff; color: #3b82f6; }
        .icon-zelle { background: #f5f3ff; color: #7c3aed; }
        .icon-revolut { background: #f8fafc; color: #0f172a; }
        .icon-alipay { background: #f0f9ff; color: #0ea5e9; }
        .icon-wechat { background: #f0fdf4; color: #22c55e; }

        .secure-alert-premium {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 16px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 30px;
        }
        
        .secure-alert-premium i {
            color: #10b981;
            font-size: 1.5rem;
        }
        
        .secure-alert-premium h6 {
            margin-bottom: 4px;
            font-weight: 700;
            color: #1a202c;
        }
        
        .secure-alert-premium p {
            margin-bottom: 0;
            font-size: 0.9rem;
            color: #718096;
        }

        .section-header-with-back {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 2rem;
        }
        
        .btn-back-section {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            color: #4a5568;
        }
        
        .btn-back-section:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateX(-3px);
        }

        .hidden {
            display: none !important;
        }

        .fade-in {
            animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .method-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
    </style>
</head>
<body>

<?php 
$page = 'international';
include '../includes/user-sidebar.php'; 
?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <?php include '../includes/user-navbar.php'; ?>

        <!-- Page Content -->
        <div class="page-container">
            
            <div class="page-header">
                <div>
                    <h1 class="page-title">International Transfer</h1>
                    <div class="breadcrumb-text">
                        <a href="index.php">Dashboard</a> <i class="fa-solid fa-chevron-right mx-2" style="font-size: 0.7rem;"></i> International Transfer
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mt-4">
                <div class="col-lg-11">
                    
                    <!-- Main Selection Section -->
                    <div id="mainMethodsSection" class="fade-in">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Select Transfer Method</h5>
                            <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">12 Methods Available</span>
                        </div>
                        
                        <div class="method-grid">
                            <!-- Wire Transfer -->
                            <div class="transfer-method-card" onclick="location.href='international-wire.php'">
                                <div class="transfer-icon-wrapper">
                                    <div class="method-icon icon-wire"><i class="fa-solid fa-building-columns"></i></div>
                                    <h6 class="method-title">Wire Transfer</h6>
                                </div>
                                <p class="method-desc">Traditional bank-to-bank transfer. Secure and reliable for large amounts.</p>
                            </div>
                            
                            <!-- Cryptocurrency -->
                            <div class="transfer-method-card" onclick="location.href='international-crypto.php'">
                                <div class="transfer-icon-wrapper">
                                    <div class="method-icon icon-crypto"><i class="fa-brands fa-bitcoin"></i></div>
                                    <h6 class="method-title">Cryptocurrency</h6>
                                </div>
                                <p class="method-desc">Instant borderless transfers using BTC, ETH, USDT, or BNB.</p>
                            </div>

                            <!-- PayPal -->
                            <div class="transfer-method-card" onclick="location.href='international-paypal.php'">
                                <div class="transfer-icon-wrapper">
                                    <div class="method-icon icon-paypal"><i class="fa-brands fa-paypal"></i></div>
                                    <h6 class="method-title">PayPal</h6>
                                </div>
                                <p class="method-desc">Reliable transfers to any PayPal account Capitally.</p>
                            </div>

                            <!-- Wise -->
                            <div class="transfer-method-card" onclick="location.href='international-wise.php'">
                                <div class="transfer-icon-wrapper">
                                    <div class="method-icon icon-wise"><i class="fa-solid fa-bolt"></i></div>
                                    <h6 class="method-title">Wise (TransferWise)</h6>
                                </div>
                                <p class="method-desc">Low-cost international transfers with mid-market exchange rates.</p>
                            </div>

                            <!-- Cash App -->
                            <div class="transfer-method-card" onclick="location.href='international-cashapp.php'">
                                <div class="transfer-icon-wrapper">
                                    <div class="method-icon icon-cashapp"><i class="fa-solid fa-dollar-sign"></i></div>
                                    <h6 class="method-title">Cash App</h6>
                                </div>
                                <p class="method-desc">Rapid transfers to $Cashtags. Primarily for US and UK users.</p>
                            </div>

                            <!-- More -->
                            <div class="transfer-method-card" onclick="toggleMethods()">
                                <div class="transfer-icon-wrapper">
                                    <div class="method-icon icon-more"><i class="fa-solid fa-grid-2"></i></div>
                                    <h6 class="method-title">More Methods</h6>
                                </div>
                                <p class="method-desc">Zelle, Venmo, Revolut, Alipay, WeChat, and Skrill.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Methods Section -->
                    <div id="additionalMethodsSection" class="hidden fade-in">
                        <div class="section-header-with-back">
                            <button class="btn-back-section" onclick="toggleMethods()">
                                <i class="fa-solid fa-arrow-left"></i>
                            </button>
                            <h5 class="fw-bold mb-0">E-Wallets & Mobile Payments</h5>
                        </div>
                        
                        <div class="method-grid">
                            <!-- Skrill -->
                            <div class="transfer-method-card" onclick="location.href='international-skrill.php'">
                                <div class="transfer-icon-wrapper">
                                    <div class="method-icon icon-skrill"><i class="fa-solid fa-wallet"></i></div>
                                    <h6 class="method-title">Skrill</h6>
                                </div>
                                <p class="method-desc">Popular digital wallet for international money transfers.</p>
                            </div>

                            <!-- Venmo -->
                            <div class="transfer-method-card" onclick="location.href='international-venmo.php'">
                                <div class="transfer-icon-wrapper">
                                    <div class="method-icon icon-venmo"><i class="fa-solid fa-v"></i></div>
                                    <h6 class="method-title">Venmo</h6>
                                </div>
                                <p class="method-desc">Social payment platform for quick peer-to-peer transfers.</p>
                            </div>

                            <!-- Zelle -->
                            <div class="transfer-method-card" onclick="location.href='international-zelle.php'">
                                <div class="transfer-icon-wrapper">
                                    <div class="method-icon icon-zelle"><i class="fa-solid fa-bolt-lightning"></i></div>
                                    <h6 class="method-title">Zelle</h6>
                                </div>
                                <p class="method-desc">Direct bank-to-bank transfers within the United States.</p>
                            </div>

                            <!-- Revolut -->
                            <div class="transfer-method-card" onclick="location.href='international-revolut.php'">
                                <div class="transfer-icon-wrapper">
                                    <div class="method-icon icon-revolut"><i class="fa-solid fa-rotate"></i></div>
                                    <h6 class="method-title">Revolut</h6>
                                </div>
                                <p class="method-desc">Modern banking app for seamless Capital transfers.</p>
                            </div>

                            <!-- Alipay -->
                            <div class="transfer-method-card" onclick="location.href='international-alipay.php'">
                                <div class="transfer-icon-wrapper">
                                    <div class="method-icon icon-alipay"><i class="fa-brands fa-alipay"></i></div>
                                    <h6 class="method-title">Alipay</h6>
                                </div>
                                <p class="method-desc">Leading Chinese mobile payment platform for Capital use.</p>
                            </div>

                            <!-- WeChat Pay -->
                            <div class="transfer-method-card" onclick="location.href='international-wechat.php'">
                                <div class="transfer-icon-wrapper">
                                    <div class="method-icon icon-wechat"><i class="fa-brands fa-weixin"></i></div>
                                    <h6 class="method-title">WeChat Pay</h6>
                                </div>
                                <p class="method-desc">Integrated payment system within the WeChat ecosystem.</p>
                            </div>
                        </div>
                    </div>

                    <div class="secure-alert-premium">
                        <div class="d-flex align-items-center gap-3">
                            <i class="fa-solid fa-shield-check"></i>
                            <div>
                                <h6>Secured by SwiftCapital Engine</h6>
                                <p>Every international transfer undergoes multi-layer encryption and fraud monitoring to ensure your funds arrive safely.</p>
                            </div>
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

        function toggleMethods() {
            const main = document.getElementById('mainMethodsSection');
            const additional = document.getElementById('additionalMethodsSection');
            
            if (main.classList.contains('hidden')) {
                main.classList.remove('hidden');
                additional.classList.add('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                main.classList.add('hidden');
                additional.classList.remove('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    </script>
</body>
</html>

