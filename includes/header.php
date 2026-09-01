<?php
/**
 * JobPortal.lk - Standard Shared HTML Header
 * Member 1 - Admin & Core/Shared
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

$page_title = isset($page_title) ? $page_title . ' - ' . APP_NAME : APP_NAME . ' - Leading Job Portal in Sri Lanka';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="JobPortal.lk - Premium Job Portal & Administrative Control Center">
  <title><?php echo htmlspecialchars($page_title); ?></title>
  
  <!-- Modern Google Font: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Global Core Stylesheet -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
