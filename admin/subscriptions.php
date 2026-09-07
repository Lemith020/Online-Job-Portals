<?php
/**
 * JobPortal.lk - Subscriptions Management
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Subscriptions Management';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle_sub') {
        $sub_id = (int)($_POST['sub_id'] ?? 0);
        $is_active = (int)($_POST['is_active'] ?? 1);
        toggle_user_subscription_status($sub_id, $is_active);
        add_activity("Updated employer subscription #$sub_id active status to $is_active", "subscription");
        set_flash("Subscription status updated successfully.", "success");
        header("Location: " . BASE_URL . "/admin/subscriptions.php");
        exit;
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
    <h1 class="page-title">Employer Subscriptions & Plans</h1>
    <p class="page-subtitle">Manage membership tiers, pricing plans, and corporate subscriber packages.</p>
  </div>
</div>

<!-- Plan Cards Overview -->
<div class="plans-grid">
  <?php foreach ($plans as $p): ?>
    <div class="plan-card">
      <div class="plan-header">
        <h3 class="plan-title"><?php echo htmlspecialchars($p['name']); ?></h3>
        <div class="plan-price">
          <span class="price-val">Rs. <?php echo number_format($p['price'], 2); ?></span>
          <span class="price-period">/ <?php echo $p['duration_days']; ?> days</span>
        </div>
      </div>
      <div class="plan-body">
        <div class="plan-feature-item">
          <i class="fa-solid fa-check text-emerald"></i>
          <span><strong><?php echo $p['max_jobs']; ?></strong> Job Listings Max</span>
        </div>
        <div class="plan-feature-item">
          <i class="fa-solid fa-check text-emerald"></i>
          <span><?php echo htmlspecialchars($p['features']); ?></span>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Active Subscribers Table -->
<div class="card mt-4">
  <div class="card-header flex-between">
    <h3 class="card-title">Corporate Subscribers (<?php echo count($subscriptions); ?>)</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Company</th>
            <th>Active Plan</th>
            <th>Amount</th>
            <th>Duration Dates</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($subscriptions)): ?>
            <tr>
              <td colspan="6" class="text-center py-4">
                <div class="empty-state">
                  <i class="fa-solid fa-credit-card empty-icon"></i>
                  <p>No active subscriptions found.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($subscriptions as $sub): ?>
              <tr>
                <td>
                  <div class="user-row-info">
                    <div class="company-avatar-box">
                      <i class="fa-solid fa-building"></i>
                    </div>
                    <div>
                      <strong><?php echo htmlspecialchars($sub['user_name']); ?></strong>
                      <span class="text-muted d-block"><?php echo htmlspecialchars($sub['email']); ?></span>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge badge-purple"><?php echo htmlspecialchars($sub['plan_name']); ?></span>
                </td>
                <td>
                  <strong>Rs. <?php echo number_format($sub['price'], 2); ?></strong>
                </td>
                <td>
                  <span><?php echo date('M d, Y', strtotime($sub['start_date'])); ?> &rarr; <?php echo date('M d, Y', strtotime($sub['end_date'])); ?></span>
                </td>
                <td>
                  <?php if ($sub['is_active']): ?>
                    <span class="status-pill status-active">● Active</span>
                  <?php else: ?>
                    <span class="status-pill status-danger">● Expired / Inactive</span>
                  <?php endif; ?>
                </td>
                <td class="text-right">
                  <div class="action-buttons">
                    <?php if ($sub['is_active']): ?>
                      <form method="POST" action="subscriptions.php" style="display:inline;" onsubmit="return confirm('Deactivate this subscription?');">
                        <input type="hidden" name="action" value="toggle_sub">
                        <input type="hidden" name="sub_id" value="<?php echo $sub['id']; ?>">
                        <input type="hidden" name="is_active" value="0">
                        <button type="submit" class="btn-icon text-amber" title="Deactivate Plan">
                          <i class="fa-solid fa-pause"></i>
                        </button>
                      </form>
                    <?php else: ?>
                      <form method="POST" action="subscriptions.php" style="display:inline;">
                        <input type="hidden" name="action" value="toggle_sub">
                        <input type="hidden" name="sub_id" value="<?php echo $sub['id']; ?>">
                        <input type="hidden" name="is_active" value="1">
                        <button type="submit" class="btn-icon text-emerald" title="Activate Plan">
                          <i class="fa-solid fa-play"></i>
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
