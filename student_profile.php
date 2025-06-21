<?php
session_start();
require 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get student ID from query string
$student_id = isset($_GET['lrn']) ? intval($_GET['lrn']) : 0;

if ($student_id <= 0) {
    header('Location: students.php');
    exit;
}

// Fetch student info
$stmt = $pdo->prepare("SELECT * FROM tbl_students WHERE lrn = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: students.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>
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
                    <h3 class="mb-4 text-center">Student Profile</h3>
                    <div class="text-center mb-4">
                        <?php if (!empty($student['profile_picture'])): ?>
                            <img src="<?= htmlspecialchars($student['profile_picture']) ?>" alt="Profile Picture" class="rounded-circle border" style="width: 120px; height: 120px; object-fit: cover;">
                        <?php else: ?>
                            <img src="img/pics/default_profile.png" alt="No Profile Picture" class="rounded-circle border" style="width: 120px; height: 120px; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                    <dl class="row">
                        <dt class="col-sm-4">LRN</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['lrn']) ?></dd>

                        <dt class="col-sm-4">First Name</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['first_name']) ?></dd>

                        <dt class="col-sm-4">Last Name</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['last_name']) ?></dd>

                        <dt class="col-sm-4">Birthdate</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['birthdate']) ?></dd>

                        <dt class="col-sm-4">Gender</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['gender']) ?></dd>

                        <dt class="col-sm-4">Address</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['address']) ?></dd>

                        <dt class="col-sm-4">Grade Level</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['grade_level']) ?></dd>

                        <dt class="col-sm-4">Section</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['section']) ?></dd>

                        <dt class="col-sm-4">Contact Number</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['contact_number']) ?></dd>

                        <dt class="col-sm-4">Guardian Name</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['guardian_name']) ?></dd>
                    </dl>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="students.php" class="btn btn-secondary">Back to Students</a>
                        <a href="student_edit.php?lrn=<?= urlencode($student['lrn']) ?>" class="btn btn-warning">Edit Profile</a>
                        <!-- You can add Attendance buttons here -->
                        <a href="student_attendance.php?lrn=<?= urlencode($student['lrn']) ?>" class="btn btn-info">View Attendance</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>