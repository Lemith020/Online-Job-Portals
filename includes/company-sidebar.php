<?php $active = isset($active_page) ? $active_page : ''; ?>
<aside class="sidebar" id="sidebar">
    <ul class="sidebar-menu">
        <li class="<?php echo $active == 'dashboard' ? 'active' : ''; ?>">
            <a href="dashboard.php"><i class="fa-solid fa-house"></i> <span>Dashboard</span></a>
        </li>
        <li class="<?php echo $active == 'jobs' ? 'active' : ''; ?>">
            <a href="jobs.php"><i class="fa-solid fa-briefcase"></i> <span>Manage Jobs</span></a>
        </li>
        <li class="<?php echo $active == 'applicants' ? 'active' : ''; ?>">
            <a href="applicants.php"><i class="fa-solid fa-users"></i> <span>Applicants</span></a>
        </li>
        <li class="<?php echo $active == 'interviews' ? 'active' : ''; ?>">
            <a href="interviews.php"><i class="fa-solid fa-calendar-check"></i> <span>Interviews</span></a>
        </li>
        <li class="<?php echo $active == 'profile' ? 'active' : ''; ?>">
            <a href="profile.php"><i class="fa-solid fa-building"></i> <span>Company Profile</span></a>
        </li>
        <li class="<?php echo $active == 'categories' ? 'active' : ''; ?>">
            <a href="categories.php"><i class="fa-solid fa-list"></i> <span>Categories</span></a>
        </li>
        <li class="<?php echo $active == 'settings' ? 'active' : ''; ?>">
            <a href="settings.php"><i class="fa-solid fa-gear"></i> <span>Settings</span></a>
        </li>
        <li class="sidebar-divider"></li>
        <li>
            <a href="logout.php" class="logout-item"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
        </li>
    </ul>
</aside>