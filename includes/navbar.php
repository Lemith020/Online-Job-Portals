<?php
// -----------------------------------------------------------------
// navbar.php
// Purpose : Main menu, changes based on logged-in role.
// Included right after header.php.
// -----------------------------------------------------------------
$role = $_SESSION['role'] ?? 'guest'; // guest | seeker | company | admin
?>
<nav class="navbar">
    <ul>
        <li><a href="/index.php">Home</a></li>

        <?php if ($role === 'guest'): ?>
            <li><a href="/auth/login.php">Login</a></li>
            <li><a href="/auth/register.php">Register</a></li>
        <?php elseif ($role === 'seeker'): ?>
            <li><a href="/seeker/jobs/index.php">Find Jobs</a></li>
            <li><a href="/seeker/applications/index.php">My Applications</a></li>
        <?php elseif ($role === 'company'): ?>
            <li><a href="/company/jobs/index.php">My Jobs</a></li>
            <li><a href="/company/applications/index.php">Applications</a></li>
        <?php elseif ($role === 'admin'): ?>
            <li><a href="/admin/dashboard.php">Dashboard</a></li>
        <?php endif; ?>

        <?php if ($role !== 'guest'): ?>
            <li><a href="/auth/logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>
