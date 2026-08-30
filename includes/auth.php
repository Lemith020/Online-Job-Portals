
<?php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$company_id = $_SESSION['company_id'];

// Not logged in -> send to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: /Online-Job-Portal/auth/login.php");
    exit;
}

// Logged in but not a job seeker -> block access
if ($_SESSION['role'] !== 'job_seeker') {
    header("Location: /Online-Job-Portal/index.php");
    exit;
}

?>
