<?php
/**
 * JobPortal.lk - System Settings
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'System Settings';

// Handle POST save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_settings') {
        $settings_data = [
            'site_name' => trim($_POST['site_name'] ?? 'JobPortal.lk'),
            'site_email' => trim($_POST['site_email'] ?? 'admin@jobportal.lk'),
            'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0,
            'enable_registration' => isset($_POST['enable_registration']) ? 1 : 0,
            'enable_job_approval' => isset($_POST['enable_job_approval']) ? 1 : 0,
            'jobs_per_page' => (int)($_POST['jobs_per_page'] ?? 10)
        ];

        save_system_settings($settings_data);
        add_activity("Updated global system settings configuration", "settings");
        set_flash("System settings saved successfully!", "success");
        header("Location: " . BASE_URL . "/admin/settings.php");
        exit;
    }
}

$settings = get_system_settings();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div class="page-title-group">
    <h1 class="page-title">System Settings</h1>
    <p class="page-subtitle">Configure portal preferences, moderation rules, and security controls.</p>
  </div>
</div>

<div class="settings-grid">
  <!-- Left: Core System Settings Form -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><i class="fa-solid fa-sliders text-primary"></i> General Preferences</h3>
    </div>
    <div class="card-body">
      <form method="POST" action="settings.php">
        <input type="hidden" name="action" value="save_settings">

        <div class="form-group mb-4">
          <label class="form-label">Portal Brand Name <span class="text-danger">*</span></label>
          <input type="text" name="site_name" class="form-input" value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
          <small class="text-muted">Displayed in header, emails, and page titles.</small>
        </div>

        <div class="form-group mb-4">
          <label class="form-label">Support / Admin Notification Email <span class="text-danger">*</span></label>
          <input type="email" name="site_email" class="form-input" value="<?php echo htmlspecialchars($settings['site_email']); ?>" required>
          <small class="text-muted">Receives new employer registrations and flagged review alerts.</small>
        </div>

        <div class="form-group mb-4">
          <label class="form-label">Public Listings Per Page</label>
          <input type="number" name="jobs_per_page" class="form-input" value="<?php echo (int)$settings['jobs_per_page']; ?>" min="5" max="50">
        </div>

        <hr class="divider mb-4">

        <h4 class="mb-3 font-semibold">Moderation & Security Toggles</h4>

        <div class="toggle-group mb-3">
          <label class="toggle-switch">
            <input type="checkbox" name="enable_job_approval" <?php echo ($settings['enable_job_approval']) ? 'checked' : ''; ?>>
            <span class="toggle-slider"></span>
          </label>
          <div class="toggle-label">
            <strong>Require Admin Approval for Job Postings</strong>
            <small class="text-muted d-block">When enabled, jobs remain pending until approved by an administrator.</small>
          </div>
        </div>

        <div class="toggle-group mb-3">
          <label class="toggle-switch">
            <input type="checkbox" name="enable_registration" <?php echo ($settings['enable_registration']) ? 'checked' : ''; ?>>
            <span class="toggle-slider"></span>
          </label>
          <div class="toggle-label">
            <strong>Allow New User & Employer Registrations</strong>
            <small class="text-muted d-block">Enable or disable public signup forms on the portal.</small>
          </div>
        </div>

        <div class="toggle-group mb-4">
          <label class="toggle-switch">
            <input type="checkbox" name="maintenance_mode" <?php echo ($settings['maintenance_mode']) ? 'checked' : ''; ?>>
            <span class="toggle-slider"></span>
          </label>
          <div class="toggle-label">
            <strong>Maintenance Mode</strong>
            <small class="text-muted d-block">Blocks public users from browsing while system upgrades are performed.</small>
          </div>
        </div>

        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-floppy-disk"></i> Save System Settings
        </button>
      </form>
    </div>
  </div>

  <!-- Right: Environment & System Diagnostic Info -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><i class="fa-solid fa-server text-primary"></i> Server & Database Health</h3>
    </div>
    <div class="card-body">
      <div class="info-list">
        <div class="info-row">
          <span class="info-key">Application Version:</span>
          <span class="info-val badge badge-secondary"><?php echo APP_VERSION; ?></span>
        </div>
        <div class="info-row">
          <span class="info-key">PHP Version:</span>
          <span class="info-val"><?php echo phpversion(); ?></span>
        </div>
        <div class="info-row">
          <span class="info-key">Database Status:</span>
          <span class="info-val">
            <?php global $is_db_connected; if ($is_db_connected): ?>
              <span class="status-pill status-active">● Connected (MySQL)</span>
            <?php else: ?>
              <span class="status-pill status-warning">● Local Fallback Mode</span>
            <?php endif; ?>
          </span>
        </div>
        <div class="info-row">
          <span class="info-key">Base URL:</span>
          <span class="info-val text-truncate-1" style="max-width:200px;"><?php echo BASE_URL; ?></span>
        </div>
        <div class="info-row">
          <span class="info-key">Server Time:</span>
          <span class="info-val"><?php echo date('Y-m-d H:i:s T'); ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
