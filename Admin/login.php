<?php
session_start();
require_once '../includes/auth.php';
checkAdminAuth();
require '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt) {
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            echo "Invalid credentials.";
        }
    } else {
        echo "Database query error.";
    }
}
?>
