<?php
$page_title = "Company Profile";
$page_css = "profile.css";
$active_page = "profile";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$saved = false;

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
    mysqli_query($conn, $sql);

    // refresh $company so the page shows the updated values
    $company_result = mysqli_query($conn, "SELECT * FROM company WHERE company_id = $company_id");
    $company = mysqli_fetch_assoc($company_result);
    $saved = true;
}
?>

<div class="page-header">
    <h1>Edit Company Profile</h1>
</div>

<?php if ($saved) : ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Profile updated successfully.</div>
<?php endif; ?>

<form method="post" class="card">
    <h2 style="margin-bottom:16px;">Basic Information</h2>

    <div class="form-row">
        <div class="form-group">
            <label>Company Name</label>
            <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($company['company_name']); ?>" required>
        </div>
        <div class="form-group">
            <label>Industry Type</label>
            <input type="text" name="industry_type" class="form-control" value="<?php echo htmlspecialchars($company['industry_type']); ?>" required>
        </div>
    </div>

    <div class="form-group">
        <label>Location</label>
        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($company['location']); ?>" required>
    </div>

    <h2 style="margin:20px 0 16px;">Company Details</h2>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" placeholder="Tell us about your company, mission, and culture." style="min-height:140px;"><?php echo htmlspecialchars($company['description']); ?></textarea>
    </div>

    <div class="modal-actions" style="max-width:320px; margin-left:auto;">
        <button type="submit" name="save_profile" class="btn btn-primary btn-block">Save Changes</button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
