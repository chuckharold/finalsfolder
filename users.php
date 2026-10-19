<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    die("Only an administrator can access this page.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    if ($name == "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $message = "Enter a name, valid email, and password with at least 8 characters.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

        if ($stmt->execute()) {
            $message = "User added successfully.";
        } else {
            $message = "Could not add user. The email may already be registered.";
        }
    }
}

$users = $conn->query("SELECT id, name, email, role FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users - PCNL SIS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar">
    <strong>PCNL Student Portal</strong>
    <a href="dashboard.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="users.php">Users</a>
    <a href="logout.php">Log Out</a>
</div>
<div class="container">
    <h1>System Users</h1>
    <div class="card">
        <h2>Add a User</h2>
        <?php if ($message != "") { ?><div class="message"><?php echo htmlspecialchars($message); ?></div><?php } ?>
        <form method="POST">
            <label>Full Name</label>
            <input name="name" required>
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Password (at least 8 characters)</label>
            <input type="password" name="password" minlength="8" required>
            <label>Role</label>
            <select name="role"><option value="staff">Staff</option><option value="admin">Admin</option></select>
            <button type="submit">Add User</button>
        </form>
    </div>
    <div class="card" style="overflow-x:auto;">
        <h2>Registered Users</h2>
        <table>
            <tr><th>Name</th><th>Email</th><th>Role</th></tr>
            <?php while ($user = $users->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($user["name"]); ?></td>
                    <td><?php echo htmlspecialchars($user["email"]); ?></td>
                    <td><?php echo htmlspecialchars($user["role"]); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
</body>
</html>
