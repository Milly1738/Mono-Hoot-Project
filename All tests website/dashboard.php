<?php
// 1. Initialize session memory map
session_start();

// 2. Protect the page: Block anonymous traffic
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 3. Connect to your XAMPP Database structure
require_once 'db.php';

try {
    // Fetch ONLY the bookings belonging to this logged-in account
    $stmt = $conn->prepare("SELECT a.*, s.title AS service_title FROM appointments a JOIN services s ON a.service_id = s.id WHERE a.user_id = :user_id ORDER BY a.appointment_date ASC");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $myAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $myAppointments = [];
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$owlMark = '<svg viewBox="0 0 40 40" fill="none" style="width:40px;height:40px;margin-bottom:15px;"><circle cx="20" cy="22" r="15" fill="#18150f"/><circle cx="15" cy="20" r="5.5" fill="#fff"/><circle cx="25" cy="20" r="5.5" fill="#fff"/><circle cx="15" cy="20" r="2.3" fill="#18150f"/><circle cx="25" cy="20" r="2.3" fill="#18150f"/><path d="M20 25L17 30H23L20 25Z" fill="#fff"/></svg>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard — Mono Hoot Studios</title>
    <link rel="preconnect" href="https://googleapis.com">
    <link rel="preconnect" href="https://gstatic.com" crossorigin>
    <link href="https://googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #ffffff;
            --bg-soft: #fbfaf7;
            --ink: #18150f;
            --ink-soft: #6f6a5d;
            --accent: #c6a06a;
            --border: #e2e8f0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-soft);
            color: var(--ink);
            margin: 0;
            padding: 50px 20px;
            -webkit-font-smoothing: antialiased;
        }
        .dashboard-container {
            max-width: 750px;
            margin: 0 auto;
            background: var(--bg);
            padding: 40px;
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(24,21,15,0.03);
        }
        h1 {
            font-family: 'Fraunces', serif;
            font-weight: 600;
            font-size: 32px;
            margin: 0 0 10px 0;
        }
        .subtext {
            color: var(--ink-soft);
            margin-bottom: 35px;
            font-size: 15px;
        }
        .appointment-item {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
        }
        .details h3 {
            margin: 0 0 6px 0;
            font-size: 17px;
            font-weight: 600;
        }
        .details p {
            margin: 0;
            font-size: 14px;
            color: var(--ink-soft);
        }
        .badge-status {
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            border: 1px solid transparent;
        }
        .badge-status.pending { background: #fffbeb; color: #b45309; border-color: #fef3c7; }
        .badge-status.confirmed { background: #e6f7ff; color: #0050b3; border-color: #91d5ff; }
        .badge-status.cancelled { background: #f5f5f5; color: #595959; border-color: #d9d9d9; }
        
        .btn-wrapper {
            margin-top: 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 22px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-home { background: var(--ink); color: #fff; }
        .btn-home:hover { background: #000; }
        .btn-logout { background: #fff5f5; color: #e53e3e; border: 1px solid #fed7d7; }
        .btn-logout:hover { background: #e53e3e; color: #fff; }
    </style>
</head>
<body>

<div class="dashboard-container">
    <?= $owlMark ?>
    <h1>Welcome, <?= e($_SESSION['username']) ?>! 👋</h1>
    <p class="subtext">Review and manage your scheduled session timelines below.</p>

    <h2 style="font-family:'Fraunces', serif; font-size:20px; margin-bottom:15px;">My Booked Sessions</h2>

    <?php if (empty($myAppointments)): ?>
        <div style="text-align:center; padding:30px; border:1px dashed var(--border); border-radius:10px; color:var(--ink-soft); font-size:14px;">
            You have not booked any appointments yet.
        </div>
    <?php else: ?>
        <?php foreach ($myAppointments as $app): ?>
            <div class="appointment-item">
                <div class="details">
                    <h3><?= e($app['service_title']) ?></h3>
                    <p>
                        Date: <strong><?= date('M d, Y', strtotime($app['appointment_date'])) ?></strong> 
                        <?php if ($app['appointment_time'] !== '00:00:00'): ?>
                            • Time: <strong><?= date('g:i A', strtotime($app['appointment_time'])) ?></strong>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <span class="badge-status <?= strtolower($app['status']) ?>"><?= e($app['status']) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="btn-wrapper">
        <a href="index.php" class="btn btn-home">← Back to Homepage</a>
        <a href="logout.php" class="btn btn-logout">Log Out</a>
    </div>
</div>

</body>
</html>
