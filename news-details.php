<?php
/**
 * News Details Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';

$slug = getParam('slug');
if (!$slug) { redirect(SITE_URL . '/news.php'); }

$article = dbFetchOne(
    "SELECT n.*, nc.name as category_name FROM news n 
     LEFT JOIN news_categories nc ON n.category_id = nc.id 
     WHERE n.slug = ? AND n.status = 'Published'",
    [$slug]
);

if (!$article) { http_response_code(404); include BASE_PATH . '/404.php'; exit; }

$seo = [
    'title' => $article['seo_title'] ?: $article['title'],
    'description' => $article['seo_description'] ?: truncateText(strip_tags($article['short_description'] ?? $article['content'] ?? ''), 160),
    'type' => 'article'
];

$relatedArticles = dbFetchAll(
    "SELECT n.*, nc.name as category_name FROM news n 
     LEFT JOIN news_categories nc ON n.category_id = nc.id 
     WHERE n.id != ? AND n.status = 'Published' ORDER BY n.published_date DESC LIMIT 3",
    [$article['id']]
);

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/news.php">News</a></li>
                <li class="breadcrumb-item active"><?= e(truncateText($article['title'], 40)) ?></li>
            </ol>
        </nav>
        <h1><?= e($article['title']) ?></h1>
        <p>
            <i class="bi bi-calendar3 me-1"></i> <?= formatDate($article['published_date']) ?>
            <?php if (!empty($article['category_name'])): ?>
            · <span class="badge bg-light text-dark"><?= e($article['category_name']) ?></span>
            <?php endif; ?>
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <!-- Featured Image -->
                <?php if (!empty($article['featured_image'])): ?>
                <div class="rounded-4 overflow-hidden shadow-sm mb-4">
                    <img src="<?= getUploadUrl('news/' . $article['featured_image']) ?>" 
                         alt="<?= e($article['title']) ?>" class="w-100" style="max-height:450px; object-fit:cover;"
                         onerror="this.src='<?= getPlaceholderImage($article['title'], 800, 400) ?>'">
                </div>
                <?php endif; ?>
                
                <!-- Content -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="content-body" style="line-height:1.8; font-size:1.02rem;">
                        <?= $article['content'] ?? '' ?>
                    </div>
                    
                    <!-- Tags -->
                    <?php if (!empty($article['tags'])): ?>
                    <div class="mt-4 pt-3 border-top">
                        <i class="bi bi-tags me-1"></i>
                        <?php foreach (explode(',', $article['tags']) as $tag): ?>
                        <span class="badge bg-primary-light text-primary-custom me-1"><?= e(trim($tag)) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Share -->
                <div class="card border-0 shadow-sm rounded-4 p-3 mt-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-semibold small">Share:</span>
                        <a href="https://wa.me/?text=<?= urlencode($article['title'] . ' - ' . getCurrentUrl()) ?>" target="_blank" class="social-link" style="width:36px;height:36px;font-size:0.95rem;"><i class="bi bi-whatsapp"></i></a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(getCurrentUrl()) ?>" target="_blank" class="social-link" style="width:36px;height:36px;font-size:0.95rem;"><i class="bi bi-facebook"></i></a>
                        <a href="https://twitter.com/intent/tweet?text=<?= urlencode($article['title']) ?>&url=<?= urlencode(getCurrentUrl()) ?>" target="_blank" class="social-link" style="width:36px;height:36px;font-size:0.95rem;"><i class="bi bi-twitter-x"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="mb-3">About the Author</h5>
                    <div class="d-flex align-items-center gap-3">
                        <div class="testimonial-avatar"><?= strtoupper(mb_substr($article['author'] ?? 'K', 0, 1)) ?></div>
                        <div>
                            <div class="fw-semibold"><?= e($article['author'] ?? 'Kamadenu Goushala') ?></div>
                            <small class="text-muted"><?= formatDate($article['published_date']) ?></small>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 rounded-4 p-4 mb-4 text-center" style="background: linear-gradient(135deg, var(--clr-primary-light), var(--clr-gold-light));">
                    <h5>Support Gau Seva</h5>
                    <p class="small text-muted mb-3">Your donations help us continue our mission of cow protection.</p>
                    <a href="<?= SITE_URL ?>/donate.php" class="btn btn-donate btn-sm"><i class="bi bi-heart-fill me-1"></i> Donate Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($relatedArticles)): ?>
<section class="section section-warm">
    <div class="container">
        <h2 class="section-title">More News</h2>
        <div class="row g-4">
            <?php foreach ($relatedArticles as $ra): ?>
            <div class="col-lg-4 col-md-6 animate-on-scroll zoom-in">
                <div class="custom-card">
                    <div class="card-img-wrapper">
                        <img src="<?= getUploadUrl($ra['featured_image'] ? 'news/' . $ra['featured_image'] : '', getPlaceholderImage($ra['title'], 400, 250)) ?>" 
                             alt="<?= e($ra['title']) ?>" loading="lazy"
                             onerror="this.src='<?= getPlaceholderImage($ra['title'], 400, 250) ?>'">
                    </div>
                    <div class="card-body">
                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?= formatDate($ra['published_date']) ?></small>
                        <h3 class="card-title mt-1"><?= e($ra['title']) ?></h3>
                        <a href="<?= SITE_URL ?>/news-details.php?slug=<?= e($ra['slug']) ?>" class="btn btn-outline-custom btn-sm mt-auto">Read More</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include BASE_PATH . '/includes/footer.php'; ?>
