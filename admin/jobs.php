<?php
/**
 * JobPortal.lk - Jobs Approval & Moderation (Admin)
 * Member 1 - Admin UI
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Job Listings & Approval';

// Handle Action Requests (Approve, Reject, Delete, Add)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_job') {
    $title = trim($_POST['title'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $location = trim($_POST['location'] ?? 'Colombo, Sri Lanka');
    $salary = trim($_POST['salary_range'] ?? 'Rs. 150,000 - Rs. 250,000 / month');
    $job_type = trim($_POST['job_type'] ?? 'Full-time');
    $description = trim($_POST['description'] ?? '');

    if (!empty($title) && !empty($company_name)) {
        if ($db) {
            try {
                $stmt = $db->prepare("INSERT INTO jobs (company_id, title, company_name, location, salary_range, job_type, description, status) VALUES (1, ?, ?, ?, ?, ?, ?, 'Approved')");
                $stmt->execute([$title, $company_name, $location, $salary, $job_type, $description]);
            } catch (Exception $e) {}
        }
        $new_id = count($_SESSION['mock_jobs']) + 1;
        $_SESSION['mock_jobs'][] = [
            'id' => $new_id,
            'company_id' => 1,
            'category_id' => 1,
            'title' => $title,
            'company_name' => $company_name,
            'location' => $location,
            'posted_date' => date('d/m/Y, H:i'),
            'status' => 'Approved',
            'job_type' => $job_type,
            'salary_range' => $salary,
            'description' => $description
        ];
        add_activity("Job created: $title by $company_name", 'job');
        set_flash("Job listing '$title' created successfully!", 'success');
    }
    header('Location: jobs.php');
    exit;
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $target_id = (int)$_GET['id'];

    if ($action === 'approve') {
        update_job_status($target_id, 'Approved');
        set_flash('Job listing has been Approved and published.', 'success');
    } elseif ($action === 'reject') {
        update_job_status($target_id, 'Rejected');
        set_flash('Job listing has been Rejected.', 'warning');
    } elseif ($action === 'delete') {
        delete_job($target_id);
        set_flash('Job listing removed permanently.', 'error');
    }

    header('Location: jobs.php');
    exit;
}

// Filters & Search
$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? 'all');
$sort = trim($_GET['sort'] ?? 'newest');

$jobs = get_all_jobs($search, $status_filter, $sort);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="page-header">
  <div class="page-title-group">
    <h1>Job Listings & Approval</h1>
    <p>Review newly submitted vacancies, manage publishing status, and moderate public job listings.</p>
  </div>
  <div>
    <button type="button" class="btn btn-primary" onclick="openModal('addJobModal')">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>+ Add New Job</span>
    </button>
  </div>
</div>

<!-- Main Table Card -->
<div class="data-table-card">
  <!-- Table Toolbar -->
  <form method="GET" action="jobs.php" class="table-toolbar">
    <div class="toolbar-search">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search job title or company..." data-table-search="jobsTable">
    </div>

    <div class="toolbar-filters">
      <!-- Status Tabs -->
      <div class="filter-tab-group">
        <a href="jobs.php?status=all&sort=<?php echo urlencode($sort); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($status_filter === 'all' || empty($status_filter)) ? 'active' : ''; ?>">All Jobs</a>
        <a href="jobs.php?status=Pending+Approval&sort=<?php echo urlencode($sort); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($status_filter === 'Pending Approval') ? 'active' : ''; ?>">Pending Approval</a>
        <a href="jobs.php?status=Approved&sort=<?php echo urlencode($sort); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($status_filter === 'Approved') ? 'active' : ''; ?>">Approved</a>
        <a href="jobs.php?status=Rejected&sort=<?php echo urlencode($sort); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($status_filter === 'Rejected') ? 'active' : ''; ?>">Rejected</a>
      </div>

      <!-- Sort Selection -->
      <select name="sort" class="select-filter" onchange="this.form.submit()">
        <option value="newest" <?php echo ($sort === 'newest') ? 'selected' : ''; ?>>Newest First</option>
        <option value="oldest" <?php echo ($sort === 'oldest') ? 'selected' : ''; ?>>Oldest First</option>
      </select>

      <?php if (!empty($search) || $status_filter !== 'all' || $sort !== 'newest'): ?>
        <a href="jobs.php" class="btn btn-secondary btn-sm">Reset</a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Responsive Jobs Table -->
  <div class="table-responsive">
    <table class="custom-table" id="jobsTable">
      <thead>
        <tr>
          <th>Job Title</th>
          <th>Company Name</th>
          <th>Location</th>
          <th>Posted Date</th>
          <th>Status</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($jobs)): ?>
          <tr>
            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
              No job postings found matching the specified criteria.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($jobs as $job): ?>
            <tr>
              <td>
                <div class="primary-text" style="font-size: 14px;"><?php echo htmlspecialchars($job['title']); ?></div>
                <div class="secondary-text"><?php echo htmlspecialchars($job['job_type'] ?? 'Full-time'); ?> • <?php echo htmlspecialchars($job['salary_range'] ?? 'Negotiable'); ?></div>
              </td>
              <td>
                <span style="font-weight: 600; color: var(--text-heading);"><?php echo htmlspecialchars($job['company_name']); ?></span>
              </td>
              <td><?php echo htmlspecialchars($job['location']); ?></td>
              <td><?php echo htmlspecialchars($job['posted_date']); ?></td>
              <td><?php echo render_status_badge($job['status']); ?></td>
              <td style="text-align: right;">
                <div class="action-btn-group" style="justify-content: flex-end;">
                  <?php if ($job['status'] === 'Pending Approval'): ?>
                    <a href="jobs.php?action=approve&id=<?php echo $job['id']; ?>" class="btn btn-success btn-sm" title="Approve Job">
                      Approve
                    </a>
                    <a href="jobs.php?action=reject&id=<?php echo $job['id']; ?>" class="btn btn-danger btn-sm" title="Reject Job">
                      Reject
                    </a>
                  <?php elseif ($job['status'] === 'Approved'): ?>
                    <a href="jobs.php?action=reject&id=<?php echo $job['id']; ?>" class="btn btn-secondary btn-sm" title="Unpublish">
                      Reject
                    </a>
                  <?php else: ?>
                    <a href="jobs.php?action=approve&id=<?php echo $job['id']; ?>" class="btn btn-success btn-sm" title="Re-approve">
                      Approve
                    </a>
                  <?php endif; ?>

                  <button type="button" class="btn btn-primary btn-sm" onclick="showJobDetails(<?php echo htmlspecialchars(json_encode($job)); ?>)">
                    View Details
                  </button>

                  <button type="button" class="btn btn-danger btn-sm" onclick="confirmAction('Are you sure you want to delete this job listing?', 'jobs.php?action=delete&id=<?= $job['id'] ?>')">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="table-pagination">
    <span class="secondary-text">Showing <?php echo count($jobs); ?> of <?php echo count($jobs); ?> jobs</span>
    <div class="pagination-controls">
      <button class="page-btn page-btn-wide">Previous</button>
      <button class="page-btn active">1</button>
      <button class="page-btn">2</button>
      <button class="page-btn">3</button>
      <button class="page-btn page-btn-wide">Next</button>
    </div>
  </div>
</div>

<!-- Modal: Add New Job -->
<div class="modal-backdrop" id="addJobModal">
  <div class="modal-dialog" style="max-width: 580px;">
    <div class="modal-header">
      <h3 class="modal-title">Create Job Posting (Admin)</h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('addJobModal')">&times;</button>
    </div>
    <form method="POST" action="jobs.php">
      <input type="hidden" name="action" value="add_job">
      <div class="modal-body">
        <div class="form-field">
          <label class="form-label">Job Title *</label>
          <input type="text" name="title" class="form-input-text" placeholder="e.g. Lead Full Stack Developer" required>
        </div>

        <div class="form-grid-2">
          <div class="form-field">
            <label class="form-label">Company Name *</label>
            <input type="text" name="company_name" class="form-input-text" placeholder="e.g. Dialog Axiata PLC" required>
          </div>
          <div class="form-field">
            <label class="form-label">Job Type</label>
            <select name="job_type" class="form-input-text">
              <option value="Full-time">Full-time</option>
              <option value="Part-time">Part-time</option>
              <option value="Contract">Contract</option>
              <option value="Remote">Remote</option>
              <option value="Internship">Internship</option>
            </select>
          </div>
        </div>

        <div class="form-grid-2">
          <div class="form-field">
            <label class="form-label">Location *</label>
            <input type="text" name="location" class="form-input-text" value="Colombo, Sri Lanka" required>
          </div>
          <div class="form-field">
            <label class="form-label">Salary Range</label>
            <input type="text" name="salary_range" class="form-input-text" value="Rs. 150,000 - Rs. 250,000 / month">
          </div>
        </div>

        <div class="form-field">
          <label class="form-label">Description & Requirements</label>
          <textarea name="description" class="form-input-text" rows="4" placeholder="Brief job responsibilities, skills, and eligibility requirements..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addJobModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Publish Job</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: View Job Details -->
<div class="modal-backdrop" id="jobDetailsModal">
  <div class="modal-dialog" style="max-width: 640px;">
    <div class="modal-header">
      <h3 class="modal-title" id="detailJobTitle">Job Details</h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('jobDetailsModal')">&times;</button>
    </div>
    <div class="modal-body" id="detailJobBody">
      <!-- Injected via JavaScript -->
    </div>
    <div class="modal-footer">
      <span id="detailJobActions"></span>
      <button type="button" class="btn btn-secondary" onclick="closeModal('jobDetailsModal')">Close</button>
    </div>
  </div>
</div>

<script>
function showJobDetails(job) {
  document.getElementById('detailJobTitle').textContent = job.title;
  const body = document.getElementById('detailJobBody');
  body.innerHTML = `
    <div style="background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 18px; margin-bottom: 20px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <div>
          <h4 style="font-size: 16px; font-weight: 700; color: var(--text-heading);">${job.company_name}</h4>
          <p style="color: var(--text-muted); font-size: 13px; margin-top: 2px;">📍 ${job.location} • 💼 ${job.job_type || 'Full-time'}</p>
        </div>
        <span class="status-badge ${job.status === 'Approved' ? 'badge-success' : (job.status === 'Rejected' ? 'badge-danger' : 'badge-warning')}">${job.status}</span>
      </div>
      <div style="margin-top: 12px; font-size: 14px; font-weight: 700; color: var(--primary-blue);">
        💰 ${job.salary_range || 'Competitive / Negotiable'}
      </div>
    </div>

    <div style="margin-bottom: 16px;">
      <h5 style="font-size: 14px; font-weight: 700; color: var(--text-heading); margin-bottom: 6px;">Job Description:</h5>
      <p style="color: var(--text-main); line-height: 1.6; font-size: 13.5px;">
        ${job.description || 'No detailed description provided for this job opening.'}
      </p>
    </div>

    <div style="margin-bottom: 12px;">
      <h5 style="font-size: 14px; font-weight: 700; color: var(--text-heading); margin-bottom: 6px;">Key Responsibilities & Scope:</h5>
      <ul style="padding-left: 20px; color: var(--text-main); font-size: 13.5px; line-height: 1.6;">
        <li>Collaborate with cross-functional teams to design, architect, and ship high quality software.</li>
        <li>Ensure stability, code maintainability, test coverage, and documentation.</li>
        <li>Participate in agile sprint plannings, reviews, and retrospectives.</li>
      </ul>
    </div>
  `;

  const actions = document.getElementById('detailJobActions');
  if (job.status === 'Pending Approval') {
    actions.innerHTML = `
      <a href="jobs.php?action=approve&id=${job.id}" class="btn btn-success">Approve Job</a>
      <a href="jobs.php?action=reject&id=${job.id}" class="btn btn-danger">Reject Job</a>
    `;
  } else {
    actions.innerHTML = '';
  }

  openModal('jobDetailsModal');
}
</script>

</main> <!-- Close admin-content -->
</div> <!-- Close admin-main -->
</div> <!-- Close admin-wrapper -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
