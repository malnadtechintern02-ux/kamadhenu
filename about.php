<?php
/**
 * About Us Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';

$seo = [
    'title' => 'About Us',
    'description' => 'Learn about Kamadhenu Goushala - our mission, history, team, and dedication to protecting indigenous Indian cow breeds in Kodagu, Karnataka.',
];

$breeds = dbFetchAll("SELECT * FROM breeds WHERE is_active = 1 ORDER BY sort_order ASC");
$statYears = getSetting('stat_years_service', '6');
$statCows = getSetting('stat_total_cows', '70');

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About Us</li>
            </ol>
        </nav>
        <h1>About Kamadhenu Goushala</h1>
        <p>A sacred space dedicated to protecting indigenous cows and preserving the spiritual traditions of Bharath.</p>
    </div>
</section>

<!-- About Content -->
<section class="section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 animate-on-scroll fade-left">
                <div class="about-image-wrapper">
                    <img src="<?= ASSETS_URL ?>/images/about/goushala-about.jpg" 
                         alt="Kamadhenu Goushala" loading="lazy"
                         onerror="this.src='<?= getPlaceholderImage('Kamadhenu Goushala', 600, 450) ?>'">
                    <div class="about-accent text-center">
                        <div>
                            <div class="years"><?= e($statYears) ?>+</div>
                            <div class="years-label">Years</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 animate-on-scroll fade-right">
                <span class="text-uppercase text-primary-custom fw-semibold small d-block mb-2">Our Story</span>
                <h2 class="mb-3">About Our Goushala</h2>
                <p>Kamadhenu Goushala is dedicated to protecting indigenous Indian cow breeds and promoting sustainable agriculture practices rooted in our ancient traditions.</p>
                <p>Established on <strong>24th August 2020</strong> at <strong>Kavadi, Virajpet Taluk, Kodagu (Coorg)</strong>, the Goushala serves as a sanctuary for sacred cows and a center for spreading awareness about Gau Seva.</p>
                <p>Our mission extends beyond mere sheltering — we strive to educate communities about the immense cultural, spiritual, agricultural, and health significance of indigenous cow breeds. We believe that protecting Gau Mata is not just a duty but a sacred privilege.</p>
                
                <div class="row g-3 mt-3">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon me-3" style="width:48px;height:48px;font-size:1.1rem;"><i class="bi bi-heart-fill"></i></div>
                            <div>
                                <div class="fw-bold text-primary-custom fs-5"><?= e($statCows) ?>+</div>
                                <small class="text-muted">Cows Protected</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon me-3" style="width:48px;height:48px;font-size:1.1rem;"><i class="bi bi-grid-fill"></i></div>
                            <div>
                                <div class="fw-bold text-primary-custom fs-5"><?= count($breeds) ?></div>
                                <small class="text-muted">Breeds Preserved</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quote Section -->
<section class="section section-warm">
    <div class="container">
        <div class="text-center mx-auto animate-on-scroll blur-in" style="max-width: 750px;">
            <i class="bi bi-quote fs-1 text-gold d-block mb-3"></i>
            <blockquote class="font-heading fs-4 text-brown mb-3" style="line-height: 1.6;">
                "The place we refer to as home deserves to be recognized as such only if it is filled with devotion and respect for Guru and Gau Mata."
            </blockquote>
            <p class="text-muted fw-semibold">
                — Jagadguru Shankaracharya Sri Sri Raghaveshwara Bharati Mahaswamiji
            </p>
        </div>
    </div>
</section>

<!-- Our Journey Timeline -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Our Journey</h2>
        <p class="section-subtitle">From humble beginnings to a thriving sanctuary for indigenous cows.</p>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="timeline-wrapper">
                    <?php
                    $timeline = [
                        ['year' => '2020', 'title' => 'Foundation', 'desc' => 'Kamadhenu Goushala was established on 24th August 2020 with the mission of protecting indigenous cow breeds at Kavadi, Virajpet Taluk, Kodagu.'],
                        ['year' => '2021', 'title' => 'Growth & Products', 'desc' => 'The Goushala expanded and began producing traditional cow-based products including pure desi ghee, Panchagavya items, and cow dung products.'],
                        ['year' => '2023', 'title' => 'Awareness Programs', 'desc' => 'Launched awareness programs to educate farmers about indigenous cow breeds, A2 milk benefits, and sustainable farming practices.'],
                        ['year' => '2024', 'title' => 'Rescue Operations', 'desc' => 'Started active cow rescue operations, bringing in abandoned, injured, and neglected indigenous cows from across Karnataka.'],
                        ['year' => 'Today', 'title' => 'Thriving Sanctuary', 'desc' => 'The Goushala now shelters approximately ' . $statCows . ' cows across 5 indigenous breeds and continues expanding its service to the community.'],
                    ];
                    foreach ($timeline as $i => $item):
                    ?>
                    <div class="d-flex mb-4 animate-on-scroll fade-left">
                        <div class="me-4 text-center" style="min-width: 80px;">
                            <div class="bg-primary text-white rounded-3 px-3 py-2 fw-bold font-heading"><?= e($item['year']) ?></div>
                            <?php if ($i < count($timeline) - 1): ?>
                            <div class="mx-auto mt-2" style="width:2px; height:40px; background: var(--clr-primary-light);"></div>
                            <?php endif; ?>
                        </div>
                        <div class="pb-2">
                            <h4 class="mb-1"><?= e($item['title']) ?></h4>
                            <p class="text-muted mb-0"><?= e($item['desc']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Mission -->
<section class="section section-warm">
    <div class="container">
        <h2 class="section-title">Our Mission</h2>
        <div class="row g-4 mt-2">
            <div class="col-lg-4 col-md-6 animate-on-scroll zoom-in">
                <div class="seva-card">
                    <div class="seva-icon"><i class="bi bi-shield-check"></i></div>
                    <h3 class="seva-title">Protection</h3>
                    <p class="seva-text">Provide safe shelter, nutritious food, and medical care to indigenous cows, especially those rescued from neglect or abuse.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 animate-on-scroll zoom-in">
                <div class="seva-card">
                    <div class="seva-icon"><i class="bi bi-book"></i></div>
                    <h3 class="seva-title">Education</h3>
                    <p class="seva-text">Educate communities about the importance of indigenous breeds, A2 milk, sustainable farming, and cow-based products.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 animate-on-scroll zoom-in">
                <div class="seva-card">
                    <div class="seva-icon"><i class="bi bi-tree"></i></div>
                    <h3 class="seva-title">Sustainability</h3>
                    <p class="seva-text">Promote organic farming using cow-based inputs and develop self-sustainable models for Goushala management.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Indigenous Breeds -->
<?php if (!empty($breeds)): ?>
<section class="section">
    <div class="container">
        <h2 class="section-title">Indigenous Cow Breeds</h2>
        <p class="section-subtitle">We protect and nurture these sacred indigenous breeds at our Goushala.</p>
        
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
    </div>
</section>
<?php endif; ?>

<!-- Support CTA -->
<section class="adoption-cta">
    <div class="container text-center">
        <h2 class="animate-on-scroll">Support Gau Seva</h2>
        <p class="animate-on-scroll" style="max-width:600px; margin:0 auto 2rem; color:rgba(255,255,255,.85);">
            Help us protect and nurture sacred cows. Your support makes our mission possible.
        </p>
        <div class="animate-on-scroll">
            <a href="<?= SITE_URL ?>/donate.php" class="btn btn-donate btn-lg me-2">
                <i class="bi bi-heart-fill me-1"></i> Donate Now
            </a>
            <a href="<?= SITE_URL ?>/contact.php" class="btn btn-outline-light btn-lg">
                <i class="bi bi-chat me-1"></i> Get in Touch
            </a>
        </div>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
