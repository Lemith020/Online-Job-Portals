<?php
/**
 * JobPortal.lk - Admin Job Seekers Management
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Job Seekers Management';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle_status') {
        $id = (int)($_POST['id'] ?? 0);
        $new_status = $_POST['status'] ?? 'Active';
        toggle_user_status($id, $new_status);
        add_activity("Updated job seeker user #$id status to $new_status", "user");
        set_flash("Candidate status updated to {$new_status}.", "success");
        header("Location: " . BASE_URL . "/admin/job-seekers.php");
        exit;
    }
}

$status_filter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');
$seekers = get_all_job_seekers($search, $status_filter);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div class="page-title-group">
    <h1 class="page-title">Job Seekers Directory</h1>
    <p class="page-subtitle">Manage candidate profiles, experience records, and portal account statuses.</p>
  </div>
  <div class="page-actions">
    <a href="<?php echo BASE_URL; ?>/admin/users.php?role=seeker" class="btn btn-secondary">
      <i class="fa-solid fa-users"></i> All Seeker Accounts
    </a>
  </div>
</div>

<!-- Filter Bar -->
<div class="card table-filter-card">
  <div class="card-body filter-bar-body">
    <form method="GET" action="job-seekers.php" class="filter-form">
      <div class="filter-group">
        <label>Status:</label>
        <select name="status" class="form-select" onchange="this.form.submit()">
          <option value="" <?php echo ($status_filter === '') ? 'selected' : ''; ?>>All Statuses</option>
          <option value="Active" <?php echo ($status_filter === 'Active') ? 'selected' : ''; ?>>Active</option>
          <option value="Suspended" <?php echo ($status_filter === 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
          <option value="Pending" <?php echo ($status_filter === 'Pending') ? 'selected' : ''; ?>>Pending</option>
        </select>
      </div>

      <div class="filter-group search-group">
        <label>Search Candidates:</label>
        <div class="input-with-icon">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" name="search" class="form-input" placeholder="Search candidate name, email, location..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
      </div>

      <div class="filter-actions">
        <button type="submit" class="btn btn-secondary">Filter</button>
        <?php if (!empty($status_filter) || !empty($search)): ?>
          <a href="job-seekers.php" class="btn btn-outline">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<!-- Job Seekers Table Card -->
<div class="card">
  <div class="card-header flex-between">
    <h3 class="card-title">Registered Candidates (<?php echo count($seekers); ?>)</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Candidate</th>
            <th>Location</th>
            <th>Experience</th>
            <th>Bio / Professional Summary</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($seekers)): ?>
            <tr>
              <td colspan="6" class="text-center py-4">
                <div class="empty-state">
                  <i class="fa-solid fa-user-slash empty-icon"></i>
                  <p>No job seekers matching your criteria.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($seekers as $s): ?>
              <tr>
                <td>
                  <div class="user-row-info">
                    <div class="user-avatar-sm role-seeker">
                      <span><?php echo strtoupper(substr($s['name'], 0, 1)); ?></span>
                    </div>
                    <div>
                      <strong class="user-display-name"><?php echo htmlspecialchars($s['name']); ?></strong>
                      <span class="user-display-email"><?php echo htmlspecialchars($s['email']); ?></span>
                      <small class="text-muted d-block"><?php echo htmlspecialchars($s['phone'] ?: 'No phone provided'); ?></small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="location-badge">
                    <i class="fa-solid fa-location-dot"></i>
                    <?php echo htmlspecialchars($s['location'] ?? 'Sri Lanka'); ?>
                  </span>
                </td>
                <td>
                  <span class="badge badge-secondary">
                    <?php echo (int)($s['experience_years'] ?? 0); ?> Years
                  </span>
                </td>
                <td>
                  <span class="text-truncate-2" style="max-width: 280px; display:inline-block; font-size:13px; color:var(--text-muted);">
                    <?php echo htmlspecialchars($s['bio'] ?? 'No bio entered yet.'); ?>
                  </span>
                </td>
                <td>
                  <?php if ($s['status'] === 'Active'): ?>
                    <span class="status-pill status-active">● Active</span>
                  <?php elseif ($s['status'] === 'Suspended'): ?>
                    <span class="status-pill status-danger">● Suspended</span>
                  <?php else: ?>
                    <span class="status-pill status-warning">● <?php echo htmlspecialchars($s['status'] ?? 'Pending'); ?></span>
                  <?php endif; ?>
                </td>
                <td class="text-right">
                  <div class="action-buttons">
                    <?php if ($s['status'] === 'Active'): ?>
                      <form method="POST" action="job-seekers.php" style="display:inline;" onsubmit="return confirm('Suspend this job seeker?');">
                        <input type="hidden" name="action" value="toggle_status">
                        <input type="hidden" name="id" value="<?php echo $s['user_id'] ?? $s['id']; ?>">
                        <input type="hidden" name="status" value="Suspended">
                        <button type="submit" class="btn-icon text-amber" title="Suspend Candidate">
                          <i class="fa-solid fa-ban"></i>
                        </button>
                      </form>
                    <?php else: ?>
                      <form method="POST" action="job-seekers.php" style="display:inline;">
                        <input type="hidden" name="action" value="toggle_status">
                        <input type="hidden" name="id" value="<?php echo $s['user_id'] ?? $s['id']; ?>">
                        <input type="hidden" name="status" value="Active">
                        <button type="submit" class="btn-icon text-emerald" title="Activate Candidate">
                          <i class="fa-solid fa-circle-check"></i>
                        </button>
                      </form>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
