<?php
session_start();
require 'includes/db.php';

// Redirect if already logged in
if (isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricule = trim($_POST['matricule']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM students WHERE matricule = ?");
    $stmt->execute([$matricule]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['student_id'] = $user['id'];
        $_SESSION['student_name'] = $user['full_name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid matricule or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background: #f5f7fa;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            margin: 60px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }

        form label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            background-color: #007BFF;
            color: white;
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        .link {
            text-align: center;
            margin-top: 15px;
        }

        .link a {
            color: #007BFF;
            text-decoration: none;
        }

        @media (max-width: 480px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Student Login</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Matricule:</label>
        <input type="text" name="matricule" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <div class="link">
        Don't have an account? <a href="register.php">Register</a>
    </div>
</div>

</body>
</html>
