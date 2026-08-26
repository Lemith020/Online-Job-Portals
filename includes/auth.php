<?php
session_start();


if (!isset($_SESSION['company_id'])) {
    $_SESSION['company_id'] = 1;
    $_SESSION['user_id']    = 1;
}

$company_id = $_SESSION['company_id'];
