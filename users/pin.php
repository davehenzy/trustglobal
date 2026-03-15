<?php require_once '../includes/user-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PIN Verification - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #fafbfc;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .auth-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .pin-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            text-align: center;
        }
        .pin-card-header {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            padding: 40px 30px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        /* Decorative circles for header */
        .pin-card-header::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .pin-card-header::after {
            content: '';
            position: absolute;
            bottom: -30px;
            right: -10px;
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .header-icon {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 20px;
            position: relative;
            z-index: 2;
        }
        .pin-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }
        .pin-subtitle {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 0;
            position: relative;
            z-index: 2;
        }
        
        .pin-card-body {
            padding: 40px 30px 30px;
            position: relative;
        }
        .user-avatar-wrapper {
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            width: 64px;
            height: 64px;
            background: #fff;
            border-radius: 50%;
            padding: 4px;
            z-index: 10;
        }
        .user-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #eff6ff;
            color: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.2rem;
        }
        .user-status {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 20px;
            height: 20px;
            background: #0ea5e9;
            border: 2px solid #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.55rem;
        }

        .user-name {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1e293b;
            margin-bottom: 5px;
            margin-top: 10px;
        }
        .pin-instruction {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 25px;
        }

        .pin-input {
            width: 100%;
            text-align: center;
            font-size: 2.5rem;
            letter-spacing: 35px;
            padding: 15px 0 15px 35px; /* Added left padding to balance letter-spacing visually */
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 25px;
            color: #0f172a;
            outline: none;
            transition: all 0.2s;
            font-family: monospace; /* usually produces cleaner dots for password fields */
        }
        .pin-input:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }

        .btn-verify {
            width: 100%;
            padding: 12px;
            background: #0ea5e9;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-verify:hover {
            background: #0284c7;
        }

        .security-notice {
            margin-top: 25px;
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: flex-start;
            text-align: left;
            font-size: 0.75rem;
            color: #64748b;
        }
        .security-notice i {
            color: #0ea5e9;
            margin-right: 12px;
            margin-top: 3px;
            font-size: 1rem;
        }

        .auth-footer {
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
        }
        .footer-links a {
            color: #64748b;
            text-decoration: none;
            margin-left: 15px;
        }
        .lang-selector {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 5px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            color: #475569;
        }
        .lang-selector img {
            width: 20px;
            margin-right: 8px;
        }

        @media (max-width: 576px) {
            .auth-footer {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <div class="pin-card">
            <div class="pin-card-header">
                <div class="header-icon">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
                <h2 class="pin-title">Verify Your Identity</h2>
                <p class="pin-subtitle">Please enter your secure 4-digit PIN to continue</p>
            </div>
            <div class="pin-card-body">
                <div class="user-avatar-wrapper">
                    <div class="user-avatar">KA</div>
                    <div class="user-status"><i class="fa-solid fa-shield-halved"></i></div>
                </div>
                
                <h3 class="user-name">Kante Cante Calm</h3>
                <p class="pin-instruction">Enter your 4-digit verification PIN</p>

                <form action="index.php">
                    <input type="password" class="pin-input" maxlength="4" autofocus>
                    <button type="button" class="btn-verify" onclick="window.location.href='index.php'">Verify PIN</button>
                </form>

                <div class="security-notice">
                    <i class="fa-solid fa-shield-halved"></i>
                    <div>Your security is our priority. PIN verification protects your account from unauthorized access.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-footer">
        <div class="lang-selector">
            <img src="https://flagcdn.com/w20/gb.png" alt="English"> EN <i class="fa-solid fa-chevron-up ms-2 text-muted" style="font-size: 0.6rem;"></i>
        </div>
        <div class="text-center text-muted">
            © 2026 SwiftCapital. All rights reserved.<br>
            <div class="footer-links mt-1">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Support</a>
            </div>
        </div>
        <div style="width: 70px;"></div> <!-- Spacer for flex balance -->
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
