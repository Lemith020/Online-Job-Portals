<?php
/**
 * JobPortal.lk - Admin Navigation (Sidebar & Topbar)
 * Member 1 - Admin & Core/Shared
 */

$current_page = basename($_SERVER['PHP_SELF']);
$admin_user = $_SESSION['user'] ?? [
    'name' => 'Admin Kamal',
    'email' => 'admin@jobportal.lk',
    'role' => 'admin'
];
?>

<!-- Admin Fixed Sidebar Navigation -->
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-header">
    <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="brand-logo">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
      </svg>
      <span>JobPortal.lk</span>
    </a>
    <span class="brand-badge">Admin</span>
  </div>

  <nav class="sidebar-nav">
    <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="nav-item <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="7" height="7"></rect>
        <rect x="14" y="3" width="7" height="7"></rect>
        <rect x="14" y="14" width="7" height="7"></rect>
        <rect x="3" y="14" width="7" height="7"></rect>
      </svg>
      <span>Dashboard</span>
    </a>

    <a href="<?php echo BASE_URL; ?>/admin/users.php" class="nav-item <?php echo ($current_page === 'users.php') ? 'active' : ''; ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
      </svg>
      <span>Users</span>
    </a>

    <a href="<?php echo BASE_URL; ?>/admin/companies.php" class="nav-item <?php echo ($current_page === 'companies.php') ? 'active' : ''; ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
        <line x1="9" y1="22" x2="9" y2="22.01"></line>
        <line x1="15" y1="22" x2="15" y2="22.01"></line>
        <line x1="8" y1="6" x2="8" y2="6.01"></line>
        <line x1="16" y1="6" x2="16" y2="6.01"></line>
        <line x1="8" y1="10" x2="8" y2="10.01"></line>
        <line x1="16" y1="10" x2="16" y2="10.01"></line>
        <line x1="8" y1="14" x2="8" y2="14.01"></line>
        <line x1="16" y1="14" x2="16" y2="14.01"></line>
        <line x1="8" y1="18" x2="8" y2="18.01"></line>
        <line x1="16" y1="18" x2="16" y2="18.01"></line>
      </svg>
      <span>Companies</span>
    </a>

    <a href="<?php echo BASE_URL; ?>/admin/jobs.php" class="nav-item <?php echo ($current_page === 'jobs.php') ? 'active' : ''; ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
      </svg>
      <span>Jobs</span>
    </a>

    <a href="<?php echo BASE_URL; ?>/admin/categories.php" class="nav-item <?php echo ($current_page === 'categories.php') ? 'active' : ''; ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="8" y1="6" x2="21" y2="6"></line>
        <line x1="8" y1="12" x2="21" y2="12"></line>
        <line x1="8" y1="18" x2="21" y2="18"></line>
        <line x1="3" y1="6" x2="3.01" y2="6"></line>
        <line x1="3" y1="12" x2="3.01" y2="12"></line>
        <line x1="3" y1="18" x2="3.01" y2="18"></line>
      </svg>
      <span>Categories</span>
    </a>

    <a href="<?php echo BASE_URL; ?>/admin/reviews.php" class="nav-item <?php echo ($current_page === 'reviews.php') ? 'active' : ''; ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
      </svg>
      <span>Reviews</span>
    </a>

    <a href="<?php echo BASE_URL; ?>/admin/settings.php" class="nav-item <?php echo ($current_page === 'settings.php') ? 'active' : ''; ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="3"></circle>
        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
      </svg>
      <span>Settings</span>
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="sidebar-user-card">
      <div class="user-avatar-circle">
        <?php echo strtoupper(substr($admin_user['name'], 0, 1)); ?>
      </div>
      <div class="user-info-text">
        <div class="user-info-name"><?php echo htmlspecialchars($admin_user['name']); ?></div>
        <div class="user-info-role"><?php echo htmlspecialchars($admin_user['email']); ?></div>
      </div>
    </div>
  </div>
</aside>

<!-- Admin Main Container Wrapper -->
<div class="admin-main">
  <!-- Top Navigation Header -->
  <header class="admin-topbar">
    <div class="topbar-left">
      <button type="button" class="mobile-menu-btn" id="mobileMenuToggle" aria-label="Toggle Menu">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>

      <div class="topbar-search">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" placeholder="Search anything in JobPortal..." data-table-search="mainDataTable">
      </div>
    </div>

    <div class="topbar-right">
      <a href="<?php echo BASE_URL; ?>/index.php" target="_blank" class="topbar-link-pill">
        View Public Site ↗
      </a>

      <div class="user-profile-dropdown">
        <div class="profile-trigger" onclick="location.href='<?php echo BASE_URL; ?>/admin/settings.php'">
          <div class="user-avatar-circle" style="width:28px; height:28px; font-size:12px;">
            <?php echo strtoupper(substr($admin_user['name'], 0, 1)); ?>
          </div>
          <span class="profile-trigger-name"><?php echo htmlspecialchars($admin_user['name']); ?></span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </div>
      </div>

      <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="btn btn-secondary btn-sm" title="Log Out">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
          <polyline points="16 17 21 12 16 7"></polyline>
          <line x1="21" y1="12" x2="9" y2="12"></line>
        </svg>
        <span>Logout</span>
      </a>
    </div>
  </header>

  <!-- Open page content container -->
  <main class="admin-content">
