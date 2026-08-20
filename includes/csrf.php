<?php
/**
 * CSRF Token Management
 * Kamadenu Goushala
 */

/**
 * Generate a CSRF token and store in session
 */
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_time']) || 
        (time() - $_SESSION['csrf_token_time']) > 3600) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output a hidden CSRF token input field
 */
function csrfField(): string {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

/**
 * Validate CSRF token from request
 */
function validateCsrfToken(): bool {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Verify CSRF token and abort if invalid
 */
function requireCsrfToken(): void {
    if (!validateCsrfToken()) {
        http_response_code(403);
        if (defined('IS_AJAX') && IS_AJAX) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh the page and try again.']);
        } else {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
        }
        exit;
    }
}
