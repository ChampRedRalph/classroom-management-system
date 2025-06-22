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
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
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
</body>
</html>