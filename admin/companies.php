<?php
/**
 * JobPortal.lk - Company Management (Admin)
 * Member 1 - Admin UI
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Company Management';

// Handle Action Requests (Approve, Reject, Suspend, Delete, Add)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_company') {
    $name = trim($_POST['company_name'] ?? '');
    $industry = trim($_POST['industry_type'] ?? 'Software');
    $location = trim($_POST['location'] ?? 'Colombo, Sri Lanka');
    $email = trim($_POST['owner_email'] ?? '');

    if (!empty($name) && !empty($email)) {
        if ($db) {
            try {
                $stmt = $db->prepare("INSERT INTO companies (company_name, industry_type, location, owner_email, status) VALUES (?, ?, ?, ?, 'Approved')");
                $stmt->execute([$name, $industry, $location, $email]);
            } catch (Exception $e) {}
        }
        $new_id = count($_SESSION['mock_companies']) + 1;
        $_SESSION['mock_companies'][] = [
            'id' => $new_id,
            'company_name' => $name,
            'industry_type' => $industry,
            'location' => $location,
            'owner_email' => $email,
            'status' => 'Approved'
        ];
        add_activity("Company created manually: $name", 'company');
        set_flash("Company '$name' added successfully!", 'success');
    }
    header('Location: companies.php');
    exit;
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $target_id = (int)$_GET['id'];

    if ($action === 'approve') {
        update_company_status($target_id, 'Approved');
        set_flash('Company has been approved successfully.', 'success');
    } elseif ($action === 'reject') {
        update_company_status($target_id, 'Rejected');
        set_flash('Company registration has been rejected.', 'warning');
    } elseif ($action === 'suspend') {
        update_company_status($target_id, 'Suspended');
        set_flash('Company account suspended.', 'warning');
    } elseif ($action === 'delete') {
        delete_company($target_id);
        set_flash('Company profile removed.', 'error');
    }

    header('Location: companies.php');
    exit;
}

// Filters & Search
$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? 'all');
$sort = trim($_GET['sort'] ?? 'newest');

$companies = get_all_companies($search, $status_filter, $sort);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="page-header">
  <div class="page-title-group">
    <h1>Company Management</h1>
    <p>Verify employers, manage company directory listings, and review corporate profiles.</p>
  </div>
  <div>
    <button type="button" class="btn btn-primary" onclick="openModal('addCompanyModal')">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>+ Add Company</span>
    </button>
  </div>
</div>

<!-- Main Table Card -->
<div class="data-table-card">
  <!-- Table Toolbar -->
  <form method="GET" action="companies.php" class="table-toolbar">
    <div class="toolbar-search">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search company name or owner email..." data-table-search="companiesTable">
    </div>

    <div class="toolbar-filters">
      <!-- Status Tabs -->
      <div class="filter-tab-group">
        <a href="companies.php?status=all&sort=<?php echo urlencode($sort); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($status_filter === 'all' || empty($status_filter)) ? 'active' : ''; ?>">All Companies</a>
        <a href="companies.php?status=Pending+Approval&sort=<?php echo urlencode($sort); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($status_filter === 'Pending Approval') ? 'active' : ''; ?>">Pending Approval</a>
        <a href="companies.php?status=Approved&sort=<?php echo urlencode($sort); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($status_filter === 'Approved') ? 'active' : ''; ?>">Approved</a>
        <a href="companies.php?status=Suspended&sort=<?php echo urlencode($sort); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($status_filter === 'Suspended') ? 'active' : ''; ?>">Suspended</a>
      </div>

      <!-- Sort Selection -->
      <select name="sort" class="select-filter" onchange="this.form.submit()">
        <option value="newest" <?php echo ($sort === 'newest') ? 'selected' : ''; ?>>Newest First</option>
        <option value="alphabetical" <?php echo ($sort === 'alphabetical') ? 'selected' : ''; ?>>Alphabetical</option>
      </select>

      <?php if (!empty($search) || $status_filter !== 'all' || $sort !== 'newest'): ?>
        <a href="companies.php" class="btn btn-secondary btn-sm">Reset</a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Responsive Companies Table -->
  <div class="table-responsive">
    <table class="custom-table" id="companiesTable">
      <thead>
        <tr>
          <th>Company Name</th>
          <th>Industry Type</th>
          <th>Location</th>
          <th>Owner Email</th>
          <th>Status</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($companies)): ?>
          <tr>
            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
              No companies found matching the specified filters.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($companies as $comp): ?>
            <tr>
              <td>
                <div class="user-cell">
                  <div class="user-cell-avatar" style="background:#e0f2fe; color:#0284c7;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                      <line x1="9" y1="22" x2="9" y2="22.01"></line>
                      <line x1="15" y1="22" x2="15" y2="22.01"></line>
                    </svg>
                  </div>
                  <div>
                    <div class="primary-text"><?php echo htmlspecialchars($comp['company_name']); ?></div>
                  </div>
                </div>
              </td>
              <td><?php echo htmlspecialchars($comp['industry_type']); ?></td>
              <td><?php echo htmlspecialchars($comp['location']); ?></td>
              <td><?php echo htmlspecialchars($comp['owner_email']); ?></td>
              <td><?php echo render_status_badge($comp['status']); ?></td>
              <td style="text-align: right;">
                <div class="action-btn-group" style="justify-content: flex-end;">
                  <?php if ($comp['status'] === 'Pending Approval'): ?>
                    <a href="companies.php?action=approve&id=<?php echo $comp['id']; ?>" class="btn btn-success btn-sm" title="Approve Company">
                      Approve
                    </a>
                    <a href="companies.php?action=reject&id=<?php echo $comp['id']; ?>" class="btn btn-danger btn-sm" title="Reject Company">
                      Reject
                    </a>
                  <?php elseif ($comp['status'] === 'Approved'): ?>
                    <a href="companies.php?action=suspend&id=<?php echo $comp['id']; ?>" class="btn btn-secondary btn-sm" title="Suspend Company">
                      Suspend
                    </a>
                  <?php else: ?>
                    <a href="companies.php?action=approve&id=<?php echo $comp['id']; ?>" class="btn btn-success btn-sm" title="Re-approve">
                      Approve
                    </a>
                  <?php endif; ?>

                  <button type="button" class="btn btn-secondary btn-sm" onclick="viewCompanyJobs('<?php echo htmlspecialchars(addslashes($comp['company_name'])); ?>')">
                    View Jobs
                  </button>

                  <button type="button" class="btn btn-danger btn-sm" onclick="confirmAction('Are you sure you want to delete <?= htmlspecialchars($comp['company_name'], ENT_QUOTES) ?>?', 'companies.php?action=delete&id=<?= $comp['id'] ?>')">
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
    <span class="secondary-text">Showing <?php echo count($companies); ?> companies</span>
    <div class="pagination-controls">
      <button class="page-btn page-btn-wide">Previous</button>
      <button class="page-btn active">1</button>
      <button class="page-btn">2</button>
      <button class="page-btn page-btn-wide">Next</button>
    </div>
  </div>
</div>

<!-- Modal: Add Company Form -->
<div class="modal-backdrop" id="addCompanyModal">
  <div class="modal-dialog">
    <div class="modal-header">
      <h3 class="modal-title">Register New Employer / Company</h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('addCompanyModal')">&times;</button>
    </div>
    <form method="POST" action="companies.php">
      <input type="hidden" name="action" value="add_company">
      <div class="modal-body">
        <div class="form-field">
          <label class="form-label">Company Name *</label>
          <input type="text" name="company_name" class="form-input-text" placeholder="e.g. Sysco LABS Sri Lanka" required>
        </div>

        <div class="form-field">
          <label class="form-label">Industry Type *</label>
          <select name="industry_type" class="form-input-text" required>
            <option value="Software">Software & Information Technology</option>
            <option value="Telecommunications">Telecommunications</option>
            <option value="Finance">Banking & Finance</option>
            <option value="Healthcare">Healthcare & Pharmaceuticals</option>
            <option value="Marketing">Marketing & Advertising</option>
            <option value="Education">Education & Academia</option>
          </select>
        </div>

        <div class="form-field">
          <label class="form-label">Location *</label>
          <input type="text" name="location" class="form-input-text" value="Colombo, Sri Lanka" required>
        </div>

        <div class="form-field">
          <label class="form-label">Owner Email *</label>
          <input type="email" name="owner_email" class="form-input-text" placeholder="hr@company.com" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addCompanyModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Company</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: View Company Jobs -->
<div class="modal-backdrop" id="viewJobsModal">
  <div class="modal-dialog" style="max-width: 600px;">
    <div class="modal-header">
      <h3 class="modal-title" id="jobsModalTitle">Company Jobs</h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('viewJobsModal')">&times;</button>
    </div>
    <div class="modal-body">
      <p style="margin-bottom: 12px; color: var(--text-muted);">Active job listings published by this company:</p>
      <div id="companyJobsContainer">
        <!-- Filled dynamically by JavaScript -->
      </div>
    </div>
    <div class="modal-footer">
      <a href="jobs.php" class="btn btn-primary btn-sm">Go to Job Moderation</a>
      <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('viewJobsModal')">Close</button>
    </div>
  </div>
</div>

<script>
function viewCompanyJobs(companyName) {
  document.getElementById('jobsModalTitle').textContent = companyName + ' - Active Listings';
  const container = document.getElementById('companyJobsContainer');
  container.innerHTML = `
    <div style="border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 14px; margin-bottom: 10px;">
      <div style="font-weight: 700; color: var(--text-heading);">Senior Software Engineer</div>
      <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 3px;">Full-time • Colombo, Sri Lanka • Rs. 150,000 - Rs. 250,000 / month</div>
      <div style="margin-top: 8px;"><span class="status-badge badge-success">Approved</span></div>
    </div>
    <div style="border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 14px;">
      <div style="font-weight: 700; color: var(--text-heading);">Technical Lead / Cloud Architect</div>
      <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 3px;">Full-time • Colombo / Remote • Rs. 350,000 - Rs. 450,000 / month</div>
      <div style="margin-top: 8px;"><span class="status-badge badge-warning">Pending Approval</span></div>
    </div>
  `;
  openModal('viewJobsModal');
}
</script>

</main> <!-- Close admin-content -->
</div> <!-- Close admin-main -->
</div> <!-- Close admin-wrapper -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
