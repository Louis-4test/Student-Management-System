<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require '../includes/db.php';

// Fetch all students
$stmt = $pdo->query("SELECT s.id, s.full_name, s.matricule, d.name AS department_name, f.name AS faculty_name
                     FROM students s
                     JOIN departments d ON s.department_id = d.id
                     JOIN faculties f ON d.faculty_id = f.id
                     ORDER BY s.full_name ASC");
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #007BFF;
        }

        .top-actions {
            text-align: center;
            margin-bottom: 20px;
        }

        .top-actions a {
            background: #28a745;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #007BFF;
            color: white;
        }

        .actions a {
            padding: 5px 10px;
            margin: 0 5px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
        }

        .edit-btn {
            background-color: #ffc107;
        }

        .delete-btn {
            background-color: #dc3545;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<h2>Student Management</h2>

<div class="top-actions">
    <a href="add_student.php">➕ Add New Student</a>
</div>

<table>
    <thead>
        <tr>
            <th>Full Name</th>
            <th>Matricule</th>
            <th>Department</th>
            <th>Faculty</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($students) > 0): ?>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['full_name']) ?></td>
                    <td><?= htmlspecialchars($student['matricule']) ?></td>
                    <td><?= htmlspecialchars($student['department_name']) ?></td>
                    <td><?= htmlspecialchars($student['faculty_name']) ?></td>
                    <td class="actions">
                        <a href="edit_student.php?id=<?= $student['id'] ?>" class="edit-btn">Edit</a>
                        <a href="delete_student.php?id=<?= $student['id'] ?>" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">No students found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="back-link" style="text-align: center;">
    <br><a href="dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
