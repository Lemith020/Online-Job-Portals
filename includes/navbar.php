<?php
$role = $_SESSION['role'] ?? 'guest';
?>
<nav class="navbar">
    <div class="container navbar-inner">
        <a href="/index.php" class="logo">
            <span class="logo-circle">JP</span>
            JobPortal.lk
        </a>

        <ul class="nav-links">
            <li><a href="/index.php">Home</a></li>
            <li><a href="/seeker/jobs/index.php">Jobs</a></li>
            <li><a href="#">Categories</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Contact Us</a></li>
        </ul>

        <div class="auth-buttons">
            <?php if ($role === 'guest'): ?>
                <a href="/auth/login.php" class="btn btn-outline">Login</a>
                <a href="/auth/register.php" class="btn btn-primary">Register</a>
            <?php elseif ($role === 'seeker'): ?>
                <a href="/seeker/applications/index.php">My Applications</a>
                <a href="/auth/logout.php" class="btn btn-outline">Logout</a>
            <?php elseif ($role === 'company'): ?>
                <a href="/company/jobs/index.php">My Jobs</a>
                <a href="/auth/logout.php" class="btn btn-outline">Logout</a>
            <?php elseif ($role === 'admin'): ?>
                <a href="/admin/dashboard.php">Dashboard</a>
                <a href="/auth/logout.php" class="btn btn-outline">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>