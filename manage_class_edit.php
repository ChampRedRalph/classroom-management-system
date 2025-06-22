<?php
// filepath: e:\xampp\htdocs\classroom-management-system\manage_class_edit.php
session_start();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manage_class.php');
    exit;
}

// Get and sanitize form data
$grade_level = trim($_POST['grade_level'] ?? '');
$section = trim($_POST['section'] ?? '');
$adviser = trim($_POST['adviser'] ?? '');
$school_year = trim($_POST['school_year'] ?? '');

// Handle class photo upload
$class_photo = 'img/class_photo.jpg'; // Default path
if (isset($_FILES['class_photo']) && $_FILES['class_photo']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['class_photo']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($ext, $allowed)) {
        $target_dir = 'img/';
        $target_file = $target_dir . 'class_photo.' . $ext;
        if (move_uploaded_file($_FILES['class_photo']['tmp_name'], $target_file)) {
            $class_photo = $target_file;
        }
    }
}

// Save class info to a file (class_info.json)
$class_info = [
    'grade_level' => $grade_level,
    'section' => $section,
    'adviser' => $adviser,
    'school_year' => $school_year,
    'class_photo' => $class_photo
];
file_put_contents('class_info.json', json_encode($class_info));

// Redirect back to manage_class.php
header('Location: manage_class.php');
exit;