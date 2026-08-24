<?php
/**
 * Product Details Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';

$slug = getParam('slug');
if (!$slug) { redirect(SITE_URL . '/products.php'); }

$product = dbFetchOne(
    "SELECT p.*, pc.name as category_name FROM products p 
     LEFT JOIN product_categories pc ON p.category_id = pc.id 
     WHERE p.slug = ? AND p.is_active = 1",
    [$slug]
);

if (!$product) { http_response_code(404); include BASE_PATH . '/404.php'; exit; }

$seo = [
    'title' => $product['name'],
    'description' => truncateText($product['description'] ?? '', 160),
];

$relatedProducts = dbFetchAll(
    "SELECT p.*, pc.name as category_name FROM products p 
     LEFT JOIN product_categories pc ON p.category_id = pc.id 
     WHERE p.id != ? AND p.is_active = 1 AND p.category_id = ? 
     ORDER BY RAND() LIMIT 4",
    [$product['id'], $product['category_id']]
);

$whatsappMsg = !empty($product['whatsapp_message']) ? $product['whatsapp_message'] : "🙏 Namaste, I would like to order:\n\nProduct: {$product['name']}\nPrice: ₹{$product['price']}\n\nPlease share the ordering details.";
$whatsappLink = getWhatsAppLink($whatsappMsg, $product['whatsapp_number']);

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/products.php">Products</a></li>
                <li class="breadcrumb-item active"><?= e($product['name']) ?></li>
            </ol>
        </nav>
        <h1><?= e($product['name']) ?></h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5 animate-on-scroll fade-left">
                <div class="rounded-4 overflow-hidden shadow-lg">
                    <img src="<?= getUploadUrl($product['image'] ? 'products/' . $product['image'] : '', getPlaceholderImage($product['name'], 500, 500)) ?>" 
                         alt="<?= e($product['name']) ?>" class="w-100" style="aspect-ratio:1; object-fit:cover;"
                         onerror="this.src='<?= getPlaceholderImage($product['name'], 500, 500) ?>'">
                </div>
            </div>
            <div class="col-lg-7 animate-on-scroll fade-right">
                <?php if (!empty($product['category_name'])): ?>
                <span class="badge bg-primary-light text-primary-custom mb-2"><?= e($product['category_name']) ?></span>
                <?php endif; ?>
                
                <h2><?= e($product['name']) ?></h2>
                
                <div class="d-flex align-items-center gap-3 my-3">
                    <span class="product-price fs-3"><?= formatCurrency($product['price']) ?></span>
                    <span class="badge <?= getStatusBadgeClass($product['stock_status']) ?> fs-6"><?= e($product['stock_status']) ?></span>
                </div>
                
                <?php if (!empty($product['description'])): ?>
                <div class="mb-4">
                    <p><?= nl2br(e($product['description'])) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?= e($whatsappLink) ?>" target="_blank" class="btn btn-lg" style="background:#25D366; color:white; border-radius:var(--radius-lg);">
                        <i class="bi bi-whatsapp me-2"></i> Order on WhatsApp
                    </a>
                    <a href="<?= SITE_URL ?>/contact.php" class="btn btn-outline-custom btn-lg">
                        <i class="bi bi-chat me-1"></i> Enquire Now
                    </a>
                </div>
                
                <!-- Features -->
                <div class="row g-3 mt-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-patch-check-fill text-primary-custom fs-5 me-2"></i>
                            <span class="small">100% Natural & Authentic</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-check text-primary-custom fs-5 me-2"></i>
                            <span class="small">From Indigenous Cows</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-tree text-primary-custom fs-5 me-2"></i>
                            <span class="small">Eco-Friendly & Chemical Free</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-heart-fill text-primary-custom fs-5 me-2"></i>
                            <span class="small">Supports Gau Seva</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($relatedProducts)): ?>
<section class="section section-warm">
    <div class="container">
        <h2 class="section-title">Related Products</h2>
        <div class="row g-4">
            <?php foreach ($relatedProducts as $rp): ?>
            <div class="col-lg-3 col-md-6 animate-on-scroll zoom-in">
                <div class="custom-card product-card">
                    <div class="card-img-wrapper">
                        <img src="<?= getUploadUrl($rp['image'] ? 'products/' . $rp['image'] : '', getPlaceholderImage($rp['name'], 400, 300)) ?>" 
                             alt="<?= e($rp['name']) ?>" loading="lazy"
                             onerror="this.src='<?= getPlaceholderImage($rp['name'], 400, 300) ?>'">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?= e($rp['name']) ?></h3>
                        <p class="product-price"><?= formatCurrency($rp['price']) ?></p>
                        <a href="<?= SITE_URL ?>/product-details.php?slug=<?= e($rp['slug']) ?>" class="btn btn-outline-custom btn-sm w-100 mt-auto">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include BASE_PATH . '/includes/footer.php'; ?>
