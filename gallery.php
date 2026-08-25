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

$sql = "
SELECT * FROM (
    SELECT 
        'gallery' as source,
        g.id,
        g.image_path,
        g.caption,
        g.alt_text,
        g.category_id,
        gc.name as category_name,
        gc.slug as category_slug,
        g.sort_order,
        g.created_at
    FROM gallery g
    LEFT JOIN gallery_categories gc ON g.category_id = gc.id
    WHERE g.is_active = 1

    UNION ALL

    SELECT 
        'cow' as source,
        c.id,
        CONCAT('../cows/', c.photo) as image_path,
        CONCAT(c.name, ' - ', COALESCE(b.name, 'Indigenous'), ' Breed') as caption,
        c.name as alt_text,
        gc.id as category_id,
        gc.name as category_name,
        gc.slug as category_slug,
        100 as sort_order,
        c.created_at
    FROM cows c
    LEFT JOIN breeds b ON c.breed_id = b.id
    CROSS JOIN (SELECT id, name, slug FROM gallery_categories WHERE slug = 'our-cows' LIMIT 1) gc
    WHERE c.photo IS NOT NULL AND c.photo != ''

    UNION ALL

    SELECT 
        'breed' as source,
        br.id,
        CONCAT('../breeds/', br.image) as image_path,
        CONCAT(br.name, ' Breed') as caption,
        br.name as alt_text,
        gc.id as category_id,
        gc.name as category_name,
        gc.slug as category_slug,
        110 as sort_order,
        br.created_at
    FROM breeds br
    CROSS JOIN (SELECT id, name, slug FROM gallery_categories WHERE slug = 'goushala' LIMIT 1) gc
    WHERE br.image IS NOT NULL AND br.image != '' AND br.is_active = 1

    UNION ALL

    SELECT 
        'event' as source,
        ev.id,
        CONCAT('../events/', ev.image) as image_path,
        ev.title as caption,
        ev.title as alt_text,
        gc.id as category_id,
        gc.name as category_name,
        gc.slug as category_slug,
        120 as sort_order,
        ev.created_at
    FROM events ev
    CROSS JOIN (SELECT id, name, slug FROM gallery_categories WHERE slug = 'events' LIMIT 1) gc
    WHERE ev.image IS NOT NULL AND ev.image != ''

    UNION ALL

    SELECT 
        'news' as source,
        nw.id,
        CONCAT('../news/', nw.featured_image) as image_path,
        nw.title as caption,
        nw.title as alt_text,
        gc.id as category_id,
        gc.name as category_name,
        gc.slug as category_slug,
        130 as sort_order,
        nw.created_at
    FROM news nw
    CROSS JOIN (SELECT id, name, slug FROM gallery_categories WHERE slug = 'goushala' LIMIT 1) gc
    WHERE nw.featured_image IS NOT NULL AND nw.featured_image != '' AND nw.status = 'Published'
) AS aggregated_gallery
";

$where = "1=1";
$params = [];
if ($activeCategory !== 'all') {
    $where .= " AND category_slug = ?";
    $params[] = $activeCategory;
}

$sql .= " WHERE {$where} ORDER BY sort_order ASC, created_at DESC";

$images = dbFetchAll($sql, $params);

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
