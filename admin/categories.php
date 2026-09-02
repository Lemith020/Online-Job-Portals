<?php
/**
 * JobPortal.lk - Category Management
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Category Management';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? 'briefcase');
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

        if (empty($name)) {
            set_flash("Category name is required.", "danger");
        } else {
            save_category($name, $icon, $id);
            add_activity(($id ? "Updated" : "Created") . " job category: $name", "category");
            set_flash("Category '{$name}' " . ($id ? "updated" : "created") . " successfully!", "success");
        }
        header("Location: " . BASE_URL . "/admin/categories.php");
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        delete_category($id);
        add_activity("Deleted job category #$id", "category");
        set_flash("Category deleted successfully.", "success");
        header("Location: " . BASE_URL . "/admin/categories.php");
        exit;
    }
}

$categories = get_all_categories_admin();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div class="page-title-group">
    <h1 class="page-title">Job Categories</h1>
    <p class="page-subtitle">Organize and manage job classification sectors across the portal.</p>
  </div>
  <div class="page-actions">
    <button type="button" class="btn btn-primary" onclick="openAddCategoryModal()">
      <i class="fa-solid fa-plus"></i> Add New Category
    </button>
  </div>
</div>

<!-- Categories Grid -->
<div class="categories-grid">
  <?php foreach ($categories as $cat): ?>
    <div class="category-card">
      <div class="category-card-header">
        <div class="category-icon-box">
          <i class="fa-solid fa-<?php echo htmlspecialchars($cat['icon'] ?: 'briefcase'); ?>"></i>
        </div>
        <div class="category-actions">
          <button class="btn-icon text-primary" title="Edit Category" onclick='openEditCategoryModal(<?php echo json_encode($cat); ?>)'>
            <i class="fa-regular fa-pen-to-square"></i>
          </button>
          <form method="POST" action="categories.php" style="display:inline;" onsubmit="return confirm('Delete this category?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
            <button type="submit" class="btn-icon text-danger" title="Delete Category">
              <i class="fa-regular fa-trash-can"></i>
            </button>
          </form>
        </div>
      </div>
      <div class="category-card-body">
        <h3 class="category-name"><?php echo htmlspecialchars($cat['name']); ?></h3>
        <span class="category-jobs-count">
          <i class="fa-solid fa-briefcase"></i> <?php echo number_format($cat['job_count'] ?? 0); ?> Active Jobs
        </span>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<script>
function openAddCategoryModal() {
  const content = `
    <form method="POST" action="categories.php">
      <input type="hidden" name="action" value="create">
      <div class="form-group mb-3">
        <label class="form-label">Category Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-input" required placeholder="e.g. AI & Machine Learning">
      </div>
      <div class="form-group mb-4">
        <label class="form-label">FontAwesome Icon Name</label>
        <input type="text" name="icon" class="form-input" value="briefcase" placeholder="e.g. code, brain, cloud, chart-line">
        <small class="text-muted d-block mt-1">Provide icon slug without 'fa-' prefix (e.g. 'code', 'database', 'users').</small>
      </div>
      <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn btn-secondary" onclick="closeAdminModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Category</button>
      </div>
    </form>
  `;
  openAdminModal('Add New Job Category', content);
}

function openEditCategoryModal(cat) {
  const content = `
    <form method="POST" action="categories.php">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" value="${cat.id}">
      <div class="form-group mb-3">
        <label class="form-label">Category Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-input" required value="${cat.name}">
      </div>
      <div class="form-group mb-4">
        <label class="form-label">FontAwesome Icon Name</label>
        <input type="text" name="icon" class="form-input" value="${cat.icon || 'briefcase'}">
      </div>
      <div class="modal-footer px-0 pb-0">
        <button type="button" class="btn btn-secondary" onclick="closeAdminModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Category</button>
      </div>
    </form>
  `;
  openAdminModal('Edit Category', content);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
