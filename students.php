<?php
session_start();
require 'db_connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch students from the database
$stmt = $pdo->query("SELECT lrn, first_name, last_name, birthdate, gender, grade_level, section FROM tbl_students ORDER BY gender, last_name, first_name");
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Students List</title>
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
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Students List</h4>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add Student</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>LRN</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Birthdate</th>
                            <th>Gender</th>
                            <th>Grade Level</th>
                            <th>Section</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($students) > 0): ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td>
                                        <a href="student_profile.php?lrn=<?= urlencode($student['lrn']) ?>">
                                            <?= htmlspecialchars($student['lrn']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($student['last_name']) ?></td>
                                    <td><?= htmlspecialchars($student['first_name']) ?></td>
                                    <td><?= htmlspecialchars($student['birthdate']) ?></td>
                                    <td><?= htmlspecialchars($student['gender']) ?></td>
                                    <td><?= htmlspecialchars($student['grade_level']) ?></td>
                                    <td><?= htmlspecialchars($student['section']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No students found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <a href="index.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="student_add.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">LRN</label>
                        <input type="text" name="lrn" class="form-control" required pattern="\d{12}" maxlength="12" inputmode="numeric" title="Please enter exactly 12 digits" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Birthdate</label>
                        <input type="date" name="birthdate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Grade Level</label>
                        <input type="text" name="grade_level" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Section</label>
                        <input type="text" name="section" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Student</button>
                </div>
            </form>
        </div>
    </div>
    <!-- End Add Student Modal -->

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