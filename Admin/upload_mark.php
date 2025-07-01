<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require '../includes/db.php';

$message = '';
$error = '';

// Handle Delete Mark
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM marks WHERE id = ?")->execute([$delete_id]);
    $message = "✅ Mark record deleted successfully!";
    header("Location: upload_marks.php");
    exit;
}

// Handle Edit form load
$edit_mark = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM marks WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_mark = $stmt->fetch();
}

// Handle Add or Update Mark
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = (int)$_POST['student_id'];
    $course_id = (int)$_POST['course_id'];
    $ca_mark = floatval($_POST['ca_mark']);
    $exam_mark = floatval($_POST['exam_mark']);
    $semester = (int)$_POST['semester'];

    if ($ca_mark < 0 || $ca_mark > 100 || $exam_mark < 0 || $exam_mark > 100) {
        $error = "Marks must be between 0 and 100.";
    } elseif ($semester < 1 || $semester > 10) {
        $error = "Invalid semester value.";
    } else {
        if (isset($_POST['update_mark'])) {
            $id = (int)$_POST['mark_id'];
            $stmt = $pdo->prepare("UPDATE marks SET student_id = ?, course_id = ?, ca_mark = ?, exam_mark = ?, semester = ? WHERE id = ?");
            $stmt->execute([$student_id, $course_id, $ca_mark, $exam_mark, $semester, $id]);
            $message = "✅ Mark updated successfully!";
            header("Location: upload_marks.php");
            exit;
        } else {
            // Insert or update if exists
            $stmt = $pdo->prepare("INSERT INTO marks (student_id, course_id, ca_mark, exam_mark, semester)
                                   VALUES (?, ?, ?, ?, ?)
                                   ON DUPLICATE KEY UPDATE ca_mark = VALUES(ca_mark), exam_mark = VALUES(exam_mark), semester = VALUES(semester)");
            $stmt->execute([$student_id, $course_id, $ca_mark, $exam_mark, $semester]);
            $message = "✅ Mark uploaded successfully!";
        }
    }
}

// Fetch students and courses
$students = $pdo->query("SELECT id, full_name FROM students ORDER BY full_name ASC")->fetchAll();
$courses = $pdo->query("SELECT id, course_name FROM courses ORDER BY course_name ASC")->fetchAll();

// Fetch all marks with joined student and course names
$marks = $pdo->query("
    SELECT m.id, m.ca_mark, m.exam_mark, m.semester, s.full_name, c.course_name, m.student_id, m.course_id
    FROM marks m
    JOIN students s ON m.student_id = s.id
    JOIN courses c ON m.course_id = c.id
    ORDER BY s.full_name, c.course_name, m.semester
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Marks</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 40px auto;
            max-width: 900px;
            background: #f5f8fa;
            color: #333;
        }
        h2, h3 {
            text-align: center;
            color: #2c3e50;
        }
        form {
            background: white;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgb(0 0 0 / 0.1);
            margin-bottom: 40px;
        }
        label {
            font-weight: 600;
            display: block;
            margin-top: 15px;
        }
        select, input[type="number"] {
            width: 100%;
            padding: 10px 12px;
            margin-top: 5px;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        select:focus, input[type="number"]:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.5);
        }
        button {
            margin-top: 25px;
            background: #3498db;
            color: white;
            border: none;
            padding: 14px 0;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background: #2980b9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 10px rgb(0 0 0 / 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 14px 20px;
            border-bottom: 1px solid #e1e4e8;
            text-align: center;
            font-size: 14px;
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
            font-weight: 600;
            text-decoration: none;
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
            margin: 20px auto;
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
        @media (max-width: 650px) {
            body {
                margin: 20px 10px;
            }
            th, td {
                padding: 10px 8px;
                font-size: 12px;
            }
            form {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<h2>Manage Marks</h2>

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($edit_mark): ?>
    <form method="POST">
        <h3>Edit Marks for <?= htmlspecialchars($students[array_search($edit_mark['student_id'], array_column($students, 'id'))]['full_name'] ?? 'Student') ?></h3>
        <input type="hidden" name="mark_id" value="<?= $edit_mark['id'] ?>">

        <label>Student:</label>
        <select name="student_id" required>
            <option value="">-- Select Student --</option>
            <?php foreach ($students as $student): ?>
                <option value="<?= $student['id'] ?>" <?= $student['id'] == $edit_mark['student_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($student['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Course:</label>
        <select name="course_id" required>
            <option value="">-- Select Course --</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= $course['id'] ?>" <?= $course['id'] == $edit_mark['course_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($course['course_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>CA Mark:</label>
        <input type="number" step="0.01" name="ca_mark" min="0" max="100" value="<?= htmlspecialchars($edit_mark['ca_mark']) ?>" required>

        <label>Exam Mark:</label>
        <input type="number" step="0.01" name="exam_mark" min="0" max="100" value="<?= htmlspecialchars($edit_mark['exam_mark']) ?>" required>

        <label>Semester:</label>
        <input type="number" name="semester" min="1" max="10" value="<?= htmlspecialchars($edit_mark['semester']) ?>" required>

        <button type="submit" name="update_mark">Update Mark</button>
    </form>

    <p style="text-align:center; margin-bottom: 40px;">
        <a href="upload_marks.php">&larr; Cancel Editing</a>
    </p>

<?php else: ?>
    <form method="POST">
        <h3>Add New Marks</h3>
        <label>Student:</label>
        <select name="student_id" required>
            <option value="">-- Select Student --</option>
            <?php foreach ($students as $student): ?>
                <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['full_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Course:</label>
        <select name="course_id" required>
            <option value="">-- Select Course --</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>CA Mark:</label>
        <input type="number" step="0.01" name="ca_mark" min="0" max="100" required>

        <label>Exam Mark:</label>
        <input type="number" step="0.01" name="exam_mark" min="0" max="100" required>

        <label>Semester:</label>
        <input type="number" name="semester" min="1" max="10" required>

        <button type="submit">Upload Marks</button>
    </form>
<?php endif; ?>

<h3>All Marks</h3>
<table>
    <thead>
        <tr>
            <th>Student</th>
            <th>Course</th>
            <th>CA Mark</th>
            <th>Exam Mark</th>
            <th>Semester</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($marks) === 0): ?>
            <tr><td colspan="6" style="text-align:center; padding: 20px;">No marks found.</td></tr>
        <?php else: ?>
            <?php foreach ($marks as $mark): ?>
                <tr>
                    <td><?= htmlspecialchars($mark['full_name']) ?></td>
                    <td><?= htmlspecialchars($mark['course_name']) ?></td>
                    <td><?= htmlspecialchars($mark['ca_mark']) ?></td>
                    <td><?= htmlspecialchars($mark['exam_mark']) ?></td>
                    <td><?= htmlspecialchars($mark['semester']) ?></td>
                    <td>
                        <a href="?edit=<?= $mark['id'] ?>">Edit</a> |
                        <a href="?delete=<?= $mark['id'] ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this mark?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
