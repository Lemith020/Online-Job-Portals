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

<!-- Main Layout Container -->
<div class="main-content" style="margin-top: 60px; margin-left: 240px; padding: 28px; width: calc(100% - 240px); box-sizing: border-box; min-height: calc(100vh - 60px);">

  <!-- Page Header -->
  <div style="margin-bottom: 24px;">
    <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0;">Job Categories</h1>
    <p style="font-size: 13.5px; color: #64748b; margin-top: 4px;">Add, organize, and manage job classification sectors across the portal.</p>
  </div>

  <!-- Inline Add / Edit Category Form Card -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; margin-bottom: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 16px 0;">
      <?php echo $edit_category ? '✏️ Edit Job Category' : '➕ Add New Job Category'; ?>
    </h3>
    
    <form method="POST" action="categories.php" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin: 0;">
      <input type="hidden" name="action" value="<?php echo $edit_category ? 'update' : 'create'; ?>">
      <?php if ($edit_category): ?>
        <input type="hidden" name="id" value="<?php echo $edit_category['category_id']; ?>">
      <?php endif; ?>

      <div style="flex: 1; min-width: 280px;">
        <input type="text" name="name" required placeholder="Enter Category Name (e.g. Software Engineering, Marketing, Healthcare)" 
               value="<?php echo htmlspecialchars($edit_category['category_name'] ?? ''); ?>" 
               style="width: 100%; height: 42px; padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; box-sizing: border-box; outline: none;">
      </div>

      <div style="display: flex; gap: 8px;">
        <button type="submit" style="height: 42px; padding: 0 20px; background: #2563eb; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-check"></i> <?php echo $edit_category ? 'Update Category' : 'Save Category'; ?>
        </button>

        <?php if ($edit_category): ?>
          <a href="categories.php" style="height: 42px; padding: 0 16px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">Cancel</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Categories Grid Display -->
  <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin-bottom: 16px;">Existing Categories (<?php echo count($categories); ?>)</h3>

  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px;">
    <?php if (empty($categories)): ?>
      <div style="grid-column: 1 / -1; background: #fff; padding: 40px; text-align: center; border-radius: 12px; border: 1px solid #e2e8f0; color: #64748b;">
        No categories found in database. Use the form above to add your first category!
      </div>
    <?php else: ?>
      <?php foreach ($categories as $cat): ?>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; justify-content: space-between; align-items: center;">
          
          <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 16px;">
              <i class="fa-solid fa-folder"></i>
            </div>
            <div>
              <h4 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 2px 0;"><?php echo htmlspecialchars($cat['category_name']); ?></h4>
              <span style="font-size: 12px; color: #64748b;">
                <i class="fa-solid fa-briefcase"></i> <?php echo number_format($cat['job_count'] ?? 0); ?> Jobs
              </span>
            </div>
          </div>

          <!-- Actions -->
          <div style="display: flex; gap: 6px;">
            <!-- Edit Trigger -->
            <a href="categories.php?edit=<?php echo $cat['category_id']; ?>" style="background: #f1f5f9; color: #2563eb; border-radius: 6px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; text-decoration: none;" title="Edit Category">
              <i class="fa-regular fa-pen-to-square"></i>
            </a>

            <!-- Delete Trigger -->
            <form method="POST" action="categories.php" style="display:inline;" onsubmit="return confirm('Delete this category?');">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?php echo $cat['category_id']; ?>">
              <button type="submit" style="background: #fee2e2; color: #dc2626; border: none; border-radius: 6px; width: 32px; height: 32px; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Delete Category">
                <i class="fa-regular fa-trash-can"></i>
              </button>
            </form>
          </div>

        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>