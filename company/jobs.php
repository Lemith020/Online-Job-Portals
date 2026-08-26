<?php
$page_title = "Manage Jobs";
$page_css = "jobs.css";
$page_js = "jobs.js";
$active_page = "jobs";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// ---- Handle Add / Edit job form submit ----
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_job'])) {
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category_id = (int) $_POST['category_id'];
    $location    = mysqli_real_escape_string($conn, $_POST['location']);
    $salary_min  = (float) $_POST['salary_min'];
    $salary_max  = (float) $_POST['salary_max'];
    $job_type    = mysqli_real_escape_string($conn, $_POST['job_type']);
    $expiry_date = mysqli_real_escape_string($conn, $_POST['expiry_date']);

    if (!empty($_POST['job_id'])) {
        $job_id = (int) $_POST['job_id'];
        $sql = "UPDATE jobs SET
                    category_id = $category_id,
                    title = '$title',
                    description = '$description',
                    location = '$location',
                    salary_min = $salary_min,
                    salary_max = $salary_max,
                    job_type = '$job_type',
                    expiry_date = '$expiry_date'
                WHERE job_id = $job_id AND company_id = $company_id";
    } else {
        $sql = "INSERT INTO jobs
                    (company_id, category_id, title, description, location, salary_min, salary_max, job_type, posted_date, expiry_date, status)
                VALUES
                    ($company_id, $category_id, '$title', '$description', '$location', $salary_min, $salary_max, '$job_type', CURDATE(), '$expiry_date', 'pending')";
    }
    mysqli_query($conn, $sql);
    header("Location: jobs.php");
    exit;
}

// ---- Handle Delete ----
if (isset($_GET['delete'])) {
    $del_id = (int) $_GET['delete'];
    mysqli_query($conn, "DELETE FROM jobs WHERE job_id = $del_id AND company_id = $company_id");
    header("Location: jobs.php");
    exit;
}

// ---- Load job for editing ----
$edit_job = null;
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $edit_result = mysqli_query($conn, "SELECT * FROM jobs WHERE job_id = $edit_id AND company_id = $company_id");
    $edit_job = mysqli_fetch_assoc($edit_result);
}

// ---- Filters ----
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$where = "WHERE j.company_id = $company_id";
if ($status_filter != 'all') {
    $status_filter_safe = mysqli_real_escape_string($conn, $status_filter);
    $where .= " AND j.status = '$status_filter_safe'";
}
if ($search != '') {
    $search_safe = mysqli_real_escape_string($conn, $search);
    $where .= " AND j.title LIKE '%$search_safe%'";
}

$order = "ORDER BY j.posted_date DESC";
if ($sort == 'oldest') $order = "ORDER BY j.posted_date ASC";
if ($sort == 'expiry') $order = "ORDER BY j.expiry_date ASC";

$jobs_sql = "SELECT j.*, c.category_name FROM jobs j
             LEFT JOIN categories c ON j.category_id = c.category_id
             $where $order";
$jobs_result = mysqli_query($conn, $jobs_sql);

$categories_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");
?>

<div class="page-header">
    <h1>Manage Job Postings</h1>
    <button class="btn btn-primary" onclick="openJobModal()">
        <i class="fa-solid fa-plus"></i> Post New Job
    </button>
</div>

<div class="filters-bar">
    <form method="get" class="search-input">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="q" class="form-control" placeholder="Search job title..." value="<?php echo htmlspecialchars($search); ?>">
    </form>

    <select class="form-control" style="width:auto;" onchange="location = 'jobs.php?status=' + this.value">
        <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Statuses</option>
        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
        <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Approved</option>
        <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
    </select>

    <select class="form-control" style="width:auto;" onchange="location = 'jobs.php?sort=' + this.value">
        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
        <option value="oldest" <?php echo $sort == 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
        <option value="expiry" <?php echo $sort == 'expiry' ? 'selected' : ''; ?>>Expiry Soon</option>
    </select>
</div>

<?php if (mysqli_num_rows($jobs_result) > 0) : ?>
    <?php while ($job = mysqli_fetch_assoc($jobs_result)) : ?>
    <div class="list-item">
        <div>
            <div class="list-item-title"><?php echo htmlspecialchars($job['title']); ?></div>
            <div class="list-item-meta">
                <span><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                <span><i class="fa-solid fa-tag"></i> <?php echo htmlspecialchars($job['category_name']); ?></span>
                <span><i class="fa-solid fa-clock"></i> Posted <?php echo date('d/m/Y', strtotime($job['posted_date'])); ?></span>
                <span><i class="fa-solid fa-hourglass-end"></i> Expires <?php echo date('d/m/Y', strtotime($job['expiry_date'])); ?></span>
            </div>
        </div>

        <div style="display:flex; align-items:center; gap:12px;">
            <span class="badge badge-<?php echo $job['status']; ?>"><?php echo ucfirst($job['status']); ?></span>
            <button class="btn btn-outline btn-sm" onclick='openJobModal(<?php echo json_encode($job); ?>)'>
                <i class="fa-solid fa-pen"></i> Edit
            </button>
            <a href="jobs.php?delete=<?php echo $job['job_id']; ?>" class="btn btn-danger-outline btn-sm" onclick="return confirm('Delete this job?');">
                <i class="fa-solid fa-trash"></i> Delete
            </a>
        </div>
    </div>
    <?php endwhile; ?>
<?php else : ?>
    <div class="empty-state">No jobs found. Click "Post New Job" to add one.</div>
<?php endif; ?>

<!-- Add / Edit Job Modal -->
<div class="modal-overlay" id="jobModal">
    <div class="modal-box">
        <div class="modal-header">
            <h2 id="jobModalTitle">Post New Job</h2>
            <button class="modal-close" onclick="closeJobModal()">&times;</button>
        </div>

        <form method="post">
            <input type="hidden" name="job_id" id="job_id">

            <div class="form-group">
                <label>Job Title</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="description" class="form-control" required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        <?php mysqli_data_seek($categories_result, 0); ?>
                        <?php while ($cat = mysqli_fetch_assoc($categories_result)) : ?>
                        <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" id="location" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Salary Min</label>
                    <input type="number" step="0.01" name="salary_min" id="salary_min" class="form-control">
                </div>
                <div class="form-group">
                    <label>Salary Max</label>
                    <input type="number" step="0.01" name="salary_max" id="salary_max" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Job Type</label>
                    <select name="job_type" id="job_type" class="form-control">
                        <option value="Full-time">Full-time</option>
                        <option value="Part-time">Part-time</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" id="expiry_date" class="form-control" required>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeJobModal()">Cancel</button>
                <button type="submit" name="save_job" class="btn btn-primary btn-block">Submit for Approval</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
