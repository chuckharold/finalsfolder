<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$studentNo = "";
$firstName = "";
$lastName = "";
$course = "";
$yearLevel = "1st Year";
$email = "";
$status = "Enrolled";
$message = "";

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $student = $result->fetch_assoc();
        $studentNo = $student["student_no"];
        $firstName = $student["first_name"];
        $lastName = $student["last_name"];
        $course = $student["course"];
        $yearLevel = $student["year_level"];
        $email = $student["email"];
        $status = $student["status"];
    } else {
        die("Student record not found.");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentNo = trim($_POST["student_no"]);
    $firstName = trim($_POST["first_name"]);
    $lastName = trim($_POST["last_name"]);
    $course = trim($_POST["course"]);
    $yearLevel = $_POST["year_level"];
    $email = trim($_POST["email"]);
    $status = $_POST["status"];

    if ($studentNo == "" || $firstName == "" || $lastName == "" || $course == "") {
        $message = "Please fill in all required fields.";
    } else {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE students SET student_no=?, first_name=?, last_name=?, course=?, year_level=?, email=?, status=? WHERE id=?");
            $stmt->bind_param("sssssssi", $studentNo, $firstName, $lastName, $course, $yearLevel, $email, $status, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO students (student_no, first_name, last_name, course, year_level, email, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $studentNo, $firstName, $lastName, $course, $yearLevel, $email, $status);
        }

        if ($stmt->execute()) {
            header("Location: students.php");
            exit;
        } else {
            $message = "Could not save. Check if the student number is already used.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $id > 0 ? "Edit" : "Add"; ?> Student - PCNL SIS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar">
    <strong>PCNL Student Portal</strong>
    <a href="dashboard.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="logout.php">Log Out</a>
</div>
<div class="container" style="max-width:750px;">
    <div class="card">
        <h1><?php echo $id > 0 ? "Edit Student" : "Add Student"; ?></h1>
        <?php if ($message != "") { ?><div class="message error"><?php echo htmlspecialchars($message); ?></div><?php } ?>
        <form method="POST">
            <label>Student Number *</label>
            <input name="student_no" value="<?php echo htmlspecialchars($studentNo); ?>" required>

            <label>First Name *</label>
            <input name="first_name" value="<?php echo htmlspecialchars($firstName); ?>" required>

            <label>Last Name *</label>
            <input name="last_name" value="<?php echo htmlspecialchars($lastName); ?>" required>

            <label>Course / Program *</label>
            <input name="course" value="<?php echo htmlspecialchars($course); ?>" placeholder="Example: BS Information Technology" required>

            <label>Year Level</label>
            <select name="year_level">
                <?php foreach (["1st Year", "2nd Year", "3rd Year", "4th Year", "5th Year"] as $year) { ?>
                    <option value="<?php echo $year; ?>" <?php if ($yearLevel == $year) echo "selected"; ?>><?php echo $year; ?></option>
                <?php } ?>
            </select>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">

            <label>Status</label>
            <select name="status">
                <?php foreach (["Enrolled", "Pending", "Graduated", "Inactive"] as $item) { ?>
                    <option value="<?php echo $item; ?>" <?php if ($status == $item) echo "selected"; ?>><?php echo $item; ?></option>
                <?php } ?>
            </select>

            <button type="submit"><?php echo $id > 0 ? "Save Changes" : "Add Student"; ?></button>
            <a class="button" href="students.php" style="background:#64748b;">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>
