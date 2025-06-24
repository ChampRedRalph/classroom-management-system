<?php
// filepath: e:\xampp\htdocs\classroom-management-system\sf2_report_excel.php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$month = $_GET['month'] ?? date('F');
$school_year = $_GET['school_year'] ?? '2025-2026';
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

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=SF2_{$month}_{$school_year}.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr><th colspan='" . (count($valid_days) + 3) . "'>SF2 - Daily Attendance Report of Learners ({$month} {$school_year})</th></tr>";
echo "<tr>";
echo "<th rowspan='2'>Name</th>";
foreach ($valid_days as $d) {
    echo "<th>{$d}</th>";
}
echo "<th rowspan='2'>Total Present</th><th rowspan='2'>Total Absent</th>";
echo "</tr><tr>";
foreach ($valid_days as $d) {
    $date_str = sprintf('%04d-%02d-%02d', $year, date('n', strtotime($month)), $d);
    $day_letter = strtoupper(substr(date('l', strtotime($date_str)), 0, 1));
    echo "<th style='font-size:0.7em;'>{$day_letter}</th>";
}
echo "</tr>";

$gender_groups = ['Male', 'Female'];
$combined_present_per_day = array_fill_keys($valid_days, 0);

foreach ($gender_groups as $gender) {
    $gender_students = array_filter($students, fn($s) => $s['gender'] === $gender);
    $present_per_day = array_fill_keys($valid_days, 0);

    foreach ($gender_students as $student) {
        $sid = $lrn_to_id[$student['lrn']] ?? null;
        echo "<tr>";
        echo "<td style='text-align:left'>" . htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) . "</td>";
        $present = 0;
        $absent = 0;
        foreach ($valid_days as $d) {
            $am = $attendance[$sid][$d]['am'] ?? '';
            $pm = $attendance[$sid][$d]['pm'] ?? '';
            $cell = '';
            $is_present = 0;
            if ($am === 'Present' && $pm === 'Present') {
                $cell = ' ';
                $present++;
                $is_present = 1;
            } elseif ($am === 'Absent' && $pm === 'Absent') {
                $cell = 'A';
                $absent++;
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
            echo "<td>{$cell}</td>";
        }
        echo "<td>{$present}</td>";
        echo "<td>{$absent}</td>";
        echo "</tr>";
    }

    // Summary row for this gender
    if (count($gender_students) > 0) {
        echo "<tr style='font-weight:bold;background:#f8f9fa'>";
        echo "<td>{$gender} Total Present</td>";
        foreach ($valid_days as $d) {
            echo "<td>" . ($present_per_day[$d] > 0 ? $present_per_day[$d] : '') . "</td>";
        }
        echo "<td colspan='2'></td>";
        echo "</tr>";
    }
}

// Combined summary row
echo "<tr style='font-weight:bold;background:#e2e3e5'>";
echo "<td>Combined Total Present</td>";
foreach ($valid_days as $d) {
    echo "<td>" . ($combined_present_per_day[$d] > 0 ? $combined_present_per_day[$d] : '') . "</td>";
}
echo "<td colspan='2'></td>";
echo "</tr>";

echo "</table>";