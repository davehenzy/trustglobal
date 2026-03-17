<?php 
require_once '../includes/admin-check.php'; 
if ($_SESSION['role'] !== 'Super Admin') {
    header("Location: index.php");
    exit();
} 

// Handle Updates
$success_msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
    foreach ($_POST as $key => $value) {
        if ($key == 'update_settings') continue;
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    $success_msg = 'Settings updated successfully!';
}

// Handle Service Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['service_action'])) {
    $action = $_POST['service_action'];
    if ($action == 'save') {
        $id = $_POST['service_id'] ?? null;
        $title = $_POST['title'];
        $description = $_POST['description'];
        $image_url = $_POST['image_url'];
        $icon = $_POST['icon'];
        $color_class = $_POST['color_class'];

        if ($id) {
            $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, image_url = ?, icon = ?, color_class = ? WHERE id = ?");
            $stmt->execute([$title, $description, $image_url, $icon, $color_class, $id]);
            $success_msg = 'Service updated successfully!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO services (title, description, image_url, icon, color_class) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $image_url, $icon, $color_class]);
            $success_msg = 'Service added successfully!';
        }
    } elseif ($action == 'delete') {
        $id = $_POST['service_id'];
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $success_msg = 'Service deleted successfully!';
    }
}

// Fetch all settings into an associative array
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings_raw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Fetch all services
$stmt = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC, id DESC");
$services = $stmt->fetchAll();

// Helper function to get setting with fallback
function getSetting($key, $default = '') {
    global $settings_raw;
    return $settings_raw[$key] ?? $default;
}
?>
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
                    <div class="admin-avatar">
                        <?php if(!empty($_SESSION['profile_pic'])): ?>
                            <img src="../assets/uploads/profiles/<?php echo $_SESSION['profile_pic']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
                        <?php else: ?>
                            <?php echo strtoupper(substr($_SESSION["user_name"] ?? "A", 0, 1)); ?>
                        <?php endif; ?>
                    </div>
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
                                <form method="POST">
                                <input type="hidden" name="update_settings" value="1">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h4 class="fw-800 mb-0">Hero Section Editor</h4>
                                    <button type="submit" class="btn btn-primary px-4 fw-800" style="border-radius: 10px;">Save Changes</button>
                                </div>
                                <?php if ($success_msg): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; font-weight: 600;">
                                        <?php echo $success_msg; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Main Headline</label>
                                            <input type="text" name="hero_headline" class="form-control form-control-lg bg-light border-0 fw-600" value="<?php echo htmlspecialchars(getSetting('hero_headline')); ?>">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Sub-Headline / Description</label>
                                            <textarea name="hero_description" class="form-control bg-light border-0 fw-500" rows="3"><?php echo htmlspecialchars(getSetting('hero_description')); ?></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Primary CTA Text</label>
                                            <input type="text" name="hero_cta_primary" class="form-control bg-light border-0 fw-600" value="<?php echo htmlspecialchars(getSetting('hero_cta_primary')); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Secondary CTA Text</label>
                                            <input type="text" name="hero_cta_secondary" class="form-control bg-light border-0 fw-600" value="<?php echo htmlspecialchars(getSetting('hero_cta_secondary')); ?>">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Hero Background Asset</label>
                                            <div class="input-group">
                                                <input type="text" name="hero_bg" class="form-control bg-light border-0 fw-500" value="<?php echo htmlspecialchars(getSetting('hero_bg')); ?>">
                                                <button class="btn btn-indigo px-4 fw-800" type="button">UPLOAD</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- About Section -->
                            <div class="tab-pane fade" id="about">
                                <form method="POST">
                                <input type="hidden" name="update_settings" value="1">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h4 class="fw-800 mb-0">Brand Story Editor</h4>
                                    <button type="submit" class="btn btn-primary px-4 fw-800" style="border-radius: 10px;">Save Story</button>
                                </div>
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Core Narrative Heading</label>
                                            <input type="text" name="about_heading" class="form-control bg-light border-0 fw-600" value="<?php echo htmlspecialchars(getSetting('about_heading')); ?>">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Story Content</label>
                                            <textarea name="about_content" class="form-control bg-light border-0 fw-500" rows="6"><?php echo htmlspecialchars(getSetting('about_content')); ?></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Asset Count (Dynamic Display)</label>
                                            <input type="text" name="active_users_display" class="form-control bg-light border-0 fw-600" value="<?php echo htmlspecialchars(getSetting('active_users_display')); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Financial Velocity Tracking</label>
                                            <input type="text" name="aum_display" class="form-control bg-light border-0 fw-600" value="<?php echo htmlspecialchars(getSetting('aum_display')); ?>">
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Services Grid -->
                            <div class="tab-pane fade" id="services">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h4 class="fw-800 mb-0">Product Grid</h4>
                                    <button class="btn btn-indigo px-4 fw-800" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#serviceModal" onclick="resetServiceModal()"><i class="fa-solid fa-plus me-2"></i> ADD PRODUCT</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th class="border-0 text-muted fw-800 text-xs uppercase">Symbol</th>
                                                <th class="border-0 text-muted fw-800 text-xs uppercase">Product Line</th>
                                                <th class="border-0 text-muted fw-800 text-xs uppercase">Description</th>
                                                <th class="border-0 text-end text-muted fw-800 text-xs uppercase">Resolution</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($services as $service): ?>
                                            <tr class="border-bottom-0">
                                                <td class="py-4">
                                                    <div class="stat-icon <?php echo htmlspecialchars($service['color_class']); ?>" style="width: 40px; height: 40px; border-radius: 12px;">
                                                        <i class="fa-solid <?php echo htmlspecialchars($service['icon']); ?> shadow-sm"></i>
                                                    </div>
                                                </td>
                                                <td class="py-4 fw-800"><?php echo htmlspecialchars($service['title']); ?></td>
                                                <td class="py-4 text-xs fw-600 text-muted"><?php echo htmlspecialchars($service['description']); ?></td>
                                                <td class="py-4 text-end">
                                                    <button class="btn btn-light btn-sm fw-800 px-3 border-0" style="border-radius: 8px;" onclick='editService(<?php echo json_encode($service); ?>)'>EDIT</button>
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="service_action" value="delete">
                                                        <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                                        <button type="submit" class="btn btn-rose-light btn-sm fw-800 px-3 border-0 text-danger ms-2" style="border-radius: 8px;" onclick="return confirm(\'Delete this service?\')">DEL</button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
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

                            <!-- Inner Pages -->
                            <div class="tab-pane fade" id="sc-services">
                                <form method="POST">
                                <input type="hidden" name="update_settings" value="1">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h4 class="fw-800 mb-0">Inner Page Settings</h4>
                                    <button type="submit" class="btn btn-primary px-4 fw-800" style="border-radius: 10px;">Update Pages</button>
                                </div>
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Wealth Management Title</label>
                                            <input type="text" name="wealth_mgmt_title" class="form-control bg-light border-0 fw-600" value="<?php echo htmlspecialchars(getSetting('wealth_mgmt_title', 'Wealth Management Services')); ?>">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Global Markets Description</label>
                                            <textarea name="global_markets_desc" class="form-control bg-light border-0 fw-500" rows="3"><?php echo htmlspecialchars(getSetting('global_markets_desc', 'Access international liquidity and institutional-grade trading protocols.')); ?></textarea>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Contact Center -->
                            <div class="tab-pane fade" id="contact">
                                <form method="POST">
                                <input type="hidden" name="update_settings" value="1">
                                <div class="d-flex justify-content-between align-items-center mb-5">
                                    <h4 class="fw-800 mb-0">Contact Information</h4>
                                    <button type="submit" class="btn btn-primary px-4 fw-800" style="border-radius: 10px;">Save Contact Details</button>
                                </div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Support Email</label>
                                            <input type="email" name="contact_email" class="form-control bg-light border-0 fw-600" value="<?php echo htmlspecialchars(getSetting('contact_email')); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Contact Phone</label>
                                            <input type="text" name="contact_phone" class="form-control bg-light border-0 fw-600" value="<?php echo htmlspecialchars(getSetting('contact_phone')); ?>">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Headquarters Address</label>
                                            <textarea name="contact_address" class="form-control bg-light border-0 fw-500" rows="3"><?php echo htmlspecialchars(getSetting('contact_address')); ?></textarea>
                                        </div>
                                    </div>
                                </form>
                            </div>
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

    <!-- Service Modal -->
    <div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                <div class="modal-header border-0 p-4">
                    <h5 class="modal-title fw-800" id="serviceModalTitle">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="service_action" value="save">
                    <input type="hidden" name="service_id" id="service_id">
                    <div class="modal-body p-4 pt-0">
                        <div class="mb-3">
                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Product Title</label>
                            <input type="text" name="title" id="service_title" class="form-control bg-light border-0 fw-600" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Description</label>
                            <textarea name="description" id="service_description" class="form-control bg-light border-0 fw-500" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-800 text-xs text-uppercase text-muted">Cover Image URL</label>
                            <input type="text" name="image_url" id="service_image" class="form-control bg-light border-0 fw-600" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-800 text-xs text-uppercase text-muted">Icon Class</label>
                                <input type="text" name="icon" id="service_icon" class="form-control bg-light border-0 fw-600" placeholder="fa-gem" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-800 text-xs text-uppercase text-muted">Color Theme</label>
                                <select name="color_class" id="service_color" class="form-select bg-light border-0 fw-600">
                                    <option value="bg-indigo-light text-primary">Indigo / Primary</option>
                                    <option value="bg-emerald-light text-success">Emerald / Success</option>
                                    <option value="bg-amber-light text-warning">Amber / Warning</option>
                                    <option value="bg-rose-light text-danger">Rose / Danger</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light fw-800 px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-800 px-4">Commit Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetServiceModal() {
            document.getElementById('serviceModalTitle').innerText = 'Add New Product';
            document.getElementById('service_id').value = '';
            document.getElementById('service_title').value = '';
            document.getElementById('service_description').value = '';
            document.getElementById('service_image').value = '';
            document.getElementById('service_icon').value = 'fa-gem';
            document.getElementById('service_color').value = 'bg-indigo-light text-primary';
        }

        function editService(service) {
            document.getElementById('serviceModalTitle').innerText = 'Edit Product Line';
            document.getElementById('service_id').value = service.id;
            document.getElementById('service_title').value = service.title;
            document.getElementById('service_description').value = service.description;
            document.getElementById('service_image').value = service.image_url;
            document.getElementById('service_icon').value = service.icon;
            document.getElementById('service_color').value = service.color_class;
            
            var modal = new bootstrap.Modal(document.getElementById('serviceModal'));
            modal.show();
        }
    </script>
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
