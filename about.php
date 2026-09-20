<?php
/**
 * JobPortal.lk - About Us
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$sys_settings = function_exists('get_system_settings') ? get_system_settings() : [];
$site_name = !empty($sys_settings['site_name']) ? $sys_settings['site_name'] : 'JobPortal.lk';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us | <?php echo htmlspecialchars($site_name); ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background-color: #f8fafc;
      font-family: 'Plus Jakarta Sans', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .public-navbar {
      background: #ffffff;
      border-bottom: 1px solid #e2e8f0;
      padding: 16px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    .page-hero {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: #ffffff;
      padding: 50px 20px;
      text-align: center;
    }
    .page-hero h1 { font-size: 32px; font-weight: 800; margin-bottom: 8px; }
    .page-hero p {
      color: #94a3b8;
      font-size: 15px;
      max-width: 600px;
      margin: 0 auto;
      line-height: 1.5;
    }
    .site-wrapper { flex: 1; }
    .main-container {
      max-width: 1000px;
      margin: 40px auto;
      padding: 0 20px;
    }
    .back-home-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      font-weight: 700;
      color: #2563eb;
      text-decoration: none;
      margin-bottom: 24px;
    }
    .back-home-link:hover { text-decoration: underline; }
    .intro-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 28px;
      margin-bottom: 28px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .intro-card p { color: #334155; font-size: 15px; line-height: 1.7; }
    .section-title {
      font-size: 22px;
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 20px;
      text-align: center;
    }
    .roles-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }
    .role-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 24px;
      transition: all 0.2s ease;
      box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .role-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.06);
      border-color: #2563eb;
    }
    .role-icon {
      width: 46px;
      height: 46px;
      border-radius: 10px;
      background: #f1f5f9;
      color: #2563eb;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      margin-bottom: 14px;
      border: 1px solid #e2e8f0;
    }
    .role-card h3 { font-size: 17px; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
    .role-card ul { list-style: none; display: flex; flex-direction: column; gap: 8px; }
    .role-card li { font-size: 13.5px; color: #475569; display: flex; align-items: flex-start; gap: 8px; }
    .role-card li i { color: #2563eb; font-size: 12px; margin-top: 3px; }
    .main-footer {
      background: #0f172a;
      color: #94a3b8;
      padding: 28px 20px;
      margin-top: auto;
      border-top: 1px solid #1e293b;
    }
    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 15px;
    }

        /* Nav Buttons */
    .nav-btn {
      padding: 8px 18px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 14px;
      text-decoration: none;
      color: #64748b;
      transition: all 0.2s ease;
    }
    .nav-btn:hover {
      background: #f1f5f9;
      color: #0f172a;
    }
    .nav-btn.active {
      background: #2563eb;
      color: #ffffff;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header class="public-navbar">
    <a href="<?php echo BASE_URL; ?>/index.php" style="text-decoration: none; display: flex; align-items: center; gap: 10px;">
      <div style="background: #2563eb; color: white; padding: 8px 12px; border-radius: 8px;"><i class="fa-solid fa-briefcase"></i></div>
      <span style="color: #0f172a; font-size: 20px; font-weight: 800;"><?php echo htmlspecialchars($site_name); ?></span>
    </a>

<nav style="display: flex; align-items: center; gap: 8px;">
  <a href="<?php echo BASE_URL; ?>/index.php" class="nav-btn">Home</a>
  <a href="<?php echo BASE_URL; ?>/index.php#featured-jobs" class="nav-btn">Jobs</a>
  <a href="<?php echo BASE_URL; ?>/about.php" class="nav-btn active">About Us</a>
  <a href="<?php echo BASE_URL; ?>/contact.php" class="nav-btn">Contact</a>
</nav>

    <div style="display: flex; align-items: center; gap: 12px;">
      <a href="<?php echo BASE_URL; ?>/auth/login.php" style="padding: 8px 18px; border-radius: 8px; font-weight: 600; border: 1px solid #e2e8f0; color: #0f172a; text-decoration: none; font-size: 14px;">Login</a>
      <a href="<?php echo BASE_URL; ?>/auth/register.php" style="padding: 8px 20px; border-radius: 8px; font-weight: 600; background: #2563eb; color: white; text-decoration: none; font-size: 14px;">Register</a>
    </div>
  </header>

  <div class="site-wrapper">
    <section class="page-hero">
      <h1>About <?php echo htmlspecialchars($site_name); ?></h1>
      <p>Connecting job seekers, companies, and opportunities across Sri Lanka on one trusted platform.</p>
    </section>

    <div class="main-container">
      <a href="<?php echo BASE_URL; ?>/index.php" class="back-home-link"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>

      <div class="intro-card">
        <p>
          <?php echo htmlspecialchars($site_name); ?> is a job portal built to make hiring and job searching simple
          for everyone in Sri Lanka. Our platform brings together three key groups — job seekers, companies, and
          administrators — each with the tools they need to get things done efficiently.
        </p>
      </div>

      <h2 class="section-title">How Our Platform Works</h2>
      <div class="roles-grid">
        <div class="role-card">
          <div class="role-icon"><i class="fa-solid fa-user"></i></div>
          <h3>Job Seekers</h3>
          <ul>
            <li><i class="fa-solid fa-check"></i> Browse and apply for jobs</li>
            <li><i class="fa-solid fa-check"></i> Get job alerts based on preferences</li>
            <li><i class="fa-solid fa-check"></i> Join interviews scheduled by companies</li>
            <li><i class="fa-solid fa-check"></i> Track applications and their status</li>
          </ul>
        </div>
        <div class="role-card">
          <div class="role-icon"><i class="fa-solid fa-building"></i></div>
          <h3>Companies</h3>
          <ul>
            <li><i class="fa-solid fa-check"></i> Post jobs under relevant categories</li>
            <li><i class="fa-solid fa-check"></i> Review applications from seekers</li>
            <li><i class="fa-solid fa-check"></i> Approve, reject, or mark applications pending</li>
            <li><i class="fa-solid fa-check"></i> Schedule or cancel interviews</li>
          </ul>
        </div>
        <div class="role-card">
          <div class="role-icon"><i class="fa-solid fa-gauge-high"></i></div>
          <h3>Administrators</h3>
          <ul>
            <li><i class="fa-solid fa-check"></i> Approve job posts before they go live</li>
            <li><i class="fa-solid fa-check"></i> Manage job categories</li>
            <li><i class="fa-solid fa-check"></i> Add and manage subscription plans</li>
            <li><i class="fa-solid fa-check"></i> Suspend users when necessary</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer">
    <div class="footer-content">
      <div style="font-size: 18px; font-weight: 800; color: white;"><?php echo htmlspecialchars($site_name); ?></div>
      <div style="font-size: 14px;">&copy; <?php echo date('Y'); ?> <strong><?php echo htmlspecialchars($site_name); ?></strong>. All rights reserved.</div>
    </div>
  </footer>

</body>
</html>
