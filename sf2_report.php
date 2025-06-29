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
$school_year = $_GET['school_year'] ?? '2025-2026';

// Get days in month (for current year)
$year = date('Y');
$days_in_month = cal_days_in_month(CAL_GREGORIAN, date('n', strtotime($month)), $year);

// Build an array of valid (non-weekend) days
$valid_days = [];
for ($d = 1; $d <= $days_in_month; $d++) {
    $date_str = sprintf('%04d-%02d-%02d', $year, date('n', strtotime($month)), $d);
    $day_of_week = date('N', strtotime($date_str)); // 6 = Saturday, 7 = Sunday
    if ($day_of_week < 6) {
        $valid_days[] = $d;
    }
}

// Fetch all students, sorted by gender (Male first), then by last name, first name
$stmt = $pdo->query("SELECT lrn, first_name, last_name, gender FROM tbl_students ORDER BY 
    CASE WHEN gender = 'Male' THEN 0 ELSE 1 END, last_name, first_name");
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
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .container-fluid {
            width: 100vw !important;
            max-width: 100vw !important;
            padding-left: 0;
            padding-right: 0;
        }
        .sf2-table th, .sf2-table td { font-size: 0.85rem; text-align: center; vertical-align: middle; }
        .sf2-table th.rotate { height: 120px; white-space: nowrap; }
        .sf2-table th.rotate > div { transform: translate(0px, 40px) rotate(-90deg); width: 20px; }
        .table-responsive { overflow-x: auto; }
    </style>
</head>
<body class="bg-light">
<div class="container-fluid py-4 sf2-margin" style="padding-left:2vw; padding-right:2vw;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="w-100 text-center">SF2 - Daily Attendance Report of Learners<br>
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
    <div class="mb-3">
        <div class="row">
            <div class="col-12">
                <table class="table table-borderless mb-0" style="font-size:1rem;">
                    <tr>
                        <td colspan="4" style="padding:0;">
                            <div style="border:2px solid #333; border-radius:8px; padding:10px; margin-bottom:5px;">
                                <table style="width:100%; margin-bottom:0;">
                                    <tr>
                                        <td><strong>School ID:</strong> 315407</td>
                                        <td><strong>School Year:</strong> <?= htmlspecialchars($school_year) ?></td>
                                        <td><strong>Report for the Month of:</strong> <?= htmlspecialchars($month) ?></td>
                                        <td><strong>Section:</strong> Generosity</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Name of School:</strong> East Gusa National High School</td>
                                        <td><strong>Grade Level:</strong> Grade 7</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered sf2-table">
            <!-- Table header -->
            <thead class="table-primary">
                <tr>
                    <th rowspan="2">Name</th>
                    <?php foreach ($valid_days as $d): ?>
                        <th ><div><?= $d ?></div></th>
                    <?php endforeach; ?>    
                    <th rowspan="2">Total Present</th>
                    <th rowspan="2">Total Absent</th>
                    <th rowspan="2" style="max-width:400px; width:400px;">Remarks</th>
                </tr>
                <tr>
                    <?php foreach ($valid_days as $d): ?>
                        <?php
                            $date_str = sprintf('%04d-%02d-%02d', $year, date('n', strtotime($month)), $d);
                            $day_letter = strtoupper(substr(date('l', strtotime($date_str)), 0, 1));
                        ?>
                        <th style="font-size:0.7em;"><?= $day_letter ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $gender_groups = ['Male', 'Female'];
                $combined_present_per_day = array_fill_keys($valid_days, 0);

                foreach ($gender_groups as $gender) {
                    $gender_students = array_filter($students, fn($s) => $s['gender'] === $gender);
                    $present_per_day = array_fill_keys($valid_days, 0);

                    foreach ($gender_students as $student) {
                        $sid = $lrn_to_id[$student['lrn']] ?? null;
                        echo '<tr>';
                        echo '<td class="text-start">' . htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) . '</td>';
                        $present = 0;
                        $absent = 0;
                        foreach ($valid_days as $d) {
                            $am = $attendance[$sid][$d]['am'] ?? '';
                            $pm = $attendance[$sid][$d]['pm'] ?? '';
                            $cell = '';
                            $is_present = 0;
                            $cell_style = '';
                            if ($am === 'Present' && $pm === 'Present') {
                                $cell = ' ';
                                $present++;
                                $is_present = 1;
                            } elseif ($am === 'Absent' && $pm === 'Absent') {
                                $cell = 'A';
                                $absent++;
                                $cell_style = ' style="background:#ffcccc;"'; // Make cell red
                            } elseif ($am === 'Present' && $pm === 'Absent') {
                                $cell = 'HD';
                                $present += 0.5; $absent += 0.5;
                                $is_present = 0.5;
                            } elseif ($am === 'Absent' && $pm === 'Present') {
                                $cell = 'HD';
                                $present += 0.5; $absent += 0.5;
                                $is_present = 0.5;
                            }
                            $present_per_day[$d] += $is_present;
                            $combined_present_per_day[$d] += $is_present;
                            echo '<td' . $cell_style . '>' . $cell . '</td>';
                        }
                        echo '<td>' . $present . '</td>';
                        echo '<td>' . $absent . '</td>';
                        echo '</tr>';
                    }

                    // Summary row for this gender
                    if (count($gender_students) > 0) {
                        echo '<tr style="font-weight:bold;">';
                        echo '<td style="background:#adb5bd;color:#fff;">' . $gender . ' Total Present</td>';
                        foreach ($valid_days as $d) {
                            echo '<td style="background:#adb5bd;color:#fff;">' . ($present_per_day[$d] > 0 ? $present_per_day[$d] : '') . '</td>';
                        }
                        echo '<td colspan="2" style="background:#adb5bd;color:#fff;"></td>';
                        echo '<td colspan="2" style="background:#adb5bd;color:#fff;"></td>';
                        echo '</tr>';
                    }
                }

                // Combined summary row
                echo '<tr style="font-weight:bold;">';
                echo '<td style="background:#495057;color:#fff;">Combined Total Present</td>';
                foreach ($valid_days as $d) {
                    echo '<td style="background:#495057;color:#fff;">' . ($combined_present_per_day[$d] > 0 ? $combined_present_per_day[$d] : '') . '</td>';
                }
                echo '<td colspan="2" style="background:#495057;color:#fff;"></td>';
                echo '<td colspan="2" style="background:#495057;color:#fff;"></td>';
                echo '</tr>';
                ?>
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