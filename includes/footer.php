<?php
/**
 * Footer
 * Kamadenu Goushala
 */

$siteName = getSetting('site_name', 'Kamadenu Goushala');
$footerText = getSetting('footer_text', 'Dedicated to protecting and serving Gau Mata with devotion.');
$phone = getSetting('phone', '[PHONE NUMBER]');
$email = getSetting('email', '[EMAIL ADDRESS]');
$address = getSetting('address', '[GOUSHALA ADDRESS]');
$whatsapp = getSetting('whatsapp', '');
$whatsappLink = getWhatsAppLink('🙏 Namaste, I would like to support Gau Seva.');
$currentYear = date('Y');
?>

<!-- Donation CTA Section -->
<section class="cta-section" id="ctaSection">
    <div class="container text-center">
        <h2 class="cta-title">Your Seva Makes a Difference</h2>
        <p class="cta-subtitle">Every contribution, big or small, helps us protect and care for Gau Mata. Join us in this sacred mission.</p>
        <div class="cta-buttons">
            <a href="<?= SITE_URL ?>/donate.php" class="btn btn-donate btn-lg me-2 mb-2">
                <i class="bi bi-heart-fill me-1"></i> Donate Now
            </a>
            <a href="<?= SITE_URL ?>/feed-a-cow.php" class="btn btn-outline-light btn-lg me-2 mb-2">
                <i class="bi bi-flower1 me-1"></i> Feed a Cow
            </a>
            <a href="<?= SITE_URL ?>/adopt-a-cow.php" class="btn btn-outline-light btn-lg mb-2">
                <i class="bi bi-house-heart me-1"></i> Adopt a Cow
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="site-footer" id="siteFooter">
    <div class="container">
        <div class="row g-4">
            <!-- About Column -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?= ASSETS_URL ?>/images/logo-icon.png" alt="<?= e($siteName) ?>" class="footer-brand-logo me-2" width="44" height="44">
                        <h3 class="footer-logo-text"><?= e($siteName) ?></h3>
                    </div>
                    <p class="footer-about-text"><?= e($footerText) ?></p>
                    <a href="<?= SITE_URL ?>/donate.php" class="btn btn-donate btn-sm mt-2">
                        <i class="bi bi-heart-fill me-1"></i> Support Gau Seva
                    </a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 col-6">
                <h4 class="footer-heading">Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="<?= SITE_URL ?>/">Home</a></li>
                    <li><a href="<?= SITE_URL ?>/about.php">About Us</a></li>
                    <li><a href="<?= SITE_URL ?>/cows.php">Our Cows</a></li>
                    <li><a href="<?= SITE_URL ?>/breeds.php">Breeds</a></li>
                    <li><a href="<?= SITE_URL ?>/events.php">Events</a></li>
                    <li><a href="<?= SITE_URL ?>/news.php">News</a></li>
                    <li><a href="<?= SITE_URL ?>/gallery.php">Gallery</a></li>
                    <li><a href="<?= SITE_URL ?>/products.php">Products</a></li>
                </ul>
            </div>
            
            <!-- Seva Links -->
            <div class="col-lg-2 col-md-6 col-6">
                <h4 class="footer-heading">Seva</h4>
                <ul class="footer-links">
                    <li><a href="<?= SITE_URL ?>/gau-seva.php">Seva Offerings</a></li>
                    <li><a href="<?= SITE_URL ?>/feed-a-cow.php">Feed a Cow</a></li>
                    <li><a href="<?= SITE_URL ?>/adopt-a-cow.php">Adopt a Cow</a></li>
                    <li><a href="<?= SITE_URL ?>/donate.php">Donate</a></li>
                </ul>
                <h4 class="footer-heading mt-3">Legal</h4>
                <ul class="footer-links">
                    <li><a href="<?= SITE_URL ?>/privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="<?= SITE_URL ?>/terms.php">Terms & Conditions</a></li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-4 col-md-6">
                <h4 class="footer-heading">Contact Us</h4>
                <ul class="footer-contact">
                    <li>
                        <i class="bi bi-geo-alt-fill"></i>
                        <span><?= e($address) ?></span>
                    </li>
                    <li>
                        <i class="bi bi-telephone-fill"></i>
                        <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= e($phone) ?></a>
                    </li>
                    <li>
                        <i class="bi bi-envelope-fill"></i>
                        <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
                    </li>
                    <?php if ($whatsapp): ?>
                    <li>
                        <i class="bi bi-whatsapp"></i>
                        <a href="<?= e($whatsappLink) ?>" target="_blank" rel="noopener">WhatsApp Chat</a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Social Links -->
                <div class="footer-social mt-3">
                    <?php if ($fb = getSetting('facebook_url')): ?>
                    <a href="<?= e($fb) ?>" target="_blank" rel="noopener" aria-label="Facebook" class="social-link"><i class="bi bi-facebook"></i></a>
                    <?php endif; ?>
                    <?php if ($ig = getSetting('instagram_url')): ?>
                    <a href="<?= e($ig) ?>" target="_blank" rel="noopener" aria-label="Instagram" class="social-link"><i class="bi bi-instagram"></i></a>
                    <?php endif; ?>
                    <?php if ($yt = getSetting('youtube_url')): ?>
                    <a href="<?= e($yt) ?>" target="_blank" rel="noopener" aria-label="YouTube" class="social-link"><i class="bi bi-youtube"></i></a>
                    <?php endif; ?>
                    <?php if ($whatsapp): ?>
                    <a href="<?= e($whatsappLink) ?>" target="_blank" rel="noopener" aria-label="WhatsApp" class="social-link"><i class="bi bi-whatsapp"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p>&copy; <?= $currentYear ?> <?= e($siteName) ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="footer-tagline">Serving Gau Mata with devotion 🙏</p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- WhatsApp Floating Button -->
<?php if ($whatsapp): ?>
<a href="<?= e($whatsappLink) ?>" target="_blank" rel="noopener" class="whatsapp-float" aria-label="Chat on WhatsApp" id="whatsappFloat">
    <i class="bi bi-whatsapp"></i>
</a>
<?php endif; ?>

<!-- Back to Top -->
<button class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="bi bi-chevron-up"></i>
</button>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?= ASSETS_URL ?>/js/main.js"></script>

<?php if (!empty($extraJs)): ?>
    <?php foreach ($extraJs as $js): ?>
    <script src="<?= e($js) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
