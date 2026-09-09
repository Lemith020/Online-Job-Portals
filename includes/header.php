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
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">

  <?php if (isset($page_css) && !empty($page_css)) : ?>
  <link rel="stylesheet" href="<?php echo $page_css; ?>">
  <?php endif; ?>

  <!-- Direct Fix for Layout, Sidebar & Toggle Animation -->
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #1e293b; }
    
    /* Topbar Layout */
    .topnav {
      height: 60px;
      background: #0f172a;
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 1000;
    }
    .topnav-left { font-size: 20px; font-weight: 800; display: flex; align-items: center; gap: 16px; }
    .topnav-left i.fa-briefcase { color: #2563eb; }

    /* Hamburger Toggle Button */
    .hamburger-btn {
      background: rgba(255, 255, 255, 0.1);
      border: none;
      color: #ffffff;
      font-size: 18px;
      padding: 8px 12px;
      border-radius: 8px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s;
    }
    .hamburger-btn:hover { background: rgba(255, 255, 255, 0.2); }

    .profile-btn { background: rgba(255,255,255,0.1); padding: 6px 14px; border-radius: 8px; font-size: 14px; display: flex; align-items: center; gap: 8px; }

    /* Layout & Sidebar Wrapper */
    .layout { display: flex; margin-top: 60px; min-height: calc(100vh - 60px); }
    
    .sidebar {
      width: 250px;
      background: #ffffff;
      border-right: 1px solid #e2e8f0;
      position: fixed;
      top: 60px; bottom: 0; left: 0;
      padding: 20px 12px;
      overflow-y: auto;
      z-index: 900;
      transition: transform 0.3s ease;
    }

    /* Collapsed State for Sidebar */
    .sidebar.collapsed {
      transform: translateX(-250px);
    }

    .sidebar-menu { list-style: none; padding: 0; margin: 0; }
    .sidebar-menu li a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      border-radius: 8px;
      color: #64748b;
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 4px;
      transition: all 0.2s;
    }
    .sidebar-menu li a:hover, .sidebar-menu li.active a {
      background: #2563eb;
      color: #ffffff;
    }
    .sidebar-menu li a:hover i, .sidebar-menu li.active a i { color: #ffffff; }
    .sidebar-menu li a i { width: 20px; text-align: center; color: #64748b; }
    .sidebar-divider { height: 1px; background: #e2e8f0; margin: 12px 4px; }
    .logout-item { color: #dc2626 !important; }
    .logout-item i { color: #dc2626 !important; }

    /* Main Content Shift & Expand */
    .main-content {
      margin-left: 250px;
      flex: 1;
      padding: 30px;
      width: calc(100% - 250px);
      transition: margin-left 0.3s ease, width 0.3s ease;
    }

    .main-content.expanded {
      margin-left: 0;
      width: 100%;
    }
  </style>
</head>
<body>

  <!-- Topbar -->
  <header class="topnav">
    <div class="topnav-left">
      <!-- Hamburger Toggle Button -->
      <button class="hamburger-btn" id="sidebarToggle" title="Toggle Sidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
      <div style="display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-briefcase"></i>
        <span>JobPortal<span style="color: #2563eb;">.lk</span></span>
      </div>
    </div>
    <div class="profile-menu">
      <div class="profile-btn">
        <i class="fa-solid fa-circle-user" style="color: #14b8a6;"></i>
        <span><?php echo htmlspecialchars($_SESSION['user']['name'] ?? $_SESSION['name'] ?? 'virtusa'); ?></span>
      </div>
    </div>
  </header>

  <!-- Layout Start -->
  <div class="layout">

  <!-- Sidebar Toggle Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const toggleBtn = document.getElementById('sidebarToggle');
      const sidebar = document.getElementById('sidebar');
      const mainContent = document.querySelector('.main-content');

      if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
          sidebar.classList.toggle('collapsed');
          if (mainContent) {
            mainContent.classList.toggle('expanded');
          }
        });
      }
    });
  </script>