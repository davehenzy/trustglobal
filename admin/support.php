<?php require_once '../includes/admin-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Center - SwiftCapital Admin</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="admin-sidebar">
        <div class="brand-area">
            <div class="brand-icon"><i class="fa-solid fa-shield-halved"></i></div>
            <div class="brand-name">SwiftAdmin</div>
        </div>

        <div class="nav-links">
            <a href="index.php" class="nav-link">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="users.php" class="nav-link">
                <i class="fa-solid fa-users"></i> Users Management
            </a>
            <a href="transactions.php" class="nav-link">
                <i class="fa-solid fa-money-bill-transfer"></i> Transactions
            </a>
            <a href="loans.php" class="nav-link">
                <i class="fa-solid fa-hand-holding-dollar"></i> Loan Requests
            </a>
            <a href="kyc.php" class="nav-link">
                <i class="fa-solid fa-id-card-clip"></i> KYC Verifications
            </a>
            <a href="support.php" class="nav-link active">
                <i class="fa-solid fa-headset"></i> Support Tickets
            </a>
            <a href="cms.php" class="nav-link">
                <i class="fa-solid fa-pen-nib"></i> Frontend CMS
            </a>
            <a href="settings.php" class="nav-link">
                <i class="fa-solid fa-gear"></i> System Settings
            </a>
            
            <div class="mt-auto" style="position: absolute; bottom: 20px; width: 100%;">
                <a href="../logout.php" class="nav-link text-danger">
                    <i class="fa-solid fa-power-off"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="breadcrumb-area">
                <h4 class="mb-0 fw-800">Support Command Center</h4>
            </div>

            <div class="user-nav">
                <div class="notification-bell">
                    <i class="fa-solid fa-bell fs-5"></i>
                    <span class="notification-dot"></span>
                </div>
                
                <div class="admin-profile">
                    <div class="admin-avatar">AD</div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold text-sm">Admin Master</div>
                        <div class="text-xs text-muted">Super Administrator</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-padding">
            
            <div class="row g-4">
                <!-- Support Tickets List -->
                <div class="col-lg-4">
                    <div class="data-table-card mt-0" style="height: 700px; display: flex; flex-direction: column;">
                        <div class="card-header border-0 border-bottom bg-transparent py-4 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-800">Inbox</h5>
                            <span class="badge bg-indigo-light text-primary fw-800 px-3">12 ACTIVE</span>
                        </div>
                        <div class="p-3 border-bottom">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-search text-muted"></i></span>
                                <input type="text" class="form-control bg-light border-0" placeholder="Search conversations...">
                            </div>
                        </div>
                        <div class="list-group list-group-flush flex-grow-1" style="overflow-y: auto;">
                            <!-- Ticket Item -->
                            <a href="#" class="list-group-item list-group-item-action p-4 border-0 mb-1" style="background: #f1f5f9; border-left: 5px solid var(--admin-primary) !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-rose-light text-danger fw-800 text-uppercase" style="font-size: 0.6rem;">Urgent</span>
                                    <span class="text-xs text-muted fw-600">10:45 AM</span>
                                </div>
                                <h6 class="mb-1 fw-800 text-dark">Withdrawal issue - Delayed</h6>
                                <p class="text-xs text-muted mb-3 line-clamp-2">My withdrawal of $500 has been pending for 24 hours now. Can you please investigate this immediately?</p>
                                <div class="d-flex align-items-center gap-2 mt-auto">
                                    <div class="admin-avatar" style="width: 24px; height: 24px; font-size: 0.6rem;">JD</div>
                                    <span class="text-xs fw-800 text-dark">John Doe</span>
                                </div>
                            </a>
                            
                            <a href="#" class="list-group-item list-group-item-action p-4 border-0 mb-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-indigo-light text-primary fw-800 text-uppercase" style="font-size: 0.6rem;">General</span>
                                    <span class="text-xs text-muted fw-600">Yesterday</span>
                                </div>
                                <h6 class="mb-1 fw-800 text-dark">KYC Document Help</h6>
                                <p class="text-xs text-muted mb-3 line-clamp-2">I am having trouble uploading my passport image on the portal...</p>
                                <div class="d-flex align-items-center gap-2 mt-auto">
                                    <div class="admin-avatar" style="width: 24px; height: 24px; font-size: 0.6rem; background: #fbbf24;">KC</div>
                                    <span class="text-xs fw-800 text-dark">Kante Calm</span>
                                </div>
                            </a>

                            <a href="#" class="list-group-item list-group-item-action p-4 border-0 mb-1 opacity-50">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-light text-muted fw-800 text-uppercase" style="font-size: 0.6rem;">Closed</span>
                                    <span class="text-xs text-muted fw-600">Mar 12</span>
                                </div>
                                <h6 class="mb-1 fw-800 text-dark">Password Reset request</h6>
                                <p class="text-xs text-muted mb-3 line-clamp-2">I forgot my password and need a reset link for my login...</p>
                                <div class="d-flex align-items-center gap-2 mt-auto">
                                    <div class="admin-avatar" style="width: 24px; height: 24px; font-size: 0.6rem; background: #64748b;">RB</div>
                                    <span class="text-xs fw-800 text-dark">Robert Bryan</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Chat/Conversation Console -->
                <div class="col-lg-8">
                    <div class="data-table-card mt-0 d-flex flex-column shadow-sm" style="height: 700px; border-radius: 24px;">
                        <div class="card-header border-0 border-bottom bg-white d-flex justify-content-between align-items-center py-4 px-4">
                            <div class="d-flex align-items-center gap-4">
                                <div class="admin-avatar" style="width: 48px; height: 48px; font-size: 1rem;">JD</div>
                                <div>
                                    <h5 class="mb-1 fw-800 text-dark">John Doe</h5>
                                    <div class="text-xs text-muted fw-600"><span class="text-success pulse-dot me-1"></span> Online â€¢ Ticket #TK-22941</div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-light btn-sm fw-bold border-0 px-3" style="border-radius: 10px;">Archive</button>
                                <button class="btn btn-primary btn-sm fw-800 px-4" style="border-radius: 10px;">Resolve</button>
                            </div>
                        </div>

                        <!-- Messages Container -->
                        <div class="p-4 flex-grow-1" style="overflow-y: auto; background: #fcfdfe;">
                            <div class="text-center my-4">
                                <span class="badge bg-light text-muted fw-bold px-3 py-2 text-xs" style="border-radius: 20px;">TODAY, MARCH 15</span>
                            </div>

                            <div class="mb-5">
                                <div class="d-flex gap-3 mb-2">
                                    <div class="admin-avatar shadow-sm" style="width: 32px; height: 32px;">JD</div>
                                    <div class="p-4 bg-white shadow-sm border-0 rounded-4" style="max-width: 75%; border-top-left-radius: 4px !important;">
                                        <p class="text-sm mb-0 fw-500 text-dark">Hello, I made a withdrawal request of $500 yesterday morning around 10:45 AM. It's still showing "Pending" on my dashboard. Can you please check the status for me?</p>
                                    </div>
                                </div>
                                <span class="text-xs text-muted ms-5 fw-600">10:45 AM</span>
                            </div>

                            <div class="mb-5 text-end">
                                <div class="d-flex gap-3 mb-2 justify-content-end">
                                    <div class="p-4 text-white shadow-lg border-0 rounded-4" style="max-width: 75%; background: linear-gradient(135deg, var(--admin-primary), #6366f1); border-top-right-radius: 4px !important;">
                                        <p class="text-sm mb-0 fw-500">Hello John, thank you for reaching out to SwiftCapital Support. Let me check the transaction records for you. One moment please.</p>
                                    </div>
                                    <div class="admin-avatar shadow-sm" style="width: 32px; height: 32px; background: #0f172a;">AD</div>
                                </div>
                                <span class="text-xs text-muted me-5 fw-600">11:02 AM</span>
                            </div>

                            <div class="mb-5">
                                <div class="d-flex gap-3 mb-2">
                                    <div class="admin-avatar shadow-sm" style="width: 32px; height: 32px;">JD</div>
                                    <div class="p-4 bg-white shadow-sm border-0 rounded-4" style="max-width: 75%; border-top-left-radius: 4px !important;">
                                        <p class="text-sm mb-0 fw-500 text-dark">Sure, I'll wait. Thank you for the quick response. I really appreciate it.</p>
                                    </div>
                                </div>
                                <span class="text-xs text-muted ms-5 fw-600">11:05 AM</span>
                            </div>
                        </div>

                        <!-- Reply Input -->
                        <div class="p-4 bg-white border-top">
                            <div class="input-group gap-2 p-1 bg-light rounded-4" style="border: 2px solid transparent; transition: 0.3s;">
                                <textarea class="form-control border-0 bg-transparent py-3 px-3 shadow-none" rows="1" placeholder="Type your response to John..." style="resize: none;"></textarea>
                                <button class="btn btn-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-radius: 12px;"><i class="fa-solid fa-paper-plane"></i></button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="d-flex gap-4">
                                    <button class="btn btn-link btn-xs p-0 text-decoration-none text-muted fw-800" style="font-size: 0.7rem;"><i class="fa-solid fa-paperclip me-2"></i> ATTACH</button>
                                    <button class="btn btn-link btn-xs p-0 text-decoration-none text-muted fw-800" style="font-size: 0.7rem;"><i class="fa-solid fa-image me-2"></i> IMAGE</button>
                                    <button class="btn btn-link btn-xs p-0 text-decoration-none text-muted fw-800" style="font-size: 0.7rem;"><i class="fa-solid fa-bolt me-2"></i> MACROS</button>
                                </div>
                                <div class="text-xs text-muted fw-600">Press ENTER to send</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="mt-auto py-4 px-4 border-top bg-white text-center text-muted" style="font-size: 0.85rem;">
            SwiftCapital Admin © 2026. Internal System Only.
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
</body>
</html>
