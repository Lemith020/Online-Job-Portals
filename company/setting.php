<?php
$page_title = "Settings";
$page_css = "settings.css";
$page_js = "settings.js";
$active_page = "settings";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $user_result = mysqli_query($conn, "SELECT password FROM users WHERE user_id = $user_id");
    $user_row = mysqli_fetch_assoc($user_result);

    if (!$user_row || !password_verify($current_password, $user_row['password'])) {
        $message = "Current password is incorrect.";
        $message_type = "error";
    } elseif ($new_password !== $confirm_password) {
        $message = "New password and confirm password do not match.";
        $message_type = "error";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password = '$hashed' WHERE user_id = $user_id");
        $message = "Password updated successfully.";
        $message_type = "success";
    }
}
?>

<div class="page-header">
    <h1>Account & Notification Settings</h1>
</div>

<?php if ($message) : ?>
<div class="alert alert-<?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="card" style="margin-bottom:20px;">
    <h2 style="margin-bottom:16px;">Security</h2>
    <p style="font-weight:600; font-size:14px; margin-bottom:12px;">Change Password</p>

    <form method="post">
        <div class="form-group">
            <label>Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>
        <button type="submit" name="update_password" class="btn btn-primary btn-block">Update Password</button>
    </form>
</div>

<div class="card">
    <h2 style="margin-bottom:6px;">Preferences</h2>
    <p style="font-weight:600; font-size:14px; margin-bottom:6px;">Notification Preferences</p>
    <p style="color:var(--muted); font-size:12px; margin-bottom:10px;">
        (Display only for now — the database doesn't have a notification-settings table yet, add one later if this needs to be saved.)
    </p>

    <div class="pref-row">
        <div class="pref-text">
            <strong>New Applicant Alerts</strong>
            <span>Receive an email when a job seeker applies to any of your job postings.</span>
        </div>
        <label class="switch">
            <input type="checkbox" checked>
            <span class="slider"></span>
        </label>
    </div>

    <div class="pref-row">
        <div class="pref-text">
            <strong>Application Status Updates</strong>
            <span>Receive email notifications when you update the status of an applicant.</span>
        </div>
        <label class="switch">
            <input type="checkbox">
            <span class="slider"></span>
        </label>
    </div>

    <div class="pref-row">
        <div class="pref-text">
            <strong>Interview Reminders</strong>
            <span>Receive email reminders for upcoming interviews 24 hours in advance.</span>
        </div>
        <label class="switch">
            <input type="checkbox" checked>
            <span class="slider"></span>
        </label>
    </div>

    <div class="pref-row">
        <div class="pref-text">
            <strong>Platform Updates</strong>
            <span>Receive important announcements and feature updates from JobPortal.lk.</span>
        </div>
        <label class="switch">
            <input type="checkbox" checked>
            <span class="slider"></span>
        </label>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
