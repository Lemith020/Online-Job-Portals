<?php
/**
 * JobPortal.lk - Admin Authentication Check
 * Enforces admin-only access for all /admin/ pages
 */

require_once __DIR__ . '/auth.php';

// Enforce admin role
require_role('admin');
