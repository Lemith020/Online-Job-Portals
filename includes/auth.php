
<?php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check whether user is logged in
function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit;
    }
}

// Check user's role
function requireRole($role)
{
    requireLogin();

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header("Location: ../auth/login.php");
        exit;
    }
}
