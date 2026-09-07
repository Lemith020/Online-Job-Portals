<?php
// seeker/apply-job.php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$seeker_id = get_seeker_id($conn, $_SESSION['user_id']);
$job_id = (int)($_GET['job_id'] ?? $_POST['job_id'] ?? 0);

$job = get_job_by_id($conn, $job_id);
if (!$job) { die("Job not found."); }

$cvs = get_seeker_cvs($conn, $seeker_id);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cv_id = (int)$_POST['cv_id'];
    $experience = trim($_POST['experience']);

    if (!$cv_id) {
        $error = "Please choose a CV.";
    } else {
        insert_application($conn, $seeker_id, $job_id, $cv_id, $experience);
        redirect("applications.php?applied=1");
    }
}

$page_title = "Apply for Job";
$page_css = "seeker-browse-jobs.css";
require_once '../includes/header.php';
require_once '../includes/seeker-sidebar.php';
?>

<h1 class="page-title">Apply for <?= clean($job['title']) ?></h1>
<p class="job-company">at <?= clean($job['company_name']) ?></p>

<div class="card" style="max-width:600px;">
    <?php if ($error): ?><p style="color:var(--red)"><?= clean($error) ?></p><?php endif; ?>
    <?php if (!$cvs): ?>
        <p>You need to upload a CV before applying. <a href="my-cv.php">Upload one here</a>.</p>
    <?php else: ?>
    <form method="POST">
        <input type="hidden" name="job_id" value="<?= $job_id ?>">
        <div class="form-group">
            <label>Select CV</label>
            <select name="cv_id" required>
                <?php foreach ($cvs as $cv): ?>
                    <option value="<?= $cv['cv_id'] ?>"><?= clean(basename($cv['file_path'])) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Experience / Cover Letter</label>
            <textarea name="experience" rows="5" placeholder="Tell the employer why you're a good fit..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Application</button>
    </form>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
