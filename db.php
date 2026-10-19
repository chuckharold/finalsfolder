<?php
$conn = new mysqli("localhost", "root", "", "pcnl_sis");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
