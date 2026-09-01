<?php
/**
 * JobPortal.lk - Category Management (Admin)
 * Member 1 - Admin UI
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Job Category Management';

// Handle Add / Edit Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $category_name = trim($_POST['category_name'] ?? '');
    $cat_id = (int)($_POST['category_id'] ?? 0);

    if (!empty($category_name)) {
        save_category($cat_id, $category_name);
        if ($cat_id > 0) {
            set_flash("Category updated to '$category_name'.", 'success');
        } else {
            set_flash("New category '$category_name' created successfully.", 'success');
        }
    }
    header('Location: categories.php');
    exit;
}

// Handle Delete Category
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $cat_id = (int)$_GET['id'];
    delete_category($cat_id);
    set_flash('Category deleted successfully.', 'error');
    header('Location: categories.php');
    exit;
}

// Filters & Sort
$search = trim($_GET['search'] ?? '');
$sort = trim($_GET['sort'] ?? 'name');

$categories = get_all_categories($search, $sort);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="page-header">
  <div class="page-title-group">
    <h1>Job Category Management</h1>
    <p>Organize industries and job classifications displayed on candidate search and post forms.</p>
  </div>
  <div>
    <button type="button" class="btn btn-primary" onclick="openAddCategoryModal()">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>+ Add Category</span>
    </button>
  </div>
</div>

<!-- Main Table Card -->
<div class="data-table-card">
  <!-- Table Toolbar -->
  <form method="GET" action="categories.php" class="table-toolbar">
    <div class="toolbar-search">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search category name..." data-table-search="categoriesTable">
    </div>

    <div class="toolbar-filters">
      <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Sort By:</span>
      <div class="filter-tab-group">
        <a href="categories.php?sort=name&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($sort === 'name') ? 'active' : ''; ?>">Alphabetical</a>
        <a href="categories.php?sort=jobs&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($sort === 'jobs') ? 'active' : ''; ?>">Most Jobs</a>
        <a href="categories.php?sort=recent&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($sort === 'recent') ? 'active' : ''; ?>">Recently Added</a>
      </div>

      <?php if (!empty($search) || $sort !== 'name'): ?>
        <a href="categories.php" class="btn btn-secondary btn-sm">Reset</a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Responsive Categories Table -->
  <div class="table-responsive">
    <table class="custom-table" id="categoriesTable">
      <thead>
        <tr>
          <th>Category Name</th>
          <th>Number of Jobs</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($categories)): ?>
          <tr>
            <td colspan="3" style="text-align: center; padding: 40px; color: var(--text-muted);">
              No job categories found matching your query.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($categories as $cat): ?>
            <tr>
              <td>
                <div class="user-cell">
                  <div class="user-cell-avatar" style="background:#ede9fe; color:#7c3aed;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                      <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                  </div>
                  <div>
                    <div class="primary-text" style="font-size: 14.5px;"><?php echo htmlspecialchars($cat['name']); ?></div>
                  </div>
                </div>
              </td>
              <td>
                <span style="font-weight: 700; color: var(--text-heading);"><?php echo number_format($cat['job_count']); ?></span>
                <span class="secondary-text"> open positions</span>
              </td>
              <td style="text-align: right;">
                <div class="action-btn-group" style="justify-content: flex-end;">
                  <button type="button" class="btn btn-secondary btn-sm" onclick="openEditCategoryModal(<?= $cat['id']; ?>, '<?= htmlspecialchars(addslashes($cat['name'])); ?>')">
                    Edit
                  </button>
                  <button type="button" class="btn btn-danger btn-sm" onclick="confirmAction('Are you sure you want to delete category <?= htmlspecialchars($cat['name'], ENT_QUOTES); ?>?', 'categories.php?action=delete&id=<?= $cat['id']; ?>')">
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
    <span class="secondary-text">Showing <?php echo count($categories); ?> of <?php echo count($categories); ?> categories</span>
    <div class="pagination-controls">
      <button class="page-btn page-btn-wide">Previous</button>
      <button class="page-btn active">1</button>
      <button class="page-btn">2</button>
      <button class="page-btn">3</button>
      <button class="page-btn page-btn-wide">Next</button>
    </div>
  </div>
</div>

<!-- Modal: Add / Edit Category -->
<div class="modal-backdrop" id="categoryModal">
  <div class="modal-dialog">
    <div class="modal-header">
      <h3 class="modal-title" id="catModalTitle">Add Job Category</h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('categoryModal')">&times;</button>
    </div>
    <form method="POST" action="categories.php">
      <input type="hidden" name="action" value="save_category">
      <input type="hidden" name="category_id" id="modalCatId" value="0">
      
      <div class="modal-body">
        <div class="form-field">
          <label class="form-label">Category Name *</label>
          <input type="text" name="category_name" id="modalCatName" class="form-input-text" placeholder="e.g. Artificial Intelligence & Machine Learning" required>
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('categoryModal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="catModalSubmitBtn">Save Category</button>
      </div>
    </form>
  </div>
</div>

<script>
function openAddCategoryModal() {
  document.getElementById('catModalTitle').textContent = 'Add New Job Category';
  document.getElementById('modalCatId').value = '0';
  document.getElementById('modalCatName').value = '';
  document.getElementById('catModalSubmitBtn').textContent = 'Create Category';
  openModal('categoryModal');
}

function openEditCategoryModal(id, name) {
  document.getElementById('catModalTitle').textContent = 'Edit Job Category';
  document.getElementById('modalCatId').value = id;
  document.getElementById('modalCatName').value = name;
  document.getElementById('catModalSubmitBtn').textContent = 'Update Category';
  openModal('categoryModal');
}
</script>

</main> <!-- Close admin-content -->
</div> <!-- Close admin-main -->
</div> <!-- Close admin-wrapper -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
