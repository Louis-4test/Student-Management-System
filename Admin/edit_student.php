<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

require '../includes/db.php';

// Get student ID
if (!isset($_GET['id'])) {
    header("Location: student.php");
    exit;
}

$student_id = $_GET['id'];

// Fetch departments
$departments = $pdo->query("SELECT * FROM departments")->fetchAll();

// Fetch student info
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    echo "Student not found!";
    exit;
}

// Handle update form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $matricule = $_POST['matricule'];
    $department_id = $_POST['department_id'];
    $new_password = $_POST['new_password'];

    if (!empty($new_password)) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE students SET full_name=?, email=?, matricule=?, password=?, department_id=? WHERE id=?");
        $stmt->execute([$full_name, $email, $matricule, $hashed, $department_id, $student_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE students SET full_name=?, email=?, matricule=?, department_id=? WHERE id=?");
        $stmt->execute([$full_name, $email, $matricule, $department_id, $student_id]);
    }

    header("Location: student.php?msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        form { width: 50%; margin: auto; background: #f9f9f9; padding: 20px; border-radius: 6px; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Edit Student</h2>

<form method="POST">
    <label>Full Name:</label>
    <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>

    <label>Matricule:</label>
    <input type="text" name="matricule" value="<?= htmlspecialchars($student['matricule']) ?>" required>

    <label>Department:</label>
    <select name="department_id" required>
        <option value="">-- Select Department --</option>
        <?php foreach ($departments as $d): ?>
            <option value="<?= $d['id'] ?>" <?= $student['department_id'] == $d['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>New Password (optional):</label>
    <input type="password" name="new_password">

    <br><br>
    <button type="submit">Update Student</button>
</form>

</body>
</html>
