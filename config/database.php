<?php
$host = "localhost";
$db_name = "test_company_part_db";
$db_user = "root";
$db_pass = "";

$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}