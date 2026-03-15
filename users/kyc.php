<?php 
require_once '../includes/db.php';
require_once '../includes/user-check.php'; 

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Check if user already has a pending or verified KYC
$stmt = $pdo->prepare("SELECT * FROM kyc_verifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$existing_kyc = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_kyc'])) {
    if ($existing_kyc && $existing_kyc['status'] == 'Verified') {
        $error_msg = "Your identity has already been verified.";
    } elseif ($existing_kyc && $existing_kyc['status'] == 'Pending') {
        $error_msg = "Your verification is already in review. Please wait for our audit.";
    } else {
        $doc_type = $_POST['document_type'] ?? 'International Passport';
        $full_name = htmlspecialchars($_POST['full_name']);
        $dob = $_POST['dob'];
        $ssn = htmlspecialchars($_POST['ssn']);
        $acc_type = htmlspecialchars($_POST['account_type']);
        $employment = htmlspecialchars($_POST['employment']);
        $income = htmlspecialchars($_POST['income']);
        $address = htmlspecialchars($_POST['address']);
        $city = htmlspecialchars($_POST['city']);
        $state = htmlspecialchars($_POST['state']);
        $zip = htmlspecialchars($_POST['zip']);
        $country = htmlspecialchars($_POST['country']);
        $nok_name = htmlspecialchars($_POST['next_of_kin_name']);
        $nok_rel = htmlspecialchars($_POST['next_of_kin_relationship']);

        $upload_dir = '../uploads/kyc/';
        
        $front_name = '';
        $back_name = '';
        $selfie_name = '';

        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

        // Helper for uploads
        function uploadDoc($file, $dir, $allowed) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) return false;
            $new_name = uniqid('KYC_') . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $dir . $new_name)) {
                return $new_name;
            }
            return false;
        }

        if (isset($_FILES['front_id'])) $front_name = uploadDoc($_FILES['front_id'], $upload_dir, $allowed);
        if (isset($_FILES['back_id'])) $back_name = uploadDoc($_FILES['back_id'], $upload_dir, $allowed);
        if (isset($_FILES['selfie'])) $selfie_name = uploadDoc($_FILES['selfie'], $upload_dir, $allowed);

        if ($front_name && $selfie_name) {
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO kyc_verifications (user_id, full_name, dob, ssn, account_type, employment, income, address, city, state, zip, country, next_of_kin_name, next_of_kin_relationship, document_type, document_front, document_back, selfie, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
                $stmt->execute([$user_id, $full_name, $dob, $ssn, $acc_type, $employment, $income, $address, $city, $state, $zip, $country, $nok_name, $nok_rel, $doc_type, $front_name, $back_name, $selfie_name]);
                
                $pdo->prepare("UPDATE users SET kyc_status = 'Pending' WHERE id = ?")->execute([$user_id]);
                $pdo->commit();
                $success_msg = "Verification documents submitted successfully! Our compliance team will review them.";
                
                // Refresh status
                $existing_kyc = ['status' => 'Pending'];
            } catch (Exception $e) { $pdo->rollBack(); $error_msg = "Database Error: " . $e->getMessage(); }
        } else {
            $error_msg = "Please upload the required front ID and selfie documents.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KYC Verification - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .verification-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }
        
        .verification-card-header {
            padding: 25px 30px;
            background: #fff;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .verification-header-icon {
            width: 40px;
            height: 40px;
            background: #eff6ff;
            color: #3b82f6;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .verification-header-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
        }

        .verification-body {
            padding: 35px 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
        }
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--text-dark);
        }
        .breadcrumb-text {
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        .breadcrumb-text a {
            color: var(--text-muted);
            text-decoration: none;
        }
        .breadcrumb-text a:hover {
            color: var(--primary-color);
        }

        .upload-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        .upload-tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .upload-tab i {
            font-size: 1.5rem;
        }
        .upload-tab.active {
            border-color: #3b82f6;
            background: #eff6ff;
            color: #2563eb;
        }

        .checklist {
            margin-bottom: 25px;
            padding-left: 0;
            list-style: none;
        }
        .checklist li {
            font-size: 0.85rem;
            color: #475569;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        .checklist li i {
            color: #3b82f6;
            margin-right: 10px;
        }

        .upload-zone-wrapper {
            margin-bottom: 25px;
        }
        .upload-zone-title {
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #1e293b;
        }
        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .upload-zone:hover {
            border-color: #94a3b8;
            background: #f1f5f9;
        }
        .upload-zone i {
            font-size: 2rem;
            color: #94a3b8;
            margin-bottom: 10px;
        }
        .upload-zone p {
            margin-bottom: 5px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #1e293b;
        }
        .upload-zone small {
            color: #64748b;
            font-size: 0.75rem;
        }

        .btn-submit-kyc {
            width: 100%;
            background: #3b82f6;
            color: #fff;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s;
            margin-top: 10px;
        }
        .btn-submit-kyc:hover {
            background: #2563eb;
        }

        .help-footer-card {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .help-footer-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .help-footer-icon {
            width: 48px;
            height: 48px;
            background: #eff6ff;
            color: #3b82f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .help-footer-text h6 {
            font-weight: 700;
            margin-bottom: 5px;
            color: #1e293b;
        }

        .help-footer-text p {
            margin-bottom: 0;
            font-size: 0.85rem;
            color: #64748b;
        }

        .btn-support-outline {
            border: 1px solid #3b82f6;
            color: #3b82f6;
            background: transparent;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            transition: all 0.2s;
        }

        .btn-support-outline:hover {
            background: rgba(59, 130, 246, 0.05);
    </style>
</head>
<body>

<?php 
$page = 'kyc';
include '../includes/user-sidebar.php'; 
?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <?php include '../includes/user-navbar.php'; ?>

        <!-- Page Content -->
        <div class="page-container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <div class="page-header-centered">
                        <div class="header-icon-circle">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <h1 class="page-title-centered">KYC Verification</h1>
                        <p class="page-subtitle-centered">Complete your Know Your Customer (KYC) profile to unlock full account capabilities and ensure regulatory compliance.</p>
                    </div>

                    <?php if($success_msg): ?>
                        <div class="alert alert-success border-0 rounded-4 p-4 shadow-sm mb-4 fw-600">
                            <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success_msg; ?>
                        </div>
                    <?php endif; ?>

                    <?php if($error_msg): ?>
                        <div class="alert alert-danger border-0 rounded-4 p-4 shadow-sm mb-4 fw-600">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $error_msg; ?>
                        </div>
                    <?php endif; ?>

                    <?php if($existing_kyc && $existing_kyc['status'] == 'Rejected'): ?>
                        <div class="alert alert-danger border-0 rounded-4 p-4 shadow-sm mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; min-width: 50px;">
                                    <i class="fa-solid fa-circle-xmark fa-xl"></i>
                                </div>
                                <div>
                                    <h5 class="fw-800 mb-1">Verification Declined</h5>
                                    <p class="mb-0 fw-600 text-sm opacity-75">Reason: <strong class="text-dark"><?php echo htmlspecialchars($existing_kyc['rejection_reason']); ?></strong>. Please re-examine your documents and submit again.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($existing_kyc && ($existing_kyc['status'] == 'Verified' || $existing_kyc['status'] == 'Pending')): ?>
                        <div class="card border-0 rounded-4 shadow-sm p-5 text-center mb-5">
                            <div class="mb-4">
                                <?php if($existing_kyc['status'] == 'Verified'): ?>
                                    <div class="bg-success text-white d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px;">
                                        <i class="fa-solid fa-check fa-3x"></i>
                                    </div>
                                    <h3 class="fw-800">Identity Verified</h3>
                                    <p class="text-muted">Your account is now fully compliant. All financial limits have been lifted.</p>
                                    <a href="index.php" class="btn btn-primary px-5 py-3 fw-800 mt-3" style="border-radius: 12px;">Back to Dashboard</a>
                                <?php else: ?>
                                    <div class="bg-warning text-white d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px;">
                                        <i class="fa-solid fa-hourglass-half fa-3x"></i>
                                    </div>
                                    <h3 class="fw-800">Verification in Progress</h3>
                                    <p class="text-muted">We have received your documents. Our compliance team is currently auditing your submission. This usually takes 2-4 hours.</p>
                                    <div class="p-3 bg-light rounded-3 d-inline-block px-4 border">
                                        <span class="text-xs fw-800 text-muted text-uppercase">Audit Status: <strong class="text-warning">In Review</strong></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                    <div class="verification-card">
                        <div class="verification-card-header">
                            <div class="verification-header-icon">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            <div class="verification-header-title">Complete Your Profile</div>
                        </div>

                        <div class="verification-body">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="document_type" id="docTypeInput" value="International Passport">
                                <!-- Personal Information -->
                                <div class="section-header">
                                    <div class="section-header-title">
                                        <i class="fa-solid fa-user"></i> Personal Information
                                    </div>
                                    <p class="section-header-desc">Legal name as it appears on your ID</p>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name <span class="req">*</span></label>
                                        <div class="custom-input-group">
                                            <input type="text" name="full_name" placeholder="John Doe" required>
                                            <i class="fa-solid fa-user left-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Date of Birth <span class="req">*</span></label>
                                        <div class="custom-input-group">
                                            <input type="date" name="dob" required>
                                            <i class="fa-solid fa-calendar left-icon"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Employment Information -->
                                <div class="section-header mt-4">
                                    <div class="section-header-title">
                                        <i class="fa-solid fa-briefcase"></i> Employment Information
                                    </div>
                                    <p class="section-header-desc">Required for regulatory compliance</p>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">SSN / National ID Number <span class="req">*</span></label>
                                        <div class="custom-input-group">
                                            <input type="text" name="ssn" placeholder="XXX-XX-XXXX" required>
                                            <i class="fa-solid fa-shield-halved left-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Account Type <span class="req">*</span></label>
                                        <div class="custom-input-group">
                                            <select name="account_type" required>
                                                <option value="" disabled selected>Select Account Type</option>
                                                <option>Savings Account</option>
                                                <option>Checking Account</option>
                                                <option>Business Account</option>
                                                <option>Fixed Deposit Account</option>
                                                <option>Investment Account</option>
                                            </select>
                                            <i class="fa-solid fa-building-columns left-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Type of Employment <span class="req">*</span></label>
                                        <div class="custom-input-group">
                                            <select name="employment" required>
                                                <option value="" disabled selected>Select Employment Type</option>
                                                <option>Employed (Full-time)</option>
                                                <option>Employed (Part-time)</option>
                                                <option>Self-Employed / Business Owner</option>
                                                <option>Freelancer</option>
                                                <option>Contractor</option>
                                                <option>Unemployed</option>
                                                <option>Retired</option>
                                                <option>Student</option>
                                            </select>
                                            <i class="fa-solid fa-file-invoice left-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Annual Income Range <span class="req">*</span></label>
                                        <div class="custom-input-group">
                                            <select name="income" required>
                                                <option value="" disabled selected>Select Income Range</option>
                                                <option>$0 - $10,000</option>
                                                <option>$10,000 - $50,000</option>
                                                <option>$50,000 - $100,000</option>
                                                <option>$100,000 - $250,000</option>
                                                <option>$250,000 - $500,000</option>
                                                <option>$500,000+</option>
                                            </select>
                                            <i class="fa-solid fa-dollar-sign left-icon"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Your Address -->
                                <div class="section-header mt-4">
                                    <div class="section-header-title">
                                        <i class="fa-solid fa-location-dot"></i> Your Address
                                    </div>
                                    <p class="section-header-desc">Current residential address</p>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Home Address <span class="req">*</span></label>
                                        <div class="custom-input-group">
                                            <input type="text" name="address" placeholder="123 Main St, Apt 4" required>
                                            <i class="fa-solid fa-house left-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">City <span class="req">*</span></label>
                                        <div class="custom-input-group no-icon">
                                            <input type="text" name="city" placeholder="New York" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">State / Province <span class="req">*</span></label>
                                        <div class="custom-input-group no-icon">
                                            <input type="text" name="state" placeholder="NY" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Zip / Postal Code <span class="req">*</span></label>
                                        <div class="custom-input-group no-icon">
                                            <input type="text" name="zip" placeholder="10001" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Country <span class="req">*</span></label>
                                        <div class="custom-input-group">
                                            <select name="country" required>
                                                <option value="" disabled selected>Select Country</option>
                                                <option value="AF">Afghanistan</option>
                                                <option value="AX">Aland Islands</option>
                                                <option value="AL">Albania</option>
                                                <option value="DZ">Algeria</option>
                                                <option value="AS">American Samoa</option>
                                                <option value="AD">Andorra</option>
                                                <option value="AO">Angola</option>
                                                <option value="AI">Anguilla</option>
                                                <option value="AQ">Antarctica</option>
                                                <option value="AG">Antigua and Barbuda</option>
                                                <option value="AR">Argentina</option>
                                                <option value="AM">Armenia</option>
                                                <option value="AW">Aruba</option>
                                                <option value="AU">Australia</option>
                                                <option value="AT">Austria</option>
                                                <option value="AZ">Azerbaijan</option>
                                                <option value="BS">Bahamas</option>
                                                <option value="BH">Bahrain</option>
                                                <option value="BD">Bangladesh</option>
                                                <option value="BB">Barbados</option>
                                                <option value="BY">Belarus</option>
                                                <option value="BE">Belgium</option>
                                                <option value="BZ">Belize</option>
                                                <option value="BJ">Benin</option>
                                                <option value="BM">Bermuda</option>
                                                <option value="BT">Bhutan</option>
                                                <option value="BO">Bolivia</option>
                                                <option value="BQ">Bonaire, Sint Eustatius and Saba</option>
                                                <option value="BA">Bosnia and Herzegovina</option>
                                                <option value="BW">Botswana</option>
                                                <option value="BV">Bouvet Island</option>
                                                <option value="BR">Brazil</option>
                                                <option value="IO">British Indian Ocean Territory</option>
                                                <option value="BN">Brunei Darussalam</option>
                                                <option value="BG">Bulgaria</option>
                                                <option value="BF">Burkina Faso</option>
                                                <option value="BI">Burundi</option>
                                                <option value="KH">Cambodia</option>
                                                <option value="CM">Cameroon</option>
                                                <option value="CA">Canada</option>
                                                <option value="CV">Cape Verde</option>
                                                <option value="KY">Cayman Islands</option>
                                                <option value="CF">Central African Republic</option>
                                                <option value="TD">Chad</option>
                                                <option value="CL">Chile</option>
                                                <option value="CN">China</option>
                                                <option value="CX">Christmas Island</option>
                                                <option value="CC">Cocos (Keeling) Islands</option>
                                                <option value="CO">Colombia</option>
                                                <option value="KM">Comoros</option>
                                                <option value="CG">Congo</option>
                                                <option value="CD">Congo, Democratic Republic of the Congo</option>
                                                <option value="CK">Cook Islands</option>
                                                <option value="CR">Costa Rica</option>
                                                <option value="CI">Cote D'Ivoire</option>
                                                <option value="HR">Croatia</option>
                                                <option value="CU">Cuba</option>
                                                <option value="CW">Curacao</option>
                                                <option value="CY">Cyprus</option>
                                                <option value="CZ">Czech Republic</option>
                                                <option value="DK">Denmark</option>
                                                <option value="DJ">Djibouti</option>
                                                <option value="DM">Dominica</option>
                                                <option value="DO">Dominican Republic</option>
                                                <option value="EC">Ecuador</option>
                                                <option value="EG">Egypt</option>
                                                <option value="SV">El Salvador</option>
                                                <option value="GQ">Equatorial Guinea</option>
                                                <option value="ER">Eritrea</option>
                                                <option value="EE">Estonia</option>
                                                <option value="ET">Ethiopia</option>
                                                <option value="FK">Falkland Islands (Malvinas)</option>
                                                <option value="FO">Faroe Islands</option>
                                                <option value="FJ">Fiji</option>
                                                <option value="FI">Finland</option>
                                                <option value="FR">France</option>
                                                <option value="GF">French Guiana</option>
                                                <option value="PF">French Polynesia</option>
                                                <option value="TF">French Southern Territories</option>
                                                <option value="GA">Gabon</option>
                                                <option value="GM">Gambia</option>
                                                <option value="GE">Georgia</option>
                                                <option value="DE">Germany</option>
                                                <option value="GH">Ghana</option>
                                                <option value="GI">Gibraltar</option>
                                                <option value="GR">Greece</option>
                                                <option value="GL">Greenland</option>
                                                <option value="GD">Grenada</option>
                                                <option value="GP">Guadeloupe</option>
                                                <option value="GU">Guam</option>
                                                <option value="GT">Guatemala</option>
                                                <option value="GG">Guernsey</option>
                                                <option value="GN">Guinea</option>
                                                <option value="GW">Guinea-Bissau</option>
                                                <option value="GY">Guyana</option>
                                                <option value="HT">Haiti</option>
                                                <option value="HM">Heard Island and Mcdonald Islands</option>
                                                <option value="VA">Holy See (Vatican City State)</option>
                                                <option value="HN">Honduras</option>
                                                <option value="HK">Hong Kong</option>
                                                <option value="HU">Hungary</option>
                                                <option value="IS">Iceland</option>
                                                <option value="IN">India</option>
                                                <option value="ID">Indonesia</option>
                                                <option value="IR">Iran, Islamic Republic of</option>
                                                <option value="IQ">Iraq</option>
                                                <option value="IE">Ireland</option>
                                                <option value="IM">Isle of Man</option>
                                                <option value="IL">Israel</option>
                                                <option value="IT">Italy</option>
                                                <option value="JM">Jamaica</option>
                                                <option value="JP">Japan</option>
                                                <option value="JE">Jersey</option>
                                                <option value="JO">Jordan</option>
                                                <option value="KZ">Kazakhstan</option>
                                                <option value="KE">Kenya</option>
                                                <option value="KI">Kiribati</option>
                                                <option value="KP">Korea, Democratic People's Republic of</option>
                                                <option value="KR">Korea, Republic of</option>
                                                <option value="XK">Kosovo</option>
                                                <option value="KW">Kuwait</option>
                                                <option value="KG">Kyrgyzstan</option>
                                                <option value="LA">Lao People's Democratic Republic</option>
                                                <option value="LV">Latvia</option>
                                                <option value="LB">Lebanon</option>
                                                <option value="LS">Lesotho</option>
                                                <option value="LR">Liberia</option>
                                                <option value="LY">Libyan Arab Jamahiriya</option>
                                                <option value="LI">Liechtenstein</option>
                                                <option value="LT">Lithuania</option>
                                                <option value="LU">Luxembourg</option>
                                                <option value="MO">Macao</option>
                                                <option value="MK">Macedonia, the Former Yugoslav Republic of</option>
                                                <option value="MG">Madagascar</option>
                                                <option value="MW">Malawi</option>
                                                <option value="MY">Malaysia</option>
                                                <option value="MV">Maldives</option>
                                                <option value="ML">Mali</option>
                                                <option value="MT">Malta</option>
                                                <option value="MH">Marshall Islands</option>
                                                <option value="MQ">Martinique</option>
                                                <option value="MR">Mauritania</option>
                                                <option value="MU">Mauritius</option>
                                                <option value="YT">Mayotte</option>
                                                <option value="MX">Mexico</option>
                                                <option value="FM">Micronesia, Federated States of</option>
                                                <option value="MD">Moldova, Republic of</option>
                                                <option value="MC">Monaco</option>
                                                <option value="MN">Mongolia</option>
                                                <option value="ME">Montenegro</option>
                                                <option value="MS">Montserrat</option>
                                                <option value="MA">Morocco</option>
                                                <option value="MZ">Mozambique</option>
                                                <option value="MM">Myanmar</option>
                                                <option value="NA">Namibia</option>
                                                <option value="NR">Nauru</option>
                                                <option value="NP">Nepal</option>
                                                <option value="NL">Netherlands</option>
                                                <option value="AN">Netherlands Antilles</option>
                                                <option value="NC">New Caledonia</option>
                                                <option value="NZ">New Zealand</option>
                                                <option value="NI">Nicaragua</option>
                                                <option value="NE">Niger</option>
                                                <option value="NG">Nigeria</option>
                                                <option value="NU">Niue</option>
                                                <option value="NF">Norfolk Island</option>
                                                <option value="MP">Northern Mariana Islands</option>
                                                <option value="NO">Norway</option>
                                                <option value="OM">Oman</option>
                                                <option value="PK">Pakistan</option>
                                                <option value="PW">Palau</option>
                                                <option value="PS">Palestinian Territory, Occupied</option>
                                                <option value="PA">Panama</option>
                                                <option value="PG">Papua New Guinea</option>
                                                <option value="PY">Paraguay</option>
                                                <option value="PE">Peru</option>
                                                <option value="PH">Philippines</option>
                                                <option value="PN">Pitcairn</option>
                                                <option value="PL">Poland</option>
                                                <option value="PT">Portugal</option>
                                                <option value="PR">Puerto Rico</option>
                                                <option value="QA">Qatar</option>
                                                <option value="RE">Reunion</option>
                                                <option value="RO">Romania</option>
                                                <option value="RU">Russian Federation</option>
                                                <option value="RW">Rwanda</option>
                                                <option value="BL">Saint Barthelemy</option>
                                                <option value="SH">Saint Helena</option>
                                                <option value="KN">Saint Kitts and Nevis</option>
                                                <option value="LC">Saint Lucia</option>
                                                <option value="MF">Saint Martin</option>
                                                <option value="PM">Saint Pierre and Miquelon</option>
                                                <option value="VC">Saint Vincent and the Grenadines</option>
                                                <option value="WS">Samoa</option>
                                                <option value="SM">San Marino</option>
                                                <option value="ST">Sao Tome and Principe</option>
                                                <option value="SA">Saudi Arabia</option>
                                                <option value="SN">Senegal</option>
                                                <option value="RS">Serbia</option>
                                                <option value="CS">Serbia and Montenegro</option>
                                                <option value="SC">Seychelles</option>
                                                <option value="SL">Sierra Leone</option>
                                                <option value="SG">Singapore</option>
                                                <option value="SX">Sint Maarten</option>
                                                <option value="SK">Slovakia</option>
                                                <option value="SI">Slovenia</option>
                                                <option value="SB">Solomon Islands</option>
                                                <option value="SO">Somalia</option>
                                                <option value="ZA">South Africa</option>
                                                <option value="GS">South Georgia and the South Sandwich Islands</option>
                                                <option value="SS">South Sudan</option>
                                                <option value="ES">Spain</option>
                                                <option value="LK">Sri Lanka</option>
                                                <option value="SD">Sudan</option>
                                                <option value="SR">Suriname</option>
                                                <option value="SJ">Svalbard and Jan Mayen</option>
                                                <option value="SZ">Swaziland</option>
                                                <option value="SE">Sweden</option>
                                                <option value="CH">Switzerland</option>
                                                <option value="SY">Syrian Arab Republic</option>
                                                <option value="TW">Taiwan, Province of China</option>
                                                <option value="TJ">Tajikistan</option>
                                                <option value="TZ">Tanzania, United Republic of</option>
                                                <option value="TH">Thailand</option>
                                                <option value="TL">Timor-Leste</option>
                                                <option value="TG">Togo</option>
                                                <option value="TK">Tokelau</option>
                                                <option value="TO">Tonga</option>
                                                <option value="TT">Trinidad and Tobago</option>
                                                <option value="TN">Tunisia</option>
                                                <option value="TR">Turkey</option>
                                                <option value="TM">Turkmenistan</option>
                                                <option value="TC">Turks and Caicos Islands</option>
                                                <option value="TV">Tuvalu</option>
                                                <option value="UG">Uganda</option>
                                                <option value="UA">Ukraine</option>
                                                <option value="AE">United Arab Emirates</option>
                                                <option value="GB">United Kingdom</option>
                                                <option value="US">United States</option>
                                                <option value="UM">United States Minor Outlying Islands</option>
                                                <option value="UY">Uruguay</option>
                                                <option value="UZ">Uzbekistan</option>
                                                <option value="VU">Vanuatu</option>
                                                <option value="VE">Venezuela</option>
                                                <option value="VN">Viet Nam</option>
                                                <option value="VG">Virgin Islands, British</option>
                                                <option value="VI">Virgin Islands, U.s.</option>
                                                <option value="WF">Wallis and Futuna</option>
                                                <option value="EH">Western Sahara</option>
                                                <option value="YE">Yemen</option>
                                                <option value="ZM">Zambia</option>
                                                <option value="ZW">Zimbabwe</option>
                                            </select>
                                            <i class="fa-solid fa-globe left-icon"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Next of Kin -->
                                <div class="section-header mt-4">
                                    <div class="section-header-title">
                                        <i class="fa-solid fa-user-group"></i> Registered Next of Kin
                                    </div>
                                    <p class="section-header-desc">Information about your beneficiary</p>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Beneficiary Legal Name <span class="req">*</span></label>
                                        <div class="custom-input-group">
                                            <input type="text" name="next_of_kin_name" placeholder="Full Name" required>
                                            <i class="fa-solid fa-user left-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Relationship <span class="req">*</span></label>
                                        <div class="custom-input-group">
                                            <input type="text" name="next_of_kin_relationship" placeholder="e.g. Spouse, Parent" required>
                                            <i class="fa-solid fa-heart left-icon"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Document Upload -->
                                <div class="section-header mt-4">
                                    <div class="section-header-title">
                                        <i class="fa-solid fa-file-invoice"></i> Document Upload
                                    </div>
                                    <p class="section-header-desc">Upload clear photos of your documents</p>
                                </div>

                                <div class="upload-tabs">
                                    <div class="upload-tab active" data-type="International Passport" onclick="setType('International Passport', this)">
                                        <i class="fa-solid fa-passport"></i> Intl Passport
                                    </div>
                                    <div class="upload-tab" data-type="National ID Card" onclick="setType('National ID Card', this)">
                                        <i class="fa-solid fa-id-card"></i> National ID
                                    </div>
                                    <div class="upload-tab" data-type="Driver License" onclick="setType('Driver License', this)">
                                        <i class="fa-solid fa-address-card"></i> Driver's License
                                    </div>
                                </div>

                                <ul class="checklist mt-3">
                                    <li><i class="fa-solid fa-circle-check"></i> Ensure documents have not expired</li>
                                    <li><i class="fa-solid fa-circle-check"></i> Photo must be clear and readable</li>
                                    <li><i class="fa-solid fa-circle-check"></i> Files should be under 2MB</li>
                                </ul>

                                <div class="upload-zone-wrapper mt-4">
                                    <div class="row g-3">
                                        <div class="col-md-4" id="frontUploadWrapper">
                                            <div class="upload-zone-title">Upload Front Side <span class="req">*</span></div>
                                            <div class="upload-zone" onclick="document.getElementById('frontInput').click()">
                                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                                <p>Front Side</p>
                                                <small>Click to upload or drag</small>
                                                <input type="file" name="front_id" id="frontInput" hidden accept="image/*" onchange="updateFileName(this, 'frontName')">
                                                <div id="frontName" class="text-xs text-primary mt-2 fw-bold"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="backUploadWrapper">
                                            <div class="upload-zone-title">Upload Back Side <span class="req">*</span></div>
                                            <div class="upload-zone" onclick="document.getElementById('backInput').click()">
                                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                                <p>Back Side</p>
                                                <small>Click to upload or drag</small>
                                                <input type="file" name="back_id" id="backInput" hidden accept="image/*" onchange="updateFileName(this, 'backName')">
                                                <div id="backName" class="text-xs text-primary mt-2 fw-bold"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="selfieUploadWrapper">
                                            <div class="upload-zone-title">Upload Selfie <span class="req">*</span></div>
                                            <div class="upload-zone" onclick="document.getElementById('selfieInput').click()">
                                                <i class="fa-solid fa-camera"></i>
                                                <p>Selfie Probe</p>
                                                <small>Click to upload or drag</small>
                                                <input type="file" name="selfie" id="selfieInput" hidden accept="image/*" onchange="updateFileName(this, 'selfieName')">
                                                <div id="selfieName" class="text-xs text-primary mt-2 fw-bold"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid mt-5">
                                    <button type="submit" name="submit_kyc" class="btn-submit-kyc">
                                        Submit Verification <i class="fa-solid fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="help-footer-card">
                        <div class="help-footer-info">
                            <div class="help-footer-icon">
                                <i class="fa-solid fa-life-ring"></i>
                            </div>
                            <div class="help-footer-text">
                                <h6>Need help with verification?</h6>
                                <p>Our support team is available 24/7 to assist you with the process.</p>
                            </div>
                        </div>
                        <button class="btn btn-support-outline" onclick="location.href='support.php'"><i class="fa-solid fa-message me-2"></i> Contact Support</button>
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
        function setType(type, element) {
            document.getElementById('docTypeInput').value = type;
            const tabs = document.querySelectorAll('.upload-tab');
            tabs.forEach(t => t.classList.remove('active'));
            element.classList.add('active');
            
            const backWrapper = document.getElementById('backUploadWrapper');
            const frontWrapper = document.getElementById('frontUploadWrapper');
            const selfieWrapper = document.getElementById('selfieUploadWrapper');
            
            if (type === 'International Passport') {
                backWrapper.style.display = 'none';
                frontWrapper.className = 'col-md-6';
                selfieWrapper.className = 'col-md-6';
                frontWrapper.querySelector('.upload-zone-title').innerText = 'Upload Passport Data Page *';
            } else {
                backWrapper.style.display = 'block';
                frontWrapper.className = 'col-md-4';
                backWrapper.className = 'col-md-4';
                selfieWrapper.className = 'col-md-4';
                frontWrapper.querySelector('.upload-zone-title').innerText = 'Upload Front Side *';
            }
        }

        function updateFileName(input, targetId) {
            const display = document.getElementById(targetId);
            if (display && input.files && input.files[0]) {
                display.textContent = 'Selected: ' + input.files[0].name;
            }
        }

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
