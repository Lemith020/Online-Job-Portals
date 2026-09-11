<?php
/**
 * JobPortal.lk - Company Logout
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session clear 
session_unset();
session_destroy();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
set_flash("You have been logged out successfully.", "success");

header("Location: " . BASE_URL . "/index.php");
exit;