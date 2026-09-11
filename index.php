<?php
/**
 * JobPortal.lk - Main Portal Entry Point & Router
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? 'guest';
$user_name = $_SESSION['user_name'] ?? ($_SESSION['first_name'] ?? '');

// Fetch Categories
$categories = function_exists('get_all_categories_admin') ? get_all_categories_admin() : [];

// Search Filter Logic
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (!empty($search_query) && isset($conn)) {
    $search_safe = mysqli_real_escape_string($conn, $search_query);
    $jobs_sql = "SELECT j.*, c.company_name 
                 FROM jobs j
                 LEFT JOIN company c ON j.company_id = c.company_id
                 WHERE (j.title LIKE '%$search_safe%' OR c.company_name LIKE '%$search_safe%' OR j.description LIKE '%$search_safe%')
                 ORDER BY j.job_id DESC";
    $jobs_res = mysqli_query($conn, $jobs_sql);
    $featured_jobs = [];
    if ($jobs_res) {
        while ($row = mysqli_fetch_assoc($jobs_res)) {
            $featured_jobs[] = $row;
        }
    }
} else {
    $featured_jobs = function_exists('get_all_jobs_admin') ? get_all_jobs_admin('Approved') : [];
    if (empty($featured_jobs) && isset($conn)) {
        $jobs_res = mysqli_query($conn, "SELECT j.*, c.company_name FROM jobs j LEFT JOIN company c ON j.company_id = c.company_id ORDER BY j.job_id DESC LIMIT 10");
        if ($jobs_res) {
            while ($row = mysqli_fetch_assoc($jobs_res)) {
                $featured_jobs[] = $row;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JobPortal.lk | Sri Lanka's Premier Job Network</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
  
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background-color: #f8fafc;
      font-family: 'Plus Jakarta Sans', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    
    /* Header */
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

    /* Hero */
    .hero-section {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: #ffffff;
      padding: 60px 20px;
      text-align: center;
    }
    .hero-title {
      font-size: 38px;
      font-weight: 800;
      margin-bottom: 12px;
      color: #ffffff;
    }
    .hero-subtitle {
      font-size: 16px;
      color: #94a3b8;
      max-width: 600px;
      margin: 0 auto 28px;
      line-height: 1.5;
    }
    .hero-search-box {
      background: #ffffff;
      padding: 6px;
      border-radius: 10px;
      max-width: 680px;
      margin: 0 auto;
      display: flex;
      gap: 8px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }
    .hero-search-box input {
      flex: 1;
      border: none;
      padding: 12px 16px;
      font-size: 15px;
      outline: none;
      color: #1e293b;
    }

    /* Layout Containers */
    .site-wrapper {
      flex: 1;
    }
    .main-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }
    
    /* Category Cards Grid */
    .categories-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 16px;
      margin-bottom: 40px;
    }
    .category-card {
      background: #ffffff;
      padding: 20px;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      text-align: center;
      transition: all 0.2s ease;
      box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .category-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.05);
      border-color: #3b82f6;
    }
    
    /* Featured Jobs Card Grid Styles */
    .jobs-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
      gap: 20px;
    }
    .job-card-item {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 22px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: all 0.2s ease-in-out;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
      position: relative;
    }
    .job-card-item:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
      border-color: #2563eb;
    }
    .job-card-header {
      display: flex;
      align-items: flex-start;
      gap: 14px;
      margin-bottom: 14px;
    }
    .company-icon-avatar {
      width: 46px;
      height: 46px;
      border-radius: 10px;
      background: #f1f5f9;
      color: #2563eb;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      font-weight: 800;
      flex-shrink: 0;
      border: 1px solid #e2e8f0;
    }
    .job-title-text {
      font-size: 16px;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 2px;
      line-height: 1.3;
    }
    .company-name-text {
      font-size: 13px;
      color: #64748b;
      font-weight: 500;
    }
    .job-meta-list {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-bottom: 16px;
      font-size: 13px;
      color: #64748b;
    }
    .job-meta-item {
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .job-card-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-top: 14px;
      border-top: 1px dashed #e2e8f0;
      margin-top: auto;
    }
    .salary-text {
      font-size: 14px;
      font-weight: 700;
      color: #059669;
    }

    /* Footer Fix */
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
  </style>
</head>
<body>

  <!-- Header -->
  <header class="public-navbar">
    <a href="<?php echo BASE_URL; ?>/index.php" style="text-decoration: none; display: flex; align-items: center; gap: 10px;">
      <div style="background: #2563eb; color: white; padding: 8px 12px; border-radius: 8px;"><i class="fa-solid fa-briefcase"></i></div>
      <span style="color: #0f172a; font-size: 20px; font-weight: 800;">
        JobPortal<span style="color: #2563eb;">.lk</span>
      </span>
    </a>

    <nav class="public-nav-links" style="display: flex; align-items: center; gap: 24px;">
      <a href="<?php echo BASE_URL; ?>/index.php" style="color: #0f172a; font-weight: 600; font-size: 14px;">Home</a>
      <a href="<?php echo BASE_URL; ?>/seeker/browse-jobs.php" style="color: #64748b; font-weight: 500; font-size: 14px;">Jobs</a>
      <a href="#categories" style="color: #64748b; font-weight: 500; font-size: 14px;">Categories</a>
      <a href="#about" style="color: #64748b; font-weight: 500; font-size: 14px;">About Us</a>
      <a href="#contact" style="color: #64748b; font-weight: 500; font-size: 14px;">Contact</a>
    </nav>

    <div style="display: flex; align-items: center; gap: 12px;">
      <?php if ($role === 'guest'): ?>
        <a href="<?php echo BASE_URL; ?>/auth/login.php" class="btn btn-outline btn-sm" style="padding: 8px 18px; border-radius: 8px; font-weight: 600;">
          Login
        </a>
        <a href="<?php echo BASE_URL; ?>/auth/register.php" class="btn btn-primary btn-sm" style="padding: 8px 20px; border-radius: 8px; font-weight: 600;">
          Register
        </a>
      <?php else: ?>
        <span style="font-size: 14px; color: #64748b;">Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong></span>
        <?php if ($role === 'admin'): ?>
          <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="btn btn-primary btn-sm" style="padding: 8px 16px;">
            <i class="fa-solid fa-gauge-high"></i> Admin Dashboard
          </a>
        <?php elseif ($role === 'company'): ?>
          <a href="<?php echo BASE_URL; ?>/company/dashboard.php" class="btn btn-primary btn-sm" style="padding: 8px 16px;">
            <i class="fa-solid fa-building"></i> Employer Portal
          </a>
        <?php else: ?>
          <a href="<?php echo BASE_URL; ?>/seeker/dashboard.php" class="btn btn-primary btn-sm" style="padding: 8px 16px;">
            <i class="fa-solid fa-user"></i> Candidate Portal
          </a>
        <?php endif; ?>
        <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="btn btn-outline btn-sm" style="padding: 8px 16px;">Logout</a>
      <?php endif; ?>
    </div>
  </header>

  <?php if (function_exists('display_flash')) : ?>
    <div style="max-width: 1200px; margin: 16px auto 0; padding: 0 20px; width: 100%;">
      <?php display_flash(); ?>
    </div>
  <?php endif; ?>

  <div class="site-wrapper">
    <!-- Hero Section -->
    <section class="hero-section">
      <div style="max-width: 1200px; margin: 0 auto;">
        <h1 class="hero-title">Discover Sri Lanka's Top Career Opportunities</h1>
        <p class="hero-subtitle">Connect with verified companies and start the next milestone in your professional journey.</p>
        
        <!-- Working Search Form -->
        <form method="GET" action="index.php" class="hero-search-box">
          <input type="text" name="q" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Job title, keywords, or skills (e.g. React Developer, Accountant)..." required>
          <button type="submit" style="padding: 12px 24px; font-size: 15px; font-weight: 700; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer;">
            <i class="fa-solid fa-magnifying-glass"></i> Search Jobs
          </button>
        </form>
      </div>
    </section>

    <!-- Content Sections -->
    <div class="main-container">

      <!-- Categories Section Cards -->
      <?php if (!empty($categories)) : ?>
      <div id="categories" style="margin-bottom: 24px; text-align: center;">
        <h2 style="font-size: 24px; color: #0f172a; margin-bottom: 6px;">Explore Popular Job Categories</h2>
        <p style="color: #64748b; font-size: 14px;">Find roles tailored to your specialization</p>
      </div>

      <div class="categories-grid">
        <?php foreach (array_slice($categories, 0, 8) as $cat): ?>
          <div class="category-card">
            <div style="font-size: 26px; color: #2563eb; margin-bottom: 10px;">
              <i class="fa-solid fa-<?php echo htmlspecialchars($cat['icon'] ?? 'briefcase'); ?>"></i>
            </div>
            <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 4px;"><?php echo htmlspecialchars($cat['name'] ?? $cat['category_name'] ?? ''); ?></h3>
            <span style="font-size: 13px; color: #64748b;"><?php echo $cat['job_count'] ?? 0; ?> Open Positions</span>
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Featured Jobs Grid Cards (Replaced Table with Modern Cards) -->
      <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h2 style="margin: 0; font-size: 24px; color: #0f172a; font-weight: 800;">
            <i class="fa-solid fa-fire" style="color: #2563eb; margin-right: 6px;"></i> 
            <?php echo !empty($search_query) ? 'Search Results for "' . htmlspecialchars($search_query) . '"' : 'Featured Jobs'; ?>
          </h2>
          <p style="color: #64748b; font-size: 14px; margin-top: 4px;">Explore latest verified career opportunities</p>
        </div>
        <?php if (!empty($search_query)) : ?>
          <a href="index.php" style="font-size: 14px; color: #ef4444; text-decoration: none; font-weight: 600;"><i class="fa-solid fa-xmark"></i> Clear Search</a>
        <?php endif; ?>
      </div>

      <div class="jobs-grid">
        <?php if (!empty($featured_jobs)) : ?>
          <?php foreach ($featured_jobs as $fj): ?>
            <div class="job-card-item">
              <div>
                <div class="job-card-header">
                  <div class="company-icon-avatar">
                    <i class="fa-solid fa-building"></i>
                  </div>
                  <div>
                    <h3 class="job-title-text"><?php echo htmlspecialchars($fj['title'] ?? ''); ?></h3>
                    <span class="company-name-text"><?php echo htmlspecialchars($fj['company_name'] ?? 'Company'); ?></span>
                  </div>
                </div>

                <div class="job-meta-list">
                  <div class="job-meta-item">
                    <i class="fa-solid fa-location-dot" style="color: #3b82f6;"></i>
                    <span><?php echo htmlspecialchars($fj['location'] ?? 'Sri Lanka'); ?></span>
                  </div>
                  <div class="job-meta-item">
                    <span style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                      <?php echo htmlspecialchars($fj['job_type'] ?? 'Full-time'); ?>
                    </span>
                  </div>
                </div>
              </div>

              <div class="job-card-footer">
                <span class="salary-text"><?php echo htmlspecialchars($fj['salary_range'] ?? $fj['salary'] ?? 'Negotiable'); ?></span>
                <a href="<?php echo BASE_URL; ?>/auth/login.php" style="padding: 8px 18px; background: #2563eb; color: white; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                  Apply Now <i class="fa-solid fa-arrow-right" style="font-size: 11px;"></i>
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else : ?>
          <div style="grid-column: 1 / -1; background: white; padding: 40px; text-align: center; border-radius: 12px; border: 1px solid #e2e8f0; color: #64748b;">
            <i class="fa-solid fa-briefcase" style="font-size: 32px; color: #94a3b8; margin-bottom: 12px; display: block;"></i>
            No job openings found matching your criteria.
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <!-- Footer -->
  <footer class="main-footer">
    <div class="footer-content">
      <div style="font-size: 18px; font-weight: 800; color: white;">
        JobPortal<span style="color: #38bdf8;">.lk</span>
      </div>
      <div style="font-size: 14px;">
        &copy; <?php echo date('Y'); ?> <strong>JobPortal.lk</strong>. All rights reserved.
      </div>
    </div>
  </footer>

</body>
</html>