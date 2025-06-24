<?php
// filepath: e:\xampp\htdocs\classroom-management-system\manage_class.php
session_start();
require 'db_connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Load class info from JSON file or use default values
if (file_exists('class_info.json')) {
    $class_info = json_decode(file_get_contents('class_info.json'), true);
} else {
    $class_info = [
        'grade_level' => 'Grade 6',
        'section' => 'Section 1',
        'adviser' => 'Mr. Example Name',
        'school_year' => '2024-2025',
        'class_photo' => 'img/class_photo.jpg' // Optional: path to class photo
    ];
}

// Fetch students for this class
$stmt = $pdo->prepare("SELECT lrn, first_name, last_name, gender FROM tbl_students ORDER BY last_name, first_name");
$stmt->execute();
$students = $stmt->fetchAll();

// Example statistics
$total_students = count($students);
$total_male = count(array_filter($students, fn($s) => $s['gender'] === 'Male'));
$total_female = count(array_filter($students, fn($s) => $s['gender'] === 'Female'));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Class</title>
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
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Class Information</h4>
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editClassModal">Edit</button>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Grade Level</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($class_info['grade_level']) ?></dd>
                    <dt class="col-sm-4">Section</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($class_info['section']) ?></dd>
                    <dt class="col-sm-4">Adviser</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($class_info['adviser']) ?></dd>
                    <dt class="col-sm-4">School Year</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($class_info['school_year']) ?></dd>
                </dl>
                <?php if (!empty($class_info['class_photo']) && file_exists($class_info['class_photo'])): ?>
                    <div class="mb-3 text-center">
                        <img src="<?= htmlspecialchars($class_info['class_photo']) ?>" alt="Class Photo" class="img-fluid rounded shadow" style="max-width:300px;">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Edit Class Modal -->
        <div class="modal fade" id="editClassModal" tabindex="-1" aria-labelledby="editClassModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="post" action="manage_class_edit.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editClassModalLabel">Edit Class Information</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Grade Level</label>
                            <input type="text" name="grade_level" class="form-control" value="<?= htmlspecialchars($class_info['grade_level']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Section</label>
                            <input type="text" name="section" class="form-control" value="<?= htmlspecialchars($class_info['section']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Adviser</label>
                            <input type="text" name="adviser" class="form-control" value="<?= htmlspecialchars($class_info['adviser']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">School Year</label>
                            <input type="text" name="school_year" class="form-control" value="<?= htmlspecialchars($class_info['school_year']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Class Photo (optional)</label>
                            <input type="file" name="class_photo" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header bg-info text-dark">
                <h5 class="mb-0">Class Statistics</h5>
            </div>
            <div class="card-body">
                <span class="badge bg-primary me-2">Total Students: <?= $total_students ?></span>
                <span class="badge bg-success me-2">Male: <?= $total_male ?></span>
                <span class="badge bg-warning text-dark">Female: <?= $total_female ?></span>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Student List</h5>
                <a href="students.php" class="btn btn-outline-light btn-sm">View All Students</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>LRN</th>
                            <th>Name</th>
                            <th>Gender</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($students): ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?= htmlspecialchars($student['lrn']) ?></td>
                                    <td>
                                        <a href="student_profile.php?lrn=<?= urlencode($student['lrn']) ?>">
                                            <?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($student['gender']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No students found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- You can add more sections here, e.g., Announcements, Schedule, etc. -->

        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
            <h5 class="mb-0">Attendance Report</h5>
            </div>
            <div class="card-body">
            <a href="sf2_report.php" class="btn btn-primary" target="_blank">Generate SF2 Attendance Report</a>
            <a href="attendance_bulk.php" class="btn btn-warning">Bulk Attendance Entry</a>
            </div>
        </div>
        <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <!-- Theme Toggle Button -->
    <button id="theme-toggle" class="btn btn-outline-secondary position-fixed" style="top: 1rem; right: 1rem; z-index: 1050;">
        Switch to Dark Mode
    </button>
    <script>
        // Check saved theme or default to light
        function setTheme(theme) {
            if (theme === 'dark') {
                document.body.classList.add('bg-dark', 'text-light');
                document.querySelectorAll('.card, .navbar, .table, .form-control, .form-select').forEach(el => {
                    el.classList.add('bg-dark', 'text-light', 'border-secondary');
                });
                document.getElementById('theme-toggle').textContent = 'Switch to Light Mode';
            } else {
                document.body.classList.remove('bg-dark', 'text-light');
                document.querySelectorAll('.card, .navbar, .table, .form-control, .form-select').forEach(el => {
                    el.classList.remove('bg-dark', 'text-light', 'border-secondary');
                });
                document.getElementById('theme-toggle').textContent = 'Switch to Dark Mode';
            }
            localStorage.setItem('theme', theme);
        }

        // On load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);

            document.getElementById('theme-toggle').addEventListener('click', function() {
                const currentTheme = localStorage.getItem('theme') === 'dark' ? 'light' : 'dark';
                setTheme(currentTheme);
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>