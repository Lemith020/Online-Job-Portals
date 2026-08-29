<?php
// ============================================
// SEEKER DASHBOARD
// Put this file in: /seeker/index.php
// ============================================

require_once '../includes/auth.php';
requireRole('job_seeker');
require_once '../config/database.php';
require_once '../includes/functions.php';

$user_id    = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'] ?? 'User';

$seeker_id = get_seeker_id($conn, $user_id);

$total_applications = get_total_applications($conn, $seeker_id);
$pending_interviews = get_pending_interviews($conn, $seeker_id);
$subscription       = get_subscription_status($conn, $user_id);
$profile_completion = get_profile_completion($conn, $seeker_id);
$recent_apps        = get_recent_applications($conn, $seeker_id, 4);

include '../includes/header.php';
include '../includes/navbar.php';   
?>

<div class="dashboard-layout">
    <?php include '../includes/seeker-sidebar.php'; ?>
    <main class="main-content">
        <h2>Welcome back, <?php echo htmlspecialchars($first_name); ?>!</h2>

        <!-- STAT CARDS -->
        <div class="stats-grid">

            <div class="card">
                <h4>Total Applications</h4>
                <p class="big-number"><?php echo $total_applications; ?></p>
                <span>Jobs Applied</span>
            </div>

            <div class="card">
                <h4>Pending Interviews</h4>
                <p class="big-number"><?php echo $pending_interviews; ?></p>
                <span>Scheduled Interviews</span>
            </div>

            <div class="card">
                <h4>Active CV Status</h4>
                <p class="big-number"><?php echo $subscription['status']; ?></p>
                <span>Subscription until <?php echo $subscription['end_date']; ?></span>
            </div>

            <div class="card">
                <h4>Profile Completion %</h4>
                <p class="big-number"><?php echo $profile_completion; ?>%</p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $profile_completion; ?>%;"></div>
                </div>
            </div>

        </div>

        <!-- RECENT APPLICATIONS TABLE -->
        <div class="recent-applications">
            <h3>Recent Applications</h3>
            <table>
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Company Name</th>
                        <th>Application Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recent_apps) === 0): ?>
                        <tr><td colspan="4">No applications yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recent_apps as $app): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($app['title']); ?></td>
                                <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($app['apply_date'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($app['status']); ?>">
                                        <?php echo ucfirst($app['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>