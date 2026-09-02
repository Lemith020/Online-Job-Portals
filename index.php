<?php
/**
 * JobPortal.lk - Main Portal Entry Point & Router
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$role = $_SESSION['user']['role'] ?? 'guest';
$user_name = $_SESSION['user']['name'] ?? '';
$categories = get_all_categories_admin();
$featured_jobs = get_all_jobs_admin('Approved');
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
    .hero-section {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: #ffffff;
      padding: 80px 24px;
      text-align: center;
      position: relative;
    }
    .hero-title {
      font-size: 44px;
      font-weight: 800;
      letter-spacing: -1px;
      margin-bottom: 16px;
      color: #ffffff;
    }
    .hero-subtitle {
      font-size: 18px;
      color: #94a3b8;
      max-width: 650px;
      margin: 0 auto 36px;
      line-height: 1.6;
    }
    .hero-search-box {
      background: #ffffff;
      padding: 10px;
      border-radius: 12px;
      max-width: 760px;
      margin: 0 auto;
      display: flex;
      gap: 10px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
    }
    .hero-search-box input {
      flex: 1;
      border: none;
      padding: 12px 16px;
      font-size: 15px;
      outline: none;
      color: #1e293b;
    }
    .public-navbar {
      background: #ffffff;
      border-bottom: 1px solid var(--border-color);
      padding: 16px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
  </style>
</head>
<body style="background-color: var(--bg-main);">

  <!-- Public Header Navigation -->
  <header class="public-navbar">
    <a href="<?php echo BASE_URL; ?>/index.php" class="brand-logo">
      <div class="brand-icon"><i class="fa-solid fa-briefcase"></i></div>
      <span class="brand-text" style="color:var(--text-heading); font-size:20px; font-weight:800;">
        JobPortal<span style="color:var(--primary);">.lk</span>
      </span>
    </a>

    <div class="flex-center gap-3">
      <?php if ($role === 'guest'): ?>
        <a href="<?php echo BASE_URL; ?>/auth/login.php" class="btn btn-outline">
          <i class="fa-solid fa-arrow-right-to-bracket"></i> Sign In
        </a>
        <a href="<?php echo BASE_URL; ?>/auth/register.php" class="btn btn-primary">
          <i class="fa-solid fa-user-plus"></i> Register
        </a>
      <?php else: ?>
        <span class="text-muted" style="font-size:14px;">Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong></span>
        <?php if ($role === 'admin'): ?>
          <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="btn btn-primary">
            <i class="fa-solid fa-gauge-high"></i> Admin Dashboard
          </a>
        <?php elseif ($role === 'company'): ?>
          <a href="<?php echo BASE_URL; ?>/company/dashboard.php" class="btn btn-primary">
            <i class="fa-solid fa-building"></i> Employer Portal
          </a>
        <?php else: ?>
          <a href="<?php echo BASE_URL; ?>/seeker/dashboard.php" class="btn btn-primary">
            <i class="fa-solid fa-user"></i> Candidate Portal
          </a>
        <?php endif; ?>
        <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="btn btn-outline">Logout</a>
      <?php endif; ?>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="container">
      <h1 class="hero-title">Discover Sri Lanka's Top Career Opportunities</h1>
      <p class="hero-subtitle">Connect with over 500+ verified companies and start the next milestone in your professional journey.</p>
      
      <div class="hero-search-box">
        <input type="text" placeholder="Job title, keywords, or skills (e.g. React Developer, Accountant)...">
        <button class="btn btn-primary px-4 py-3" style="font-size:15px; font-weight:700;">
          <i class="fa-solid fa-magnifying-glass"></i> Search Jobs
        </button>
      </div>

      <!-- Quick Portals Links -->
      <div class="flex-center gap-3 mt-4" style="flex-wrap:wrap;">
        <span class="text-muted" style="font-size:14px;">Quick Portals:</span>
        <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="badge badge-purple" style="font-size:13px; padding:6px 14px;">
          <i class="fa-solid fa-shield-halved"></i> Admin Control Center
        </a>
        <a href="<?php echo BASE_URL; ?>/auth/login.php" class="badge badge-indigo" style="font-size:13px; padding:6px 14px;">
          <i class="fa-solid fa-building"></i> Employer Portal
        </a>
        <a href="<?php echo BASE_URL; ?>/auth/login.php" class="badge badge-teal" style="font-size:13px; padding:6px 14px;">
          <i class="fa-solid fa-user-graduate"></i> Job Seeker Portal
        </a>
      </div>
    </div>
  </section>

  <!-- Categories Section -->
  <section class="container py-5">
    <div class="text-center mb-5">
      <h2 class="page-title" style="font-size:28px;">Explore Popular Job Categories</h2>
      <p class="text-muted">Find roles tailored to your specialization</p>
    </div>

    <div class="categories-grid">
      <?php foreach (array_slice($categories, 0, 8) as $cat): ?>
        <div class="category-card">
          <div class="category-icon-box">
            <i class="fa-solid fa-<?php echo htmlspecialchars($cat['icon'] ?: 'briefcase'); ?>"></i>
          </div>
          <h3 class="category-name"><?php echo htmlspecialchars($cat['name']); ?></h3>
          <span class="category-jobs-count"><?php echo $cat['job_count']; ?> Open Positions</span>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Featured Jobs List -->
  <section class="container pb-5">
    <div class="card">
      <div class="card-header flex-between">
        <h3 class="card-title"><i class="fa-solid fa-fire text-primary"></i> Latest Verified Job Openings</h3>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Position</th>
                <th>Company</th>
                <th>Type</th>
                <th>Location</th>
                <th>Compensation</th>
                <th class="text-right">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach (array_slice($featured_jobs, 0, 5) as $fj): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($fj['title']); ?></strong></td>
                  <td><?php echo htmlspecialchars($fj['company_name']); ?></td>
                  <td><span class="badge badge-teal"><?php echo htmlspecialchars($fj['job_type']); ?></span></td>
                  <td><i class="fa-solid fa-location-dot text-muted"></i> <?php echo htmlspecialchars($fj['location']); ?></td>
                  <td><strong class="text-emerald"><?php echo htmlspecialchars($fj['salary_range']); ?></strong></td>
                  <td class="text-right">
                    <a href="<?php echo BASE_URL; ?>/auth/login.php" class="btn btn-sm btn-primary">Apply Now</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <footer class="admin-footer" style="padding: 24px; text-align: center; border-top: 1px solid var(--border-color);">
    <div>&copy; <?php echo date('Y'); ?> <strong>JobPortal.lk</strong>. All rights reserved. Sri Lanka's Leading Career Network.</div>
  </footer>

</body>
</html>
