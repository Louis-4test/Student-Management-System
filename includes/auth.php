<?php
session_start();

/**
 * Check if student is logged in, else redirect to login page
 */
function checkStudentAuth() {
    if (!isset($_SESSION['student_id'])) {
        header('Location: ../login.php');
        exit;
    }
}

/**
 * Check if admin is logged in, else redirect to admin login page
 */
function checkAdminAuth() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: ../admin/login.php');
        exit;
    }
}

/**
 * Optional: Logout function to clear session
 */
function logout() {
    session_unset();
    session_destroy();
    header('Location: ../index.php');
    exit;
}
