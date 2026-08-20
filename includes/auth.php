<?php
/**
 * Authentication Helpers
 * Kamadenu Goushala
 */

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn(): bool {
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_logged_in']);
}

/**
 * Get current admin info
 */
function getCurrentAdmin(): ?array {
    if (!isAdminLoggedIn()) return null;
    
    return dbFetchOne(
        "SELECT id, username, email, full_name, avatar FROM admins WHERE id = ? AND is_active = 1",
        [$_SESSION['admin_id']]
    );
}

/**
 * Attempt admin login
 */
function attemptLogin(string $username, string $password): bool {
    $admin = dbFetchOne(
        "SELECT id, username, email, full_name, password_hash FROM admins WHERE (username = ? OR email = ?) AND is_active = 1",
        [$username, $username]
    );
    
    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        return false;
    }
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    // Set session
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_name'] = $admin['full_name'];
    
    // Update last login
    dbUpdate('admins', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$admin['id']]);
    
    return true;
}

/**
 * Logout admin
 */
function adminLogout(): void {
    $_SESSION = [];
    
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    
    session_destroy();
}

/**
 * Require admin authentication (guard)
 */
function requireAdmin(): void {
    if (!isAdminLoggedIn()) {
        setFlash('warning', 'Please log in to access the admin panel.');
        redirect(ADMIN_URL . '/login.php');
    }
}
