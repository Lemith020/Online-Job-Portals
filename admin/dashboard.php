<?php
/**
 * JobPortal.lk - Admin Dashboard
 * Member 1 - Admin UI
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Admin Dashboard';
$metrics = get_admin_metrics();
$activities = get_recent_activities();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<!-- Header Greeting Section -->
<div class="page-header">
  <div class="page-title-group">
    <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'Admin Kamal'); ?>!</h1>
    <p>Here is an overview of JobPortal.lk system performance, activity, and moderation queues.</p>
  </div>
  <div>
    <span class="status-badge badge-success">● System Online</span>
  </div>
</div>

<!-- 6 KPI Metric Cards (Matching Wireframe Page 12) -->
<div class="metrics-grid">
  <!-- 1. Total Users -->
  <div class="metric-card" onclick="location.href='users.php'" style="cursor: pointer;">
    <div class="metric-info">
      <span class="metric-title">Total Users</span>
      <span class="metric-value"><?php echo number_format($metrics['total_users']); ?></span>
      <span class="metric-desc">Registered Job Seekers & Employers</span>
    </div>
    <div class="metric-icon-box icon-blue">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
      </svg>
    </div>
  </div>

  <!-- 2. Total Companies -->
  <div class="metric-card" onclick="location.href='companies.php'" style="cursor: pointer;">
    <div class="metric-info">
      <span class="metric-title">Total Companies</span>
      <span class="metric-value"><?php echo number_format($metrics['total_companies']); ?></span>
      <span class="metric-desc">Verified Employers</span>
    </div>
    <div class="metric-icon-box icon-teal">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
        <line x1="9" y1="22" x2="9" y2="22.01"></line>
        <line x1="15" y1="22" x2="15" y2="22.01"></line>
        <line x1="8" y1="6" x2="8" y2="6.01"></line>
        <line x1="16" y1="6" x2="16" y2="6.01"></line>
      </svg>
    </div>
  </div>

  <!-- 3. Total Jobs -->
  <div class="metric-card" onclick="location.href='jobs.php'" style="cursor: pointer;">
    <div class="metric-info">
      <span class="metric-title">Total Jobs</span>
      <span class="metric-value"><?php echo number_format($metrics['total_jobs']); ?></span>
      <span class="metric-desc">Across all categories</span>
    </div>
    <div class="metric-icon-box icon-purple">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
      </svg>
    </div>
  </div>

  <!-- 4. Pending Job Approvals -->
  <div class="metric-card" onclick="location.href='jobs.php?status=Pending+Approval'" style="cursor: pointer;">
    <div class="metric-info">
      <span class="metric-title">Pending Job Approvals</span>
      <span class="metric-value" style="color: var(--warning-amber);"><?php echo number_format($metrics['pending_jobs']); ?></span>
      <span class="metric-desc">Awaiting Review</span>
    </div>
    <div class="metric-icon-box icon-amber">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <polyline points="12 6 12 12 16 14"></polyline>
      </svg>
    </div>
  </div>

  <!-- 5. Flagged Reviews -->
  <div class="metric-card" onclick="location.href='reviews.php'" style="cursor: pointer;">
    <div class="metric-info">
      <span class="metric-title">Flagged Reviews</span>
      <span class="metric-value" style="color: var(--danger-red);"><?php echo number_format($metrics['flagged_reviews']); ?></span>
      <span class="metric-desc">Reported Content</span>
    </div>
    <div class="metric-icon-box icon-rose">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
        <line x1="4" y1="22" x2="4" y2="15"></line>
      </svg>
    </div>
  </div>

  <!-- 6. Active Subscriptions -->
  <div class="metric-card">
    <div class="metric-info">
      <span class="metric-title">Active Subscriptions</span>
      <span class="metric-value" style="color: var(--success-green);"><?php echo number_format($metrics['active_subscriptions']); ?></span>
      <span class="metric-desc">Premium Employers</span>
    </div>
    <div class="metric-icon-box icon-emerald">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="1" x2="12" y2="23"></line>
        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
      </svg>
    </div>
  </div>
</div>

<!-- Dashboard Bottom Section: Feed & Quick Actions -->
<div class="dashboard-grid-2">
  <!-- Recent Activity Feed -->
  <div class="panel-card">
    <div class="panel-header">
      <h2 class="panel-title">Recent Activity Feed</h2>
      <a href="javascript:location.reload()" class="btn btn-secondary btn-sm">Refresh</a>
    </div>
    <ul class="activity-list">
      <?php foreach ($activities as $item): ?>
        <li class="activity-item">
          <span class="activity-dot"></span>
          <div class="activity-content">
            <div class="activity-title"><?php echo htmlspecialchars($item['title']); ?></div>
            <div class="activity-time"><?php echo htmlspecialchars($item['time']); ?></div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <!-- Quick Management Actions -->
  <div class="panel-card">
    <div class="panel-header">
      <h2 class="panel-title">Administrative Actions</h2>
    </div>
    <div style="padding: 22px; display: flex; flex-direction: column; gap: 12px;">
      <a href="jobs.php?status=Pending+Approval" class="btn btn-warning" style="justify-content: flex-start;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
        <span>Moderate Pending Jobs (<?php echo $metrics['pending_jobs']; ?>)</span>
      </a>

      <a href="companies.php?status=Pending+Approval" class="btn btn-primary" style="justify-content: flex-start;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
        </svg>
        <span>Verify Company Profiles</span>
      </a>

      <a href="categories.php" class="btn btn-secondary" style="justify-content: flex-start;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <span>Manage Job Categories</span>
      </a>

      <a href="reviews.php" class="btn btn-danger" style="justify-content: flex-start;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
        </svg>
        <span>Moderate User Reviews</span>
      </a>
    </div>
  </div>
</div>

</main> <!-- Close admin-content -->
</div> <!-- Close admin-main -->
</div> <!-- Close admin-wrapper -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
