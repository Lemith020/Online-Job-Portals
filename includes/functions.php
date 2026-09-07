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
    if (session_status() === PHP_SESSION_NONE) session_start();
    $admin_id = $_SESSION['user']['id'] ?? 1;

    if ($conn) {
        $stmt = @mysqli_prepare($conn, "INSERT INTO admin_activity_log (admin_id, action, details, created_at) VALUES (?, ?, ?, NOW())");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iss", $admin_id, $action, $type);
            mysqli_stmt_execute($stmt);
        }
    }

    if (!isset($_SESSION['activity_logs'])) {
        $_SESSION['activity_logs'] = [];
    }
    array_unshift($_SESSION['activity_logs'], [
        'id' => uniqid(),
        'action' => $action,
        'type' => $type,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    $_SESSION['activity_logs'] = array_slice($_SESSION['activity_logs'], 0, 20);
}

function get_recent_activities($limit = 6) {
    global $conn;
    if ($conn) {
        $sql = "SELECT log_id AS id, action, details AS type, created_at FROM admin_activity_log ORDER BY log_id DESC LIMIT " . intval($limit);
        $res = @mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) > 0) {
            $rows = [];
            while ($row = mysqli_fetch_assoc($res)) {
                $rows[] = $row;
            }
            return $rows;
        }
    }

    if (!empty($_SESSION['activity_logs'])) {
        return array_slice($_SESSION['activity_logs'], 0, $limit);
    }

    return [
        ['id' => 1, 'action' => 'Job approved: Senior Software Engineer at Dialog Axiata', 'type' => 'job', 'created_at' => date('Y-m-d H:i:s', time() - 300)],
        ['id' => 2, 'action' => 'New Company verified: Virtusa Pvt Ltd', 'type' => 'company', 'created_at' => date('Y-m-d H:i:s', time() - 1800)],
        ['id' => 3, 'action' => 'User suspended: spammer_99@mail.com', 'type' => 'user', 'created_at' => date('Y-m-d H:i:s', time() - 4200)],
        ['id' => 4, 'action' => 'Review flagged: Flagged abusive comment on WSO2', 'type' => 'review', 'created_at' => date('Y-m-d H:i:s', time() - 14400)],
        ['id' => 5, 'action' => 'Subscription upgraded: Enterprise Plan for Sysco LABS', 'type' => 'subscription', 'created_at' => date('Y-m-d H:i:s', time() - 86400)],
        ['id' => 6, 'action' => 'Category updated: Artificial Intelligence & Data Science', 'type' => 'category', 'created_at' => date('Y-m-d H:i:s', time() - 98000)],
    ];
}

// Admin KPI Metrics
function get_admin_metrics() {
    global $conn;
    $metrics = [
        'total_users' => 1248,
        'total_job_seekers' => 895,
        'total_companies' => 84,
        'total_jobs' => 412,
        'pending_jobs' => 18,
        'flagged_reviews' => 7,
        'active_subscriptions' => 52
    ];

    if ($conn) {
        $u_res = @mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM users");
        if ($u_res && $row = mysqli_fetch_assoc($u_res)) $metrics['total_users'] = (int)$row['cnt'];

        $s_res = @mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM job_seekers");
        if ($s_res && $row = mysqli_fetch_assoc($s_res)) $metrics['total_job_seekers'] = (int)$row['cnt'];

        $c_res = @mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM companies");
        if ($c_res && $row = mysqli_fetch_assoc($c_res)) $metrics['total_companies'] = (int)$row['cnt'];

        $j_res = @mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM jobs");
        if ($j_res && $row = mysqli_fetch_assoc($j_res)) $metrics['total_jobs'] = (int)$row['cnt'];

        $pj_res = @mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM jobs WHERE status = 'Pending Approval'");
        if ($pj_res && $row = mysqli_fetch_assoc($pj_res)) $metrics['pending_jobs'] = (int)$row['cnt'];

        $r_res = @mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM reviews WHERE status = 'Flagged' OR is_flagged = 1");
        if ($r_res && $row = mysqli_fetch_assoc($r_res)) $metrics['flagged_reviews'] = (int)$row['cnt'];

        $sub_res = @mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM user_subscriptions WHERE is_active = 1");
        if ($sub_res && $row = mysqli_fetch_assoc($sub_res)) $metrics['active_subscriptions'] = (int)$row['cnt'];
    }

    return $metrics;
}

// -------------------------------------------------------------
// USER MANAGEMENT
// -------------------------------------------------------------
function get_all_users($role_filter = '', $search = '') {
    global $conn;
    if ($conn) {
        $sql = "SELECT id, name, email, role, phone, status, created_at FROM users WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($role_filter)) {
            $sql .= " AND role = ?";
            $params[] = $role_filter;
            $types .= "s";
        }
        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }
        $sql .= " ORDER BY id DESC";

        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            if (!empty($params)) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
            }
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $users = [];
            while ($row = mysqli_fetch_assoc($res)) {
                $users[] = $row;
            }
            if (!empty($users)) return $users;
        }
    }

    $mock_users = [
        ['id' => 1, 'name' => 'Admin Kamal Perera', 'email' => 'admin@jobportal.lk', 'role' => 'admin', 'phone' => '+94 77 123 4567', 'status' => 'Active', 'created_at' => '2026-01-10 09:30:00'],
        ['id' => 2, 'name' => 'Dilshan Silva', 'email' => 'dilshan.silva@gmail.com', 'role' => 'seeker', 'phone' => '+94 71 987 6543', 'status' => 'Active', 'created_at' => '2026-02-14 11:20:00'],
        ['id' => 3, 'name' => 'Virtusa HR Team', 'email' => 'careers@virtusa.com', 'role' => 'company', 'phone' => '+94 11 234 5678', 'status' => 'Active', 'created_at' => '2026-02-18 14:45:00'],
        ['id' => 4, 'name' => 'Nadeesha Fernando', 'email' => 'nadeesha.f@hotmail.com', 'role' => 'seeker', 'phone' => '+94 76 555 4321', 'status' => 'Active', 'created_at' => '2026-02-22 16:10:00'],
        ['id' => 5, 'name' => 'Dialog Axiata Careers', 'email' => 'jobs@dialog.lk', 'role' => 'company', 'phone' => '+94 77 733 3333', 'status' => 'Active', 'created_at' => '2026-03-01 08:50:00'],
        ['id' => 6, 'name' => 'Kasun Jayawardena', 'email' => 'kasun.j@yahoo.com', 'role' => 'seeker', 'phone' => '+94 70 111 2233', 'status' => 'Suspended', 'created_at' => '2026-03-05 10:15:00'],
        ['id' => 7, 'name' => 'WSO2 Recruitment', 'email' => 'hr@wso2.com', 'role' => 'company', 'phone' => '+94 11 214 5345', 'status' => 'Active', 'created_at' => '2026-03-12 13:00:00'],
        ['id' => 8, 'name' => 'Anura Gunasekara', 'email' => 'anura.g@gmail.com', 'role' => 'seeker', 'phone' => '+94 72 345 6789', 'status' => 'Pending', 'created_at' => '2026-03-15 17:30:00']
    ];

    if (!empty($role_filter)) {
        $mock_users = array_values(array_filter($mock_users, fn($u) => $u['role'] === $role_filter));
    }
    if (!empty($search)) {
        $s = strtolower($search);
        $mock_users = array_values(array_filter($mock_users, fn($u) => str_contains(strtolower($u['name']), $s) || str_contains(strtolower($u['email']), $s)));
    }
    return $mock_users;
}

function toggle_user_status($id, $new_status) {
    global $conn;
    if ($conn) {
        $stmt = mysqli_prepare($conn, "UPDATE users SET status = ? WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $new_status, $id);
            return mysqli_stmt_execute($stmt);
        }
    }
    return true;
}

function delete_user($id) {
    global $conn;
    if ($conn) {
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            return mysqli_stmt_execute($stmt);
        }
    }
    return true;
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
function get_all_jobs_admin($status_filter = '', $category_filter = '', $search = '') {
    global $conn;
    if ($conn) {
        $sql = "SELECT j.id, j.title, j.company_name, j.location, j.job_type, j.salary_range, j.status, j.posted_date, c.name AS category_name
                FROM jobs j
                LEFT JOIN categories c ON j.category_id = c.id
                WHERE 1=1";
        if (!empty($status_filter)) {
            $sql .= " AND j.status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
        }
        if (!empty($category_filter)) {
            $sql .= " AND c.name = '" . mysqli_real_escape_string($conn, $category_filter) . "'";
        }
        if (!empty($search)) {
            $s = mysqli_real_escape_string($conn, $search);
            $sql .= " AND (j.title LIKE '%$s%' OR j.company_name LIKE '%$s%' OR j.location LIKE '%$s%')";
        }
        $sql .= " ORDER BY j.id DESC";
        $res = @mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) > 0) {
            $jobs = [];
            while ($row = mysqli_fetch_assoc($res)) $jobs[] = $row;
            return $jobs;
        }
    }

    $mock_jobs = [
        ['id' => 1, 'title' => 'Senior Full Stack Engineer', 'company_name' => 'Virtusa (Pvt) Ltd', 'category_name' => 'Software Engineering', 'location' => 'Colombo 07', 'job_type' => 'Full-time', 'salary_range' => 'Rs. 250,000 - Rs. 400,000', 'status' => 'Approved', 'posted_date' => '2026-03-25 10:00:00'],
        ['id' => 2, 'title' => 'DevOps & Cloud Architect', 'company_name' => 'Dialog Axiata PLC', 'category_name' => 'Cloud & DevOps', 'location' => 'Colombo 02', 'job_type' => 'Full-time', 'salary_range' => 'Rs. 300,000 - Rs. 500,000', 'status' => 'Approved', 'posted_date' => '2026-03-26 14:30:00'],
        ['id' => 3, 'title' => 'UI/UX Product Designer', 'company_name' => 'WSO2 Lanka', 'category_name' => 'Design & Creative', 'location' => 'Colombo 03', 'job_type' => 'Remote', 'salary_range' => 'Rs. 180,000 - Rs. 280,000', 'status' => 'Approved', 'posted_date' => '2026-03-27 09:15:00'],
        ['id' => 4, 'title' => 'Digital Marketing Lead', 'company_name' => 'Apex Digital Media', 'category_name' => 'Marketing & Sales', 'location' => 'Nugegoda', 'job_type' => 'Full-time', 'salary_range' => 'Rs. 120,000 - Rs. 180,000', 'status' => 'Pending Approval', 'posted_date' => '2026-03-29 11:20:00'],
        ['id' => 5, 'title' => 'Junior QA Automation Tester', 'company_name' => 'Virtusa (Pvt) Ltd', 'category_name' => 'Software Engineering', 'location' => 'Colombo 07', 'job_type' => 'Contract', 'salary_range' => 'Rs. 90,000 - Rs. 140,000', 'status' => 'Pending Approval', 'posted_date' => '2026-03-30 16:45:00'],
        ['id' => 6, 'title' => 'Cybersecurity Analyst', 'company_name' => 'Dialog Axiata PLC', 'category_name' => 'Security', 'location' => 'Colombo 02', 'job_type' => 'Full-time', 'salary_range' => 'Rs. 220,000 - Rs. 350,000', 'status' => 'Pending Approval', 'posted_date' => '2026-03-31 08:30:00'],
        ['id' => 7, 'title' => 'Casino Content Writer (Spam)', 'company_name' => 'Suspicious Corp', 'category_name' => 'Content & Writing', 'location' => 'Online', 'job_type' => 'Remote', 'salary_range' => 'Rs. 500,000', 'status' => 'Rejected', 'posted_date' => '2026-03-20 12:00:00']
    ];

    if (!empty($status_filter)) {
        $mock_jobs = array_values(array_filter($mock_jobs, fn($j) => $j['status'] === $status_filter));
    }
    if (!empty($category_filter)) {
        $mock_jobs = array_values(array_filter($mock_jobs, fn($j) => $j['category_name'] === $category_filter));
    }
    if (!empty($search)) {
        $s = strtolower($search);
        $mock_jobs = array_values(array_filter($mock_jobs, fn($j) => str_contains(strtolower($j['title']), $s) || str_contains(strtolower($j['company_name']), $s)));
    }
    return $mock_jobs;
}

function update_job_status($id, $status) {
    global $conn;
    if ($conn) {
        $stmt = mysqli_prepare($conn, "UPDATE jobs SET status = ? WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $status, $id);
            return mysqli_stmt_execute($stmt);
        }
    }
    return true;
}

function delete_job_admin($id) {
    global $conn;
    if ($conn) {
        $stmt = mysqli_prepare($conn, "DELETE FROM jobs WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            return mysqli_stmt_execute($stmt);
        }
    }
    return true;
}

// -------------------------------------------------------------
// CATEGORIES MANAGEMENT
// -------------------------------------------------------------
function get_all_categories_admin() {
    global $conn;
    if ($conn) {
        $sql = "SELECT c.id, c.name, c.icon, c.created_at, 
                (SELECT COUNT(*) FROM jobs j WHERE j.category_id = c.id) AS job_count
                FROM categories c ORDER BY c.name ASC";
        $res = @mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) > 0) {
            $cats = [];
            while ($row = mysqli_fetch_assoc($res)) $cats[] = $row;
            return $cats;
        }
    }

    return [
        ['id' => 1, 'name' => 'Software Engineering', 'icon' => 'code', 'job_count' => 142, 'created_at' => '2026-01-01'],
        ['id' => 2, 'name' => 'Cloud & DevOps', 'icon' => 'cloud', 'job_count' => 64, 'created_at' => '2026-01-01'],
        ['id' => 3, 'name' => 'Design & Creative', 'icon' => 'pen-tool', 'job_count' => 48, 'created_at' => '2026-01-01'],
        ['id' => 4, 'name' => 'Marketing & Sales', 'icon' => 'trending-up', 'job_count' => 52, 'created_at' => '2026-01-01'],
        ['id' => 5, 'name' => 'Accounting & Finance', 'icon' => 'dollar-sign', 'job_count' => 38, 'created_at' => '2026-01-01'],
        ['id' => 6, 'name' => 'Healthcare & Medical', 'icon' => 'activity', 'job_count' => 29, 'created_at' => '2026-01-01'],
        ['id' => 7, 'name' => 'Human Resources', 'icon' => 'users', 'job_count' => 21, 'created_at' => '2026-01-01'],
        ['id' => 8, 'name' => 'Security & Networking', 'icon' => 'shield', 'job_count' => 18, 'created_at' => '2026-01-01']
    ];
}

function save_category($name, $icon = 'briefcase', $id = null) {
    global $conn;
    if ($conn) {
        if ($id) {
            $stmt = mysqli_prepare($conn, "UPDATE categories SET name = ?, icon = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $name, $icon, $id);
            return mysqli_stmt_execute($stmt);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO categories (name, icon) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ss", $name, $icon);
            return mysqli_stmt_execute($stmt);
        }
    }
    return true;
}

function delete_category($id) {
    global $conn;
    if ($conn) {
        $stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            return mysqli_stmt_execute($stmt);
        }
    }
    return true;
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
    if ($conn) {
        $res = @mysqli_query($conn, "SELECT * FROM subscription_plans ORDER BY price ASC");
        if ($res && mysqli_num_rows($res) > 0) {
            $plans = [];
            while ($row = mysqli_fetch_assoc($res)) $plans[] = $row;
            return $plans;
        }
    }

    return [
        ['plan_id' => 1, 'name' => 'Starter / Free', 'price' => 0.00, 'duration_days' => 30, 'max_jobs' => 3, 'features' => 'Standard job postings, Basic candidate search, 3 Active job listings', 'is_active' => 1],
        ['plan_id' => 2, 'name' => 'Professional Employer', 'price' => 15000.00, 'duration_days' => 30, 'max_jobs' => 15, 'features' => 'Featured job tag, Unlimited applicant CV downloads, Priority tech support', 'is_active' => 1],
        ['plan_id' => 3, 'name' => 'Enterprise Unlimited', 'price' => 45000.00, 'duration_days' => 90, 'max_jobs' => 100, 'features' => 'Dedicated account manager, Automated candidate shortlisting, Custom branding', 'is_active' => 1]
    ];
}

function get_user_subscriptions_admin($plan_filter = '', $search = '') {
    global $conn;
    if ($conn) {
        $sql = "SELECT us.sub_id AS id, u.name AS user_name, u.email, sp.name AS plan_name, sp.price, us.start_date, us.end_date, us.is_active
                FROM user_subscriptions us
                JOIN users u ON us.user_id = u.id
                JOIN subscription_plans sp ON us.plan_id = sp.plan_id
                ORDER BY us.sub_id DESC";
        $res = @mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) > 0) {
            $subs = [];
            while ($row = mysqli_fetch_assoc($res)) $subs[] = $row;
            return $subs;
        }
    }

    return [
        ['id' => 101, 'user_name' => 'Virtusa (Pvt) Ltd', 'email' => 'careers@virtusa.com', 'plan_name' => 'Enterprise Unlimited', 'price' => 45000.00, 'start_date' => '2026-02-01', 'end_date' => '2026-05-01', 'is_active' => 1],
        ['id' => 102, 'user_name' => 'Dialog Axiata PLC', 'email' => 'jobs@dialog.lk', 'plan_name' => 'Enterprise Unlimited', 'price' => 45000.00, 'start_date' => '2026-03-01', 'end_date' => '2026-06-01', 'is_active' => 1],
        ['id' => 103, 'user_name' => 'WSO2 Lanka', 'email' => 'hr@wso2.com', 'plan_name' => 'Professional Employer', 'price' => 15000.00, 'start_date' => '2026-03-15', 'end_date' => '2026-04-15', 'is_active' => 1],
        ['id' => 104, 'user_name' => 'Apex Digital Media', 'email' => 'contact@apexdigital.lk', 'plan_name' => 'Starter / Free', 'price' => 0.00, 'start_date' => '2026-03-20', 'end_date' => '2026-04-20', 'is_active' => 1],
        ['id' => 105, 'user_name' => 'FastTrack Logistics', 'email' => 'support@fasttrack.lk', 'plan_name' => 'Professional Employer', 'price' => 15000.00, 'start_date' => '2026-01-10', 'end_date' => '2026-02-10', 'is_active' => 0]
    ];
}

function toggle_user_subscription_status($sub_id, $is_active) {
    global $conn;
    if ($conn) {
        $stmt = mysqli_prepare($conn, "UPDATE user_subscriptions SET is_active = ? WHERE sub_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $is_active, $sub_id);
            return mysqli_stmt_execute($stmt);
        }
    }
    return true;
}

// -------------------------------------------------------------
// SYSTEM SETTINGS
// -------------------------------------------------------------
function get_system_settings() {
    global $conn;
    if ($conn) {
        $res = @mysqli_query($conn, "SELECT * FROM settings WHERE id = 1");
        if ($res && $row = mysqli_fetch_assoc($res)) {
            return $row;
        }
    }

    return [
        'site_name' => 'JobPortal.lk',
        'site_email' => 'admin@jobportal.lk',
        'maintenance_mode' => 0,
        'enable_registration' => 1,
        'enable_job_approval' => 1,
        'jobs_per_page' => 10
    ];
}

function save_system_settings($data) {
    global $conn;
    if ($conn) {
        $stmt = mysqli_prepare($conn, "UPDATE settings SET site_name = ?, site_email = ?, maintenance_mode = ?, enable_registration = ?, enable_job_approval = ?, jobs_per_page = ? WHERE id = 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssiiii", $data['site_name'], $data['site_email'], $data['maintenance_mode'], $data['enable_registration'], $data['enable_job_approval'], $data['jobs_per_page']);
            return mysqli_stmt_execute($stmt);
        }
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

function get_applications($conn, $seeker_id, $status_filter = '') {
    $sql = "SELECT a.app_id, a.apply_date, a.status, a.experience,
                   j.title, c.company_name, cv.file_path
            FROM applications a
            JOIN jobs j ON a.job_id = j.job_id
            JOIN company c ON j.company_id = c.company_id
            JOIN cvs cv ON a.cv_id = cv.cv_id
            WHERE a.seeker_id = ?" . ($status_filter ? " AND a.status = ?" : "") . "
            ORDER BY a.apply_date DESC";
    $stmt = mysqli_prepare($conn, $sql);
    if ($status_filter) {
        mysqli_stmt_bind_param($stmt, "is", $seeker_id, $status_filter);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $seeker_id);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
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
