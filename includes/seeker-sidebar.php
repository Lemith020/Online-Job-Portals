<!-- includes/seeker-sidebar.php -->
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <ul>
        <li class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <a href="/Online-Job-Portals/seeker/index.php">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
        </li>
        <li><a href="/Online-Job-Portals/seeker/jobs/browse.php">
            <i class="fa-solid fa-briefcase"></i> Browse Jobs
        </a></li>
        <li><a href="/Online-Job-Portals/seeker/applications/index.php">
            <i class="fa-solid fa-file-lines"></i> Applications
        </a></li>
        <li><a href="/Online-Job-Portals/seeker/interviews/index.php">
            <i class="fa-solid fa-calendar-days"></i> Interviews
        </a></li>
        <li><a href="/Online-Job-Portals/seeker/profile/index.php">
            <i class="fa-solid fa-user"></i> My Profile
        </a></li>
        <li><a href="/Online-Job-Portals/seeker/cv/index.php">
            <i class="fa-solid fa-file-invoice"></i> My CV
        </a></li>
        <li><a href="/Online-Job-Portals/seeker/alerts/index.php">
            <i class="fa-solid fa-bell"></i> Job Alerts
        </a></li>
        <li><a href="/Online-Job-Portals/seeker/settings.php">
            <i class="fa-solid fa-gear"></i> Settings
        </a></li>
    </ul>
</aside>