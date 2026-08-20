<?php
/**
 * 500 - Internal Server Error
 * Kamadenu Goushala
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
    require_once BASE_PATH . '/config/config.php';
}

$seo = [
    'title' => '500 - Server Error',
    'description' => 'A temporary server error has occurred.',
];

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="section py-5 text-center">
    <div class="container py-5" style="max-width: 600px;">
        <div class="stat-icon mx-auto mb-4" style="width: 90px; height: 90px; font-size: 2.5rem; background: #fff3e0; color: #e65100;">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <h1 class="display-4 fw-bold text-brown">500</h1>
        <h2 class="h4 mb-3">Server Error</h2>
        <p class="text-muted mb-4">We are experiencing a temporary technical issue. Please try refreshing the page or check back shortly.</p>
        <a href="<?= SITE_URL ?>/" class="btn btn-primary-custom">
            <i class="bi bi-house me-1"></i> Return Home
        </a>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
