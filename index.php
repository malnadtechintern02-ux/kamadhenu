<?php
/**
 * Homepage
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/pagination.php';

// SEO
$seo = [
    'title' => 'Protecting Gau Mata, Preserving Our Heritage',
    'description' => getSetting('site_description', 'Kamadhenu Goushala is dedicated to protecting indigenous Indian cow breeds and promoting sustainable agriculture practices rooted in our ancient traditions.'),
    'keywords' => 'kamadenu goushala, gau seva, cow protection, indigenous breeds, donate, adopt a cow, gir cow, hallikar, malenadu gidda'
];

// Fetch homepage data
$featuredCows = dbFetchAll(
    "SELECT c.*, b.name as breed_name FROM cows c 
     LEFT JOIN breeds b ON c.breed_id = b.id 
     WHERE c.is_featured = 1 
     ORDER BY c.created_at DESC LIMIT 6"
);

$breeds = dbFetchAll(
    "SELECT * FROM breeds WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 5"
);

$sevaCategories = dbFetchAll(
    "SELECT * FROM seva_categories WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 6"
);

$upcomingEvents = dbFetchAll(
    "SELECT * FROM events WHERE status = 'Upcoming' AND event_date >= CURDATE() 
     ORDER BY event_date ASC LIMIT 3"
);

$latestNews = dbFetchAll(
    "SELECT n.*, nc.name as category_name FROM news n 
     LEFT JOIN news_categories nc ON n.category_id = nc.id 
     WHERE n.status = 'Published' 
     ORDER BY n.published_date DESC LIMIT 3"
);

$featuredProducts = dbFetchAll(
    "SELECT p.*, pc.name as category_name FROM products p 
     LEFT JOIN product_categories pc ON p.category_id = pc.id 
     WHERE p.is_featured = 1 AND p.is_active = 1 
     ORDER BY p.created_at DESC LIMIT 4"
);

$testimonials = dbFetchAll(
    "SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 5"
);

$galleryImages = dbFetchAll(
    "SELECT g.*, gc.name as category_name FROM gallery g 
     LEFT JOIN gallery_categories gc ON g.category_id = gc.id 
     WHERE g.is_active = 1 
     ORDER BY g.sort_order ASC, g.created_at DESC LIMIT 8"
);

// Stats
$statTotalCows = getSetting('stat_total_cows', '70');
$statRescuedCows = getSetting('stat_rescued_cows', '45');
$statSevaPrograms = getSetting('stat_seva_programs', '8');
$statYearsService = getSetting('stat_years_service', '6');

// Hero Section Settings
$heroBgPath = getSetting('hero_bg');
$heroBgUrl = !empty($heroBgPath) ? getUploadUrl('hero/' . $heroBgPath) : ASSETS_URL . '/images/hero/hero-bg.jpg';
$heroBadge = getSetting('hero_badge', '🙏 Vande Gou Mataram');
$heroTitle = getSetting('hero_title', "Protecting Gau Mata.\nPreserving Our Heritage.");
$heroSubtitle = getSetting('hero_subtitle', 'Serving indigenous cows with compassion, care and devotion at Kamadhenu Goushala, nestled in the sacred lands of Kodagu, Karnataka.');
$heroBtn1Text = getSetting('hero_btn1_text', 'Donate Now');
$heroBtn1Url = getSetting('hero_btn1_url', '/donate.php');
$heroBtn2Text = getSetting('hero_btn2_text', 'Adopt a Cow');
$heroBtn2Url = getSetting('hero_btn2_url', '/adopt-a-cow.php');

$aboutImage = getSetting('about_image');
$aboutImageUrl = !empty($aboutImage) ? getUploadUrl('about/' . $aboutImage) : ASSETS_URL . '/images/about/goushala-about.jpg';

$btn1Url = (str_starts_with($heroBtn1Url, 'http') || str_starts_with($heroBtn1Url, '#')) ? $heroBtn1Url : SITE_URL . '/' . ltrim($heroBtn1Url, '/');
$btn2Url = (str_starts_with($heroBtn2Url, 'http') || str_starts_with($heroBtn2Url, '#')) ? $heroBtn2Url : SITE_URL . '/' . ltrim($heroBtn2Url, '/');

// Include layout
include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<!-- ============================================================
     HERO SECTION
     ============================================================ -->
<section class="hero-section animate-fade-slide delay-100" id="heroSection">
    <div class="hero-bg" style="background-image: url('<?= e($heroBgUrl) ?>');"></div>
    <div class="hero-overlay"></div>
    <div class="hero-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    <div class="hero-content">
        <?php if (!empty($heroBadge)): ?>
        <div class="hero-badge"><?= e($heroBadge) ?></div>
        <?php endif; ?>
        <h1 class="hero-title"><?= nl2br(e($heroTitle)) ?></h1>
        <p class="hero-subtitle"><?= e($heroSubtitle) ?></p>
        <div class="hero-buttons">
            <?php if (!empty($heroBtn1Text)): ?>
            <a href="<?= e($btn1Url) ?>" class="btn btn-donate btn-lg animate-fade-slide delay-100">
                <i class="bi bi-heart-fill me-2"></i><?= e($heroBtn1Text) ?>
            </a>
            <?php endif; ?>
            <?php if (!empty($heroBtn2Text)): ?>
            <a href="<?= e($btn2Url) ?>" class="btn btn-outline-light btn-lg animate-fade-slide delay-100">
                <i class="bi bi-house-heart me-2"></i><?= e($heroBtn2Text) ?>
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     STATS SECTION
     ============================================================ -->
<section class="section animate-fade-slide delay-300" id="newsSection">
    <div class="container">
        <div class="stats-container">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="stat-item animate-on-scroll zoom-in">
                        <div class="stat-icon"><i class="bi bi-heart-fill"></i></div>
                        <div class="stat-number" data-counter="<?= (int)$statTotalCows ?>" data-suffix="+">0</div>
                        <div class="stat-label">Cows Protected</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item animate-on-scroll zoom-in">
                        <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
                        <div class="stat-number" data-counter="<?= (int)$statRescuedCows ?>" data-suffix="+">0</div>
                        <div class="stat-label">Cows Rescued</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item animate-on-scroll zoom-in">
                        <div class="stat-icon"><i class="bi bi-flower1"></i></div>
                        <div class="stat-number" data-counter="<?= (int)$statSevaPrograms ?>">0</div>
                        <div class="stat-label">Seva Programs</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item animate-on-scroll zoom-in">
                        <div class="stat-icon"><i class="bi bi-calendar-heart"></i></div>
                        <div class="stat-number" data-counter="<?= (int)$statYearsService ?>" data-suffix="+">0</div>
                        <div class="stat-label">Years of Service</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     ABOUT GOUSHALA
     ============================================================ -->
<section class="section animate-fade-slide delay-300" id="aboutSection">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 animate-on-scroll fade-left">
                <div class="about-image-wrapper">
                    <img src="<?= e($aboutImageUrl) ?>" 
                         alt="Kamadhenu Goushala" 
                         loading="lazy"
                         onerror="this.src='<?= getPlaceholderImage('Kamadhenu Goushala', 600, 450) ?>'">
                    <div class="about-accent text-center">
                        <div>
                            <div class="years"><?= e($statYearsService) ?>+</div>
                            <div class="years-label">Years</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 animate-on-scroll fade-right">
                <span class="text-uppercase text-primary-custom fw-semibold small ls-wide d-block mb-2">About Our Goushala</span>
                <h2 class="mb-3">A Sacred Sanctuary for Indigenous Cows</h2>
                <p>Kamadhenu Goushala is dedicated to protecting indigenous Indian cow breeds and promoting sustainable agriculture practices rooted in our ancient traditions.</p>
                <p>Established on <strong>24th August 2020</strong> at <strong>Kavadi, Virajpet Taluk, Kodagu (Coorg)</strong>, the Goushala serves as a sanctuary for sacred cows and a center for spreading awareness about Gau Seva.</p>
                <p>Today, we shelter and care for approximately <strong><?= e($statTotalCows) ?> indigenous cows</strong> across five sacred breeds — Gir, Hallikar, Malenadu Gidda, Amritamahal, and Tharparkar.</p>
                <div class="mt-4">
                    <a href="<?= SITE_URL ?>/about.php" class="btn btn-primary-custom">
                        <i class="bi bi-arrow-right me-1"></i> Know Our Story
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     GAU SEVA SECTION
     ============================================================ -->
<section class="section section-warm animate-fade-slide delay-300" id="sevaSection">
    <div class="container">
        <h2 class="section-title">Gau Seva Opportunities</h2>
        <p class="section-subtitle">Support Gau Mata through seva paths rooted in devotion and dharma. Every act of seva brings blessings.</p>
        
        <div class="row g-4">
            <?php foreach ($sevaCategories as $seva): ?>
            <div class="col-lg-4 col-md-6 animate-on-scroll zoom-in">
                <div class="seva-card">
                    <div class="seva-icon">
                        <i class="bi <?= e($seva['icon'] ?: 'bi-heart-fill') ?>"></i>
                    </div>
                    <h3 class="seva-title"><?= e($seva['title']) ?></h3>
                    <p class="seva-text"><?= e($seva['short_description'] ?: truncateText($seva['description'] ?? '', 120)) ?></p>
                    <a href="<?= SITE_URL ?>/donate.php?seva=<?= e($seva['slug']) ?>" class="btn btn-primary-custom btn-sm">
                        Contribute <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($sevaCategories)): ?>
            <!-- Fallback static seva cards -->
            <div class="col-lg-4 col-md-6 animate-on-scroll zoom-in">
                <div class="seva-card">
                    <div class="seva-icon"><i class="bi bi-heart-fill"></i></div>
                    <h3 class="seva-title">Feed a Cow</h3>
                    <p class="seva-text">Sponsor daily food for cows and support their nourishment with nutritious fodder and grains.</p>
                    <a href="<?= SITE_URL ?>/feed-a-cow.php" class="btn btn-primary-custom btn-sm">Feed Now <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 animate-on-scroll zoom-in">
                <div class="seva-card">
                    <div class="seva-icon"><i class="bi bi-house-heart-fill"></i></div>
                    <h3 class="seva-title">Adopt a Cow</h3>
                    <p class="seva-text">Provide full monthly care and become a lifelong protector of a sacred cow.</p>
                    <a href="<?= SITE_URL ?>/adopt-a-cow.php" class="btn btn-primary-custom btn-sm">Adopt Now <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 animate-on-scroll zoom-in">
                <div class="seva-card">
                    <div class="seva-icon"><i class="bi bi-gift-fill"></i></div>
                    <h3 class="seva-title">General Donation</h3>
                    <p class="seva-text">Support the overall operations and mission of the Goushala with a general contribution.</p>
                    <a href="<?= SITE_URL ?>/donate.php" class="btn btn-primary-custom btn-sm">Donate <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     FEATURED COWS
     ============================================================ -->
<section class="section animate-fade-slide delay-300" id="cowsSection">
    <div class="container">
        <h2 class="section-title">Our Beloved Cows</h2>
        <p class="section-subtitle">Meet some of the beautiful indigenous cows we protect and nurture at Kamadhenu Goushala.</p>
        
        <div class="row g-4">
            <?php foreach ($featuredCows as $cow): ?>
            <div class="col-lg-4 col-md-6 animate-on-scroll">
                <div class="cow-card">
                    <div class="cow-img-wrapper">
                        <img src="<?= getCowPhotoUrl($cow['photo'], $cow['name']) ?>" 
                             alt="<?= e($cow['name']) ?>" loading="lazy">
                        <span class="cow-status">
                            <span class="badge <?= getStatusBadgeClass($cow['status']) ?>"><?= e($cow['status']) ?></span>
                        </span>
                    </div>
                    <div class="cow-info">
                        <h3 class="cow-name"><?= e($cow['name']) ?></h3>
                        <div class="cow-meta">
                            <span><i class="bi bi-tag"></i> <?= e($cow['breed_name'] ?? 'Indigenous') ?></span>
                            <span><i class="bi bi-calendar"></i> <?= calculateAge($cow['date_of_birth']) ?></span>
                            <span><i class="bi bi-gender-ambiguous"></i> <?= e($cow['gender']) ?></span>
                        </div>
                        <p class="card-text"><?= e(truncateText($cow['description'] ?? '', 80)) ?></p>
                        <div class="cow-actions">
                            <a href="<?= SITE_URL ?>/cow-details.php?id=<?= (int)$cow['id'] ?>" class="btn btn-outline-custom btn-sm flex-fill">View Profile</a>
                            <?php if ($cow['is_adoptable']): ?>
                            <a href="<?= SITE_URL ?>/adopt-a-cow.php?cow=<?= (int)$cow['id'] ?>" class="btn btn-primary-custom btn-sm flex-fill">Adopt</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (!empty($featuredCows)): ?>
        <div class="text-center mt-4">
            <a href="<?= SITE_URL ?>/cows.php" class="btn btn-outline-custom">
                View All Cows <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================================
     INDIGENOUS BREEDS
     ============================================================ -->
<section class="section section-warm animate-fade-slide delay-300" id="breedsSection">
    <div class="container">
        <h2 class="section-title">Indigenous Breeds We Protect</h2>
        <p class="section-subtitle">Preserving the sacred native cow breeds of Bharath, each with unique characteristics and significance.</p>
        
        <div class="row g-4">
            <?php foreach ($breeds as $breed): ?>
            <div class="col-lg col-md-4 col-6 animate-on-scroll scale-up">
                <a href="<?= SITE_URL ?>/breed-details.php?slug=<?= e($breed['slug']) ?>" class="text-decoration-none">
                    <div class="breed-card">
                        <img src="<?= getUploadUrl($breed['image'] ? 'breeds/' . $breed['image'] : '', getPlaceholderImage($breed['name'], 400, 300)) ?>" 
                             alt="<?= e($breed['name']) ?>" loading="lazy"
                             onerror="this.src='<?= getPlaceholderImage($breed['name'], 400, 300) ?>'">
                        <div class="breed-overlay">
                            <h3 class="breed-name"><?= e($breed['name']) ?></h3>
                            <span class="breed-origin"><?= e($breed['origin'] ?? '') ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?= SITE_URL ?>/breeds.php" class="btn btn-primary-custom">
                Explore All Breeds <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>

<!-- ============================================================
     ADOPTION CTA
     ============================================================ -->
<section class="adoption-cta animate-fade-slide delay-300" id="adoptionCta">
    <div class="container text-center">
        <h2 class="animate-on-scroll">Give a Cow a Better Tomorrow</h2>
        <p class="animate-on-scroll" style="max-width: 650px; margin: 0 auto 2rem;">
            By adopting a cow, you provide food, shelter, and medical care. You become a direct guardian of Gau Mata and receive regular updates about your adopted cow's well-being.
        </p>
        <div class="animate-on-scroll">
            <a href="<?= SITE_URL ?>/adopt-a-cow.php" class="btn btn-donate btn-lg">
                <i class="bi bi-house-heart-fill me-2"></i>Adopt a Cow Today
            </a>
        </div>
    </div>
</section>

<!-- ============================================================
     UPCOMING EVENTS
     ============================================================ -->
<?php if (!empty($upcomingEvents)): ?>
<section class="section animate-fade-slide delay-300" id="eventsSection">
    <div class="container">
        <h2 class="section-title">Upcoming Events</h2>
        <p class="section-subtitle">Join us in our sacred events and programs dedicated to Gau Seva and community service.</p>
        
        <div class="row g-4">
            <?php foreach ($upcomingEvents as $event): 
                $eventDate = new DateTime($event['event_date']);
            ?>
            <div class="col-lg-4 col-md-6 animate-on-scroll">
                <div class="custom-card event-card animate-fade-slide delay-200">
                    <div class="card-img-wrapper">
                        <img src="<?= getUploadUrl($event['image'] ? 'events/' . $event['image'] : '', getPlaceholderImage($event['title'], 400, 250)) ?>" 
                             alt="<?= e($event['title']) ?>" loading="lazy"
                             onerror="this.src='<?= getPlaceholderImage($event['title'], 400, 250) ?>'">
                        <div class="event-date-badge">
                            <span class="day"><?= $eventDate->format('d') ?></span>
                            <span class="month"><?= $eventDate->format('M') ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?= e($event['title']) ?></h3>
                        <p class="event-location"><i class="bi bi-geo-alt me-1"></i><?= e($event['location'] ?? '') ?></p>
                        <p class="card-text"><?= e(truncateText($event['short_description'] ?? $event['description'] ?? '', 100)) ?></p>
                        <a href="<?= SITE_URL ?>/event-details.php?slug=<?= e($event['slug']) ?>" class="btn btn-outline-custom btn-sm mt-auto">
                            View Event <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?= SITE_URL ?>/events.php" class="btn btn-outline-custom">
                View All Events <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================
     LATEST NEWS
     ============================================================ -->
<?php if (!empty($latestNews)): ?>
<section class="section section-warm" id="newsSection">
    <div class="container">
        <h2 class="section-title">Latest News & Updates</h2>
        <p class="section-subtitle">Stay informed about our Gau Seva activities, rescue missions, and community outreach.</p>
        
        <div class="row g-4">
            <?php foreach ($latestNews as $article): ?>
            <div class="col-lg-4 col-md-6 animate-on-scroll">
                <div class="custom-card animate-fade-slide delay-200">
                    <div class="card-img-wrapper">
                        <img src="<?= getUploadUrl($article['featured_image'] ? 'news/' . $article['featured_image'] : '', getPlaceholderImage($article['title'], 400, 250)) ?>" 
                             alt="<?= e($article['title']) ?>" loading="lazy"
                             onerror="this.src='<?= getPlaceholderImage($article['title'], 400, 250) ?>'">
                        <?php if (!empty($article['category_name'])): ?>
                        <span class="card-badge bg-primary text-white"><?= e($article['category_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 small text-muted">
                            <i class="bi bi-calendar3 me-1"></i>
                            <span><?= formatDate($article['published_date']) ?></span>
                            <span class="mx-2">·</span>
                            <span><?= e($article['author'] ?? 'Kamadenu Goushala') ?></span>
                        </div>
                        <h3 class="card-title"><?= e($article['title']) ?></h3>
                        <p class="card-text"><?= e(truncateText($article['short_description'] ?? strip_tags($article['content'] ?? ''), 120)) ?></p>
                        <a href="<?= SITE_URL ?>/news-details.php?slug=<?= e($article['slug']) ?>" class="btn btn-outline-custom btn-sm mt-auto">
                            Read More <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?= SITE_URL ?>/news.php" class="btn btn-outline-custom">
                View All News <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================
     GALLERY PREVIEW
     ============================================================ -->
<?php if (!empty($galleryImages)): ?>
<section class="section animate-fade-slide delay-300" id="gallerySection">
    <div class="container">
        <h2 class="section-title">Photo Gallery</h2>
        <p class="section-subtitle">Glimpses of daily life, care, and devotion at Kamadhenu Goushala.</p>
        
        <div class="gallery-grid">
            <?php foreach ($galleryImages as $img): ?>
            <div class="gallery-item animate-on-scroll zoom-in" data-gallery="<?= getUploadUrl('gallery/' . $img['image_path']) ?>" data-caption="<?= e($img['caption'] ?? '') ?>">
                <img src="<?= getUploadUrl('gallery/' . $img['image_path'], getPlaceholderImage($img['caption'] ?? 'Gallery', 300, 300)) ?>" 
                     alt="<?= e($img['alt_text'] ?? $img['caption'] ?? 'Goushala gallery') ?>" loading="lazy"
                     onerror="this.src='<?= getPlaceholderImage($img['caption'] ?? 'Gallery', 300, 300) ?>'">
                <div class="gallery-overlay">
                    <i class="bi bi-zoom-in"></i>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?= SITE_URL ?>/gallery.php" class="btn btn-outline-custom">
                View Full Gallery <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================
     FEATURED PRODUCTS
     ============================================================ -->
<?php if (!empty($featuredProducts)): ?>
<section class="section section-warm animate-fade-slide delay-300" id="productsSection">
    <div class="container">
        <h2 class="section-title">Goushala Products</h2>
        <p class="section-subtitle">Authentic, natural products made from indigenous cow derivatives. Support Gau Seva with every purchase.</p>
        
        <div class="row g-4">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="col-lg-3 col-md-6 animate-on-scroll">
                <div class="custom-card product-card animate-fade-slide delay-200">
                    <div class="card-img-wrapper">
                        <img src="<?= getUploadUrl($product['image'] ? 'products/' . $product['image'] : '', getPlaceholderImage($product['name'], 400, 300)) ?>" 
                             alt="<?= e($product['name']) ?>" loading="lazy"
                             onerror="this.src='<?= getPlaceholderImage($product['name'], 400, 300) ?>'">
                        <span class="card-badge <?= getStatusBadgeClass($product['stock_status']) ?>"><?= e($product['stock_status']) ?></span>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($product['category_name'])): ?>
                        <small class="text-muted text-uppercase fw-semibold"><?= e($product['category_name']) ?></small>
                        <?php endif; ?>
                        <h3 class="card-title mt-1"><?= e($product['name']) ?></h3>
                        <p class="product-price mb-2"><?= formatCurrency($product['price']) ?></p>
                        <p class="card-text small"><?= e(truncateText($product['description'] ?? '', 80)) ?></p>
                        <div class="d-flex gap-2 mt-auto">
                            <a href="<?= SITE_URL ?>/product-details.php?slug=<?= e($product['slug']) ?>" class="btn btn-outline-custom btn-sm flex-fill">View Details</a>
                            <?php 
                            $prodMsg = !empty($product['whatsapp_message']) ? $product['whatsapp_message'] : '🙏 Namaste, I am interested in: ' . $product['name'] . ' (₹' . $product['price'] . '). Please share details.';
                            $waLink = getWhatsAppLink($prodMsg, $product['whatsapp_number']);
                            ?>
                            <a href="<?= e($waLink) ?>" 
                               target="_blank" class="btn btn-sm flex-shrink-0" style="background:#25D366; color:white; border-radius:var(--radius-md);">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?= SITE_URL ?>/products.php" class="btn btn-primary-custom">
                View All Products <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================
     TESTIMONIALS
     ============================================================ -->
<?php if (!empty($testimonials)): ?>
<section class="section animate-fade-slide delay-300" id="testimonialsSection">
    <div class="container">
        <h2 class="section-title">What People Say</h2>
        <p class="section-subtitle">Hear from our supporters and visitors about their experience with Kamadhenu Goushala.</p>
        
        <div class="row g-4">
            <?php foreach (array_slice($testimonials, 0, 3) as $testimonial): ?>
            <div class="col-lg-4 col-md-6 animate-on-scroll flip-up">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                        <i class="bi bi-star-fill"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="testimonial-text">"<?= e($testimonial['message']) ?>"</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">
                            <?= strtoupper(mb_substr($testimonial['name'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="testimonial-name"><?= e($testimonial['name']) ?></div>
                            <?php if (!empty($testimonial['location'])): ?>
                            <div class="testimonial-location"><?= e($testimonial['location']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include BASE_PATH . '/includes/footer.php'; ?>
