<?php
// includes/seeker-sidebar.php
$current = basename($_SERVER['PHP_SELF']);

if (!function_exists('nav_active')) {
    function nav_active($file, $current) {
        return $file === $current ? 'active' : '';
    }
}
$GLOBALS['layout_main_open'] = true;
?>
<aside class="sidebar" id="sidebar">
    <ul class="sidebar-menu">
        <li class="<?= nav_active('dashboard.php', $current) ?>">
            <a href="<?php echo BASE_URL; ?>/seeker/dashboard.php"><i class="fa-solid fa-house"></i> <span>Dashboard</span></a>
        </li>
        <li class="<?= nav_active('browse-jobs.php', $current) ?>">
            <a href="<?php echo BASE_URL; ?>/seeker/browse-jobs.php"><i class="fa-solid fa-briefcase"></i> <span>Browse Jobs</span></a>
        </li>
        <li class="<?= nav_active('applications.php', $current) ?>">
            <a href="<?php echo BASE_URL; ?>/seeker/applications.php"><i class="fa-solid fa-file-circle-check"></i> <span>Applications</span></a>
        </li>
        <li class="<?= nav_active('interviews.php', $current) ?>">
            <a href="<?php echo BASE_URL; ?>/seeker/interviews.php"><i class="fa-solid fa-calendar-check"></i> <span>Interviews</span></a>
        </li>
        <li class="<?= nav_active('profile.php', $current) ?>">
            <a href="<?php echo BASE_URL; ?>/seeker/profile.php"><i class="fa-solid fa-user"></i> <span>My Profile</span></a>
        </li>
        <li class="<?= nav_active('my-cv.php', $current) ?>">
            <a href="<?php echo BASE_URL; ?>/seeker/my-cv.php"><i class="fa-solid fa-file-lines"></i> <span>My CV</span></a>
        </li>
        <li class="<?= nav_active('job-alerts.php', $current) ?>">
            <a href="<?php echo BASE_URL; ?>/seeker/job-alerts.php"><i class="fa-solid fa-bell"></i> <span>Job Alerts</span></a>
        </li>
        <li class="<?= nav_active('settings.php', $current) ?>">
            <a href="<?php echo BASE_URL; ?>/seeker/settings.php"><i class="fa-solid fa-gear"></i> <span>Settings</span></a>
        </li>
        <li class="sidebar-divider"></li>
        <li>
            <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="logout-item"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
        </li>
    </ul>
</aside>

<main class="main-content">