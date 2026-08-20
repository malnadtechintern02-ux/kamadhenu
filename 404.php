<?php
/**
 * 404 - Page Not Found
 * Kamadenu Goushala
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
    require_once BASE_PATH . '/config/config.php';
}

$seo = [
    'title' => '404 - Page Not Found',
    'description' => 'The page you are looking for does not exist or has been moved.',
];

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="section py-5 text-center">
    <div class="container py-5" style="max-width: 600px;">
        <div class="stat-icon mx-auto mb-4" style="width: 90px; height: 90px; font-size: 2.5rem; background: var(--clr-primary-light); color: var(--clr-primary);">
            <i class="bi bi-compass"></i>
        </div>
        <h1 class="display-4 fw-bold text-brown">404</h1>
        <h2 class="h4 mb-3">Page Not Found</h2>
        <p class="text-muted mb-4">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= SITE_URL ?>/" class="btn btn-primary-custom">
                <i class="bi bi-house me-1"></i> Return Home
            </a>
            <a href="<?= SITE_URL ?>/cows.php" class="btn btn-outline-custom">
                <i class="bi bi-heart me-1"></i> View Our Cows
            </a>
        </div>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
