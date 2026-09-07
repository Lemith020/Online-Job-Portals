<?php
/**
 * JobPortal.lk - Company Management & Moderation
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Company Management';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $id = (int)($_POST['id'] ?? 0);
        $new_status = $_POST['status'] ?? 'Approved';
        update_company_status($id, $new_status);
        add_activity("Updated company #$id verification status to $new_status", "company");
        set_flash("Company verification status updated to {$new_status}.", "success");
        header("Location: " . BASE_URL . "/admin/companies.php");
        exit;
    }

    if ($action === 'create') {
        $name = trim($_POST['company_name'] ?? '');
        $industry = trim($_POST['industry_type'] ?? 'IT');
        $location = trim($_POST['location'] ?? 'Colombo');
        $email = trim($_POST['owner_email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($name) || empty($email)) {
            set_flash("Company Name and Email are required.", "danger");
        } else {
            global $conn;
            if ($conn) {
                $stmt = mysqli_prepare($conn, "INSERT INTO companies (company_name, industry_type, location, owner_email, phone, status) VALUES (?, ?, ?, ?, ?, 'Approved')");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssss", $name, $industry, $location, $email, $phone);
                    mysqli_stmt_execute($stmt);
                }
            }
            add_activity("Added company profile: $name", "company");
            set_flash("Company '{$name}' created and verified!", "success");
        }
        header("Location: " . BASE_URL . "/admin/companies.php");
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        delete_company($id);
        add_activity("Deleted company profile #$id", "company");
        set_flash("Company #{$id} deleted successfully.", "success");
        header("Location: " . BASE_URL . "/admin/companies.php");
        exit;
    }
}

$status_filter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');
$companies = get_all_companies($status_filter, $search);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div class="page-title-group">
    <h1 class="page-title">Company Moderation</h1>
    <p class="page-subtitle">Verify employer credentials, approve business profiles, and manage corporate accounts.</p>
  </div>
  <div class="page-actions">
    <button type="button" class="btn btn-primary" onclick="openAddCompanyModal()">
      <i class="fa-solid fa-plus"></i> Add New Company
    </button>
  </div>
</div>

<!-- Filter Bar -->
<div class="card table-filter-card">
  <div class="card-body filter-bar-body">
    <form method="GET" action="companies.php" class="filter-form">
      <div class="filter-group">
        <label>Status:</label>
        <select name="status" class="form-select" onchange="this.form.submit()">
          <option value="" <?php echo ($status_filter === '') ? 'selected' : ''; ?>>All Statuses</option>
          <option value="Approved" <?php echo ($status_filter === 'Approved') ? 'selected' : ''; ?>>Approved / Verified</option>
          <option value="Pending Approval" <?php echo ($status_filter === 'Pending Approval') ? 'selected' : ''; ?>>Pending Review</option>
          <option value="Suspended" <?php echo ($status_filter === 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
          <option value="Rejected" <?php echo ($status_filter === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
        </select>
      </div>

      <div class="filter-group search-group">
        <label>Search Companies:</label>
        <div class="input-with-icon">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" name="search" class="form-input" placeholder="Search company name, industry, location..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
      </div>

      <div class="filter-actions">
        <button type="submit" class="btn btn-secondary">Filter</button>
        <?php if (!empty($status_filter) || !empty($search)): ?>
          <a href="companies.php" class="btn btn-outline">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<!-- Companies Table Card -->
<div class="card">
  <div class="card-header flex-between">
    <h3 class="card-title">Registered Companies (<?php echo count($companies); ?>)</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Company</th>
            <th>Industry Sector</th>
            <th>Headquarters</th>
            <th>Contact Info</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($companies)): ?>
            <tr>
              <td colspan="6" class="text-center py-4">
                <div class="empty-state">
                  <i class="fa-solid fa-building-circle-xmark empty-icon"></i>
                  <p>No companies found matching criteria.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($companies as $c): ?>
              <tr>
                <td>
                  <div class="user-row-info">
                    <div class="company-avatar-box">
                      <i class="fa-solid fa-building"></i>
                    </div>
                    <div>
                      <strong class="company-display-name"><?php echo htmlspecialchars($c['company_name']); ?></strong>
                      <span class="company-display-email"><?php echo htmlspecialchars($c['owner_email']); ?></span>
                    </div>
                  </div>
                </td>
                <td><span class="badge badge-secondary"><?php echo htmlspecialchars($c['industry_type']); ?></span></td>
                <td>
                  <span class="location-badge">
                    <i class="fa-solid fa-location-dot"></i>
                    <?php echo htmlspecialchars($c['location']); ?>
                  </span>
                </td>
                <td><?php echo htmlspecialchars($c['phone'] ?: 'N/A'); ?></td>
                <td>
                  <?php if ($c['status'] === 'Approved'): ?>
                    <span class="status-pill status-active">✓ Approved</span>
                  <?php elseif ($c['status'] === 'Pending Approval'): ?>
                    <span class="status-pill status-warning">⏳ Pending Review</span>
                  <?php elseif ($c['status'] === 'Suspended'): ?>
                    <span class="status-pill status-danger">✕ Suspended</span>
                  <?php else: ?>
                    <span class="status-pill status-secondary">✕ Rejected</span>
                  <?php endif; ?>
                </td>
                <td class="text-right">
                  <div class="action-buttons">
                    <?php if ($c['status'] === 'Pending Approval'): ?>
                      <form method="POST" action="companies.php" style="display:inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                        <input type="hidden" name="status" value="Approved">
                        <button type="submit" class="btn btn-sm btn-success" title="Approve Company">
                          <i class="fa-solid fa-check"></i> Approve
                        </button>
                      </form>
                      <form method="POST" action="companies.php" style="display:inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                        <input type="hidden" name="status" value="Rejected">
                        <button type="submit" class="btn btn-sm btn-danger" title="Reject Company">
                          <i class="fa-solid fa-xmark"></i>
                        </button>
                      </form>
                    <?php elseif ($c['status'] === 'Approved'): ?>
                      <form method="POST" action="companies.php" style="display:inline;" onsubmit="return confirm('Suspend this company?');">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                        <input type="hidden" name="status" value="Suspended">
                        <button type="submit" class="btn-icon text-amber" title="Suspend Company">
                          <i class="fa-solid fa-ban"></i>
                        </button>
                      </form>
                    <?php else: ?>
                      <form method="POST" action="companies.php" style="display:inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                        <input type="hidden" name="status" value="Approved">
                        <button type="submit" class="btn-icon text-emerald" title="Reactivate / Approve">
                          <i class="fa-solid fa-circle-check"></i>
                        </button>
                      </form>
                    <?php endif; ?>

                    <form method="POST" action="companies.php" style="display:inline;" onsubmit="return confirm('Permanently delete this company profile?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                      <button type="submit" class="btn-icon text-danger" title="Delete Company">
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

<script>
function openAddCompanyModal() {
  const content = `
    <form method="POST" action="companies.php">
      <input type="hidden" name="action" value="create">
      <div class="form-group mb-3">
        <label class="form-label">Company Name <span class="text-danger">*</span></label>
        <input type="text" name="company_name" class="form-input" required placeholder="e.g. Sysco LABS">
      </div>
      <div class="form-group mb-3">
        <label class="form-label">Industry Type</label>
        <input type="text" name="industry_type" class="form-input" placeholder="e.g. Information Technology">
      </div>
      <div class="form-group mb-3">
        <label class="form-label">Headquarters / Location</label>
        <input type="text" name="location" class="form-input" placeholder="e.g. Colombo 03, Sri Lanka">
      </div>
      <div class="form-group mb-3">
        <label class="form-label">Corporate Contact Email <span class="text-danger">*</span></label>
        <input type="email" name="owner_email" class="form-input" required placeholder="e.g. careers@company.com">
      </div>
      <div class="form-group mb-4">
        <label class="form-label">Phone Number</label>
        <input type="text" name="phone" class="form-input" placeholder="+94 11 234 5678">
      </div>
      <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn btn-secondary" onclick="closeAdminModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Company Profile</button>
      </div>
    </form>
  `;
  openAdminModal('Register Employer Company', content);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
