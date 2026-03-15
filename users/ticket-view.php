<?php 
require_once '../includes/db.php';
require_once '../includes/user-check.php'; 

$user_id = $_SESSION['user_id'];
$ticket_id = $_GET['id'] ?? null;

if (!$ticket_id) {
    header("Location: support.php");
    exit;
}

// Fetch Ticket
$stmt = $pdo->prepare("SELECT * FROM support_tickets WHERE id = ? AND user_id = ?");
$stmt->execute([$ticket_id, $user_id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    header("Location: support.php");
    exit;
}

// Handle Reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $message = cleanInput($_POST['message']);
    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO messages (ticket_id, sender_id, message, is_admin) VALUES (?, ?, ?, 0)");
        $stmt->execute([$ticket_id, $user_id, $message]);
        
        // If ticket was resolved, reopening it might be logical, but let's keep it simple
        if ($ticket['status'] == 'Resolved') {
            $pdo->prepare("UPDATE support_tickets SET status = 'Open' WHERE id = ?")->execute([$ticket_id]);
        }
        
        header("Location: ticket-view.php?id=" . $ticket_id);
        exit;
    }
}

// Fetch Messages
$stmt = $pdo->prepare("SELECT * FROM messages WHERE ticket_id = ? ORDER BY created_at ASC");
$stmt->execute([$ticket_id]);
$messages = $stmt->fetchAll();

$initials = strtoupper(substr($_SESSION['name'], 0, 1) . substr($_SESSION['lastname'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?php echo str_pad($ticket['id'], 5, '0', STR_PAD_LEFT); ?> - SwiftCapital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .chat-container {
            height: 500px;
            overflow-y: auto;
            background: #f8fafc;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .message-bubble {
            max-width: 80%;
            padding: 15px 20px;
            border-radius: 18px;
            position: relative;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .message-user {
            align-self: flex-end;
            background: #3b82f6;
            color: white;
            border-bottom-right-radius: 4px;
        }
        .message-admin {
            align-self: flex-start;
            background: white;
            color: #1e293b;
            border-bottom-left-radius: 4px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }
        .message-time {
            font-size: 0.7rem;
            margin-top: 5px;
            display: block;
            opacity: 0.7;
        }
        .ticket-header-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            border: 1px solid #e2e8f0;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .reply-box {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            margin-top: 25px;
        }
    </style>
</head>
<body>

<?php 
$page = 'support';
include '../includes/user-sidebar.php'; 
?>

    <main class="main-content">
        <!-- Top Navbar -->
        <?php include '../includes/user-navbar.php'; ?>

        <div class="page-container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    
                    <div class="ticket-header-card shadow-sm">
                        <div>
                            <span class="text-xs text-muted fw-bold">TICKET #<?php echo str_pad($ticket['id'], 5, '0', STR_PAD_LEFT); ?></span>
                            <h4 class="fw-800 mb-0"><?php echo htmlspecialchars($ticket['subject']); ?></h4>
                        </div>
                        <?php 
                            $status = $ticket['status'];
                            $class = 'bg-secondary';
                            if($status == 'Open') $class = 'bg-primary';
                            elseif($status == 'Resolved') $class = 'bg-success';
                            elseif($status == 'Pending') $class = 'bg-warning';
                        ?>
                        <span class="badge <?php echo $class; ?> px-3 py-2 fw-800" style="border-radius: 8px;"><?php echo $status; ?></span>
                    </div>

                    <div class="chat-container shadow-inner" id="chatBox">
                        <?php foreach($messages as $msg): ?>
                            <div class="message-bubble <?php echo $msg['is_admin'] ? 'message-admin' : 'message-user'; ?>">
                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                <span class="message-time"><?php echo date('H:i, d M', strtotime($msg['created_at'])); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if($ticket['status'] != 'Resolved'): ?>
                    <div class="reply-box shadow-sm">
                        <form method="POST">
                            <textarea name="message" class="form-control border-0 bg-light p-3 mb-3 shadow-none" rows="3" placeholder="Type your message here..." style="border-radius: 12px; resize: none;"></textarea>
                            <div class="d-flex justify-content-end">
                                <button type="submit" name="send_message" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 10px;">
                                    <i class="fa-solid fa-paper-plane me-2"></i> Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info border-0 rounded-4 mt-4 text-center fw-600">
                            This ticket has been marked as <strong>Resolved</strong>. If you still need help, simply send a new message to reopen it.
                            <div class="mt-3">
                                <form method="POST">
                                    <textarea name="message" class="form-control border-0 bg-light p-3 mb-3 shadow-none" rows="2" placeholder="Send a message to reopen..." style="border-radius: 12px; resize: none;"></textarea>
                                    <button type="submit" name="send_message" class="btn btn-sm btn-primary px-4 fw-bold">Reopen Ticket</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateNodes = document.querySelectorAll('#currentDate');
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = now.toLocaleDateString('en-US', options);
            dateNodes.forEach(node => node.textContent = formattedDate);
            
            const chatBox = document.getElementById('chatBox');
            chatBox.scrollTop = chatBox.scrollHeight;
        });
    </script>
</body>
</html>
