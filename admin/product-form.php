<?php
/**
 * Admin - Product Add/Edit Form
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/validation.php';
require_once BASE_PATH . '/includes/upload.php';

$prodId = getIntParam('id');
$isEdit = !empty($prodId);
$pageTitle = $isEdit ? 'Edit Product' : 'Add New Product';

$product = [
    'name' => '',
    'slug' => '',
    'category_id' => '',
    'price' => '',
    'image' => '',
    'description' => '',
    'stock_status' => 'In Stock',
    'is_featured' => 0,
    'is_active' => 1
];

if ($isEdit) {
    $existing = dbFetchOne("SELECT * FROM products WHERE id = ?", [$prodId]);
    if (!$existing) {
        setFlash('error', 'Product not found.');
        redirect(ADMIN_URL . '/products.php');
    }
    $product = array_merge($product, $existing);
}

$errors = [];

if (isPost()) {
    requireCsrfToken();
    
    $product['name'] = getParam('name', '', 'POST');
    $product['slug'] = getParam('slug', '', 'POST') ?: slugify($product['name']);
    $product['category_id'] = getIntParam('category_id', 0, 'POST');
    $product['price'] = (float)getParam('price', 0, 'POST');
    $product['description'] = getParam('description', '', 'POST');
    $product['stock_status'] = getParam('stock_status', 'In Stock', 'POST');
    $product['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
    $product['is_active'] = isset($_POST['is_active']) ? 1 : 0;

    $validator = new Validator($product);
    $validator->required('name', 'Product Name')
              ->required('slug', 'Slug')
              ->required('price', 'Price');

    if ($validator->passes()) {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['image'], 'products', ['image/jpeg', 'image/png', 'image/webp'], 5 * 1024 * 1024);
            if ($uploadResult['success']) {
                if ($isEdit && !empty($product['image'])) {
                    deleteUploadedFile('products/' . $product['image']);
                }
                $product['image'] = $uploadResult['filename'];
            } else {
                $errors['image'] = $uploadResult['message'];
            }
        }

        if (empty($errors)) {
            $data = [
                'name' => $product['name'],
                'slug' => $product['slug'],
                'category_id' => $product['category_id'] ?: null,
                'price' => $product['price'],
                'image' => $product['image'],
                'description' => $product['description'],
                'stock_status' => $product['stock_status'],
                'is_featured' => $product['is_featured'],
                'is_active' => $product['is_active']
            ];

            if ($isEdit) {
                dbUpdate('products', $data, 'id = ?', [$prodId]);
                setFlash('success', 'Product updated successfully.');
            } else {
                dbInsert('products', $data);
                setFlash('success', 'New product added successfully.');
            }
            redirect(ADMIN_URL . '/products.php');
        }
    } else {
        $errors = array_merge($errors, $validator->getErrors());
    }
}

$categories = dbFetchAll("SELECT id, name FROM product_categories ORDER BY name ASC");

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold"><?= $pageTitle ?></h2>
        <p class="text-muted small mb-0">Manage product catalog, pricing, and stock.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/products.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Products
    </a>
</div>

<form method="POST" action="" enctype="multipart/form-data" class="admin-card p-4">
    <?= csrfField() ?>
    <div class="row g-4">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name" data-slug-source class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= e($product['name']) ?>" required>
            <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= e($errors['name']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
            <input type="text" name="slug" data-slug-target class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" value="<?= e($product['slug']) ?>" required>
            <?php if (isset($errors['slug'])): ?><div class="invalid-feedback"><?= e($errors['slug']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Category</label>
            <select name="category_id" class="form-select">
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Price (₹) <span class="text-danger">*</span></label>
            <input type="number" step="1" name="price" class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" value="<?= e($product['price']) ?>" required>
            <?php if (isset($errors['price'])): ?><div class="invalid-feedback"><?= e($errors['price']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Stock Status</label>
            <select name="stock_status" class="form-select">
                <option value="In Stock" <?= $product['stock_status'] === 'In Stock' ? 'selected' : '' ?>>In Stock</option>
                <option value="Out of Stock" <?= $product['stock_status'] === 'Out of Stock' ? 'selected' : '' ?>>Out of Stock</option>
                <option value="Made to Order" <?= $product['stock_status'] === 'Made to Order' ? 'selected' : '' ?>>Made to Order</option>
            </select>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Product Image</label>
            <input type="file" name="image" class="form-control" accept="image/*" data-preview="prodImgPreview">
            <div class="mt-2">
                <img id="prodImgPreview" src="<?= $product['image'] ? getUploadUrl('products/' . $product['image']) : '' ?>" 
                     style="max-width: 150px; max-height: 150px; object-fit: cover; border-radius: 8px; display: <?= $product['image'] ? 'block' : 'none' ?>;">
            </div>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Product Description &amp; Benefits</label>
            <textarea name="description" rows="5" class="form-control"><?= e($product['description']) ?></textarea>
        </div>

        <div class="col-md-12">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?>>
                <label class="form-check-label fw-medium" for="is_featured">Feature on Homepage</label>
            </div>
            <div class="form-check form-check-inline ms-3">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= $product['is_active'] ? 'checked' : '' ?>>
                <label class="form-check-label fw-medium" for="is_active">Active &amp; Available in Store</label>
            </div>
        </div>

        <div class="col-12 text-end pt-3 border-top">
            <a href="<?= ADMIN_URL ?>/products.php" class="btn btn-light me-2">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check-circle me-1"></i> Save Product
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
