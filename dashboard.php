<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?>!</h2>
    <p>Your role: <?= htmlspecialchars($_SESSION['role']) ?></p>
    <a href="logout.php">Logout</a>
</body>
</html>