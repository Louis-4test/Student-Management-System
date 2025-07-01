<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

require '../includes/db.php';

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Delete the student
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$student_id]);
}

header("Location: student.php");
exit;
?>
