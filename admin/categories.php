<?php
/**
 * JobPortal.lk - Category Management (Simple Inline Form Version)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Category Management';
$page_css = BASE_URL . '/assets/css/admin_page.css';

// Edit mode detection
$edit_category = null;

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $name = trim($_POST['name'] ?? '');
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

        if (empty($name)) {
            set_flash("Category name is required.", "danger");
        } else {
            save_category($name, $id);
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

// Handle GET edit trigger
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    global $conn;
    $res = mysqli_query($conn, "SELECT * FROM categories WHERE category_id = $edit_id");
    if ($res && mysqli_num_rows($res) > 0) {
        $edit_category = mysqli_fetch_assoc($res);
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
    <p class="page-subtitle">Add, organize, and manage job classification sectors across the portal.</p>
  </div>
</div>

<!-- Inline Add / Edit Category Form Card -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <?php echo $edit_category ? '✏️ Edit Job Category' : '➕ Add New Job Category'; ?>
    </h3>
  </div>
  <div class="card-body">
    <form method="POST" action="categories.php" class="sub-form-inline">
      <input type="hidden" name="action" value="<?php echo $edit_category ? 'update' : 'create'; ?>">
      <?php if ($edit_category): ?>
        <input type="hidden" name="id" value="<?php echo $edit_category['category_id']; ?>">
      <?php endif; ?>

      <div class="form-field-group field-large">
        <label>Category Name <span class="text-danger">*</span></label>
        <input type="text" name="name" required placeholder="Enter Category Name (e.g. Software Engineering, Marketing, Healthcare)" 
               value="<?php echo htmlspecialchars($edit_category['category_name'] ?? ''); ?>" 
               class="form-input">
      </div>

      <div class="form-actions-inline">
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-check"></i> <?php echo $edit_category ? 'Update Category' : 'Save Category'; ?>
        </button>

        <?php if ($edit_category): ?>
          <a href="categories.php" class="btn btn-secondary">Cancel</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<!-- Categories Grid Display -->
<h3 class="section-heading mb-3">Existing Categories (<?php echo count($categories); ?>)</h3>

<div class="categories-cards-grid">
  <?php if (empty($categories)): ?>
    <div class="empty-state card text-center py-4" style="grid-column: 1 / -1;">
      <i class="fa-solid fa-folder-open empty-icon"></i>
      <p>No categories found in database. Use the form above to add your first category!</p>
    </div>
  <?php else: ?>
    <?php foreach ($categories as $cat): ?>
      <div class="category-card-item">
        <div class="category-card-info">
          <div class="category-icon-box">
            <i class="fa-solid fa-folder"></i>
          </div>
          <div>
            <h4 class="category-name"><?php echo htmlspecialchars($cat['category_name']); ?></h4>
            <span class="category-count">
              <i class="fa-solid fa-briefcase"></i> <?php echo number_format($cat['job_count'] ?? 0); ?> Jobs
            </span>
          </div>
        </div>

        <!-- Actions -->
        <div class="action-buttons">
          <!-- Edit Trigger -->
          <a href="categories.php?edit=<?php echo $cat['category_id']; ?>" class="btn-icon text-primary" title="Edit Category">
            <i class="fa-regular fa-pen-to-square"></i>
          </a>

          <!-- Delete Trigger -->
          <form method="POST" action="categories.php" style="display:inline;" onsubmit="return confirm('Delete this category?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?php echo $cat['category_id']; ?>">
            <button type="submit" class="btn-icon text-danger" title="Delete Category">
              <i class="fa-regular fa-trash-can"></i>
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
