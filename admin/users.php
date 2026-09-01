<?php
/**
 * JobPortal.lk - User Management (Admin)
 * Member 1 - Admin UI
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'User Management';

// Handle Action Requests (Activate, Suspend, Delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $target_id = (int)$_GET['id'];

    if ($action === 'activate') {
        update_user_status($target_id, 'Active');
        set_flash('User status changed to Active.', 'success');
    } elseif ($action === 'suspend') {
        update_user_status($target_id, 'Suspended');
        set_flash('User has been Suspended.', 'warning');
    } elseif ($action === 'delete') {
        delete_user($target_id);
        set_flash('User has been permanently deleted.', 'error');
    }

    header('Location: users.php');
    exit;
}

// Filters & Search
$search = trim($_GET['search'] ?? '');
$role_filter = trim($_GET['role'] ?? 'all');
$status_filter = trim($_GET['status'] ?? 'all');

$users = get_all_users($search, $role_filter, $status_filter);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="page-header">
  <div class="page-title-group">
    <h1>User Management</h1>
    <p>Monitor, activate, suspend, or manage roles for registered candidates and employers.</p>
  </div>
</div>

<!-- Main Table Card -->
<div class="data-table-card">
  <!-- Table Search and Filtering Toolbar -->
  <form method="GET" action="users.php" class="table-toolbar">
    <div class="toolbar-search">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search user name or email..." data-table-search="usersTable">
    </div>

    <div class="toolbar-filters">
      <!-- Role Filter Tabs -->
      <div class="filter-tab-group">
        <a href="users.php?role=all&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($role_filter === 'all' || empty($role_filter)) ? 'active' : ''; ?>">All</a>
        <a href="users.php?role=seeker&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($role_filter === 'seeker') ? 'active' : ''; ?>">Job Seeker</a>
        <a href="users.php?role=company&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($role_filter === 'company') ? 'active' : ''; ?>">Company</a>
        <a href="users.php?role=admin&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($role_filter === 'admin') ? 'active' : ''; ?>">Admin</a>
      </div>

      <!-- Status Dropdown -->
      <select name="status" class="select-filter" onchange="this.form.submit()">
        <option value="all" <?php echo ($status_filter === 'all') ? 'selected' : ''; ?>>All Statuses</option>
        <option value="Active" <?php echo ($status_filter === 'Active') ? 'selected' : ''; ?>>Active</option>
        <option value="Suspended" <?php echo ($status_filter === 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
      </select>

      <?php if (!empty($search) || $role_filter !== 'all' || $status_filter !== 'all'): ?>
        <a href="users.php" class="btn btn-secondary btn-sm">Reset</a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Responsive Users Table -->
  <div class="table-responsive">
    <table class="custom-table" id="usersTable">
      <thead>
        <tr>
          <th>User Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Phone Number</th>
          <th>Status</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($users)): ?>
          <tr>
            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
              No users found matching the specified filters.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($users as $user): ?>
            <tr>
              <td>
                <div class="user-cell">
                  <div class="user-cell-avatar">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                  </div>
                  <div>
                    <div class="primary-text"><?php echo htmlspecialchars($user['name']); ?></div>
                  </div>
                </div>
              </td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td><?php echo render_role_badge($user['role']); ?></td>
              <td><?php echo htmlspecialchars($user['phone'] ?? '+94 77 000 0000'); ?></td>
              <td><?php echo render_status_badge($user['status']); ?></td>
              <td style="text-align: right;">
                <div class="action-btn-group" style="justify-content: flex-end;">
                  <?php if ($user['status'] === 'Active'): ?>
                    <a href="users.php?action=suspend&id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm" title="Suspend account">
                      Suspend
                    </a>
                  <?php else: ?>
                    <a href="users.php?action=activate&id=<?php echo $user['id']; ?>" class="btn btn-success btn-sm" title="Activate account">
                      Activate
                    </a>
                  <?php endif; ?>

                  <button type="button" class="btn btn-danger btn-sm" onclick="confirmAction('Are you sure you want to permanently delete user <?= htmlspecialchars($user['name'], ENT_QUOTES) ?>?', 'users.php?action=delete&id=<?= $user['id'] ?>')">
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

  <!-- Pagination Controls (Matching Blueprint) -->
  <div class="table-pagination">
    <span class="secondary-text">Showing <?php echo count($users); ?> of <?php echo count($users); ?> registered users</span>
    <div class="pagination-controls">
      <button class="page-btn page-btn-wide">Previous</button>
      <button class="page-btn active">1</button>
      <button class="page-btn">2</button>
      <button class="page-btn page-btn-wide">Next</button>
    </div>
  </div>
</div>

</main> <!-- Close admin-content -->
</div> <!-- Close admin-main -->
</div> <!-- Close admin-wrapper -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
