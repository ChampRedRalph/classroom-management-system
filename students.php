<?php
session_start();
require 'db_connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch students from the database
$stmt = $pdo->query("SELECT lrn, first_name, last_name, birthdate, gender, grade_level, section FROM tbl_students ORDER BY last_name, first_name");
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
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Students List</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>LRN</th>
                            <th>First Name</th>
                            <th>Last Name</th>
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
                                    <td><?= htmlspecialchars($student['first_name']) ?></td>
                                    <td><?= htmlspecialchars($student['last_name']) ?></td>
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
</body>