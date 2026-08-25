<?php
/**
 * Admin - Gallery Photo Edit
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/validation.php';
require_once BASE_PATH . '/includes/upload.php';

$imgId = getIntParam('id');
if (!$imgId) {
    setFlash('error', 'Invalid photo ID.');
    redirect(ADMIN_URL . '/gallery.php');
}

$photo = dbFetchOne("SELECT * FROM gallery WHERE id = ?", [$imgId]);
if (!$photo) {
    setFlash('error', 'Photo not found.');
    redirect(ADMIN_URL . '/gallery.php');
}

$categories = dbFetchAll("SELECT * FROM gallery_categories ORDER BY sort_order ASC");
$errors = [];

if (isPost()) {
    requireCsrfToken();
    
    $categoryId = getIntParam('category_id', 0, 'POST');
    $caption = getParam('caption', '', 'POST');
    $altText = getParam('alt_text', '', 'POST');
    $sortOrder = getIntParam('sort_order', 0, 'POST');
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    $filename = $photo['image_path'];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = uploadFile($_FILES['photo'], 'gallery', ['image/jpeg', 'image/png', 'image/webp'], 10 * 1024 * 1024);
        if ($uploadResult['success']) {
            // Delete old photo file
            deleteUploadedFile('gallery/' . $photo['image_path']);
            $filename = $uploadResult['filename'];
        } else {
            $errors['photo'] = $uploadResult['message'];
        }
    }

    if (empty($errors)) {
        dbUpdate('gallery', [
            'category_id' => $categoryId ?: null,
            'image_path' => $filename,
            'caption' => $caption,
            'alt_text' => $altText ?: $caption,
            'sort_order' => $sortOrder,
            'is_active' => $isActive
        ], 'id = ?', [$imgId]);

        setFlash('success', 'Gallery photo updated successfully.');
        redirect(ADMIN_URL . '/gallery.php');
    }
}

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Edit Gallery Photo</h2>
        <p class="text-muted small mb-0">Modify caption, category, or replace the photo.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/gallery.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Gallery
    </a>
</div>

<form method="POST" action="" enctype="multipart/form-data" class="admin-card p-4" style="max-width: 700px;">
    <?= csrfField() ?>
    <div class="row g-3">
        <div class="col-md-12">
            <label class="form-label fw-semibold">Current Photo</label>
            <div class="mb-2">
                <img src="<?= getUploadUrl('gallery/' . $photo['image_path']) ?>" alt="<?= e($photo['caption']) ?>" style="max-width: 250px; max-height: 180px; object-fit: cover; border-radius: 8px;">
            </div>
            <label class="form-label fw-semibold">Replace Photo (Optional)</label>
            <input type="file" name="photo" class="form-control <?= isset($errors['photo']) ? 'is-invalid' : '' ?>" accept="image/*" data-preview="uploadPreview">
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
                <option value="<?= $cat['id'] ?>" <?= $photo['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="<?= (int)$photo['sort_order'] ?>">
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Caption / Title</label>
            <input type="text" name="caption" class="form-control" value="<?= e($photo['caption']) ?>" placeholder="e.g. Gir cow with calf during morning grazing">
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Alt Text (Accessibility &amp; SEO)</label>
            <input type="text" name="alt_text" class="form-control" value="<?= e($photo['alt_text']) ?>" placeholder="Descriptive text for screen readers">
        </div>

        <div class="col-md-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= $photo['is_active'] ? 'checked' : '' ?>>
                <label class="form-check-label fw-medium" for="is_active">Publish in Gallery</label>
            </div>
        </div>

        <div class="col-md-12 pt-3">
            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
            <a href="<?= ADMIN_URL ?>/gallery.php" class="btn btn-outline-secondary ms-2">Cancel</a>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
