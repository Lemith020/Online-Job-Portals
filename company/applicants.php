<?php
$page_title = "Applicants";
$page_css = "applicants.css";
$page_js = "applicants.js";
$active_page = "applicants";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// ---- Update application status ----
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $app_id = (int) $_POST['app_id'];
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);

    $check_sql = "SELECT a.app_id FROM applications a
                  JOIN jobs j ON a.job_id = j.job_id
                  WHERE a.app_id = $app_id AND j.company_id = $company_id";
    if (mysqli_num_rows(mysqli_query($conn, $check_sql)) > 0) {
        mysqli_query($conn, "UPDATE applications SET status = '$new_status' WHERE app_id = $app_id");
    }
    header("Location: applicants.php" . (isset($_POST['redirect_qs']) ? '?' . $_POST['redirect_qs'] : ''));
    exit;
}

// ---- Filters ----
$job_filter = isset($_GET['job_id']) ? (int) $_GET['job_id'] : 0;
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$where = "WHERE j.company_id = $company_id";
if ($job_filter > 0) $where .= " AND j.job_id = $job_filter";
if ($status_filter != 'all') {
    $status_safe = mysqli_real_escape_string($conn, $status_filter);
    $where .= " AND a.status = '$status_safe'";
}
if ($search != '') {
    $search_safe = mysqli_real_escape_string($conn, $search);
    $where .= " AND (u.first_name LIKE '%$search_safe%' OR u.last_name LIKE '%$search_safe%')";
}

$order = "ORDER BY a.apply_date DESC";
if ($sort == 'oldest') $order = "ORDER BY a.apply_date ASC";

$applicants_sql = "SELECT a.*, u.first_name, u.last_name, s.phone, s.bio, j.title AS job_title, cv.file_path
                    FROM applications a
                    JOIN job_seekers s ON a.seeker_id = s.seeker_id
                    JOIN users u ON s.user_id = u.user_id
                    JOIN jobs j ON a.job_id = j.job_id
                    LEFT JOIN cvs cv ON a.cv_id = cv.cv_id
                    $where $order";
$applicants_result = mysqli_query($conn, $applicants_sql);

$jobs_result = mysqli_query($conn, "SELECT job_id, title FROM jobs WHERE company_id = $company_id ORDER BY title");
?>

<div class="page-header">
    <h1>Applicants</h1>
</div>

<div class="filters-bar">
    <form method="get" class="search-input">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="q" class="form-control" placeholder="Search seeker name..." value="<?php echo htmlspecialchars($search); ?>">
    </form>

    <select class="form-control" style="width:auto;" onchange="location='applicants.php?job_id=' + this.value">
        <option value="0">All Jobs</option>
        <?php while ($j = mysqli_fetch_assoc($jobs_result)) : ?>
        <option value="<?php echo $j['job_id']; ?>" <?php echo $job_filter == $j['job_id'] ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($j['title']); ?>
        </option>
        <?php endwhile; ?>
    </select>

    <select class="form-control" style="width:auto;" onchange="location='applicants.php?status=' + this.value">
        <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Applicants</option>
        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
        <option value="reviewed" <?php echo $status_filter == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
        <option value="accepted" <?php echo $status_filter == 'accepted' ? 'selected' : ''; ?>>Accepted</option>
        <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
    </select>

    <select class="form-control" style="width:auto;" onchange="location='applicants.php?sort=' + this.value">
        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
        <option value="oldest" <?php echo $sort == 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
    </select>
</div>

<?php if (mysqli_num_rows($applicants_result) > 0) : ?>
    <?php while ($app = mysqli_fetch_assoc($applicants_result)) : ?>
    <div class="list-item">
        <div>
            <div class="list-item-title"><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></div>
            <div class="list-item-meta">
                <span><i class="fa-solid fa-briefcase"></i> <?php echo htmlspecialchars($app['job_title']); ?></span>
                <span><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($app['phone']); ?></span>
                <span><i class="fa-solid fa-calendar"></i> Applied <?php echo date('d/m/Y', strtotime($app['apply_date'])); ?></span>
            </div>
            <?php if (!empty($app['experience'])) : ?>
            <p style="font-size:13px; color:var(--muted); margin-top:8px; max-width:520px;">
                <?php echo htmlspecialchars($app['experience']); ?>
            </p>
            <?php endif; ?>
        </div>

        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <span class="badge badge-<?php echo $app['status']; ?>"><?php echo ucfirst($app['status']); ?></span>

            <?php if ($app['file_path']) : ?>
            <a href="<?php echo htmlspecialchars($app['file_path']); ?>" target="_blank" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-file"></i> View CV
            </a>
            <?php endif; ?>

            <form method="post" class="status-form">
                <input type="hidden" name="app_id" value="<?php echo $app['app_id']; ?>">
                <input type="hidden" name="redirect_qs" value="<?php echo htmlspecialchars($_SERVER['QUERY_STRING']); ?>">
                <select name="status" class="form-control btn-sm" onchange="this.form.submit()">
                    <option value="pending" <?php echo $app['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="reviewed" <?php echo $app['status'] == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                    <option value="accepted" <?php echo $app['status'] == 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                    <option value="rejected" <?php echo $app['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
                <input type="hidden" name="update_status" value="1">
            </form>

            <?php if ($app['status'] == 'reviewed' || $app['status'] == 'accepted') : ?>
            <a href="interviews.php?app_id=<?php echo $app['app_id']; ?>" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-calendar-plus"></i> Schedule Interview
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endwhile; ?>
<?php else : ?>
    <div class="empty-state">No applicants found.</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
