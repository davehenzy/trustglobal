<?php 
require_once '../includes/db.php';
require_once '../includes/user-check.php'; 

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ticket'])) {
    $subject = cleanInput($_POST['subject']);
    $priority = cleanInput($_POST['priority']);
    $message = cleanInput($_POST['message']);

    if ($subject && $message) {
        try {
            $pdo->beginTransaction();
            
            // Create Ticket
            $stmt = $pdo->prepare("INSERT INTO support_tickets (user_id, subject, status) VALUES (?, ?, 'Open')");
            $stmt->execute([$user_id, $subject]);
            $ticket_id = $pdo->lastInsertId();

            // Create Initial Message
            $stmt = $pdo->prepare("INSERT INTO messages (ticket_id, sender_id, message, is_admin) VALUES (?, ?, ?, 0)");
            $stmt->execute([$ticket_id, $user_id, $message]);

            $pdo->commit();
            $success_msg = "Ticket #TK-" . str_pad($ticket_id, 5, '0', STR_PAD_LEFT) . " created successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_msg = "Error creating ticket: " . $e->getMessage();
        }
    } else {
        $error_msg = "Please fill in all required fields.";
    }
}

// Fetch user tickets
$stmt = $pdo->prepare("SELECT * FROM support_tickets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$user_tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Center - SwiftCapital</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .support-form-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            margin-bottom: 40px;
        }

        .support-icon-header {
            width: 80px;
            height: 80px;
            background-color: #0ea5e9;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 30px;
            box-shadow: 0 8px 16px rgba(14, 165, 233, 0.2);
        }

        .support-info-alert {
            background-color: #f0f9ff;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            border: 1px solid #e0f2fe;
        }

        .support-input-group i.left-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .support-input-group input, 
        .support-input-group select, 
        .support-input-group textarea {
            width: 100%;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 14px 15px 14px 45px;
            font-size: 0.95rem;
            color: var(--text-dark);
            outline: none;
            transition: all 0.2s;
            background: #fff;
        }

        .support-input-group textarea {
            padding-top: 15px;
            resize: none;
        }

        .support-input-group i.textarea-icon {
            top: 25px;
            transform: none;
        }

        .support-input-group input:focus, 
        .support-input-group select:focus, 
        .support-input-group textarea:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
        }

        .support-info-alert {
            background-color: #eff6ff;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .support-info-alert i {
            color: #3b82f6;
            font-size: 1.1rem;
            margin-top: 3px;
        }

        .support-info-alert h6 {
            color: #1e40af;
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 4px;
        }

        .support-info-alert p {
            color: #1e40af;
            font-size: 0.85rem;
            margin-bottom: 0;
            line-height: 1.5;
            opacity: 0.9;
        }

        .btn-submit-ticket {
            background-color: #0ea5e9;
            color: #fff;
            border: none;
            padding: 14px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
        }

        .btn-submit-ticket:hover {
            background-color: #0284c7;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);
        }
    </style>
</head>
<body>

<?php 
$page = 'support';
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
                        <div class="header-icon-circle" style="background: #0ea5e9;">
                            <i class="fa-solid fa-circle-question"></i>
                        </div>
                        <h1 class="page-title-centered">Support Center</h1>
                        <p class="page-subtitle-centered">We're here to help. Tell us about your issue and we'll find a solution as quickly as possible.</p>
                    </div>

                    <?php if($success_msg): ?>
                        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 fw-600">
                            <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success_msg; ?>
                        </div>
                    <?php endif; ?>

                    <?php if($error_msg): ?>
                        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 fw-600">
                            <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $error_msg; ?>
                        </div>
                    <?php endif; ?>

                    <div class="support-form-card">
                        
                        <div class="support-form-section-title">
                            <i class="fa-solid fa-globe"></i> Submit a Support Ticket
                        </div>
                        <p class="text-muted mb-5" style="font-size: 0.9rem;">We're here to help. Tell us about your issue and we'll find a solution.</p>

                        <div class="support-icon-header">
                            <i class="fa-solid fa-comment-dots"></i>
                        </div>

                        <form method="POST">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label">Subject <span class="req">*</span></label>
                                <div class="custom-input-group">
                                    <input type="text" name="subject" placeholder="What can we help you with?" required>
                                    <i class="fa-solid fa-pen-to-square left-icon"></i>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Priority Level <span class="req">*</span></label>
                                <div class="custom-input-group">
                                    <select name="priority" required>
                                        <option value="Medium Priority" selected>Medium Priority</option>
                                        <option>Low Priority</option>
                                        <option>High Priority</option>
                                        <option>Urgent</option>
                                    </select>
                                    <i class="fa-solid fa-flag left-icon"></i>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Describe Your Issue <span class="req">*</span></label>
                                <div class="custom-input-group">
                                    <textarea name="message" rows="6" placeholder="Please provide all relevant details..." required></textarea>
                                    <i class="fa-solid fa-comment-dots left-icon" style="top: 25px;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="support-info-alert mt-5">
                            <i class="fa-solid fa-circle-info"></i>
                            <div class="support-info-alert-content">
                                <h6>Support Information</h6>
                                <p>Our support team typically responds within 24 hours. For urgent matters, please select "High Priority".</p>
                            </div>
                        </div>

                        <div class="d-grid shadow-sm" style="border-radius: 12px; overflow: hidden;">
                            <button type="submit" name="submit_ticket" class="btn btn-primary btn-lg fw-bold py-3" style="border-radius: 0;">
                                <i class="fa-solid fa-paper-plane me-2"></i> Submit Ticket
                            </button>
                        </div>
                        </form>
                    </div>

                    <!-- Previous Tickets -->
                    <div class="support-form-card">
                        <div class="support-form-section-title mb-4">
                            <i class="fa-solid fa-history"></i> Your Support History
                        </div>

                        <?php if(empty($user_tickets)): ?>
                            <div class="text-center py-5">
                                <i class="fa-solid fa-folder-open text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="text-muted fw-600">No support tickets found.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table custom-table">
                                    <thead>
                                        <tr>
                                            <th>Ticket ID</th>
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($user_tickets as $ticket): ?>
                                            <tr>
                                                <td><span class="fw-bold">#TK-<?php echo str_pad($ticket['id'], 5, '0', STR_PAD_LEFT); ?></span></td>
                                                <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                                                <td>
                                                    <?php 
                                                        $status = $ticket['status'];
                                                        $class = 'bg-secondary';
                                                        if($status == 'Open') $class = 'bg-primary';
                                                        elseif($status == 'Resolved') $class = 'bg-success';
                                                        elseif($status == 'Pending') $class = 'bg-warning';
                                                    ?>
                                                    <span class="badge <?php echo $class; ?>"><?php echo $status; ?></span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></td>
                                                <td>
                                                    <a href="ticket-view.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
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
    </script>
</body>
</html>
