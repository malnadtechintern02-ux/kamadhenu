<?php
/**
 * Indigenous Breeds Listing
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';

$seo = [
    'title' => 'Indigenous Cow Breeds',
    'description' => 'Explore the sacred indigenous cow breeds protected at Kamadhenu Goushala - Gir, Hallikar, Malenadu Gidda, Amritamahal, and Tharparkar.',
];

$breeds = dbFetchAll("SELECT * FROM breeds WHERE is_active = 1 ORDER BY sort_order ASC");

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active">Indigenous Breeds</li>
            </ol>
        </nav>
        <h1>Indigenous Cow Breeds</h1>
        <p>Explore the sacred native breeds protected at Kamadhenu Goushala.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php foreach ($breeds as $i => $breed): 
            $cowCount = dbCount('cows', 'breed_id = ?', [$breed['id']]);
        ?>
        <div class="row align-items-center g-5 mb-5 animate-on-scroll <?= $i % 2 ? 'flex-row-reverse' : '' ?>">
            <div class="col-lg-5">
                <div class="breed-card" style="height: 350px;">
                    <img src="<?= getUploadUrl($breed['image'] ? 'breeds/' . $breed['image'] : '', getPlaceholderImage($breed['name'], 500, 350)) ?>" 
                         alt="<?= e($breed['name']) ?>" loading="lazy"
                         onerror="this.src='<?= getPlaceholderImage($breed['name'], 500, 350) ?>'">
                    <div class="breed-overlay">
                        <h3 class="breed-name"><?= e($breed['name']) ?></h3>
                        <span class="breed-origin"><?= e($breed['origin'] ?? '') ?></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <span class="text-uppercase text-primary-custom fw-semibold small"><?= e($breed['origin'] ?? '') ?></span>
                <h2 class="mt-1 mb-3"><?= e($breed['name']) ?></h2>
                <p><?= e(truncateText($breed['description'] ?? '', 250)) ?></p>
                
                <?php if (!empty($breed['milk_quality'])): ?>
                <div class="p-3 rounded-3 mb-3" style="background:var(--clr-primary-light);">
                    <strong class="text-primary-custom"><i class="bi bi-droplet-fill me-1"></i> Milk Quality:</strong>
                    <p class="mb-0 mt-1 small"><?= e(truncateText($breed['milk_quality'], 200)) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="d-flex gap-3 align-items-center">
                    <a href="<?= SITE_URL ?>/breed-details.php?slug=<?= e($breed['slug']) ?>" class="btn btn-primary-custom">
                        View Breed <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <span class="text-muted small"><i class="bi bi-heart me-1"></i><?= $cowCount ?> cows at our Goushala</span>
                </div>
            </div>
        </div>
        <?php if ($i < count($breeds) - 1): ?>
        <hr class="my-4" style="border-color: var(--clr-border-light);">
        <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
