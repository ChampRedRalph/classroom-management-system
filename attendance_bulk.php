<?php
// filepath: e:\xampp\htdocs\classroom-management-system\attendance_bulk.php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get date from form or default to today
$date = $_GET['date'] ?? date('Y-m-d');

// Fetch students
$stmt = $pdo->query("SELECT lrn, first_name, last_name, gender FROM tbl_students ORDER BY gender, last_name, first_name");
$students = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    foreach ($_POST['attendance'] as $lrn => $status) {
        // Get student id
        $stmt = $pdo->prepare("SELECT id FROM tbl_students WHERE lrn = ?");
        $stmt->execute([$lrn]);
        $student = $stmt->fetch();
        if ($student) {
            // Insert or update attendance
            $stmt2 = $pdo->prepare("REPLACE INTO tbl_attendance_raw (student_id, day, month, school_year, am_status, pm_status) VALUES (?, ?, ?, ?, ?, ?)");
            $day = date('j', strtotime($date));
            $month = date('F', strtotime($date));
            $school_year = $_POST['school_year'] ?? date('Y') . '-' . (date('Y')+1);
            // For simplicity, mark both AM and PM the same
            $stmt2->execute([$student['id'], $day, $month, $school_year, $status, $status]);
        }
    }
    $success = "Attendance saved for $date!";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Daily Attendance Bulk Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h3>Daily Attendance Bulk Entry</h3>
    <form method="get" class="mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="date" class="col-form-label">Date:</label>
            </div>
            <div class="col-auto">
                <input type="date" id="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Go</button>
            </div>
        </div>
    </form>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="post">
        <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
        <input type="hidden" name="school_year" value="<?= date('Y') . '-' . (date('Y')+1) ?>">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:200px;">Attendance</th>
                    <th>Name</th>
                    <th>Gender</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td>
                            <select name="attendance[<?= htmlspecialchars($student['lrn']) ?>]" class="form-select attendance-select" required>
                                <option value="">Select</option>
                                <option value="Present" style="color:green;" selected>Present</option>
                                <option value="Absent" style="color:red;">Absent</option>
                                <option value="HD" style="color:orange;">Half Day</option>
                            </select>
                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                function updateSelectColor(select) {
                                    let color = '';
                                    let bg = '';
                                    switch(select.value) {
                                        case 'Present':
                                            color = 'white';
                                            bg = 'green';
                                            break;
                                        case 'Absent':
                                            color = 'white';
                                            bg = 'red';
                                            break;
                                        case 'HD':
                                            color = 'black';
                                            bg = 'orange';
                                            break;
                                        default:
                                            color = '';
                                            bg = '';
                                            break;
                                    }
                                    select.style.color = color;
                                    select.style.backgroundColor = bg;
                                }
                                document.querySelectorAll('.attendance-select').forEach(function(select) {
                                    updateSelectColor(select);
                                    select.addEventListener('change', function() {
                                        updateSelectColor(this);
                                    });
                                });
                            });
                            </script>
                        </td>
                        <td><?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?></td>
                        <td><?= htmlspecialchars($student['gender']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Save Attendance</button>
        <a href="manage_class.php" class="btn btn-secondary">Back to Class</a>
    </form>
</div>
<button id="theme-toggle" class="btn btn-outline-secondary position-fixed" style="top: 1rem; right: 1rem; z-index: 1050;">
    Switch to Dark Mode
</button>
<script>
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