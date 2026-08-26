<?php
$page_title = "Dashboard";
$page_css = "dashboard.css";
$active_page = "dashboard";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$total_jobs_sql = "SELECT COUNT(*) AS total FROM jobs WHERE company_id = $company_id";
$total_jobs = mysqli_fetch_assoc(mysqli_query($conn, $total_jobs_sql))['total'];

$active_jobs_sql = "SELECT COUNT(*) AS total FROM jobs
                     WHERE company_id = $company_id AND status = 'approved' AND expiry_date >= CURDATE()";
$active_jobs = mysqli_fetch_assoc(mysqli_query($conn, $active_jobs_sql))['total'];

$applicants_sql = "SELECT COUNT(*) AS total FROM applications a
                    JOIN jobs j ON a.job_id = j.job_id
                    WHERE j.company_id = $company_id";
$total_applicants = mysqli_fetch_assoc(mysqli_query($conn, $applicants_sql))['total'];

$pending_interviews_sql = "SELECT COUNT(*) AS total FROM interviews i
                            JOIN applications a ON i.app_id = a.app_id
                            JOIN jobs j ON a.job_id = j.job_id
                            WHERE j.company_id = $company_id AND i.status = 'Scheduled'";
$pending_interviews = mysqli_fetch_assoc(mysqli_query($conn, $pending_interviews_sql))['total'];

$recent_sql = "SELECT a.app_id, j.title, u.first_name, u.last_name, a.apply_date, a.status
                FROM applications a
                JOIN jobs j ON a.job_id = j.job_id
                JOIN job_seekers s ON a.seeker_id = s.seeker_id
                JOIN users u ON s.user_id = u.user_id
                WHERE j.company_id = $company_id
                ORDER BY a.apply_date DESC
                LIMIT 5";
$recent_result = mysqli_query($conn, $recent_sql);
?>

<div class="page-header">
    <h1>Welcome back, <?php echo htmlspecialchars($company['company_name']); ?>!</h1>
</div>

<div class="stat-grid">
    <div class="card stat-card">
        <i class="fa-solid fa-briefcase stat-icon"></i>
        <span class="stat-value"><?php echo $total_jobs; ?></span>
        <span class="stat-label">Total Jobs Posted</span>
    </div>
    <div class="card stat-card">
        <i class="fa-solid fa-bolt stat-icon"></i>
        <span class="stat-value"><?php echo $active_jobs; ?></span>
        <span class="stat-label">Active Jobs</span>
    </div>
    <div class="card stat-card">
        <i class="fa-solid fa-users stat-icon"></i>
        <span class="stat-value"><?php echo $total_applicants; ?></span>
        <span class="stat-label">Total Applicants Received</span>
    </div>
    <div class="card stat-card">
        <i class="fa-solid fa-calendar-days stat-icon"></i>
        <span class="stat-value"><?php echo $pending_interviews; ?></span>
        <span class="stat-label">Pending Interviews</span>
    </div>
</div>

<div class="card">
    <h2 style="margin-bottom: 4px;">Recent Applicants</h2>
    <p style="color: var(--muted); font-size: 13px; margin-bottom: 16px;">Last 5 applicants across all jobs.</p>

    <?php if (mysqli_num_rows($recent_result) > 0) : ?>
    <table class="dash-table">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Applicant Name</th>
                <th>Applied Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($recent_result)) : ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['apply_date'])); ?></td>
                <td><span class="badge badge-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else : ?>
        <div class="empty-state">No applicants yet.</div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
