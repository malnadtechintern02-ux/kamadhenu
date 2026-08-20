<?php
/**
 * Admin - Seva Add/Edit Form
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/validation.php';
require_once BASE_PATH . '/includes/upload.php';

$sevaId = getIntParam('id');
$isEdit = !empty($sevaId);
$pageTitle = $isEdit ? 'Edit Seva Program' : 'Add New Seva Program';

$seva = [
    'title' => '',
    'slug' => '',
    'icon' => 'bi-heart-fill',
    'image' => '',
    'short_description' => '',
    'description' => '',
    'suggested_amounts' => '501,1001,2501,5001,11000',
    'benefits' => '',
    'sort_order' => 0,
    'is_active' => 1
];

if ($isEdit) {
    $existing = dbFetchOne("SELECT * FROM seva_categories WHERE id = ?", [$sevaId]);
    if (!$existing) {
        setFlash('error', 'Seva program not found.');
        redirect(ADMIN_URL . '/seva.php');
    }
    $seva = array_merge($seva, $existing);
}

$errors = [];

if (isPost()) {
    requireCsrfToken();
    
    $seva['title'] = getParam('title', '', 'POST');
    $seva['slug'] = getParam('slug', '', 'POST') ?: slugify($seva['title']);
    $seva['icon'] = getParam('icon', 'bi-heart-fill', 'POST');
    $seva['short_description'] = getParam('short_description', '', 'POST');
    $seva['description'] = getParam('description', '', 'POST');
    $seva['suggested_amounts'] = getParam('suggested_amounts', '', 'POST');
    $seva['benefits'] = getParam('benefits', '', 'POST');
    $seva['sort_order'] = getIntParam('sort_order', 0, 'POST');
    $seva['is_active'] = isset($_POST['is_active']) ? 1 : 0;

    $validator = new Validator($seva);
    $validator->required('title', 'Seva Title')
              ->required('slug', 'Slug');

    if ($validator->passes()) {
        // Image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['image'], 'seva', ['image/jpeg', 'image/png', 'image/webp'], 5 * 1024 * 1024);
            if ($uploadResult['success']) {
                if ($isEdit && !empty($seva['image'])) {
                    deleteUploadedFile('seva/' . $seva['image']);
                }
                $seva['image'] = $uploadResult['filename'];
            } else {
                $errors['image'] = $uploadResult['message'];
            }
        }

        if (empty($errors)) {
            $data = [
                'title' => $seva['title'],
                'slug' => $seva['slug'],
                'icon' => $seva['icon'],
                'image' => $seva['image'],
                'short_description' => $seva['short_description'],
                'description' => $seva['description'],
                'suggested_amounts' => $seva['suggested_amounts'],
                'benefits' => $seva['benefits'],
                'sort_order' => $seva['sort_order'],
                'is_active' => $seva['is_active']
            ];

            if ($isEdit) {
                dbUpdate('seva_categories', $data, 'id = ?', [$sevaId]);
                setFlash('success', 'Seva program updated.');
            } else {
                dbInsert('seva_categories', $data);
                setFlash('success', 'New seva program added.');
            }
            redirect(ADMIN_URL . '/seva.php');
        }
    } else {
        $errors = array_merge($errors, $validator->getErrors());
    }
}

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold"><?= $pageTitle ?></h2>
        <p class="text-muted small mb-0">Configure seva offerings and amounts.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/seva.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Seva List
    </a>
</div>

<form method="POST" action="" enctype="multipart/form-data" class="admin-card p-4">
    <?= csrfField() ?>
    <div class="row g-4">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" data-slug-source class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= e($seva['title']) ?>" required>
            <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?= e($errors['title']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
            <input type="text" name="slug" data-slug-target class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" value="<?= e($seva['slug']) ?>" required>
            <?php if (isset($errors['slug'])): ?><div class="invalid-feedback"><?= e($errors['slug']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Bootstrap Icon Class</label>
            <input type="text" name="icon" class="form-control" value="<?= e($seva['icon']) ?>" placeholder="e.g. bi-heart-fill, bi-flower1">
        </div>

        <div class="col-md-5">
            <label class="form-label fw-semibold">Suggested Donation Amounts (Comma Separated)</label>
            <input type="text" name="suggested_amounts" class="form-control" value="<?= e($seva['suggested_amounts']) ?>" placeholder="501,1001,2501,5001,11000">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="<?= e($seva['sort_order']) ?>">
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Cover Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Short Description</label>
            <textarea name="short_description" rows="2" class="form-control"><?= e($seva['short_description']) ?></textarea>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Detailed Description</label>
            <textarea name="description" rows="4" class="form-control"><?= e($seva['description']) ?></textarea>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Spiritual Benefits / Significance</label>
            <textarea name="benefits" rows="2" class="form-control"><?= e($seva['benefits']) ?></textarea>
        </div>

        <div class="col-md-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= $seva['is_active'] ? 'checked' : '' ?>>
                <label class="form-check-label fw-medium" for="is_active">Active &amp; Visible to Donors</label>
            </div>
        </div>

        <div class="col-12 text-end pt-3 border-top">
            <a href="<?= ADMIN_URL ?>/seva.php" class="btn btn-light me-2">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check-circle me-1"></i> Save Seva Program
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
