<?php
// seeker/apply-job.php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$seeker_id = get_seeker_id($conn, $_SESSION['user_id']);
$job_id = (int)($_GET['job_id'] ?? $_POST['job_id'] ?? 0);

$job = get_job_by_id($conn, $job_id);
if (!$job) { 
    die("Job not found."); 
}


$cvs = get_seeker_cvs($conn, $seeker_id);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    if (isset($_POST['apply_with_new_cv']) && isset($_FILES['cv_file'])) {
        $cover_letter = trim($_POST['cover_letter'] ?? '');
        
        // Upload Directory Configuration
        $upload_dir = '../uploads/cvs/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = time() . '_' . basename($_FILES['cv_file']['name']);
        $target_path = $upload_dir . $file_name;
        $file_ext = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));

        $allowed_exts = ['pdf', 'doc', 'docx'];

        if (in_array($file_ext, $allowed_exts)) {
            if (move_uploaded_file($_FILES['cv_file']['tmp_name'], $target_path)) {
        
     
        $new_cv_id = insert_cv($conn, $seeker_id, $target_path);

        if ($new_cv_id) {
         
            if (insert_application($conn, $seeker_id, $job_id, $new_cv_id, $cover_letter)) {
                redirect("applications.php?applied=1");
            } else {
                $error = "Failed to save job application.";
            }
        } else {
            $error = "Database Error while saving CV.";
        }

    } else {
        $error = "Failed to upload file to target directory.";
    }
        } else {
            $error = "Invalid file format. Only PDF, DOC, and DOCX are allowed.";
        }


    } else {
    
        $cv_id = (int)($_POST['cv_id'] ?? 0);
        $experience = trim($_POST['experience'] ?? $_POST['cover_letter'] ?? '');

        if (!$cv_id) {
            $error = "Please choose a CV.";
        } else {
            insert_application($conn, $seeker_id, $job_id, $cv_id, $experience);
            redirect("applications.php?applied=1");
        }
    }
}

$page_title = "Apply for Job";
$page_css = "seeker_page_css/browse-jobs.css";
require_once '../includes/header.php';
require_once '../includes/seeker-sidebar.php';
?>

<!-- Centered Container to wrap the content dynamically -->
<div class="apply-container" style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; padding: 20px 10px;">

    <div style="text-align: center; margin-bottom: 20px; width: 100%; max-width: 650px;">
        <h1 class="page-title" style="margin-bottom: 5px;">Apply for <?= clean($job['title']) ?></h1>
        <p class="job-company" style="font-size: 1.1rem; color: #666;">at <strong><?= clean($job['company_name'] ?? 'Company') ?></strong></p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger" style="width: 100%; max-width: 650px; margin-bottom: 20px; padding: 12px 15px; border-radius: 6px; background-color: #f8d7da; color: #721c24;">
            <?= clean($error) ?>
        </div>
    <?php endif; ?>

    <div class="card" style="width: 100%; max-width: 650px; background: #ffffff; border-radius: 10px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: all 0.3s ease;">
        <h2 style="margin-bottom: 8px; font-size: 1.5rem; color: #333;"><?= clean($job['title']) ?></h2>
        <p style="color: #6c757d; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
            📍 <?= clean($job['location']) ?> &nbsp;•&nbsp; 💰 Rs. <?= number_format($job['salary_min'] ?? 0) ?> - <?= number_format($job['salary_max'] ?? 0) ?>
        </p>

        <?php if (empty($cvs)): ?>
          
            <div class="alert alert-warning" style="margin-bottom: 20px; padding: 12px; border-radius: 6px; background-color: #fff3cd; color: #856404;">
                You don't have any uploaded CVs yet. Upload one below to submit your application.
            </div>

            <form action="apply-job.php?job_id=<?= $job_id ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #444;">Upload CV (PDF, DOCX)</label>
                    <input type="file" name="cv_file" class="form-control" required accept=".pdf,.doc,.docx" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #444;">Cover Letter / Note (Optional)</label>
                    <textarea name="cover_letter" class="form-control" rows="4" placeholder="Why are you a good fit for this role?" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; resize: vertical;"></textarea>
                </div>

                <button type="submit" name="apply_with_new_cv" class="btn btn-primary btn-block" style="width: 100%; padding: 12px; font-weight: 600; border-radius: 6px;">Upload & Submit Application</button>
            </form>

        <?php else: ?>
          
            <form action="apply-job.php?job_id=<?= $job_id ?>" method="POST">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #444;">Select CV</label>
                    <select name="cv_id" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                        <?php foreach ($cvs as $cv): ?>
                            <option value="<?= $cv['cv_id'] ?>"><?= clean($cv['title'] ?? 'My CV') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom: 8px; font-weight: 600; color: #444;">Cover Letter / Experience Note (Optional)</label>
                    <textarea name="experience" class="form-control" rows="4" placeholder="Briefly describe your relevant experience..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; resize: vertical;"></textarea>
                </div>

                <button type="submit" name="submit_application" class="btn btn-primary btn-block" style="width: 100%; padding: 12px; font-weight: 600; border-radius: 6px;">Submit Application</button>
            </form>
        <?php endif; ?>
    </div>

</div>

<?php require_once '../includes/footer.php'; ?>