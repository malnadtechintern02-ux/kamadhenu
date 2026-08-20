<?php
/**
 * Admin Auth Guard
 * Include at top of every admin page (except login)
 */

define('BASE_PATH', dirname(__DIR__, 2));
define('IS_ADMIN', true);
require_once BASE_PATH . '/config/config.php';
requireAdmin();
