<?php require_once '../includes/user-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cryptocurrency Deposit - SwiftCapital</title>
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
            color: #f97316;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .amount-badge-premium {
            background: #3b82f6;
            color: #fff;
            padding: 8px 18px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        .instruction-box {
            background: #f0f7ff;
            border-radius: 12px;
            border: 1px solid #e0efff;
            padding: 20px 25px;
            margin: 30px;
            display: flex;
            gap: 18px;
        }

        .instruction-box i {
            color: #3b82f6;
            font-size: 1.3rem;
            margin-top: 2px;
        }

        .instruction-content h6 {
            color: #1e40af;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .instruction-content p {
            color: #1e40af;
            font-size: 0.9rem;
            margin-bottom: 0;
            line-height: 1.6;
            opacity: 0.9;
        }

        .details-grid {
            padding: 0 30px 30px;
        }

        .qr-section {
            background: #f8fafc;
            border-radius: 20px;
            border: 1px solid #edf2f7;
            padding: 40px;
            text-align: center;
            margin-bottom: 30px;
        }

        .qr-header-icon {
            width: 50px;
            height: 50px;
            background: #dbeafe;
            color: #3b82f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.2rem;
        }

        .qr-code-wrapper {
            background: #fff;
            padding: 20px;
            border-radius: 16px;
            display: inline-block;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .qr-label {
            font-weight: 800;
            font-size: 1.25rem;
            color: #1a202c;
            margin-bottom: 10px;
        }

        .qr-subtext {
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .qr-details {
            font-size: 0.85rem;
            color: #4a5568;
            line-height: 1.6;
        }

        .qr-details b {
            color: #1a202c;
            display: block;
            font-family: monospace;
            word-break: break-all;
        }

        .crypto-address-field {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
        }

        .crypto-address-field:hover {
            border-color: #cbd5e0;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        .info-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0;
            font-family: monospace;
            letter-spacing: 0.5px;
        }

        .copy-trigger {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #a0aec0;
            cursor: pointer;
            transition: all 0.2s;
        }

        .copy-trigger:hover {
            color: #3b82f6;
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .network-info {
            font-size: 0.85rem;
            color: #718096;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
        }

        .upload-zone-premium {
            margin: 0 30px 30px;
        }

        .dropzone-box {
            border: 2px dashed #e2e8f0;
            border-radius: 16px;
            padding: 40px 20px;
            text-align: center;
            background: #fcfcfc;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dropzone-box:hover {
            border-color: #3b82f6;
            background: #f0f7ff;
            transform: translateY(-2px);
        }

        .dropzone-box i {
            font-size: 3rem;
            color: #cbd5e0;
            margin-bottom: 20px;
            transition: color 0.3s;
        }

        .dropzone-box:hover i {
            color: #3b82f6;
        }

        .dropzone-box p {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 5px;
        }

        .dropzone-box span {
            font-size: 0.8rem;
            color: #a0aec0;
        }

        .btn-submit-premium {
            width: 100%;
            background: #002d62;
            background: var(--primary-gradient, #002d62);
            color: #ffffff !important;
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
            margin-top: 25px;
        }

        .btn-submit-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.35);
            filter: brightness(1.05);
        }
    </style>
</head>
<body>

<?php 
$page = 'deposit';
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
                    <h1 class="page-title">Make Deposit</h1>
                    <div class="breadcrumb-text">
                        <a href="index.php">Dashboard</a> <i class="fa-solid fa-chevron-right mx-2" style="font-size: 0.7rem;"></i> Deposits <i class="fa-solid fa-chevron-right mx-2" style="font-size: 0.7rem;"></i> Make Payment
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    
                    <div class="payment-card-premium border-0 shadow-lg">
                        <div class="payment-card-header">
                            <div class="method-info">
                                <div class="method-icon-box">
                                    <i class="fa-brands fa-bitcoin"></i>
                                </div>
                                Payment Method: Bitcoin
                            </div>
                            <div class="amount-badge-premium">
                                Amount: $<?php echo number_format($_GET['amount'] ?? 0, 2); ?> USD
                            </div>
                        </div>

                        <!-- Instruction -->
                        <div class="instruction-box">
                            <i class="fa-solid fa-circle-info"></i>
                            <div class="instruction-content">
                                <h6>Crypto Deposit Instructions</h6>
                                <p>You are to make payment of <strong>$<?php echo number_format($_GET['amount'] ?? 0, 2); ?></strong> using your selected payment method. Screenshot and upload the proof of payment.</p>
                            </div>
                        </div>

                        <!-- QR Section -->
                        <div class="details-grid">
                            <div class="qr-section">
                                <div class="qr-header-icon">
                                    <i class="fa-solid fa-qrcode"></i>
                                </div>
                                <div class="qr-label">Scan QR Code</div>
                                <div class="qr-code-wrapper">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=bc1qcmglmujnhepnuy2f3u3f7l7qzujkr82shtvf4j&bgcolor=fff" alt="QR Code" id="qrCodeImg">
                                </div>
                                <div class="qr-subtext">Scan the QR code with your payment app</div>
                                <div class="qr-details">
                                    Bitcoin Address:
                                    <b id="qrAddr">bc1qcmglmujnhepnuy2f3u3f7l7qzujkr82shtvf4j</b>
                                    Amount:
                                    <b>$<?php echo number_format($_GET['amount'] ?? 0, 2); ?></b>
                                </div>
                            </div>

                            <!-- Address Field -->
                            <div class="mb-5">
                                <div class="info-label">
                                    <i class="fa-brands fa-bitcoin"></i> Bitcoin Address
                                </div>
                                <div class="crypto-address-field">
                                    <div class="info-value" id="btcAddr">bc1qcmglmujnhepnuy2f3u3f7l7qzujkr82shtvf4j</div>
                                    <div class="copy-trigger" onclick="copyValue('btcAddr')">
                                        <i class="fa-regular fa-copy"></i>
                                    </div>
                                </div>
                                <div class="network-info">
                                    <i class="fa-solid fa-circle-info"></i> Network Type: Bitcoin
                                </div>
                            </div>
                        </div>

                        <!-- Upload Section -->
                        <form action="process-deposit.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="amount" value="<?php echo htmlspecialchars($_GET['amount'] ?? 0); ?>">
                            <input type="hidden" name="method" value="Crypto (Bitcoin)">
                            
                            <div class="upload-zone-premium border-top pt-5">
                                <div class="details-title mb-4 fw-800" style="color: #2d3748; font-size: 1.1rem;">
                                    <i class="fa-solid fa-cloud-arrow-up me-2 text-primary"></i> Upload Payment Proof
                                </div>
                                <div class="dropzone-box" id="dropzone" onclick="document.getElementById('fileInput').click()">
                                    <i class="fa-solid fa-receipt"></i>
                                    <p>Click to upload or drag and drop</p>
                                    <span>PNG, JPG or PDF (max. 10MB)</span>
                                    <input type="file" name="proof" id="fileInput" class="d-none" accept="image/*,.pdf" required>
                                    <div id="fileNameDisplay" class="mt-3 fw-bold text-primary"></div>
                                </div>

                                <button type="submit" class="btn-submit-premium">
                                    <i class="fa-solid fa-check-circle me-2"></i> Submit Payment for Verification
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted fs-7"><i class="fa-solid fa-lock me-1"></i> Transactions are secured with end-to-end blockchain encryption.</p>
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

        function copyValue(id) {
            const text = document.getElementById(id).innerText;
            navigator.clipboard.writeText(text).then(() => {
                const trigger = event.currentTarget;
                const icon = trigger.querySelector('i');
                icon.classList.replace('fa-regular', 'fa-solid');
                icon.classList.replace('fa-copy', 'fa-check');
                icon.style.color = '#10b981';
                
                setTimeout(() => {
                    icon.classList.replace('fa-solid', 'fa-regular');
                    icon.classList.replace('fa-check', 'fa-copy');
                    icon.style.color = '';
                }, 2000);
            });
        }

        // Dropzone interaction
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('fileInput');
        const fileNameDisplay = document.getElementById('fileNameDisplay');

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.style.borderColor = '#3b82f6';
            dropzone.style.background = '#f0f7ff';
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.style.borderColor = '#e2e8f0';
            dropzone.style.background = '#fcfcfc';
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.style.borderColor = '#e2e8f0';
            dropzone.style.background = '#fcfcfc';
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                fileNameDisplay.textContent = 'Selected: ' + e.dataTransfer.files[0].name;
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                fileNameDisplay.textContent = 'Selected: ' + fileInput.files[0].name;
            }
        });
    </script>
</body>
</html>
