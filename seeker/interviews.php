<?php
// seeker/interviews.php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$seeker_id = get_seeker_id($conn, $_SESSION['user_id']);

$per_page = 6;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

$total = get_interviews_count($conn, $seeker_id);
$total_pages = max(1, ceil($total / $per_page));
$interviews = get_interviews($conn, $seeker_id, $per_page, $offset);

$page_title = "My Interviews";
$page_css = "../assets/css/seeker_page_css/interviews.css";
require_once '../includes/seeker-header.php';
require_once '../includes/seeker-sidebar.php';
?>

<h1 class="page-title">My Interviews</h1>

<div class="card">
    <table>
        <thead>
            <tr><th>Job Title</th><th>Company</th><th>Status</th><th>Meeting Link</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php if ($interviews): ?>
            <?php foreach ($interviews as $iv): ?>
                <tr>
                    <td>
                        <?= formatDate($iv['interview_date']) ?> <?= clean($iv['title']) ?><br>
                        <small class="job-company">With <?= clean($iv['interviewer_name']) ?> at <?= date('h:i A', strtotime($iv['start_time'])) ?></small>
                        <?php if ($iv['status'] === 'Completed' && $iv['notes']): ?>
                            <br><small class="interview-notes">Notes: <?= clean($iv['notes']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= clean($iv['company_name']) ?></td>
                    <td><span class="badge <?= status_badge_class($iv['status']) ?>"><?= clean($iv['status']) ?></span></td>
                    <td>
                        <?php if ($iv['meeting_link']): ?>
                            <a href="<?= clean($iv['meeting_link']) ?>" target="_blank">View Link ↗</a>
                        <?php else: ?> - <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($iv['status'] === 'Scheduled' && $iv['meeting_link']): ?>
                            <a href="<?= clean($iv['meeting_link']) ?>" target="_blank" class="btn btn-primary">Join Meeting</a>
                        <?php else: ?>
                            <button class="btn btn-outline" disabled>Join Meeting</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">No interviews scheduled yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="pagination">
    <?php if ($page > 1): ?><a href="?page=<?= $page - 1 ?>">Previous</a><?php endif; ?>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?><a href="?page=<?= $page + 1 ?>">Next</a><?php endif; ?>
</div>

<?php require_once '../includes/seeker-footer.php'; ?>
