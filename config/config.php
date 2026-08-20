<?php
/**
 * Main Configuration
 * Kamadenu Goushala
 * 
 * Site-wide constants, paths, and settings loader.
 */

// Prevent direct access
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Environment
define('APP_ENV', 'development'); // 'development' or 'production'
define('APP_DEBUG', APP_ENV === 'development');

// Error reporting based on environment
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Error logging
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/logs/error.log');

// Site URL (auto-detect)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('SITE_URL', $protocol . '://' . $host . '/kamadhenu');
define('ADMIN_URL', SITE_URL . '/admin');

// Asset paths
define('ASSETS_URL', SITE_URL . '/assets');
define('UPLOADS_URL', SITE_URL . '/uploads');
define('UPLOADS_PATH', BASE_PATH . '/uploads');

// Upload limits
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// Pagination
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 15);

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// Include database connection
require_once BASE_PATH . '/config/database.php';

// Include core functions
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/csrf.php';
require_once BASE_PATH . '/includes/flash.php';
require_once BASE_PATH . '/includes/auth.php';

/**
 * Load all site settings from database into a global array
 * Cached per request for performance
 * 
 * @return array
 */
function getSiteSettings(): array {
    static $settings = null;
    
    if ($settings === null) {
        try {
            $db = getDB();
            $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
            $rows = $stmt->fetchAll();
            
            $settings = [];
            foreach ($rows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            error_log('Settings load error: ' . $e->getMessage());
            $settings = [];
        }
    }
    
    return $settings;
}

/**
 * Get a specific site setting
 * 
 * @param string $key
 * @param string $default
 * @return string
 */
function getSetting(string $key, string $default = ''): string {
    $settings = getSiteSettings();
    return $settings[$key] ?? $default;
}

// Create logs directory if it doesn't exist
if (!is_dir(BASE_PATH . '/logs')) {
    @mkdir(BASE_PATH . '/logs', 0755, true);
}
