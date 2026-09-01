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

$page_title = "Job Alerts";
$page_css = "../assets/css/seeker_page_css/job-alerts.css";
$page_js = "../assets/js/seeker_page_js/job-alerts.js";
require_once '../includes/seeker-header.php';
require_once '../includes/seeker-sidebar.php';
?>

<h1 class="page-title">Job Alerts</h1>

<div class="card" style="margin-bottom:16px;">
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
        <p>No job alerts yet.</p>
    <?php endif; ?>
    </div>
</div>

<div class="card">
    <button type="button" class="btn btn-primary" onclick="toggleForm()">+ Add New Alert</button>
    <form method="POST" id="alert-form" class="add-alert-form">
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
        <button type="submit" name="add_alert" class="btn btn-primary">Save</button>
    </form>
</div>

<?php require_once '../includes/seeker-footer.php'; ?>
