<?php require_once '../includes/user-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Deposit - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .page-header {
            margin-bottom: 25px;
        }
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .breadcrumb-text {
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        .payment-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }
        .payment-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .payment-method-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: var(--text-dark);
        }
        .payment-amount-badge {
            background: #e0f2fe;
            color: #0369a1;
            padding: 6px 15px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .instruction-alert {
            background: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            padding: 15px 20px;
            margin: 25px;
            display: flex;
            gap: 15px;
        }
        .instruction-alert i {
            color: #3b82f6;
            font-size: 1.2rem;
            margin-top: 3px;
        }
        .instruction-alert p {
            margin-bottom: 0;
            font-size: 0.85rem;
            color: #1e3a8a;
            line-height: 1.5;
        }
        
        .bank-details-section {
            padding: 0 25px 25px;
        }
        .section-subtitle {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .details-grid {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .detail-item {
            margin-bottom: 20px;
        }
        .detail-item:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
            display: block;
        }

        .detail-input-group {
            display: flex;
            gap: 10px;
            position: relative;
        }

        .detail-field {
            flex: 1;
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 0.95rem;
            color: var(--text-dark);
            font-weight: 500;
            outline: none;
        }

        .copy-btn {
            background: #fff;
            border: 1px solid #e2e8f0;
            width: 45px;
            height: 45px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            transition: all 0.2s;
            cursor: pointer;
        }

        .copy-btn:hover {
            color: var(--primary-color);
            background: #eff6ff;
            border-color: #bfdbfe;
        }
        
        .upload-section {
            padding: 0 25px 25px;
        }
        .dropzone {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .dropzone:hover {
            border-color: var(--primary-color);
            background: #f8fafc;
        }
        .dropzone i {
            font-size: 2.5rem;
            color: #94a3b8;
            margin-bottom: 15px;
        }
        .dropzone p {
            margin-bottom: 5px;
            font-size: 0.9rem;
            color: var(--text-dark);
        }
        .dropzone span {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        .submit-btn {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            font-weight: 700;
            margin-top: 20px;
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
                <div class="col-lg-10">
                    
                    <div class="payment-card">
                        <div class="payment-header">
                            <div class="payment-method-badge">
                                <i class="fa-solid fa-building-columns text-primary"></i> Payment Method: Bank Transfer
                            </div>
                            <div class="payment-amount-badge">
                                Amount: $<?php echo number_format($_GET['amount'] ?? 0, 2); ?> USD
                            </div>
                        </div>

                        <!-- Instruction -->
                        <div class="instruction-alert">
                            <i class="fa-solid fa-circle-info"></i>
                            <div>
                                <p class="fw-bold mb-1">Payment Instructions</p>
                                <p>You are to make payment of <strong>$<?php echo number_format($_GET['amount'] ?? 0, 2); ?></strong> using your selected payment method. Screenshot and upload the proof of payment.</p>
                            </div>
                        </div>

                        <!-- Bank Details -->
                        <div class="bank-details-section">
                            <h5 class="section-subtitle">Bank Transfer Details</h5>
                            
                            <div class="details-grid">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">Bank Name</label>
                                            <div class="detail-input-group">
                                                <input type="text" class="detail-field" value="Mining Bank" id="bankName" readonly>
                                                <button class="copy-btn" onclick="copyValue('bankName')">
                                                    <i class="fa-regular fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">Account Name</label>
                                            <div class="detail-input-group">
                                                <input type="text" class="detail-field" value="Miller lauren" id="accName" readonly>
                                                <button class="copy-btn" onclick="copyValue('accName')">
                                                    <i class="fa-regular fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">Account Number</label>
                                            <div class="detail-input-group">
                                                <input type="text" class="detail-field" value="99388383" id="accNum" readonly>
                                                <button class="copy-btn" onclick="copyValue('accNum')">
                                                    <i class="fa-regular fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">Swift Code</label>
                                            <div class="detail-input-group">
                                                <input type="text" class="detail-field" value="3222ASD" id="swiftCode" readonly>
                                                <button class="copy-btn" onclick="copyValue('swiftCode')">
                                                    <i class="fa-regular fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Proof -->
                        <form action="process-deposit.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="amount" value="<?php echo htmlspecialchars($_GET['amount'] ?? 0); ?>">
                            <input type="hidden" name="method" value="Bank Transfer">
                            
                            <div class="upload-section">
                                <label class="fw-bold mb-2">Upload Payment Proof</label>
                                <div class="dropzone" id="dropzone" onclick="document.getElementById('fileInput').click()">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                    <p><span class="text-primary fw-bold">Click to upload</span> or drag and drop</p>
                                    <span>PNG, JPG or PDF (max. 5MB)</span>
                                    <input type="file" name="proof" id="fileInput" class="d-none" accept="image/*,.pdf" required>
                                </div>

                                <button type="submit" class="btn btn-primary submit-btn">
                                    <i class="fa-solid fa-check-circle me-2"></i> Submit Payment
                                </button>
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

        function copyValue(id) {
            const field = document.getElementById(id);
            field.select();
            document.execCommand('copy');
            
            // Visual feedback
            const btn = field.nextElementSibling;
            const icon = btn.querySelector('i');
            icon.classList.replace('fa-regular', 'fa-solid');
            icon.classList.replace('fa-copy', 'fa-check');
            btn.style.borderColor = '#22c55e';
            btn.style.color = '#22c55e';
            
            setTimeout(() => {
                icon.classList.replace('fa-solid', 'fa-regular');
                icon.classList.replace('fa-check', 'fa-copy');
                btn.style.borderColor = '#e2e8f0';
                btn.style.color = '#94a3b8';
            }, 2000);
        }

        // Dropzone interaction
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('fileInput');

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.style.borderColor = '#2563eb';
            dropzone.style.background = '#f8fafc';
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.style.borderColor = '#e2e8f0';
            dropzone.style.background = 'transparent';
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.style.borderColor = '#e2e8f0';
            dropzone.style.background = 'transparent';
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateDropzoneText(e.dataTransfer.files[0].name);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                updateDropzoneText(fileInput.files[0].name);
            }
        });

        function updateDropzoneText(fileName) {
            const p = dropzone.querySelector('p');
            p.innerHTML = `<span class="text-success fw-bold">File Selected:</span> ${fileName}`;
        }
    </script>
</body>
</html>
