<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

require 'includes/db.php';

$student_id = $_SESSION['student_id'];
$selected_semester = $_GET['semester'] ?? 1;

// Fetch student marks + course credits
$stmt = $pdo->prepare("SELECT c.course_code, c.course_name, c.credit_value, m.ca_mark, m.exam_mark,
                              (m.ca_mark + m.exam_mark) AS total
                       FROM marks m
                       JOIN courses c ON m.course_id = c.id
                       WHERE m.student_id = ? AND m.semester = ?");
$stmt->execute([$student_id, $selected_semester]);
$marks = $stmt->fetchAll();

// Function to get grade letter and point
function getGradePoint($total) {
    if ($total >= 80) return ['A', 4.0];
    if ($total >= 70) return ['B+', 3.5];
    if ($total >= 60) return ['B', 3.0];
    if ($total >= 50) return ['C', 2.0];
    if ($total >= 40) return ['D', 1.0];
    return ['F', 0.0];
}

// GPA calculation
$total_points = 0;
$total_credits = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Marks + GPA - Semester <?= htmlspecialchars($selected_semester) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f8fc;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 950px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.08);
        }

        h2, h3 {
            text-align: center;
            color: #007BFF;
        }

        form {
            text-align: center;
            margin-bottom: 25px;
        }

        select {
            padding: 10px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .gpa {
            margin-top: 20px;
            text-align: center;
            font-size: 20px;
            color: #333;
        }

        .gpa span {
            color: #28a745;
            font-weight: bold;
        }

        .back {
            margin-top: 20px;
            text-align: center;
        }

        .back a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        @media (max-width: 600px) {
            table, thead, tbody, th, td, tr {
                display: block;
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
                top: 12px;
                font-weight: bold;
                content: attr(data-label);
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Semester <?= $selected_semester ?> Results & GPA</h2>

    <form method="GET">
        <label for="semester">Select Semester:</label>
        <select name="semester" id="semester" onchange="this.form.submit()">
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <option value="<?= $i ?>" <?= $selected_semester == $i ? 'selected' : '' ?>>
                    Semester <?= $i ?>
                </option>
            <?php endfor; ?>
        </select>
    </form>

    <?php if (count($marks) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Credit</th>
                    <th>CA</th>
                    <th>Exam</th>
                    <th>Total</th>
                    <th>Grade</th>
                    <th>GPA Points</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($marks as $m): 
                [$grade, $points] = getGradePoint($m['total']);
                $credit = $m['credit_value'];
                $total_points += $points * $credit;
                $total_credits += $credit;
            ?>
                <tr>
                    <td data-label="Course"><?= htmlspecialchars($m['course_code'] . ' - ' . $m['course_name']) ?></td>
                    <td data-label="Credit"><?= $credit ?></td>
                    <td data-label="CA"><?= $m['ca_mark'] ?></td>
                    <td data-label="Exam"><?= $m['exam_mark'] ?></td>
                    <td data-label="Total"><?= $m['total'] ?></td>
                    <td data-label="Grade"><?= $grade ?></td>
                    <td data-label="GPA Points"><?= round($points * $credit, 2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="gpa">
            Semester GPA: <span><?= $total_credits > 0 ? round($total_points / $total_credits, 2) : 'N/A' ?></span>
        </div>
    <?php else: ?>
        <p style="text-align:center; color:#888;">No marks found for this semester.</p>
    <?php endif; ?>

    <div class="back">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>
