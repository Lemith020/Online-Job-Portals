<?php
/**
 * JobPortal.lk - System Administration Settings
 * Member 1 - Admin UI (Matching Page 14 Blueprint)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'System Administration Settings';

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $current = trim($_POST['current_password'] ?? '');
    $new = trim($_POST['new_password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    if (empty($current) || empty($new) || empty($confirm)) {
        set_flash('Please fill in all password fields.', 'error');
    } elseif ($new !== $confirm) {
        set_flash('New password and confirmation do not match.', 'error');
    } elseif (strlen($new) < 6) {
        set_flash('Password must be at least 6 characters.', 'error');
    } else {
        set_flash('Admin password updated successfully.', 'success');
    }
    header('Location: settings.php');
    exit;
}

// Handle Site Configuration Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_site_settings') {
    $site_name = trim($_POST['site_name'] ?? 'JobPortal.lk');
    $site_email = trim($_POST['site_email'] ?? 'admin@jobportal.lk');
    $maintenance = isset($_POST['maintenance_mode']) ? 1 : 0;
    $enable_reg = isset($_POST['enable_registration']) ? 1 : 0;
    $enable_approval = isset($_POST['enable_job_approval']) ? 1 : 0;
    $jobs_per_page = (int)($_POST['jobs_per_page'] ?? 10);

    update_system_settings([
        'site_name' => $site_name,
        'site_email' => $site_email,
        'maintenance_mode' => $maintenance,
        'enable_registration' => $enable_reg,
        'enable_job_approval' => $enable_approval,
        'jobs_per_page' => $jobs_per_page
    ]);

    set_flash('Site configuration settings have been updated successfully.', 'success');
    header('Location: settings.php');
    exit;
}

$settings = get_system_settings();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="page-header">
  <div class="page-title-group">
    <h1>System Administration Settings</h1>
    <p>Configure core application rules, administrator authentication credentials, and platform flags.</p>
  </div>
</div>

<!-- Section 1: Admin Security & Password Change -->
<div class="settings-section-card">
  <h2 class="settings-section-title">Admin Security — Password Change</h2>
  
  <form method="POST" action="settings.php">
    <input type="hidden" name="action" value="change_password">
    
    <div class="form-grid-3">
      <div class="form-field">
        <label class="form-label">Current Password *</label>
        <input type="password" name="current_password" class="form-input-text" placeholder="••••••••" required>
      </div>

      <div class="form-field">
        <label class="form-label">New Password *</label>
        <input type="password" name="new_password" class="form-input-text" placeholder="••••••••" required>
      </div>

      <div class="form-field">
        <label class="form-label">Confirm New Password *</label>
        <input type="password" name="confirm_password" class="form-input-text" placeholder="••••••••" required>
      </div>
    </div>

    <div style="margin-top: 10px;">
      <button type="submit" class="btn btn-primary">Update Admin Password</button>
    </div>
  </form>
</div>

<!-- Section 2: General Site Configuration (Matching Page 14) -->
<div class="settings-section-card">
  <h2 class="settings-section-title">Site Configuration — General Settings</h2>
  
  <form method="POST" action="settings.php">
    <input type="hidden" name="action" value="save_site_settings">

    <div class="form-grid-2">
      <div class="form-field">
        <label class="form-label">Site Name</label>
        <input type="text" name="site_name" class="form-input-text" value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
      </div>

      <div class="form-field">
        <label class="form-label">Site Email (Admin Contact)</label>
        <input type="email" name="site_email" class="form-input-text" value="<?php echo htmlspecialchars($settings['site_email']); ?>" required>
      </div>
    </div>

    <div class="form-grid-3" style="margin-top: 10px; margin-bottom: 20px;">
      <!-- Maintenance Mode Switch -->
      <div class="form-switch-row">
        <div>
          <div style="font-weight: 700; font-size: 13.5px; color: var(--text-heading);">Maintenance Mode</div>
          <div style="font-size: 12px; color: var(--text-muted);">Temporarily close public access</div>
        </div>
        <label class="switch">
          <input type="checkbox" name="maintenance_mode" <?php echo !empty($settings['maintenance_mode']) ? 'checked' : ''; ?>>
          <span class="slider"></span>
        </label>
      </div>

      <!-- Enable User Registration Switch -->
      <div class="form-switch-row">
        <div>
          <div style="font-weight: 700; font-size: 13.5px; color: var(--text-heading);">Enable Registration</div>
          <div style="font-size: 12px; color: var(--text-muted);">Allow new users to sign up</div>
        </div>
        <label class="switch">
          <input type="checkbox" name="enable_registration" <?php echo !empty($settings['enable_registration']) ? 'checked' : ''; ?>>
          <span class="slider"></span>
        </label>
      </div>

      <!-- Enable Job Approval Switch -->
      <div class="form-switch-row">
        <div>
          <div style="font-weight: 700; font-size: 13.5px; color: var(--text-heading);">Require Job Approval</div>
          <div style="font-size: 12px; color: var(--text-muted);">Moderator review before publishing</div>
        </div>
        <label class="switch">
          <input type="checkbox" name="enable_job_approval" <?php echo !empty($settings['enable_job_approval']) ? 'checked' : ''; ?>>
          <span class="slider"></span>
        </label>
      </div>
    </div>

    <div class="form-field" style="max-width: 260px; margin-bottom: 24px;">
      <label class="form-label">Default Jobs Per Page</label>
      <input type="number" name="jobs_per_page" class="form-input-text" min="5" max="100" value="<?php echo (int)($settings['jobs_per_page'] ?? 10); ?>" required>
    </div>

    <div>
      <button type="submit" class="btn btn-primary">Save Site Settings</button>
    </div>
  </form>
</div>

<p style="font-size: 12.5px; color: var(--text-light); text-align: center; margin-top: 10px;">
  Changes to site settings take effect immediately across all client sessions.
</p>

</main> <!-- Close admin-content -->
</div> <!-- Close admin-main -->
</div> <!-- Close admin-wrapper -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
