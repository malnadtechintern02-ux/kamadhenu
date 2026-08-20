<?php
/**
 * 403 - Forbidden Access
 * Kamadenu Goushala
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
    require_once BASE_PATH . '/config/config.php';
}

$seo = [
    'title' => '403 - Access Forbidden',
    'description' => 'You do not have permission to access this area.',
];

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="section py-5 text-center">
    <div class="container py-5" style="max-width: 600px;">
        <div class="stat-icon mx-auto mb-4" style="width: 90px; height: 90px; font-size: 2.5rem; background: #ffebee; color: #c62828;">
            <i class="bi bi-shield-x"></i>
        </div>
        <h1 class="display-4 fw-bold text-danger">403</h1>
        <h2 class="h4 mb-3">Access Denied</h2>
        <p class="text-muted mb-4">You do not have administrative privileges to view this section.</p>
        <a href="<?= SITE_URL ?>/" class="btn btn-primary-custom">
            <i class="bi bi-house me-1"></i> Return to Safety
        </a>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
