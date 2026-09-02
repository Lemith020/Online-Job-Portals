<?php
/**
 * JobPortal.lk - Reviews & Ratings Moderation
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Review Moderation';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $id = (int)($_POST['id'] ?? 0);
        $new_status = $_POST['status'] ?? 'Approved';
        update_review_status($id, $new_status);
        add_activity("Updated review #$id status to $new_status", "review");
        set_flash("Review status updated to {$new_status}.", "success");
        header("Location: " . BASE_URL . "/admin/reviews.php");
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        delete_review_admin($id);
        add_activity("Deleted review #$id", "review");
        set_flash("Review deleted permanently.", "success");
        header("Location: " . BASE_URL . "/admin/reviews.php");
        exit;
    }
}

$status_filter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');
$reviews = get_all_reviews_admin($status_filter, $search);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div class="page-title-group">
    <h1 class="page-title">Reviews & Feedback Moderation</h1>
    <p class="page-subtitle">Moderate candidate interview reviews, investigate reported feedback, and ensure quality.</p>
  </div>
</div>

<!-- Filter Bar -->
<div class="card table-filter-card">
  <div class="card-body filter-bar-body">
    <form method="GET" action="reviews.php" class="filter-form">
      <div class="filter-group">
        <label>Status:</label>
        <select name="status" class="form-select" onchange="this.form.submit()">
          <option value="" <?php echo ($status_filter === '') ? 'selected' : ''; ?>>All Reviews</option>
          <option value="Flagged" <?php echo ($status_filter === 'Flagged') ? 'selected' : ''; ?>>Flagged / Reported Only</option>
          <option value="Approved" <?php echo ($status_filter === 'Approved') ? 'selected' : ''; ?>>Approved Only</option>
        </select>
      </div>

      <div class="filter-group search-group">
        <label>Search Comments:</label>
        <div class="input-with-icon">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" name="search" class="form-input" placeholder="Search by job, candidate, or comment keywords..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
      </div>

      <div class="filter-actions">
        <button type="submit" class="btn btn-secondary">Filter</button>
        <?php if (!empty($status_filter) || !empty($search)): ?>
          <a href="reviews.php" class="btn btn-outline">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<!-- Reviews Table Card -->
<div class="card">
  <div class="card-header flex-between">
    <h3 class="card-title">Reviews & Ratings (<?php echo count($reviews); ?>)</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Company / Position</th>
            <th>Candidate</th>
            <th>Rating</th>
            <th>Review Feedback</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reviews)): ?>
            <tr>
              <td colspan="6" class="text-center py-4">
                <div class="empty-state">
                  <i class="fa-solid fa-star-half-stroke empty-icon"></i>
                  <p>No reviews found matching criteria.</p>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($reviews as $r): ?>
              <tr class="<?php echo ($r['status'] === 'Flagged') ? 'row-flagged' : ''; ?>">
                <td>
                  <strong><?php echo htmlspecialchars($r['job_title']); ?></strong>
                  <small class="d-block text-muted"><?php echo date('M d, Y', strtotime($r['created_at'])); ?></small>
                </td>
                <td>
                  <span class="user-display-name"><?php echo htmlspecialchars($r['seeker_name']); ?></span>
                </td>
                <td>
                  <div class="rating-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <i class="fa-solid fa-star <?php echo ($i <= $r['rating']) ? 'star-filled' : 'star-empty'; ?>"></i>
                    <?php endfor; ?>
                    <span class="rating-num">(<?php echo $r['rating']; ?>/5)</span>
                  </div>
                </td>
                <td>
                  <p class="review-comment-text">"<?php echo htmlspecialchars($r['comment']); ?>"</p>
                </td>
                <td>
                  <?php if ($r['status'] === 'Flagged'): ?>
                    <span class="status-pill status-danger">⚠️ Flagged</span>
                  <?php else: ?>
                    <span class="status-pill status-active">✓ Approved</span>
                  <?php endif; ?>
                </td>
                <td class="text-right">
                  <div class="action-buttons">
                    <?php if ($r['status'] === 'Flagged'): ?>
                      <form method="POST" action="reviews.php" style="display:inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                        <input type="hidden" name="status" value="Approved">
                        <button type="submit" class="btn btn-sm btn-success" title="Approve / Unflag">
                          <i class="fa-solid fa-check"></i> Approve
                        </button>
                      </form>
                    <?php endif; ?>

                    <form method="POST" action="reviews.php" style="display:inline;" onsubmit="return confirm('Permanently remove this review?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                      <button type="submit" class="btn-icon text-danger" title="Delete Review">
                        <i class="fa-regular fa-trash-can"></i>
                      </button>
                    </form>
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
