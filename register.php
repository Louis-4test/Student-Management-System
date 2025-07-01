<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $matricule = $_POST['matricule'];
    $department_id = $_POST['department_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO students (full_name, email, matricule, password, department_id) 
                           VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$full_name, $email, $matricule, $password, $department_id])) {
        $success = true;
    } else {
        $error = "Error occurred during registration.";
    }
}

// Fetch departments
$departments = $pdo->query("SELECT id, name FROM departments")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background: #f5f7fa;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 480px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.08);
        }

        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 25px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            margin-top: 15px;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"],
        form select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        form button {
            background-color: #007BFF;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            padding: 10px;
            margin-top: 10px;
            font-weight: bold;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .login-link a {
            color: #007BFF;
            text-decoration: none;
        }

        @media (max-width: 480px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            form button {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Student Registration</h2>

    <?php if (isset($success) && $success): ?>
        <div class="message success">Registration successful. You can now <a href="login.php">log in</a>.</div>
    <?php elseif (isset($error)): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Full Name:</label>
        <input type="text" name="full_name" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Matricule (from admin):</label>
        <input type="text" name="matricule" required>

        <label>department:</label>
        <select name="department_id" required>
            <option value="">-- Select department --</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?= $department['id'] ?>"><?= htmlspecialchars($department['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Register</button>
    </form>

    <div class="login-link">
        Already registered? <a href="login.php">Login here</a>
    </div>
</div>
</body>
</html>
