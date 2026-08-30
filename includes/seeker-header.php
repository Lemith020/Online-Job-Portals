<?php
// includes/header.php
// Include AFTER auth.php/functions.php, right where you want the HTML to start.
// Set $page_title and $page_css (filename in assets/css/) BEFORE including this file.

if (!isset($page_title)) $page_title = "JobPortal.lk";
if (!isset($page_css)) $page_css = "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title) ?> | JobPortal.lk</title>

<!-- shared base styles (sidebar, topbar, layout) -->
<link rel="stylesheet" href="/Online-Job-Portals/assets/css/seeker_page_css/base.css">
<?php if ($page_css): ?>
<link rel="stylesheet" href="/Online-Job-Portals/assets/css/<?= e($page_css) ?>">
<?php endif; ?>
</head>
<body>

<!-- Top Navigation Bar -->
<div class="topbar">
    <div class="topbar-brand">JobPortal.lk</div>
    <nav class="topbar-links">
        <a href="/Online-Job-Portals/index.php">Home</a>
        <a href="/Online-Job-Portals/seeker/browse-jobs.php">Jobs</a>
        <a href="#">Categories</a>
        <a href="#">About Us</a>
        <a href="#">Contact</a>
    </nav>
    <div class="topbar-user">
        <span>👤 <?= e($_SESSION['first_name'] ?? 'User') ?> ▾</span>
    </div>
</div>

<div class="app-layout">
