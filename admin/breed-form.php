<?php
/**
 * Admin - Breed Add/Edit Form
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/validation.php';
require_once BASE_PATH . '/includes/upload.php';

$breedId = getIntParam('id');
$isEdit = !empty($breedId);
$pageTitle = $isEdit ? 'Edit Breed Profile' : 'Add New Breed';

$breed = [
    'name' => '',
    'slug' => '',
    'origin' => '',
    'image' => '',
    'description' => '',
    'milk_quality' => '',
    'characteristics' => '',
    'sort_order' => 0,
    'is_active' => 1
];

if ($isEdit) {
    $existing = dbFetchOne("SELECT * FROM breeds WHERE id = ?", [$breedId]);
    if (!$existing) {
        setFlash('error', 'Breed profile not found.');
        redirect(ADMIN_URL . '/breeds.php');
    }
    $breed = array_merge($breed, $existing);
}

$errors = [];

if (isPost()) {
    requireCsrfToken();
    
    $breed['name'] = getParam('name', '', 'POST');
    $breed['slug'] = getParam('slug', '', 'POST') ?: slugify($breed['name']);
    $breed['origin'] = getParam('origin', '', 'POST');
    $breed['description'] = getParam('description', '', 'POST');
    $breed['milk_quality'] = getParam('milk_quality', '', 'POST');
    $breed['characteristics'] = getParam('characteristics', '', 'POST');
    $breed['sort_order'] = getIntParam('sort_order', 'POST');
    $breed['is_active'] = isset($_POST['is_active']) ? 1 : 0;

    $validator = new Validator($breed);
    $validator->required('name', 'Breed Name')
              ->required('slug', 'Slug');

    // Unique slug check
    $slugWhere = "slug = ?" . ($isEdit ? " AND id != {$breedId}" : "");
    if (dbCount('breeds', $slugWhere, [$breed['slug']]) > 0) {
        $errors['slug'] = 'This slug is already in use by another breed.';
    }

    if ($validator->passes() && empty($errors)) {
        // Image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['image'], 'breeds', ['image/jpeg', 'image/png', 'image/webp'], 5 * 1024 * 1024);
            if ($uploadResult['success']) {
                if ($isEdit && !empty($breed['image'])) {
                    deleteUploadedFile('breeds/' . $breed['image']);
                }
                $breed['image'] = $uploadResult['filename'];
            } else {
                $errors['image'] = $uploadResult['message'];
            }
        }

        if (empty($errors)) {
            $data = [
                'name' => $breed['name'],
                'slug' => $breed['slug'],
                'origin' => $breed['origin'],
                'image' => $breed['image'],
                'description' => $breed['description'],
                'milk_quality' => $breed['milk_quality'],
                'characteristics' => $breed['characteristics'],
                'sort_order' => $breed['sort_order'],
                'is_active' => $breed['is_active']
            ];

            if ($isEdit) {
                dbUpdate('breeds', $data, 'id = ?', [$breedId]);
                setFlash('success', 'Breed profile updated successfully.');
            } else {
                dbInsert('breeds', $data);
                setFlash('success', 'New breed added successfully.');
            }
            redirect(ADMIN_URL . '/breeds.php');
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
        <p class="text-muted small mb-0">Specify origin, milk properties, and characteristics.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/breeds.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Breeds
    </a>
</div>

<form method="POST" action="" enctype="multipart/form-data" class="admin-card p-4">
    <?= csrfField() ?>
    <div class="row g-4">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Breed Name <span class="text-danger">*</span></label>
            <input type="text" name="name" data-slug-source class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= e($breed['name']) ?>" required>
            <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= e($errors['name']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
            <input type="text" name="slug" data-slug-target class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" value="<?= e($breed['slug']) ?>" required>
            <?php if (isset($errors['slug'])): ?><div class="invalid-feedback"><?= e($errors['slug']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Native Origin Region</label>
            <input type="text" name="origin" class="form-control" value="<?= e($breed['origin']) ?>" placeholder="e.g., Gujarat, Karnataka, Rajasthan">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="<?= e($breed['sort_order']) ?>">
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= $breed['is_active'] ? 'checked' : '' ?>>
                <label class="form-check-label fw-medium" for="is_active">Active &amp; Visible</label>
            </div>
        </div>

        <!-- Breed Image -->
        <div class="col-md-12">
            <label class="form-label fw-semibold">Breed Banner / Image</label>
            <input type="file" name="image" class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>" accept="image/*" data-preview="breedImgPreview">
            <?php if (isset($errors['image'])): ?><div class="invalid-feedback"><?= e($errors['image']) ?></div><?php endif; ?>
            <div class="mt-2">
                <img id="breedImgPreview" src="<?= $breed['image'] ? getUploadUrl('breeds/' . $breed['image']) : '' ?>" 
                     style="max-width: 200px; max-height: 140px; object-fit: cover; border-radius: 8px; display: <?= $breed['image'] ? 'block' : 'none' ?>;">
            </div>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">General Description</label>
            <textarea name="description" rows="4" class="form-control"><?= e($breed['description']) ?></textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Milk Quality &amp; Medicinal Properties</label>
            <textarea name="milk_quality" rows="3" class="form-control" placeholder="Describe A2 beta-casein content, daily yield, nutritional attributes..."><?= e($breed['milk_quality']) ?></textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Physical &amp; Temperamental Characteristics</label>
            <textarea name="characteristics" rows="3" class="form-control" placeholder="Horns, hump, color variations, endurance, climate adaptation..."><?= e($breed['characteristics']) ?></textarea>
        </div>

        <div class="col-12 text-end pt-3 border-top">
            <a href="<?= ADMIN_URL ?>/breeds.php" class="btn btn-light me-2">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check-circle me-1"></i> Save Breed
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
