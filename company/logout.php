<?php
/**
 * JobPortal.lk - Company Logout
 */
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session clear 
session_unset();
session_destroy();

header("Location: " . BASE_URL . "/index.php");
exit;