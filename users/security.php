<?php
require_once '../includes/db.php';
require_once '../includes/user-check.php';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || !verifyCSRF($_POST['csrf_token'])) {
        $error = "Security session expired. Please refresh the page and try again.";
    } else {
        $action = $_POST['action'] ?? '';

    if ($action === 'update_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Fetch current user password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!password_verify($current_password, $user['password'])) {
            $error = "The current password you entered is incorrect.";
        } elseif (strlen($new_password) < 8) {
            $error = "The new password must be at least 8 characters long.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New password and confirmation do not match.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashed_password, $user_id])) {
                $success = "Your account password has been successfully updated.";
            } else {
                $error = "System error occurred. Please try again later.";
            }
        }
    } elseif ($action === 'update_pin') {
        $current_pin = $_POST['current_pin'] ?? '';
        $new_pin = $_POST['new_pin'] ?? '';
        $confirm_pin = $_POST['confirm_pin'] ?? '';

        // Fetch current user PIN
        $stmt = $pdo->prepare("SELECT pin FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($current_pin !== $user['pin']) {
            $error = "The current transaction PIN entered is incorrect.";
        } elseif (!is_numeric($new_pin) || strlen($new_pin) < 4) {
            $error = "New PIN must be at least 4 digits.";
        } elseif ($new_pin !== $confirm_pin) {
            $error = "New PIN and confirmation do not match.";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET pin = ? WHERE id = ?");
            if ($stmt->execute([$new_pin, $user_id])) {
                $success = "Your transaction PIN has been successfully updated.";
                $_SESSION['pin'] = $new_pin; // Update session
            } else {
                $error = "System error occurred. Please try again later.";
            }
        }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Governance - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --security-accent: #c5a059;
            --security-dark: #001f44;
        }

        .sec-wrap { max-width: 900px; margin: 0 auto; }
        
        .security-audit-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            position: relative;
        }

        .security-audit-header {
            padding: 35px 40px;
            background: #fcfcfc;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sec-title-box h5 {
            font-weight: 800; color: var(--security-dark);
            text-transform: uppercase; letter-spacing: 1px;
            margin: 0; font-size: 1.1rem;
        }

        .sec-title-box p {
            margin: 5px 0 0; color: #718096; font-size: 0.85rem; font-weight: 500;
        }

        .security-audit-body {
            padding: 40px;
        }

        .sec-icon-circle {
            width: 45px; height: 45px;
            background: rgba(197, 160, 89, 0.1);
            color: var(--security-accent);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }

        .protocol-label {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #a0aec0;
            margin-bottom: 8px;
            display: block;
        }

        .protocol-input {
            width: 100%;
            padding: 16px 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 1rem;
            color: #2d3748;
            transition: all 0.3s;
            outline: none;
        }

        .protocol-input:focus {
            border-color: var(--security-accent);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(197, 160, 89, 0.05);
        }

        .btn-authorize {
            background: var(--security-dark);
            color: #fff;
            border: none;
            padding: 18px 30px;
            border-radius: 4px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-authorize:hover {
            background: var(--security-accent);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(197, 160, 89, 0.2);
        }

        .alert-prestige {
            border: none;
            border-left: 4px solid;
            border-radius: 0;
            padding: 20px 25px;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .alert-prestige-success {
            background: #f0fdf4; border-color: #22c55e; color: #166534;
        }

        .alert-prestige-error {
            background: #fef2f2; border-color: #ef4444; color: #991b1b;
        }

        .sec-note-box {
            background: #fffbeb;
            border-radius: 4px;
            padding: 24px;
            border: 1px solid #fef3c7;
            display: flex; gap: 18px;
            margin-bottom: 40px;
        }

        .sec-note-box i { color: #d97706; font-size: 1.3rem; margin-top: 3px; }
        .sec-note-text h6 { font-weight: 800; color: #92400e; margin-bottom: 5px; text-transform: uppercase; font-size: 0.8rem; }
        .sec-note-text p { margin: 0; color: #b45309; font-size: 0.85rem; line-height: 1.6; }

        .requirement-list-gold {
            list-style: none; padding: 0; margin-top: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
        }
        .requirement-list-gold li {
            font-size: 0.8rem; font-weight: 600; color: #718096; display: flex; align-items: center; gap: 10px;
        }
        .requirement-list-gold li i { color: var(--security-accent); font-size: 0.7rem; }
    </style>
</head>
<body>

<?php 
$page = 'security';
include '../includes/user-sidebar.php'; 
?>

<main class="main-content">
    <?php include '../includes/user-navbar.php'; ?>

    <div class="page-container">
        <div class="sec-wrap">
            <div class="page-header mb-5 text-center text-lg-start">
                <h1 class="page-title fw-900">Access Governance</h1>
                <div class="breadcrumb-text">
                    <a href="index.php">Institutional</a> / Security Protocols / Identity Verification
                </div>
            </div>

            <?php if ($success): ?>
            <div class="alert alert-prestige alert-prestige-success mb-5 animate__animated animate__fadeInDown">
                <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success; ?>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="alert alert-prestige alert-prestige-error mb-5 animate__animated animate__shakeX">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <div class="sec-note-box shadow-sm">
                <i class="fa-solid fa-shield-halved"></i>
                <div class="sec-note-text">
                    <h6>Institutional Cyber-Security Reminder</h6>
                    <p>We do not store plain-text credentials. Every modification to your access protocols undergoes immediate encryption and global node synchronization. Ensure your credentials are unique to this primary account.</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Change Password -->
                <div class="col-lg-6">
                    <div class="security-audit-card">
                        <div class="security-audit-header">
                            <div class="sec-title-box">
                                <h5>Password Protocol</h5>
                                <p>Primary Access Authentication</p>
                            </div>
                            <div class="sec-icon-circle"><i class="fa-solid fa-key"></i></div>
                        </div>
                        <div class="security-audit-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="action" value="update_password">
                                <div class="mb-4">
                                    <label class="protocol-label">Existing Password</label>
                                    <input type="password" name="current_password" class="protocol-input" placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢" required>
                                </div>
                                <div class="mb-4">
                                    <label class="protocol-label">New Secure Password</label>
                                    <input type="password" name="new_password" class="protocol-input" placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢" required>
                                </div>
                                <div class="mb-4">
                                    <label class="protocol-label">Verify New Password</label>
                                    <input type="password" name="confirm_password" class="protocol-input" placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢" required>
                                </div>
                                <button type="submit" class="btn-authorize">Authorize Update</button>
                            </form>
                            
                            <ul class="requirement-list-gold">
                                <li><i class="fa-solid fa-circle"></i> 8+ Characters</li>
                                <li><i class="fa-solid fa-circle"></i> Alpha-Numeric</li>
                                <li><i class="fa-solid fa-circle"></i> Case Sensitive</li>
                                <li><i class="fa-solid fa-circle"></i> Unique String</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Change PIN -->
                <div class="col-lg-6">
                    <div class="security-audit-card">
                        <div class="security-audit-header">
                            <div class="sec-title-box">
                                <h5>Transaction PIN</h5>
                                <p>Operational Fund Access</p>
                            </div>
                            <div class="sec-icon-circle"><i class="fa-solid fa-fingerprint"></i></div>
                        </div>
                        <div class="security-audit-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="action" value="update_pin">
                                <div class="mb-4">
                                    <label class="protocol-label">Current Strategic PIN</label>
                                    <input type="password" name="current_pin" class="protocol-input" maxlength="10" placeholder="â€¢â€¢â€¢â€¢" required>
                                </div>
                                <div class="mb-4">
                                    <label class="protocol-label">New Operational PIN</label>
                                    <input type="password" name="new_pin" class="protocol-input" maxlength="10" placeholder="â€¢â€¢â€¢â€¢" required>
                                </div>
                                <div class="mb-4">
                                    <label class="protocol-label">Verify Operational PIN</label>
                                    <input type="password" name="confirm_pin" class="protocol-input" maxlength="10" placeholder="â€¢â€¢â€¢â€¢" required>
                                </div>
                                <button type="submit" class="btn-authorize" style="border-top: 2px solid var(--security-accent);">Authorize PIN Reset</button>
                            </form>
                            <div class="mt-4 p-3 bg-light rounded small text-muted">
                                <i class="fa-solid fa-circle-info me-2 text-gold" style="color:var(--security-accent);"></i> Required for all international wires and local capital transfers.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Login History / Extra Info -->
            <div class="security-audit-card mt-4">
                <div class="security-audit-header py-4">
                    <div class="sec-title-box">
                        <h5>Encryption Standard</h5>
                        <p>FIPS 140-2 Validated Infrastructure</p>
                    </div>
                    <div class="badge bg-success-subtle text-success px-4 py-2 border border-success-subtle">ACTIVE PROTOCOL</div>
                </div>
                <div class="security-audit-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <p class="mb-0 text-muted small">Your session is protected by 256-bit AES encryption. Automated logout is engaged after 15 minutes of inactivity to preserve asset integrity.</p>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <i class="fa-solid fa-shield-check fs-2 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer mt-auto">
        <div class="brand">
            <span class="text-primary fw-bold" style="letter-spacing: -0.5px;">Swift</span><span class="text-dark fw-bold" style="letter-spacing: -0.5px;">Capital</span> Institutional © 2026.
        </div>
        <div class="footer-links">
            <a href="#">Privacy Charter</a>
            <a href="#">Terms of Governance</a>
            <a href="#">Security Team</a>
        </div>
    </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
