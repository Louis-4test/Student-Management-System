<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

require '../includes/db.php';

$message = '';
$error = '';

// Handle Add Course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $course_code = trim($_POST['course_code']);
    $course_name = trim($_POST['course_name']);
    $credit = (int)$_POST['credit_value'];
    $semester = (int)$_POST['semester'];
    $department_id = (int)$_POST['department_id'];

    if (!$course_code || !$course_name || !$credit || !$semester || !$department_id) {
        $error = "Please fill all fields correctly to add a course.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO courses (course_code, course_name, credit_value, semester, department_id)
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$course_code, $course_name, $credit, $semester, $department_id]);
        $message = "✅ Course added successfully!";
    }
}

// Handle Delete Course
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM courses WHERE id = ?")->execute([$delete_id]);
    $message = "✅ Course deleted successfully!";
}

// Handle Edit Course - show edit form
$edit_course = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_course = $stmt->fetch();
}

// Handle Update Course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_course'])) {
    $course_id = (int)$_POST['course_id'];
    $course_code = trim($_POST['course_code']);
    $course_name = trim($_POST['course_name']);
    $credit = (int)$_POST['credit_value'];
    $semester = (int)$_POST['semester'];
    $department_id = (int)$_POST['department_id'];

    if (!$course_code || !$course_name || !$credit || !$semester || !$department_id) {
        $error = "Please fill all fields correctly to update the course.";
    } else {
        $stmt = $pdo->prepare("UPDATE courses SET course_code = ?, course_name = ?, credit_value = ?, semester = ?, department_id = ? WHERE id = ?");
        $stmt->execute([$course_code, $course_name, $credit, $semester, $department_id, $course_id]);
        $message = "✅ Course updated successfully!";
        header("Location: manage_course.php");
        exit;
    }
}

// Fetch departments and courses
$departments = $pdo->query("SELECT * FROM departments")->fetchAll();
$courses = $pdo->query("SELECT c.*, d.name AS department_name
                        FROM courses c
                        JOIN departments d ON c.department_id = d.id
                        ORDER BY c.semester, c.course_code")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Courses</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px;
            background: #f0f2f5;
            color: #333;
        }
        h2, h3 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }
        form {
            max-width: 600px;
            margin: 0 auto 40px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgb(0 0 0 / 0.1);
        }
        form input[type="text"],
        form input[type="number"],
        form select {
            width: 100%;
            padding: 12px 15px;
            margin-top: 8px;
            margin-bottom: 20px;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s ease;
        }
        form input[type="text"]:focus,
        form input[type="number"]:focus,
        form select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 6px rgba(52, 152, 219, 0.5);
        }
        form button {
            background: #3498db;
            color: white;
            border: none;
            padding: 14px 0;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.25s ease;
        }
        form button:hover {
            background: #2980b9;
        }
        table {
            border-collapse: collapse;
            width: 90%;
            max-width: 900px;
            margin: 0 auto 50px auto;
            background: #fff;
            box-shadow: 0 4px 10px rgb(0 0 0 / 0.08);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 14px 20px;
            border-bottom: 1px solid #e1e4e8;
            text-align: center;
            font-size: 15px;
            color: #444;
        }
        th {
            background-color: #3498db;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        tbody tr:hover {
            background-color: #f9fbfd;
        }
        a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
            margin: 0 8px;
            transition: color 0.3s ease;
        }
        a.delete-link {
            color: #e74c3c;
        }
        a:hover {
            color: #2980b9;
        }
        a.delete-link:hover {
            color: #c0392b;
        }
        .message, .error {
            max-width: 600px;
            margin: 0 auto 20px auto;
            padding: 15px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
        }
        .message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        @media (max-width: 700px) {
            form, table {
                width: 95%;
            }
            th, td {
                font-size: 13px;
                padding: 10px 12px;
            }
            form input[type="text"],
            form input[type="number"],
            form select {
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>

<h2>Manage Courses</h2>

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($edit_course): ?>
    <!-- Edit course form -->
    <form method="POST">
        <h3>Edit Course: <?= htmlspecialchars($edit_course['course_code']) ?></h3>
        <input type="hidden" name="course_id" value="<?= $edit_course['id'] ?>">
        <input type="text" name="course_code" placeholder="Course Code" value="<?= htmlspecialchars($edit_course['course_code']) ?>" required>
        <input type="text" name="course_name" placeholder="Course Name" value="<?= htmlspecialchars($edit_course['course_name']) ?>" required>
        <input type="number" name="credit_value" placeholder="Credit Value" value="<?= $edit_course['credit_value'] ?>" min="1" max="10" required>
        <input type="number" name="semester" placeholder="Semester" value="<?= $edit_course['semester'] ?>" min="1" max="8" required>
        <select name="department_id" required>
            <option value="">-- Select Department --</option>
            <?php foreach ($departments as $d): ?>
                <option value="<?= $d['id'] ?>" <?= $d['id'] == $edit_course['department_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="update_course">Update Course</button>
        <p style="text-align:center; margin-top: 12px;">
            <a href="manage_course.php" style="color:#777; font-weight:normal;">Cancel Edit</a>
        </p>
    </form>
<?php else: ?>
    <!-- Add course form -->
    <form method="POST">
        <h3>Add New Course</h3>
        <input type="text" name="course_code" placeholder="Course Code" required>
        <input type="text" name="course_name" placeholder="Course Name" required>
        <input type="number" name="credit_value" placeholder="Credit Value" required min="1" max="10">
        <input type="number" name="semester" placeholder="Semester" required min="1" max="8">
        <select name="department_id" required>
            <option value="">-- Select Department --</option>
            <?php foreach ($departments as $d): ?>
                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="add_course">Add Course</button>
    </form>
<?php endif; ?>

<h3>Course List</h3>
<table>
    <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Credit</th>
            <th>Semester</th>
            <th>Department</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($courses): ?>
        <?php foreach ($courses as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['course_code']) ?></td>
                <td><?= htmlspecialchars($c['course_name']) ?></td>
                <td><?= $c['credit_value'] ?></td>
                <td><?= $c['semester'] ?></td>
                <td><?= htmlspecialchars($c['department_name']) ?></td>
                <td>
                    <a href="?edit=<?= $c['id'] ?>">Edit</a>
                    <a href="?delete=<?= $c['id'] ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6">No courses found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>
