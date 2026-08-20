<?php
/**
 * Admin - Gallery Photo Upload
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/validation.php';
require_once BASE_PATH . '/includes/upload.php';

$pageTitle = 'Upload Photo to Gallery';
$categories = dbFetchAll("SELECT * FROM gallery_categories ORDER BY sort_order ASC");

$errors = [];

if (isPost()) {
    requireCsrfToken();
    
    $categoryId = getIntParam('category_id', 'POST');
    $caption = getParam('caption', '', 'POST');
    $altText = getParam('alt_text', '', 'POST');
    $sortOrder = getIntParam('sort_order', 'POST');
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $errors['photo'] = 'Please select a valid image file.';
    } else {
        $uploadResult = uploadFile($_FILES['photo'], 'gallery', ['image/jpeg', 'image/png', 'image/webp'], 10 * 1024 * 1024);
        if ($uploadResult['success']) {
            dbInsert('gallery', [
                'category_id' => $categoryId ?: null,
                'image_path' => $uploadResult['filename'],
                'caption' => $caption,
                'alt_text' => $altText ?: $caption,
                'sort_order' => $sortOrder,
                'is_active' => $isActive
            ]);
            setFlash('success', 'Photo added to gallery successfully.');
            redirect(ADMIN_URL . '/gallery.php');
        } else {
            $errors['photo'] = $uploadResult['message'];
        }
    }
}

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Upload Photo</h2>
        <p class="text-muted small mb-0">Add a new image to the Goushala album.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/gallery.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Gallery
    </a>
</div>

<form method="POST" action="" enctype="multipart/form-data" class="admin-card p-4" style="max-width: 700px;">
    <?= csrfField() ?>
    <div class="row g-3">
        <div class="col-md-12">
            <label class="form-label fw-semibold">Select Photo <span class="text-danger">*</span></label>
            <input type="file" name="photo" class="form-control <?= isset($errors['photo']) ? 'is-invalid' : '' ?>" accept="image/*" required data-preview="uploadPreview">
            <?php if (isset($errors['photo'])): ?><div class="invalid-feedback"><?= e($errors['photo']) ?></div><?php endif; ?>
            <div class="mt-2">
                <img id="uploadPreview" src="" style="max-width: 250px; max-height: 180px; object-fit: cover; border-radius: 8px; display: none;">
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Category</label>
            <select name="category_id" class="form-select">
                <option value="">General</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="0">
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Caption / Title</label>
            <input type="text" name="caption" class="form-control" placeholder="e.g. Gir cow with calf during morning grazing">
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Alt Text (Accessibility &amp; SEO)</label>
            <input type="text" name="alt_text" class="form-control" placeholder="Descriptive text for screen readers">
        </div>

        <div class="col-md-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                <label class="form-check-label fw-medium" for="is_active">Publish in Gallery immediately</label>
            </div>
        </div>

        <div class="col-12 text-end pt-3 border-top">
            <a href="<?= ADMIN_URL ?>/gallery.php" class="btn btn-light me-2">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-upload me-1"></i> Upload Photo
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
