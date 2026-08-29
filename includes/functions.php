<?php
// -----------------------------------------------------------------
// functions.php
// Purpose : Small reusable helper functions used across the site
// -----------------------------------------------------------------

function clean($data) {
    return htmlspecialchars(trim($data));
}

function formatDate($date) {
    return date("d M Y", strtotime($date));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

// Get seeker_id from the logged-in user_id
function get_seeker_id($conn, $user_id) {
    $sql = "SELECT seeker_id FROM job_seekers WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row ? $row['seeker_id'] : null;
}

// Count total jobs the seeker has applied for
function get_total_applications($conn, $seeker_id) {
    $sql = "SELECT COUNT(*) AS total FROM applications WHERE seeker_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $seeker_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

// Count interviews that are still "Scheduled"
function get_pending_interviews($conn, $seeker_id) {
    $sql = "SELECT COUNT(*) AS total
            FROM interviews i
            JOIN applications a ON i.app_id = a.app_id
            WHERE a.seeker_id = ? AND i.status = 'Scheduled'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $seeker_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

// Get the seeker's current subscription (CV/account) status
function get_subscription_status($conn, $user_id) {
    $sql = "SELECT sp.plan_name, us.is_active, us.start_date, us.end_date
            FROM user_subscriptions us
            JOIN subscription_plans sp ON us.plan_id = sp.plan_id
            WHERE us.user_id = ?
            ORDER BY us.end_date DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        return ["status" => "Inactive", "plan_name" => null, "start_date" => null, "end_date" => "N/A"];
    }
    $is_active = $row['is_active'] && strtotime($row['end_date']) >= time();
    return [
        "status"     => $is_active ? "Active" : "Inactive",
        "plan_name"  => $row['plan_name'],
        "start_date" => $row['start_date'],
        "end_date"   => date("d/m/Y", strtotime($row['end_date']))
    ];
}

// Simple profile completion % based on filled fields + CV upload
function get_profile_completion($conn, $seeker_id) {
    $sql = "SELECT bio, phone, birth_day FROM job_seekers WHERE seeker_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $seeker_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $profile = mysqli_fetch_assoc($result);

    // Check if a CV has been uploaded
    $sql2 = "SELECT COUNT(*) AS total FROM cvs WHERE seeker_id = ?";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "i", $seeker_id);
    mysqli_stmt_execute($stmt2);
    $cvResult = mysqli_stmt_get_result($stmt2);
    $cvRow = mysqli_fetch_assoc($cvResult);
    $has_cv = $cvRow['total'] > 0;

    // 4 fields we consider = 25% each
    $filled = 0;
    if (!empty($profile['bio'])) $filled++;
    if (!empty($profile['phone'])) $filled++;
    if (!empty($profile['birth_day'])) $filled++;
    if ($has_cv) $filled++;

    return round(($filled / 4) * 100);
}

// Get the seeker's most recent job applications (joined with Jobs + Company)
function get_recent_applications($conn, $seeker_id, $limit = 4) {
    $sql = "SELECT j.title, c.company_name, a.apply_date, a.status
            FROM applications a
            JOIN jobs j ON a.job_id = j.job_id
            JOIN company c ON j.company_id = c.company_id
            WHERE a.seeker_id = ?
            ORDER BY a.apply_date DESC
            LIMIT ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $seeker_id, $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $applications = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $applications[] = $row;
    }
    return $applications;
}

// -----------------------------------------------------------------
// Added below: helpers needed by browse-jobs / applications /
// interviews / profile / my-cv / job-alerts / settings pages.
// Same mysqli style as above - nothing above this line was changed.
// -----------------------------------------------------------------

// Turn a status string into a CSS badge class
function status_badge_class($status) {
    $map = [
        'pending'   => 'badge-pending',
        'reviewed'  => 'badge-reviewed',
        'accepted'  => 'badge-accepted',
        'rejected'  => 'badge-rejected',
        'Scheduled' => 'badge-scheduled',
        'Completed' => 'badge-completed',
        'Cancelled' => 'badge-cancelled',
    ];
    return $map[$status] ?? 'badge-default';
}

// ---- Categories ----
function get_categories($conn) {
    $sql = "SELECT category_id, category_name FROM categories ORDER BY category_name";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function get_seeker_categories($conn, $seeker_id) {
    $sql = "SELECT category_id FROM seeker_category WHERE seeker_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $seeker_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $ids = [];
    while ($row = mysqli_fetch_assoc($result)) $ids[] = $row['category_id'];
    return $ids;
}

function save_seeker_categories($conn, $seeker_id, $category_ids) {
    $del = mysqli_prepare($conn, "DELETE FROM seeker_category WHERE seeker_id = ?");
    mysqli_stmt_bind_param($del, "i", $seeker_id);
    mysqli_stmt_execute($del);

    $ins = mysqli_prepare($conn, "INSERT INTO seeker_category (seeker_id, category_id) VALUES (?, ?)");
    foreach ($category_ids as $cat_id) {
        $cat_id = (int)$cat_id;
        mysqli_stmt_bind_param($ins, "ii", $seeker_id, $cat_id);
        mysqli_stmt_execute($ins);
    }
}

// ---- Browse Jobs ----
// $filters = ['keyword' => '', 'location' => '', 'category' => '', 'job_type' => '', 'salary_min' => '', 'salary_max' => '']
function get_jobs($conn, $filters, $limit, $offset) {
    [$where_sql, $types, $params] = build_job_filters($filters);

    $sql = "SELECT j.job_id, j.title, j.location, j.salary_min, j.salary_max, j.job_type, j.posted_date, c.company_name
            FROM jobs j
            JOIN company c ON j.company_id = c.company_id
            WHERE $where_sql
            ORDER BY j.posted_date DESC
            LIMIT ? OFFSET ?";
    $types .= "ii";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $jobs = [];
    while ($row = mysqli_fetch_assoc($result)) $jobs[] = $row;
    return $jobs;
}

function get_jobs_count($conn, $filters) {
    [$where_sql, $types, $params] = build_job_filters($filters);
    $sql = "SELECT COUNT(*) AS total FROM jobs j WHERE $where_sql";
    $stmt = mysqli_prepare($conn, $sql);
    if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result)['total'];
}

// Shared WHERE-clause builder used by get_jobs() and get_jobs_count()
function build_job_filters($filters) {
    $where = ["j.status = 'approved'", "j.expiry_date >= CURDATE()"];
    $types = "";
    $params = [];

    if (!empty($filters['keyword'])) {
        $where[] = "(j.title LIKE ? OR j.description LIKE ?)";
        $types .= "ss";
        $params[] = "%{$filters['keyword']}%";
        $params[] = "%{$filters['keyword']}%";
    }
    if (!empty($filters['location'])) {
        $where[] = "j.location LIKE ?";
        $types .= "s";
        $params[] = "%{$filters['location']}%";
    }
    if (!empty($filters['category'])) {
        $where[] = "j.category_id = ?";
        $types .= "i";
        $params[] = (int)$filters['category'];
    }
    if (!empty($filters['job_type'])) {
        $where[] = "j.job_type = ?";
        $types .= "s";
        $params[] = $filters['job_type'];
    }
    if (!empty($filters['salary_min']) && !empty($filters['salary_max'])) {
        $where[] = "j.salary_min >= ? AND j.salary_max <= ?";
        $types .= "ii";
        $params[] = (int)$filters['salary_min'];
        $params[] = (int)$filters['salary_max'];
    }
    return [implode(' AND ', $where), $types, $params];
}

function get_job_by_id($conn, $job_id) {
    $sql = "SELECT j.title, c.company_name
            FROM jobs j JOIN company c ON j.company_id = c.company_id
            WHERE j.job_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $job_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// ---- CVs / Applying ----
function get_seeker_cvs($conn, $seeker_id) {
    $sql = "SELECT cv_id, file_path, uploaded_at FROM cvs WHERE seeker_id = ? ORDER BY uploaded_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $seeker_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function insert_application($conn, $seeker_id, $job_id, $cv_id, $experience) {
    $sql = "INSERT INTO applications (seeker_id, job_id, cv_id, apply_date, status, experience)
            VALUES (?, ?, ?, CURDATE(), 'pending', ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiis", $seeker_id, $job_id, $cv_id, $experience);
    return mysqli_stmt_execute($stmt);
}

function insert_cv($conn, $seeker_id, $file_path) {
    $sql = "INSERT INTO cvs (seeker_id, file_path, uploaded_at) VALUES (?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $seeker_id, $file_path);
    return mysqli_stmt_execute($stmt);
}

function set_default_cv($conn, $cv_id, $seeker_id) {
    $sql = "UPDATE cvs SET uploaded_at = NOW() WHERE cv_id = ? AND seeker_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $cv_id, $seeker_id);
    return mysqli_stmt_execute($stmt);
}

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

// ---- Subscription plans ----
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

// ---- Applications page ----
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

// ---- Interviews page ----
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

// ---- Profile page ----
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

// ---- Job Alerts page ----
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

// ---- Settings page ----
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

// TODO: add more helpers as the app grows
?>
