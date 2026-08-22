<?php
// -----------------------------------------------------------------
// Database Connection
// Purpose : Every other PHP file includes this to get $conn
// -----------------------------------------------------------------

$host = "localhost";
$dbname = "online_job_portal_db";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// TODO: move these credentials to a .env file for real projects
