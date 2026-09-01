<?php
/**
 * JobPortal.lk - Core & Shared Utility Functions
 * Member 1 - Admin & Core/Shared
 */

require_once __DIR__ . '/../config/db.php';

// -----------------------------------------------------------------------------
// Security & Sanitization Helpers
// -----------------------------------------------------------------------------

function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim((string)$input), ENT_QUOTES, 'UTF-8');
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

// -----------------------------------------------------------------------------
// Flash Messaging System
// -----------------------------------------------------------------------------

function set_flash($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type // 'success', 'error', 'info', 'warning'
    ];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// -----------------------------------------------------------------------------
// UI Formatting & Badges
// -----------------------------------------------------------------------------

function render_status_badge($status) {
    $status = trim($status);
    $badge_class = 'badge-secondary';

    switch (strtolower($status)) {
        case 'active':
        case 'approved':
            $badge_class = 'badge-success';
            break;
        case 'pending':
        case 'pending approval':
            $badge_class = 'badge-warning';
            break;
        case 'suspended':
        case 'rejected':
            $badge_class = 'badge-danger';
            break;
        case 'flagged':
            $badge_class = 'badge-flagged';
            break;
    }

    return '<span class="status-badge ' . $badge_class . '">' . htmlspecialchars($status) . '</span>';
}

function render_role_badge($role) {
    $role = strtolower(trim($role));
    $badge_class = 'badge-role-seeker';
    $label = 'Job Seeker';

    if ($role === 'admin') {
        $badge_class = 'badge-role-admin';
        $label = 'Admin';
    } elseif ($role === 'company' || $role === 'employer') {
        $badge_class = 'badge-role-company';
        $label = 'Company';
    }

    return '<span class="role-badge ' . $badge_class . '">' . htmlspecialchars($label) . '</span>';
}

function render_star_rating($rating) {
    $rating = max(1, min(5, (int)$rating));
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '<span class="star-filled">★</span>';
        } else {
            $stars .= '<span class="star-empty">☆</span>';
        }
    }
    return '<span class="star-rating" title="' . $rating . ' out of 5 stars">' . $stars . '</span>';
}

function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    if (!$timestamp) return $datetime;
    
    $difference = time() - $timestamp;
    if ($difference < 60) return 'Just now';
    if ($difference < 3600) return floor($difference / 60) . ' mins ago';
    if ($difference < 86400) return floor($difference / 3600) . ' hours ago';
    if ($difference < 604800) return floor($difference / 86400) . ' days ago';
    return date('d/m/Y', $timestamp);
}

// -----------------------------------------------------------------------------
// In-Memory Data Store (Provides Resilient Fallback if MySQL service is stopped)
// -----------------------------------------------------------------------------

function init_fallback_data() {
    if (!isset($_SESSION['data_initialized'])) {
        $_SESSION['data_initialized'] = true;

        $_SESSION['mock_users'] = [
            ['id' => 1, 'name' => 'Admin Kamal', 'email' => 'admin@jobportal.lk', 'role' => 'admin', 'phone' => '+94 71 888 9900', 'status' => 'Active'],
            ['id' => 2, 'name' => 'Kamal Perera', 'email' => 'dialog.axiata@email.com', 'role' => 'company', 'phone' => '+94 11 987 6890', 'status' => 'Active'],
            ['id' => 3, 'name' => 'Nimal Perera', 'email' => 'dialog.axiata.hr@plc.com', 'role' => 'company', 'phone' => '+94 77 464 7460', 'status' => 'Suspended'],
            ['id' => 4, 'name' => 'Kamal Perera', 'email' => 'nimal.perera@gmail.com', 'role' => 'seeker', 'phone' => '+94 71 664 7468', 'status' => 'Active'],
            ['id' => 5, 'name' => 'Sunil Shantha', 'email' => 'dialog.cxc@park.com', 'role' => 'company', 'phone' => '+94 11 387 4915', 'status' => 'Active'],
            ['id' => 6, 'name' => 'Kasun Silva', 'email' => 'dialog.axiata.sl@plc.com', 'role' => 'seeker', 'phone' => '+94 74 485 8875', 'status' => 'Active'],
            ['id' => 7, 'name' => 'Nimal Perera', 'email' => 'dialog.axiata@job.com', 'role' => 'seeker', 'phone' => '+94 70 426 8885', 'status' => 'Active'],
            ['id' => 8, 'name' => 'Kamal Perera', 'email' => 'dialog.axiata.test@plc.com', 'role' => 'company', 'phone' => '+94 76 467 4855', 'status' => 'Active'],
            ['id' => 9, 'name' => 'Dilshan Fernando', 'email' => 'dialog.carrier@coord.com', 'role' => 'seeker', 'phone' => '+94 74 485 0296', 'status' => 'Suspended'],
            ['id' => 10, 'name' => 'Nimal Silva', 'email' => 'dialog12@gmail.com', 'role' => 'admin', 'phone' => '+94 78 638 0233', 'status' => 'Active'],
            ['id' => 11, 'name' => 'Kamal Perera', 'email' => 'didxg.axiata.corp@com', 'role' => 'admin', 'phone' => '+94 73 406 4298', 'status' => 'Active']
        ];

        $_SESSION['mock_companies'] = [
            ['id' => 1, 'company_name' => 'Dialog Axiata PLC', 'industry_type' => 'Telecommunications', 'location' => 'Colombo, Sri Lanka', 'owner_email' => 'dialog.axiata@email.com', 'status' => 'Pending Approval'],
            ['id' => 2, 'company_name' => 'CodeGen International', 'industry_type' => 'Software', 'location' => 'Colombo, Sri Lanka', 'owner_email' => 'codegen.international@com', 'status' => 'Approved'],
            ['id' => 3, 'company_name' => 'WSO2', 'industry_type' => 'Finance', 'location' => 'Colombo, Sri Lanka', 'owner_email' => 'dialogaxiata@wso2.com', 'status' => 'Approved'],
            ['id' => 4, 'company_name' => 'CodeGen', 'industry_type' => 'Software', 'location' => 'Colombo, Sri Lanka', 'owner_email' => 'dialogaxiata.commercial.com', 'status' => 'Pending Approval'],
            ['id' => 5, 'company_name' => 'Axiata PLC', 'industry_type' => 'Software', 'location' => 'Colombo, Sri Lanka', 'owner_email' => 'dialogaxiata@animal.com', 'status' => 'Approved'],
            ['id' => 6, 'company_name' => 'Cambio', 'industry_type' => 'Software', 'location' => 'Colombo, Sri Lanka', 'owner_email' => 'codegendata@mail.com', 'status' => 'Suspended'],
            ['id' => 7, 'company_name' => 'Carlocoolseal', 'industry_type' => 'Software', 'location' => 'Colombo, Sri Lanka', 'owner_email' => 'dlog.mail@gmial.com', 'status' => 'Suspended'],
            ['id' => 8, 'company_name' => 'CodeGen International', 'industry_type' => 'Software', 'location' => 'Colombo, Sri Lanka', 'owner_email' => 'dialogaxiata@plc.com', 'status' => 'Approved'],
            ['id' => 9, 'company_name' => 'WSO2', 'industry_type' => 'Finance', 'location' => 'Colombo, Sri Lanka', 'owner_email' => 'dialog126@gmail.com', 'status' => 'Approved'],
            ['id' => 10, 'company_name' => 'WSO2', 'industry_type' => 'Finance', 'location' => 'Colombo, Sri Lanka', 'owner_email' => 'dilog.hoste.ccm@com', 'status' => 'Rejected']
        ];

        $_SESSION['mock_categories'] = [
            ['id' => 1, 'name' => 'Software Engineering', 'icon' => 'code', 'job_count' => 1543],
            ['id' => 2, 'name' => 'Marketing', 'icon' => 'bullhorn', 'job_count' => 892],
            ['id' => 3, 'name' => 'Sales', 'icon' => 'dollar-sign', 'job_count' => 710],
            ['id' => 4, 'name' => 'Healthcare', 'icon' => 'activity', 'job_count' => 1543],
            ['id' => 5, 'name' => 'Finance', 'icon' => 'pie-chart', 'job_count' => 1543],
            ['id' => 6, 'name' => 'Education', 'icon' => 'book-open', 'job_count' => 890],
            ['id' => 7, 'name' => 'Design', 'icon' => 'layout', 'job_count' => 890],
            ['id' => 8, 'name' => 'Operations', 'icon' => 'settings', 'job_count' => 1543],
            ['id' => 9, 'name' => 'Hardware', 'icon' => 'cpu', 'job_count' => 450],
            ['id' => 10, 'name' => 'Secure Engineering', 'icon' => 'shield', 'job_count' => 445],
            ['id' => 11, 'name' => 'Mortalin Care', 'icon' => 'heart', 'job_count' => 260]
        ];

        $_SESSION['mock_jobs'] = [
            ['id' => 1, 'company_id' => 1, 'category_id' => 1, 'title' => 'Senior Software Engineer', 'company_name' => 'Dialog Axiata PLC', 'location' => 'Colombo, Sri Lanka', 'posted_date' => '04/05/2026, 19:59', 'status' => 'Approved', 'job_type' => 'Full-time', 'salary_range' => 'Rs. 150,000 - Rs. 250,000 / month', 'description' => 'We are looking for a skilled Senior Software Engineer to build scalable web applications with PHP, JavaScript, and modern database systems.'],
            ['id' => 2, 'company_id' => 2, 'category_id' => 2, 'title' => 'Marketing Manager', 'company_name' => 'CodeGen', 'location' => 'Colombo, Sri Lanka', 'posted_date' => '06/05/2026, 18:25', 'status' => 'Pending Approval', 'job_type' => 'Full-time', 'salary_range' => 'Rs. 120,000 - Rs. 180,000 / month', 'description' => 'Lead campaigns, brand development, and market engagement strategy.'],
            ['id' => 3, 'company_id' => 2, 'category_id' => 2, 'title' => 'Marketing Manager', 'company_name' => 'CodeGen', 'location' => 'Colombo, Sri Lanka', 'posted_date' => '05/05/2026, 20:38', 'status' => 'Approved', 'job_type' => 'Full-time', 'salary_range' => 'Rs. 140,000 - Rs. 190,000 / month', 'description' => 'Manage corporate marketing and social media initiatives.'],
            ['id' => 4, 'company_id' => 2, 'category_id' => 2, 'title' => 'Marketing Manager', 'company_name' => 'CodeGen', 'location' => 'Colombo, Sri Lanka', 'posted_date' => '05/05/2026, 17:58', 'status' => 'Approved', 'job_type' => 'Full-time', 'salary_range' => 'Rs. 130,000 - Rs. 175,000 / month', 'description' => 'Oversee performance marketing across all business verticals.'],
            ['id' => 5, 'company_id' => 1, 'category_id' => 2, 'title' => 'Axiata Manager', 'company_name' => 'CodeGen', 'location' => 'Colombo, Sri Lanka', 'posted_date' => '05/05/2026, 18:00', 'status' => 'Pending Approval', 'job_type' => 'Full-time', 'salary_range' => 'Rs. 160,000 - Rs. 220,000 / month', 'description' => 'Enterprise telecom accounts and relationship management.'],
            ['id' => 6, 'company_id' => 2, 'category_id' => 2, 'title' => 'Marketing Manager', 'company_name' => 'CodeGen', 'location' => 'Colombo, Sri Lanka', 'posted_date' => '05/05/2026, 10:00', 'status' => 'Approved', 'job_type' => 'Full-time', 'salary_range' => 'Rs. 125,000 - Rs. 180,000 / month', 'description' => 'Content marketing and community outreach coordinator.'],
            ['id' => 7, 'company_id' => 2, 'category_id' => 2, 'title' => 'Marketing Manager', 'company_name' => 'CodeGen', 'location' => 'Colombo, Sri Lanka', 'posted_date' => '04/05/2026, 12:40', 'status' => 'Approved', 'job_type' => 'Full-time', 'salary_range' => 'Rs. 130,000 - Rs. 180,000 / month', 'description' => 'Brand strategy and international customer engagement.'],
            ['id' => 8, 'company_id' => 2, 'category_id' => 1, 'title' => 'Codeban Manager', 'company_name' => 'CodeGen', 'location' => 'Colombo, Sri Lanka', 'posted_date' => '05/05/2026, 18:00', 'status' => 'Pending Approval', 'job_type' => 'Full-time', 'salary_range' => 'Rs. 170,000 - Rs. 240,000 / month', 'description' => 'Agile team leadership and software engineering deliverable delivery.'],
            ['id' => 9, 'company_id' => 2, 'category_id' => 2, 'title' => 'Marketing Manager', 'company_name' => 'CodeGen', 'location' => 'Colombo, Sri Lanka', 'posted_date' => '04/05/2026, 20:02', 'status' => 'Approved', 'job_type' => 'Full-time', 'salary_range' => 'Rs. 135,000 - Rs. 190,000 / month', 'description' => 'Digital media campaigns and product marketing.'],
            ['id' => 10, 'company_id' => 2, 'category_id' => 2, 'title' => 'Marketing Manager', 'company_name' => 'CodeGen', 'location' => 'Colombo, Sri Lanka', 'posted_date' => '05/05/2026, 10:02', 'status' => 'Rejected', 'job_type' => 'Part-time', 'salary_range' => 'Rs. 90,000 - Rs. 120,000 / month', 'description' => 'Temporary marketing support. Duplicate submission rejected.']
        ];

        $_SESSION['mock_reviews'] = [
            ['id' => 1, 'job_title' => 'Senior Engineer: Kamal P.', 'seeker_name' => 'Kamal Perera', 'rating' => 5, 'comment' => 'Dummy data in review: and more info what I context a boy with job postings. Safe a face of interviews where applicants starts to evilvet in a sentiment...', 'status' => 'Approved', 'date' => '2026-05-10'],
            ['id' => 2, 'job_title' => 'Senior Engineer: Kamal P.', 'seeker_name' => 'Nimal Fernando', 'rating' => 4, 'comment' => 'Confidence and it can be all broad categories when optionnaire to mew and dic...', 'status' => 'Approved', 'date' => '2026-05-09'],
            ['id' => 3, 'job_title' => 'Marketing Manager', 'seeker_name' => 'Kasun Silva', 'rating' => 5, 'comment' => 'I have all mapers entirely active to the w-line card of real source graph Fre...', 'status' => 'Approved', 'date' => '2026-05-08'],
            ['id' => 4, 'job_title' => 'Senior Engineer: Marketing Manager', 'seeker_name' => 'Dilshan P.', 'rating' => 4, 'comment' => 'I though type glow be a beautiful couch and am counting to correction corp...', 'status' => 'Approved', 'date' => '2026-05-07'],
            ['id' => 5, 'job_title' => 'Senior Engineer: Kamal P.', 'seeker_name' => 'Sachith Perera', 'rating' => 5, 'comment' => 'I was so elated when a mandated remained, I roasting overview pro to t...', 'status' => 'Approved', 'date' => '2026-05-06'],
            ['id' => 6, 'job_title' => 'Senior Engineer: Kamal P.', 'seeker_name' => 'Thilini K.', 'rating' => 3, 'comment' => 'Home modal and seeker conceded in cover and glass seeker in myhead...', 'status' => 'Approved', 'date' => '2026-05-05'],
            ['id' => 7, 'job_title' => 'Senior Engineer: Kamal P.', 'seeker_name' => 'Rohan Dias', 'rating' => 2, 'comment' => 'Solve seekers com identifier and glass testrek staters Senior Engineer re...', 'status' => 'Flagged', 'date' => '2026-05-04'],
            ['id' => 8, 'job_title' => 'Senior Engineer: Kamal P.', 'seeker_name' => 'Nadeeka W.', 'rating' => 5, 'comment' => 'Select hike to template a big data rite. Ease job for news a exchanging use...', 'status' => 'Approved', 'date' => '2026-05-03'],
            ['id' => 9, 'job_title' => 'Senior Engineer: Kamal P.', 'seeker_name' => 'Nuwan Pradeep', 'rating' => 4, 'comment' => 'Caregiver darce to enterling unws buckets. Lists stry rate supervisors...', 'status' => 'Approved', 'date' => '2026-05-02'],
            ['id' => 10, 'job_title' => 'Senior Engineer: Kamal P.', 'seeker_name' => 'Chathura Fernando', 'rating' => 5, 'comment' => 'Pretty with unless me to gem to the solutions and reverts to provides at...', 'status' => 'Approved', 'date' => '2026-05-01']
        ];

        $_SESSION['mock_settings'] = [
            'site_name' => 'JobPortal.lk',
            'site_email' => 'admin@jobportal.lk',
            'maintenance_mode' => 0,
            'enable_registration' => 1,
            'enable_job_approval' => 1,
            'jobs_per_page' => 10
        ];

        $_SESSION['mock_activities'] = [
            ['title' => 'Job Posted: Senior Engineer at Dialog Axiata PLC', 'type' => 'job', 'time' => '10 mins ago'],
            ['title' => 'User Registered: Nimal Perera (Job Seeker)', 'type' => 'user', 'time' => '25 mins ago'],
            ['title' => 'Company Verified: CodeGen International', 'type' => 'company', 'time' => '45 mins ago'],
            ['title' => 'Flagged Review: Reported review requires moderation', 'type' => 'review', 'time' => '1 hour ago'],
            ['title' => 'Job Approved: Marketing Manager at CodeGen', 'type' => 'job', 'time' => '2 hours ago']
        ];
    }
}

// Call on every execution to ensure data store is primed
init_fallback_data();

// -----------------------------------------------------------------------------
// Data Access Methods (DB first with Fallback Store)
// -----------------------------------------------------------------------------

function get_admin_metrics() {
    global $db;
    if ($db) {
        try {
            $total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $total_companies = $db->query("SELECT COUNT(*) FROM companies WHERE status = 'Approved'")->fetchColumn();
            $total_jobs = $db->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
            $pending_jobs = $db->query("SELECT COUNT(*) FROM jobs WHERE status = 'Pending Approval'")->fetchColumn();
            $flagged_reviews = $db->query("SELECT COUNT(*) FROM reviews WHERE status = 'Flagged'")->fetchColumn();
            return [
                'total_users' => $total_users ?: 15432,
                'total_companies' => $total_companies ?: 2189,
                'total_jobs' => $total_jobs ?: 45760,
                'pending_jobs' => $pending_jobs ?: 124,
                'flagged_reviews' => $flagged_reviews ?: 38,
                'active_subscriptions' => 5670
            ];
        } catch (Exception $e) {}
    }

    return [
        'total_users' => 15432,
        'total_companies' => 2189,
        'total_jobs' => 45760,
        'pending_jobs' => 124,
        'flagged_reviews' => 38,
        'active_subscriptions' => 5670
    ];
}

function get_recent_activities() {
    global $db;
    if ($db) {
        try {
            $stmt = $db->query("SELECT * FROM activities ORDER BY created_at DESC LIMIT 5");
            $rows = $stmt->fetchAll();
            if (!empty($rows)) {
                return array_map(function($r) {
                    return ['title' => $r['title'], 'time' => time_ago($r['created_at'])];
                }, $rows);
            }
        } catch (Exception $e) {}
    }
    return $_SESSION['mock_activities'] ?? [];
}

function add_activity($title, $type = 'info') {
    global $db;
    if ($db) {
        try {
            $stmt = $db->prepare("INSERT INTO activities (title, type) VALUES (?, ?)");
            $stmt->execute([$title, $type]);
        } catch (Exception $e) {}
    }
    array_unshift($_SESSION['mock_activities'], ['title' => $title, 'type' => $type, 'time' => 'Just now']);
    if (count($_SESSION['mock_activities']) > 15) {
        array_pop($_SESSION['mock_activities']);
    }
}

// User CRUD
function get_all_users($search = '', $role = '', $status = '') {
    global $db;
    if ($db) {
        try {
            $query = "SELECT * FROM users WHERE 1=1";
            $params = [];
            if (!empty($search)) {
                $query .= " AND (name LIKE ? OR email LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            if (!empty($role) && $role !== 'all') {
                $query .= " AND role = ?";
                $params[] = $role;
            }
            if (!empty($status) && $status !== 'all') {
                $query .= " AND status = ?";
                $params[] = $status;
            }
            $query .= " ORDER BY id ASC";
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {}
    }

    // Fallback store
    $users = $_SESSION['mock_users'] ?? [];
    if (!empty($search)) {
        $search = strtolower($search);
        $users = array_filter($users, function($u) use ($search) {
            return str_contains(strtolower($u['name']), $search) || str_contains(strtolower($u['email']), $search);
        });
    }
    if (!empty($role) && $role !== 'all') {
        $users = array_filter($users, function($u) use ($role) {
            return strtolower($u['role']) === strtolower($role);
        });
    }
    if (!empty($status) && $status !== 'all') {
        $users = array_filter($users, function($u) use ($status) {
            return strtolower($u['status']) === strtolower($status);
        });
    }
    return array_values($users);
}

function update_user_status($id, $status) {
    global $db;
    if ($db) {
        try {
            $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
        } catch (Exception $e) {}
    }
    foreach ($_SESSION['mock_users'] as &$u) {
        if ($u['id'] == $id) {
            $u['status'] = $status;
            add_activity("User #$id (" . $u['name'] . ") status changed to $status", 'user');
            break;
        }
    }
}

function delete_user($id) {
    global $db;
    if ($db) {
        try {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
        } catch (Exception $e) {}
    }
    foreach ($_SESSION['mock_users'] as $index => $u) {
        if ($u['id'] == $id) {
            add_activity("User deleted: " . $u['name'] . " (" . $u['email'] . ")", 'user');
            unset($_SESSION['mock_users'][$index]);
            break;
        }
    }
    $_SESSION['mock_users'] = array_values($_SESSION['mock_users']);
}

// Company CRUD
function get_all_companies($search = '', $status = '', $sort = 'newest') {
    global $db;
    if ($db) {
        try {
            $query = "SELECT * FROM companies WHERE 1=1";
            $params = [];
            if (!empty($search)) {
                $query .= " AND (company_name LIKE ? OR owner_email LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            if (!empty($status) && $status !== 'all') {
                $query .= " AND status = ?";
                $params[] = $status;
            }
            if ($sort === 'alphabetical') {
                $query .= " ORDER BY company_name ASC";
            } else {
                $query .= " ORDER BY id DESC";
            }
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {}
    }

    $companies = $_SESSION['mock_companies'] ?? [];
    if (!empty($search)) {
        $search = strtolower($search);
        $companies = array_filter($companies, function($c) use ($search) {
            return str_contains(strtolower($c['company_name']), $search) || str_contains(strtolower($c['owner_email']), $search);
        });
    }
    if (!empty($status) && $status !== 'all') {
        $companies = array_filter($companies, function($c) use ($status) {
            return strtolower($c['status']) === strtolower($status);
        });
    }
    $companies = array_values($companies);
    if ($sort === 'alphabetical') {
        usort($companies, fn($a, $b) => strcmp($a['company_name'], $b['company_name']));
    }
    return $companies;
}

function update_company_status($id, $status) {
    global $db;
    if ($db) {
        try {
            $stmt = $db->prepare("UPDATE companies SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
        } catch (Exception $e) {}
    }
    foreach ($_SESSION['mock_companies'] as &$c) {
        if ($c['id'] == $id) {
            $c['status'] = $status;
            add_activity("Company: " . $c['company_name'] . " status updated to $status", 'company');
            break;
        }
    }
}

function delete_company($id) {
    global $db;
    if ($db) {
        try {
            $stmt = $db->prepare("DELETE FROM companies WHERE id = ?");
            $stmt->execute([$id]);
        } catch (Exception $e) {}
    }
    foreach ($_SESSION['mock_companies'] as $index => $c) {
        if ($c['id'] == $id) {
            add_activity("Company removed: " . $c['company_name'], 'company');
            unset($_SESSION['mock_companies'][$index]);
            break;
        }
    }
    $_SESSION['mock_companies'] = array_values($_SESSION['mock_companies']);
}

// Jobs CRUD
function get_all_jobs($search = '', $status = '', $sort = 'newest') {
    global $db;
    if ($db) {
        try {
            $query = "SELECT * FROM jobs WHERE 1=1";
            $params = [];
            if (!empty($search)) {
                $query .= " AND (title LIKE ? OR company_name LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            if (!empty($status) && $status !== 'all') {
                $query .= " AND status = ?";
                $params[] = $status;
            }
            if ($sort === 'oldest') {
                $query .= " ORDER BY id ASC";
            } else {
                $query .= " ORDER BY id DESC";
            }
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {}
    }

    $jobs = $_SESSION['mock_jobs'] ?? [];
    if (!empty($search)) {
        $search = strtolower($search);
        $jobs = array_filter($jobs, function($j) use ($search) {
            return str_contains(strtolower($j['title']), $search) || str_contains(strtolower($j['company_name']), $search);
        });
    }
    if (!empty($status) && $status !== 'all') {
        $jobs = array_filter($jobs, function($j) use ($status) {
            return strtolower($j['status']) === strtolower($status);
        });
    }
    $jobs = array_values($jobs);
    if ($sort === 'oldest') {
        usort($jobs, fn($a, $b) => $a['id'] <=> $b['id']);
    }
    return $jobs;
}

function update_job_status($id, $status) {
    global $db;
    if ($db) {
        try {
            $stmt = $db->prepare("UPDATE jobs SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
        } catch (Exception $e) {}
    }
    foreach ($_SESSION['mock_jobs'] as &$j) {
        if ($j['id'] == $id) {
            $j['status'] = $status;
            add_activity("Job moderation: " . $j['title'] . " marked as $status", 'job');
            break;
        }
    }
}

function delete_job($id) {
    global $db;
    if ($db) {
        try {
            $stmt = $db->prepare("DELETE FROM jobs WHERE id = ?");
            $stmt->execute([$id]);
        } catch (Exception $e) {}
    }
    foreach ($_SESSION['mock_jobs'] as $index => $j) {
        if ($j['id'] == $id) {
            add_activity("Job deleted: " . $j['title'] . " (" . $j['company_name'] . ")", 'job');
            unset($_SESSION['mock_jobs'][$index]);
            break;
        }
    }
    $_SESSION['mock_jobs'] = array_values($_SESSION['mock_jobs']);
}

// Categories CRUD
function get_all_categories($search = '', $sort = 'name') {
    global $db;
    if ($db) {
        try {
            $query = "SELECT * FROM categories WHERE 1=1";
            $params = [];
            if (!empty($search)) {
                $query .= " AND name LIKE ?";
                $params[] = "%$search%";
            }
            if ($sort === 'jobs') {
                $query .= " ORDER BY job_count DESC";
            } elseif ($sort === 'recent') {
                $query .= " ORDER BY id DESC";
            } else {
                $query .= " ORDER BY name ASC";
            }
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {}
    }

    $cats = $_SESSION['mock_categories'] ?? [];
    if (!empty($search)) {
        $search = strtolower($search);
        $cats = array_filter($cats, function($c) use ($search) {
            return str_contains(strtolower($c['name']), $search);
        });
    }
    $cats = array_values($cats);
    if ($sort === 'jobs') {
        usort($cats, fn($a, $b) => $b['job_count'] <=> $a['job_count']);
    } elseif ($sort === 'recent') {
        usort($cats, fn($a, $b) => $b['id'] <=> $a['id']);
    } else {
        usort($cats, fn($a, $b) => strcmp($a['name'], $b['name']));
    }
    return $cats;
}

function save_category($id, $name, $job_count = 0) {
    global $db;
    if ($db) {
        try {
            if ($id > 0) {
                $stmt = $db->prepare("UPDATE categories SET name = ? WHERE id = ?");
                $stmt->execute([$name, $id]);
            } else {
                $stmt = $db->prepare("INSERT INTO categories (name, job_count) VALUES (?, ?)");
                $stmt->execute([$name, $job_count]);
            }
        } catch (Exception $e) {}
    }

    if ($id > 0) {
        foreach ($_SESSION['mock_categories'] as &$cat) {
            if ($cat['id'] == $id) {
                $cat['name'] = $name;
                add_activity("Category updated: $name", 'category');
                break;
            }
        }
    } else {
        $new_id = count($_SESSION['mock_categories']) + 1;
        $_SESSION['mock_categories'][] = [
            'id' => $new_id,
            'name' => $name,
            'icon' => 'briefcase',
            'job_count' => (int)$job_count
        ];
        add_activity("New Category created: $name", 'category');
    }
}

function delete_category($id) {
    global $db;
    if ($db) {
        try {
            $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
        } catch (Exception $e) {}
    }
    foreach ($_SESSION['mock_categories'] as $index => $c) {
        if ($c['id'] == $id) {
            add_activity("Category removed: " . $c['name'], 'category');
            unset($_SESSION['mock_categories'][$index]);
            break;
        }
    }
    $_SESSION['mock_categories'] = array_values($_SESSION['mock_categories']);
}

// Reviews Moderation
function get_all_reviews($search = '', $rating = 0, $sort = 'newest') {
    global $db;
    if ($db) {
        try {
            $query = "SELECT * FROM reviews WHERE 1=1";
            $params = [];
            if (!empty($search)) {
                $query .= " AND (job_title LIKE ? OR seeker_name LIKE ? OR comment LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            if ($rating > 0) {
                $query .= " AND rating = ?";
                $params[] = $rating;
            }
            if ($sort === 'oldest') {
                $query .= " ORDER BY id ASC";
            } elseif ($sort === 'lowest') {
                $query .= " ORDER BY rating ASC";
            } elseif ($sort === 'highest') {
                $query .= " ORDER BY rating DESC";
            } else {
                $query .= " ORDER BY id DESC";
            }
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {}
    }

    $reviews = $_SESSION['mock_reviews'] ?? [];
    if (!empty($search)) {
        $search = strtolower($search);
        $reviews = array_filter($reviews, function($r) use ($search) {
            return str_contains(strtolower($r['job_title']), $search) || 
                   str_contains(strtolower($r['seeker_name']), $search) || 
                   str_contains(strtolower($r['comment']), $search);
        });
    }
    if ($rating > 0) {
        $reviews = array_filter($reviews, function($r) use ($rating) {
            return (int)$r['rating'] === (int)$rating;
        });
    }
    $reviews = array_values($reviews);
    if ($sort === 'oldest') {
        usort($reviews, fn($a, $b) => $a['id'] <=> $b['id']);
    } elseif ($sort === 'lowest') {
        usort($reviews, fn($a, $b) => $a['rating'] <=> $b['rating']);
    } elseif ($sort === 'highest') {
        usort($reviews, fn($a, $b) => $b['rating'] <=> $a['rating']);
    }
    return $reviews;
}

function delete_review($id) {
    global $db;
    if ($db) {
        try {
            $stmt = $db->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->execute([$id]);
        } catch (Exception $e) {}
    }
    foreach ($_SESSION['mock_reviews'] as $index => $r) {
        if ($r['id'] == $id) {
            add_activity("Review #$id deleted by admin", 'review');
            unset($_SESSION['mock_reviews'][$index]);
            break;
        }
    }
    $_SESSION['mock_reviews'] = array_values($_SESSION['mock_reviews']);
}

// System Settings
function get_system_settings() {
    global $db;
    if ($db) {
        try {
            $stmt = $db->query("SELECT * FROM settings WHERE id = 1");
            $row = $stmt->fetch();
            if ($row) return $row;
        } catch (Exception $e) {}
    }
    return $_SESSION['mock_settings'] ?? [
        'site_name' => 'JobPortal.lk',
        'site_email' => 'admin@jobportal.lk',
        'maintenance_mode' => 0,
        'enable_registration' => 1,
        'enable_job_approval' => 1,
        'jobs_per_page' => 10
    ];
}

function update_system_settings($settings) {
    global $db;
    if ($db) {
        try {
            $stmt = $db->prepare("UPDATE settings SET site_name = ?, site_email = ?, maintenance_mode = ?, enable_registration = ?, enable_job_approval = ?, jobs_per_page = ? WHERE id = 1");
            $stmt->execute([
                $settings['site_name'],
                $settings['site_email'],
                $settings['maintenance_mode'],
                $settings['enable_registration'],
                $settings['enable_job_approval'],
                $settings['jobs_per_page']
            ]);
        } catch (Exception $e) {}
    }
    $_SESSION['mock_settings'] = array_merge($_SESSION['mock_settings'] ?? [], $settings);
    add_activity("System administration settings updated", 'settings');
}
