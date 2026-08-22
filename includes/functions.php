<?php
// -----------------------------------------------------------------
// functions.php
// Purpose : Small reusable helper functions used across the site
// -----------------------------------------------------------------

function clean($data) {
    return htmlspecialchars(trim($data));
}

function formatDate($date) {
    return date("d M Y", strtotime($date));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

// TODO: add more helpers as the app grows
