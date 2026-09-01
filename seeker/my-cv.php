<?php
// seeker/my-cv.php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$user_id = $_SESSION['user_id'];
$seeker_id = get_seeker_id($conn, $user_id);
$error = '';

// ---- upload a new CV ----
if (isset($_POST['upload_cv'])) {
    if (!empty($_FILES['cv_file']['name'])) {
        $ext = strtolower(pathinfo($_FILES['cv_file']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $error = "Only PDF files are allowed.";
        } else {
            $upload_dir = "../uploads/cvs/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $filename = "seeker{$seeker_id}_" . time() . ".pdf";
            $target = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['cv_file']['tmp_name'], $target)) {
                insert_cv($conn, $seeker_id, "uploads/cvs/" . $filename);
            } else {
                $error = "Upload failed. Please try again.";
            }
        }
    }
}

// ---- set as default: bump uploaded_at so it becomes the newest / "Active" CV ----
if (isset($_GET['set_default'])) {
    set_default_cv($conn, (int)$_GET['set_default'], $seeker_id);
    redirect("my-cv.php");
}

// ---- delete a CV ----
if (isset($_GET['delete_cv'])) {
    $cv_id = (int)$_GET['delete_cv'];
    $row = get_cv_by_id($conn, $cv_id, $seeker_id);
    if ($row) {
        @unlink("../" . $row['file_path']);
        delete_cv($conn, $cv_id, $seeker_id);
    }
    redirect("my-cv.php");
}

// ---- subscribe to a plan ----
if (isset($_POST['subscribe_plan'])) {
    subscribe_to_plan($conn, $user_id, (int)$_POST['plan_id']);
    redirect("my-cv.php");
}

$subscription = get_subscription_status($conn, $user_id);
$plans = get_all_plans($conn);
$cvs = get_seeker_cvs($conn, $seeker_id);

$page_title = "My CV";
$page_css = "../assets/css/seeker_page_css/my-cv.css";
$page_js = "../assets/js/seeker_page_js/my-cv.js";
require_once '../includes/seeker-header.php';
require_once '../includes/seeker-sidebar.php';
?>

<h1 class="page-title">My CV & Subscription</h1>
<?php if ($error): ?><div class="alert-error"><?= clean($error) ?></div><?php endif; ?>

<div class="cv-layout">
    <div class="card">
        <h2 class="section-title">Current Subscription</h2>
        <?php if ($subscription['plan_name']): ?>
            <div class="plan-name"><?= clean($subscription['plan_name']) ?>
                <span class="badge <?= $subscription['status'] === 'Active' ? 'badge-accepted' : 'badge-rejected' ?>"><?= $subscription['status'] ?></span>
            </div>
            <p class="plan-dates">Start Date: <?= formatDate($subscription['start_date']) ?></p>
            <p class="plan-dates">End Date: <?= $subscription['end_date'] ?></p>
        <?php else: ?>
            <p>No subscription yet.</p>
        <?php endif; ?>

        <button type="button" class="btn btn-primary btn-full" onclick="togglePlans()">Change Plan</button>

        <div id="plan-list" class="plan-list">
            <?php foreach ($plans as $plan): ?>
                <form method="POST" class="plan-option">
                    <input type="hidden" name="plan_id" value="<?= $plan['plan_id'] ?>">
                    <div>
                        <strong><?= clean($plan['plan_name']) ?></strong>
                        <div class="plan-sub"><?= $plan['duration_days'] ?> days</div>
                    </div>
                    <div class="plan-price">Rs. <?= number_format($plan['price'], 2) ?></div>
                    <button type="submit" name="subscribe_plan" class="btn btn-outline">Choose</button>
                </form>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <h2 class="section-title">CV Management</h2>
        <form method="POST" enctype="multipart/form-data" class="upload-row">
            <input type="file" name="cv_file" accept="application/pdf" required>
            <button type="submit" name="upload_cv" class="btn btn-primary">Upload CV</button>
        </form>

        <div class="cv-list">
        <?php if ($cvs): ?>
            <?php foreach ($cvs as $i => $cv): ?>
                <div class="cv-item">
                    <div>
                        <div class="cv-name">📄 <?= clean(basename($cv['file_path'])) ?></div>
                        <div class="cv-date">Uploaded <?= formatDate($cv['uploaded_at']) ?></div>
                    </div>
                    <div class="cv-actions">
                        <?php if ($i === 0): ?>
                            <span class="badge badge-accepted">Active</span>
                        <?php else: ?>
                            <a href="my-cv.php?set_default=<?= $cv['cv_id'] ?>" class="btn btn-outline">Set as default</a>
                        <?php endif; ?>
                        <a href="my-cv.php?delete_cv=<?= $cv['cv_id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this CV?')">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No CVs uploaded yet.</p>
        <?php endif; ?>
        </div>
        <p class="cv-note">Your CV is active only while your subscription is active. If your subscription is inactive, you cannot apply for jobs.</p>
    </div>
</div>

<?php require_once '../includes/seeker-footer.php'; ?>
