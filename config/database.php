<?php
/**
 * JobPortal.lk - Database Configuration & Connection
 * Compatible with MySQLi and PDO, with automatic offline fallback
 */

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Global Application Constants
if (!defined('APP_NAME')) define('APP_NAME', 'JobPortal.lk');
if (!defined('APP_VERSION')) define('APP_VERSION', '1.0.0');
if (!defined('ADMIN_EMAIL')) define('ADMIN_EMAIL', 'admin@jobportal.lk');

// Determine Base URL (Flexible for all team members)
if (!defined('BASE_URL')) {
    // Localhost host & protocol setup
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Project root folder detection
    $script_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    
    // Sub-folder trim (If page is inside company, seeker, auth, etc.)
    $sub_folders = ['/admin', '/auth', '/company', '/seeker', '/reviews', '/config', '/includes'];
    foreach ($sub_folders as $folder) {
        if (substr($script_path, -strlen($folder)) === $folder) {
            $script_path = substr($script_path, 0, -strlen($folder));
            break;
        }
    }
    
    $project_root = rtrim($script_path, '/');
    define('BASE_URL', $protocol . $host . $project_root);
}

// Database Credentials
$db_host = "localhost";
$db_name = "online_job_portal_db";
$db_user = "root";
$db_pass = "";
$db_port = "3306";

// Global database connection variables
$conn = null;
$pdo = null;
$is_db_connected = false;

// Attempt MySQLi Connection (team standard)
try {
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if ($conn) {
        mysqli_set_charset($conn, "utf8mb4");
        $is_db_connected = true;
    }
} catch (Exception $e) {
    $conn = null;
}

// PDO Singleton Connection
function get_db_connection() {
    global $pdo, $db_host, $db_name, $db_user, $db_pass;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        $dsn = "mysql:host=127.0.0.1;dbname=" . $db_name . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 2,
        ];
        $pdo = new PDO($dsn, $db_user, $db_pass, $options);
        return $pdo;
    } catch (PDOException $e) {
        return null;
    }
}

// Initialize PDO if available
$pdo = get_db_connection();