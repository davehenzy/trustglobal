<?php require_once '../includes/user-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Application - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php 
$page = 'loan';
include '../includes/user-sidebar.php'; 
?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <?php include '../includes/user-navbar.php'; ?>

        <!-- Page Content -->
        <div class="page-container">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-8">
                    <div class="page-header-centered">
                        <div class="header-icon-circle">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <h1 class="page-title-centered">Loan Application Form</h1>
                        <p class="page-subtitle-centered">Please complete the form below with accurate information to apply for your preferred loan product.</p>
                    </div>

                    <!-- Application Form -->
                    <div class="card card-premium rounded-top-0 border-0 border-top shadow-sm mb-4">
                        <div class="card-body p-4 p-md-5">
                            <form action="process-loan.php" method="POST">
                                
                                <!-- Header / Nav inside form -->
                                <div class="d-flex justify-content-between align-items-center mb-5 pb-3 border-bottom">
                                    <a href="loan.php" class="text-muted text-decoration-none fw-semibold fs-7"><i class="fa-solid fa-arrow-left me-2"></i> Back to Information</a>
                                    <span class="text-danger fs-8 fw-semibold">* Required fields</span>
                                </div>

                                <!-- 1. Loan Details -->
                                <div class="mb-5">
                                    <h5 class="fw-bold mb-4 d-flex align-items-center text-dark">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:28px; height:28px; font-size:0.8rem;">
                                            <i class="fa-solid fa-list-ul"></i>
                                        </div>
                                        Loan Details
                                    </h5>
                                    
                                    <!-- Inside form fields container -->
                                    <div class="bg-light bg-opacity-50 p-4 rounded-3 border">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label">Loan Amount (USD) <span class="req">*</span></label>
                                                <div class="custom-input-group">
                                                    <input type="number" name="amount" placeholder="Enter loan amount" required step="100">
                                                    <i class="fa-solid fa-dollar-sign left-icon"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Duration (Months) <span class="req">*</span></label>
                                                <div class="custom-input-group">
                                                    <select name="duration" required>
                                                        <option value="" selected disabled>Select duration</option>
                                                        <option value="6">6 Months</option>
                                                        <option value="12">12 Months</option>
                                                        <option value="24">24 Months</option>
                                                        <option value="36">36 Months</option>
                                                        <option value="48">48 Months</option>
                                                        <option value="60">60 Months</option>
                                                    </select>
                                                    <i class="fa-solid fa-calendar-days left-icon"></i>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <label class="form-label">Credit Facility <span class="req">*</span></label>
                                                <div class="custom-input-group">
                                                    <select name="loan_type" required>
                                                        <option value="" selected disabled>Select Loan/Credit Facility</option>
                                                        <option value="Personal Home Loan">Personal Home Loans</option>
                                                        <option value="Automobile Loan">Automobile Loans</option>
                                                        <option value="Business Loan">Business Loans</option>
                                                        <option value="Joint Mortgage">Joint Mortgage</option>
                                                        <option value="Secured Overdraft">Secured Overdraft</option>
                                                        <option value="Health Finance">Health Finance</option>
                                                    </select>
                                                    <i class="fa-solid fa-building-columns left-icon"></i>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <label class="form-label">Purpose of Loan <span class="req">*</span></label>
                                                <div class="custom-input-group">
                                                    <textarea name="purpose" rows="4" placeholder="Please describe the purpose of this loan..." required></textarea>
                                                    <i class="fa-solid fa-message left-icon" style="top: 25px;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. Financial Information -->
                                <div class="mb-5">
                                    <h5 class="fw-bold mb-4 d-flex align-items-center text-dark">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:28px; height:28px; font-size:0.8rem;">
                                            <i class="fa-solid fa-wallet"></i>
                                        </div>
                                        Financial Information
                                    </h5>
                                    
                                    <div class="bg-light bg-opacity-50 p-4 rounded-3 border">
                                        <div class="row g-4">
                                            <div class="col-12">
                                                <label class="form-label">Monthly Net Income <span class="req">*</span></label>
                                                <div class="custom-input-group">
                                                    <select name="income" required>
                                                        <option value="" selected disabled>Select income range</option>
                                                        <option value="Under $2,000">Under $2,000</option>
                                                        <option value="$2,000 - $5,000">$2,000 - $5,000</option>
                                                        <option value="$5,000 - $10,000">$5,000 - $10,000</option>
                                                        <option value="Over $10,000">Over $10,000</option>
                                                    </select>
                                                    <i class="fa-solid fa-dollar-sign left-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Terms -->
                                <div class="mb-5 bg-light bg-opacity-50 p-4 rounded-3 border">
                                    <div class="form-check d-flex gap-3 align-items-start">
                                        <input class="form-check-input mt-1" type="checkbox" id="termsCheck" required style="width: 1.25em; height: 1.25em;">
                                        <label class="form-check-label text-dark" for="termsCheck">
                                            <span class="fw-bold d-block mb-1">I agree to the terms and conditions</span>
                                            <span class="text-muted fs-7">By submitting this application, I confirm that all information provided is accurate and complete. I authorize SwiftCapital to verify my information and credit history.</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                                    <button type="submit" name="submit_loan" class="btn btn-primary btn-lg flex-grow-1 fw-bold px-5">Submit Loan Application</button>
                                    <a href="loan.php" class="btn btn-outline-secondary btn-lg flex-shrink-0 fw-bold px-5"><i class="fa-solid fa-xmark me-2"></i> Cancel</a>
                                </div>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
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
        // Set dynamic times
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
