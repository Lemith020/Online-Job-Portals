<?php
session_start();


if (!isset($_SESSION['company_id'])) {
    $_SESSION['company_id'] = 1;
    $_SESSION['user_id']    = 1;
}

$company_id = $_SESSION['company_id'];

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /auth/login.php");
        exit();
    }
}

function requireRole($role) {
    requireLogin();
    if (($_SESSION['role'] ?? '') !== $role) {
        die("Access denied.");
    }
}

?>