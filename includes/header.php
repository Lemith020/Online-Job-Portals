<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth_check.php';

$company_sql = "SELECT * FROM company WHERE company_id = $company_id";
$company_result = mysqli_query($conn, $company_sql);
$company = mysqli_fetch_assoc($company_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? $page_title . " - JobPortal.lk" : "JobPortal.lk"; ?></title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<?php if (isset($page_css)) : ?>
<link rel="stylesheet" href="assets/css/<?php echo $page_css; ?>">
<?php endif; ?>
</head>
<body>

<nav class="topnav">
    <div class="topnav-left">
        <button class="hamburger-btn" id="hamburgerBtn">
            <i class="fa-solid fa-bars"></i>
        </button>
        <i class="fa-solid fa-briefcase"></i>
        <span>JobPortal.lk</span>
    </div>

    <div class="topnav-right">
        <div class="profile-menu" id="profileMenu">
            <button class="profile-btn" id="profileBtn">
                <i class="fa-solid fa-circle-user"></i>
                <span><?php echo htmlspecialchars($company['company_name']); ?></span>
                <i class="fa-solid fa-chevron-down profile-chevron"></i>
            </button>

            <div class="profile-dropdown" id="profileDropdown">
                <div class="profile-dropdown-header">
                    <i class="fa-solid fa-building"></i>
                    <div>
                        <p class="profile-name"><?php echo htmlspecialchars($company['company_name']); ?></p>
                        <p class="profile-sub"><?php echo htmlspecialchars($company['industry_type']); ?></p>
                    </div>
                </div>
                <div class="profile-dropdown-body">
                    <p><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($company['location']); ?></p>
                </div>
                <div class="profile-dropdown-footer">
                    <a href="profile.php"><i class="fa-solid fa-id-card"></i> View Profile</a>
                    <a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="layout">
