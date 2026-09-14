<?php
// 1. Initialize session variables
session_start();

// 2. Security Check: Block non-admins from poking around
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// 3. FIRST CONNECT TO THE DATABASE ARCHITECTURE 
require_once 'db.php'; 

$feedbackMessage = "";
$feedbackClass = "";

// --- APPOINTMENT STATUS PROCESSOR LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_appointment_status'])) {
    $appointmentId = intval($_POST['appointment_id']);
    $newStatus = trim($_POST['status_action']); // Will be 'Confirmed' or 'Cancelled'

    try {
        $statusUpdateStmt = $conn->prepare("UPDATE appointments SET status = :status WHERE id = :id");
        $statusUpdateStmt->execute(['status' => $newStatus, 'id' => $appointmentId]);
        
        $feedbackMessage = "Appointment status updated to " . htmlspecialchars($newStatus) . " successfully.";
        $feedbackClass = "success";
    } catch (PDOException $e) {
        $feedbackMessage = "Failed to update appointment: " . $e->getMessage();
        $feedbackClass = "error";
    }
}

// --- ADD NEW SERVICE LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_service'])) {
    $tag = trim($_POST['tag']);
    $title = trim($_POST['title']);
    $desc = trim($_POST['desc']);
    $priceInput = $_POST['price'] ?? '';
    $price = (float) $priceInput;

    if (empty($tag) || empty($title) || empty($desc) || $priceInput === '') {
        $feedbackMessage = "All service form fields are required.";
        $feedbackClass = "error";
    } else {
        try {
            $addServiceStmt = $conn->prepare("INSERT INTO services (tag, title, `desc`, price) VALUES (:tag, :title, :desc, :price)");
            $addServiceStmt->execute(['tag' => $tag, 'title' => $title, 'desc' => $desc, 'price' => $price]);
            $feedbackMessage = "New service successfully added!";
            $feedbackClass = "success";
        } catch (PDOException $e) {
            $feedbackMessage = "Failed to add service: " . $e->getMessage();
            $feedbackClass = "error";
        }
    }
}

// --- DELETE SERVICE LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_service_id'])) {
    $delServiceId = intval($_POST['delete_service_id']);
    try {
        $delServiceStmt = $conn->prepare("DELETE FROM services WHERE id = :id");
        $delServiceStmt->execute(['id' => $delServiceId]);
        $feedbackMessage = "Service removed successfully.";
        $feedbackClass = "success";
    } catch (PDOException $e) {
        $feedbackMessage = "Failed to remove service: " . $e->getMessage();
        $feedbackClass = "error";
    }
}

// --- TOGGLE READ/UNREAD PROCESSING LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_read_id'])) {
    $submissionId = intval($_POST['toggle_read_id']);
    $currentStatus = intval($_POST['current_status']);
    $newStatus = ($currentStatus === 1) ? 0 : 1; 

    try {
        $statusStmt = $conn->prepare("UPDATE contact_submissions SET is_read = :is_read WHERE id = :id");
        $statusStmt->execute(['is_read' => $newStatus, 'id' => $submissionId]);
        $feedbackMessage = "Submission status updated successfully.";
        $feedbackClass = "success";
    } catch (PDOException $e) {
        $feedbackMessage = "Failed to update message status: " . $e->getMessage();
        $feedbackClass = "error";
    }
}

// --- DELETE SUBMISSION PROCESSING LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);
    try {
        $deleteStmt = $conn->prepare("DELETE FROM contact_submissions WHERE id = :id");
        $deleteStmt->execute(['id' => $deleteId]);
        $feedbackMessage = "Submission deleted successfully.";
        $feedbackClass = "success";
    } catch (PDOException $e) {
        $feedbackMessage = "Failed to delete submission: " . $e->getMessage();
        $feedbackClass = "error";
    }
}

// 4. Query data from the database safely
try {
    // Fetch active appointment requests cleanly
    $appStmt = $conn->query("SELECT a.*, s.title AS service_title FROM appointments a JOIN services s ON a.service_id = s.id ORDER BY a.appointment_date ASC");
    $appointmentsList = $appStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch live services
    $serviceQuery = $conn->query("SELECT * FROM services ORDER BY id DESC");
    $servicesList = $serviceQuery->fetchAll(PDO::FETCH_ASSOC);

    // Fetch contact forms
    $stmt = $conn->query("SELECT * FROM contact_submissions ORDER BY submitted_at DESC");
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $unreadCount = 0;
    foreach ($submissions as $msg) {
        if ($msg['is_read'] == 0) {
            $unreadCount++;
        }
    }
} catch (PDOException $e) {
    die("Database communication failure: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Mono Hoot Studios</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #f8f9fa;
            --bg-card: #ffffff;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --accent: #c6a06a;
            --border: #e2e8f0;
        }
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg-body); 
            color: var(--text-dark); 
            margin: 0; 
            padding: 40px 20px; 
        }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
        }
        header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 2px solid var(--border); 
            padding-bottom: 20px; 
            margin-bottom: 30px; 
        }
        h1 { 
            font-family: 'Fraunces', serif; 
            font-weight: 600; 
            margin: 0; 
            font-size: 28px; 
        }
        h2 { 
            font-family: 'Fraunces', serif; 
            font-size: 22px; 
            margin: 0 0 15px 0; 
            border-bottom: 1px dashed var(--border); 
            padding-bottom: 8px; 
        }
        .btn-exit { 
            padding: 8px 16px; 
            background: #64748b; 
            color: white; 
            text-decoration: none; 
            border-radius: 6px; 
            font-size: 14px; 
        }
        .alert { 
            padding: 12px; 
            margin-bottom: 20px; 
            border-radius: 6px; 
            font-weight: 500; 
            text-align: center; 
        }
        .alert.success { 
            background: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb; 
        }
        .alert.error { 
            background: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb; 
        }
        .management-section { 
            background: var(--bg-card); 
            border: 1px solid var(--border); 
            border-radius: 12px; 
            padding: 24px; 
            margin-bottom: 35px; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); 
        }
        .service-form { 
            display: grid; 
            grid-template-columns: 80px 200px 1fr auto; 
            gap: 12px; 
            align-items: flex-end; 
            margin-bottom: 20px; 
        }
        .form-group-admin { 
            display: flex; 
            flex-direction: column; 
            gap: 4px; 
        }
        .form-group-admin label { 
            font-size: 12px; 
            font-weight: 600; 
            color: var(--text-muted); 
        }
        .form-input-admin { 
            padding: 8px 12px; 
            border: 1px solid var(--border); 
            border-radius: 6px; 
            font-family: inherit; 
            font-size: 14px; 
        }
        .btn-admin-submit { 
            padding: 9px 18px; 
            background: var(--text-dark); 
            color: white; 
            border: none; 
            border-radius: 6px; 
            font-weight: 500; 
            cursor: pointer; 
        }
        .services-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            font-size: 14px; 
        }
        .services-table th { 
            text-align: left; 
            padding: 10px; 
            background: #f1f5f9; 
            color: var(--text-muted); 
            font-weight: 600; 
        }
        .services-table td { 
            padding: 12px 10px; 
            border-bottom: 1px solid var(--border); 
        }
        .meta-counters { 
            display: flex; 
            gap: 20px; 
            font-size: 14px; 
            color: var(--text-muted); 
            margin-bottom: 20px; 
        }
        .message-card { 
            background: var(--bg-card); 
            border: 1px solid var(--border); 
            border-radius: 12px; 
            padding: 24px; 
            margin-bottom: 20px; 
            position: relative; 
        }
        .message-card.unread { 
            border-left: 5px solid var(--accent); 
        }
        .card-top { 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start; 
            margin-bottom: 12px; 
            border-bottom: 1px dashed var(--border); 
            padding-bottom: 12px; 
            padding-right: 220px; 
        }
        .sender-info h3 { 
            margin: 0 0 4px 0; 
            font-size: 18px; 
            font-weight: 600; 
        }
        .sender-info a { 
            color: var(--accent); 
            text-decoration: none; 
            font-size: 14px; 
        }
        .timestamp { 
            font-size: 12.5px; 
            color: var(--text-muted); 
            display: flex; 
            flex-direction: column; 
            align-items: flex-end; 
            gap: 4px; 
        }
        .badge { 
            font-size: 11px; 
            padding: 2px 8px; 
            border-radius: 99px; 
            font-weight: 600; 
            text-transform: uppercase; 
        }
        .badge.unread-badge { 
            background: #fffbeb; 
            color: #b45309; 
            border: 1px solid #fef3c7; 
        }
        .badge.read-badge { 
            background: #f1f5f9; 
            color: #475569; 
        }
        .message-body { 
            font-size: 15px; 
            line-height: 1.6; 
            color: #334155; 
            white-space: pre-wrap; 
            margin: 0; 
        }
        .actions-container { 
            position: absolute; 
            right: 24px;  top: 24px; 
            display: flex; 
            gap: 8px; 
        }
        .btn-action { 
            border: 1px solid var(--border); 
            padding: 6px 12px; 
            border-radius: 6px; 
            font-size: 13px; 
            font-weight: 500; 
            cursor: pointer; 
            background: #fff; 
        }
        .btn-action.delete { 
            background: #fff5f5; 
            color: #e53e3e; 
            border-color: #fed7d7; 
        }
        .btn-action.delete:hover { 
            background: #e53e3e; 
            color: #fff; 
        }
        /* Interactive Appointment Status Buttons */
        .btn-status-action {
            padding: 4px 10px; font-size: 12px; font-weight: 600; border-radius: 4px;
            cursor: pointer; border: 1px solid transparent; transition: all 0.15s ease;
        }
        .btn-status-action.accept { background: #f6ffed; color: #389e0d; border-color: #b7eb8f; }
        .btn-status-action.accept:hover { background: #389e0d; color: #ffffff; border-color: #389e0d; }
        .btn-status-action.reject { background: #fff1f0; color: #cf1322; border-color: #ffa39e; }
        .btn-status-action.reject:hover { background: #cf1322; color: #ffffff; border-color: #cf1322; }
        .badge-status { display: inline-block; padding: 3px 8px; border-radius: 99px; font-size: 11px; font-weight: 600; text-transform: uppercase; border: 1px solid transparent; }
        .badge-status.pending { background: #fffbeb; color: #b45309; border-color: #fef3c7; }
        .badge-status.confirmed { background: #e6f7ff; color: #0050b3; border-color: #91d5ff; }
        .badge-status.cancelled { background: #f5f5f5; color: #595959; border-color: #d9d9d9; }
        .empty-state { 
            text-align: center; 
            padding: 40px; 
            color: var(--text-muted); 
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <div>
                <h1>Studio Management Console</h1>
                <div style="font-size:14px; color:var(--text-muted); margin-top:4px;">Secure Administrative Node</div>
            </div>
            <a href="index.php" class="btn-exit">Return to Site</a>
        </header>

        <?php if (!empty($feedbackMessage)): ?>
            <div class="alert <?php echo $feedbackClass; ?>">
                <?php echo htmlspecialchars($feedbackMessage); ?>
            </div>
        <?php endif; ?>

        <div class="management-section">
            <h2>Offerings &amp; Services Manager</h2>
            
            <form action="admin.php" method="POST" class="service-form">
                <div class="form-group-admin">
                    <label for="tag">Tag/Icon</label>
                    <input type="text" id="tag" name="tag" class="form-input-admin" placeholder="e.g., Wd, Ps" required>
                </div>
                <div class="form-group-admin">
                    <label for="title">Service Title</label>
                    <input type="text" id="title" name="title" class="form-input-admin" placeholder="e.g., Photoshoots" required>
                </div>
                <div class="form-group-admin">
                    <label for="desc">Short Description</label>
                    <input type="text" id="desc" name="desc" class="form-input-admin" placeholder="Explain the service details briefly..." required>
                </div>
                <div class="form-group-admin">
                    <label for="price">Base Price (PHP)</label>
                    <input type="number" id="price" name="price" class="form-input-admin" placeholder="e.g., 8500" step="0.01" required>
                </div>
                <button type="submit" name="add_service" class="btn-admin-submit">Add Service +</button>
            </form>

            <?php if (empty($servicesList)): ?>
                <p class="empty-state">No services added yet. Fill out the form above to display one.</p>
            <?php else: ?>
                <table class="services-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Icon</th>
                            <th style="width: 200px;">Title</th>
                            <th>Description</th>
                            <th style="width: 100px; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($servicesList as $srv): ?>
                            <tr>
                                <td><strong style="background:#f1f5f9; padding:4px 8px; border-radius:4px; font-size:12px;"><?php echo htmlspecialchars($srv['tag']); ?></strong></td>
                                <td><strong><?php echo htmlspecialchars($srv['title']); ?></strong></td>
                                <td style="color: var(--text-muted);"><?php echo htmlspecialchars($srv['desc']); ?></td>
                                <td style="text-align: right;">
                                    <form action="admin.php" method="POST" style="display:inline;" onsubmit="return confirm('Remove this service from live user display?');">
                                        <input type="hidden" name="delete_service_id" value="<?php echo $srv['id']; ?>">
                                        <button type="submit" class="btn-action delete" style="padding:4px 8px; font-size:12px;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="management-section">
            <h2>Upcoming Bookings Schedule</h2>
            <?php if (empty($appointmentsList)): ?>
                <p class="empty-state">No active appointment requests logged yet.</p>
            <?php else: ?>
                <table style="width:100%; border-collapse:collapse; font-size:14px; text-align:left;">
                    <thead>
                        <tr style="background:#f1f5f9; color:#64748b;">
                            <th style="padding:10px;">Client</th>
                            <th style="padding:10px;">Service</th>
                            <th style="padding:10px;">Date &amp; Time</th>
                            <th style="padding:10px;">Notes</th>
                            <th style="padding:10px; text-align:right;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointmentsList as $app): ?>
                            <tr style="border-bottom:1px solid #e2e8f0;">
                                <td style="padding:12px 10px;"><strong><?= htmlspecialchars($app['client_name']) ?></strong><br><span style="font-size:12px; color:#64748b;"><?= htmlspecialchars($app['client_email']) ?> | <?= htmlspecialchars($app['client_phone']) ?></span></td>
                                <td style="padding:12px 10px; font-weight:600; color:#c6a06a;"><?= htmlspecialchars($app['service_title']) ?></td>
                                <td style="padding:12px 10px;"><?= date('M d, Y', strtotime($app['appointment_date'])) ?><br><span style="font-size:12px; color:#64748b;"><?= date('g:i A', strtotime($app['appointment_time'])) ?></span></td>
                                <td style="padding:12px 10px; color:#64748b; font-size:13px; max-width:250px;"><?= htmlspecialchars($app['additional_notes']) ?></td>
                                <td style="padding:12px 10px; text-align:right;">
                                    <?php if ($app['status'] === 'Pending'): ?>
                                        <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                            <form action="admin.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="appointment_id" value="<?= (int) $app['id'] ?>">
                                                <input type="hidden" name="status_action" value="Confirmed">
                                                <button type="submit" name="update_appointment_status" class="btn-status-action accept">Accept &#10003;</button>
                                            </form>
                                            <form action="admin.php" method="POST" style="display:inline;" onsubmit="return confirm('Reject and cancel this appointment session?');">
                                                <input type="hidden" name="appointment_id" value="<?= (int) $app['id'] ?>">
                                                <input type="hidden" name="status_action" value="Cancelled">
                                                <button type="submit" name="update_appointment_status" class="btn-status-action reject">Reject &#10005;</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge-status <?= htmlspecialchars(strtolower($app['status'])) ?>"><?= htmlspecialchars($app['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <h2>Client Contact Form Messages</h2>
        <div class="meta-counters">
            <div>Total submissions: <strong><?php echo count($submissions); ?></strong></div>
            <div>•</div>
            <div style="color: <?php echo $unreadCount > 0 ? '#b45309' : 'inherit'; ?>">Unread messages: <strong><?php echo $unreadCount; ?></strong></div>
        </div>

        <?php if (empty($submissions)): ?>
            <div class="empty-state"><h3>No messages found</h3><p>Inbound client submissions will populate here.</p></div>
        <?php else: ?>
            <?php foreach ($submissions as $msg): ?>
                <div class="message-card <?php echo $msg['is_read'] == 0 ? 'unread' : ''; ?>">
                    <div class="actions-container">
                        <form action="admin.php" method="POST" style="display:inline;">
                            <input type="hidden" name="toggle_read_id" value="<?php echo $msg['id']; ?>">
                            <input type="hidden" name="current_status" value="<?php echo $msg['is_read']; ?>">
                            <button type="submit" class="btn-action toggle-read"><?php echo $msg['is_read'] == 1 ? 'Mark Unread ⚑' : 'Mark Read ✓'; ?></button>
                        </form>
                        <form action="admin.php" method="POST" style="display:inline;" onsubmit="return confirm('Permanently delete this message?');">
                            <input type="hidden" name="delete_id" value="<?php echo $msg['id']; ?>">
                            <button type="submit" class="btn-action delete">Delete ✕</button>
                        </form>
                    </div>
                    <div class="card-top">
                        <div class="sender-info">
                            <h3><?php echo htmlspecialchars($msg['full_name']); ?></h3>
                            <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>"><?php echo htmlspecialchars($msg['email']); ?> ✉</a>
                        </div>
                        <div class="timestamp">
                            <div><?php echo date('M d, Y — g:i A', strtotime($msg['submitted_at'])); ?></div>
                            <div><?php echo $msg['is_read'] == 0 ? '<span class="badge unread-badge">New</span>' : '<span class="badge read-badge">Read</span>'; ?></div>
                        </div>
                    </div>
                    <p class="message-body"><?php echo htmlspecialchars($msg['message']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>
