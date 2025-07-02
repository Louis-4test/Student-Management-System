<?php
require_once 'includes/db.php';

// Fetch data with prepared statements for best practice
$facultiesStmt = $pdo->prepare("SELECT * FROM faculties");
$facultiesStmt->execute();
$faculties = $facultiesStmt->fetchAll(PDO::FETCH_ASSOC);

$departmentsStmt = $pdo->prepare("SELECT * FROM departments");
$departmentsStmt->execute();
$departments = $departmentsStmt->fetchAll(PDO::FETCH_ASSOC);

$coursesStmt = $pdo->prepare("SELECT * FROM courses");
$coursesStmt->execute();
$courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>School Management System - Home</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header, footer {
            background: #007BFF;
            color: white;
            padding: 18px 0;
            text-align: center;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto 60px;
            padding: 20px;
            background: white;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        p.intro {
            line-height: 1.6;
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

        /* Faculty Cards Grid */
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

        .faculty-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-bottom: 4px solid #007BFF;
        }

        .faculty-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .faculty-title {
            font-size: 1.5rem;
            margin: 0 0 15px;
            color: #007BFF;
            font-weight: 700;
        }

        /* Department Cards inside faculty */
        .departments-list {
            margin-top: 10px;
            padding-left: 0;
            list-style: none;
        }
        .department-item {
            background: #f9fafb;
            margin-bottom: 14px;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 3px 7px rgba(0,0,0,0.05);
            display: flex;
            gap: 15px;
        }

        .department-img {
            flex-shrink: 0;
            width: 90px;
            height: 90px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #17a2b8;
        }

        .department-content {
            flex-grow: 1;
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .department-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .department-content {
                padding-top: 10px;
            }
        }

        footer {
            margin-top: 60px;
            padding: 15px 0;
            font-size: 0.9rem;
            color: #888;
            background: #f1f3f5;
            text-align: center;
            letter-spacing: 0.03em;
        }

        .small-link {
            font-size: 14px;
            color: #555;
            margin-top: 8px;
        }

        .small-link a {
            color: #007BFF;
            text-decoration: none;
            font-weight: 600;
        }
        .small-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<header>
    <h1>Welcome to the Students Management System</h1>
</header>

<div class="container">
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

    <div class="button-section">
        <a href="register.php" class="button register">Register as a Student</a>
        <div class="small-link">Already have an account? <a href="login.php">Login here</a></div>
        <a href="admin/login.php" class="button admin">Admin Access</a>
    </div>

    <h2 class="section-title">Faculties & Departments</h2>

    <div class="faculties-grid">
        <?php foreach ($faculties as $faculty): ?>
            <article class="faculty-card" aria-labelledby="faculty-<?= htmlspecialchars($faculty['id']) ?>">
                <img 
                    class="faculty-img" 
                    src="<?= htmlspecialchars($faculty['image_url'] ?? 'https://via.placeholder.com/400x180?text=Faculty+Image') ?>" 
                    alt="<?= htmlspecialchars($faculty['faculty_name'] ?: 'Faculty Image') ?>"
                    loading="lazy"
                />
                <div class="faculty-content">
                    <h3 id="faculty-<?= htmlspecialchars($faculty['id']) ?>" class="faculty-title"><?= htmlspecialchars($faculty['faculty_name']) ?></h3>
                    <ul class="departments-list">
                        <?php foreach ($departments as $dept): ?>
                            <?php if ($dept['faculty_id'] == $faculty['id']): ?>
                                <li class="department-item">
                                    <img 
                                        class="department-img" 
                                        src="<?= htmlspecialchars($dept['image_url'] ?? 'https://via.placeholder.com/90?text=Dept') ?>" 
                                        alt="<?= htmlspecialchars($dept['department_name'] ?: 'Department Image') ?>" 
                                        loading="lazy"
                                    />
                                    <div class="department-content">
                                        <div class="department-name"><?= htmlspecialchars($dept['department_name']) ?></div>
                                        <ul class="courses-list">
                                            <?php foreach ($courses as $course): ?>
                                                <?php if ($course['department_id'] == $dept['id']): ?>
                                                    <li>
                                                        <span class="code"><?= htmlspecialchars($course['course_code']) ?></span> - 
                                                        <?= htmlspecialchars($course['course_name']) ?>
                                                        (Sem <?= (int)$course['semester'] ?>, <?= (int)$course['credit_value'] ?> Credits)
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <hr />
    <p style="text-align:center; color: #666; margin-top: 40px;">
        Only students with a
