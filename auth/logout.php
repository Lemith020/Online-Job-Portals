<?php
/**
 * JobPortal.lk - User Logout
 * Member 1 - Core/Shared
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Unset user session
if (isset($_SESSION['user'])) {
    add_activity("User logged out: " . ($_SESSION['user']['email'] ?? 'admin'), 'auth');
    unset($_SESSION['user']);
}

set_flash('You have been successfully logged out.', 'info');
header('Location: ' . BASE_URL . '/auth/login.php');
exit;
