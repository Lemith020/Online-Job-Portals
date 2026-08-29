<?php
// seeker/browse-jobs.php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$seeker_id = get_seeker_id($conn, $_SESSION['user_id']);

// ---- read filters from the search form (GET) ----
$keyword  = trim($_GET['keyword'] ?? '');
$location = trim($_GET['location'] ?? '');
$category = $_GET['category'] ?? '';
$job_type = $_GET['job_type'] ?? '';
$salary   = $_GET['salary'] ?? ''; // e.g. "50000-100000"

$salary_min = '';
$salary_max = '';
if ($salary !== '') {
    [$salary_min, $salary_max] = array_map('intval', explode('-', $salary));
}

$filters = [
    'keyword' => $keyword, 'location' => $location, 'category' => $category,
    'job_type' => $job_type, 'salary_min' => $salary_min, 'salary_max' => $salary_max,
];

// ---- pagination ----
$per_page = 5;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

$total_jobs = get_jobs_count($conn, $filters);
$total_pages = max(1, ceil($total_jobs / $per_page));
$jobs = get_jobs($conn, $filters, $per_page, $offset);
$categories = get_categories($conn);

$page_title = "Browse Jobs";
$page_css = "seeker-browse-jobs.css";
$page_js = "seeker-browse-jobs.js";
require_once '../includes/header.php';
require_once '../includes/seeker-sidebar.php';
?>

<h1 class="page-title">Browse Opportunities</h1>

<form method="GET" class="filter-card card">
    <div class="search-row">
        <input type="text" name="keyword" placeholder="Job title, keyword, or skills" value="<?= clean($keyword) ?>">
        <input type="text" name="location" placeholder="Location" value="<?= clean($location) ?>">
        <button type="submit" class="btn btn-primary">Search Jobs</button>
    </div>
    <div class="filter-row">
        <select name="category">
            <option value="">Category</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['category_id'] ?>" <?= $category == $c['category_id'] ? 'selected' : '' ?>>
                    <?= clean($c['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="job_type">
            <option value="">Job Type</option>
            <option value="Full-time" <?= $job_type === 'Full-time' ? 'selected' : '' ?>>Full-time</option>
            <option value="Part-time" <?= $job_type === 'Part-time' ? 'selected' : '' ?>>Part-time</option>
        </select>
        <select name="salary">
            <option value="">Salary Range</option>
            <option value="0-100000" <?= $salary === '0-100000' ? 'selected' : '' ?>>Below 100,000</option>
            <option value="100000-250000" <?= $salary === '100000-250000' ? 'selected' : '' ?>>100,000 - 250,000</option>
            <option value="250000-1000000" <?= $salary === '250000-1000000' ? 'selected' : '' ?>>Above 250,000</option>
        </select>
        <a href="browse-jobs.php" class="reset-link">Reset</a>
    </div>
</form>

<div class="job-list">
    <?php if ($jobs): ?>
        <?php foreach ($jobs as $job): ?>
            <div class="job-card">
                <div class="job-logo"><?= clean(strtoupper(substr($job['company_name'], 0, 1))) ?></div>
                <div class="job-info">
                    <h3><?= clean($job['title']) ?></h3>
                    <p class="job-company"><?= clean($job['company_name']) ?></p>
                    <p class="job-meta">📍 <?= clean($job['location']) ?> &nbsp; 💰 Rs. <?= number_format($job['salary_min']) ?> - Rs. <?= number_format($job['salary_max']) ?> / month</p>
                    <p class="job-posted">Posted <?= formatDate($job['posted_date']) ?></p>
                </div>
                <div class="job-actions">
                    <span class="badge <?= $job['job_type'] === 'Full-time' ? 'badge-accepted' : 'badge-pending' ?>"><?= clean($job['job_type']) ?></span>
                    <a href="apply-job.php?job_id=<?= $job['job_id'] ?>" class="btn btn-primary">Apply Now</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card">No jobs found matching your filters.</div>
    <?php endif; ?>
</div>

<div class="pagination">
    <?php if ($page > 1): ?><a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Previous</a><?php endif; ?>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?><a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a><?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
