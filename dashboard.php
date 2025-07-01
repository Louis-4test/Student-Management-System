<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

require 'includes/db.php';

$student_id = $_SESSION['student_id'];
$stmt = $pdo->prepare("SELECT s.full_name, p.name AS department_name, f.name AS faculty_name 
                       FROM students s
                       JOIN departments p ON s.department_id = p.id
                       JOIN faculties f ON p.faculty_id = f.id
                       WHERE s.id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
        }

        .dashboard {
            max-width: 700px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.08);
        }

        h2 {
            color: #007BFF;
            text-align: center;
            margin-bottom: 30px;
        }

        .info {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .info p {
            margin: 10px 0;
        }

        .actions {
            text-align: center;
        }

        .actions a, .actions form button {
            display: inline-block;
            background: #007BFF;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            margin: 10px;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .actions a:hover, .actions form button:hover {
            background: #0056b3;
        }

        .actions .logout {
            background: #dc3545;
        }

        .actions .logout:hover {
            background: #b02a37;
        }

        .transcript-form {
            display: inline-block;
            margin: 10px;
        }

        .transcript-form select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }

        @media (max-width: 600px) {
            .actions a, .actions form button, .transcript-form select {
                display: block;
                width: 90%;
                margin: 10px auto;
            }

            .transcript-form {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="dashboard">
    <h2>Welcome, <?= htmlspecialchars($student['full_name']) ?></h2>

    <div class="info">
        <p><strong>Faculty:</strong> <?= htmlspecialchars($student['faculty_name']) ?></p>
        <p><strong>Department:</strong> <?= htmlspecialchars($student['department_name']) ?></p>
    </div>

    <div class="actions">
        <a href="view_courses.php">📚 View Courses</a>
        <a href="view_marks.php">📝 View Marks</a>

        <form class="transcript-form" method="GET" action="download_transcript.php">
            <select name="semester" required>
                <option value="">📄 Select Semester</option>
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <option value="<?= $i ?>">Semester <?= $i ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit">Download Transcript</button>
        </form>

        <a href="logout.php" class="logout">🚪 Logout</a>
    </div>
</div>

</body>
</html>
