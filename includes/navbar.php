<?php
/**
 * Navigation Bar & Header Component
 * Kamadenu Goushala
 * 
 * Multi-bar responsive header with top info bar, glassmorphic main navbar,
 * dropdowns, social badges, and quick CTA buttons.
 */

$siteName = getSetting('site_name', 'Kamadenu Goushala');
$phone = getSetting('phone', '[PHONE NUMBER]');
$email = getSetting('email', '[EMAIL ADDRESS]');
$address = getSetting('address', 'Kavadi, Virajpet, Kodagu, Karnataka');
$whatsapp = getSetting('whatsapp', '');
$whatsappLink = getWhatsAppLink('🙏 Namaste Kamadenu Goushala, I would like to know more about Gau Seva.');
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
?>

<!-- Header Wrapper -->
<header class="site-header-wrapper" id="siteHeader">
    <!-- Top Announcement & Contact Bar -->
    <div class="top-header-bar d-none d-lg-block">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Left Contact Badges -->
                <div class="top-header-info d-flex align-items-center gap-3">
                    <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>" class="top-info-link">
                        <i class="bi bi-telephone-fill"></i> <?= e($phone) ?>
                    </a>
                    <span class="top-info-divider"></span>
                    <a href="mailto:<?= e($email) ?>" class="top-info-link">
                        <i class="bi bi-envelope-fill"></i> <?= e($email) ?>
                    </a>
                    <span class="top-info-divider"></span>
                    <span class="top-info-text">
                        <i class="bi bi-geo-alt-fill text-gold"></i> Kavadi, Virajpet, Kodagu, Karnataka
                    </span>
                </div>

                <!-- Right Quick Links & Social -->
                <div class="top-header-actions d-flex align-items-center gap-3">
                    <span class="tax-badge">
                        <i class="bi bi-patch-check-fill me-1"></i> 80G Tax Exempted
                    </span>
                    <span class="top-info-divider"></span>
                    <div class="top-social-links d-flex align-items-center gap-2">
                        <?php if ($fb = getSetting('facebook_url')): ?>
                        <a href="<?= e($fb) ?>" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <?php endif; ?>
                        <?php if ($ig = getSetting('instagram_url')): ?>
                        <a href="<?= e($ig) ?>" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <?php endif; ?>
                        <?php if ($yt = getSetting('youtube_url')): ?>
                        <a href="<?= e($yt) ?>" target="_blank" rel="noopener" aria-label="YouTube" title="YouTube"><i class="bi bi-youtube"></i></a>
                        <?php endif; ?>
                        <?php if ($whatsapp): ?>
                        <a href="<?= e($whatsappLink) ?>" target="_blank" rel="noopener" aria-label="WhatsApp" title="WhatsApp Chat"><i class="bi bi-whatsapp"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg sticky-top" id="mainNavbar">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand d-flex align-items-center" href="<?= SITE_URL ?>/" aria-label="<?= e($siteName) ?>">
                <img src="<?= ASSETS_URL ?>/images/logo.png" alt="<?= e($siteName) ?>" class="site-navbar-logo">
            </a>

            <!-- Mobile Navbar Toggler -->
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="toggler-icon-bar"></span>
                <span class="toggler-icon-bar"></span>
                <span class="toggler-icon-bar"></span>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse mobile-nav-scroll" id="navbarMain">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPath === '/kamadhenu/' || $currentPath === '/kamadhenu/index.php') ? 'active' : '' ?>" href="<?= SITE_URL ?>/">
                            <i class="bi bi-house-door d-lg-none me-2"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isCurrentPage('about') ? 'active' : '' ?>" href="<?= SITE_URL ?>/about.php">
                            <i class="bi bi-info-circle d-lg-none me-2"></i>About Us
                        </a>
                    </li>
                    
                    <!-- Our Cows Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= (isCurrentPage('cow') || isCurrentPage('breed')) ? 'active' : '' ?>" 
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Our Sanctuary
                        </a>
                        <ul class="dropdown-menu shadow-lg border-0 rounded-4">
                            <li>
                                <a class="dropdown-item py-2" href="<?= SITE_URL ?>/cows.php">
                                    <i class="bi bi-heart-fill text-danger me-2"></i>
                                    <div>
                                        <strong>Our Beloved Cows</strong>
                                        <small class="d-block text-muted">Browse cows protected at Goushala</small>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item py-2" href="<?= SITE_URL ?>/breeds.php">
                                    <i class="bi bi-grid-3x3-gap-fill text-success me-2"></i>
                                    <div>
                                        <strong>Indigenous Breeds</strong>
                                        <small class="d-block text-muted">Gir, Hallikar, Malenadu Gidda &amp; more</small>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Gau Seva Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= (isCurrentPage('seva') || isCurrentPage('donate') || isCurrentPage('feed') || isCurrentPage('adopt')) ? 'active' : '' ?>" 
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Gau Seva
                        </a>
                        <ul class="dropdown-menu shadow-lg border-0 rounded-4">
                            <li>
                                <a class="dropdown-item py-2" href="<?= SITE_URL ?>/gau-seva.php">
                                    <i class="bi bi-flower1 text-primary me-2"></i>
                                    <div>
                                        <strong>Seva Offerings</strong>
                                        <small class="d-block text-muted">Explore all Gau Seva opportunities</small>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item py-2" href="<?= SITE_URL ?>/feed-a-cow.php">
                                    <i class="bi bi-basket-fill text-warning me-2"></i>
                                    <div>
                                        <strong>Feed a Cow (Gau Grass)</strong>
                                        <small class="d-block text-muted">Sponsor daily meals &amp; fodder</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="<?= SITE_URL ?>/adopt-a-cow.php">
                                    <i class="bi bi-house-heart-fill text-danger me-2"></i>
                                    <div>
                                        <strong>Adopt a Cow</strong>
                                        <small class="d-block text-muted">Become a monthly Gau Guardian</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="<?= SITE_URL ?>/donate.php">
                                    <i class="bi bi-gift-fill text-success me-2"></i>
                                    <div>
                                        <strong>General Donation</strong>
                                        <small class="d-block text-muted">80G tax benefit eligible</small>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= isCurrentPage('event') ? 'active' : '' ?>" href="<?= SITE_URL ?>/events.php">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isCurrentPage('news') ? 'active' : '' ?>" href="<?= SITE_URL ?>/news.php">News</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isCurrentPage('gallery') ? 'active' : '' ?>" href="<?= SITE_URL ?>/gallery.php">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isCurrentPage('product') ? 'active' : '' ?>" href="<?= SITE_URL ?>/products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isCurrentPage('contact') ? 'active' : '' ?>" href="<?= SITE_URL ?>/contact.php">Contact</a>
                    </li>

                    <!-- Action Buttons -->
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0 d-flex gap-2">
                        <a class="btn btn-feed-quick d-none d-xl-inline-flex align-items-center" href="<?= SITE_URL ?>/feed-a-cow.php">
                            <i class="bi bi-flower1 me-1"></i> Feed Cow
                        </a>
                        <a class="btn btn-donate" href="<?= SITE_URL ?>/donate.php">
                            <i class="bi bi-heart-fill me-1"></i> Donate Now
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- Flash Notification Banner -->
<div class="container mt-3 flash-messages-container">
    <?= renderFlashMessages() ?>
</div>
