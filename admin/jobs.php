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

<!-- Outer Main Wrapper -->
<div class="main-content" style="margin-top: 60px; margin-left: 240px; padding: 28px; width: calc(100% - 240px); box-sizing: border-box; min-height: calc(100vh - 60px);">

  <!-- Page Header -->
  <div style="margin-bottom: 24px;">
    <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0;">Job Postings Moderation</h1>
    <p style="font-size: 13.5px; color: #64748b; margin-top: 4px;">Review, approve, reject, or delete job listings from employers.</p>
  </div>

  <!-- Filter Bar -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
    <form method="GET" action="jobs.php" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap; margin: 0;">
      
      <div style="width: 180px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #0f172a; margin-bottom: 6px;">Status</label>
        <select name="status" onchange="this.form.submit()" style="width: 100%; height: 38px; padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; background: #fff;">
          <option value="">All Statuses</option>
          <option value="approved" <?php echo ($status_filter === 'approved') ? 'selected' : ''; ?>>Approved / Active</option>
          <option value="pending" <?php echo ($status_filter === 'pending') ? 'selected' : ''; ?>>Pending Moderation</option>
          <option value="rejected" <?php echo ($status_filter === 'rejected') ? 'selected' : ''; ?>>Rejected / Spam</option>
        </select>
      </div>

      <div style="width: 200px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #0f172a; margin-bottom: 6px;">Category</label>
        <select name="category" onchange="this.form.submit()" style="width: 100%; height: 38px; padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; background: #fff;">
          <option value="">All Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?php echo htmlspecialchars($cat['category_id'] ?? $cat['id']); ?>" <?php echo ($category_filter == ($cat['category_id'] ?? $cat['id'])) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($cat['category_name'] ?? $cat['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="flex: 1; min-width: 220px;">
        <label style="display: block; font-size: 13px; font-weight: 600; color: #0f172a; margin-bottom: 6px;">Search Jobs</label>
        <input type="text" name="search" placeholder="Search title, location..." value="<?php echo htmlspecialchars($search); ?>" style="width: 100%; height: 38px; padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
      </div>

      <div style="display: flex; gap: 8px;">
        <button type="submit" style="height: 38px; padding: 0 16px; background: #2563eb; color: #ffffff; border: none; border-radius: 6px; font-weight: 600; font-size: 13.5px; cursor: pointer;">Filter</button>
        <?php if (!empty($status_filter) || !empty($category_filter) || !empty($search)): ?>
          <a href="jobs.php" style="height: 38px; padding: 0 16px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600; font-size: 13.5px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Jobs Table Card -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
    <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
      <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">Job Listings (<?php echo count($jobs); ?>)</h3>
    </div>

    <div style="width: 100%; overflow-x: auto;">
      <table class="table" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
          <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <th style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">JOB TITLE & EMPLOYER</th>
            <th style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">CATEGORY</th>
            <th style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">JOB TYPE & SALARY</th>
            <th style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">LOCATION</th>
            <th style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">STATUS</th>
            <th style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; text-align: right;">ACTIONS</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($jobs)): ?>
            <tr>
              <td colspan="6" style="text-align: center; padding: 32px; color: #64748b;">No job listings found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($jobs as $j): ?>
              <?php $job_db_id = $j['job_id'] ?? $j['id']; ?>
              <tr style="border-bottom: 1px solid #e2e8f0;">
                <td style="padding: 14px 16px;">
                  <strong style="display: block; font-size: 14px; color: #0f172a;"><?php echo htmlspecialchars($j['title']); ?></strong>
                  <span style="font-size: 12px; color: #64748b;">
                    <i class="fa-regular fa-building"></i> <?php echo htmlspecialchars($j['company_name'] ?? 'Company #' . $j['company_id']); ?>
                  </span>
                </td>
                <td style="padding: 14px 16px;">
                  <span style="background: #f3e8ff; color: #6b21a8; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                    <?php echo htmlspecialchars($j['category_name'] ?? 'General'); ?>
                  </span>
                </td>
                <td style="padding: 14px 16px;">
                  <span style="background: #ccfbf1; color: #0f766e; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                    <?php echo htmlspecialchars($j['job_type']); ?>
                  </span>
                  <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                    LKR <?php echo number_format($j['salary_min']); ?> - <?php echo number_format($j['salary_max']); ?>
                  </div>
                </td>
                <td style="padding: 14px 16px; font-size: 13px; color: #334155;">
                  <i class="fa-solid fa-location-dot" style="color: #ef4444;"></i> <?php echo htmlspecialchars($j['location']); ?>
                </td>
                <td style="padding: 14px 16px;">
                  <?php if ($j['status'] === 'approved'): ?>
                    <span style="color: #16a34a; font-weight: 700; font-size: 13px;">✓ Approved</span>
                  <?php elseif ($j['status'] === 'pending'): ?>
                    <span style="color: #d97706; font-weight: 700; font-size: 13px;">⏳ Pending</span>
                  <?php else: ?>
                    <span style="color: #dc2626; font-weight: 700; font-size: 13px;">✕ Rejected</span>
                  <?php endif; ?>
                </td>
                <td style="padding: 14px 16px; text-align: right;">
                  <div style="display: flex; gap: 6px; justify-content: flex-end;">
                    
                    <?php if ($j['status'] === 'pending'): ?>
                      <form method="POST" action="jobs.php" style="display:inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $job_db_id; ?>">
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" style="padding: 6px 10px; background: #16a34a; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer;">
                          <i class="fa-solid fa-check"></i> Approve
                        </button>
                      </form>
                      <form method="POST" action="jobs.php" style="display:inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $job_db_id; ?>">
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" style="padding: 6px 10px; background: #dc2626; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer;">
                          <i class="fa-solid fa-xmark"></i> Reject
                        </button>
                      </form>

                    <?php elseif ($j['status'] === 'approved'): ?>
                      <form method="POST" action="jobs.php" style="display:inline;" onsubmit="return confirm('Reject / Take down this job?');">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $job_db_id; ?>">
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" style="padding: 6px 10px; background: #f59e0b; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer;" title="Reject Job">
                          <i class="fa-solid fa-ban"></i> Reject
                        </button>
                      </form>

                    <?php else: ?>
                      <form method="POST" action="jobs.php" style="display:inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $job_db_id; ?>">
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" style="padding: 6px 10px; background: #16a34a; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer;">
                          <i class="fa-solid fa-circle-check"></i> Approve
                        </button>
                      </form>
                    <?php endif; ?>

                    <!-- Delete Button -->
                    <form method="POST" action="jobs.php" style="display:inline;" onsubmit="return confirm('Permanently delete this job listing?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo $job_db_id; ?>">
                      <button type="submit" style="padding: 6px 10px; background: #fee2e2; color: #dc2626; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer;" title="Delete Job">
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