<?php
require_once 'includes/db.php';

// Fetch data
$faculties = $pdo->query("SELECT * FROM faculties")->fetchAll(PDO::FETCH_ASSOC);
$departments = $pdo->query("SELECT * FROM departments")->fetchAll(PDO::FETCH_ASSOC);
$courses = $pdo->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>School Management System - Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        header, footer {
            background: #007BFF;
            color: white;
            padding: 15px 0;
            text-align: center;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        h2 {
            border-bottom: 2px solid #007BFF;
            padding-bottom: 5px;
            color: #007BFF;
        }

        .button-section {
            text-align: center;
            margin: 30px 0;
        }

        a.button {
            display: inline-block;
            background: #007BFF;
            color: white;
            padding: 12px 20px;
            margin: 10px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }

        a.button.register {
            background: #17a2b8;
        }

        a.button.admin {
            background: #28a745;
        }

        .faculty {
            margin-top: 20px;
            background: #f0f0f0;
            padding: 15px;
            border-radius: 8px;
        }

        .department {
            margin-left: 20px;
            margin-top: 10px;
        }

        .course {
            margin-left: 40px;
        }

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        footer {
            margin-top: 40px;
        }

        .small-link {
            font-size: 14px;
            color: #555;
        }

        @media (max-width: 600px) {
            .button {
                display: block;
                width: 80%;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
<header>
    <h1>Welcome to the School Management System</h1>
</header>

<div class="container">
    <p>
        This platform provides access to the school's academic structure including faculties, departments, and courses.
        Anyone can explore the academic offerings, but only registered students can:
    </p>
    <ul>
        <li>✅ Create a personal profile (using a matricule from the admin)</li>
        <li>✅ Enroll in a program</li>
        <li>✅ View CA and Exam Marks</li>
        <li>✅ Download Semester Transcripts</li>
    </ul>

    <div class="button-section">
        <a href="register.php" class="button register">Register as a Student</a>
        <div class="small-link">Already have an account? <a href="login.php">Login here</a></div>
        <a href="admin/login.php" class="button admin">Admin Access</a>
    </div>

    <h2>Faculties & Programs</h2>

    <?php foreach ($faculties as $faculty): ?>
        <div class="faculty">
            <h3><?= htmlspecialchars($faculty['faculty_name']) ?></h3>

            <?php foreach ($departments as $dept): ?>
                <?php if ($dept['faculty_id'] == $faculty['id']): ?>
                    <div class="department">
                        <strong>Department:</strong> <?= htmlspecialchars($dept['department_name']) ?>

                        <ul>
                            <?php foreach ($courses as $course): ?>
                                <?php if ($course['program_id'] == $dept['id']): ?>
                                    <li class="course">
                                        <?= htmlspecialchars($course['course_code']) ?> - <?= htmlspecialchars($course['course_name']) ?>
                                        (Semester <?= $course['semester'] ?>, <?= $course['credit_value'] ?> Credits)
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <hr />
    <p style="text-align:center; color: #666;">
        Only students with a valid matricule can create a profile and access academic features.
    </p>
</div>

<footer>
    &copy; <?= date('Y') ?> Your School Name. All rights reserved.
</footer>
</body>
</html>
