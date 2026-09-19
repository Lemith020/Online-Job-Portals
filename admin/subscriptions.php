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

<!-- Page Header -->
<div class="page-header">
  <div class="page-title-group">
    <h1 class="page-title">Subscriptions & Membership Plans</h1>
    <p class="page-subtitle">Monitor portal subscription plans, add new tiers, and manage active user memberships.</p>
  </div>
</div>

<!-- Inline Add / Edit Subscription Plan Form Card -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      <?php echo $edit_plan ? '✏️ Edit Subscription Plan' : '➕ Add New Subscription Plan'; ?>
    </h3>
  </div>
  <div class="card-body">
    <form method="POST" action="subscriptions.php" class="sub-form-inline">
      <input type="hidden" name="action" value="save_plan">
      <?php if ($edit_plan): ?>
        <input type="hidden" name="plan_id" value="<?php echo $edit_plan['plan_id']; ?>">
      <?php endif; ?>

      <div class="form-field-group field-large">
        <label>Plan Name <span class="text-danger">*</span></label>
        <input type="text" name="plan_name" class="form-input" required placeholder="e.g. Enterprise Corporate Plan" 
               value="<?php echo htmlspecialchars($edit_plan['plan_name'] ?? ''); ?>">
      </div>

      <div class="form-field-group field-medium">
        <label>Price (LKR) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="price" class="form-input" required placeholder="0.00" 
               value="<?php echo htmlspecialchars($edit_plan['price'] ?? '0.00'); ?>">
      </div>

      <div class="form-field-group field-medium">
        <label>Duration (Days) <span class="text-danger">*</span></label>
        <input type="number" name="duration_days" class="form-input" required placeholder="30" 
               value="<?php echo htmlspecialchars($edit_plan['duration_days'] ?? '30'); ?>">
      </div>

      <div class="form-actions-inline">
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-check"></i> <?php echo $edit_plan ? 'Update Plan' : 'Save Plan'; ?>
        </button>

        <?php if ($edit_plan): ?>
          <a href="subscriptions.php" class="btn btn-secondary">Cancel</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<!-- Available Subscription Plans Cards Overview -->
<h3 class="section-heading mb-3">Active Subscription Plans (<?php echo count($plans); ?>)</h3>

<div class="plans-cards-grid">
  <?php foreach ($plans as $p): ?>
    <div class="plan-card-item">
      <div class="plan-card-header">
        <span class="badge badge-indigo">Plan #<?php echo $p['plan_id']; ?></span>
        <a href="subscriptions.php?edit_plan=<?php echo $p['plan_id']; ?>" class="plan-edit-link" title="Edit Plan">
          <i class="fa-regular fa-pen-to-square"></i> Edit
        </a>
      </div>
      
      <div class="plan-card-body">
        <h4 class="plan-title-text"><?php echo htmlspecialchars($p['plan_name']); ?></h4>
        <div class="plan-price-tag">
          LKR <?php echo number_format($p['price'], 2); ?>
          <span class="plan-period">/ <?php echo $p['duration_days']; ?> Days</span>
        </div>
      </div>

      <div class="plan-card-footer">
        <i class="fa-solid fa-circle-check"></i> Valid for <?php echo $p['duration_days']; ?> Days Access
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Subscribers Table Card -->
<div class="card mt-4">
  <div class="card-header">
    <h3 class="card-title">Subscribed Accounts (<?php echo count($subscriptions); ?>)</h3>
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>SUBSCRIBER</th>
          <th>ACTIVE PLAN</th>
          <th>PRICE</th>
          <th>DURATION DATES</th>
          <th>STATUS</th>
          <th class="text-right">ACTIONS</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($subscriptions)): ?>
          <tr>
            <td colspan="6" class="text-center py-4 text-muted">No active subscriptions found.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($subscriptions as $sub): ?>
            <tr>
              <td>
                <strong class="d-block" style="font-size: 14px; color: #0f172a;"><?php echo htmlspecialchars($sub['subscriber_name']); ?></strong>
                <span class="text-muted" style="font-size: 12px;"><?php echo htmlspecialchars($sub['email'] ?? 'N/A'); ?></span>
              </td>
              <td>
                <span class="badge badge-purple">
                  <?php echo htmlspecialchars($sub['plan_name'] ?? 'Plan #' . $sub['plan_id']); ?>
                </span>
              </td>
              <td style="font-size: 13.5px; font-weight: 700; color: #0f172a;">
                LKR <?php echo number_format($sub['price'] ?? 0, 2); ?>
              </td>
              <td class="text-muted" style="font-size: 13px;">
                <?php echo date('M d, Y', strtotime($sub['start_date'])); ?> &rarr; <?php echo date('M d, Y', strtotime($sub['end_date'])); ?>
              </td>
              <td>
                <?php if ((int)$sub['is_active'] === 1): ?>
                  <span class="status-pill status-active">● Active</span>
                <?php else: ?>
                  <span class="status-pill status-danger">● Expired / Inactive</span>
                <?php endif; ?>
              </td>
              <td class="text-right">
                <div class="action-buttons">
                  <?php if ((int)$sub['is_active'] === 1): ?>
                    <form method="POST" action="subscriptions.php" style="display:inline;" onsubmit="return confirm('Deactivate this user subscription?');">
                      <input type="hidden" name="action" value="toggle_sub">
                      <input type="hidden" name="sub_id" value="<?php echo $sub['sub_id']; ?>">
                      <input type="hidden" name="is_active" value="0">
                      <button type="submit" class="btn btn-secondary" style="height: 32px; padding: 0 10px; font-size: 12px; color: #dc2626;">
                        Deactivate
                      </button>
                    </form>
                  <?php else: ?>
                    <form method="POST" action="subscriptions.php" style="display:inline;">
                      <input type="hidden" name="action" value="toggle_sub">
                      <input type="hidden" name="sub_id" value="<?php echo $sub['sub_id']; ?>">
                      <input type="hidden" name="is_active" value="1">
                      <button type="submit" class="btn btn-secondary" style="height: 32px; padding: 0 10px; font-size: 12px; color: #16a34a;">
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>