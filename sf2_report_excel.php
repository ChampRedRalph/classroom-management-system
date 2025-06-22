<?php
// filepath: e:\xampp\htdocs\classroom-management-system\sf2_report_excel.php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$month = $_GET['month'] ?? date('F');
$school_year = $_GET['school_year'] ?? '2024-2025';
$year = date('Y');
$days_in_month = cal_days_in_month(CAL_GREGORIAN, date('n', strtotime($month)), $year);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=SF2_{$month}_{$school_year}.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Fetch students
$stmt = $pdo->query("SELECT lrn, first_name, last_name FROM tbl_students ORDER BY last_name, first_name");
$students = $stmt->fetchAll();

// Fetch attendance
$stmt = $pdo->prepare("SELECT * FROM tbl_attendance_raw WHERE month = ? AND school_year = ?");
$stmt->execute([$month, $school_year]);
$attendance_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organize attendance
$attendance = [];
foreach ($attendance_raw as $row) {
    $attendance[$row['student_id']][$row['day']] = [
        'am' => $row['am_status'],
        'pm' => $row['pm_status']
    ];
}
$student_ids = [];
$stmt = $pdo->query("SELECT id, lrn FROM tbl_students");
while ($row = $stmt->fetch()) {
    $student_ids[$row['id']] = $row['lrn'];
    $lrn_to_id[$row['lrn']] = $row['id'];
}
?>
<table border="1">
    <tr>
        <th>Name</th>
        <?php for ($d = 1; $d <= $days_in_month; $d++): ?>
            <th><?= $d ?></th>
        <?php endfor; ?>
        <th>Total Present</th>
        <th>Total Absent</th>
    </tr>
    <?php foreach ($students as $student): ?>
        <?php
            $sid = $lrn_to_id[$student['lrn']] ?? null;
            $present = 0;
            $absent = 0;
        ?>
        <tr>
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
</table>