<?php
/**
 * JobPortal.lk - Database Configuration & Session Init
 * Member 1 - Admin & Core/Shared
 */

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Global Application Constants
define('APP_NAME', 'JobPortal.lk');
define('APP_VERSION', '1.0.0');
define('ADMIN_EMAIL', 'admin@jobportal.lk');

// Determine Base URL dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
// Normalize base path to root of project
$base_dir = rtrim(dirname($script_name), '/\\');
// If nested in admin or auth, traverse up
if (basename($base_dir) === 'admin' || basename($base_dir) === 'auth' || basename($base_dir) === 'company' || basename($base_dir) === 'seeker') {
    $base_dir = dirname($base_dir);
}
$base_dir = str_replace('\\', '/', $base_dir);
define('BASE_URL', rtrim($protocol . $host . $base_dir, '/'));

// Database Credentials
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'online_job_portal_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * PDO Database Connection Singleton
 */
function get_db_connection() {
    static $pdo = null;
    static $connection_failed = false;

    if ($pdo !== null) {
        return $pdo;
    }

    if ($connection_failed) {
        return null;
    }

    $hosts = ['127.0.0.1', 'localhost'];
    $db_names = [DB_NAME, 'jobportal'];

    foreach ($hosts as $host) {
        foreach ($db_names as $dbname) {
            try {
                $dsn = "mysql:host=" . $host . ";port=" . DB_PORT . ";dbname=" . $dbname . ";charset=" . DB_CHARSET;
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_TIMEOUT            => 2,
                ];
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                return $pdo;
            } catch (PDOException $e) {
                // Try next host / db combination
            }
        }
    }

    // If all failed, log and set flag
    error_log("Database connection failed for all candidates.");
    $connection_failed = true;
    return null;
}

// Global flag to check if database is online
$db = get_db_connection();
$is_db_connected = ($db !== null);
