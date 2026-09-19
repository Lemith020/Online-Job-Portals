\<?php
/**
 * JobPortal.lk - System Settings
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'System Settings';
$page_css = BASE_URL . '/assets/css/admin_page.css';

// Handle POST save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_settings') {
        $settings_data = [
            'site_name'  => trim($_POST['site_name'] ?? 'JobPortal.lk'),
            'site_email' => trim($_POST['site_email'] ?? 'admin@jobportal.lk')
        ];

        save_system_settings($settings_data);
        add_activity("Updated global system brand settings", "settings");
        set_flash("System settings saved successfully!", "success");
        header("Location: " . BASE_URL . "/admin/settings.php");
        exit;
    }
}

$settings = get_system_settings();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<!-- Outer Main Wrapper (Correct Layout Alignment) -->
<div class="main-content" style="margin-top: 60px; margin-left: 240px; padding: 28px; width: calc(100% - 240px); box-sizing: border-box; min-height: calc(100vh - 60px);">

  <!-- Page Header -->
  <div style="margin-bottom: 24px;">
    <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0;">System Settings</h1>
    <p style="font-size: 13.5px; color: #64748b; margin-top: 4px;">Manage global portal brand identification and system email configurations.</p>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
    
    <!-- Left: Brand Settings Form -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
      <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 20px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
        <i class="fa-solid fa-sliders" style="color: #2563eb; margin-right: 6px;"></i> General Preferences
      </h3>

      <form method="POST" action="settings.php">
        <input type="hidden" name="action" value="save_settings">

        <div style="margin-bottom: 20px;">
          <label style="display: block; font-size: 13px; font-weight: 600; color: #0f172a; margin-bottom: 6px;">
            Portal Brand Name <span style="color: #dc2626;">*</span>
          </label>
          <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>" required
                 style="width: 100%; height: 42px; padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; box-sizing: border-box; outline: none;">
          <small style="display: block; font-size: 12px; color: #64748b; margin-top: 4px;">Displayed across application headers, system titles, and notifications.</small>
        </div>

        <div style="margin-bottom: 24px;">
          <label style="display: block; font-size: 13px; font-weight: 600; color: #0f172a; margin-bottom: 6px;">
            Support / Admin Notification Email <span style="color: #dc2626;">*</span>
          </label>
          <input type="email" name="site_email" value="<?php echo htmlspecialchars($settings['site_email']); ?>" required
                 style="width: 100%; height: 42px; padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; box-sizing: border-box; outline: none;">
          <small style="display: block; font-size: 12px; color: #64748b; margin-top: 4px;">Primary contact address for incoming portal alerts and inquiry responses.</small>
        </div>

        <button type="submit" style="padding: 10px 20px; background: #2563eb; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-floppy-disk"></i> Save System Settings
        </button>
      </form>
    </div>

    <!-- Right: System Diagnostic Health -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
      <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 20px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
        <i class="fa-solid fa-server" style="color: #2563eb; margin-right: 6px;"></i> Environment & Health
      </h3>

      <div style="display: flex; flex-direction: column; gap: 14px;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13.5px;">
          <span style="color: #64748b; font-weight: 500;">Application Version:</span>
          <span style="background: #f1f5f9; color: #475569; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 12px;">
            <?php echo defined('APP_VERSION') ? APP_VERSION : 'v1.0.0'; ?>
          </span>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13.5px;">
          <span style="color: #64748b; font-weight: 500;">PHP Engine Version:</span>
          <strong style="color: #0f172a;"><?php echo phpversion(); ?></strong>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13.5px;">
          <span style="color: #64748b; font-weight: 500;">Database Connection:</span>
          <?php global $conn; if ($conn): ?>
            <span style="color: #16a34a; font-weight: 700; font-size: 13px;">● Connected (MySQL)</span>
          <?php else: ?>
            <span style="color: #dc2626; font-weight: 700; font-size: 13px;">● Offline</span>
          <?php endif; ?>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13.5px;">
          <span style="color: #64748b; font-weight: 500;">Base Endpoint URL:</span>
          <span style="color: #0f172a; font-family: monospace; font-size: 12px; background: #f8fafc; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
            <?php echo defined('BASE_URL') ? BASE_URL : 'http://localhost'; ?>
          </span>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13.5px;">
          <span style="color: #64748b; font-weight: 500;">Server Timestamp:</span>
          <span style="color: #0f172a; font-size: 12.5px;"><?php echo date('Y-m-d H:i:s'); ?></span>
        </div>

      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>