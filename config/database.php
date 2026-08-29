<?php
$host = "localhost";
$db_name = "online_job_portal_db";
$db_user = "root";
$db_pass = "";

$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}