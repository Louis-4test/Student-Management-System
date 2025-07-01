<?php
require_once '../includes/auth.php'; // checks if student is logged in
require_once '../includes/db.php';

// Fetch student info from session
$student_id = $_SESSION['student_id'];
$stmt = $pdo->prepare("SELECT s.full_name, s.email, s.matricule, p.name AS department_name
                       FROM students s
                       JOIN departments p ON s.department_id = p.id
                       WHERE s.id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    echo "Student profile not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.07);
        }

        h2 {
            color: #007BFF;
            margin-bottom: 20px;
            text-align: center;
        }

        .info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
        }

        .info p {
            margin: 10px 0;
        }

        .links {
            margin-top: 30px;
            text-align: center;
        }

        .links a {
            display: inline-block;
            background: #007BFF;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            margin: 8px;
            border-radius: 6px;
            font-weight: bold;
        }

        .links a:hover {
            background: #0056b3;
        }

        .logout {
            margin-top: 20px;
            text-align: center;
        }

        .logout a {
            color: red;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .links a {
                display: block;
                margin: 10px auto;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($student['full_name']) ?></h2>

    <div class="info">
        <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
        <p><strong>Matricule:</strong> <?= htmlspecialchars($student['matricule']) ?></p>
        <p><strong>department:</strong> <?= htmlspecialchars($student['department_name']) ?></p>
    </div>

    <div class="links">
        <a href="view_courses.php">📚 View Courses</a>
        <a href="view_marks.php">📝 View Marks</a>
        <a href="download_transcript.php">📄 Download Transcript</a>
    </div>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>
</div>

</body>
</html>
