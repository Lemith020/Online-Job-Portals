<?php
/**
 * JobPortal.lk - Job Listings Moderation
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Job Moderation';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $id = (int)($_POST['id'] ?? 0);
        $new_status = $_POST['status'] ?? 'Approved';
        update_job_status($id, $new_status);
        add_activity("Updated Job #$id moderation status to $new_status", "job");
        set_flash("Job posting status updated to {$new_status}.", "success");
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
    <p class="page-subtitle">Review, approve, or take down job postings submitted by registered employers.</p>
  </div>
</div>

<!-- Filter Bar -->
<div class="card table-filter-card">
  <div class="card-body filter-bar-body">
    <form method="GET" action="jobs.php" class="filter-form">
      <div class="filter-group">
        <label>Status:</label>
        <select name="status" class="form-select" onchange="this.form.submit()">
          <option value="" <?php echo ($status_filter === '') ? 'selected' : ''; ?>>All Statuses</option>
          <option value="Approved" <?php echo ($status_filter === 'Approved') ? 'selected' : ''; ?>>Approved / Active</option>
          <option value="Pending Approval" <?php echo ($status_filter === 'Pending Approval') ? 'selected' : ''; ?>>Pending Moderation</option>
          <option value="Rejected" <?php echo ($status_filter === 'Rejected') ? 'selected' : ''; ?>>Rejected / Spam</option>
        </select>
      </div>

      <div class="filter-group">
        <label>Category:</label>
        <select name="category" class="form-select" onchange="this.form.submit()">
          <option value="" <?php echo ($category_filter === '') ? 'selected' : ''; ?>>All Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo ($category_filter === $cat['name']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($cat['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="filter-group search-group">
        <label>Search Jobs:</label>
        <div class="input-with-icon">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" name="search" class="form-input" placeholder="Search job title, company, location..." value="<?php echo htmlspecialchars($search); ?>">
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
    <h3 class="card-title">Job Postings (<?php echo count($jobs); ?>)</h3>
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
              <td colspan="6" class="text-center py-4">
                <div class="empty-state">
                  <i class="fa-solid fa-briefcase empty-icon"></i>
                  <p>No job postings matching your query.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($jobs as $j): ?>
              <tr>
                <td>
                  <div class="job-item-title">
                    <strong><?php echo htmlspecialchars($j['title']); ?></strong>
                    <span class="job-item-company text-muted d-block">
                      <i class="fa-regular fa-building"></i> <?php echo htmlspecialchars($j['company_name']); ?>
                    </span>
                  </div>
                </td>
                <td>
                  <span class="badge badge-purple"><?php echo htmlspecialchars($j['category_name'] ?? 'General'); ?></span>
                </td>
                <td>
                  <div>
                    <span class="badge badge-teal"><?php echo htmlspecialchars($j['job_type']); ?></span>
                    <small class="d-block text-muted mt-1"><?php echo htmlspecialchars($j['salary_range']); ?></small>
                  </div>
                </td>
                <td>
                  <span class="location-badge">
                    <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($j['location']); ?>
                  </span>
                </td>
                <td>
                  <?php if ($j['status'] === 'Approved'): ?>
                    <span class="status-pill status-active">✓ Approved</span>
                  <?php elseif ($j['status'] === 'Pending Approval'): ?>
                    <span class="status-pill status-warning">⏳ Pending Approval</span>
                  <?php else: ?>
                    <span class="status-pill status-danger">✕ Rejected</span>
                  <?php endif; ?>
                </td>
                <td class="text-right">
                  <div class="action-buttons">
                    <?php if ($j['status'] === 'Pending Approval'): ?>
                      <form method="POST" action="jobs.php" style="display:inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $j['id']; ?>">
                        <input type="hidden" name="status" value="Approved">
                        <button type="submit" class="btn btn-sm btn-success" title="Approve Job">
                          <i class="fa-solid fa-check"></i> Approve
                        </button>
                      </form>
                      <form method="POST" action="jobs.php" style="display:inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $j['id']; ?>">
                        <input type="hidden" name="status" value="Rejected">
                        <button type="submit" class="btn btn-sm btn-danger" title="Reject Job">
                          <i class="fa-solid fa-xmark"></i> Reject
                        </button>
                      </form>
                    <?php elseif ($j['status'] === 'Approved'): ?>
                      <form method="POST" action="jobs.php" style="display:inline;" onsubmit="return confirm('Take down / reject this active job?');">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $j['id']; ?>">
                        <input type="hidden" name="status" value="Rejected">
                        <button type="submit" class="btn-icon text-amber" title="Take Down Job">
                          <i class="fa-solid fa-eye-slash"></i>
                        </button>
                      </form>
                    <?php else: ?>
                      <form method="POST" action="jobs.php" style="display:inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $j['id']; ?>">
                        <input type="hidden" name="status" value="Approved">
                        <button type="submit" class="btn-icon text-emerald" title="Re-approve Job">
                          <i class="fa-solid fa-circle-check"></i>
                        </button>
                      </form>
                    <?php endif; ?>

                    <form method="POST" action="jobs.php" style="display:inline;" onsubmit="return confirm('Permanently delete this job listing?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo $j['id']; ?>">
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
