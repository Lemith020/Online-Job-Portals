<?php
/**
 * JobPortal.lk - Company Categories Selection
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'company') {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'] ?? 0;

if ($company_id == 0 && isset($conn) && $conn) {
    $c_q = mysqli_query($conn, "SELECT company_id FROM company WHERE user_id = $user_id");
    if ($c_q && $c_row = mysqli_fetch_assoc($c_q)) {
        $company_id = (int)$c_row['company_id'];
        $_SESSION['company_id'] = $company_id;
    }
}

$error_msg = '';

// -------------------------------------------------------------
// 1. SAVE CATEGORIES LOGIC (POST request check)
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($company_id <= 0) {
        $error_msg = "Company ID not found. Please log in again.";
    } else {
      
        $del_query = "DELETE FROM company_category WHERE company_id = $company_id";
        if (!mysqli_query($conn, $del_query)) {
            $error_msg = "Delete Error: " . mysqli_error($conn);
        }

       
        if (empty($error_msg) && isset($_POST['categories']) && is_array($_POST['categories'])) {
            foreach ($_POST['categories'] as $cat_id) {
                $cat_id = (int) $cat_id;
                $ins_query = "INSERT INTO company_category (company_id, category_id) VALUES ($company_id, $cat_id)";
                if (!mysqli_query($conn, $ins_query)) {
                    $error_msg = "Insert Error: " . mysqli_error($conn);
                    break;
                }
            }
        }

        if (empty($error_msg)) {
            header("Location: categories.php?saved=1");
            exit();
        }
    }
}

// -------------------------------------------------------------
// 2. FETCH SELECTED CATEGORIES
// -------------------------------------------------------------
$selected_ids = [];
if ($conn && $company_id > 0) {
    $selected_result = mysqli_query($conn, "SELECT category_id FROM company_category WHERE company_id = $company_id");
    if ($selected_result) {
        while ($row = mysqli_fetch_assoc($selected_result)) {
            $selected_ids[] = (int)$row['category_id'];
        }
    }
}

$all_categories = $conn ? mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name") : false;

$page_title = "Categories";
$active_page = "categories";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/company-sidebar.php';
?>

<main class="main-content">
    <div class="page-header">
        <h1>Company Categories</h1>
    </div>

    <?php if (isset($_GET['saved'])) : ?>
        <div class="alert alert-success" style="margin-bottom: 20px; background: #d4edda; color: #155724; padding: 12px; border-radius: 6px;">
            <i class="fa-solid fa-circle-check"></i> Category selection saved successfully.
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)) : ?>
        <div class="alert alert-danger" style="margin-bottom: 20px; background: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px;">
            <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error_msg); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <p style="color:var(--muted); font-size:14px; margin-bottom:16px;">
            Select the categories that best describe your company's focus areas.
        </p>

        <div class="search-input" style="max-width:320px; margin-bottom:18px;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="categorySearch" class="form-control" placeholder="Search category...">
        </div>

        <form method="post" action="categories.php" id="categoryForm">
            <input type="hidden" name="save_categories" value="1">
            
            <div class="tag-grid" id="tagGrid" style="display:flex; flex-wrap:wrap; gap:10px;">
                <?php if ($all_categories && mysqli_num_rows($all_categories) > 0) : ?>
                    <?php while ($cat = mysqli_fetch_assoc($all_categories)) : ?>
                        <?php $is_selected = in_array((int)$cat['category_id'], $selected_ids); ?>
                        <label class="tag <?php echo $is_selected ? 'selected' : ''; ?>" data-name="<?php echo strtolower($cat['category_name']); ?>" style="cursor:pointer; padding: 8px 14px; border: 1px solid #ccc; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px; <?php echo $is_selected ? 'background:#e0e7ff; border-color:#6366f1;' : ''; ?>">
                            <input type="checkbox" name="categories[]" value="<?php echo $cat['category_id']; ?>" <?php echo $is_selected ? 'checked' : ''; ?> style="display:none;">
                            <i class="fa-solid fa-check check-icon" style="<?php echo $is_selected ? 'display:inline-block;' : 'display:none;'; ?>"></i>
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                        </label>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p style="color:var(--muted); font-size:14px;">No categories available.</p>
                <?php endif; ?>
            </div>

            <div class="modal-actions" style="max-width:320px; margin-left:auto; margin-top:20px;">
                <button type="submit" class="btn btn-primary btn-block">Save Category Selection</button>
            </div>
        </form>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tags = document.querySelectorAll('.tag-grid .tag');
    
    tags.forEach(tag => {
        tag.addEventListener('click', function(e) {
            const checkbox = this.querySelector('input[type="checkbox"]');
            const icon = this.querySelector('.check-icon');
            
            setTimeout(() => {
                if (checkbox.checked) {
                    this.classList.add('selected');
                    this.style.background = '#e0e7ff';
                    this.style.borderColor = '#6366f1';
                    if (icon) icon.style.display = 'inline-block';
                } else {
                    this.classList.remove('selected');
                    this.style.background = 'transparent';
                    this.style.borderColor = '#ccc';
                    if (icon) icon.style.display = 'none';
                }
            }, 10);
        });
    });

    const searchInput = document.getElementById('categorySearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            tags.forEach(tag => {
                const name = tag.getAttribute('data-name');
                if (name.includes(query)) {
                    tag.style.display = 'inline-flex';
                } else {
                    tag.style.display = 'none';
                }
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>