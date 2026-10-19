<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$search = "";
if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
    $term = "%" . $search . "%";
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_no LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR course LIKE ? ORDER BY last_name");
    $stmt->bind_param("ssss", $term, $term, $term, $term);
    $stmt->execute();
    $students = $stmt->get_result();
} else {
    $students = $conn->query("SELECT * FROM students ORDER BY last_name");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Students - PCNL SIS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar">
    <strong>PCNL Student Portal</strong>
    <a href="dashboard.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="logout.php">Log Out</a>
</div>
<div class="container">
    <h1>Student Records</h1>
    <div class="card">
        <form method="GET">
            <label>Search student</label>
            <input name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Student number, name, or course">
            <button type="submit">Search</button>
            <a class="button" href="student_form.php">+ Add Student</a>
        </form>
    </div>

    <div class="card" style="overflow-x:auto;padding:10px;">
        <table>
            <tr>
                <th>Student No.</th><th>Name</th><th>Course</th><th>Year</th><th>Status</th><th>Actions</th>
            </tr>
            <?php if ($students->num_rows > 0) { ?>
                <?php while ($student = $students->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student["student_no"]); ?></td>
                        <td><?php echo htmlspecialchars($student["last_name"] . ", " . $student["first_name"]); ?></td>
                        <td><?php echo htmlspecialchars($student["course"]); ?></td>
                        <td><?php echo htmlspecialchars($student["year_level"]); ?></td>
                        <td><?php echo htmlspecialchars($student["status"]); ?></td>
                        <td>
                            <a class="small-link" href="student_form.php?id=<?php echo $student["id"]; ?>">Edit</a>
                            <?php if ($_SESSION["role"] == "admin") { ?>
                                | <a class="small-link" href="delete_student.php?id=<?php echo $student["id"]; ?>" onclick="return confirm('Delete this student?');">Delete</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="6">No student records found.</td></tr>
            <?php } ?>
        </table>
    </div>
</div>
</body>
</html>
