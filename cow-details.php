<?php
/**
 * Cow Details Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';

$cowId = getIntParam('id');
if (!$cowId) { redirect(SITE_URL . '/cows.php'); }

$cow = dbFetchOne(
    "SELECT c.*, b.name as breed_name, b.slug as breed_slug, b.origin as breed_origin 
     FROM cows c LEFT JOIN breeds b ON c.breed_id = b.id WHERE c.id = ?",
    [$cowId]
);

if (!$cow) {
    http_response_code(404);
    include BASE_PATH . '/404.php';
    exit;
}

$cowGallery = dbFetchAll("SELECT * FROM cow_gallery WHERE cow_id = ? ORDER BY sort_order ASC", [$cowId]);

$relatedCows = dbFetchAll(
    "SELECT c.*, b.name as breed_name FROM cows c 
     LEFT JOIN breeds b ON c.breed_id = b.id 
     WHERE c.id != ? AND c.breed_id = ? 
     ORDER BY RAND() LIMIT 3",
    [$cowId, $cow['breed_id']]
);

$seo = [
    'title' => $cow['name'] . ' - ' . ($cow['breed_name'] ?? 'Indigenous Cow'),
    'description' => truncateText($cow['description'] ?? 'Meet ' . $cow['name'] . ', a beautiful ' . ($cow['breed_name'] ?? 'indigenous') . ' cow at Kamadhenu Goushala.', 160),
];

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/cows.php">Our Cows</a></li>
                <li class="breadcrumb-item active"><?= e($cow['name']) ?></li>
            </ol>
        </nav>
        <h1><?= e($cow['name']) ?></h1>
        <p><?= e($cow['breed_name'] ?? 'Indigenous') ?> · <?= calculateAge($cow['date_of_birth']) ?> · <?= e($cow['gender']) ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5">
            <!-- Photo -->
            <div class="col-lg-5 animate-on-scroll">
                <div class="rounded-4 overflow-hidden shadow-lg">
                    <img src="<?= getCowPhotoUrl($cow['photo'], $cow['name']) ?>" 
                         alt="<?= e($cow['name']) ?>" class="w-100" style="aspect-ratio:1; object-fit:cover;">
                </div>
                
                <?php if (!empty($cowGallery)): ?>
                <div class="row g-2 mt-3">
                    <?php foreach ($cowGallery as $img): ?>
                    <div class="col-3">
                        <div class="gallery-item rounded-3" data-gallery="<?= getUploadUrl('cows/' . $img['image_path']) ?>" data-caption="<?= e($img['caption'] ?? $cow['name']) ?>">
                            <img src="<?= getUploadUrl('cows/' . $img['image_path'], getPlaceholderImage($cow['name'], 100, 100)) ?>" 
                                 alt="<?= e($img['caption'] ?? $cow['name']) ?>" class="rounded-3"
                                 onerror="this.src='<?= getPlaceholderImage($cow['name'], 100, 100) ?>'">
                            <div class="gallery-overlay"><i class="bi bi-zoom-in"></i></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Details -->
            <div class="col-lg-7 animate-on-scroll">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge <?= getStatusBadgeClass($cow['status']) ?> fs-6"><?= e($cow['status']) ?></span>
                    <?php if ($cow['is_adoptable']): ?>
                    <span class="badge bg-primary-light text-primary-custom fs-6">Adoptable</span>
                    <?php endif; ?>
                </div>
                
                <h2 class="mb-3"><?= e($cow['name']) ?></h2>
                
                <!-- Info Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center p-3 rounded-3" style="background:var(--clr-primary-light);">
                            <i class="bi bi-tag-fill text-primary-custom fs-4 me-3"></i>
                            <div><small class="text-muted">Breed</small><div class="fw-semibold"><?= e($cow['breed_name'] ?? 'Indigenous') ?></div></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center p-3 rounded-3" style="background:var(--clr-saffron-light);">
                            <i class="bi bi-calendar-fill text-saffron fs-4 me-3"></i>
                            <div><small class="text-muted">Age</small><div class="fw-semibold"><?= calculateAge($cow['date_of_birth']) ?></div></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center p-3 rounded-3" style="background:var(--clr-gold-light);">
                            <i class="bi bi-gender-ambiguous fs-4 me-3" style="color:var(--clr-gold);"></i>
                            <div><small class="text-muted">Gender</small><div class="fw-semibold"><?= e($cow['gender']) ?></div></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center p-3 rounded-3" style="background:var(--clr-primary-light);">
                            <i class="bi bi-heart-pulse-fill text-primary-custom fs-4 me-3"></i>
                            <div><small class="text-muted">Health</small><div class="fw-semibold"><?= e($cow['health_status'] ?? 'Healthy') ?></div></div>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <?php if (!empty($cow['description'])): ?>
                <h4 class="mb-2">About <?= e($cow['name']) ?></h4>
                <p><?= nl2br(e($cow['description'])) ?></p>
                <?php endif; ?>
                
                <!-- Rescue Story -->
                <?php if (!empty($cow['rescue_story'])): ?>
                <h4 class="mb-2 mt-4">Rescue Story</h4>
                <div class="p-3 rounded-3 border-start border-4" style="border-color:var(--clr-saffron) !important; background:var(--clr-saffron-light);">
                    <p class="mb-0"><?= nl2br(e($cow['rescue_story'])) ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Adoption CTA -->
                <?php if ($cow['is_adoptable'] && $cow['status'] === 'Available'): ?>
                <div class="mt-4 p-4 rounded-4" style="background: linear-gradient(135deg, var(--clr-primary-light), var(--clr-gold-light));">
                    <h4 class="mb-2"><i class="bi bi-house-heart-fill text-primary-custom me-2"></i>Adopt <?= e($cow['name']) ?></h4>
                    <p class="mb-2">Monthly adoption amount: <strong class="text-primary-custom fs-5"><?= formatCurrency($cow['monthly_adoption_amount']) ?></strong>/month</p>
                    <p class="text-muted small mb-3">Your adoption covers food, shelter, medical care, and daily maintenance.</p>
                    <a href="<?= SITE_URL ?>/adopt-a-cow.php?cow=<?= (int)$cow['id'] ?>" class="btn btn-primary-custom">
                        <i class="bi bi-heart-fill me-1"></i> Adopt <?= e($cow['name']) ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Related Cows -->
<?php if (!empty($relatedCows)): ?>
<section class="section section-warm">
    <div class="container">
        <h2 class="section-title">More <?= e($cow['breed_name'] ?? '') ?> Cows</h2>
        <div class="row g-4">
            <?php foreach ($relatedCows as $related): ?>
            <div class="col-lg-4 col-md-6 animate-on-scroll">
                <div class="cow-card">
                    <div class="cow-img-wrapper">
                        <img src="<?= getCowPhotoUrl($related['photo'], $related['name']) ?>" alt="<?= e($related['name']) ?>" loading="lazy">
                        <span class="cow-status"><span class="badge <?= getStatusBadgeClass($related['status']) ?>"><?= e($related['status']) ?></span></span>
                    </div>
                    <div class="cow-info">
                        <h3 class="cow-name"><?= e($related['name']) ?></h3>
                        <div class="cow-meta">
                            <span><i class="bi bi-tag"></i> <?= e($related['breed_name'] ?? 'Indigenous') ?></span>
                            <span><i class="bi bi-calendar"></i> <?= calculateAge($related['date_of_birth']) ?></span>
                        </div>
                        <div class="cow-actions">
                            <a href="<?= SITE_URL ?>/cow-details.php?id=<?= (int)$related['id'] ?>" class="btn btn-outline-custom btn-sm flex-fill">View Profile</a>
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
