<?php
/**
 * JobPortal.lk - Company Manage Jobs
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'company') {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'] ?? 0;

// Fallback: company_id සොයා ගැනීම
if ($company_id == 0 && isset($conn) && $conn) {
   $c_q = mysqli_query($conn, "SELECT company_id FROM company WHERE user_id = $user_id");
    if ($c_q && $c_row = mysqli_fetch_assoc($c_q)) {
        $company_id = (int)$c_row['company_id'];
        $_SESSION['company_id'] = $company_id; // Session එකට Save කිරීම
    }
}



// ---- Handle Add / Edit job form submit ----
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_job'])) {
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category_id = (int) $_POST['category_id'];
    $location    = mysqli_real_escape_string($conn, $_POST['location']);

    $salary_min  = (!empty($_POST['salary_min']) && is_numeric($_POST['salary_min'])) ? (float)$_POST['salary_min'] : "NULL";
    $salary_max  = (!empty($_POST['salary_max']) && is_numeric($_POST['salary_max'])) ? (float)$_POST['salary_max'] : "NULL";

    $job_type    = mysqli_real_escape_string($conn, $_POST['job_type']);
    $expiry_date = mysqli_real_escape_string($conn, $_POST['expiry_date']);

    if ($company_id <= 0) {
        die("Error: No company profile found for this user account. Please contact support or setup your company profile first.");
    }

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
    if (mysqli_query($conn, $sql)) {
        header("Location: jobs.php?msg=success");
        exit();
    } else {
        die("Database Error: " . mysqli_error($conn));
    }
}

// ---- Handle Delete ----
if (isset($_GET['delete'])) {
    $del_id = (int) $_GET['delete'];
    mysqli_query($conn, "DELETE FROM jobs WHERE job_id = $del_id AND company_id = $company_id");
    header("Location: jobs.php");
    exit;
}


$page_title = "Manage Jobs";
$active_page = "jobs";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/company-sidebar.php';

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

<main class="main-content">
    <div class="page-header">
        <h1>Manage Job Postings</h1>
        <button class="btn btn-primary" onclick="openJobModal()">
            <i class="fa-solid fa-plus"></i> Post New Job
        </button>
    </div>

    <!-- Filters Bar -->
    <div class="filters-bar card mb-4">
        <form method="get" action="jobs.php" style="display:flex; gap:12px; width:100%; flex-wrap:wrap;">
            <div class="search-input" style="flex:1; min-width:200px;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" class="form-control" placeholder="Search job title..." value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <select name="status" class="form-control" style="width:auto;" onchange="this.form.submit()">
                <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Statuses</option>
                <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Approved</option>
                <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            </select>

            <select name="sort" class="form-control" style="width:auto;" onchange="this.form.submit()">
                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                <option value="oldest" <?php echo $sort == 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                <option value="expiry" <?php echo $sort == 'expiry' ? 'selected' : ''; ?>>Expiry Soon</option>
            </select>
        </form>
    </div>

    <!-- Job Items List -->
    <?php if ($jobs_result && mysqli_num_rows($jobs_result) > 0) : ?>
        <?php while ($job = mysqli_fetch_assoc($jobs_result)) : ?>
        <div class="list-item">
            <div class="list-item-main">
                <div class="list-item-title"><?php echo htmlspecialchars($job['title']); ?></div>
                <div class="list-item-meta">
                    <span class="meta-tag"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                    <span class="meta-tag"><i class="fa-solid fa-tag"></i> <?php echo htmlspecialchars($job['category_name'] ?? 'General'); ?></span>
                    <span class="meta-tag"><i class="fa-regular fa-calendar-check"></i> Posted <?php echo date('d/m/Y', strtotime($job['posted_date'])); ?></span>
                    <span class="meta-tag"><i class="fa-regular fa-clock"></i> Expires <?php echo date('d/m/Y', strtotime($job['expiry_date'])); ?></span>
                </div>
            </div>

            <div class="list-item-actions">
                <span class="badge badge-<?php echo strtolower($job['status']); ?>"><?php echo ucfirst($job['status']); ?></span>
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
        <div class="card empty-state">No jobs found. Click "Post New Job" to add one.</div>
    <?php endif; ?>

    <!-- Add / Edit Job Modal -->
    <div class="modal-overlay" id="jobModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2 id="jobModalTitle">Post New Job</h2>
                <button class="modal-close" onclick="closeJobModal()">&times;</button>
            </div>

            <form method="post" action="jobs.php">
                <input type="hidden" name="job_id" id="job_id">

                <div class="form-group">
                    <label>Job Title</label>
                    <input type="text" name="title" id="title" class="form-control" required placeholder="e.g. Senior Software Engineer">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="description" class="form-control" required placeholder="Job responsibilities and requirements..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <?php if ($categories_result && mysqli_num_rows($categories_result) > 0) : ?>
                                <?php mysqli_data_seek($categories_result, 0); ?>
                                <?php while ($cat = mysqli_fetch_assoc($categories_result)) : ?>
                                    <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <option value="1">General</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" id="location" class="form-control" required placeholder="e.g. Colombo / Remote">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Salary Min (LKR)</label>
                        <input type="number" step="0.01" name="salary_min" id="salary_min" class="form-control" placeholder="e.g. 100000">
                    </div>
                    <div class="form-group">
                        <label>Salary Max (LKR)</label>
                        <input type="number" step="0.01" name="salary_max" id="salary_max" class="form-control" placeholder="e.g. 200000">
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
</main>

<!-- JS for Modal Control -->
<script>
function openJobModal(jobData = null) {
    const modal = document.getElementById('jobModal');
    const modalTitle = document.getElementById('jobModalTitle');
    
    if (jobData) {
        modalTitle.innerText = "Edit Job Posting";
        document.getElementById('job_id').value = jobData.job_id;
        document.getElementById('title').value = jobData.title;
        document.getElementById('description').value = jobData.description;
        document.getElementById('category_id').value = jobData.category_id;
        document.getElementById('location').value = jobData.location;
        document.getElementById('salary_min').value = jobData.salary_min || '';
        document.getElementById('salary_max').value = jobData.salary_max || '';
        document.getElementById('job_type').value = jobData.job_type;
        document.getElementById('expiry_date').value = jobData.expiry_date;
    } else {
        modalTitle.innerText = "Post New Job";
        document.getElementById('job_id').value = '';
        document.getElementById('title').value = '';
        document.getElementById('description').value = '';
        document.getElementById('location').value = '';
        document.getElementById('salary_min').value = '';
        document.getElementById('salary_max').value = '';
        document.getElementById('expiry_date').value = '';
    }
    
    modal.classList.add('open');
}

function closeJobModal() {
    document.getElementById('jobModal').classList.remove('open');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>