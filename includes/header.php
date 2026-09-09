<?php
/**
 * JobPortal.lk - Standard Header
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

$app_name = defined('APP_NAME') ? APP_NAME : 'JobPortal.lk';
$page_title_display = isset($page_title) ? $page_title . " | " . $app_name : $app_name . " - Portal";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title_display); ?></title>
  
  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  

<!-- Core Application CSS Path Fix -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/company_page_css/style.css">

  <!-- Page Dynamic CSS -->
  <?php if (isset($page_css) && !empty($page_css)) : ?>
  <link rel="stylesheet" href="<?php echo $page_css; ?>">
  <?php endif; ?>
</head>
<body>

  <!-- Fixed Navigation Top Bar -->
  <header class="topnav">
    <div class="topnav-left">
      <button class="hamburger-btn" id="sidebarToggle" title="Toggle Navigation">
        <i class="fa-solid fa-bars"></i>
      </button>
      <div style="display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-briefcase"></i>
        <span>JobPortal<span style="color: var(--primary);">.lk</span></span>
      </div>
    </div>
    
    <div class="profile-menu" id="profileMenu">
      <div class="profile-btn" onclick="document.getElementById('profileMenu').classList.toggle('open')">
        <i class="fa-solid fa-circle-user"></i>
        <span><?php echo htmlspecialchars($_SESSION['user']['name'] ?? $_SESSION['name'] ?? 'Company'); ?></span>
        <i class="fa-solid fa-chevron-down profile-chevron"></i>
      </div>
      <div class="profile-dropdown">
        <div class="profile-dropdown-header">
          <i class="fa-solid fa-building"></i>
          <div>
            <div class="profile-name"><?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'Company Portal'); ?></div>
            <div class="profile-sub">Employer Account</div>
          </div>
        </div>
        <div class="profile-dropdown-footer">
          <a href="<?php echo BASE_URL; ?>/company/profile.php"><i class="fa-solid fa-user-gear"></i> Account Profile</a>
          <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
      </div>
    </div>
  </header>

  <!-- Layout Wrapper Start -->
  <div class="layout">

  <!-- Sidebar Toggle Script (Matches your CSS .toggled class) -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const toggleBtn = document.getElementById('sidebarToggle');
      const sidebar = document.querySelector('.sidebar');
      const mainContent = document.querySelector('.main-content');

      if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
          sidebar.classList.toggle('toggled');
          if (mainContent) {
            if (sidebar.classList.contains('toggled')) {
              mainContent.style.marginLeft = '0';
            } else {
              mainContent.style.marginLeft = '240px';
            }
          }
        });
      }
    });
  </script>