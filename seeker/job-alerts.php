<?php
// seeker/job-alerts.php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$seeker_id = get_seeker_id($conn, $_SESSION['user_id']);

// ---- add new alert ----
if (isset($_POST['add_alert'])) {
    $keyword = trim($_POST['suggest_job']);
    $location = trim($_POST['location_pref']);
    if ($keyword !== '') {
        add_job_alert($conn, $seeker_id, $keyword, $location);
    }
    redirect("job-alerts.php");
}

// ---- toggle on/off ----
if (isset($_GET['toggle'])) {
    toggle_job_alert($conn, (int)$_GET['toggle'], $seeker_id);
    redirect("job-alerts.php");
}

// ---- delete alert ----
if (isset($_GET['delete'])) {
    delete_job_alert($conn, (int)$_GET['delete'], $seeker_id);
    redirect("job-alerts.php");
}

$alerts = get_job_alerts($conn, $seeker_id);


$matching_jobs = get_matching_jobs_for_alerts($conn, $seeker_id);

$page_title = "Job Alerts";
$page_css = "../assets/css/seeker_page_css/job-alerts.css";
require_once '../includes/seeker-header.php';
require_once '../includes/seeker-sidebar.php';
?>

<h1 class="page-title">Job Alerts</h1>

<div class="card" style="margin-bottom:24px;">
    <h2>🔔 Matching Job Alerts</h2>
    <div class="job-matches-list" style="margin-top: 15px;">
    <?php if (!empty($matching_jobs)): ?>
        <?php foreach ($matching_jobs as $job): ?>
            <div class="job-match-item" style="border-bottom: 1px solid #ddd; padding: 12px 0; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0; font-size: 18px;"><?= clean($job['title']) ?></h3>
                    <div style="color: #666; font-size: 14px;">📍 <?= clean($job['location']) ?> | 🕒 <?= clean($job['job_type']) ?></div>
                    <small style="color: #888;">Posted on: <?= clean($job['posted_date']) ?></small>
                </div>
                <a href="browse-jobs.php?id=<?= $job['job_id'] ?>" class="btn btn-primary">View Job</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: #777;">No jobs matching your alerts have been posted yet.</p>
    <?php endif; ?>
    </div>
</div>

<!-- My Saved Alerts Section -->
<div class="card" style="margin-bottom:16px;">
    <h3>My Alert Preferences</h3>
    <div class="alert-list">
    <?php if ($alerts): ?>
        <?php foreach ($alerts as $alert): ?>
            <div class="alert-item">
                <div>
                    <strong><?= clean($alert['suggest_job']) ?></strong>
                    <div class="alert-location">📍 <?= clean($alert['location_pref'] ?: 'Any location') ?></div>
                </div>
                <div class="alert-actions">
                    <label class="switch">
                        <input type="checkbox" onclick="location.href='job-alerts.php?toggle=<?= $alert['alert_id'] ?>'" <?= $alert['selects_or_not'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                    <a href="job-alerts.php?delete=<?= $alert['alert_id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this alert?')">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No job alerts set yet.</p>
    <?php endif; ?>
    </div>
</div>

<div class="card">
    <button type="button" class="btn btn-primary" onclick="toggleForm()">+ Add New Alert</button>
    <form method="POST" id="alert-form" class="add-alert-form" style="display: none; margin-top: 15px;">
        <div class="form-row">
            <div class="form-group">
                <label>Job Keyword</label>
                <input type="text" name="suggest_job" placeholder="e.g. Software Engineer" required>
            </div>
            <div class="form-group">
                <label>Location Preference</label>
                <input type="text" name="location_pref" placeholder="e.g. Colombo">
            </div>
        </div>
        <button type="submit" name="add_alert" class="btn btn-primary" style="margin-top: 10px;">Save</button>
    </form>
</div>

<script>
function toggleForm() {
    var form = document.getElementById("alert-form");
    if (form) {
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>