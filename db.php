<?php
$host = "127.0.0.1";
$db   = "governorforbes_db";
$user = "root"; // change if needed
$pass = "";     // change if needed

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset
$conn->set_charset("utf8mb4");
?>
