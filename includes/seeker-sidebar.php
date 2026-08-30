<?php
// includes/seeker-sidebar.php
// Include this right after includes/header.php, inside the <div class="app-layout">.

$current = basename($_SERVER['PHP_SELF']);

function nav_active($file, $current) {
    return $file === $current ? 'active' : '';
}
?>
<aside class="sidebar">
    <ul class="sidebar-menu">
        <li class="<?= nav_active('dashboard.php', $current) ?>">
            <a href="dashboard.php">📊 Dashboard</a>
        </li>
        <li class="<?= nav_active('browse-jobs.php', $current) ?>">
            <a href="browse-jobs.php">💼 Browse Jobs</a>
        </li>
        <li class="<?= nav_active('applications.php', $current) ?>">
            <a href="applications.php">✅ Applications</a>
        </li>
        <li class="<?= nav_active('interviews.php', $current) ?>">
            <a href="interviews.php">📅 Interviews</a>
        </li>
        <li class="<?= nav_active('profile.php', $current) ?>">
            <a href="profile.php">👤 My Profile</a>
        </li>
        <li class="<?= nav_active('my-cv.php', $current) ?>">
            <a href="my-cv.php">📄 My CV</a>
        </li>
        <li class="<?= nav_active('job-alerts.php', $current) ?>">
            <a href="job-alerts.php">🔔 Job Alerts</a>
        </li>
        <li class="<?= nav_active('settings.php', $current) ?>">
            <a href="settings.php">⚙️ Settings</a>
        </li>
    </ul>
</aside>

<main class="main-content">
