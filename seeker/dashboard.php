<?php
// seeker/dashboard.php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$seeker_id = get_seeker_id($conn, $_SESSION['user_id']);

$total_applications = get_total_applications($conn, $seeker_id);
$pending_interviews = get_pending_interviews($conn, $seeker_id);
$subscription = get_subscription_status($conn, $_SESSION['user_id']);
$profile_completion = get_profile_completion($conn, $seeker_id);
$recent_applications = get_recent_applications($conn, $seeker_id, 5);

$page_title = "Dashboard";
$page_css = "seeker-dashboard.css";
$page_js = "seeker-dashboard.js";
require_once '../includes/header.php';
require_once '../includes/seeker-sidebar.php';
?>

<h1 class="page-title">Welcome back, <?= clean($_SESSION['first_name'] ?? '') ?>!</h1>

<div class="card-grid">
    <div class="card">
        <div class="card-label">Total Applications</div>
        <div class="card-value"><?= $total_applications ?></div>
        <div class="card-sub">Jobs Applied</div>
    </div>
    <div class="card">
        <div class="card-label">Pending Interviews</div>
        <div class="card-value"><?= $pending_interviews ?></div>
        <div class="card-sub">Scheduled Interviews</div>
    </div>
    <div class="card">
        <div class="card-label">Active CV Status</div>
        <div class="card-value"><?= $subscription['status'] ?></div>
        <div class="card-sub">
            <?= $subscription['plan_name'] ? "Subscription until " . $subscription['end_date'] : "No active plan" ?>
        </div>
    </div>
    <div class="card">
        <div class="card-label">Profile Completion %</div>
        <div class="card-value"><?= $profile_completion ?>%</div>
        <div class="progress-bar"><div class="progress-fill" style="width:<?= $profile_completion ?>%"></div></div>
    </div>
</div>

<div class="card">
    <h2 class="section-title">Recent Applications</h2>
    <table>
        <thead>
            <tr><th>Job Title</th><th>Company Name</th><th>Application Date</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php if ($recent_applications): ?>
            <?php foreach ($recent_applications as $app): ?>
                <tr>
                    <td><?= clean($app['title']) ?></td>
                    <td><?= clean($app['company_name']) ?></td>
                    <td><?= formatDate($app['apply_date']) ?></td>
                    <td><span class="badge <?= status_badge_class($app['status']) ?>"><?= clean(ucfirst($app['status'])) ?></span></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">No applications yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
