<?php
/**
 * News/Blog Listing Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/pagination.php';

$seo = [
    'title' => 'News & Updates',
    'description' => 'Latest news, updates, and stories from Kamadhenu Goushala. Stay informed about Gau Seva activities, rescue missions, and community outreach.',
];

$categoryFilter = getParam('category');
$categories = dbFetchAll("SELECT * FROM news_categories WHERE is_active = 1 ORDER BY name ASC");

$where = "n.status = 'Published'";
$params = [];
if ($categoryFilter) {
    $where .= " AND nc.slug = ?";
    $params[] = $categoryFilter;
}

$totalItems = (int)dbFetchOne("SELECT COUNT(*) as count FROM news n LEFT JOIN news_categories nc ON n.category_id = nc.id WHERE {$where}", $params)['count'];
$page = getCurrentPage();
$pagination = buildPagination($totalItems, ITEMS_PER_PAGE, $page);

$articles = dbFetchAll(
    "SELECT n.*, nc.name as category_name FROM news n 
     LEFT JOIN news_categories nc ON n.category_id = nc.id 
     WHERE {$where} ORDER BY n.published_date DESC 
     LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
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
                <li class="breadcrumb-item active">News</li>
            </ol>
        </nav>
        <h1>News & Updates</h1>
        <p>Stay informed about our Gau Seva activities and community impact.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <!-- Category Filter -->
        <?php if (!empty($categories)): ?>
        <div class="gallery-filter mb-4">
            <a href="<?= SITE_URL ?>/news.php" class="filter-btn <?= !$categoryFilter ? 'active' : '' ?>">All</a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?= SITE_URL ?>/news.php?category=<?= e($cat['slug']) ?>" class="filter-btn <?= $categoryFilter === $cat['slug'] ? 'active' : '' ?>"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (empty($articles)): ?>
        <div class="text-center py-5">
            <i class="bi bi-newspaper text-muted" style="font-size: 3rem;"></i>
            <h3 class="mt-3">No Articles Found</h3>
            <p class="text-muted">Check back later for news and updates.</p>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($articles as $article): ?>
            <div class="col-lg-4 col-md-6 animate-on-scroll">
                <div class="custom-card">
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
        
        <div class="mt-5"><?= renderPagination($pagination) ?></div>
        <?php endif; ?>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
