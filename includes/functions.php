<?php
/**
 * JobPortal.lk - Global Helper Functions
 * Integrates Seeker, Company, and Admin modules with fallback support.
 */

require_once __DIR__ . '/../config/database.php';

// Flash Message Utility
function set_flash($message, $type = 'success') {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function get_flash() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function display_flash() {
    $flash = get_flash();
    if ($flash) {
        $type = htmlspecialchars($flash['type']);
        $msg = htmlspecialchars($flash['message']);
        $icon = ($type === 'success') ? '✓' : (($type === 'danger' || $type === 'error') ? '✕' : 'ℹ');
        echo "<div class='alert alert-{$type}' id='flashAlert'>
                <span class='alert-icon'>{$icon}</span>
                <span class='alert-text'>{$msg}</span>
                <button type='button' class='alert-close' onclick=\"this.parentElement.remove();\">&times;</button>
              </div>";
    }
}

// XSS clean helper
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Time Ago Formatter
function time_ago($datetime) {
    $time = is_numeric($datetime) ? $datetime : strtotime($datetime);
    if (!$time) return 'Just now';
    $diff = time() - $time;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' mins ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hrs ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('M d, Y', $time);
}

// Activity Logging
function add_activity($action, $type = 'general') {
    global $conn;
    $stmt = mysqli_prepare($conn, "INSERT INTO activity_logs (action, type, created_at) VALUES (?, ?, NOW())");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $action, $type);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function get_recent_activities($limit = 6) {
    global $conn;
    $activities = [];

    // Audit logs / activity_log table එකෙන් නවතම Records ගන්නා SQL Query එක
    $query = "SELECT action, created_at FROM activity_logs ORDER BY id DESC LIMIT " . (int)$limit;
    $res = mysqli_query($conn, $query);

    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $activities[] = $row;
        }
    } else {
        // Table එකේ records නැත්නම් හෝ Table එකක් නැත්නම් Default Placeholder Data
        $activities = [
            ['action' => 'New employer account pending verification', 'created_at' => date('Y-m-d H:i:s', strtotime('-10 mins'))],
            ['action' => 'Job posting "Mobile App Developer" submitted for approval', 'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))],
            ['action' => 'System metrics database backup completed successfully', 'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))]
        ];
    }

    return $activities;
}

// Admin KPI Metrics
function get_admin_metrics() {
    global $conn;

    $metrics = [
        'total_users'          => 0,
        'total_job_seekers'    => 0,
        'total_companies'      => 0,
        'total_jobs'           => 0,
        'pending_jobs'         => 0,
        'flagged_reviews'      => 0,
        'active_subscriptions' => 0
    ];

    try {
        // 1. Total Users
        $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users");
        if ($res) { $metrics['total_users'] = mysqli_fetch_assoc($res)['cnt'] ?? 0; }

        // 2. Job Seekers (Table Name: job_seekers)
        $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM job_seekers");
        if ($res) { $metrics['total_job_seekers'] = mysqli_fetch_assoc($res)['cnt'] ?? 0; }

        // 3. Companies (Table Name: company)
        $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM company");
        if ($res) { $metrics['total_companies'] = mysqli_fetch_assoc($res)['cnt'] ?? 0; }

        // 4. Active Jobs
        $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM jobs WHERE status = 'active' OR status = 'published' OR status = 'Approved'");
        if ($res) { $metrics['total_jobs'] = mysqli_fetch_assoc($res)['cnt'] ?? 0; }

        // 5. Pending Jobs
        $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM jobs WHERE status = 'pending' OR status = 'Pending Approval'");
        if ($res) { $metrics['pending_jobs'] = mysqli_fetch_assoc($res)['cnt'] ?? 0; }

        // 6. Flagged Reviews
        $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM reviews WHERE status = 'flagged' OR is_flagged = 1");
        if ($res) { $metrics['flagged_reviews'] = mysqli_fetch_assoc($res)['cnt'] ?? 0; }

        // 7. Active Subscriptions
        $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM subscriptions WHERE status = 'active'");
        if ($res) { $metrics['active_subscriptions'] = mysqli_fetch_assoc($res)['cnt'] ?? 0; }

    } catch (Exception $e) {
        error_log("Metrics Fetch Error: " . $e->getMessage());
    }

    return $metrics;
}
// -------------------------------------------------------------
// USER MANAGEMENT
// -------------------------------------------------------------
function get_all_users($role_filter = '', $search = '') {
    global $conn;
    $users = [];

    // Base Query with LEFT JOINs
    $sql = "SELECT u.*, 
            c.company_name,
            CASE 
                WHEN j.seeker_id IS NOT NULL THEN 'seeker'
                WHEN c.company_id IS NOT NULL THEN 'company'
                ELSE COALESCE(u.role, 'admin')
            END AS calculated_role
            FROM users u
            LEFT JOIN job_seekers j ON u.user_id = j.user_id
            LEFT JOIN company c ON u.user_id = c.user_id
            WHERE 1=1";

    $params = [];
    $types = "";

    // 🔍 Dynamic & Safe Search SQL Condition
    if (!empty($search)) {
        $sql .= " AND (u.email LIKE ? OR u.phone LIKE ? OR c.company_name LIKE ?)";
        $searchTerm = "%" . $search . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "sss";
    }

    $sql .= " ORDER BY u.user_id DESC";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            // Standardize Keys
            $row['id'] = $row['user_id'] ?? $row['id'] ?? 0;
            $row['phone'] = $row['phone'] ?? 'N/A';
            $row['status'] = !empty($row['status']) ? ucfirst(strtolower($row['status'])) : 'Active';
            $row['created_at'] = $row['created_at'] ?? date('Y-m-d');
            $row['role'] = $row['calculated_role'];

            // Name Resolution Fallback Logic
            $user_name = $row['name'] ?? $row['username'] ?? $row['full_name'] ?? '';

            if (empty($user_name) && !empty($row['company_name'])) {
                $user_name = $row['company_name'];
            }

            if (empty($user_name) && !empty($row['email'])) {
                $email_parts = explode('@', $row['email']);
                $user_name = ucfirst($email_parts[0]);
            }

            $row['name'] = !empty($user_name) ? $user_name : 'User #' . $row['id'];

            // Role Filter application
            if (!empty($role_filter) && $row['role'] !== $role_filter) {
                continue;
            }

            $users[] = $row;
        }
        mysqli_stmt_close($stmt);
    }

    return $users;
}

/**
 * Update User Status in real DB ('Active' or 'Suspended') across all linked tables
 */
function toggle_user_status($user_id, $new_status) {
    global $conn;
    $uid = (int)$user_id;

    if ($uid <= 0) return false;

    if ($new_status === 'Suspended') {
        // Suspend Company
        mysqli_query($conn, "UPDATE company SET status = 'suspended' WHERE user_id = $uid");
        // Suspend Job Seeker
        mysqli_query($conn, "UPDATE job_seekers SET status = 'suspended' WHERE user_id = $uid");
    } else {
        // Activate Company
        mysqli_query($conn, "UPDATE company SET status = 'approved' WHERE user_id = $uid");
        // Activate Job Seeker
        mysqli_query($conn, "UPDATE job_seekers SET status = 'not_hired' WHERE user_id = $uid");
    }

    return true;
}

function delete_user($user_id) {
    global $conn;
    $uid = (int)$user_id;

    if ($uid <= 0) return false;

    // Delete dependent profile rows first
    mysqli_query($conn, "DELETE FROM job_seekers WHERE user_id = $uid");
    mysqli_query($conn, "DELETE FROM company WHERE user_id = $uid");

    // Delete main user row
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE user_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $uid);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }
    return false;
}

// -------------------------------------------------------------
// JOB SEEKER MANAGEMENT
// -------------------------------------------------------------
function get_all_job_seekers($search = '', $status_filter = '') {
    global $conn;
    if ($conn) {
        $sql = "SELECT s.seeker_id AS id, u.id AS user_id, u.name, u.email, u.phone, u.status, s.birth_day, s.bio, s.experience_years, s.location
                FROM users u
                LEFT JOIN job_seekers s ON u.id = s.user_id
                WHERE u.role = 'seeker'";
        if (!empty($status_filter)) {
            $sql .= " AND u.status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
        }
        if (!empty($search)) {
            $s = mysqli_real_escape_string($conn, $search);
            $sql .= " AND (u.name LIKE '%$s%' OR u.email LIKE '%$s%' OR s.location LIKE '%$s%')";
        }
        $sql .= " ORDER BY u.id DESC";
        $res = @mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) > 0) {
            $seekers = [];
            while ($row = mysqli_fetch_assoc($res)) $seekers[] = $row;
            return $seekers;
        }
    }

    return [
        ['id' => 1, 'user_id' => 2, 'name' => 'Dilshan Silva', 'email' => 'dilshan.silva@gmail.com', 'phone' => '+94 71 987 6543', 'status' => 'Active', 'location' => 'Colombo, Sri Lanka', 'experience_years' => 4, 'bio' => 'Full-stack developer proficient in React, PHP, and Node.js.', 'cv_count' => 2],
        ['id' => 2, 'user_id' => 4, 'name' => 'Nadeesha Fernando', 'email' => 'nadeesha.f@hotmail.com', 'phone' => '+94 76 555 4321', 'status' => 'Active', 'location' => 'Kandy, Sri Lanka', 'experience_years' => 3, 'bio' => 'UI/UX Designer with a passion for clean aesthetics and mobile design.', 'cv_count' => 1],
        ['id' => 3, 'user_id' => 6, 'name' => 'Kasun Jayawardena', 'email' => 'kasun.j@yahoo.com', 'phone' => '+94 70 111 2233', 'status' => 'Suspended', 'location' => 'Galle, Sri Lanka', 'experience_years' => 1, 'bio' => 'Junior QA Engineer with manual testing experience.', 'cv_count' => 1],
        ['id' => 4, 'user_id' => 8, 'name' => 'Anura Gunasekara', 'email' => 'anura.g@gmail.com', 'phone' => '+94 72 345 6789', 'status' => 'Pending', 'location' => 'Kurunegala, Sri Lanka', 'experience_years' => 6, 'bio' => 'DevOps Specialist experienced in AWS, Docker, and CI/CD pipelines.', 'cv_count' => 3]
    ];
}

// -------------------------------------------------------------
// COMPANY MANAGEMENT
// -------------------------------------------------------------
function get_all_companies($status_filter = '', $search = '') {
    global $conn;
    if ($conn) {
        $sql = "SELECT id, company_name, industry_type, location, owner_email, phone, status, created_at FROM companies WHERE 1=1";
        if (!empty($status_filter)) {
            $sql .= " AND status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
        }
        if (!empty($search)) {
            $s = mysqli_real_escape_string($conn, $search);
            $sql .= " AND (company_name LIKE '%$s%' OR industry_type LIKE '%$s%' OR location LIKE '%$s%')";
        }
        $sql .= " ORDER BY id DESC";
        $res = @mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) > 0) {
            $companies = [];
            while ($row = mysqli_fetch_assoc($res)) $companies[] = $row;
            return $companies;
        }
    }

    $mock_companies = [
        ['id' => 1, 'company_name' => 'Virtusa (Pvt) Ltd', 'industry_type' => 'Information Technology', 'location' => 'Colombo 07', 'owner_email' => 'careers@virtusa.com', 'phone' => '+94 11 234 5678', 'status' => 'Approved', 'created_at' => '2026-02-18 14:45:00'],
        ['id' => 2, 'company_name' => 'Dialog Axiata PLC', 'industry_type' => 'Telecommunications', 'location' => 'Colombo 02', 'owner_email' => 'jobs@dialog.lk', 'phone' => '+94 77 733 3333', 'status' => 'Approved', 'created_at' => '2026-03-01 08:50:00'],
        ['id' => 3, 'company_name' => 'WSO2 Lanka', 'industry_type' => 'Enterprise Software', 'location' => 'Colombo 03', 'owner_email' => 'hr@wso2.com', 'phone' => '+94 11 214 5345', 'status' => 'Approved', 'created_at' => '2026-03-12 13:00:00'],
        ['id' => 4, 'company_name' => 'Apex Digital Media', 'industry_type' => 'Marketing & Advertising', 'location' => 'Nugegoda', 'owner_email' => 'contact@apexdigital.lk', 'phone' => '+94 11 280 9988', 'status' => 'Pending Approval', 'created_at' => '2026-03-28 10:15:00'],
        ['id' => 5, 'company_name' => 'Lanka Bio Health', 'industry_type' => 'Healthcare & Pharma', 'location' => 'Rajagiriya', 'owner_email' => 'info@lankabio.com', 'phone' => '+94 11 445 6789', 'status' => 'Pending Approval', 'created_at' => '2026-03-29 11:30:00'],
        ['id' => 6, 'company_name' => 'FastTrack Logistics', 'industry_type' => 'Supply Chain', 'location' => 'Peliyagoda', 'owner_email' => 'support@fasttrack.lk', 'phone' => '+94 11 556 7890', 'status' => 'Suspended', 'created_at' => '2026-02-05 16:20:00']
    ];

    if (!empty($status_filter)) {
        $mock_companies = array_values(array_filter($mock_companies, fn($c) => $c['status'] === $status_filter));
    }
    if (!empty($search)) {
        $s = strtolower($search);
        $mock_companies = array_values(array_filter($mock_companies, fn($c) => str_contains(strtolower($c['company_name']), $s) || str_contains(strtolower($c['location']), $s)));
    }
    return $mock_companies;
}

function update_company_status($id, $status) {
    global $conn;
    if ($conn) {
        $stmt = mysqli_prepare($conn, "UPDATE companies SET status = ? WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $status, $id);
            return mysqli_stmt_execute($stmt);
        }
    }
    return true;
}

function delete_company($id) {
    global $conn;
    if ($conn) {
        $stmt = mysqli_prepare($conn, "DELETE FROM companies WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            return mysqli_stmt_execute($stmt);
        }
    }
    return true;
}

// -------------------------------------------------------------
// JOB POSTING MODERATION
// -------------------------------------------------------------
function get_all_jobs_admin($status = '', $category_id = '', $search = '') {
    global $conn;
    $jobs = [];

    $sql = "SELECT j.*, 
            c.company_name,
            cat.category_name
            FROM jobs j
            LEFT JOIN company c ON j.company_id = c.company_id
            LEFT JOIN categories cat ON j.category_id = cat.category_id
            WHERE 1=1";

    if (!empty($status)) {
        $status_clean = mysqli_real_escape_string($conn, strtolower($status));
        $sql .= " AND j.status = '$status_clean'";
    }

    if (!empty($category_id)) {
        $cat_id = (int)$category_id;
        $sql .= " AND j.category_id = $cat_id";
    }

    if (!empty($search)) {
        $s = mysqli_real_escape_string($conn, $search);
        $sql .= " AND (j.title LIKE '%$s%' OR j.location LIKE '%$s%' OR c.company_name LIKE '%$s%')";
    }

    $sql .= " ORDER BY j.job_id DESC";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $jobs[] = $row;
        }
    }

    return $jobs;
}


function update_job_status($job_id, $new_status) {
    global $conn;
    $jid = (int)$job_id;
    $status_clean = mysqli_real_escape_string($conn, strtolower($new_status));

    if ($jid <= 0) return false;

    $query = "UPDATE jobs SET status = '$status_clean' WHERE job_id = $jid";
    return mysqli_query($conn, $query);
}

function delete_job_admin($job_id) {
    global $conn;
    $jid = (int)$job_id;

    if ($jid <= 0) return false;

    // Remove dependent application records if table exists
    mysqli_query($conn, "DELETE FROM job_applications WHERE job_id = $jid");

    $query = "DELETE FROM jobs WHERE job_id = $jid";
    return mysqli_query($conn, $query);
}

// -------------------------------------------------------------
// CATEGORIES MANAGEMENT
// -------------------------------------------------------------
function get_all_categories_admin() {
    global $conn;
    $categories = [];

    $sql = "SELECT c.category_id, c.category_name, 
            COUNT(j.job_id) AS job_count
            FROM categories c
            LEFT JOIN jobs j ON c.category_id = j.category_id
            GROUP BY c.category_id, c.category_name
            ORDER BY c.category_name ASC";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }

    return $categories;
}


function save_category($name, $id = null) {
    global $conn;
    $name_clean = mysqli_real_escape_string($conn, $name);

    if ($id) {
        $cat_id = (int)$id;
        $sql = "UPDATE categories SET category_name = '$name_clean' WHERE category_id = $cat_id";
    } else {
        $sql = "INSERT INTO categories (category_name) VALUES ('$name_clean')";
    }

    return mysqli_query($conn, $sql);
}

function delete_category($id) {
    global $conn;
    $cat_id = (int)$id;

    if ($cat_id <= 0) return false;

    // Optional: Reset jobs in this category
    mysqli_query($conn, "UPDATE jobs SET category_id = NULL WHERE category_id = $cat_id");

    $sql = "DELETE FROM categories WHERE category_id = $cat_id";
    return mysqli_query($conn, $sql);
}

// -------------------------------------------------------------
// REVIEWS MODERATION
// -------------------------------------------------------------
function get_all_reviews_admin($status_filter = '', $search = '') {
    global $conn;
    if ($conn) {
        $sql = "SELECT id, job_title, seeker_name, rating, comment, status, created_at FROM reviews WHERE 1=1";
        if (!empty($status_filter)) {
            $sql .= " AND status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
        }
        if (!empty($search)) {
            $s = mysqli_real_escape_string($conn, $search);
            $sql .= " AND (job_title LIKE '%$s%' OR seeker_name LIKE '%$s%' OR comment LIKE '%$s%')";
        }
        $sql .= " ORDER BY id DESC";
        $res = @mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) > 0) {
            $reviews = [];
            while ($row = mysqli_fetch_assoc($res)) $reviews[] = $row;
            return $reviews;
        }
    }

    $mock_reviews = [
        ['id' => 1, 'job_title' => 'Senior Full Stack Engineer (Virtusa)', 'seeker_name' => 'Dilshan Silva', 'rating' => 5, 'comment' => 'Excellent interview process. Professional panel with relevant technical scenarios.', 'status' => 'Approved', 'created_at' => '2026-03-24 11:30:00'],
        ['id' => 2, 'job_title' => 'UI/UX Product Designer (WSO2)', 'seeker_name' => 'Nadeesha Fernando', 'rating' => 4, 'comment' => 'Great company culture and constructive feedback after design assignment.', 'status' => 'Approved', 'created_at' => '2026-03-26 15:40:00'],
        ['id' => 3, 'job_title' => 'Digital Marketing Lead (Apex Digital)', 'seeker_name' => 'Kasun Jayawardena', 'rating' => 1, 'comment' => 'THIS COMPANY IS COMPLETE FRAUD AND SCAM DO NOT APPLY!!!', 'status' => 'Flagged', 'created_at' => '2026-03-28 09:10:00'],
        ['id' => 4, 'job_title' => 'DevOps Architect (Dialog Axiata)', 'seeker_name' => 'Anura Gunasekara', 'rating' => 5, 'comment' => 'Very smooth hiring pipeline and competitive compensation offered.', 'status' => 'Approved', 'created_at' => '2026-03-29 14:00:00'],
        ['id' => 5, 'job_title' => 'Junior QA Automation Tester (Virtusa)', 'seeker_name' => 'Anonymous User', 'rating' => 1, 'comment' => 'Abusive recruiter and offensive language used during call.', 'status' => 'Flagged', 'created_at' => '2026-03-30 18:20:00']
    ];

    if (!empty($status_filter)) {
        $mock_reviews = array_values(array_filter($mock_reviews, fn($r) => $r['status'] === $status_filter));
    }
    if (!empty($search)) {
        $s = strtolower($search);
        $mock_reviews = array_values(array_filter($mock_reviews, fn($r) => str_contains(strtolower($r['job_title']), $s) || str_contains(strtolower($r['comment']), $s)));
    }
    return $mock_reviews;
}

function update_review_status($id, $status) {
    global $conn;
    if ($conn) {
        $stmt = mysqli_prepare($conn, "UPDATE reviews SET status = ? WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $status, $id);
            return mysqli_stmt_execute($stmt);
        }
    }
    return true;
}

function delete_review_admin($id) {
    global $conn;
    if ($conn) {
        $stmt = mysqli_prepare($conn, "DELETE FROM reviews WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            return mysqli_stmt_execute($stmt);
        }
    }
    return true;
}

// -------------------------------------------------------------
// SUBSCRIPTIONS MANAGEMENT
// -------------------------------------------------------------
function get_all_subscription_plans_admin() {
    global $conn;
    $plans = [];

    $result = mysqli_query($conn, "SELECT * FROM subscription_plans ORDER BY plan_id ASC");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $plans[] = $row;
        }
    }

    return $plans;
}

function get_user_subscriptions_admin() {
    global $conn;
    $subscriptions = [];

    $sql = "SELECT s.sub_id, s.user_id, s.plan_id, s.start_date, s.end_date, s.is_active,
            p.plan_name, p.price,
            u.email, u.first_name, u.last_name,
            c.company_name
            FROM user_subscriptions s
            LEFT JOIN subscription_plans p ON s.plan_id = p.plan_id
            LEFT JOIN users u ON s.user_id = u.user_id
            LEFT JOIN company c ON u.user_id = c.user_id
            ORDER BY s.sub_id DESC";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Determine display name
            $subscriber_name = $row['company_name'] ?: trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
            if (empty($subscriber_name) && !empty($row['email'])) {
                $subscriber_name = ucfirst(explode('@', $row['email'])[0]);
            }

            $row['subscriber_name'] = !empty($subscriber_name) ? $subscriber_name : 'User #' . $row['user_id'];
            $subscriptions[] = $row;
        }
    }

    return $subscriptions;
}

function toggle_user_subscription_status($sub_id, $is_active) {
    global $conn;
    $sid = (int)$sub_id;
    $active_val = (int)$is_active;

    if ($sid <= 0) return false;

    $query = "UPDATE user_subscriptions SET is_active = $active_val WHERE sub_id = $sid";
    return mysqli_query($conn, $query);
}

function save_subscription_plan($plan_name, $price, $duration_days, $plan_id = null) {
    global $conn;
    $pname = mysqli_real_escape_string($conn, $plan_name);
    $price_val = (float)$price;
    $days = (int)$duration_days;

    if ($plan_id) {
        $pid = (int)$plan_id;
        $sql = "UPDATE subscription_plans SET plan_name = '$pname', price = $price_val, duration_days = $days WHERE plan_id = $pid";
    } else {
        $sql = "INSERT INTO subscription_plans (plan_name, duration_days, price) VALUES ('$pname', $days, $price_val)";
    }

    return mysqli_query($conn, $sql);
}

// -------------------------------------------------------------
// SYSTEM SETTINGS
// -------------------------------------------------------------
function get_system_settings() {
    global $conn;

    $defaults = [
        'site_name'  => 'JobPortal.lk',
        'site_email' => 'admin@jobportal.lk'
    ];

    // Check session or database override
    if (isset($_SESSION['system_settings'])) {
        return array_merge($defaults, $_SESSION['system_settings']);
    }

    // Try reading from DB if settings table exists
    $result = @mysqli_query($conn, "SELECT setting_key, setting_value FROM system_settings");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (isset($defaults[$row['setting_key']])) {
                $defaults[$row['setting_key']] = $row['setting_value'];
            }
        }
    }

    return $defaults;
}

function save_system_settings($data) {
    global $conn;

    // Always update session state for immediate app reactivity
    $_SESSION['system_settings'] = $data;

    // Attempt DB sync if settings table exists
    foreach ($data as $key => $val) {
        $key_clean = mysqli_real_escape_string($conn, $key);
        $val_clean = mysqli_real_escape_string($conn, $val);

        @mysqli_query($conn, "INSERT INTO system_settings (setting_key, setting_value) 
                              VALUES ('$key_clean', '$val_clean') 
                              ON DUPLICATE KEY UPDATE setting_value = '$val_clean'");
    }

    return true;
}

// -------------------------------------------------------------
// SEEKER & COMPANY PORTAL FUNCTIONS
// -------------------------------------------------------------
function get_cv_by_id($conn, $cv_id, $seeker_id) {
    $sql = "SELECT file_path FROM cvs WHERE cv_id = ? AND seeker_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $cv_id, $seeker_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function delete_cv($conn, $cv_id, $seeker_id) {
    $sql = "DELETE FROM cvs WHERE cv_id = ? AND seeker_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $cv_id, $seeker_id);
    return mysqli_stmt_execute($stmt);
}

function get_all_plans($conn) {
    $sql = "SELECT * FROM subscription_plans ORDER BY price ASC";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function subscribe_to_plan($conn, $user_id, $plan_id) {
    $sql = "SELECT duration_days FROM subscription_plans WHERE plan_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $plan_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $plan = mysqli_fetch_assoc($result);
    if (!$plan) return false;

    $deactivate = mysqli_prepare($conn, "UPDATE user_subscriptions SET is_active = 0 WHERE user_id = ?");
    mysqli_stmt_bind_param($deactivate, "i", $user_id);
    mysqli_stmt_execute($deactivate);

    $start = date('Y-m-d');
    $end = date('Y-m-d', strtotime("+{$plan['duration_days']} days"));

    $insert = mysqli_prepare($conn, "INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, is_active) VALUES (?, ?, ?, ?, 1)");
    mysqli_stmt_bind_param($insert, "iiss", $user_id, $plan_id, $start, $end);
    return mysqli_stmt_execute($insert);
}

function get_applications($conn, $seeker_id, $status = '') {
    if (!$conn || empty($seeker_id)) return [];

    $seeker_id = (int)$seeker_id;
    $sql = "SELECT a.app_id, a.apply_date, a.status, a.cv_id, a.experience,
                   j.job_id, j.title, c.company_name, cv.file_path
            FROM applications a
            JOIN jobs j ON a.job_id = j.job_id
            LEFT JOIN company c ON j.company_id = c.company_id
            LEFT JOIN cvs cv ON a.cv_id = cv.cv_id
            WHERE a.seeker_id = ?";

    $params = [$seeker_id];
    $types = "i";

    if (!empty($status) && $status !== 'all') {
        $sql .= " AND a.status = ?";
        $params[] = $status;
        $types .= "s";
    }

    $sql .= " ORDER BY a.app_id DESC";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $rows = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    return [];
}

/**
 * Get Application details along with Job, Company & CV path for my-cv.php
 */
function get_application_cv_details($conn, $app_id, $seeker_id) {
    if (!$conn || empty($app_id) || empty($seeker_id)) return null;

    $app_id    = (int)$app_id;
    $seeker_id = (int)$seeker_id;

    $sql = "SELECT a.app_id, a.apply_date, a.status, a.experience,
                   j.title AS job_title, 
                   c.company_name, 
                   cv.cv_id, cv.file_path, cv.uploaded_at
            FROM applications a
            JOIN jobs j ON a.job_id = j.job_id
            LEFT JOIN company c ON j.company_id = c.company_id
            LEFT JOIN cvs cv ON a.cv_id = cv.cv_id
            WHERE a.app_id = ? AND a.seeker_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $app_id, $seeker_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res && $row = mysqli_fetch_assoc($res)) {
            return $row;
        }
    }
    return null;
}

function withdraw_application($conn, $app_id, $seeker_id) {
    $sql = "DELETE FROM applications WHERE app_id = ? AND seeker_id = ? AND status = 'pending'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $app_id, $seeker_id);
    return mysqli_stmt_execute($stmt);
}

function get_interviews_count($conn, $seeker_id) {
    $sql = "SELECT COUNT(*) AS total FROM interviews i JOIN applications a ON i.app_id = a.app_id WHERE a.seeker_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $seeker_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result)['total'];
}

function get_interviews($conn, $seeker_id, $limit, $offset) {
    $sql = "SELECT i.interview_id, i.interview_date, i.start_time, i.meeting_link, i.notes, i.status,
                   j.title, c.company_name, iv.interviewer_name
            FROM interviews i
            JOIN applications a ON i.app_id = a.app_id
            JOIN jobs j ON a.job_id = j.job_id
            JOIN company c ON j.company_id = c.company_id
            JOIN interviewer iv ON i.interviewer_id = iv.interviewer_id
            WHERE a.seeker_id = ?
            ORDER BY i.interview_date DESC, i.start_time DESC
            LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $seeker_id, $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function get_user($conn, $user_id) {
    $sql = "SELECT first_name, middle_name, last_name, email FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function get_seeker_profile($conn, $seeker_id) {
    $sql = "SELECT birth_day, phone, bio FROM job_seekers WHERE seeker_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $seeker_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function update_user_name($conn, $user_id, $first, $middle, $last) {
    $sql = "UPDATE users SET first_name=?, middle_name=?, last_name=? WHERE user_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $first, $middle, $last, $user_id);
    return mysqli_stmt_execute($stmt);
}

function update_seeker_profile($conn, $seeker_id, $birth_day, $phone, $bio) {
    $sql = "UPDATE job_seekers SET birth_day=?, phone=?, bio=? WHERE seeker_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $birth_day, $phone, $bio, $seeker_id);
    return mysqli_stmt_execute($stmt);
}

function get_job_alerts($conn, $seeker_id) {
    $sql = "SELECT * FROM job_alerts WHERE seeker_id = ? ORDER BY alert_id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $seeker_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function add_job_alert($conn, $seeker_id, $keyword, $location) {
    $sql = "INSERT INTO job_alerts (seeker_id, suggest_job, location_pref, selects_or_not) VALUES (?, ?, ?, 1)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $seeker_id, $keyword, $location);
    return mysqli_stmt_execute($stmt);
}

function toggle_job_alert($conn, $alert_id, $seeker_id) {
    $sql = "UPDATE job_alerts SET selects_or_not = NOT selects_or_not WHERE alert_id = ? AND seeker_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $alert_id, $seeker_id);
    return mysqli_stmt_execute($stmt);
}

function delete_job_alert($conn, $alert_id, $seeker_id) {
    $sql = "DELETE FROM job_alerts WHERE alert_id = ? AND seeker_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $alert_id, $seeker_id);
    return mysqli_stmt_execute($stmt);
}

function get_user_password_hash($conn, $user_id) {
    $sql = "SELECT password FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row ? $row['password'] : null;
}

function update_user_password($conn, $user_id, $hashed_password) {
    $sql = "UPDATE users SET password = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
    return mysqli_stmt_execute($stmt);
}

function delete_user_account($conn, $user_id) {
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    return mysqli_stmt_execute($stmt);
}

function redirect($url) {
    header("Location: " . $url);
    exit;
}

// SEEKER FUNCTIONS
// 1. Get Seeker ID from User ID
function get_seeker_id($conn, $user_id) {
    if ($conn) {
        $stmt = mysqli_prepare($conn, "SELECT seeker_id FROM job_seekers WHERE user_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                return $row['seeker_id'];
            }
        }
    }
    return 1;
}

// Get Seeker CVs list for my-cv.php
function get_seeker_cvs($conn, $seeker_id) {
    if (!$conn || empty($seeker_id)) return [];

    $seeker_id = (int)$seeker_id;
    $sql = "SELECT * FROM cvs WHERE seeker_id = ? ORDER BY cv_id DESC";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $seeker_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $rows = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
    return [];
}

// Insert uploaded CV path into the database
function insert_cv($conn, $seeker_id, $file_path) {
    if ($conn) {
        $sql = "INSERT INTO cvs (seeker_id, file_path, uploaded_at) VALUES (?, ?, NOW())";
        $stmt = @mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "is", $seeker_id, $file_path);
            if (mysqli_stmt_execute($stmt)) {
                // Return newly inserted cv_id
                return mysqli_insert_id($conn);
            }
        }
    }
    return false;
}

/**
 * Insert job application details into 'applications' table
 */
/**
 * Insert job application details into 'applications' table
 */
function insert_application($conn, $seeker_id, $job_id, $cv_id, $experience = '') {
    if (!$conn || empty($seeker_id) || empty($job_id) || empty($cv_id)) {
        return false;
    }

    $seeker_id  = (int)$seeker_id;
    $job_id     = (int)$job_id;
    $cv_id      = (int)$cv_id;
    $experience = mysqli_real_escape_string($conn, trim($experience));

    // real column names: seeker_id, job_id, cv_id, apply_date, status, experience
    $sql = "INSERT INTO applications (seeker_id, job_id, cv_id, apply_date, status, experience) 
            VALUES ($seeker_id, $job_id, $cv_id, CURDATE(), 'pending', '$experience')";

    return mysqli_query($conn, $sql);
}

// 2. Clean / XSS Helper (if clean() is missing)
if (!function_exists('clean')) {
    function clean($data) {
        return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// 3. Format Date
if (!function_exists('formatDate')) {
    function formatDate($date) {
        return $date ? date('M d, Y', strtotime($date)) : 'N/A';
    }
}

// 4. Status Badge CSS Class
if (!function_exists('status_badge_class')) {
    function status_badge_class($status) {
        $status = strtolower($status);
        if ($status === 'approved' || $status === 'active' || $status === 'accepted') return 'badge-success';
        if ($status === 'pending' || $status === 'pending approval') return 'badge-warning';
        return 'badge-danger';
    }
}

// 5. Total Applications Count
function get_total_applications($conn, $seeker_id) {
    if ($conn) {
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS cnt FROM applications WHERE seeker_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $seeker_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) return (int)$row['cnt'];
    }
    return 0;
}

// 6. Pending Interviews Count
function get_pending_interviews($conn, $seeker_id) {
    if ($conn) {
        $sql = "SELECT COUNT(*) AS cnt FROM interviews i JOIN applications a ON i.app_id = a.app_id WHERE a.seeker_id = ? AND i.status = 'Pending'";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $seeker_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) return (int)$row['cnt'];
    }
    return 0;
}

// 7. Subscription Status
if (!function_exists('get_subscription_status')) {
    function get_subscription_status($conn, $user_id) {
        
        $sql = "SELECT us.*, p.plan_name, p.duration_days 
                FROM user_subscriptions us 
                JOIN subscription_plans p ON us.plan_id = p.plan_id 
                WHERE us.user_id = ? AND us.status = 'Active' 
                LIMIT 1";
                
        $stmt = @mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                $start_date = $row['start_date']; 
                
                
                $duration_days = isset($row['duration_days']) ? (int)$row['duration_days'] : 30;
                $end_date = date('Y-m-d', strtotime($start_date . " + $duration_days days"));

                return [
                    'status' => $row['status'],
                    'plan_name' => $row['plan_name'],
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ];
            }
        }
        
        
        $default_start = date('Y-m-d'); 
        $default_end = date('Y-m-d', strtotime('+30 days')); 
        
        return [
            'status' => 'Active',
            'plan_name' => 'Standard Seeker Plan',
            'start_date' => $default_start,
            'end_date' => $default_end
        ];
    }
}

// 8. Profile Completion Percentage
function get_profile_completion($conn, $seeker_id) {
    return 80; // Default completion percentage
}

// 9. Recent Applications List
function get_recent_applications($conn, $seeker_id, $limit = 5) {
    if ($conn) {
        $sql = "SELECT j.title, c.company_name, a.apply_date, a.status 
                FROM applications a 
                LEFT JOIN jobs j ON a.job_id = j.id 
                LEFT JOIN companies c ON j.company_id = c.id 
                WHERE a.seeker_id = ? ORDER BY a.app_id DESC LIMIT ?";
        
        
        $stmt = @mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $seeker_id, $limit);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $rows = [];
            if ($res) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $rows[] = $row;
                }
            }
            return $rows;
        }
    }
    return [];
}

// 10. Get Total Jobs Count (For browse-jobs.php pagination/filtering)
// 1o.1. Get Jobs Count for browse-jobs.php pagination
function get_jobs_count($conn, $search = '', $category = '') {
    if ($conn) {
        // $search එක array එකක් විදිහට ($filters) ආවොත් ඒකෙන් values ලබාගැනීම
        $filters = is_array($search) ? $search : [];
        $keyword  = is_array($search) ? ($filters['keyword'] ?? '') : $search;
        $category = is_array($search) ? ($filters['category'] ?? '') : $category;
        $location = $filters['location'] ?? '';
        $job_type = $filters['job_type'] ?? '';
        $s_min    = $filters['salary_min'] ?? '';
        $s_max    = $filters['salary_max'] ?? '';

        $sql = "SELECT COUNT(*) as total 
                FROM jobs j 
                LEFT JOIN company c ON j.company_id = c.company_id 
                WHERE 1=1";

        $params = [];
        $types = "";

        // 1. Keyword Search (Title, Description, Company Name)
        if (!empty($keyword)) {
            if (is_array($keyword)) {
                $keyword = trim(implode(' ', $keyword));
            } else {
                $keyword = trim($keyword);
            }

            if ($keyword !== '') {
                $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR c.company_name LIKE ?)";
                $searchTerm = "%{$keyword}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "sss";
            }
        }

        // 2. Category Filter
        if (!empty($category)) {
            $sql .= " AND j.category_id = ?";
            $params[] = (int)$category;
            $types .= "i";
        }

        // 3. Location Filter
        if (!empty($location)) {
            $sql .= " AND j.location LIKE ?";
            $params[] = "%" . trim($location) . "%";
            $types .= "s";
        }

        // 4. Job Type Filter
        if (!empty($job_type)) {
            $sql .= " AND j.job_type = ?";
            $params[] = trim($job_type);
            $types .= "s";
        }

        // 5. Salary Range Filter
        if (!empty($s_min)) {
            $sql .= " AND j.salary_max >= ?";
            $params[] = (float)$s_min;
            $types .= "d";
        }
        if (!empty($s_max)) {
            $sql .= " AND j.salary_min <= ?";
            $params[] = (float)$s_max;
            $types .= "d";
        }

        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            if (!empty($params)) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
            }
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                return (int)$row['total'];
            }
        }
    }
    return 0;
}

/**
 * Get single job details by job_id (For Job Details & Apply Page)
 */
function get_job_by_id($conn, $job_id) {
    if (!$conn || empty($job_id)) return null;

    $job_id = (int)$job_id;

    $sql = "SELECT j.*, 
                   c.company_name, 
                   c.location AS company_location, 
                   c.industry_type, 
                   cat.category_name 
            FROM jobs j 
            LEFT JOIN company c ON j.company_id = c.company_id 
            LEFT JOIN categories cat ON j.category_id = cat.category_id 
            WHERE j.job_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $job_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        
        if ($res && $row = mysqli_fetch_assoc($res)) {
            return $row; // Job Details Record එක Return කරයි
        }
    }

    return null;
}

// 10.2. Get Jobs List for browse-jobs.php (with Array-to-string fix)
function get_jobs($conn, $filters = [], $limit = 5, $offset = 0) {
    if (!$conn) return [];

    $sql = "SELECT j.*, c.company_name, cat.category_name 
            FROM jobs j 
            LEFT JOIN company c ON j.company_id = c.company_id 
            LEFT JOIN categories cat ON j.category_id = cat.category_id 
            WHERE 1=1";

    $params = [];
    $types = "";

    // 1. Status Filter (Approved සහ Pending දෙකම පෙන්වයි - Project Testing සඳහා)
    // Production එකේදී $sql .= " AND j.status = 'approved'"; ලෙස වෙනස් කළ හැක
    $sql .= " AND (j.status = 'approved' OR j.status = 'pending' OR j.status IS NULL OR j.status = '')";

    // 2. Keyword Filter (Title, Description, Company Name)
    if (!empty($filters['keyword'])) {
        $kw = "%" . trim($filters['keyword']) . "%";
        $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR c.company_name LIKE ?)";
        $params[] = $kw; $params[] = $kw; $params[] = $kw;
        $types .= "sss";
    }

    // 3. Location Filter
    if (!empty($filters['location'])) {
        $sql .= " AND j.location LIKE ?";
        $params[] = "%" . trim($filters['location']) . "%";
        $types .= "s";
    }

    // 4. Category Filter
    if (!empty($filters['category'])) {
        $sql .= " AND j.category_id = ?";
        $params[] = (int)$filters['category'];
        $types .= "i";
    }

    // 5. Job Type Filter
    if (!empty($filters['job_type'])) {
        $sql .= " AND j.job_type = ?";
        $params[] = trim($filters['job_type']);
        $types .= "s";
    }

    // 6. Salary Range Filter
    if (!empty($filters['salary_min'])) {
        $sql .= " AND j.salary_max >= ?";
        $params[] = (float)$filters['salary_min'];
        $types .= "d";
    }
    if (!empty($filters['salary_max'])) {
        $sql .= " AND j.salary_min <= ?";
        $params[] = (float)$filters['salary_max'];
        $types .= "d";
    }

    // Pagination Limit & Offset
    $sql .= " ORDER BY j.job_id DESC LIMIT ? OFFSET ?";
    $params[] = (int)$limit;
    $params[] = (int)$offset;
    $types .= "ii";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $rows = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    return [];
}
//10.3 Get Categories for browse-jobs.php filter dropdown
function get_categories($conn) {
    $categories = [];
    if ($conn) {
        // වෙනස් කළ තැන: jobs table එකේ category column එක වෙනුවට categories table එකෙන් IDs සහ Names ලබා ගැනීම
        $sql = "SELECT category_id, category_name FROM categories ORDER BY category_name ASC";
        $res = mysqli_query($conn, $sql);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $categories[] = $row; // category_id සහ category_name එකතු කරගත් array එකක් ලෙස ලබා දීම
            }
        }
    }
    return $categories;
}

// 11. Get All Jobs List (For browse-jobs.php display)
function get_all_jobs($conn, $keyword = '', $category = '', $limit = 10, $offset = 0, $location = '', $job_type = '') {
    if ($conn) {
        $sql = "SELECT j.*, c.company_name, cat.category_name 
                FROM jobs j 
                LEFT JOIN company c ON j.company_id = c.company_id 
                LEFT JOIN categories cat ON j.category_id = cat.category_id 
                WHERE 1=1"; 

        $params = [];
        $types = "";

        // 1. Keyword Search (Title, Description, Company Name)
        if (!empty($keyword)) {
            if (is_array($keyword)) {
                $keyword = trim(implode(' ', $keyword));
            } else {
                $keyword = trim($keyword);
            }

            if ($keyword !== '') {
                $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR c.company_name LIKE ?)";
                $searchTerm = "%{$keyword}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "sss";
            }
        }

        // 2. Location Filter (URL එකෙන් Matara ආවොත් ඒක Filter වීම)
        if (!empty($location)) {
            $location = trim($location);
            $sql .= " AND j.location LIKE ?";
            $locTerm = "%{$location}%";
            $params[] = $locTerm;
            $types .= "s";
        }

        // 3. Category Filter
        if (!empty($category) && $category !== 'all') {
            $sql .= " AND j.category_id = ?";
            $params[] = (int)$category;
            $types .= "i";
        }

        // 4. Job Type Filter
        if (!empty($job_type) && $job_type !== 'all') {
            $sql .= " AND j.job_type = ?";
            $params[] = trim($job_type);
            $types .= "s";
        }

        $sql .= " ORDER BY j.job_id DESC LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;
        $types .= "ii";

        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            if (!empty($params)) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
            }
            
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $rows = [];
            
            if ($res) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $rows[] = $row;
                }
            }
            return $rows;
        }
    }
    return [];
}
//12 Seeker profile
function get_seeker_categories($conn, $seeker_id) {
    $categories = [];
    if ($conn) {
        $sql = "SELECT category_name FROM seeker_categories WHERE seeker_id = ?";
        $stmt = @mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $seeker_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $categories[] = $row['category_name'];
                }
            }
        }
    }
    return $categories;
}

//12.1 Save or update seeker categories/skills for profile.php
function save_seeker_categories($conn, $seeker_id, $categories) {
    if ($conn) {
        
        $del_sql = "DELETE FROM seeker_categories WHERE seeker_id = ?";
        $stmt = @mysqli_prepare($conn, $del_sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $seeker_id);
            mysqli_stmt_execute($stmt);
        }

        
        if (!empty($categories)) {
            if (!is_array($categories)) {
                $categories = [$categories];
            }

            $ins_sql = "INSERT INTO seeker_categories (seeker_id, category_name) VALUES (?, ?)";
            $stmt_ins = @mysqli_prepare($conn, $ins_sql);
            if ($stmt_ins) {
                foreach ($categories as $cat) {
                    $cat = trim($cat);
                    if (!empty($cat)) {
                        mysqli_stmt_bind_param($stmt_ins, "is", $seeker_id, $cat);
                        mysqli_stmt_execute($stmt_ins);
                    }
                }
            }
        }
        return true;
    }
    return false;
}