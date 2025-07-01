<?php
require('fpdf/fpdf.php');
require 'includes/db.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    die('Unauthorized');
}

$student_id = $_SESSION['student_id'];
$semester = isset($_GET['semester']) ? intval($_GET['semester']) : 1;

// Fetch student info
$stmt = $pdo->prepare("SELECT s.full_name, s.matricule, p.name AS program_name, f.name AS faculty_name 
                       FROM students s
                       JOIN programs p ON s.program_id = p.id
                       JOIN faculties f ON p.faculty_id = f.id
                       WHERE s.id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

// Fetch marks for selected semester
$marks = $pdo->prepare("SELECT c.course_code, c.course_name, m.ca_mark, m.exam_mark 
                        FROM marks m
                        JOIN courses c ON m.course_id = c.id
                        WHERE m.student_id = ? AND m.semester = ?");
$marks->execute([$student_id, $semester]);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Semester ' . $semester . ' Transcript',0,1,'C');
$pdf->Ln(10);

// Student Info
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,10,'Name: ' . $student['full_name'],0,1);
$pdf->Cell(0,10,'Matricule: ' . $student['matricule'],0,1);
$pdf->Cell(0,10,'Faculty: ' . $student['faculty_name'],0,1);
$pdf->Cell(0,10,'Program: ' . $student['program_name'],0,1);
$pdf->Ln(10);

// Table headers
$pdf->SetFont('Arial','B',12);
$pdf->Cell(40,10,'Course Code',1);
$pdf->Cell(70,10,'Course Name',1);
$pdf->Cell(30,10,'CA',1);
$pdf->Cell(30,10,'Exam',1);
$pdf->Ln();

// Table data
$pdf->SetFont('Arial','',12);
foreach ($marks as $row) {
    $pdf->Cell(40,10,$row['course_code'],1);
    $pdf->Cell(70,10,$row['course_name'],1);
    $pdf->Cell(30,10,$row['ca_mark'],1);
    $pdf->Cell(30,10,$row['exam_mark'],1);
    $pdf->Ln();
}

$pdf->Output('I', 'Transcript_Semester_' . $semester . '.pdf');
