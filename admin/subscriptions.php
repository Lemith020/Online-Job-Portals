<?php
/**
 * JobPortal.lk - Subscriptions Management
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Subscriptions Management';
$page_css = BASE_URL . '/assets/css/admin_page.css';

$edit_plan = null;

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Create or Update Plan
    if ($action === 'save_plan') {
        $plan_name = trim($_POST['plan_name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $duration = (int)($_POST['duration_days'] ?? 30);
        $plan_id = !empty($_POST['plan_id']) ? (int)$_POST['plan_id'] : null;

        if (!empty($plan_name)) {
            save_subscription_plan($plan_name, $price, $duration, $plan_id);
            add_activity(($plan_id ? "Updated" : "Created new") . " subscription plan: $plan_name", "subscription");
            set_flash("Subscription plan saved successfully!", "success");
        }
        header("Location: " . BASE_URL . "/admin/subscriptions.php");
        exit;
    }

    // Toggle Active Status of User Subscription
    if ($action === 'toggle_sub') {
        $sub_id = (int)($_POST['sub_id'] ?? 0);
        $is_active = (int)($_POST['is_active'] ?? 0);
        toggle_user_subscription_status($sub_id, $is_active);
        add_activity("Updated subscription #$sub_id active status to $is_active", "subscription");
        set_flash("Subscription status updated successfully.", "success");
        header("Location: " . BASE_URL . "/admin/subscriptions.php");
        exit;
    }
}

// Check for Edit Trigger
if (isset($_GET['edit_plan'])) {
    $pid = (int)$_GET['edit_plan'];
    global $conn;
    $res = mysqli_query($conn, "SELECT * FROM subscription_plans WHERE plan_id = $pid");
    if ($res && mysqli_num_rows($res) > 0) {
        $edit_plan = mysqli_fetch_assoc($res);
    }
}

$plans = get_all_subscription_plans_admin();
$subscriptions = get_user_subscriptions_admin();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<!-- Outer Main Wrapper -->
<div class="main-content" style="margin-top: 60px; margin-left: 240px; padding: 28px; width: calc(100% - 240px); box-sizing: border-box; min-height: calc(100vh - 60px);">

  <!-- Page Header -->
  <div style="margin-bottom: 24px;">
    <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0;">Subscriptions & Membership Plans</h1>
    <p style="font-size: 13.5px; color: #64748b; margin-top: 4px;">Monitor portal subscription plans, add new tiers, and manage active user memberships.</p>
  </div>

  <!-- Inline Add / Edit Subscription Plan Form Card -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; margin-bottom: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 16px 0;">
      <?php echo $edit_plan ? '✏️ Edit Subscription Plan' : '➕ Add New Subscription Plan'; ?>
    </h3>
    
    <form method="POST" action="subscriptions.php" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin: 0;">
      <input type="hidden" name="action" value="save_plan">
      <?php if ($edit_plan): ?>
        <input type="hidden" name="plan_id" value="<?php echo $edit_plan['plan_id']; ?>">
      <?php endif; ?>

      <div style="flex: 2; min-width: 200px;">
        <label style="display: block; font-size: 12.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">Plan Name <span style="color: #dc2626;">*</span></label>
        <input type="text" name="plan_name" required placeholder="e.g. Enterprise Corporate Plan" 
               value="<?php echo htmlspecialchars($edit_plan['plan_name'] ?? ''); ?>" 
               style="width: 100%; height: 40px; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; box-sizing: border-box;">
      </div>

      <div style="flex: 1; min-width: 140px;">
        <label style="display: block; font-size: 12.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">Price (LKR) <span style="color: #dc2626;">*</span></label>
        <input type="number" step="0.01" name="price" required placeholder="0.00" 
               value="<?php echo htmlspecialchars($edit_plan['price'] ?? '0.00'); ?>" 
               style="width: 100%; height: 40px; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; box-sizing: border-box;">
      </div>

      <div style="flex: 1; min-width: 140px;">
        <label style="display: block; font-size: 12.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">Duration (Days) <span style="color: #dc2626;">*</span></label>
        <input type="number" name="duration_days" required placeholder="30" 
               value="<?php echo htmlspecialchars($edit_plan['duration_days'] ?? '30'); ?>" 
               style="width: 100%; height: 40px; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; box-sizing: border-box;">
      </div>

      <div style="display: flex; gap: 8px;">
        <button type="submit" style="height: 40px; padding: 0 18px; background: #2563eb; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 13.5px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-check"></i> <?php echo $edit_plan ? 'Update Plan' : 'Save Plan'; ?>
        </button>

        <?php if ($edit_plan): ?>
          <a href="subscriptions.php" style="height: 40px; padding: 0 14px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 600; font-size: 13.5px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">Cancel</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Available Subscription Plans Cards Overview -->
  <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin-bottom: 16px;">Active Subscription Plans (<?php echo count($plans); ?>)</h3>
  
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 32px;">
    <?php foreach ($plans as $p): ?>
      <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between; position: relative;">
        <div>
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 11px; font-weight: 700; color: #2563eb; text-transform: uppercase; background: #eff6ff; padding: 4px 8px; border-radius: 6px;">Plan #<?php echo $p['plan_id']; ?></span>
            <a href="subscriptions.php?edit_plan=<?php echo $p['plan_id']; ?>" style="color: #64748b; text-decoration: none; font-size: 13px;" title="Edit Plan"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
          </div>
          <h3 style="font-size: 18px; font-weight: 700; color: #0f172a; margin: 10px 0 6px 0;"><?php echo htmlspecialchars($p['plan_name']); ?></h3>
          <div style="font-size: 22px; font-weight: 800; color: #0f172a;">
            LKR <?php echo number_format($p['price'], 2); ?>
            <span style="font-size: 13px; font-weight: 500; color: #64748b;">/ <?php echo $p['duration_days']; ?> Days</span>
          </div>
        </div>
        <div style="margin-top: 16px; padding-top: 12px; border-top: 1px dashed #e2e8f0; font-size: 13px; color: #16a34a; font-weight: 600;">
          <i class="fa-solid fa-circle-check"></i> Valid for <?php echo $p['duration_days']; ?> Days Access
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Subscribers Table Card -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0;">
      <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">Subscribed Accounts (<?php echo count($subscriptions); ?>)</h3>
    </div>

    <div style="width: 100%; overflow-x: auto;">
      <table class="table" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
          <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <th style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">SUBSCRIBER</th>
            <th style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">ACTIVE PLAN</th>
            <th style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">PRICE</th>
            <th style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">DURATION DATES</th>
            <th style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">STATUS</th>
            <th style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; text-align: right;">ACTIONS</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($subscriptions)): ?>
            <tr>
              <td colspan="6" style="text-align: center; padding: 32px; color: #64748b;">No active subscriptions found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($subscriptions as $sub): ?>
              <tr style="border-bottom: 1px solid #e2e8f0;">
                <td style="padding: 14px 16px;">
                  <strong style="display: block; font-size: 14px; color: #0f172a;"><?php echo htmlspecialchars($sub['subscriber_name']); ?></strong>
                  <span style="font-size: 12px; color: #64748b;"><?php echo htmlspecialchars($sub['email'] ?? 'N/A'); ?></span>
                </td>
                <td style="padding: 14px 16px;">
                  <span style="background: #f3e8ff; color: #6b21a8; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700;">
                    <?php echo htmlspecialchars($sub['plan_name'] ?? 'Plan #' . $sub['plan_id']); ?>
                  </span>
                </td>
                <td style="padding: 14px 16px; font-size: 13.5px; font-weight: 700; color: #0f172a;">
                  LKR <?php echo number_format($sub['price'] ?? 0, 2); ?>
                </td>
                <td style="padding: 14px 16px; font-size: 13px; color: #475569;">
                  <?php echo date('M d, Y', strtotime($sub['start_date'])); ?> &rarr; <?php echo date('M d, Y', strtotime($sub['end_date'])); ?>
                </td>
                <td style="padding: 14px 16px;">
                  <?php if ((int)$sub['is_active'] === 1): ?>
                    <span style="color: #16a34a; font-weight: 700; font-size: 13px;">● Active</span>
                  <?php else: ?>
                    <span style="color: #dc2626; font-weight: 700; font-size: 13px;">● Expired / Inactive</span>
                  <?php endif; ?>
                </td>
                <td style="padding: 14px 16px; text-align: right;">
                  <div style="display: flex; gap: 6px; justify-content: flex-end;">
                    <?php if ((int)$sub['is_active'] === 1): ?>
                      <form method="POST" action="subscriptions.php" style="display:inline;" onsubmit="return confirm('Deactivate this user subscription?');">
                        <input type="hidden" name="action" value="toggle_sub">
                        <input type="hidden" name="sub_id" value="<?php echo $sub['sub_id']; ?>">
                        <input type="hidden" name="is_active" value="0">
                        <button type="submit" style="padding: 6px 12px; background: #fee2e2; color: #dc2626; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer;">
                          Deactivate
                        </button>
                      </form>
                    <?php else: ?>
                      <form method="POST" action="subscriptions.php" style="display:inline;">
                        <input type="hidden" name="action" value="toggle_sub">
                        <input type="hidden" name="sub_id" value="<?php echo $sub['sub_id']; ?>">
                        <input type="hidden" name="is_active" value="1">
                        <button type="submit" style="padding: 6px 12px; background: #dcfce7; color: #16a34a; border: none; border-radius: 6px; font-weight: 600; font-size: 12px; cursor: pointer;">
                          Activate
                        </button>
                      </form>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>