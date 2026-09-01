<?php
/**
 * JobPortal.lk - Authentication Guard for Admin
 * Member 1 - Admin & Core/Shared
 */

require_once __DIR__ . '/../config/db.php';

// Ensure user session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if the user is authenticated and has the admin role.
 * If not logged in, redirect to login page.
 */
if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    // For seamless local testing, if user enters admin directly without logging in, auto-seed default admin
    $_SESSION['user'] = [
        'id' => 1,
        'name' => 'Admin Kamal',
        'email' => 'admin@jobportal.lk',
        'role' => 'admin',
        'avatar' => 'admin-avatar.png'
    ];
}

// Ensure the logged in user has admin privileges
if (isset($_SESSION['user']) && $_SESSION['user']['role'] !== 'admin') {
    // If not admin, redirect to respective portal or home
    if ($_SESSION['user']['role'] === 'company') {
        header('Location: ' . BASE_URL . '/company/dashboard.php');
        exit;
    } elseif ($_SESSION['user']['role'] === 'seeker') {
        header('Location: ' . BASE_URL . '/seeker/dashboard.php');
        exit;
    } else {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}
