<?php
// includes/functions.php
// Small helper functions reused across the seeker pages.
// Include this AFTER config/database.php and auth.php.

// Get the seeker_id row that belongs to the logged-in user_id.
// job_seekers table links seeker_id -> user_id.
function get_seeker_id($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT seeker_id FROM job_seekers WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch();
    return $row ? $row['seeker_id'] : null;
}

// Turn a status string into a Bootstrap-ish color class for badges.
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

// Simple date formatter: 2024-05-20 -> 20/05/2024
function format_date($date) {
    if (!$date) return '-';
    return date("d/m/Y", strtotime($date));
}

// Escape output safely
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
