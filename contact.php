<?php
/**
 * JobPortal.lk - Contact Us
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$sys_settings = function_exists('get_system_settings') ? get_system_settings() : [];
$site_name = !empty($sys_settings['site_name']) ? $sys_settings['site_name'] : 'JobPortal.lk';

// Contact details - edit these as needed
$contact_name  = 'Thamosha Dilhara';
$contact_email = 'thamoshadilhara@gmail.com';
$contact_phone = '076 274 6851';
$contact_social = 'https://www.linkedin.com/in/thamosha-dilhara-851988320/recent-activity/all/'; // mock link - replace with the real one
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us | <?php echo htmlspecialchars($site_name); ?></title>

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
      max-width: 640px;
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
    .contact-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .contact-row {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 18px 16px;
      border-bottom: 1px dashed #e2e8f0;
      text-decoration: none;
      transition: background 0.15s ease;
    }
    .contact-row:last-child { border-bottom: none; }
    .contact-row:hover { background: #f8fafc; }
    .contact-icon {
      width: 44px;
      height: 44px;
      border-radius: 10px;
      background: #f1f5f9;
      color: #2563eb;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      flex-shrink: 0;
      border: 1px solid #e2e8f0;
    }
    .contact-label {
      font-size: 12px;
      color: #64748b;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      margin-bottom: 3px;
    }
    .contact-value { font-size: 15px; color: #0f172a; font-weight: 700; }
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
  <a href="<?php echo BASE_URL; ?>/about.php" class="nav-btn">About Us</a>
  <a href="<?php echo BASE_URL; ?>/contact.php" class="nav-btn active">Contact</a>
</nav>

    <div style="display: flex; align-items: center; gap: 12px;">
      <a href="<?php echo BASE_URL; ?>/auth/login.php" style="padding: 8px 18px; border-radius: 8px; font-weight: 600; border: 1px solid #e2e8f0; color: #0f172a; text-decoration: none; font-size: 14px;">Login</a>
      <a href="<?php echo BASE_URL; ?>/auth/register.php" style="padding: 8px 20px; border-radius: 8px; font-weight: 600; background: #2563eb; color: white; text-decoration: none; font-size: 14px;">Register</a>
    </div>
  </header>

  <div class="site-wrapper">
    <section class="page-hero">
      <h1>Contact Us</h1>
      <p>Have a question or need help? Reach out to us directly.</p>
    </section>

    <div class="main-container">
      <a href="<?php echo BASE_URL; ?>/index.php" class="back-home-link"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>

      <div class="contact-card">
        <div class="contact-row">
          <div class="contact-icon"><i class="fa-solid fa-user"></i></div>
          <div>
            <div class="contact-label">Full Name</div>
            <div class="contact-value"><?php echo htmlspecialchars($contact_name); ?></div>
          </div>
        </div>

        <a href="mailto:<?php echo htmlspecialchars($contact_email); ?>" class="contact-row">
          <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
          <div>
            <div class="contact-label">Email</div>
            <div class="contact-value"><?php echo htmlspecialchars($contact_email); ?></div>
          </div>
        </a>

        <a href="tel:<?php echo htmlspecialchars(str_replace(' ', '', $contact_phone)); ?>" class="contact-row">
          <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
          <div>
            <div class="contact-label">Phone Number</div>
            <div class="contact-value"><?php echo htmlspecialchars($contact_phone); ?></div>
          </div>
        </a>

        <a href="<?php echo htmlspecialchars($contact_social); ?>" target="_blank" rel="noopener" class="contact-row">
          <div class="contact-icon"><i class="fa-brands fa-linkedin"></i></div>
          <div>
            <div class="contact-label">Social Media</div>
            <div class="contact-value">Connect on LinkedIn</div>
          </div>
        </a>
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
