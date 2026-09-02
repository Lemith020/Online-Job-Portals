<?php
/**
 * JobPortal.lk - User Management
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'User Management';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'seeker';
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? 'Password123!';

        if (empty($name) || empty($email)) {
            set_flash("Name and Email are required fields.", "danger");
        } else {
            global $conn;
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            if ($conn) {
                $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role, phone, status) VALUES (?, ?, ?, ?, ?, 'Active')");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $hashed, $role, $phone);
                    mysqli_stmt_execute($stmt);
                }
            }
            add_activity("Created new user account: $name ($email) as " . ucfirst($role), "user");
            set_flash("User '{$name}' created successfully!", "success");
        }
        header("Location: " . BASE_URL . "/admin/users.php");
        exit;
    }

    if ($action === 'toggle_status') {
        $id = (int)($_POST['id'] ?? 0);
        $new_status = $_POST['status'] ?? 'Active';
        toggle_user_status($id, $new_status);
        add_activity("Updated user #$id status to $new_status", "user");
        set_flash("User status updated to {$new_status}.", "success");
        header("Location: " . BASE_URL . "/admin/users.php");
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        delete_user($id);
        add_activity("Deleted user account #$id", "user");
        set_flash("User #{$id} deleted successfully.", "success");
        header("Location: " . BASE_URL . "/admin/users.php");
        exit;
    }
}

$role_filter = $_GET['role'] ?? '';
$search = trim($_GET['search'] ?? '');
$users = get_all_users($role_filter, $search);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div class="page-title-group">
    <h1 class="page-title">User Management</h1>
    <p class="page-subtitle">View, search, suspend, or add users across all portal roles.</p>
  </div>
  <div class="page-actions">
    <button type="button" class="btn btn-primary" onclick="openAddUserModal()">
      <i class="fa-solid fa-user-plus"></i> Add New User
    </button>
  </div>
</div>

<!-- Filter Bar -->
<div class="card table-filter-card">
  <div class="card-body filter-bar-body">
    <form method="GET" action="users.php" class="filter-form">
      <div class="filter-group">
        <label>Filter by Role:</label>
        <select name="role" class="form-select" onchange="this.form.submit()">
          <option value="" <?php echo ($role_filter === '') ? 'selected' : ''; ?>>All Roles</option>
          <option value="seeker" <?php echo ($role_filter === 'seeker') ? 'selected' : ''; ?>>Job Seekers</option>
          <option value="company" <?php echo ($role_filter === 'company') ? 'selected' : ''; ?>>Employers / Companies</option>
          <option value="admin" <?php echo ($role_filter === 'admin') ? 'selected' : ''; ?>>Administrators</option>
        </select>
      </div>

      <div class="filter-group search-group">
        <label>Search Users:</label>
        <div class="input-with-icon">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" name="search" class="form-input" placeholder="Search by name, email, phone..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
      </div>

      <div class="filter-actions">
        <button type="submit" class="btn btn-secondary">Filter</button>
        <?php if (!empty($role_filter) || !empty($search)): ?>
          <a href="users.php" class="btn btn-outline">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<!-- Users Table Card -->
<div class="card">
  <div class="card-header flex-between">
    <h3 class="card-title">Registered Users (<?php echo count($users); ?>)</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table" id="usersTable">
        <thead>
          <tr>
            <th>User</th>
            <th>Role</th>
            <th>Contact Phone</th>
            <th>Status</th>
            <th>Registration Date</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr>
              <td colspan="6" class="text-center py-4">
                <div class="empty-state">
                  <i class="fa-solid fa-users-slash empty-icon"></i>
                  <p>No matching users found.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($users as $u): ?>
              <tr>
                <td>
                  <div class="user-row-info">
                    <div class="user-avatar-sm role-<?php echo htmlspecialchars($u['role']); ?>">
                      <span><?php echo strtoupper(substr($u['name'], 0, 1)); ?></span>
                    </div>
                    <div>
                      <strong class="user-display-name"><?php echo htmlspecialchars($u['name']); ?></strong>
                      <span class="user-display-email"><?php echo htmlspecialchars($u['email']); ?></span>
                    </div>
                  </div>
                </td>
                <td>
                  <?php if ($u['role'] === 'admin'): ?>
                    <span class="badge badge-purple">Admin</span>
                  <?php elseif ($u['role'] === 'company'): ?>
                    <span class="badge badge-indigo">Employer</span>
                  <?php else: ?>
                    <span class="badge badge-teal">Job Seeker</span>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($u['phone'] ?: 'N/A'); ?></td>
                <td>
                  <?php if ($u['status'] === 'Active'): ?>
                    <span class="status-pill status-active">● Active</span>
                  <?php elseif ($u['status'] === 'Suspended'): ?>
                    <span class="status-pill status-danger">● Suspended</span>
                  <?php else: ?>
                    <span class="status-pill status-warning">● <?php echo htmlspecialchars($u['status']); ?></span>
                  <?php endif; ?>
                </td>
                <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                <td class="text-right">
                  <div class="action-buttons">
                    <?php if ($u['status'] === 'Active'): ?>
                      <form method="POST" action="users.php" style="display:inline;" onsubmit="return confirm('Suspend this user account?');">
                        <input type="hidden" name="action" value="toggle_status">
                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                        <input type="hidden" name="status" value="Suspended">
                        <button type="submit" class="btn-icon text-amber" title="Suspend User">
                          <i class="fa-solid fa-ban"></i>
                        </button>
                      </form>
                    <?php else: ?>
                      <form method="POST" action="users.php" style="display:inline;">
                        <input type="hidden" name="action" value="toggle_status">
                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                        <input type="hidden" name="status" value="Active">
                        <button type="submit" class="btn-icon text-emerald" title="Activate User">
                          <i class="fa-solid fa-circle-check"></i>
                        </button>
                      </form>
                    <?php endif; ?>

                    <?php if ($u['role'] !== 'admin' || $u['id'] != ($_SESSION['user']['id'] ?? 1)): ?>
                      <form method="POST" action="users.php" style="display:inline;" onsubmit="return confirm('Permanently delete this user?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                        <button type="submit" class="btn-icon text-danger" title="Delete User">
                          <i class="fa-regular fa-trash-can"></i>
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

<script>
function openAddUserModal() {
  const content = `
    <form method="POST" action="users.php">
      <input type="hidden" name="action" value="create">
      <div class="form-group mb-3">
        <label class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-input" required placeholder="e.g. Kasun Silva">
      </div>
      <div class="form-group mb-3">
        <label class="form-label">Email Address <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-input" required placeholder="e.g. kasun@example.com">
      </div>
      <div class="form-group mb-3">
        <label class="form-label">Role <span class="text-danger">*</span></label>
        <select name="role" class="form-select" required>
          <option value="seeker">Job Seeker</option>
          <option value="company">Employer / Company</option>
          <option value="admin">Administrator</option>
        </select>
      </div>
      <div class="form-group mb-3">
        <label class="form-label">Phone Number</label>
        <input type="text" name="phone" class="form-input" placeholder="+94 77 123 4567">
      </div>
      <div class="form-group mb-4">
        <label class="form-label">Initial Password</label>
        <input type="password" name="password" class="form-input" value="Password123!" required>
      </div>
      <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn btn-secondary" onclick="closeAdminModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Create User Account</button>
      </div>
    </form>
  `;
  openAdminModal('Add New User Account', content);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
