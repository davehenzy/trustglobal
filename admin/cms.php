<?php require_once '../includes/admin-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frontend CMS - SwiftCapital Admin</title>
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
            <a href="support.php" class="nav-link">
                <i class="fa-solid fa-headset"></i> Support Tickets
            </a>
            <a href="cms.php" class="nav-link active">
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
                <h4 class="mb-0 fw-800">Content Management</h4>
            </div>

            <div class="user-nav">
                <div class="notification-bell">
                    <i class="fa-solid fa-bell fs-5"></i>
                    <span class="notification-dot"></span>
                </div>
                
                <div class="admin-profile">
                    <div class="admin-avatar"><?php echo strtoupper(substr($_SESSION["user_name"] ?? "A", 0, 1)); ?></div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold text-sm"><?php echo $_SESSION["user_name"] ?? "Admin"; ?></div>
                        <div class="text-xs text-muted"><?php echo $_SESSION["role"] ?? "Administrator"; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-padding">
            
            <div class="row g-4">
                <!-- Sidebar Tabs for CMS Sections -->
                <div class="col-lg-3">
                    <div class="data-table-card p-3 border-0 bg-white" style="border-radius: 20px;">
                        <div class="nav flex-column nav-pills gap-2" id="cmsTabs" role="tablist">
                            <button class="nav-link active text-start py-3 fw-800 border-0" data-bs-toggle="pill" data-bs-target="#homepage" type="button" style="border-radius: 12px;"><i class="fa-solid fa-house me-2"></i> Homepage Hero</button>
                            <button class="nav-link text-start py-3 fw-800 border-0" data-bs-toggle="pill" data-bs-target="#about" type="button" style="border-radius: 12px;"><i class="fa-solid fa-circle-info me-2"></i> About Trust</button>
                            <button class="nav-link text-start py-3 fw-800 border-0" data-bs-toggle="pill" data-bs-target="#services" type="button" style="border-radius: 12px;"><i class="fa-solid fa-briefcase me-2"></i> Services Grid</button>
                            <button class="nav-link text-start py-3 fw-800 border-0" data-bs-toggle="pill" data-bs-target="#sc-services" type="button" style="border-radius: 12px;"><i class="fa-solid fa-layer-group me-2"></i> Inner Pages</button>
                            <button class="nav-link text-start py-3 fw-800 border-0" data-bs-toggle="pill" data-bs-target="#contact" type="button" style="border-radius: 12px;"><i class="fa-solid fa-address-book me-2"></i> Contact Center</button>
                            <button class="nav-link text-start py-3 fw-800 border-0" data-bs-toggle="pill" data-bs-target="#appearance" type="button" style="border-radius: 12px;"><i class="fa-solid fa-palette me-2"></i> Branding Assets</button>
                        </div>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="col-lg-9">
                    <div class="data-table-card p-5 border-0 bg-white" style="border-radius: 24px;">
                        <div class="tab-content">
                            <!-- Homepage Hero -->
                            <div class="tab-pane fade show active" id="homepage">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h4 class="fw-800 mb-0">Hero Section Editor</h4>
                                    <button class="btn btn-primary px-4 fw-800" style="border-radius: 10px;">Save Changes</button>
                                </div>
                                <form>
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Main Headline</label>
                                            <input type="text" class="form-control form-control-lg bg-light border-0 fw-600" value="Experience the Future of Premium Banking">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Sub-Headline / Description</label>
                                            <textarea class="form-control bg-light border-0 fw-500" rows="3">SwiftCapital provides seamless, secure, and innovative financial luxury solutions for elite individuals and corporations worldwide.</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Primary CTA Text</label>
                                            <input type="text" class="form-control bg-light border-0 fw-600" value="Open Elite Account">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Secondary CTA Text</label>
                                            <input type="text" class="form-control bg-light border-0 fw-600" value="View Assets">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Hero Background Asset</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control bg-light border-0 fw-500" value="/assets/img/luxury-bg.jpg">
                                                <button class="btn btn-indigo px-4 fw-800" type="button">UPLOAD</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- About Section -->
                            <div class="tab-pane fade" id="about">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h4 class="fw-800 mb-0">Brand Story Editor</h4>
                                    <button class="btn btn-primary px-4 fw-800" style="border-radius: 10px;">Save Story</button>
                                </div>
                                <form>
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Core Narrative Heading</label>
                                            <input type="text" class="form-control bg-light border-0 fw-600" value="Our Mission: Redefining Trust">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Story Content</label>
                                            <textarea class="form-control bg-light border-0 fw-500" rows="6">At SwiftCapital, we are dedicated to empowering our clients through transparent, accessible, and high-performance financial services. Built on a foundation of trust and technology, our vision is to lead the next generation of banking innovation.</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Asset Count (Dynamic Display)</label>
                                            <input type="text" class="form-control bg-light border-0 fw-600" value="1.2M+ Active Users">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Financial Velocity Tracking</label>
                                            <input type="text" class="form-control bg-light border-0 fw-600" value="$42B+ Assets Under Management">
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Services Grid -->
                            <div class="tab-pane fade" id="services">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h4 class="fw-800 mb-0">Product Grid</h4>
                                    <button class="btn btn-indigo px-4 fw-800" style="border-radius: 10px;"><i class="fa-solid fa-plus me-2"></i> ADD PRODUCT</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th class="border-0 text-muted fw-800 text-xs uppercase">Symbol</th>
                                                <th class="border-0 text-muted fw-800 text-xs uppercase">Product Line</th>
                                                <th class="border-0 text-muted fw-800 text-xs uppercase">Description</th>
                                                <th class="border-0 text-end text-muted fw-800 text-xs uppercase">Resolution</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-bottom-0">
                                                <td class="py-4"><div class="stat-icon bg-indigo-light text-primary" style="width: 40px; height: 40px; border-radius: 12px;"><i class="fa-solid fa-gem shadow-sm"></i></div></td>
                                                <td class="py-4 fw-800">Ultra-Premium Savings</td>
                                                <td class="py-4 text-xs fw-600 text-muted">High-yield savings with 6.5% APY and no limits.</td>
                                                <td class="py-4 text-end">
                                                    <button class="btn btn-light btn-sm fw-800 px-3 border-0" style="border-radius: 8px;">EDIT</button>
                                                    <button class="btn btn-rose-light btn-sm fw-800 px-3 border-0 text-danger ms-2" style="border-radius: 8px;">DEL</button>
                                                </td>
                                            </tr>
                                            <tr class="border-bottom-0">
                                                <td class="py-4"><div class="stat-icon bg-emerald-light text-success" style="width: 40px; height: 40px; border-radius: 12px;"><i class="fa-solid fa-shield-halved shadow-sm"></i></div></td>
                                                <td class="py-4 fw-800">Vanguard Protection</td>
                                                <td class="py-4 text-xs fw-600 text-muted">Military-grade asset protection for high-net-worth clients.</td>
                                                <td class="py-4 text-end">
                                                    <button class="btn btn-light btn-sm fw-800 px-3 border-0" style="border-radius: 8px;">EDIT</button>
                                                    <button class="btn btn-rose-light btn-sm fw-800 px-3 border-0 text-danger ms-2" style="border-radius: 8px;">DEL</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Appearance -->
                            <div class="tab-pane fade" id="appearance">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h4 class="fw-800 mb-0">Identity & Branding</h4>
                                    <button class="btn btn-primary px-4 fw-800" style="border-radius: 10px;">Synchronize</button>
                                </div>
                                <form>
                                    <div class="row g-5">
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted mb-3">Primary Brand Logo</label>
                                            <div class="p-5 border-0 bg-light text-center mb-4 shadow-inner" style="border-radius: 20px;">
                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                    <i class="fa-solid fa-shield-halved fa-3x text-primary"></i>
                                                    <span class="fw-800 fs-2" style="letter-spacing: -1px;">SwiftCapital</span>
                                                </div>
                                            </div>
                                            <div class="input-group">
                                                <input type="file" class="form-control border-0 bg-light fw-600">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted mb-3">System Favicon</label>
                                            <div class="p-5 border-0 bg-light text-center mb-4 shadow-inner" style="border-radius: 20px;">
                                                <i class="fa-solid fa-shield-halved text-primary" style="font-size: 4rem;"></i>
                                            </div>
                                            <div class="input-group">
                                                <input type="file" class="form-control border-0 bg-light fw-600">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Primary Velocity Color</label>
                                            <div class="input-group p-1 bg-light rounded-3">
                                                <input type="color" class="form-control form-control-color border-0 bg-transparent" value="#6366f1" title="Choose color">
                                                <input type="text" class="form-control border-0 bg-transparent fw-800 text-center" value="#6366F1">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Accent Luxury Color</label>
                                            <div class="input-group p-1 bg-light rounded-3">
                                                <input type="color" class="form-control form-control-color border-0 bg-transparent" value="#0f172a" title="Choose color">
                                                <input type="text" class="form-control border-0 bg-transparent fw-800 text-center" value="#0F172A">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Remaining sections (Inner Pages, Contact) can follow similar updated pattern -->
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="mt-auto py-5 px-4 border-top bg-white text-center text-muted" style="font-size: 0.85rem;">
            SwiftCapital Admin © 2026. Internal System Only.
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .nav-pills .nav-link {
            color: #64748b;
            transition: 0.3s;
        }
        .nav-pills .nav-link:hover {
            background: #f8fafc;
            color: var(--admin-primary);
        }
        .nav-pills .nav-link.active {
            background: #eef2ff !important;
            color: var(--admin-primary) !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
        }
        .form-control:focus {
            background-color: #fff !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
        .shadow-inner {
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);
        }
    </style>
</body>
</html>
