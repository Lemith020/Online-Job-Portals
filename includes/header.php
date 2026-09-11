<?php
/**
 * JobPortal.lk - Master Unified Header
 * Compatible with Company, Seeker, Admin, and Public views.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$app_name = defined('APP_NAME') ? APP_NAME : 'JobPortal.lk';
$page_title_display = isset($page_title) ? $page_title . " | " . $app_name : $app_name;

// Detect active user info dynamically
$user_role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 'guest';
$user_display_name = $_SESSION['user']['name'] ?? $_SESSION['company_name'] ?? $_SESSION['first_name'] ?? $_SESSION['user_name'] ?? '';
$user_email = $_SESSION['user']['email'] ?? $_SESSION['email'] ?? '';

if (empty($user_display_name)) {
    if ($user_role === 'admin') $user_display_name = 'Administrator';
    elseif ($user_role === 'company') $user_display_name = 'Company Employer';
    elseif ($user_role === 'job_seeker' || $user_role === 'seeker') $user_display_name = 'Job Candidate';
    else $user_display_name = 'Guest';
}

$role_badge = 'User';
$dashboard_url = BASE_URL . '/index.php';
$settings_url = BASE_URL . '/index.php';

if ($user_role === 'admin') {
    $role_badge = 'Admin';
    $dashboard_url = BASE_URL . '/admin/dashboard.php';
    $settings_url = BASE_URL . '/admin/settings.php';
} elseif ($user_role === 'company') {
    $role_badge = 'Employer';
    $dashboard_url = BASE_URL . '/company/dashboard.php';
    $settings_url = BASE_URL . '/company/settings.php';
} elseif ($user_role === 'job_seeker' || $user_role === 'seeker') {
    $role_badge = 'Candidate';
    $dashboard_url = BASE_URL . '/seeker/dashboard.php';
    $settings_url = BASE_URL . '/seeker/settings.php';
}

// Smart Page CSS resolution
$resolved_page_css = '';
if (isset($page_css) && !empty($page_css)) {
    if (strpos($page_css, 'http://') === 0 || strpos($page_css, 'https://') === 0) {
        $resolved_page_css = $page_css;
    } elseif (strpos($page_css, '../assets/css/') === 0) {
        $resolved_page_css = BASE_URL . '/assets/css/' . substr($page_css, 14);
    } elseif (strpos($page_css, 'assets/css/') === 0) {
        $resolved_page_css = BASE_URL . '/' . $page_css;
    } elseif (strpos($page_css, '/') === 0) {
        $resolved_page_css = BASE_URL . $page_css;
    } else {
        $resolved_page_css = BASE_URL . '/assets/css/' . $page_css;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title_display); ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Master Theme Stylesheet -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">

  <?php if (!empty($resolved_page_css)) : ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($resolved_page_css); ?>">
  <?php endif; ?>
</head>
<body>

  <!-- Top Dark Navigation Bar -->
  <header class="topnav">
    <div class="topnav-left">
      <button class="hamburger-btn" id="hamburgerBtn" title="Toggle Navigation">
        <i class="fa-solid fa-bars"></i>
      </button>
      <a href="<?php echo $dashboard_url; ?>" class="brand-title">
        <i class="fa-solid fa-briefcase"></i>
        <span>JobPortal<span>.lk</span></span>
      </a>
    </div>
    
    <div class="topnav-right">
      <div class="profile-menu" id="profileMenu">
        <div class="profile-btn" id="profileBtn">
          <i class="fa-solid fa-circle-user"></i>
          <span><?php echo htmlspecialchars($user_display_name); ?></span>
          <i class="fa-solid fa-chevron-down profile-chevron"></i>
        </div>
        <div class="profile-dropdown">
          <div class="profile-dropdown-header">
            <i class="fa-solid fa-circle-user"></i>
            <div>
              <div class="profile-name"><?php echo htmlspecialchars($user_display_name); ?></div>
              <?php if (!empty($user_email)): ?>
                <div class="profile-sub"><?php echo htmlspecialchars($user_email); ?></div>
              <?php endif; ?>
              <span class="profile-role-badge"><?php echo htmlspecialchars($role_badge); ?></span>
            </div>
          </div>
          <div class="profile-dropdown-footer">
            <a href="<?php echo $settings_url; ?>"><i class="fa-solid fa-gear"></i> Settings</a>
            <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Global Layout Wrapper Start -->
  <div class="layout admin-layout portal-layout">