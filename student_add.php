<?php
// filepath: e:\xampp\htdocs\classroom-management-system\student_add.php
session_start();
require 'db_connect.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: students.php');
    exit;
}

// Get and sanitize form data
$lrn = trim($_POST['lrn'] ?? '');
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$birthdate = $_POST['birthdate'] ?? '';
$gender = $_POST['gender'] ?? '';
$grade_level = trim($_POST['grade_level'] ?? '');
$section = trim($_POST['section'] ?? '');

// Optional: Add more fields as needed

// Basic validation
if (
    empty($lrn) || empty($first_name) || empty($last_name) ||
    empty($birthdate) || empty($gender) || empty($grade_level) || empty($section)
) {
    $_SESSION['error'] = 'Please fill in all required fields.';
    header('Location: students.php');
    exit;
}

// Check for duplicate LRN
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_students WHERE lrn = ?");
$stmt->execute([$lrn]);
if ($stmt->fetchColumn() > 0) {
    $_SESSION['error'] = 'A student with this LRN already exists.';
    header('Location: students.php');
    exit;
}

// Insert student
$stmt = $pdo->prepare("INSERT INTO tbl_students (lrn, first_name, last_name, birthdate, gender, grade_level, section) VALUES (?, ?, ?, ?, ?, ?, ?)");
$success = $stmt->execute([
    $lrn, $first_name, $last_name, $birthdate, $gender, $grade_level, $section
]);

if ($success) {
    $_SESSION['success'] = 'Student added successfully.';
} else {
    $_SESSION['error'] = 'Failed to add student. Please try again.';
}

header('Location: students.php');
exit;