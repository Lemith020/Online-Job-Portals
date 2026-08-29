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
    $sql = "SELECT seeker_id FROM Job_Seekers WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row ? $row['seeker_id'] : null;
}

// Count total jobs the seeker has applied for
function get_total_applications($conn, $seeker_id) {
    $sql = "SELECT COUNT(*) AS total FROM Applications WHERE seeker_id = ?";
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
            FROM Interviews i
            JOIN Applications a ON i.app_id = a.app_id
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
    $sql = "SELECT is_active, end_date
            FROM User_Subscriptions
            WHERE user_id = ?
            ORDER BY end_date DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        return ["status" => "Inactive", "end_date" => "N/A"];
    }
    return [
        "status"   => $row['is_active'] ? "Active" : "Inactive",
        "end_date" => date("d/m/Y", strtotime($row['end_date']))
    ];
}

// Simple profile completion % based on filled fields + CV upload
function get_profile_completion($conn, $seeker_id) {
    $sql = "SELECT bio, phone, birth_day FROM Job_Seekers WHERE seeker_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $seeker_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $profile = mysqli_fetch_assoc($result);

    // Check if a CV has been uploaded
    $sql2 = "SELECT COUNT(*) AS total FROM CVs WHERE seeker_id = ?";
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
            FROM Applications a
            JOIN Jobs j ON a.job_id = j.job_id
            JOIN Company c ON j.company_id = c.company_id
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

// TODO: add more helpers as the app grows
?>
