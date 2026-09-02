<?php
/**
 * JobPortal.lk - Admin Sidebar & Top Navigation Bar
 */

$current_script = basename($_SERVER['PHP_SELF']);
$admin_name = $_SESSION['user']['name'] ?? 'Admin Kamal';
$admin_email = $_SESSION['user']['email'] ?? 'admin@jobportal.lk';
?>

<!-- Sidebar Navigation -->
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-header">
    <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="brand-logo">
      <div class="brand-icon">
        <i class="fa-solid fa-briefcase"></i>
      </div>
      <div class="brand-text">
        <span class="brand-title">JobPortal<span class="brand-accent">.lk</span></span>
        <span class="brand-badge">ADMIN</span>
      </div>
    </a>
    <button class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close sidebar">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>

  <div class="sidebar-menu-wrapper">
    <div class="menu-section-label">MAIN NAVIGATION</div>
    <ul class="sidebar-nav">
      <li class="nav-item">
        <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="nav-link <?php echo ($current_script === 'dashboard.php') ? 'active' : ''; ?>">
          <i class="fa-solid fa-gauge-high nav-icon"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo BASE_URL; ?>/admin/users.php" class="nav-link <?php echo ($current_script === 'users.php') ? 'active' : ''; ?>">
          <i class="fa-solid fa-users nav-icon"></i>
          <span>User Management</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo BASE_URL; ?>/admin/job-seekers.php" class="nav-link <?php echo ($current_script === 'job-seekers.php') ? 'active' : ''; ?>">
          <i class="fa-solid fa-user-graduate nav-icon"></i>
          <span>Job Seekers</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo BASE_URL; ?>/admin/companies.php" class="nav-link <?php echo ($current_script === 'companies.php') ? 'active' : ''; ?>">
          <i class="fa-solid fa-building nav-icon"></i>
          <span>Companies</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo BASE_URL; ?>/admin/jobs.php" class="nav-link <?php echo ($current_script === 'jobs.php') ? 'active' : ''; ?>">
          <i class="fa-solid fa-briefcase nav-icon"></i>
          <span>Job Postings</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo BASE_URL; ?>/admin/categories.php" class="nav-link <?php echo ($current_script === 'categories.php') ? 'active' : ''; ?>">
          <i class="fa-solid fa-tags nav-icon"></i>
          <span>Categories</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo BASE_URL; ?>/admin/reviews.php" class="nav-link <?php echo ($current_script === 'reviews.php') ? 'active' : ''; ?>">
          <i class="fa-solid fa-star-half-stroke nav-icon"></i>
          <span>Reviews & Ratings</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo BASE_URL; ?>/admin/subscriptions.php" class="nav-link <?php echo ($current_script === 'subscriptions.php') ? 'active' : ''; ?>">
          <i class="fa-solid fa-credit-card nav-icon"></i>
          <span>Subscriptions</span>
        </a>
      </li>
    </ul>

    <div class="menu-section-label">SYSTEM</div>
    <ul class="sidebar-nav">
      <li class="nav-item">
        <a href="<?php echo BASE_URL; ?>/admin/settings.php" class="nav-link <?php echo ($current_script === 'settings.php') ? 'active' : ''; ?>">
          <i class="fa-solid fa-gear nav-icon"></i>
          <span>Settings</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo BASE_URL; ?>/index.php" target="_blank" class="nav-link">
          <i class="fa-solid fa-arrow-up-right-from-square nav-icon"></i>
          <span>View Public Site</span>
        </a>
      </li>
    </ul>
  </div>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar">
        <span><?php echo strtoupper(substr($admin_name, 0, 1)); ?></span>
      </div>
      <div class="user-meta">
        <span class="user-name"><?php echo htmlspecialchars($admin_name); ?></span>
        <span class="user-role">Super Administrator</span>
      </div>
      <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="logout-btn" title="Logout">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
      </a>
    </div>
  </div>
</aside>

<!-- Main Admin Content Area -->
<div class="admin-main">
  <!-- Top Navigation Bar -->
  <header class="admin-topbar">
    <div class="topbar-left">
      <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle Sidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
      <div class="topbar-search">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" id="globalSearchInput" placeholder="Quick search jobs, companies, users...">
      </div>
    </div>

    <div class="topbar-right">
      <div class="system-status-indicator">
        <span class="status-dot"></span>
        <span class="status-text">Production Online</span>
      </div>

      <div class="topbar-profile" id="topbarProfile">
        <button class="profile-trigger" id="profileTrigger">
          <div class="profile-avatar-sm">
            <span><?php echo strtoupper(substr($admin_name, 0, 1)); ?></span>
          </div>
          <span class="profile-name"><?php echo htmlspecialchars($admin_name); ?></span>
          <i class="fa-solid fa-chevron-down profile-arrow"></i>
        </button>
        <div class="profile-menu-dropdown" id="profileDropdown">
          <div class="dropdown-header">
            <strong><?php echo htmlspecialchars($admin_name); ?></strong>
            <small><?php echo htmlspecialchars($admin_email); ?></small>
          </div>
          <a href="<?php echo BASE_URL; ?>/admin/settings.php" class="dropdown-item">
            <i class="fa-solid fa-sliders"></i> Account Settings
          </a>
          <div class="dropdown-divider"></div>
          <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="dropdown-item text-danger">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Page Main Content Body -->
  <main class="admin-content-body">
    <?php display_flash(); ?>
