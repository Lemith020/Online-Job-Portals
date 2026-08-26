<?php
$page_title = "Categories";
$page_css = "categories.css";
$page_js = "categories.js";
$active_page = "categories";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$saved = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_categories'])) {
    mysqli_query($conn, "DELETE FROM company_category WHERE company_id = $company_id");

    if (!empty($_POST['categories'])) {
        foreach ($_POST['categories'] as $cat_id) {
            $cat_id = (int) $cat_id;
            mysqli_query($conn, "INSERT INTO company_category (company_id, category_id) VALUES ($company_id, $cat_id)");
        }
    }
    $saved = true;
}

// currently selected categories for this company
$selected_ids = [];
$selected_result = mysqli_query($conn, "SELECT category_id FROM company_category WHERE company_id = $company_id");
while ($row = mysqli_fetch_assoc($selected_result)) {
    $selected_ids[] = $row['category_id'];
}

$all_categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");
?>

<div class="page-header">
    <h1>Company Categories</h1>
</div>

<?php if ($saved) : ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Category selection saved.</div>
<?php endif; ?>

<div class="card">
    <p style="color:var(--muted); font-size:14px; margin-bottom:16px;">
        Select the categories that best describe your company's focus areas. These selections will help job seekers find you.
    </p>

    <div class="search-input" style="max-width:320px; margin-bottom:18px;">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="categorySearch" class="form-control" placeholder="Search category...">
    </div>

    <form method="post" id="categoryForm">
        <div class="tag-grid" id="tagGrid">
            <?php while ($cat = mysqli_fetch_assoc($all_categories)) : ?>
                <?php $is_selected = in_array($cat['category_id'], $selected_ids); ?>
                <div class="tag <?php echo $is_selected ? 'selected' : ''; ?>" data-name="<?php echo strtolower($cat['category_name']); ?>">
                    <input type="checkbox" name="categories[]" value="<?php echo $cat['category_id']; ?>" <?php echo $is_selected ? 'checked' : ''; ?> style="display:none;">
                    <i class="fa-solid fa-check check-icon" style="<?php echo $is_selected ? '' : 'display:none;'; ?>"></i>
                    <?php echo htmlspecialchars($cat['category_name']); ?>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="modal-actions" style="max-width:320px; margin-left:auto; margin-top:20px;">
            <button type="submit" name="save_categories" class="btn btn-primary btn-block">Save Category Selection</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
