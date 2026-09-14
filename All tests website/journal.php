<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$owlMark = '<svg viewBox="0 0 40 40" fill="none" aria-hidden="true"><circle cx="20" cy="22" r="15" fill="#18150f"/><circle cx="15" cy="20" r="5.5" fill="#fff"/><circle cx="25" cy="20" r="5.5" fill="#fff"/><circle cx="15" cy="20" r="2.3" fill="#18150f"/><circle cx="25" cy="20" r="2.3" fill="#18150f"/><path d="M20 25L17 30H23L20 25Z" fill="#fff"/></svg>';

if (empty($_SESSION['journal_csrf_token'])) {
    $_SESSION['journal_csrf_token'] = bin2hex(random_bytes(32));
}
$feedbackMessage = '';
$feedbackClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_appointment'])) {
    $submittedToken = $_POST['csrf_token'] ?? '';
    $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);

    if (!hash_equals($_SESSION['journal_csrf_token'], $submittedToken)) {
        $feedbackMessage = 'Your request could not be verified. Please try again.';
        $feedbackClass = 'error';
    } elseif (!$appointmentId) {
        $feedbackMessage = 'Invalid appointment selected.';
        $feedbackClass = 'error';
    } else {
        try {
            $cancelStmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = :id AND user_id = :user_id AND status IN ('Pending', 'Confirmed')");
            $cancelStmt->execute([
                'id' => $appointmentId,
                'user_id' => $_SESSION['user_id']
            ]);

            if ($cancelStmt->rowCount() === 1) {
                $feedbackMessage = 'Your appointment was cancelled.';
                $feedbackClass = 'success';
            } else {
                $feedbackMessage = 'This appointment is no longer available for cancellation.';
                $feedbackClass = 'error';
            }
        } catch (PDOException $e) {
            $feedbackMessage = 'Unable to cancel the appointment right now.';
            $feedbackClass = 'error';
        }
    }
}

try {
    $stmt = $conn->prepare("\n        SELECT a.*, s.title AS service_title, s.tag AS service_tag\n        FROM appointments a\n        JOIN services s ON a.service_id = s.id\n        WHERE a.user_id = :user_id\n        ORDER BY a.appointment_date ASC, a.appointment_time ASC\n    ");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $myAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalBookings = count($myAppointments);
    $pendingCount = 0;
    $confirmedCount = 0;
    foreach ($myAppointments as $appointment) {
        if ($appointment['status'] === 'Pending') {
            $pendingCount++;
        }
        if ($appointment['status'] === 'Confirmed') {
            $confirmedCount++;
        }
    }
} catch (PDOException $e) {
    $myAppointments = [];
    $totalBookings = 0;
    $pendingCount = 0;
    $confirmedCount = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard — Mono Hoot Studios</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #ffffff;
            --bg-soft: #fbfaf7;
            --ink: #18150f;
            --ink-soft: #6f6a5d;
            --accent: #c6a06a;
            --line: #e9e5db;
            --line-soft: #f1eee6;
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg-soft); color: var(--ink); font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        .wrap { max-width: 1000px; margin: 0 auto; padding: 40px 20px; }
        .dashboard-box { background: var(--bg); border: 1px solid var(--line); border-radius: 14px; padding: 40px; box-shadow: 0 10px 30px rgba(24,21,15,0.03); }
        .dash-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--line-soft); padding-bottom: 30px; margin-bottom: 30px; }
        .brand-profile { display: flex; align-items: center; gap: 14px; }
        .brand-profile .avatar { width: 44px; height: 44px; flex: none; }
        .brand-profile h1 { font-family: 'Fraunces', serif; font-size: 28px; font-weight: 600; margin: 0; }
        .brand-profile p { margin: 4px 0 0; font-size: 14px; color: var(--ink-soft); }
        .nav-actions { display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 10px 20px; border-radius: 999px; font-size: 13.5px; font-weight: 500; border: 1px solid var(--ink); cursor: pointer; text-decoration: none; transition: background .15s, color .15s, border-color .15s; }
        .btn-dark { background: var(--ink); color: #fff; border-color: var(--ink); }
        .btn-dark:hover { background: #000; }
        .btn-ghost { background: transparent; color: var(--ink-soft); border-color: var(--line); }
        .btn-ghost:hover { color: var(--ink); border-color: var(--ink); }
        .btn-danger { background: #fff5f5; color: #e53e3e; border-color: #fed7d7; }
        .btn-danger:hover { background: #e53e3e; color: #fff; border-color: #e53e3e; }
        .analytics-strip { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px; }
        .metric-card { background: var(--bg-soft); border: 1px solid var(--line-soft); border-radius: 10px; padding: 20px; }
        .metric-card .title { font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: var(--ink-soft); }
        .metric-card .value { font-family: 'Fraunces', serif; font-size: 32px; font-weight: 500; color: var(--ink); margin-top: 6px; display: block; }
        .table-wrapper { width: 100%; overflow-x: auto; }
        .appointments-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14.5px; }
        .appointments-table th { padding: 12px 16px; background: var(--bg-soft); color: var(--ink-soft); font-weight: 600; border-bottom: 1px solid var(--line); }
        .appointments-table td { padding: 16px; border-bottom: 1px solid var(--line-soft); vertical-align: top; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 99px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .03em; border: 1px solid transparent; }
        .status-badge.pending { background: #fffbeb; color: #b45309; border-color: #fef3c7; }
        .status-badge.confirmed { background: #e6f7ff; color: #0050b3; border-color: #91d5ff; }
        .status-badge.cancelled { background: #f5f5f5; color: #595959; border-color: #d9d9d9; }
        .cancel-form { margin-top: 10px; }
        .cancel-button { padding: 6px 10px; border: 1px solid #fed7d7; border-radius: 999px; background: #fff5f5; color: #e53e3e; font: inherit; font-size: 11px; cursor: pointer; }
        .cancel-button:hover { background: #e53e3e; color: #fff; }
        .feedback { margin-bottom: 24px; padding: 12px 16px; border-radius: 8px; font-size: 14px; }
        .feedback.success { background: #edf8ef; color: #236b2c; border: 1px solid #c9e8cd; }
        .feedback.error { background: #fff5f5; color: #a12626; border: 1px solid #fed7d7; }
        .empty-dashboard { text-align: center; padding: 60px 20px; }
        .empty-dashboard svg { width: 50px; height: 50px; margin-bottom: 16px; opacity: .8; }
        .empty-text { font-family: 'Fraunces', serif; font-size: 22px; font-style: italic; color: var(--ink-soft); margin: 0; }
        @media (max-width: 768px) {
            .dashboard-box { padding: 24px; }
            .dash-header { flex-direction: column; gap: 20px; }
            .nav-actions { justify-content: flex-start; }
            .analytics-strip { grid-template-columns: 1fr; gap: 12px; }
            .appointments-table th { display: none; }
            .appointments-table td { display: block; padding: 8px 0; border: none; }
            .appointments-table tr { display: block; padding: 16px 0; border-bottom: 1px solid var(--line); }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="dashboard-box">
        <header class="dash-header">
            <div class="brand-profile">
                <div class="avatar"><?= $owlMark ?></div>
                <div>
                    <h1>Welcome, <?= e($_SESSION['username']) ?>!</h1>
                    <p>Client space: track your studio arrangements.</p>
                </div>
            </div>
            <div class="nav-actions">
                <a href="index.php" class="btn btn-ghost">Return Home</a>
                <a href="book.php" class="btn btn-dark">New Booking +</a>
                <a href="logout.php" class="btn btn-danger">Log Out</a>
            </div>
        </header>

        <?php if ($feedbackMessage !== ''): ?>
            <div class="feedback <?= e($feedbackClass) ?>" role="status"><?= e($feedbackMessage) ?></div>
        <?php endif; ?>

        <section class="analytics-strip">
            <div class="metric-card"><span class="title">Total Placed Sessions</span><span class="value"><?= $totalBookings ?></span></div>
            <div class="metric-card"><span class="title">Awaiting Approval</span><span class="value" style="color: <?= $pendingCount > 0 ? '#b45309' : 'inherit' ?>"><?= $pendingCount ?></span></div>
            <div class="metric-card"><span class="title">Confirmed Schedules</span><span class="value" style="color: <?= $confirmedCount > 0 ? '#0050b3' : 'inherit' ?>"><?= $confirmedCount ?></span></div>
        </section>

        <h2 style="font-family: 'Fraunces', serif; font-size: 20px; margin: 0 0 20px;">My Booked Appointments</h2>
        <div class="table-wrapper">
            <?php if (empty($myAppointments)): ?>
                <div class="empty-dashboard">
                    <?= $owlMark ?>
                    <p class="empty-text">None written yet.</p>
                </div>
            <?php else: ?>
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Requested Service</th>
                            <th>Target Date</th>
                            <th>Time Block Slot</th>
                            <th>Project Memo Notes</th>
                            <th style="text-align: right;">Status Flag</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($myAppointments as $appointment): ?>
                            <tr>
                                <td><strong><?= e($appointment['service_title']) ?></strong><br><span style="font-size: 12px; color: var(--ink-soft);"><?= e($appointment['service_tag']) ?></span></td>
                                <td><?= date('M d, Y', strtotime($appointment['appointment_date'])) ?></td>
                                <td><?= $appointment['appointment_time'] === '00:00:00' ? 'Timeline Kickoff' : date('g:i A', strtotime($appointment['appointment_time'])) ?></td>
                                <td><?= empty($appointment['additional_notes']) ? 'No supplementary notes provided.' : e($appointment['additional_notes']) ?></td>
                                <td style="text-align: right;">
                                    <span class="status-badge <?= strtolower(e($appointment['status'])) ?>"><?= e($appointment['status']) ?></span>
                                    <?php if (in_array($appointment['status'], ['Pending', 'Confirmed'], true)): ?>
                                        <form method="POST" class="cancel-form" onsubmit="return confirm('Cancel this appointment?');">
                                            <input type="hidden" name="appointment_id" value="<?= (int) $appointment['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['journal_csrf_token']) ?>">
                                            <button type="submit" name="cancel_appointment" class="cancel-button">Cancel Appointment</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
