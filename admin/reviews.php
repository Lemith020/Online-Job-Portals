<?php
/**
 * JobPortal.lk - Review Moderation (Admin)
 * Member 1 - Admin UI
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Review Moderation';

// Handle Action Requests (Delete Review)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $target_id = (int)$_GET['id'];
    delete_review($target_id);
    set_flash('Review was deleted and removed from the public platform.', 'error');
    header('Location: reviews.php');
    exit;
}

// Filters & Search
$search = trim($_GET['search'] ?? '');
$rating_filter = (int)($_GET['rating'] ?? 0);
$sort = trim($_GET['sort'] ?? 'newest');

$reviews = get_all_reviews($search, $rating_filter, $sort);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="page-header">
  <div class="page-title-group">
    <h1>Review Moderation</h1>
    <p>Monitor candidate reviews, audit feedback on company interview experiences, and remove inappropriate content.</p>
  </div>
</div>

<!-- Main Table Card -->
<div class="data-table-card">
  <!-- Table Toolbar -->
  <form method="GET" action="reviews.php" class="table-toolbar">
    <div class="toolbar-search">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search review or job title..." data-table-search="reviewsTable">
    </div>

    <div class="toolbar-filters">
      <!-- Rating Filter Tabs -->
      <div class="filter-tab-group">
        <a href="reviews.php?rating=0&sort=<?php echo urlencode($sort); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($rating_filter === 0) ? 'active' : ''; ?>">All Ratings</a>
        <a href="reviews.php?rating=5&sort=<?php echo urlencode($sort); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($rating_filter === 5) ? 'active' : ''; ?>">5 Stars</a>
        <a href="reviews.php?rating=4&sort=<?php echo urlencode($sort); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($rating_filter === 4) ? 'active' : ''; ?>">4 Stars</a>
        <a href="reviews.php?rating=3&sort=<?php echo urlencode($sort); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($rating_filter === 3) ? 'active' : ''; ?>">3 Stars</a>
        <a href="reviews.php?rating=2&sort=<?php echo urlencode($sort); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($rating_filter === 2) ? 'active' : ''; ?>">2 Stars</a>
        <a href="reviews.php?rating=1&sort=<?php echo urlencode($sort); ?>&search=<?php echo urlencode($search); ?>" class="filter-tab <?php echo ($rating_filter === 1) ? 'active' : ''; ?>">1 Star</a>
      </div>

      <!-- Sort Selection -->
      <select name="sort" class="select-filter" onchange="this.form.submit()">
        <option value="newest" <?php echo ($sort === 'newest') ? 'selected' : ''; ?>>Newest First</option>
        <option value="oldest" <?php echo ($sort === 'oldest') ? 'selected' : ''; ?>>Oldest First</option>
        <option value="lowest" <?php echo ($sort === 'lowest') ? 'selected' : ''; ?>>Lowest Rating</option>
        <option value="highest" <?php echo ($sort === 'highest') ? 'selected' : ''; ?>>Highest Rating</option>
      </select>

      <?php if (!empty($search) || $rating_filter !== 0 || $sort !== 'newest'): ?>
        <a href="reviews.php" class="btn btn-secondary btn-sm">Reset</a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Responsive Reviews Table -->
  <div class="table-responsive">
    <table class="custom-table" id="reviewsTable">
      <thead>
        <tr>
          <th>Job Title</th>
          <th>Seeker Name</th>
          <th>Rating</th>
          <th>Comment</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($reviews)): ?>
          <tr>
            <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
              No reviews found matching the specified filter criteria.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($reviews as $rev): ?>
            <tr>
              <td>
                <span class="primary-text"><?php echo htmlspecialchars($rev['job_title']); ?></span>
              </td>
              <td>
                <div class="user-cell">
                  <div class="user-cell-avatar" style="width:28px; height:28px; font-size:11px;">
                    <?php echo strtoupper(substr($rev['seeker_name'], 0, 1)); ?>
                  </div>
                  <span><?php echo htmlspecialchars($rev['seeker_name']); ?></span>
                </div>
              </td>
              <td>
                <?php echo render_star_rating($rev['rating']); ?>
              </td>
              <td style="max-width: 320px;">
                <div style="color: var(--text-main); line-height: 1.4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                  <?php echo htmlspecialchars($rev['comment']); ?>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" style="margin-top: 6px; padding: 2px 8px; font-size: 11px;" onclick="viewFullComment('<?php echo htmlspecialchars(addslashes($rev['seeker_name'])); ?>', '<?php echo htmlspecialchars(addslashes($rev['job_title'])); ?>', '<?php echo htmlspecialchars(addslashes($rev['comment'])); ?>', <?php echo (int)$rev['rating']; ?>)">
                  View Full Comment
                </button>
              </td>
              <td style="text-align: right;">
                <div class="action-btn-group" style="justify-content: flex-end;">
                  <button type="button" class="btn btn-danger btn-sm" onclick="confirmAction('Are you sure you want to permanently delete this review?', 'reviews.php?action=delete&id=<?= $rev['id'] ?>')">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="table-pagination">
    <span class="secondary-text">Showing <?php echo count($reviews); ?> of <?php echo count($reviews); ?> reviews</span>
    <div class="pagination-controls">
      <button class="page-btn page-btn-wide">Previous</button>
      <button class="page-btn active">1</button>
      <button class="page-btn">2</button>
      <button class="page-btn">3</button>
      <button class="page-btn page-btn-wide">Next</button>
    </div>
  </div>
</div>

<!-- Modal: View Full Comment -->
<div class="modal-backdrop" id="reviewModal">
  <div class="modal-dialog">
    <div class="modal-header">
      <h3 class="modal-title" id="revModalTitle">Candidate Review</h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('reviewModal')">&times;</button>
    </div>
    <div class="modal-body">
      <div style="margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between;">
        <span id="revRatingStars" style="font-size: 18px;"></span>
        <span id="revJobBadge" style="font-size: 12.5px; font-weight: 700; color: var(--primary-blue);"></span>
      </div>
      <div style="background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 16px;">
        <p id="revModalText" style="line-height: 1.6; color: var(--text-main); font-size: 14px; white-space: pre-wrap;"></p>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" onclick="closeModal('reviewModal')">Close</button>
    </div>
  </div>
</div>

<script>
function viewFullComment(seeker, job, comment, rating) {
  document.getElementById('revModalTitle').textContent = 'Review by ' + seeker;
  document.getElementById('revJobBadge').textContent = job;
  document.getElementById('revModalText').textContent = comment;

  let stars = '';
  for (let i = 1; i <= 5; i++) {
    stars += (i <= rating) ? '<span class="star-filled">★</span>' : '<span class="star-empty">☆</span>';
  }
  document.getElementById('revRatingStars').innerHTML = stars;

  openModal('reviewModal');
}
</script>

</main> <!-- Close admin-content -->
</div> <!-- Close admin-main -->
</div> <!-- Close admin-wrapper -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
