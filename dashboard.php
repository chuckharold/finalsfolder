<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$studentCount = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()["total"];
$userCount = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()["total"];
$courseCount = $conn->query("SELECT COUNT(DISTINCT course) AS total FROM students")->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - PCNL SIS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar">
    <strong>PCNL Student Portal</strong>
    <a href="dashboard.php">Dashboard</a>
    <a href="students.php">Students</a>
    <?php if ($_SESSION["role"] == "admin") { ?><a href="users.php">Users</a><?php } ?>
    <a href="logout.php">Log Out</a>
</div>

<div class="container">
    <h1>Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</p>

    <div class="card">
        <h2>Student Information System</h2>
        <p>This system helps the school keep student records organized.</p>
        <a class="button" href="student_form.php">+ Add Student</a>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:15px;">
        <div class="card"><p>Total Students</p><h1><?php echo $studentCount; ?></h1></div>
        <div class="card"><p>Courses</p><h1><?php echo $courseCount; ?></h1></div>
        <div class="card"><p>System Users</p><h1><?php echo $userCount; ?></h1></div>
    </div>

    <div class="card">
        <h2>Quick Links</h2>
        <p><a class="small-link" href="students.php">View student records</a></p>
        <?php if ($_SESSION["role"] == "admin") { ?>
            <p><a class="small-link" href="users.php">Manage system users</a></p>
        <?php } ?>
    </div>
</div>
</body>
</html>
