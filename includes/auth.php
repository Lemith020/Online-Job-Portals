<?php
/**
 * JobPortal.lk - Session and Authentication Gate
 * Handles authentication checks for all roles across the portal.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

// Normalize session user object for compatibility across member branches
if (isset($_SESSION['user_id']) && !isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name'] ?? 'User',
        'email' => $_SESSION['user_email'] ?? '',
        'role'  => $_SESSION['role'] ?? 'seeker'
    ];
} elseif (isset($_SESSION['user']) && !isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_SESSION['user']['id'];
    $_SESSION['role'] = $_SESSION['user']['role'];
    $_SESSION['user_name'] = $_SESSION['user']['name'];
    $_SESSION['user_email'] = $_SESSION['user']['email'];
}

/**
 * Check if a user is currently logged in
 */
function is_logged_in() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

/**
 * Get the current user's role
 */
function get_current_role() {
    if (!is_logged_in()) {
        return 'guest';
    }
    return $_SESSION['user']['role'] ?? 'seeker';
}

/**
 * Enforce role access or redirect
 */
function require_role($allowed_roles = []) {
    if (is_string($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }

    if (!is_logged_in()) {
        header("Location: " . BASE_URL . "/auth/login.php");
        exit;
    }

    $current_role = get_current_role();
    if (!empty($allowed_roles) && !in_array($current_role, $allowed_roles)) {
        if ($current_role === 'admin') {
            header("Location: " . BASE_URL . "/admin/dashboard.php");
        } elseif ($current_role === 'company') {
            header("Location: " . BASE_URL . "/company/dashboard.php");
        } else {
            header("Location: " . BASE_URL . "/seeker/dashboard.php");
        }
        exit;
    }
}
