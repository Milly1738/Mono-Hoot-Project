<?php
// 1. Include your existing database connection file
require_once 'db.php';

$message = "";
$messageClass = "";
$redirectToLogin = false; // Flag to trigger redirect

// 2. Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $message = "All fields are required!";
        $messageClass = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageClass = "error";
    } else {
        try {
            // 3. Check if username or email already exists
            $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
            $checkStmt->execute(['username' => $username, 'email' => $email]);
            
            if ($checkStmt->rowCount() > 0) {
                $message = "Username or Email is already registered!";
                $messageClass = "error";
            } else {
                // 4. SECURE STEP: Securely hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // 5. Insert the new user into the database
                $insertStmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
                $insertStmt->execute([
                    'username' => $username,
                    'email' => $email,
                    'password_hash' => $hashedPassword
                ]);

                $message = "Account created! Redirecting to login in 3 seconds...";
                $messageClass = "success";
                $redirectToLogin = true; 
            }
        } catch (PDOException $e) {
            $message = "An error occurred: " . $e->getMessage();
            $messageClass = "error";
        }
    }
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$owlMark = '<svg viewBox="0 0 40 40" fill="none"><circle cx="20" cy="22" r="15" fill="#18150f"/><circle cx="15" cy="20" r="5.5" fill="#fff"/><circle cx="25" cy="20" r="5.5" fill="#fff"/><circle cx="15" cy="20" r="2.3" fill="#18150f"/><circle cx="25" cy="20" r="2.3" fill="#18150f"/><path d="M20 25L17 30H23L20 25Z" fill="#fff"/></svg>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Mono Hoot Studios</title>
    
    <?php if ($redirectToLogin): ?>
        <meta http-equiv="refresh" content="3;url=login.php">
    <?php endif; ?>

    <style>
        :root {
            --bg: #ffffff;
            --bg-soft: #fbfaf7;
            --ink: #18150f;
            --ink-soft: #6f6a5d;
            --accent: #c6a06a;
            --line: #e9e5db;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background-color: var(--bg-soft);
            color: var(--ink);
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }
        .register-container {
            width: 100%;
            max-width: 420px;
            background: var(--bg);
            padding: 40px;
            border-radius: 14px;
            border: 1px solid var(--line);
            box-shadow: 0 10px 30px rgba(24,21,15,0.04);
            text-align: center;
        }
        .logo-wrapper {
            width: 50px;
            height: 50px;
            margin: 0 auto 20px;
        }
        h2 {
            font-family: 'Fraunces', serif;
            font-weight: 600;
            font-size: 28px;
            margin: 0 0 8px 0;
            letter-spacing: -0.01em;
        }
        .subtext {
            font-size: 14px;
            color: var(--ink-soft);
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--line);
            border-radius: 8px;
            font-family: inherit;
            font-size: 14.5px;
            background: var(--bg);
            color: var(--ink);
            transition: border-color 0.18s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
        }
        .btn {
            width: 100%;
            padding: 14px;
            background-color: var(--ink);
            color: #ffffff;
            border: 1px solid var(--ink);
            border-radius: 999px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.18s, background 0.18s;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #000000;
            transform: translateY(-1px);
        }
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
        }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .footer-link {
            display: block;
            margin-top: 24px;
            font-size: 13.5px;
            color: var(--ink-soft);
            text-decoration: none;
            transition: color 0.18s;
        }
        .footer-link strong {
            color: var(--accent);
        }
        .footer-link:hover {
            color: var(--ink);
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="logo-wrapper"><?= $owlMark ?></div>
    <h2>Create Account</h2>
    <p class="subtext">Join Mono Hoot Studios to manage bookings.</p>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?= $messageClass; ?>">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" placeholder="Choose a username" required>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
        </div>
        <button type="submit" class="btn">Get Started ↗</button>
    </form>
    
    <a href="login.php" class="footer-link">Already have an account? <strong>Log in here</strong></a>
</div>

</body>
</html>
