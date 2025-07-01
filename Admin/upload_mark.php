<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require '../includes/db.php';

// Handle Add/Edit Marks
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['mark_id'] ?? null;
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $ca_mark = floatval($_POST['ca_mark']);
    $exam_mark = floatval($_POST['exam_mark']);
    $semester = $_POST['semester'];

    // Validate marks
    if ($ca_mark < 0 || $ca_mark > 30 || $exam_mark < 0 || $exam_mark > 70) {
        $error = "CA mark must be between 0-30 and Exam mark between 0-70.";
    } else {
        if ($id) {
            // Update existing
            $stmt = $pdo->prepare("UPDATE marks SET student_id=?, course_id=?, ca_mark=?, exam_mark=?, semester=? WHERE id=?");
            $stmt->execute([$student_id, $course_id, $ca_mark, $exam_mark, $semester, $id]);
            $message = "Marks updated successfully.";
        } else {
            // Insert new or update if exists
            $stmt = $pdo->prepare("INSERT INTO marks (student_id, course_id, ca_mark, exam_mark, semester)
                                   VALUES (?, ?, ?, ?, ?)
                                   ON DUPLICATE KEY UPDATE ca_mark=VALUES(ca_mark), exam_mark=VALUES(exam_mark), semester=VALUES(semester)");
            $stmt->execute([$student_id, $course_id, $ca_mark, $exam_mark, $semester]);
            $message = "Marks uploaded successfully.";
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $pdo->prepare("DELETE FROM marks WHERE id = ?")->execute([$delete_id]);
    header("Location: upload_marks.php?msg=deleted");
    exit;
}

// Fetch marks with student & course names
$marks = $pdo->query("SELECT m.*, s.full_name, c.course_name
                      FROM marks m
                      JOIN students s ON m.student_id = s.id
                      JOIN courses c ON m.course_id = c.id
                      ORDER BY m.semester, s.full_name")->fetchAll();

// Fetch students and courses for form selects
$students = $pdo->query("SELECT id, full_name FROM students ORDER BY full_name ASC")->fetchAll();
$courses = $pdo->query("SELECT id, course_name FROM courses ORDER BY course_name ASC")->fetchAll();

// If editing a mark (edit id from GET)
$edit_mark = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM marks WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_mark = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Marks</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f6f8; }
        h2 { text-align: center; color: #333; }
        form {
            background: #fff;
            width: 60%;
            margin: 20px auto;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background: #007BFF;
            color: white;
            border: none;
            padding: 12px;
            margin-top: 20px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        button:hover { background: #0056b3; }
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: center;
        }
        th { background: #007BFF; color: white; }
        a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover { text-decoration: underline; }
        .message, .error {
            width: 60%;
            margin: 15px auto;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
        }
        .message { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<h2>Manage Marks</h2>

<?php if (!empty($message)): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="mark_id" value="<?= $edit_mark['id'] ?? '' ?>">

    <label>Student:</label>
    <select name="student_id" required>
        <option value="">-- Select Student --</option>
        <?php foreach ($students as $student): ?>
            <option value="<?= $student['id'] ?>" <?= (isset($edit_mark) && $edit_mark['student_id'] == $student['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($student['full_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Course:</label>
    <select name="course_id" required>
        <option value="">-- Select Course --</option>
        <?php foreach ($courses as $course): ?>
            <option value="<?= $course['id'] ?>" <?= (isset($edit_mark) && $edit_mark['course_id'] == $course['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($course['course_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>CA Mark (0-30):</label>
    <input type="number" step="0.01" min="0" max="30" name="ca_mark" required
           value="<?= htmlspecialchars($edit_mark['ca_mark'] ?? '') ?>">

    <label>Exam Mark (0-70):</label>
    <input type="number" step="0.01" min="0" max="70" name="exam_mark" required
           value="<?= htmlspecialchars($edit_mark['exam_mark'] ?? '') ?>">

    <label>Semester:</label>
    <input type="number" min="1" name="semester" required
           value="<?= htmlspecialchars($edit_mark['semester'] ?? '') ?>">

    <button type="submit"><?= isset($edit_mark) ? 'Update Marks' : 'Upload Marks' ?></button>
</form>

<table>
    <thead>
        <tr>
            <th>Student</th>
            <th>Course</th>
            <th>CA Mark</th>
            <th>Exam Mark</th>
            <th>Total Mark</th>
            <th>Semester</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!$marks): ?>
            <tr><td colspan="7">No marks found.</td></tr>
        <?php else: ?>
            <?php foreach ($marks as $mark): ?>
                <tr>
                    <td><?= htmlspecialchars($mark['full_name']) ?></td>
                    <td><?= htmlspecialchars($mark['course_name']) ?></td>
                    <td><?= htmlspecialchars($mark['ca_mark']) ?></td>
                    <td><?= htmlspecialchars($mark['exam_mark']) ?></td>
                    <td><?= htmlspecialchars($mark['ca_mark'] + $mark['exam_mark']) ?></td>
                    <td><?= htmlspecialchars($mark['semester']) ?></td>
                    <td>
                        <a href="?edit=<?= $mark['id'] ?>">Edit</a> | 
                        <a href="?delete=<?= $mark['id'] ?>" onclick="return confirm('Are you sure you want to delete this mark?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
