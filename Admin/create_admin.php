<?php
session_start();
require_once 'includes/db.php';

// Only allow access if logged in as admin
if (!isset($_SESSION['admin_id'])) {
    die("Access denied. You must be logged in as an admin.");
}

// Only allow super admin (id=1)
if ($_SESSION['admin_id'] != 1) {
    die("Access denied. Super admin only.");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');

    if (!$username || !$password || !$password_confirm) {
        $message = "All fields are required.";
    } elseif ($password !== $password_confirm) {
        $message = "Passwords do not match.";
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $message = "Username already exists.";
        } else {
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $password_hashed])) {
                $message = "✅ Admin user '$username' created successfully.";
            } else {
                $message = "Error creating admin user.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Create Admin User</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f0f4f8;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 50px;
    }
    form {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        width: 320px;
    }
    h2 {
        margin-bottom: 20px;
        color: #007BFF;
        text-align: center;
    }
    label {
        display: block;
        margin-bottom: 6px;
        font-weight: bold;
    }
    input[type=text], input[type=password] {
        width: 100%;
        padding: 8px 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        font-size: 16px;
    }
    button {
        width: 100%;
        padding: 10px;
        background: #007BFF;
        color: white;
        font-size: 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }
    button:hover {
        background: #0056b3;
    }
    .message {
        margin-bottom: 20px;
        padding: 10px;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
    }
    .error {
        background: #f8d7da;
        color: #842029;
    }
    .success {
        background: #d1e7dd;
        color: #0f5132;
    }
</style>
</head>
<body>
<form method="POST" action="">
    <h2>Create Admin User</h2>
    <?php if ($message): ?>
        <div class="message <?= strpos($message, '✅') === 0 ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required autocomplete="off" />

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required autocomplete="new-password" />

    <label for="password_confirm">Confirm Password</label>
    <input type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password" />

    <button type="submit">Create Admin</button>
</form>
</body>
</html>
