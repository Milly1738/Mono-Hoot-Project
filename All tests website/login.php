<?php
// 1. Start a session at the very top to track logged-in users
session_start();

// If the user is already logged in, send them straight to the index page
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// 2. Include your database connection
require_once 'db.php';

$message = "";
$messageClass = "";

// 3. Process the login form when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $message = "Please fill in all fields.";
        $messageClass = "error";
    } else {
        try {
            // Find the user by their unique username
            $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // 4. Overridden Check: Check if hashed password matches OR if it's the local testing admin override
            if (($user && password_verify($password, $user['password_hash'])) || ($username === 'admin' && $password === 'admin123')) {
                
                // Login successful! Save user data into Session variables
                $_SESSION['user_id'] = $user ? $user['id'] : 999; 
                $_SESSION['username'] = $username; 

                // Redirect them directly to their journal view
                header("Location: journal.php");
                exit;
            } else {
                $message = "Invalid username or password.";
                $messageClass = "error";
            }
        } catch (PDOException $e) {
            $message = "An error occurred: " . $e->getMessage();
            $messageClass = "error";
        }
    }
}

// Global utility helper function for sanitization
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
    <title>Login — Mono Hoot Studios</title>
    <link rel="preconnect" href="https://googleapis.com">
    <link rel="preconnect" href="https://gstatic.com" crossorigin>
    <link href="https://googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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
        .login-container {
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
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
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

<div class="login-container">
    <div class="logo-wrapper"><?= $owlMark ?></div>
    <h2>Welcome Back</h2>
    <p class="subtext">Sign in to your Mono Hoot studio profile.</p>
    
    <?php if (!empty($message)): ?>
        <div class="alert">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn">Sign In ↗</button>
    </form>
    
    <a href="register.php" class="footer-link">Don't have an account? <strong>Register here</strong></a>
</div>

</body>
</html>
