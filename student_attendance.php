<?php
session_start();
require 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get student LRN and month from query string
$student_lrn = isset($_GET['lrn']) ? $_GET['lrn'] : '';
$month = isset($_GET['month']) ? $_GET['month'] : date('F'); // Default to current month

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

// Fetch attendance records for the month
$stmt = $pdo->prepare("SELECT * FROM tbl_attendance_raw WHERE student_id = ? AND month = ? ORDER BY day ASC");
$stmt->execute([$student['id'], $month]);
$attendance = $stmt->fetchAll();

// Get all months for dropdown
$months = [
    'June', 'July', 'August', 'September', 'October', 'November',
    'December', 'January', 'February', 'March', 'April'
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Attendance - <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></title>
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
            <div class="col-lg-10">
                <div class="card shadow p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="mb-0">Attendance for <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></h3>
                        <div>
                            <a href="student_profile.php?lrn=<?= urlencode($student['lrn']) ?>" class="btn btn-secondary btn-sm">Back to Profile</a>
                        </div>
                    </div>
                    <form method="get" class="mb-3">
                        <input type="hidden" name="lrn" value="<?= htmlspecialchars($student_lrn) ?>">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label for="month" class="col-form-label">Select Month:</label>
                            </div>
                            <div class="col-auto">
                                <select name="month" id="month" class="form-select" onchange="this.form.submit()">
                                    <?php foreach ($months as $m): ?>
                                        <option value="<?= $m ?>" <?= $month === $m ? 'selected' : '' ?>><?= $m ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>Day</th>
                                    <th>Day Name</th>
                                    <th>AM Status</th>
                                    <th>PM Status</th>
                                    <th>Tardy</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($attendance) > 0): ?>
                                    <?php foreach ($attendance as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['day']) ?></td>
                                            <td><?= htmlspecialchars($row['day_name']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $row['am_status'] === 'Present' ? 'success' : ($row['am_status'] === 'Tardy' ? 'warning text-dark' : 'danger') ?>">
                                                    <?= htmlspecialchars($row['am_status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $row['pm_status'] === 'Present' ? 'success' : ($row['pm_status'] === 'Tardy' ? 'warning text-dark' : 'danger') ?>">
                                                    <?= htmlspecialchars($row['pm_status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= $row['is_tardy'] ? '<span class="badge bg-warning text-dark">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No attendance records found for this month.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="students.php" class="btn btn-outline-primary mt-3">Back to Students List</a>
                </div>
            </div>
        </div>
    </div>
    <script src=