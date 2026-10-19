<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            session_regenerate_id(true);
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["role"] = $user["role"];
            header("Location: dashboard.php");
            exit;
        }
    }

    $error = "Incorrect email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - PCNL SIS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="max-width:430px; margin-top:70px;">
    <div class="card">
        <h1 style="color:#173b68;">PCNL Student Portal</h1>
        <p>Student Information System</p>
        <p>Log in to manage student records.</p>

        <?php if ($error != "") { ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit" style="width:100%;">Log In</button>
        </form>
    </div>
</div>
</body>
</html>
