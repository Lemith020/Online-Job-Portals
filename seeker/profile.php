<?php
// seeker/profile.php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$user_id = $_SESSION['user_id'];
$seeker_id = get_seeker_id($conn, $user_id);
$success = '';

// ---- handle form save ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name  = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name   = trim($_POST['last_name']);
    $birth_day   = $_POST['birth_day'] ?: null;
    $phone       = trim($_POST['phone']);
    $bio         = trim($_POST['bio']);
    $selected_categories = $_POST['categories'] ?? [];

    update_user_name($conn, $user_id, $first_name, $middle_name, $last_name);
    update_seeker_profile($conn, $seeker_id, $birth_day, $phone, $bio);
    save_seeker_categories($conn, $seeker_id, $selected_categories);

    $_SESSION['first_name'] = $first_name;
    $success = "Profile updated successfully!";
}

// ---- load current data ----
$user = get_user($conn, $user_id);
$seeker = get_seeker_profile($conn, $seeker_id);
$categories = get_categories($conn);
$my_categories = get_seeker_categories($conn, $seeker_id);

$page_title = "My Profile";
$page_css = "../assets/css/seeker_page_css/profile.css";
require_once '../includes/seeker-header.php';
require_once '../includes/seeker-sidebar.php';
?>

<h1 class="page-title">My Profile</h1>
<?php if ($success): ?><div class="alert-success"><?= clean($success) ?></div><?php endif; ?>

<form method="POST" class="card">
    <div class="form-row">
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" value="<?= clean($user['first_name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="birth_day" value="<?= clean($seeker['birth_day']) ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Middle Name</label>
            <input type="text" name="middle_name" value="<?= clean($user['middle_name']) ?>">
        </div>
        <div class="form-group">
            <label>Bio</label>
            <textarea name="bio" rows="3"><?= clean($seeker['bio']) ?></textarea>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" value="<?= clean($user['last_name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" value="<?= clean($seeker['phone']) ?>" placeholder="+94 7x xxx xxxx">
        </div>
    </div>

    <div class="form-group">
        <label>Category Preferences</label>
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
                <label class="checkbox-label">
                    <input type="checkbox" name="categories[]" value="<?= $cat['category_id'] ?>"
                        <?= in_array($cat['category_id'], $my_categories) ? 'checked' : '' ?>>
                    <?= clean($cat['category_name']) ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-full">Save Changes</button>
</form>

<?php require_once '../includes/seeker-footer.php'; ?>
