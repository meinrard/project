<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "FINGERPRINT";

// Create connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Ensure UTF-8 for all database queries
mysqli_set_charset($conn, "utf8");

?>