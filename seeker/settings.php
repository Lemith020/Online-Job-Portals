<?php
// seeker/settings.php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// ---- change password ----
if (isset($_POST['update_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $hash = get_user_password_hash($conn, $user_id);

    if (!$hash || !password_verify($current, $hash)) {
        $error = "Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $error = "New passwords do not match.";
    } elseif (strlen($new) < 6) {
        $error = "New password must be at least 6 characters.";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        update_user_password($conn, $user_id, $hashed);
        $success = "Password updated successfully!";
    }
}

// ---- delete account ----
if (isset($_POST['delete_account'])) {
    delete_user_account($conn, $user_id);
    session_destroy();
    redirect("/Online-Job-Portal/index.php");
}

$page_title = "Settings";
$page_css = "../assets/css/seeker_page_css/settings.css";
$page_js = "../assets/js/seeker_page_js/settings.js";
require_once '../includes/seeker-header.php';
require_once '../includes/seeker-sidebar.php';
?>

<h1 class="page-title">Account Settings</h1>

<div class="card" style="max-width:500px; margin-bottom:16px;">
    <h2 class="section-title">Change Password</h2>
    <?php if ($success): ?><div class="alert-success"><?= clean($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert-error"><?= clean($error) ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Current Password</label>
            <input type="password" name="current_password" required>
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" required>
        </div>
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" name="update_password" class="btn btn-primary btn-full">Update Password</button>
    </form>
</div>

<div class="card danger-zone" style="max-width:500px;">
    <h2 class="section-title">Danger Zone</h2>
    <p>Permanently delete your account and all associated data. This action cannot be undone.</p>
    <button type="button" class="btn btn-danger" onclick="document.getElementById('delete-form').classList.toggle('show')">Delete Account</button>
    <form method="POST" id="delete-form" class="delete-confirm">
        <p>Are you sure? Type <strong>DELETE</strong> to confirm.</p>
        <input type="text" id="confirm-text" placeholder="Type DELETE">
        <button type="submit" name="delete_account" id="confirm-delete-btn" class="btn btn-danger" disabled>Confirm Delete</button>
    </form>
    <p class="settings-note">For security, ensure your passwords are complex and unique.</p>
</div>

<?php require_once '../includes/seeker-footer.php'; ?>
