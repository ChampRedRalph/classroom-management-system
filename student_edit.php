<?php
session_start();
require 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get student LRN from query string
$student_lrn = isset($_GET['lrn']) ? $_GET['lrn'] : '';
if (!$student_lrn) {
    header('Location: students.php');
    exit;
}

// Fetch student info
$stmt = $pdo->prepare("SELECT * FROM tbl_students WHERE lrn = ?");
$stmt->execute([$student_lrn]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: students.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $birthdate = $_POST['birthdate'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = $_POST['address'] ?? '';
    $grade_level = $_POST['grade_level'] ?? '';
    $section = $_POST['section'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $guardian_name = $_POST['guardian_name'] ?? '';
    $profile_picture = $student['profile_picture'];

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "img/pics/";
        $file_name = uniqid() . "_" . basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $profile_picture = $target_file;
        }
    }

    $stmt = $pdo->prepare("UPDATE tbl_students SET first_name=?, last_name=?, birthdate=?, gender=?, address=?, grade_level=?, section=?, contact_number=?, guardian_name=?, profile_picture=? WHERE lrn=?");
    $updated = $stmt->execute([
        $first_name, $last_name, $birthdate, $gender, $address, $grade_level, $section, $contact_number, $guardian_name, $profile_picture, $student_lrn
    ]);

    if ($updated) {
        $success = "Profile updated successfully!";
        // Refresh student data
        $stmt = $pdo->prepare("SELECT * FROM tbl_students WHERE lrn = ?");
        $stmt->execute([$student_lrn]);
        $student = $stmt->fetch();
    } else {
        $error = "Failed to update profile.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Student Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Classroom Management</a>
            <div class="ms-auto">
                <span class="navbar-text text-white me-3">
                    <?= htmlspecialchars($_SESSION['full_name']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow p-4">
                    <h3 class="mb-4 text-center">Edit Student Profile</h3>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php elseif ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3 text-center">
                            <?php if (!empty($student['profile_picture'])): ?>
                                <img src="<?= htmlspecialchars($student['profile_picture']) ?>" alt="Profile Picture" class="rounded-circle border mb-2" style="width: 120px; height: 120px; object-fit: cover;">
                            <?php else: ?>
                                <img src="img/pics/default_profile.png" alt="No Profile Picture" class="rounded-circle border mb-2" style="width: 120px; height: 120px; object-fit: cover;">
                            <?php endif; ?>
                            <input type="file" name="profile_picture" class="form-control mt-2">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($student['first_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($student['last_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Birthdate</label>
                            <input type="date" name="birthdate" class="form-control" value="<?= htmlspecialchars($student['birthdate']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="Male" <?= $student['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= $student['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($student['address']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Grade Level</label>
                            <input type="text" name="grade_level" class="form-control" value="<?= htmlspecialchars($student['grade_level']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Section</label>
                            <input type="text" name="section" class="form-control" value="<?= htmlspecialchars($student['section']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" value="<?= htmlspecialchars($student['contact_number']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guardian Name</label>
                            <input type="text" name="guardian_name" class="form-control" value="<?= htmlspecialchars($student['guardian_name']) ?>">
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="student_profile.php?lrn=<?= urlencode($student['lrn']) ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>