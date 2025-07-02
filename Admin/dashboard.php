<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .dashboard {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            color: #007BFF;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin-top: 30px;
        }

        li {
            margin: 15px 0;
            text-align: center;
        }

        a {
            display: inline-block;
            background: #007BFF;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        a:hover {
            background: #0056b3;
        }

        .top-bar {
        display: flex;
        justify-content: flex-end;
        background-color: #f8f9fa;
        padding: 10px 20px;
        border-bottom: 1px solid #ccc;
        }

        .logout-btn {
            background-color: #dc3545; /* Bootstrap red */
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

    </style>
</head>
<body>
    <div class="dashboard">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?></h2>
        <ul>
            <li><a href="student.php">📋 Manage Students (Add / View / Edit / Delete)</a></li>
            <li><a href="manage_course.php">📚 Manage Courses (Add / View / Edit / Delete)</a></li>
            <li><a href="upload_mark.php">📝 Manage Marks (Upload / Edit / Delete)</a></li>
            <div class="top-bar">
                <a href="logout.php" class="logout-btn" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
            </div>


        </ul>
    </div>
</body>
</html>
