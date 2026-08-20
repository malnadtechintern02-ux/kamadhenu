<?php
/**
 * Products Listing Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/pagination.php';

$seo = [
    'title' => 'Products',
    'description' => 'Authentic cow-based products from Kamadhenu Goushala - pure desi ghee, Panchagavya items, cow dung products, and organic goods.',
];

$categoryFilter = getParam('category');
$categories = dbFetchAll("SELECT * FROM product_categories WHERE is_active = 1 ORDER BY name ASC");

$where = "p.is_active = 1";
$params = [];
if ($categoryFilter) {
    $where .= " AND pc.slug = ?";
    $params[] = $categoryFilter;
}

$totalItems = (int)dbFetchOne("SELECT COUNT(*) as count FROM products p LEFT JOIN product_categories pc ON p.category_id = pc.id WHERE {$where}", $params)['count'];
$page = getCurrentPage();
$pagination = buildPagination($totalItems, ITEMS_PER_PAGE, $page);

$products = dbFetchAll(
    "SELECT p.*, pc.name as category_name FROM products p 
     LEFT JOIN product_categories pc ON p.category_id = pc.id 
     WHERE {$where} ORDER BY p.is_featured DESC, p.created_at DESC 
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
                <li class="breadcrumb-item active">Products</li>
            </ol>
        </nav>
        <h1>Goushala Products</h1>
        <p>Authentic, natural products made from indigenous cow derivatives.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <!-- Category Filter -->
        <?php if (!empty($categories)): ?>
        <div class="gallery-filter mb-4">
            <a href="<?= SITE_URL ?>/products.php" class="filter-btn <?= !$categoryFilter ? 'active' : '' ?>">All</a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?= SITE_URL ?>/products.php?category=<?= e($cat['slug']) ?>" class="filter-btn <?= $categoryFilter === $cat['slug'] ? 'active' : '' ?>"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (empty($products)): ?>
        <div class="text-center py-5">
            <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
            <h3 class="mt-3">No Products Found</h3>
            <p class="text-muted">Products will be listed soon. Check back later!</p>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
            <div class="col-lg-3 col-md-6 animate-on-scroll">
                <div class="custom-card product-card">
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
                            <a href="<?= e(getWhatsAppLink('🙏 Namaste, I am interested in: ' . $product['name'] . ' (₹' . $product['price'] . '). Please share details.')) ?>" 
                               target="_blank" class="btn btn-sm flex-shrink-0" style="background:#25D366; color:white; border-radius:var(--radius-md);">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </div>
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
