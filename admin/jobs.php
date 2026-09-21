<?php
/**
 * JobPortal.lk - Job Listings Moderation
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Job Moderation';
$page_css = BASE_URL . '/assets/css/admin_page.css';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $id = (int)($_POST['id'] ?? 0);
        $new_status = $_POST['status'] ?? 'approved';
        update_job_status($id, $new_status);
        add_activity("Updated Job #$id moderation status to " . ucfirst($new_status), "job");
        set_flash("Job posting status updated successfully.", "success");
        header("Location: " . BASE_URL . "/admin/jobs.php");
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        delete_job_admin($id);
        add_activity("Deleted job listing #$id", "job");
        set_flash("Job listing #{$id} deleted successfully.", "success");
        header("Location: " . BASE_URL . "/admin/jobs.php");
        exit;
    }
}

$status_filter = $_GET['status'] ?? '';
$category_filter = $_GET['category'] ?? '';
$search = trim($_GET['search'] ?? '');

$jobs = get_all_jobs_admin($status_filter, $category_filter, $search);
$categories = get_all_categories_admin();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div class="page-title-group">
    <h1 class="page-title">Job Postings Moderation</h1>
    <p class="page-subtitle">Review, approve, reject, or delete job listings from employers.</p>
  </div>
</div>

<!-- Filter Bar Card -->
<div class="card table-filter-card">
  <div class="card-body filter-bar-body">
    <form method="GET" action="jobs.php" class="filter-form">
      <div class="filter-group">
        <label>Status:</label>
        <select name="status" class="form-select" onchange="this.form.submit()">
          <option value="">All Statuses</option>
          <option value="approved" <?php echo ($status_filter === 'approved') ? 'selected' : ''; ?>>Approved / Active</option>
          <option value="pending" <?php echo ($status_filter === 'pending') ? 'selected' : ''; ?>>Pending Moderation</option>
          <option value="rejected" <?php echo ($status_filter === 'rejected') ? 'selected' : ''; ?>>Rejected / Spam</option>
        </select>
      </div>

      <div class="filter-group">
        <label>Category:</label>
        <select name="category" class="form-select" onchange="this.form.submit()">
          <option value="">All Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?php echo htmlspecialchars($cat['category_id'] ?? $cat['id']); ?>" <?php echo ($category_filter == ($cat['category_id'] ?? $cat['id'])) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($cat['category_name'] ?? $cat['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="filter-group search-group">
        <label>Search Jobs:</label>
        <div class="input-with-icon">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" name="search" class="form-input" placeholder="Search title, location..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
      </div>

      <div class="filter-actions">
        <button type="submit" class="btn btn-secondary">Filter</button>
        <?php if (!empty($status_filter) || !empty($category_filter) || !empty($search)): ?>
          <a href="jobs.php" class="btn btn-outline">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<!-- Jobs Table Card -->
<div class="card">
  <div class="card-header flex-between">
    <h3 class="card-title">Job Listings (<?php echo count($jobs); ?>)</h3>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Job Title & Employer</th>
            <th>Category</th>
            <th>Job Type & Salary</th>
            <th>Location</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($jobs)): ?>
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">
                <div class="empty-state">
                  <i class="fa-solid fa-briefcase empty-icon"></i>
                  <p>No job listings found.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($jobs as $j): ?>
              <?php $job_db_id = $j['job_id'] ?? $j['id']; ?>
              <tr>
                <td>
                  <strong class="d-block" style="font-size: 14px; color: #0f172a;"><?php echo htmlspecialchars($j['title']); ?></strong>
                  <span class="text-muted" style="font-size: 12px;">
                    <i class="fa-regular fa-building"></i> <?php echo htmlspecialchars($j['company_name'] ?? 'Company #' . $j['company_id']); ?>
                  </span>
                </td>
                <td>
                  <span class="badge badge-purple">
                    <?php echo htmlspecialchars($j['category_name'] ?? 'General'); ?>
                  </span>
                </td>
                <td>
                  <span class="badge badge-teal">
                    <?php echo htmlspecialchars($j['job_type']); ?>
                  </span>
                  <div class="text-muted" style="font-size: 12px; margin-top: 4px;">
                    LKR <?php echo number_format($j['salary_min']); ?> - <?php echo number_format($j['salary_max']); ?>
                  </div>
                </td>
                <td style="font-size: 13px; color: #334155;">
                  <span class="location-badge">
                    <i class="fa-solid fa-location-dot" style="color: #ef4444;"></i>
                    <?php echo htmlspecialchars($j['location']); ?>
                  </span>
                </td>
                <td>
                  <?php if ($j['status'] === 'approved'): ?>
                    <span class="status-pill status-active">✓ Approved</span>
                  <?php elseif ($j['status'] === 'pending'): ?>
                    <span class="status-pill status-warning">⏳ Pending</span>
                  <?php else: ?>
                    <span class="status-pill status-danger">✕ Rejected</span>
                  <?php endif; ?>
                </td>
                <td class="text-right">
                  <div class="action-buttons">
                    <?php if ($j['status'] === 'pending'): ?>
                      <form method="POST" action="jobs.php" style="display:inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $job_db_id; ?>">
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn btn-sm btn-success" title="Approve Job">
                          <i class="fa-solid fa-check"></i> Approve
                        </button>
                      </form>
                      <form method="POST" action="jobs.php" style="display:inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $job_db_id; ?>">
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn btn-sm btn-danger" title="Reject Job">
                          <i class="fa-solid fa-xmark"></i> Reject
                        </button>
                      </form>
                    <?php elseif ($j['status'] === 'approved'): ?>
                      <form method="POST" action="jobs.php" style="display:inline;" onsubmit="return confirm('Reject / Take down this job?');">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $job_db_id; ?>">
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn-icon text-amber" title="Reject Job">
                          <i class="fa-solid fa-ban"></i>
                        </button>
                      </form>
                    <?php else: ?>
                      <form method="POST" action="jobs.php" style="display:inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $job_db_id; ?>">
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn-icon text-emerald" title="Approve Job">
                          <i class="fa-solid fa-circle-check"></i>
                        </button>
                      </form>
                    <?php endif; ?>

                    <!-- Delete Button -->
                    <form method="POST" action="jobs.php" style="display:inline;" onsubmit="return confirm('Permanently delete this job listing?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo $job_db_id; ?>">
                      <button type="submit" class="btn-icon text-danger" title="Delete Job">
                        <i class="fa-regular fa-trash-can"></i>
                      </button>
                    </form>
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
