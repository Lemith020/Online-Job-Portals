<?php
/**
 * JobPortal.lk - Master Admin Sidebar Navigation
 */
$current_script = basename($_SERVER['PHP_SELF']);
$GLOBALS['layout_main_open'] = true;
?>

<!-- Master Admin Sidebar -->
<aside class="sidebar admin-sidebar" id="sidebar">
  <ul class="sidebar-menu sidebar-nav">
    <li class="<?php echo ($current_script === 'dashboard.php') ? 'active' : ''; ?>">
      <a href="<?php echo BASE_URL; ?>/admin/dashboard.php">
        <i class="fa-solid fa-house"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <li class="<?php echo ($current_script === 'users.php') ? 'active' : ''; ?>">
      <a href="<?php echo BASE_URL; ?>/admin/users.php">
        <i class="fa-solid fa-users"></i>
        <span>User Management</span>
      </a>
    </li>
    <li class="<?php echo ($current_script === 'jobs.php') ? 'active' : ''; ?>">
      <a href="<?php echo BASE_URL; ?>/admin/jobs.php">
        <i class="fa-solid fa-briefcase"></i>
        <span>Job Postings</span>
      </a>
    </li>
    <li class="<?php echo ($current_script === 'categories.php') ? 'active' : ''; ?>">
      <a href="<?php echo BASE_URL; ?>/admin/categories.php">
        <i class="fa-solid fa-tags"></i>
        <span>Categories</span>
      </a>
    </li>
    <li class="<?php echo ($current_script === 'subscriptions.php') ? 'active' : ''; ?>">
      <a href="<?php echo BASE_URL; ?>/admin/subscriptions.php">
        <i class="fa-solid fa-credit-card"></i>
        <span>Subscriptions</span>
      </a>
    </li>
    <li class="<?php echo ($current_script === 'settings.php') ? 'active' : ''; ?>">
      <a href="<?php echo BASE_URL; ?>/admin/settings.php">
        <i class="fa-solid fa-gear"></i>
        <span>Settings</span>
      </a>
    </li>
    <li class="sidebar-divider"></li>
    <li>
      <a href="<?php echo BASE_URL; ?>/index.php" target="_blank">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
        <span>View Public Site</span>
      </a>
    </li>
    <li>
      <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="logout-item">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Logout</span>
      </a>
    </li>
  </ul>
</aside>

<!-- Main Admin Content Body Wrapper Start -->
<main class="main-content admin-main">
  <?php if (function_exists('display_flash')) display_flash(); ?>