<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role"] != "admin") {
    die("Only an administrator can delete student records.");
}

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: students.php");
exit;
?>
