<?php
/**
 * Admin Logout
 */

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/config.php';

adminLogout();
setFlash('success', 'You have been logged out successfully.');
redirect(ADMIN_URL . '/login.php');
