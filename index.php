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
    <title>Student Management System - Home</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f7fa;
            color: #333;
        }

        /* === NAVBAR === */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007BFF;
            padding: 14px 20px;
            flex-wrap: wrap;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-logo {
            font-size: 1.4rem;
            font-weight: bold;
            color: white;
        }

        .nav-links {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 10px 0;
            gap: 25px;
            flex-wrap: wrap;
        }

        .nav-links li a {
            color: white;
            text-decoration: none;
            font-weight: 600;
        }

        .nav-links li a:hover {
            color: #ffd966;
        }

        .nav-register {
            background-color: #ffc107;
            color: #333;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .nav-register:hover {
            background-color: #e0a800;
            color: white;
        }

        /* === CONTENT === */
        .container {
            max-width: 1100px;
            margin: 30px auto 60px;
            padding: 20px;
            background: white;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        p.intro {
            line-height: 1.7;
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 25px;
        }

        ul.features {
            list-style: none;
            padding-left: 0;
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            color: #2d995b;
        }

        ul.features li {
            background: #e6f4ea;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(45, 153, 91, 0.3);
        }

        .button-section {
            text-align: center;
            margin-bottom: 40px;
        }

        a.button {
            display: inline-block;
            background: #007BFF;
            color: white;
            padding: 12px 24px;
            margin: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            transition: background-color 0.3s ease;
            box-shadow: 0 3px 7px rgba(0,123,255,0.5);
        }

        a.button:hover {
            background: #0056b3;
        }

        a.button.register {
            background: #17a2b8;
            box-shadow: 0 3px 7px rgba(23,162,184,0.5);
        }

        a.button.register:hover {
            background: #117a8b;
        }

        a.button.admin {
            background: #28a745;
            box-shadow: 0 3px 7px rgba(40,167,69,0.5);
        }

        a.button.admin:hover {
            background: #1e7e34;
        }

        h2.section-title {
            color: #007BFF;
            border-bottom: 3px solid #007BFF;
            padding-bottom: 6px;
            margin-bottom: 25px;
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .faculties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }

        .faculty-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease;
        }

        .faculty-card:hover {
            transform: translateY(-5px);
        }

        .faculty-content {
            padding: 20px;
        }

        .faculty-title {
            font-size: 1.5rem;
            margin: 0 0 15px;
            color: #007BFF;
            font-weight: 700;
        }

        .departments-list {
            list-style: none;
            padding-left: 0;
        }

        .department-item {
            background: #f9fafb;
            margin-bottom: 14px;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 3px 7px rgba(0,0,0,0.05);
        }

        .department-name {
            font-weight: 700;
            color: #17a2b8;
            margin-bottom: 8px;
            font-size: 1.2rem;
        }

        .courses-list {
            margin: 0;
            padding-left: 20px;
            list-style: disc;
            color: #555;
        }

        .courses-list li {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .courses-list li span.code {
            font-weight: 700;
            color: #666;
        }

        footer {
            margin-top: 60px;
            padding: 15px 0;
            font-size: 0.9rem;
            color: #888;
            background: #f1f3f5;
            text-align: center;
        }

        /* Responsive Navbar */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                flex-direction: column;
                gap: 10px;
                margin-top: 10px;
            }

            .nav-register {
                margin-top: 10px;
                align-self: flex-start;
            }
        }
    </style>
</head>
<body>

<!-- Navigation Header -->
<header>
    <nav class="navbar">
        <div class="nav-logo">Student System</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="#academics">Academics</a></li>
            <li><a href="#students">Students</a></li>
            <li><a href="#contact">Contact Us</a></li>
        </ul>
        <a href="register.php" class="nav-register">Register</a>
    </nav>
</header>

<!-- Page Content -->
<div class="container">

    <!-- New Intro Paragraphs -->
    <p class="intro">
        YIBS offers career development programs that are industry-focused and are designed to meet new market
        trends for emerging economies. We aim to imbue in students a career focus and a vision for a lasting 
        impact on their lives and the communities (society) in which they operate.
    </p>

    <p class="intro">
        Our tuition staff is drawn from a network of reputable business executives in leading firms, who have
        acquired similar qualifications and are currently leveraging on the field. We are unique in our
        delivery and do not discriminate on any basis, given the opportunity to transform individuals into
        becoming proactive business experts through the various tuition programs.
    </p>

    <p class="intro">
        Thus, we offer career advancement opportunities at all levels and in varied business related
        disciplines; entry levels ranging from GCE Ordinary level to Masters.
    </p>

    <ul class="features">
        <li>International Certifications and Diploma</li>
        <li>Master's Degree</li>
        <li>Bachelor's Degree</li>
        <li>HND</li>
        <li>Business Services</li>
    </ul>

    <div class="button-section" id="students">
        <a href="login.php" class="button register">Login as Student</a>
        <a href="admin/login.php" class="button admin">Admin Access</a>
    </div>

    <h2 id="academics" class="section-title">Faculties & Departments</h2>

    <div class="faculties-grid">
        <?php foreach ($faculties as $faculty): ?>
            <div class="faculty-card">
                <div class="faculty-content">
                    <h3 class="faculty-title"><?= htmlspecialchars($faculty['name']) ?></h3>
                    <ul class="departments-list">
                        <?php foreach ($departments as $dept): ?>
                            <?php if ($dept['faculty_id'] == $faculty['id']): ?>
                                <li class="department-item">
                                    <div class="department-name"><?= htmlspecialchars($dept['name']) ?></div>
                                    <ul class="courses-list">
                                        <?php foreach ($courses as $course): ?>
                                            <?php if ($course['department_id'] == $dept['id']): ?>
                                                <li>
                                                    <span class="code"><?= htmlspecialchars($course['course_code']) ?></span> -
                                                    <?= htmlspecialchars($course['course_name']) ?> (Sem <?= (int)$course['semester'] ?>, <?= (int)$course['credit_value'] ?> Credits)
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Footer -->
<footer id="contact">
    &copy; <?= date('Y') ?> Student Management System. All Rights Reserved.
</footer>

</body>
</html>
