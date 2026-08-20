<?php
/**
 * Admin - Products Management
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';

$pageTitle = 'Manage Products';

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $prodId = getIntParam('id');
    if ($prodId) {
        $prod = dbFetchOne("SELECT image FROM products WHERE id = ?", [$prodId]);
        if ($prod && $prod['image']) {
            deleteUploadedFile('products/' . $prod['image']);
        }
        dbDelete('products', 'id = ?', [$prodId]);
        setFlash('success', 'Product deleted.');
        redirect(ADMIN_URL . '/products.php');
    }
}

$products = dbFetchAll(
    "SELECT p.*, pc.name as category_name 
     FROM products p LEFT JOIN product_categories pc ON p.category_id = pc.id 
     ORDER BY p.created_at DESC"
);

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Goushala Products</h2>
        <p class="text-muted small mb-0">Organic desi cow ghee, dhoop, manure, and items.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/product-form.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Add New Product
    </a>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr><td colspan="7" class="text-center py-4 text-muted">No products cataloged yet.</td></tr>
                <?php else: foreach ($products as $p): ?>
                <tr>
                    <td style="width: 60px;">
                        <img src="<?= getUploadUrl($p['image'] ? 'products/' . $p['image'] : '', getPlaceholderImage($p['name'], 50, 50)) ?>" 
                             alt="<?= e($p['name']) ?>" class="rounded-3" style="width: 48px; height: 48px; object-fit: cover;"
                             onerror="this.src='<?= getPlaceholderImage($p['name'], 50, 50) ?>'">
                    </td>
                    <td>
                        <div class="fw-bold text-dark"><?= e($p['name']) ?></div>
                        <?php if ($p['is_featured']): ?>
                        <span class="badge bg-warning-subtle text-warning small">Featured</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($p['category_name'] ?? 'General') ?></td>
                    <td class="fw-bold text-success"><?= formatCurrency((float)$p['price']) ?></td>
                    <td><span class="badge <?= getStatusBadgeClass($p['stock_status']) ?>"><?= e($p['stock_status']) ?></span></td>
                    <td>
                        <?php if ($p['is_active']): ?>
                        <span class="badge bg-success-subtle text-success">Active</span>
                        <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary">Hidden</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <a href="<?= SITE_URL ?>/product-details.php?slug=<?= e($p['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-info me-1"><i class="bi bi-eye"></i></a>
                        <a href="<?= ADMIN_URL ?>/product-form.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        <a href="<?= ADMIN_URL ?>/products.php?action=delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm-delete="Delete product '<?= e($p['name']) ?>'?"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
