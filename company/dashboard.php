<?php
/**
 * JobPortal.lk - Company Dashboard
 */
require_once __DIR__ . '/../config/database.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'company') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'] ?? 0;


if ($company_id == 0 && isset($conn)) {
    $c_q = mysqli_query($conn, "SELECT company_id FROM company WHERE user_id = $user_id");
    if ($c_q && $c_row = mysqli_fetch_assoc($c_q)) {
        $company_id = $c_row['company_id'];
        $_SESSION['company_id'] = $company_id;
    }
}

$page_title = "Dashboard";
$active_page = "dashboard";


$page_css = BASE_URL . "/assets/css/company_page_css/dashboard.css";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/company-sidebar.php';

$company = ['company_name' => 'Company'];
$total_jobs = 0;
$active_jobs = 0;
$total_applicants = 0;
$pending_interviews = 0;
$recent_result = false;

if ($company_id > 0 && isset($conn)) {
    // 1. Company Name
    $comp_query = mysqli_query($conn, "SELECT company_name FROM company WHERE company_id = $company_id");
    if ($comp_query && mysqli_num_rows($comp_query) > 0) {
        $company = mysqli_fetch_assoc($comp_query);
    }

    // 2. Total Jobs
    $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM jobs WHERE company_id = $company_id");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        $total_jobs = $row['total'] ?? 0;
    }

    // 3. Active Jobs
    $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM jobs WHERE company_id = $company_id AND status = 'approved' AND expiry_date >= CURDATE()");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        $active_jobs = $row['total'] ?? 0;
    }

    // 4. Total Applicants
    $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications a JOIN jobs j ON a.job_id = j.job_id WHERE j.company_id = $company_id");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        $total_applicants = $row['total'] ?? 0;
    }

    // 5. Pending Interviews
    $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM interviews i JOIN applications a ON i.app_id = a.app_id JOIN jobs j ON a.job_id = j.job_id WHERE j.company_id = $company_id AND i.status = 'Scheduled'");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        $pending_interviews = $row['total'] ?? 0;
    }

    // 6. Recent Applicants
    $recent_sql = "SELECT a.app_id, j.title, u.first_name, u.last_name, a.apply_date, a.status
                   FROM applications a
                   JOIN jobs j ON a.job_id = j.job_id
                   JOIN job_seekers s ON a.seeker_id = s.seeker_id
                   JOIN users u ON s.user_id = u.user_id
                   WHERE j.company_id = $company_id
                   ORDER BY a.apply_date DESC
                   LIMIT 5";
    $recent_result = mysqli_query($conn, $recent_sql);
}
?>

<main class="main-content">

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

    <?php if ($recent_result && mysqli_num_rows($recent_result) > 0) : ?>
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
</main> 
<?php require_once __DIR__ . '/../includes/footer.php'; ?>