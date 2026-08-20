<?php
/**
 * Gau Seva Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';

$seo = [
    'title' => 'Gau Seva - Seva Offerings',
    'description' => 'Explore Gau Seva opportunities at Kamadhenu Goushala. Feed a cow, adopt a cow, medical seva, fodder seva, and more.',
];

$sevaCategories = dbFetchAll("SELECT * FROM seva_categories WHERE is_active = 1 ORDER BY sort_order ASC");

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active">Gau Seva</li>
            </ol>
        </nav>
        <h1>Gau Seva Offerings</h1>
        <p>Support Gau Mata through seva paths rooted in devotion and dharma.</p>
    </div>
</section>

<!-- Seva Categories -->
<section class="section">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($sevaCategories as $seva): 
                $amounts = array_filter(explode(',', $seva['suggested_amounts'] ?? ''));
            ?>
            <div class="col-lg-6 animate-on-scroll">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="row g-0 h-100">
                        <div class="col-md-5">
                            <div class="h-100" style="min-height:220px; background:var(--clr-primary-light); display:flex; align-items:center; justify-content:center;">
                                <?php if (!empty($seva['image'])): ?>
                                <img src="<?= getUploadUrl('seva/' . $seva['image']) ?>" alt="<?= e($seva['title']) ?>" 
                                     class="w-100 h-100" style="object-fit:cover;"
                                     onerror="this.parentElement.innerHTML='<i class=\'bi <?= e($seva['icon'] ?: 'bi-heart-fill') ?>\' style=\'font-size:3rem; color:var(--clr-primary);\'></i>'">
                                <?php else: ?>
                                <i class="bi <?= e($seva['icon'] ?: 'bi-heart-fill') ?>" style="font-size:3rem; color:var(--clr-primary);"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="card-body p-4 d-flex flex-column h-100">
                                <h3 class="card-title fs-5 mb-2"><?= e($seva['title']) ?></h3>
                                <p class="card-text small text-muted flex-grow-1">
                                    <?= e(truncateText($seva['description'] ?? $seva['short_description'] ?? '', 150)) ?>
                                </p>
                                
                                <?php if (!empty($amounts)): ?>
                                <div class="mb-3">
                                    <small class="fw-semibold text-muted d-block mb-1">Suggested amounts:</small>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach ($amounts as $amt): ?>
                                        <span class="badge bg-primary-light text-primary-custom"><?= formatCurrency((float)trim($amt)) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($seva['benefits'])): ?>
                                <p class="small text-muted mb-3"><i class="bi bi-star-fill text-gold me-1"></i><?= e(truncateText($seva['benefits'], 100)) ?></p>
                                <?php endif; ?>
                                
                                <a href="<?= SITE_URL ?>/donate.php?seva=<?= e($seva['slug']) ?>" class="btn btn-primary-custom btn-sm">
                                    <i class="bi bi-heart-fill me-1"></i> Donate for <?= e($seva['title']) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- How Seva Helps -->
<section class="section section-warm">
    <div class="container">
        <h2 class="section-title">How Your Seva Helps</h2>
        <p class="section-subtitle">Every contribution directly supports the well-being of Gau Mata at our Goushala.</p>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 animate-on-scroll">
                <div class="text-center">
                    <div class="stat-icon mx-auto mb-3"><i class="bi bi-flower1"></i></div>
                    <h5>Daily Feeding</h5>
                    <p class="small text-muted">Each cow requires ₹80-100 worth of food daily — green fodder, grains, jaggery, and mineral supplements.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 animate-on-scroll">
                <div class="text-center">
                    <div class="stat-icon mx-auto mb-3"><i class="bi bi-plus-circle"></i></div>
                    <h5>Medical Care</h5>
                    <p class="small text-muted">Regular vaccinations, deworming, dental care, and emergency treatments for all our cows.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 animate-on-scroll">
                <div class="text-center">
                    <div class="stat-icon mx-auto mb-3"><i class="bi bi-building"></i></div>
                    <h5>Shelter</h5>
                    <p class="small text-muted">Clean, spacious shelters with proper drainage, ventilation, and bedding for all cows.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 animate-on-scroll">
                <div class="text-center">
                    <div class="stat-icon mx-auto mb-3"><i class="bi bi-people"></i></div>
                    <h5>Staff & Care</h5>
                    <p class="small text-muted">Dedicated caretakers who tend to the cows with love and devotion every single day.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Donate -->
<section class="adoption-cta">
    <div class="container text-center">
        <h2 class="animate-on-scroll">Every Rupee Counts</h2>
        <p class="animate-on-scroll" style="max-width:600px; margin:0 auto 2rem; color:rgba(255,255,255,.85);">
            Whether you contribute ₹101 or ₹11,000, your seva directly impacts the life of Gau Mata.
        </p>
        <div class="animate-on-scroll">
            <a href="<?= SITE_URL ?>/donate.php" class="btn btn-donate btn-lg me-2"><i class="bi bi-heart-fill me-1"></i> Donate Now</a>
            <a href="<?= SITE_URL ?>/adopt-a-cow.php" class="btn btn-outline-light btn-lg"><i class="bi bi-house-heart me-1"></i> Adopt a Cow</a>
        </div>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
