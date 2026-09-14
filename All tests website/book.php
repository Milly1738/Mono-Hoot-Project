<?php
session_start();
require_once 'db.php';

$message = "";
$messageClass = "";

// 1. Fetch available services dynamically out of the database table layer
try {
    $servicesStmt = $conn->query("SELECT * FROM services ORDER BY title ASC");
    $servicesList = $servicesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database communication failure: " . $e->getMessage());
}

// 2. Handle Booking Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_booking'])) {
    $serviceId = intval($_POST['service_id']);
    $clientName = trim($_POST['client_name']);
    $clientEmail = trim($_POST['client_email']);
    $clientPhone = trim($_POST['client_phone']);
    $appDate = $_POST['appointment_date'];
    $appTime = $_POST['appointment_time'];
    $notes = trim($_POST['additional_notes']);
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if (empty($serviceId) || empty($clientName) || empty($clientEmail) || empty($clientPhone) || empty($appDate) || empty($appTime)) {
        $message = "Please complete all required fields.";
        $messageClass = "error";
    } elseif (!filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageClass = "error";
    } elseif (!preg_match("/^(09|\+639)\d{9}$/", $clientPhone)) {
        $message = "Invalid Philippine phone number structure.";
        $messageClass = "error";
    } elseif (!preg_match("/^[A-Za-z\s\.\-]+$/", $clientName)) {
        // Enforce letters only for names on the server side
        $message = "Please enter a valid full name (letters and spaces only).";
        $messageClass = "error";
    } elseif (strlen($clientName) < 2) {
        $message = "Name must be at least 2 characters long.";
        $messageClass = "error";
    } elseif ($appTime !== "00:00:00" && ($appTime < "09:00:00" || $appTime > "20:00:00")) {
        $message = "Please select a time between 9:00 AM and 8:00 PM.";
        $messageClass = "error";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO appointments (user_id, service_id, client_name, client_email, client_phone, appointment_date, appointment_time, additional_notes) VALUES (:user_id, :service_id, :client_name, :client_email, :client_phone, :appointment_date, :appointment_time, :notes)");
            $stmt->execute([
                'user_id' => $userId,
                'service_id' => $serviceId,
                'client_name' => $clientName,
                'client_email' => $clientEmail,
                'client_phone' => $clientPhone,
                'appointment_date' => $appDate,
                'appointment_time' => $appTime,
                'notes' => $notes
            ]);
            $message = "Your booking request has been submitted successfully! Redirecting home...";
            $messageClass = "success";
            echo "<meta http-equiv='refresh' content='3;url=index.php'>";
        } catch (PDOException $e) {
            $message = "Booking failed: " . $e->getMessage();
            $messageClass = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book an Appointment — Mono Hoot Studios</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;1,9..144,500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg:#ffffff;
            --bg-soft:#fbfaf7;
            --ink:#18150f;
            --ink-soft:#6f6a5d;
            --accent:#c6a06a;
            --line:#e9e5db;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-soft); color: var(--ink); margin:0; padding: 50px 20px; }
        .booking-container { max-width: 550px; margin: 0 auto; background: var(--bg); padding: 40px; border-radius: 14px; border: 1px solid var(--line); box-shadow: 0 4px 20px rgba(24,21,15,0.04); }
        h1 { font-family: 'Fraunces', serif; font-weight: 500; font-size: 32px; margin: 0 0 10px 0; text-align: center; }
        h1 span { color: var(--accent); font-style: italic; }
        .subtext { font-size: 14.5px; color: var(--ink-soft); text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13.5px; font-weight: 600; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 12px 16px; border: 1px solid var(--line); border-radius: 8px; font-family: inherit; font-size: 14.5px; background: var(--bg); color: var(--ink); box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: var(--accent); }
        textarea.form-control { resize: vertical; min-height: 100px; }
        .btn { width: 100%; padding: 14px; background: var(--ink); color: #fff; border: none; border-radius: 999px; font-size: 16px; font-weight: 500; cursor: pointer; transition: background 0.18s; }
        .btn:hover { background: #000; }
        .btn-back { display: block; text-align: center; margin-top: 20px; color: var(--ink-soft); font-size: 14px; text-decoration: none; }
        .btn-back:hover { color: var(--ink); }
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 6px; font-weight: 500; text-align: center; }
        .alert.error { background: #f8d7da; color: #721c24; }
        .alert.success { background: #d4edda; color: #155724; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    </style>
</head>
<body>

<div class="booking-container">
    <h1>Book an <span>Appointment</span></h1>
    <p class="subtext">Secure your session with our creative studio team today.</p>

    <?php if (!empty($message)): ?>
        <div class="alert <?= $messageClass; ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form action="book.php" method="POST">
        <div class="form-group">
            <label for="service_id">Select Offering / Service *</label>
            <select id="service_id" name="service_id" class="form-control" required>
                <option value="">-- Choose a service --</option>
                <?php foreach ($servicesList as $srv): ?>
                    <option value="<?= $srv['id'] ?>"><?= htmlspecialchars($srv['title']) ?> (<?= htmlspecialchars($srv['tag']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="client_name">Full Name</label>
            <input type="text" id="client_name" name="client_name" class="form-control" 
                   value="<?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '' ?>" 
                   pattern="^[A-Za-z\s\.\-]+$" 
                   title="Please enter a valid name. Letters, spaces, dots, and hyphens are only allowed." 
                   placeholder="e.g., Juan Dela Cruz" 
                   required>
        </div>

        <div class="form-group">
            <label for="client_email">Email Address *</label>
            <input type="email" id="client_email" name="client_email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="client_phone">Phone Number (Philippines)</label>
            <input type="tel" id="client_phone" name="client_phone" class="form-control" 
                   pattern="^(09|\+639)\d{9}$" 
                   placeholder="e.g., 09171234567 or +639171234567" 
                   title="Please enter a valid Philippine mobile number starting with 09 or +639 followed by 9 digits." 
                   required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="appointment_date">Preferred Date *</label>
                <input type="date" id="appointment_date" name="appointment_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group" id="time-wrapper">
                <label for="appointment_time">Preferred Time (Hourly Slots)</label>
                <select id="appointment_time" name="appointment_time" class="form-control" required>
                    <option value="">-- Select a Time Slot --</option>
                    <option value="09:00:00">09:00 AM</option>
                    <option value="10:00:00">10:00 AM</option>
                    <option value="11:00:00">11:00 AM</option>
                    <option value="12:00:00">12:00 PM</option>
                    <option value="13:00:00">01:00 PM</option>
                    <option value="14:00:00">02:00 PM</option>
                    <option value="15:00:00">03:00 PM</option>
                    <option value="16:00:00">04:00 PM</option>
                    <option value="17:00:00">05:00 PM</option>
                    <option value="18:00:00">06:00 PM</option>
                    <option value="19:00:00">07:00 PM</option>
                    <option value="20:00:00">08:00 PM</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="additional_notes">Creative Project Specifications / Notes</label>
            <textarea id="additional_notes" name="additional_notes" class="form-control" placeholder="Tell us more details about your timeline or expectations..."></textarea>
        </div>

        <button type="submit" name="submit_booking" class="btn">Confirm Booking Request ↗</button>
        <a href="index.php" class="btn-back">← Back to Homepage</a>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const serviceSelect = document.getElementById("service_id");
    const timeWrapper = document.getElementById("time-wrapper");
    const timeSelect = document.getElementById("appointment_time");

    function toggleTimeField() {
        // Get the text description of the selected dropdown option
        const selectedText = serviceSelect.options[serviceSelect.selectedIndex].text.toLowerCase();
        
        if (selectedText.includes("web development")) {
            // Hide the wrapper card, remove required validation, and set an empty dummy database slot value
            timeWrapper.style.display = "none";
            timeSelect.removeAttribute("required");
            timeSelect.value = "00:00:00"; 
        } else {
            // Restore visibility and force requirement constraints for regular sessions
            timeWrapper.style.display = "block";
            timeSelect.setAttribute("required", "required");
            if (timeSelect.value === "00:00:00") {
                timeSelect.value = "";
            }
        }
    }

    // Run verification immediately on load and map it to input changes
    serviceSelect.addEventListener("change", toggleTimeField);
    toggleTimeField();
});
</script>

</body>
</html>
