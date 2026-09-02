<?php
/**
 * JobPortal.lk - Portal Logout
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_name = $_SESSION['user']['name'] ?? 'User';
add_activity("Logged out: " . ($_SESSION['user']['email'] ?? 'Session ended'), 'auth');

// Unset all session variables
$_SESSION = [];

// Destroy session cookie if present
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Redirect to login page with flash message
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
set_flash("You have been logged out successfully.", "success");
header("Location: " . BASE_URL . "/auth/login.php");
exit;
