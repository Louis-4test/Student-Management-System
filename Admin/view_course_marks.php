<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

require '../includes/db.php';

// Get all courses
$courses = $pdo->query("SELECT id, course_code, course_name FROM courses")->fetchAll();
$course_id = $_GET['course_id'] ?? null;

$results = [];
if ($course_id) {
    $stmt = $pdo->prepare("SELECT s.full_name, s.matricule, m.ca_mark, m.exam_mark, 
                                  (m.ca_mark + m.exam_mark) AS total
                           FROM marks m
                           JOIN students s ON m.student_id = s.id
                           WHERE m.course_id = ?");
    $stmt->execute([$course_id]);
    $results = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Course Marks - Admin</title>
    <style>
        table { border-collapse: collapse; width: 85%; margin: auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #eee; }
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { text-align: center; }
    </style>
</head>
<body>
<h2 style="text-align:center;">View Marks By Course</h2>

<form method="GET">
    <label>Select Course:</label>
    <select name="course_id" onchange="this.form.submit()">
        <option value="">-- Choose --</option>
        <?php foreach ($courses as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $course_id == $c['id'] ? 'selected' : '' ?>>
                <?= $c['course_code'] ?> - <?= $c['course_name'] ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($course_id && count($results) > 0): ?>
    <br>
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Matricule</th>
                <th>CA</th>
                <th>Exam</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= $row['matricule'] ?></td>
                    <td><?= $row['ca_mark'] ?></td>
                    <td><?= $row['exam_mark'] ?></td>
                    <td><strong><?= $row['total'] ?></strong></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php elseif ($course_id): ?>
    <p style="text-align:center;">No marks found for this course.</p>
<?php endif; ?>

</body>
</html>
