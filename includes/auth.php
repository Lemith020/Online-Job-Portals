<?php
// -----------------------------------------------------------------
// auth.php
// Purpose : Session helpers. Included at the TOP of every page
// (before header.php) so redirects still work.
// -----------------------------------------------------------------
session_start();

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
