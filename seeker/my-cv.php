<?php
// seeker/my-cv.php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$user_id = $_SESSION['user_id'];
$seeker_id = get_seeker_id($conn, $user_id);
$error = '';

// ---- applications.php එකෙන් app_id එකක් ඇවිත් තිබේදැයි බලයි ----
$app_id = (int)($_GET['app_id'] ?? 0);
$app_detail = null;

if ($app_id > 0 && function_exists('get_application_cv_details')) {
    $app_detail = get_application_cv_details($conn, $app_id, $seeker_id);
}

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
                insert_cv($conn, $seeker_id, "../uploads/cvs/" . $filename);
                redirect("my-cv.php");
            } else {
                $error = "Upload failed. Please try again.";
            }
        }
    }
}

// ---- set as default ----
if (isset($_GET['set_default'])) {
    set_default_cv($conn, (int)$_GET['set_default'], $seeker_id);
    redirect("my-cv.php");
}

// ---- delete a CV ----
if (isset($_GET['delete_cv'])) {
    $cv_id = (int)$_GET['delete_cv'];
    $row = get_cv_by_id($conn, $cv_id, $seeker_id);
    if ($row) {
        @unlink($row['file_path']);
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
    <!-- Subscription Card -->
    <div class="card">
        <h2 class="section-title">Current Subscription</h2>
        <?php if ($subscription['plan_name']): ?>
            <div class="plan-name"><?= clean($subscription['plan_name']) ?>
                <span class="badge <?= $subscription['status'] === 'Active' ? 'badge-accepted' : 'badge-rejected' ?>"><?= $subscription['status'] ?></span>
            </div>
            <p class="plan-dates">Start Date: <?= isset($subscription['start_date']) ? formatDate($subscription['start_date']) : 'N/A' ?></p>
            <p class="plan-dates">End Date: <?= isset($subscription['end_date']) ? $subscription['end_date'] : 'N/A' ?></p>
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

    <!-- CV Management Card -->
    <div class="card">
        <h2 class="section-title">CV Management</h2>
        
        <form method="POST" enctype="multipart/form-data" class="upload-row">
            <input type="file" name="cv_file" accept="application/pdf" required>
            <button type="submit" name="upload_cv" class="btn btn-primary">Upload CV</button>
        </form>

        <?php if ($app_detail): ?>
            <div style="background: #f0f7ff; border: 1px solid #cce5ff; border-radius: 8px; padding: 15px; margin: 20px 0;">
                <h4 style="margin: 0 0 10px 0; color: #004085; font-size: 1rem;">
                    📌 Application Details
                </h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; font-size: 0.9rem;">
                    <div>
                        <span style="color: #666; display: block; font-size: 0.8rem;">Job:</span>
                        <strong><?= clean($app_detail['job_title'] ?? 'N/A') ?></strong>
                    </div>
                    <div>
                        <span style="color: #666; display: block; font-size: 0.8rem;">Company:</span>
                        <strong><?= clean($app_detail['company_name'] ?? 'N/A') ?></strong>
                    </div>
                    <div>
                        <span style="color: #666; display: block; font-size: 0.8rem;">Applied Date:</span>
                        <strong><?= formatDate($app_detail['apply_date']) ?></strong>
                    </div>
                    <div>
                        <span style="color: #666; display: block; font-size: 0.8rem;">Status:</span>
                        <span class="badge <?= status_badge_class($app_detail['status']) ?>">
                            <?= clean(ucfirst($app_detail['status'])) ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($app_detail['file_path'])): ?>
                    <div style="margin-top: 12px; padding-top: 10px; border-top: 1px dashed #b8daff; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                        <span style="font-size: 0.85rem; color: #333;">
                            <strong>Submitted CV:</strong> <?= clean(basename($app_detail['file_path'])) ?>
                        </span>
                        <!-- Direct Download Button -->
                        <a href="<?= clean($app_detail['file_path']) ?>" download="<?= clean(basename($app_detail['file_path'])) ?>" class="btn btn-primary" style="padding: 5px 12px; font-size: 0.85rem; text-decoration: none;">
                            📥 Download CV
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- CV List -->
        <div class="cv-list" style="margin-top: 15px;">
        <?php if (!empty($cvs)): ?>
            <?php foreach ($cvs as $i => $cv): ?>
                <?php 
                    $is_app_cv = ($app_detail && isset($app_detail['cv_id']) && $app_detail['cv_id'] == $cv['cv_id']);
                ?>
                <div class="cv-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #eee; <?= $is_app_cv ? 'background: #f8fbff; border-left: 4px solid #007bff;' : '' ?>">
                    <div>
                        <div class="cv-name" style="font-weight: 600;">
                            📄 <?= clean(basename($cv['file_path'])) ?>
                            <?php if ($is_app_cv): ?>
                                <span class="badge badge-accepted" style="font-size: 0.75rem; margin-left: 5px;">Submitted</span>
                            <?php endif; ?>
                        </div>
                        <div class="cv-date" style="font-size: 0.8rem; color: #888;">Uploaded <?= formatDate($cv['uploaded_at']) ?></div>
                    </div>
                    <div class="cv-actions" style="display: flex; gap: 8px; align-items: center;">
                        <a href="<?= clean($cv['file_path']) ?>" download="<?= clean(basename($cv['file_path'])) ?>" class="btn btn-outline" style="padding: 4px 8px; font-size: 0.8rem;">Download</a>
                        <?php if ($i === 0): ?>
                            <span class="badge badge-accepted">Active</span>
                        <?php else: ?>
                            <a href="my-cv.php?set_default=<?= $cv['cv_id'] ?>" class="btn btn-outline" style="padding: 4px 8px; font-size: 0.8rem;">Set default</a>
                        <?php endif; ?>
                        <a href="my-cv.php?delete_cv=<?= $cv['cv_id'] ?>" class="btn btn-danger" style="padding: 4px 8px; font-size: 0.8rem;" onclick="return confirm('Delete this CV?')">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: #777; margin-top: 15px;">No CVs uploaded yet.</p>
        <?php endif; ?>
        </div>

        <p class="cv-note" style="margin-top: 20px; font-size: 0.85rem; color: #777;">Your CV is active only while your subscription is active. If your subscription is inactive, you cannot apply for jobs.</p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>