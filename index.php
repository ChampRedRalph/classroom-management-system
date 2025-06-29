<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // Show dashboard content if logged in
    $full_name = $_SESSION['full_name'];
    $role = $_SESSION['role'];
} else {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Classroom Management</a>
            <div class="ms-auto">
                <span class="navbar-text text-white me-3">
                    <?= htmlspecialchars($full_name) ?> (<?= htmlspecialchars($role) ?>)
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container" style="max-width: 1000px; margin: 2rem auto;">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow p-4">
                    <div class="text-center mb-4">
                        <img src="logo.png" alt="School Logo" style="max-width: 100px; max-height: 100px;" onerror="this.style.display='none';">
                    </div>
                    <h2 class="mb-3 text-center">Welcome, <?= htmlspecialchars($full_name) ?>!</h2>
                    <p class="text-center mb-4">
                        Your role: <span class="badge bg-info text-dark"><?= htmlspecialchars($role) ?></span>
                    </p>
                    <div class="d-grid gap-2">
                        <a href="students.php" class="btn btn-primary">View Students</a>
                        <a href="manage_class.php" class="btn btn-secondary">Manage Classes</a>
                        <!-- Add more dashboard actions here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    require 'db_connect.php';

    // Fetch all students (include birthdate)
    $stmt = $pdo->query("SELECT lrn, first_name, last_name, gender, birthdate FROM tbl_students");
    $students = $stmt->fetchAll();

    // Birthday celebrants today
    $today = date('m-d');
    $birthday_students = [];
    foreach ($students as $student) {
        if (!empty($student['birthdate'])) {
            $bday = date('m-d', strtotime($student['birthdate']));
            if ($bday === $today) {
                $birthday_students[] = $student;
            }
        }
    }
    ?>

    <?php if (count($birthday_students) > 0): ?>
        <div class="container" style="max-width: 1000px; margin: 2rem auto;">
            <div class="card shadow mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">🎉 Birthday Celebrant<?= count($birthday_students) > 1 ? 's' : '' ?> Today</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($birthday_students as $s): ?>
                            <li class="list-group-item">
                                <strong><?= htmlspecialchars($s['last_name'] . ', ' . $s['first_name']) ?></strong>
                                (<?= htmlspecialchars($s['gender']) ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Students with High Absences Table -->
    <?php
    // Get current month and school year
    $currentMonth = date('F');
    $currentYear = date('Y');
    $school_year = date('Y') . '-' . (date('n') >= 6 ? date('Y', strtotime('+1 year')) : date('Y')); // Adjust as needed

    // Get absences for each student for the current month
    $high_absent_students = [];
    foreach ($students as $student) {
        // Get student id
        $stmt_id = $pdo->prepare("SELECT id FROM tbl_students WHERE lrn = ?");
        $stmt_id->execute([$student['lrn']]);
        $row = $stmt_id->fetch();
        $student_id = $row ? $row['id'] : null;

        if ($student_id) {
            $stmt2 = $pdo->prepare("SELECT COUNT(*) as absents FROM tbl_attendance_raw WHERE student_id = ? AND month = ? AND school_year = ? AND am_status = 'Absent' AND pm_status = 'Absent'");
            $stmt2->execute([$student_id, $currentMonth, $school_year]);
            $absents = $stmt2->fetchColumn();

            // You can adjust the threshold (e.g., >=3 absences)
            if ($absents >= 3) {
                $high_absent_students[] = [
                    'lrn' => $student['lrn'],
                    'name' => $student['last_name'] . ', ' . $student['first_name'],
                    'gender' => $student['gender'],
                    'absents' => $absents
                ];
            }
        }
    }
    ?>

    <?php if (count($high_absent_students) > 0): ?>
        <div class="container" style="max-width: 1000px; margin: 2rem auto;">
            <div class="card shadow mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Students with High Absences (<?= $currentMonth ?>)</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-danger">
                            <tr>
                                <th>LRN</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Total Absents</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($high_absent_students as $s): ?>
                                <tr>
                                    <td><?= htmlspecialchars($s['lrn']) ?></td>
                                    <td><?= htmlspecialchars($s['name']) ?></td>
                                    <td><?= htmlspecialchars($s['gender']) ?></td>
                                    <td class="fw-bold text-danger text-center"><?= $s['absents'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Prepare attendance data for the past 5 days -->
    <?php
    // Prepare attendance data for the past 5 days (excluding Saturdays and Sundays)
    $attendance_dates = [];
    $attendance_counts = [];
    $school_year = date('Y') . '-' . (date('n') >= 6 ? date('Y', strtotime('+1 year')) : date('Y')); // same as above

    $days_collected = 0;
    $days_checked = 0;
    while ($days_collected < 5) {
        $date = date('Y-m-d', strtotime("-$days_checked days"));
        $day_of_week = date('N', strtotime($date)); // 6 = Saturday, 7 = Sunday
        if ($day_of_week < 6) { // Only Monday to Friday
            $attendance_dates[] = $date;
            $day = date('j', strtotime($date));
            $month = date('F', strtotime($date));

            // Count present students (both AM and PM present)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_attendance_raw WHERE day = ? AND month = ? AND school_year = ? AND am_status = 'Present' AND pm_status = 'Present'");
            $stmt->execute([$day, $month, $school_year]);
            $attendance_counts[] = (int)$stmt->fetchColumn();

            $days_collected++;
        }
        $days_checked++;
    }
    // Reverse arrays so the latest day is last
    $attendance_dates = array_reverse($attendance_dates);
    $attendance_counts = array_reverse($attendance_counts);
    ?>

    <!-- Attendance Graph for the Past 5 Days -->
    <div class="container" style="max-width: 1000px; margin: 2rem auto;">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Attendance (Past 5 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Theme Toggle Button -->
    <button id="theme-toggle" class="btn btn-outline-secondary position-fixed" style="top: 1rem; right: 1rem; z-index: 1050;">
        Switch to Dark Mode
    </button>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_map(function($d) { return date('M d', strtotime($d)); }, $attendance_dates)) ?>,
                datasets: [{
                    label: 'Present Students',
                    data: <?= json_encode($attendance_counts) ?>,
                    backgroundColor: 'rgba(13,110,253,0.7)',
                    borderColor: 'rgba(13,110,253,1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, precision: 0 }
                }
            }
        });

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
</body>
</html>