<?php
/**
 * Admin Sidebar
 * Kamadenu Goushala
 */

$currentAdminPage = basename($_SERVER['PHP_SELF'], '.php');
$unreadMessages = dbCount('contact_messages', 'is_read = 0');

function isAdminActive(string $page): string {
    global $currentAdminPage;
    $pages = explode(',', $page);
    return in_array($currentAdminPage, $pages) ? 'active' : '';
}
?>

<!-- Top Navbar -->
<nav class="admin-topbar">
    <div class="d-flex align-items-center">
        <button class="btn btn-sm me-3 d-lg-none" id="sidebarToggle"><i class="bi bi-list fs-5"></i></button>
        <a href="<?= ADMIN_URL ?>/index.php" class="admin-brand">
            <span>Kamadenu Admin</span>
        </a>
    </div>
    <div class="d-flex align-items-center gap-3">
        <a href="<?= SITE_URL ?>/" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye me-1"></i><span class="d-none d-md-inline">View Site</span></a>
        <div class="dropdown">
            <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown">
                <div class="admin-avatar"><?= strtoupper(mb_substr($adminName, 0, 1)) ?></div>
                <span class="d-none d-md-inline ms-2 small fw-medium"><?= e($adminName) ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= ADMIN_URL ?>/profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li><a class="dropdown-item" href="<?= ADMIN_URL ?>/settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?= ADMIN_URL ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand-block">
        <img src="<?= ASSETS_URL ?>/images/logo-icon.png" alt="Kamadenu Goushala" class="sidebar-logo">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Kamadenu</span>
            <span class="sidebar-brand-sub">Goushala</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= isAdminActive('index') ?>" href="<?= ADMIN_URL ?>/index.php">
                    <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
                </a>
            </li>
            
            <li class="sidebar-heading">Content</li>
            
            <li class="nav-item">
                <a class="nav-link <?= isAdminActive('cows,cow-form') ?>" href="<?= ADMIN_URL ?>/cows.php">
                    <i class="bi bi-heart-fill"></i><span>Cows</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isAdminActive('breeds,breed-form') ?>" href="<?= ADMIN_URL ?>/breeds.php">
                    <i class="bi bi-grid-fill"></i><span>Breeds</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isAdminActive('seva,seva-form') ?>" href="<?= ADMIN_URL ?>/seva.php">
                    <i class="bi bi-flower1"></i><span>Seva</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isAdminActive('events,event-form') ?>" href="<?= ADMIN_URL ?>/events.php">
                    <i class="bi bi-calendar-event"></i><span>Events</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isAdminActive('news,news-form') ?>" href="<?= ADMIN_URL ?>/news.php">
                    <i class="bi bi-newspaper"></i><span>News</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isAdminActive('gallery,gallery-upload') ?>" href="<?= ADMIN_URL ?>/gallery.php">
                    <i class="bi bi-images"></i><span>Gallery</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isAdminActive('products,product-form') ?>" href="<?= ADMIN_URL ?>/products.php">
                    <i class="bi bi-box-seam"></i><span>Products</span>
                </a>
            </li>
            
            <li class="sidebar-heading">Transactions</li>
            
            <li class="nav-item">
                <a class="nav-link <?= isAdminActive('donations,donation-details') ?>" href="<?= ADMIN_URL ?>/donations.php">
                    <i class="bi bi-cash-coin"></i><span>Donations</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isAdminActive('adoptions,adoption-details') ?>" href="<?= ADMIN_URL ?>/adoptions.php">
                    <i class="bi bi-house-heart"></i><span>Adoptions</span>
                </a>
            </li>
            
            <li class="sidebar-heading">Engagement</li>
            
            <li class="nav-item">
                <a class="nav-link <?= isAdminActive('testimonials,testimonial-form') ?>" href="<?= ADMIN_URL ?>/testimonials.php">
                    <i class="bi bi-chat-quote"></i><span>Testimonials</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isAdminActive('messages,message-details') ?>" href="<?= ADMIN_URL ?>/messages.php">
                    <i class="bi bi-envelope"></i><span>Messages</span>
                    <?php if ($unreadMessages > 0): ?>
                    <span class="badge bg-danger rounded-pill ms-auto"><?= $unreadMessages ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <li class="sidebar-heading">System</li>
            
            <li class="nav-item">
                <a class="nav-link <?= isAdminActive('hero-settings') ?>" href="<?= ADMIN_URL ?>/hero-settings.php">
                    <i class="bi bi-image"></i><span>Hero Settings</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isAdminActive('settings') ?>" href="<?= ADMIN_URL ?>/settings.php">
                    <i class="bi bi-gear-fill"></i><span>Settings</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- Main Content Wrapper -->
<main class="admin-main">
    <div class="container-fluid p-4">
        <?= renderFlashMessages() ?>
