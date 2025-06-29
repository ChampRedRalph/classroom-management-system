<?php
require 'db_connect.php';

$show_data = false;
$error = '';
$student = null;
$attendance = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lrn = $_POST['lrn'] ?? '';
    $code = strtolower(trim($_POST['code'] ?? ''));

    // Find student by LRN
    $stmt = $pdo->prepare("SELECT * FROM tbl_students WHERE lrn = ?");
    $stmt->execute([$lrn]);
    $student = $stmt->fetch();

    if ($student) {
        // Generate code: first 2 of first name + last 2 of last name
        $first = strtolower($student['first_name']);
        $last = strtolower($student['last_name']);
        $expected_code = substr($first, 0, 2) . substr($last, -2);

        if ($code === $expected_code) {
            // Fetch attendance records
            $stmt2 = $pdo->prepare("SELECT * FROM tbl_attendance_raw WHERE student_id = ? ORDER BY month, day");
            $stmt2->execute([$student['id']]);
            $attendance = $stmt2->fetchAll();
            $show_data = true;
        } else {
            $error = "Invalid code. Please check your entry.";
            $student = null;
        }
    } else {
        $error = "No student found with that LRN.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student View - Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-4">
    <h3 class="mb-4 text-center">Student </h3>

    <!-- Modal Trigger (hidden, auto-triggered) -->
    <button id="openModalBtn" type="button" class="btn btn-primary d-none" data-bs-toggle="modal" data-bs-target="#lrnModal">
        Open Modal
    </button>

    <!-- LRN & Code Modal -->
    <div class="modal fade" id="lrnModal" tabindex="-1" aria-labelledby="lrnModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="post" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="lrnModalLabel">Enter Student Details</h5>
                </div>
                <div class="modal-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">LRN</label>
                        <input type="text" name="lrn" class="form-control" required pattern="\d{12}" maxlength="12" inputmode="numeric" title="Please enter exactly 12 digits" autocomplete="off" value="<?= htmlspecialchars($_POST['lrn'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code<br>
                            <small class="text-muted">First 2 letters of first name + last 2 letters of last name (e.g. <b>Joer</b> for John Er)</small>
                        </label>
                        <input type="text" name="code" class="form-control" required maxlength="4" autocomplete="off" value="<?= htmlspecialchars($_POST['code'] ?? '') ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">View Student</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($show_data && $student): ?>
        <div class="card shadow mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Absences for <?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?></h5>
            </div>
            <div class="card-body">
                <?php
                $absences = array_filter($attendance, function($row) {
                    return (strtolower($row['am_status']) === 'absent' || strtolower($row['pm_status']) === 'absent');
                });
                ?>
                <?php if (count($absences) > 0): ?>
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Month</th>
                                <th>Day</th>
                                <th>AM Status</th>
                                <th>PM Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($absences as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['month']) ?></td>
                                    <td><?= htmlspecialchars($row['day']) ?></td>
                                    <td<?= strtolower($row['am_status']) === 'absent' ? ' style="background:#ffcccc;"' : '' ?>>
                                        <?= htmlspecialchars($row['am_status']) ?>
                                    </td>
                                    <td<?= strtolower($row['pm_status']) === 'absent' ? ' style="background:#ffcccc;"' : '' ?>>
                                        <?= htmlspecialchars($row['pm_status']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-success mb-0">No absences found.</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Auto-open modal if not showing data or if there was an error
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!$show_data): ?>
            var modal = new bootstrap.Modal(document.getElementById('lrnModal'));
            modal.show();
        <?php endif; ?>

        // Auto-close page after 1 minute (60,000 ms)
        setTimeout(function() {
            window.location.href = "index.php";
        }, 60000);
    });
</script>
</body>
</html>