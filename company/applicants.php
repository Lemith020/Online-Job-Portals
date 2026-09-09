<?php
            /**
             * JobPortal.lk - Company Applicants Management
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

            // Company ID Retrieval
            $user_id = $_SESSION['user_id'];
            $company_id = $_SESSION['company_id'] ?? 0;

            if ($company_id == 0 && isset($conn) && $conn) {
                $c_q = mysqli_query($conn, "SELECT company_id FROM company WHERE user_id = $user_id");
                if ($c_q && $c_row = mysqli_fetch_assoc($c_q)) {
                    $company_id = $c_row['company_id'];
                    $_SESSION['company_id'] = $company_id;
                }
            }

            // -------------------------------------------------------------------
            // 1. UPDATE APPLICATION STATUS (HTML HEADERS )
            // -------------------------------------------------------------------
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
                $app_id = (int) $_POST['app_id'];
                $new_status = mysqli_real_escape_string($conn, $_POST['status']);

                $check_sql = "SELECT a.app_id FROM applications a
                            JOIN jobs j ON a.job_id = j.job_id
                            WHERE a.app_id = $app_id AND j.company_id = $company_id";
                if ($conn && mysqli_num_rows(mysqli_query($conn, $check_sql)) > 0) {
                    mysqli_query($conn, "UPDATE applications SET status = '$new_status' WHERE app_id = $app_id");
                }
                
                // Header redirect 
                $qs = (isset($_POST['redirect_qs']) && !empty($_POST['redirect_qs'])) ? '?' . $_POST['redirect_qs'] : '';
                header("Location: applicants.php" . $qs);
                exit;
            }

            // -------------------------------------------------------------------
            // 2. HTML HEADERS & SIDEBAR INCLUDES (Redirect logic )
            // -------------------------------------------------------------------
            $page_title = "Applicants";
            $active_page = "applicants";

            require_once __DIR__ . '/../includes/header.php';
            require_once __DIR__ . '/../includes/company-sidebar.php';

            // ---- Filters ----
            $job_filter = isset($_GET['job_id']) ? (int) $_GET['job_id'] : 0;
            $status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
            $search = isset($_GET['q']) ? trim($_GET['q']) : '';
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

            $where = "WHERE j.company_id = $company_id";
            if ($job_filter > 0) $where .= " AND j.job_id = $job_filter";
            if ($status_filter != 'all') {
                $status_safe = mysqli_real_escape_string($conn, $status_filter);
                $where .= " AND a.status = '$status_safe'";
            }
            if ($search != '') {
                $search_safe = mysqli_real_escape_string($conn, $search);
                $where .= " AND (u.first_name LIKE '%$search_safe%' OR u.last_name LIKE '%$search_safe%')";
            }

            $order = "ORDER BY a.apply_date DESC";
            if ($sort == 'oldest') $order = "ORDER BY a.apply_date ASC";

            $applicants_sql = "SELECT a.*, u.first_name, u.last_name, s.phone, s.bio, j.title AS job_title, cv.file_path
                                FROM applications a
                                JOIN job_seekers s ON a.seeker_id = s.seeker_id
                                JOIN users u ON s.user_id = u.user_id
                                JOIN jobs j ON a.job_id = j.job_id
                                LEFT JOIN cvs cv ON a.cv_id = cv.cv_id
                                $where $order";
            $applicants_result = $conn ? mysqli_query($conn, $applicants_sql) : false;

            $jobs_result = $conn ? mysqli_query($conn, "SELECT job_id, title FROM jobs WHERE company_id = $company_id ORDER BY title") : false;
?>

<main class="main-content">
    <div class="page-header">
        <h1>Applicants</h1>
    </div>

    <!-- Filters Bar -->
    <div class="filters-bar card mb-4">
        <form method="get" action="applicants.php" style="display:flex; gap:12px; width:100%; flex-wrap:wrap;">
            <div class="search-input" style="flex:1; min-width:220px;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" class="form-control" placeholder="Search seeker name..." value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <select name="job_id" class="form-control" style="width:auto;" onchange="this.form.submit()">
                <option value="0">All Jobs</option>
                <?php if ($jobs_result) : ?>
                    <?php while ($j = mysqli_fetch_assoc($jobs_result)) : ?>
                    <option value="<?php echo $j['job_id']; ?>" <?php echo $job_filter == $j['job_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($j['title']); ?>
                    </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>

            <select name="status" class="form-control" style="width:auto;" onchange="this.form.submit()">
                <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Applicants</option>
                <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="reviewed" <?php echo $status_filter == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                <option value="accepted" <?php echo $status_filter == 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            </select>

            <select name="sort" class="form-control" style="width:auto;" onchange="this.form.submit()">
                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                <option value="oldest" <?php echo $sort == 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
            </select>
        </form>
    </div>

    <!-- Applicants List -->
    <?php if ($applicants_result && mysqli_num_rows($applicants_result) > 0) : ?>
        <?php while ($app = mysqli_fetch_assoc($applicants_result)) : ?>
        <div class="list-item">
            <div>
                <div class="list-item-title"><?php echo htmlspecialchars(($app['first_name'] ?? '') . ' ' . ($app['last_name'] ?? '')); ?></div>
                <div class="list-item-meta">
                    <span><i class="fa-solid fa-briefcase"></i> <?php echo htmlspecialchars($app['job_title'] ?? ''); ?></span>
                    <span><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($app['phone'] ?? 'N/A'); ?></span>
                    <span><i class="fa-solid fa-calendar"></i> Applied <?php echo date('d/m/Y', strtotime($app['apply_date'] ?? 'now')); ?></span>
                </div>
                <?php if (!empty($app['experience'])) : ?>
                <p style="font-size:13px; color:var(--muted); margin-top:8px; max-width:520px;">
                    <?php echo htmlspecialchars($app['experience']); ?>
                </p>
                <?php endif; ?>
            </div>

            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                            <span class="badge badge-<?php echo strtolower($app['status'] ?? 'pending'); ?>"><?php echo ucfirst($app['status'] ?? 'pending'); ?></span>

                            <?php if (!empty($app['file_path'])) : ?>
                                <?php 
                                    
                                    $clean_path = ltrim(str_replace('../', '', $app['file_path']), '/');
                                    
                                
                                    $path_parts = explode('/', $clean_path);
                                    $encoded_parts = array_map('rawurlencode', $path_parts);
                                    $encoded_path = implode('/', $encoded_parts);
                                    
                                    
                                    $download_url = BASE_URL . '/' . $encoded_path;
                                ?>
                            <a href="<?php echo $download_url; ?>" 
                            
                            download="<?php echo htmlspecialchars(basename($app['file_path'])); ?>" 
                            class="btn btn-outline btn-sm">
                                <i class="fa-solid fa-download"></i> Download CV
                            </a>
                        <?php endif; ?>
                            <form method="post" class="status-form" style="margin:0;">
                                <input type="hidden" name="app_id" value="<?php echo $app['app_id']; ?>">
                                <input type="hidden" name="redirect_qs" value="<?php echo htmlspecialchars($_SERVER['QUERY_STRING']); ?>">
                                <select name="status" class="form-control btn-sm" onchange="this.form.submit()">
                                    <option value="pending" <?php echo ($app['status'] ?? '') == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="reviewed" <?php echo ($app['status'] ?? '') == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                    <option value="accepted" <?php echo ($app['status'] ?? '') == 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                                    <option value="rejected" <?php echo ($app['status'] ?? '') == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>

                            <?php if (($app['status'] ?? '') == 'reviewed' || ($app['status'] ?? '') == 'accepted') : ?>
                            <a href="interviews.php?app_id=<?php echo $app['app_id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-calendar-plus"></i> Schedule Interview
                            </a>
                            <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else : ?>
        <div class="card empty-state">No applicants found.</div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>