<?php
/**
 * Gallery Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';

$seo = [
    'title' => 'Photo Gallery',
    'description' => 'Browse photos from Kamadhenu Goushala - our cows, seva activities, events, festivals, and daily life at the Goushala.',
];

$categories = dbFetchAll("SELECT * FROM gallery_categories WHERE is_active = 1 ORDER BY sort_order ASC");
$activeCategory = getParam('category', 'all');

$where = "g.is_active = 1";
$params = [];
if ($activeCategory !== 'all') {
    $where .= " AND gc.slug = ?";
    $params[] = $activeCategory;
}

$images = dbFetchAll(
    "SELECT g.*, gc.name as category_name, gc.slug as category_slug FROM gallery g 
     LEFT JOIN gallery_categories gc ON g.category_id = gc.id 
     WHERE {$where} ORDER BY g.sort_order ASC, g.created_at DESC",
    $params
);

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active">Gallery</li>
            </ol>
        </nav>
        <h1>Photo Gallery</h1>
        <p>Glimpses of daily life, care, and devotion at Kamadhenu Goushala.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <!-- Category Filter -->
        <div class="gallery-filter">
            <a href="<?= SITE_URL ?>/gallery.php" class="filter-btn <?= $activeCategory === 'all' ? 'active' : '' ?>">All</a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?= SITE_URL ?>/gallery.php?category=<?= e($cat['slug']) ?>" 
               class="filter-btn <?= $activeCategory === $cat['slug'] ? 'active' : '' ?>"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($images)): ?>
        <div class="text-center py-5">
            <i class="bi bi-images text-muted" style="font-size: 3rem;"></i>
            <h3 class="mt-3">No Photos Yet</h3>
            <p class="text-muted">Gallery photos will be added soon. Check back later!</p>
        </div>
        <?php else: ?>
        <div class="gallery-grid">
            <?php foreach ($images as $img): ?>
            <div class="gallery-item animate-on-scroll" 
                 data-gallery="<?= getUploadUrl('gallery/' . $img['image_path']) ?>" 
                 data-caption="<?= e($img['caption'] ?? '') ?>"
                 data-filter-category="<?= e($img['category_slug'] ?? '') ?>">
                <img src="<?= getUploadUrl('gallery/' . $img['image_path'], getPlaceholderImage($img['caption'] ?? 'Gallery', 300, 300)) ?>" 
                     alt="<?= e($img['alt_text'] ?? $img['caption'] ?? 'Goushala gallery') ?>" loading="lazy"
                     onerror="this.src='<?= getPlaceholderImage($img['caption'] ?? 'Gallery', 300, 300) ?>'">
                <div class="gallery-overlay">
                    <i class="bi bi-zoom-in"></i>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
