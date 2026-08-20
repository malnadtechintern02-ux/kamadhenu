<?php
/**
 * Breed Details Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';

$slug = getParam('slug');
if (!$slug) { redirect(SITE_URL . '/breeds.php'); }

$breed = dbFetchOne("SELECT * FROM breeds WHERE slug = ? AND is_active = 1", [$slug]);
if (!$breed) {
    http_response_code(404);
    include BASE_PATH . '/404.php';
    exit;
}

$cows = dbFetchAll(
    "SELECT * FROM cows WHERE breed_id = ? ORDER BY is_featured DESC, name ASC",
    [$breed['id']]
);

$seo = [
    'title' => $breed['name'] . ' - Indigenous Cow Breed',
    'description' => truncateText($breed['description'] ?? $breed['name'] . ' indigenous cow breed at Kamadhenu Goushala.', 160),
];

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/breeds.php">Breeds</a></li>
                <li class="breadcrumb-item active"><?= e($breed['name']) ?></li>
            </ol>
        </nav>
        <h1><?= e($breed['name']) ?></h1>
        <p>Origin: <?= e($breed['origin'] ?? 'India') ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5 animate-on-scroll">
                <div class="rounded-4 overflow-hidden shadow-lg">
                    <img src="<?= getUploadUrl($breed['image'] ? 'breeds/' . $breed['image'] : '', getPlaceholderImage($breed['name'], 500, 400)) ?>" 
                         alt="<?= e($breed['name']) ?>" class="w-100" style="aspect-ratio:4/3; object-fit:cover;"
                         onerror="this.src='<?= getPlaceholderImage($breed['name'], 500, 400) ?>'">
                </div>
            </div>
            <div class="col-lg-7 animate-on-scroll">
                <h2><?= e($breed['name']) ?></h2>
                <p class="text-muted mb-3"><i class="bi bi-geo-alt me-1"></i>Origin: <?= e($breed['origin'] ?? 'India') ?></p>
                
                <?php if (!empty($breed['description'])): ?>
                <p><?= nl2br(e($breed['description'])) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($breed['milk_quality'])): ?>
                <div class="p-3 rounded-3 mb-3" style="background:var(--clr-primary-light);">
                    <h5 class="text-primary-custom mb-2"><i class="bi bi-droplet-fill me-1"></i> Milk Quality</h5>
                    <p class="mb-0"><?= nl2br(e($breed['milk_quality'])) ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($breed['characteristics'])): ?>
                <div class="p-3 rounded-3" style="background:var(--clr-saffron-light);">
                    <h5 class="text-saffron mb-2"><i class="bi bi-list-check me-1"></i> Characteristics</h5>
                    <p class="mb-0"><?= nl2br(e($breed['characteristics'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($cows)): ?>
<section class="section section-warm">
    <div class="container">
        <h2 class="section-title">Our <?= e($breed['name']) ?> Cows</h2>
        <p class="section-subtitle"><?= count($cows) ?> <?= e($breed['name']) ?> cows at Kamadhenu Goushala</p>
        
        <div class="row g-4">
            <?php foreach ($cows as $cow): ?>
            <div class="col-lg-4 col-md-6 animate-on-scroll">
                <div class="cow-card">
                    <div class="cow-img-wrapper">
                        <img src="<?= getCowPhotoUrl($cow['photo'], $cow['name']) ?>" alt="<?= e($cow['name']) ?>" loading="lazy">
                        <span class="cow-status"><span class="badge <?= getStatusBadgeClass($cow['status']) ?>"><?= e($cow['status']) ?></span></span>
                    </div>
                    <div class="cow-info">
                        <h3 class="cow-name"><?= e($cow['name']) ?></h3>
                        <div class="cow-meta">
                            <span><i class="bi bi-calendar"></i> <?= calculateAge($cow['date_of_birth']) ?></span>
                            <span><i class="bi bi-gender-ambiguous"></i> <?= e($cow['gender']) ?></span>
                        </div>
                        <div class="cow-actions">
                            <a href="<?= SITE_URL ?>/cow-details.php?id=<?= (int)$cow['id'] ?>" class="btn btn-outline-custom btn-sm flex-fill">View Profile</a>
                            <?php if ($cow['is_adoptable'] && $cow['status'] === 'Available'): ?>
                            <a href="<?= SITE_URL ?>/adopt-a-cow.php?cow=<?= (int)$cow['id'] ?>" class="btn btn-primary-custom btn-sm flex-fill">Adopt</a>
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
