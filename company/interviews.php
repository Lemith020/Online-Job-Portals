<?php
$page_title = "Interviews";
$page_css = "interviews.css";
$page_js = "interviews.js";
$active_page = "interviews";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// ---- Schedule new interview ----
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['schedule_interview'])) {
    $app_id = (int) $_POST['app_id'];
    $interviewer_id = (int) $_POST['interviewer_id'];
    $interview_date = mysqli_real_escape_string($conn, $_POST['interview_date']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $meeting_link = mysqli_real_escape_string($conn, $_POST['meeting_link']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    $sql = "INSERT INTO interviews (app_id, interviewer_id, interview_date, start_time, meeting_link, notes, status)
            VALUES ($app_id, $interviewer_id, '$interview_date', '$start_time', '$meeting_link', '$notes', 'Scheduled')";
    mysqli_query($conn, $sql);
    header("Location: interviews.php");
    exit;
}

// ---- Update interview status (Complete / Cancel) ----
if (isset($_GET['set_status']) && isset($_GET['interview_id'])) {
    $new_status = mysqli_real_escape_string($conn, $_GET['set_status']);
    $interview_id = (int) $_GET['interview_id'];
    mysqli_query($conn, "UPDATE interviews SET status = '$new_status' WHERE interview_id = $interview_id");
    header("Location: interviews.php");
    exit;
}

// ---- Add interviewer ----
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_interviewer'])) {
    $name = mysqli_real_escape_string($conn, $_POST['interviewer_name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact_number']);
    mysqli_query($conn, "INSERT INTO interviewer (company_id, interviewer_name, contact_number) VALUES ($company_id, '$name', '$contact')");
    header("Location: interviews.php");
    exit;
}

// ---- Delete interviewer ----
if (isset($_GET['delete_interviewer'])) {
    $id = (int) $_GET['delete_interviewer'];
    mysqli_query($conn, "DELETE FROM interviewer WHERE interviewer_id = $id AND company_id = $company_id");
    header("Location: interviews.php");
    exit;
}

// ---- Data for listing ----
$interviews_sql = "SELECT i.*, u.first_name, u.last_name, j.title AS job_title, iv.interviewer_name
                    FROM interviews i
                    JOIN applications a ON i.app_id = a.app_id
                    JOIN job_seekers s ON a.seeker_id = s.seeker_id
                    JOIN users u ON s.user_id = u.user_id
                    JOIN jobs j ON a.job_id = j.job_id
                    JOIN interviewer iv ON i.interviewer_id = iv.interviewer_id
                    WHERE j.company_id = $company_id
                    ORDER BY i.interview_date DESC, i.start_time DESC";
$interviews_result = mysqli_query($conn, $interviews_sql);

$interviewers_result = mysqli_query($conn, "SELECT * FROM interviewer WHERE company_id = $company_id ORDER BY interviewer_name");

// Applications eligible for interview scheduling (reviewed / accepted)
$preselect_app = isset($_GET['app_id']) ? (int) $_GET['app_id'] : 0;
$eligible_apps_sql = "SELECT a.app_id, u.first_name, u.last_name, j.title AS job_title
                       FROM applications a
                       JOIN job_seekers s ON a.seeker_id = s.seeker_id
                       JOIN users u ON s.user_id = u.user_id
                       JOIN jobs j ON a.job_id = j.job_id
                       WHERE j.company_id = $company_id AND a.status IN ('reviewed','accepted')
                       ORDER BY a.apply_date DESC";
$eligible_apps_result = mysqli_query($conn, $eligible_apps_sql);
?>

<div class="page-header">
    <h1>Scheduled Interviews</h1>
    <button class="btn btn-primary" onclick="openInterviewModal()">
        <i class="fa-solid fa-plus"></i> Schedule New Interview
    </button>
</div>

<div class="interviews-layout">
    <div class="interviews-list">
        <?php if (mysqli_num_rows($interviews_result) > 0) : ?>
            <?php while ($iv = mysqli_fetch_assoc($interviews_result)) : ?>
            <div class="list-item">
                <div>
                    <div class="list-item-title"><?php echo htmlspecialchars($iv['first_name'] . ' ' . $iv['last_name']); ?></div>
                    <div class="list-item-meta">
                        <span><i class="fa-solid fa-briefcase"></i> <?php echo htmlspecialchars($iv['job_title']); ?></span>
                        <span><i class="fa-solid fa-user-tie"></i> <?php echo htmlspecialchars($iv['interviewer_name']); ?></span>
                        <span><i class="fa-solid fa-calendar"></i> <?php echo date('d/m/Y', strtotime($iv['interview_date'])); ?> <?php echo date('h:i A', strtotime($iv['start_time'])); ?></span>
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <span class="badge badge-<?php echo strtolower($iv['status']); ?>"><?php echo $iv['status']; ?></span>

                    <?php if ($iv['status'] == 'Scheduled') : ?>
                    <a href="interviews.php?set_status=Completed&interview_id=<?php echo $iv['interview_id']; ?>" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-check"></i> Mark Completed
                    </a>
                    <a href="interviews.php?set_status=Cancelled&interview_id=<?php echo $iv['interview_id']; ?>" class="btn btn-danger-outline btn-sm" onclick="return confirm('Cancel this interview?');">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="empty-state">No interviews scheduled yet.</div>
        <?php endif; ?>
    </div>

    <div class="interviewers-panel card">
        <h2 style="margin-bottom:14px;">Manage Interviewers</h2>

        <?php if (mysqli_num_rows($interviewers_result) > 0) : ?>
            <?php mysqli_data_seek($interviewers_result, 0); ?>
            <?php while ($intv = mysqli_fetch_assoc($interviewers_result)) : ?>
            <div class="interviewer-row">
                <div>
                    <p class="interviewer-name"><?php echo htmlspecialchars($intv['interviewer_name']); ?></p>
                    <p class="interviewer-contact"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($intv['contact_number']); ?></p>
                </div>
                <a href="interviews.php?delete_interviewer=<?php echo $intv['interviewer_id']; ?>" onclick="return confirm('Delete this interviewer?');">
                    <i class="fa-solid fa-trash" style="color:var(--danger);"></i>
                </a>
            </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p style="color:var(--muted); font-size:13px; margin-bottom:12px;">No interviewers added yet.</p>
        <?php endif; ?>

        <hr style="border:none; border-top:1px solid var(--border); margin:16px 0;">

        <form method="post">
            <div class="form-group">
                <label>Interviewer Name</label>
                <input type="text" name="interviewer_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact_number" class="form-control" required>
            </div>
            <button type="submit" name="add_interviewer" class="btn btn-primary btn-block btn-sm">
                <i class="fa-solid fa-plus"></i> Add Interviewer
            </button>
        </form>
    </div>
</div>

<!-- Schedule Interview Modal -->
<div class="modal-overlay" id="interviewModal">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Schedule New Interview</h2>
            <button class="modal-close" onclick="closeInterviewModal()">&times;</button>
        </div>

        <form method="post">
            <div class="form-group">
                <label>Applicant</label>
                <select name="app_id" class="form-control" required>
                    <option value="">-- Select Applicant --</option>
                    <?php while ($ea = mysqli_fetch_assoc($eligible_apps_result)) : ?>
                    <option value="<?php echo $ea['app_id']; ?>" <?php echo $preselect_app == $ea['app_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($ea['first_name'] . ' ' . $ea['last_name'] . ' - ' . $ea['job_title']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Interviewer</label>
                <select name="interviewer_id" class="form-control" required>
                    <option value="">-- Select Interviewer --</option>
                    <?php mysqli_data_seek($interviewers_result, 0); ?>
                    <?php while ($intv = mysqli_fetch_assoc($interviewers_result)) : ?>
                    <option value="<?php echo $intv['interviewer_id']; ?>"><?php echo htmlspecialchars($intv['interviewer_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="interview_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Time</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label>Meeting Link</label>
                <input type="text" name="meeting_link" class="form-control" placeholder="https://meet.google.com/...">
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control"></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeInterviewModal()">Cancel</button>
                <button type="submit" name="schedule_interview" class="btn btn-primary btn-block">Schedule Interview</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
