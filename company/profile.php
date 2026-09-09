<?php
/**
 * JobPortal.lk - Company Profile Management
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

// Company ID Retrieval & Initialization
$user_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'] ?? 0;

if ($company_id == 0 && isset($conn) && $conn) {
    $c_q = mysqli_query($conn, "SELECT company_id FROM company WHERE user_id = $user_id");
    if ($c_q && $c_row = mysqli_fetch_assoc($c_q)) {
        $company_id = $c_row['company_id'];
        $_SESSION['company_id'] = $company_id;
    }
}

// Fetch Initial Company Data
$company = [
    'company_name'  => '',
    'industry_type' => '',
    'location'      => '',
    'description'   => ''
];

if (isset($conn) && $conn && $company_id > 0) {
    $company_result = mysqli_query($conn, "SELECT * FROM company WHERE company_id = $company_id");
    if ($company_result && $c_data = mysqli_fetch_assoc($company_result)) {
        $company = $c_data;
    }
}

$page_title = "Company Profile";
$active_page = "profile";

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/company-sidebar.php';

$saved = false;

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_profile'])) {
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $industry_type = mysqli_real_escape_string($conn, $_POST['industry_type']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $sql = "UPDATE company SET
                company_name = '$company_name',
                industry_type = '$industry_type',
                location = '$location',
                description = '$description'
            WHERE company_id = $company_id";
    
    if (mysqli_query($conn, $sql)) {
        // Refresh $company data with updated values
        $company_result = mysqli_query($conn, "SELECT * FROM company WHERE company_id = $company_id");
        if ($company_result && $c_data = mysqli_fetch_assoc($company_result)) {
            $company = $c_data;
        }
        $saved = true;
    }
}
?>

<main class="main-content">
    <div class="page-header">
        <h1>Edit Company Profile</h1>
    </div>

    <?php if ($saved) : ?>
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <i class="fa-solid fa-circle-check"></i> Profile updated successfully.
        </div>
    <?php endif; ?>

    <form method="post" action="profile.php" class="card">
        <h2 style="margin-bottom:16px; font-size:18px;">Basic Information</h2>

        <div class="form-row">
            <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($company['company_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Industry Type</label>
                <input type="text" name="industry_type" class="form-control" value="<?php echo htmlspecialchars($company['industry_type'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($company['location'] ?? ''); ?>" required>
        </div>

        <h2 style="margin:20px 0 16px; font-size:18px;">Company Details</h2>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" placeholder="Tell us about your company, mission, and culture." style="min-height:140px;"><?php echo htmlspecialchars($company['description'] ?? ''); ?></textarea>
        </div>

        <div class="modal-actions" style="max-width:320px; margin-left:auto;">
            <button type="submit" name="save_profile" class="btn btn-primary btn-block">Save Changes</button>
        </div>
    </form>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>