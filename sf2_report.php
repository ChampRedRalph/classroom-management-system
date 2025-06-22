<?php
// filepath: e:\xampp\htdocs\classroom-management-system\sf2_report.php
session_start();
require 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get month and school year from query or default to current
$month = $_GET['month'] ?? date('F');
$school_year = $_GET['school_year'] ?? '2024-2025';

// Get days in month (for current year)
$year = date('Y');
$days_in_month = cal_days_in_month(CAL_GREGORIAN, date('n', strtotime($month)), $year);

// Fetch all students
$stmt = $pdo->query("SELECT lrn, first_name, last_name FROM tbl_students ORDER BY last_name, first_name");
$students = $stmt->fetchAll();

// Fetch all attendance for the month
$stmt = $pdo->prepare("SELECT * FROM tbl_attendance_raw WHERE month = ? AND school_year = ?");
$stmt->execute([$month, $school_year]);
$attendance_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organize attendance by student and day
$attendance = [];
foreach ($attendance_raw as $row) {
    $attendance[$row['student_id']][$row['day']] = [
        'am' => $row['am_status'],
        'pm' => $row['pm_status']
    ];
}

// Get student IDs for mapping
$student_ids = [];
$stmt = $pdo->query("SELECT id, lrn FROM tbl_students");
while ($row = $stmt->fetch()) {
    $student_ids[$row['id']] = $row['lrn'];
    $lrn_to_id[$row['lrn']] = $row['id'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SF2 - Daily Attendance Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sf2-table th, .sf2-table td { font-size: 0.85rem; text-align: center; vertical-align: middle; }
        .sf2-table th.rotate { height: 120px; white-space: nowrap; }
        .sf2-table th.rotate > div { transform: translate(0px, 40px) rotate(-90deg); width: 20px; }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>SF2 - Daily Attendance Report of Learners<br>
            <small class="text-muted"><?= htmlspecialchars($month) ?> <?= $school_year ?></small>
        </h4>
        <form method="get" class="d-flex align-items-center">
            <select name="month" class="form-select me-2" onchange="this.form.submit()">
                <?php foreach (['June','July','August','September','October','November','December','January','February','March','April'] as $m): ?>
                    <option value="<?= $m ?>" <?= $month === $m ? 'selected' : '' ?>><?= $m ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="school_year" class="form-control me-2" value="<?= htmlspecialchars($school_year) ?>" style="width:120px;" placeholder="SY">
            <button type="submit" class="btn btn-primary btn-sm">Go</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered sf2-table">
            <thead class="table-primary">
                <tr>
                    <!-- <th rowspan="2">LRN</th> --> <!-- Removed LRN column -->
                    <th rowspan="2">Name</th>
                    <?php for ($d = 1; $d <= $days_in_month; $d++): ?>
                        <th class="rotate"><div><?= $d ?></div></th>
                    <?php endfor; ?>
                    <th rowspan="2">Total Present</th>
                    <th rowspan="2">Total Absent</th>
                </tr>
                <tr>
                    <?php for ($d = 1; $d <= $days_in_month; $d++): ?>
                        <th style="font-size:0.7em;">AM/PM</th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <?php
                        $sid = $lrn_to_id[$student['lrn']] ?? null;
                        $present = 0;
                        $absent = 0;
                    ?>
                    <tr>
                        <!-- <td><?= htmlspecialchars($student['lrn']) ?></td> --> <!-- Removed LRN cell -->
                        <td><?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?></td>
                        <?php for ($d = 1; $d <= $days_in_month; $d++): ?>
                            <?php
                                $am = $attendance[$sid][$d]['am'] ?? '';
                                $pm = $attendance[$sid][$d]['pm'] ?? '';
                                $cell = '';
                                if ($am === 'Present' && $pm === 'Present') {
                                    $cell = 'P';
                                    $present++;
                                } elseif ($am === 'Absent' && $pm === 'Absent') {
                                    $cell = 'A';
                                    $absent++;
                                } elseif ($am === 'Present' && $pm === 'Absent') {
                                    $cell = '½';
                                    $present += 0.5; $absent += 0.5;
                                } elseif ($am === 'Absent' && $pm === 'Present') {
                                    $cell = '½';
                                    $present += 0.5; $absent += 0.5;
                                } else {
                                    $cell = '';
                                }
                            ?>
                            <td><?= $cell ?></td>
                        <?php endfor; ?>
                        <td><?= $present ?></td>
                        <td><?= $absent ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between mt-3">
        <a href="manage_class.php" class="btn btn-secondary">Back to Manage Class</a>
        <div>
            <a href="sf2_report_excel.php?month=<?= urlencode($month) ?>&school_year=<?= urlencode($school_year) ?>" class="btn btn-success me-2">Download as Excel</a>
            <button onclick="window.print()" class="btn btn-outline-primary">Print Report</button>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>