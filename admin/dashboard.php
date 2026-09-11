<?php
/**
 * JobPortal.lk - Admin Dashboard
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Dashboard Overview';

$page_css = BASE_URL . '/assets/css/admin_page.css'; 
$metrics = get_admin_metrics();
$activities = get_recent_activities(6);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div class="page-title-group">
    <h1 class="page-title">Executive Dashboard</h1>
    <p class="page-subtitle">Real-time portal performance, moderation queues, and operational metrics.</p>
  </div>
  <div class="page-actions">
    <a href="<?php echo BASE_URL; ?>/admin/jobs.php?status=Pending+Approval" class="btn btn-warning">
      <i class="fa-solid fa-clock-rotate-left"></i>
      <span>Review Pending Jobs (<?php echo $metrics['pending_jobs']; ?>)</span>
    </a>
    <a href="<?php echo BASE_URL; ?>/admin/users.php" class="btn btn-primary">
      <i class="fa-solid fa-user-plus"></i>
      <span>Manage Users</span>
    </a>
  </div>
</div>

<!-- 4 KPI Metric Cards Grid -->
<div class="metrics-grid">
  <!-- 1. Total Users -->
  <div class="metric-card" onclick="location.href='users.php'">
    <div class="metric-info">
      <span class="metric-title">Total Users</span>
      <span class="metric-value"><?php echo number_format($metrics['total_users']); ?></span>
      <span class="metric-desc">Registered Users</span>
    </div>
    <div class="metric-icon-box icon-blue">
      <i class="fa-solid fa-users"></i>
    </div>
  </div>

  <!-- 2. Total Jobs Posted -->
  <div class="metric-card" onclick="location.href='jobs.php'">
    <div class="metric-info">
      <span class="metric-title">Active Job Listings</span>
      <span class="metric-value"><?php echo number_format($metrics['total_jobs']); ?></span>
      <span class="metric-desc">Across all categories</span>
    </div>
    <div class="metric-icon-box icon-purple">
      <i class="fa-solid fa-briefcase"></i>
    </div>
  </div>

  <!-- 3. Pending Approvals -->
  <div class="metric-card" onclick="location.href='jobs.php?status=Pending+Approval'">
    <div class="metric-info">
      <span class="metric-title">Pending Approvals</span>
      <span class="metric-value text-amber"><?php echo number_format($metrics['pending_jobs']); ?></span>
      <span class="metric-desc">Jobs awaiting moderation</span>
    </div>
    <div class="metric-icon-box icon-amber">
      <i class="fa-solid fa-hourglass-half"></i>
    </div>
  </div>

  <!-- 4. Active Subscriptions -->
  <div class="metric-card" onclick="location.href='subscriptions.php'">
    <div class="metric-info">
      <span class="metric-title">Active Subscriptions</span>
      <span class="metric-value text-emerald"><?php echo number_format($metrics['active_subscriptions'] ?? 0); ?></span>
      <span class="metric-desc">Current active plans</span>
    </div>
    <div class="metric-icon-box icon-teal">
      <i class="fa-solid fa-credit-card"></i>
    </div>
  </div>
</div>

<!-- Dashboard Two Column Sections -->
<div class="dashboard-grid-2">
  <!-- Left: Quick Moderation Actions & Category Distribution -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><i class="fa-solid fa-bolt-lightning text-primary"></i> Quick Moderation Actions</h3>
    </div>
    <div class="card-body">
      <div class="quick-action-list">
        <a href="<?php echo BASE_URL; ?>/admin/jobs.php?status=Pending+Approval" class="quick-action-item">
          <div class="qa-icon-wrapper bg-amber-light">
            <i class="fa-solid fa-file-circle-check text-amber"></i>
          </div>
          <div class="qa-details">
            <strong>Moderate Job Postings</strong>
            <span><?php echo $metrics['pending_jobs']; ?> jobs awaiting approval before going live</span>
          </div>
          <i class="fa-solid fa-chevron-right qa-arrow"></i>
        </a>

        <a href="<?php echo BASE_URL; ?>/admin/categories.php" class="quick-action-item">
          <div class="qa-icon-wrapper bg-blue-light">
            <i class="fa-solid fa-tags text-primary"></i>
          </div>
          <div class="qa-details">
            <strong>Manage Job Categories</strong>
            <span>Add or edit industry sectors and classifications</span>
          </div>
          <i class="fa-solid fa-chevron-right qa-arrow"></i>
        </a>

        <a href="<?php echo BASE_URL; ?>/admin/subscriptions.php" class="quick-action-item">
          <div class="qa-icon-wrapper bg-emerald-light">
            <i class="fa-solid fa-credit-card text-emerald"></i>
          </div>
          <div class="qa-details">
            <strong>Manage Employer Subscriptions</strong>
            <span><?php echo $metrics['active_subscriptions']; ?> active plans across registered companies</span>
          </div>
          <i class="fa-solid fa-chevron-right qa-arrow"></i>
        </a>

        <a href="<?php echo BASE_URL; ?>/admin/users.php" class="quick-action-item">
          <div class="qa-icon-wrapper bg-purple-light">
            <i class="fa-solid fa-users text-primary"></i>
          </div>
          <div class="qa-details">
            <strong>Manage Portal Users</strong>
            <span>View, filter, or manage user accounts and access</span>
          </div>
          <i class="fa-solid fa-chevron-right qa-arrow"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Right: Recent System & Audit Activity -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><i class="fa-solid fa-clock-rotate-left text-primary"></i> Recent Audit Activity</h3>
      <span class="badge badge-secondary">Live Feed</span>
    </div>
    <div class="card-body">
      <div class="activity-timeline">
        <?php foreach ($activities as $act): ?>
          <div class="timeline-item">
            <div class="timeline-point"></div>
            <div class="timeline-content">
              <p class="timeline-text"><?php echo htmlspecialchars($act['action']); ?></p>
              <span class="timeline-time"><i class="fa-regular fa-clock"></i> <?php echo time_ago($act['created_at']); ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
