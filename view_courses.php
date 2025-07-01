<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

require 'includes/db.php';

$student_id = $_SESSION['student_id'];

// Get student's department
$stmt = $pdo->prepare("SELECT department_id FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$department_id = $stmt->fetchColumn();

$selected_semester = $_GET['semester'] ?? 1;

// Fetch courses for selected semester
$stmt = $pdo->prepare("SELECT course_code, course_name, credit_value
                       FROM courses
                       WHERE department_id = ? AND semester = ?");
$stmt->execute([$department_id, $selected_semester]);
$courses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Courses - Semester <?= htmlspecialchars($selected_semester) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f9fc;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 25px;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        select {
            padding: 10px 15px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
            outline: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .back {
            margin-top: 20px;
            text-align: center;
        }

        .back a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }

        @media (max-width: 600px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            tr {
                margin-bottom: 15px;
            }

            th {
                display: none;
            }

            td {
                position: relative;
                padding-left: 50%;
                text-align: right;
            }

            td::before {
                position: absolute;
                left: 15px;
                width: 45%;
                white-space: nowrap;
                font-weight: bold;
                content: attr(data-label);
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>My Courses - Semester <?= htmlspecialchars($selected_semester) ?></h2>

    <form method="get">
        <label for="semester">Select Semester: </label>
        <select name="semester" id="semester" onchange="this.form.submit()">
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <option value="<?= $i ?>" <?= $selected_semester == $i ? 'selected' : '' ?>>Semester <?= $i ?></option>
            <?php endfor; ?>
        </select>
    </form>

    <?php if (count($courses)): ?>
        <table>
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Credit Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td data-label="Course Code"><?= htmlspecialchars($course['course_code']) ?></td>
                        <td data-label="Course Name"><?= htmlspecialchars($course['course_name']) ?></td>
                        <td data-label="Credit Value"><?= htmlspecialchars($course['credit_value']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center; color: #888;">No courses found for this semester.</p>
    <?php endif; ?>

    <div class="back">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>
