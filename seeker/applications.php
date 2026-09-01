<?php
// seeker/applications.php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$seeker_id = get_seeker_id($conn, $_SESSION['user_id']);

// ---- withdraw an application (only allowed while status = pending) ----
if (isset($_GET['withdraw'])) {
    withdraw_application($conn, (int)$_GET['withdraw'], $seeker_id);
    redirect("applications.php");
}

// ---- filter by status ----
$status_filter = $_GET['status'] ?? '';
$applications = get_applications($conn, $seeker_id, $status_filter);

$page_title = "My Applications";
$page_css = "../assets/css/seeker_page_css/applications.css";
$page_js = "../assets/js/seeker_page_js/applications.js";
require_once '../includes/seeker-header.php';
require_once '../includes/seeker-sidebar.php';
?>

<h1 class="page-title">My Applications</h1>

<?php if (isset($_GET['applied'])): ?>
    <div class="alert-success">Application submitted successfully!</div>
<?php endif; ?>

<div class="card" style="margin-bottom:16px; max-width:250px;">
    <form method="GET">
        <label>Filter by Status</label>
        <select name="status" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="reviewed" <?= $status_filter === 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
            <option value="accepted" <?= $status_filter === 'accepted' ? 'selected' : '' ?>>Accepted</option>
            <option value="rejected" <?= $status_filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr><th>Job Title</th><th>Apply Date</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
        <?php if ($applications): ?>
            <?php foreach ($applications as $app): ?>
                <tr class="app-row" onclick="openModal(<?= $app['app_id'] ?>)">
                    <td><?= clean($app['title']) ?> <br><small class="job-company"><?= clean($app['company_name']) ?></small></td>
                    <td><?= formatDate($app['apply_date']) ?></td>
                    <td><span class="badge <?= status_badge_class($app['status']) ?>"><?= clean(ucfirst($app['status'])) ?></span></td>
                    <td><button type="button" class="btn btn-outline">View</button></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">No applications found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- one hidden modal per application; JS shows the matching one -->
<?php foreach ($applications as $app): ?>
<div class="modal-overlay" id="modal-<?= $app['app_id'] ?>">
    <div class="modal-box">
        <h2>Application for <?= clean($app['title']) ?></h2>
        <p><strong>Job Title:</strong> <?= clean($app['title']) ?></p>
        <p><strong>Company:</strong> <?= clean($app['company_name']) ?></p>
        <p><strong>Applied Date:</strong> <?= formatDate($app['apply_date']) ?></p>
        <p><strong>Status:</strong> <span class="badge <?= status_badge_class($app['status']) ?>"><?= clean(ucfirst($app['status'])) ?></span></p>
        <p><strong>CV Used:</strong> <?= clean(basename($app['file_path'])) ?></p>
        <p><strong>Experience/Cover Letter:</strong></p>
        <p class="experience-text"><?= nl2br(clean($app['experience'])) ?></p>
        <div class="modal-actions">
            <button type="button" class="btn btn-outline" onclick="closeModal(<?= $app['app_id'] ?>)">Close</button>
            <?php if ($app['status'] === 'pending'): ?>
                <a href="applications.php?withdraw=<?= $app['app_id'] ?>" class="btn btn-danger" onclick="return confirm('Withdraw this application?')">Withdraw Application</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php require_once '../includes/seeker-footer.php'; ?>
