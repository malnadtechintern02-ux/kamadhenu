<?php
/**
 * Admin - Testimonial Add/Edit Form
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/validation.php';

$tId = getIntParam('id');
$isEdit = !empty($tId);
$pageTitle = $isEdit ? 'Edit Testimonial' : 'Add Testimonial';

$testimonial = [
    'name' => '',
    'designation' => '',
    'location' => '',
    'message' => '',
    'rating' => 5,
    'sort_order' => 0,
    'is_active' => 1
];

if ($isEdit) {
    $existing = dbFetchOne("SELECT * FROM testimonials WHERE id = ?", [$tId]);
    if (!$existing) {
        setFlash('error', 'Testimonial not found.');
        redirect(ADMIN_URL . '/testimonials.php');
    }
    $testimonial = array_merge($testimonial, $existing);
}

$errors = [];

if (isPost()) {
    requireCsrfToken();
    
    $testimonial['name'] = getParam('name', '', 'POST');
    $testimonial['designation'] = getParam('designation', '', 'POST');
    $testimonial['location'] = getParam('location', '', 'POST');
    $testimonial['message'] = getParam('message', '', 'POST');
    $testimonial['rating'] = getIntParam('rating', 0, 'POST') ?: 5;
    $testimonial['sort_order'] = getIntParam('sort_order', 0, 'POST');
    $testimonial['is_active'] = isset($_POST['is_active']) ? 1 : 0;

    $validator = new Validator($testimonial);
    $validator->required('name', 'Name')
              ->required('message', 'Testimonial Message');

    if ($validator->passes()) {
        $data = [
            'name' => $testimonial['name'],
            'designation' => $testimonial['designation'],
            'location' => $testimonial['location'],
            'message' => $testimonial['message'],
            'rating' => $testimonial['rating'],
            'sort_order' => $testimonial['sort_order'],
            'is_active' => $testimonial['is_active']
        ];

        if ($isEdit) {
            dbUpdate('testimonials', $data, 'id = ?', [$tId]);
            setFlash('success', 'Testimonial updated.');
        } else {
            dbInsert('testimonials', $data);
            setFlash('success', 'New testimonial added.');
        }
        redirect(ADMIN_URL . '/testimonials.php');
    } else {
        $errors = $validator->getErrors();
    }
}

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold"><?= $pageTitle ?></h2>
        <p class="text-muted small mb-0">Record supporter feedback and endorsements.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/testimonials.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
</div>

<form method="POST" action="" class="admin-card p-4" style="max-width: 700px;">
    <?= csrfField() ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= e($testimonial['name']) ?>" required>
            <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= e($errors['name']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Location</label>
            <input type="text" name="location" class="form-control" value="<?= e($testimonial['location']) ?>" placeholder="e.g. Bangalore, Karnataka">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Designation / Role (Optional)</label>
            <input type="text" name="designation" class="form-control" value="<?= e($testimonial['designation']) ?>" placeholder="e.g. Gau Bhakt, Volunteer">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Rating (1-5)</label>
            <select name="rating" class="form-select">
                <option value="5" <?= $testimonial['rating'] == 5 ? 'selected' : '' ?>>5 Stars</option>
                <option value="4" <?= $testimonial['rating'] == 4 ? 'selected' : '' ?>>4 Stars</option>
                <option value="3" <?= $testimonial['rating'] == 3 ? 'selected' : '' ?>>3 Stars</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="<?= e($testimonial['sort_order']) ?>">
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Testimonial Quote <span class="text-danger">*</span></label>
            <textarea name="message" rows="4" class="form-control <?= isset($errors['message']) ? 'is-invalid' : '' ?>" required><?= e($testimonial['message']) ?></textarea>
            <?php if (isset($errors['message'])): ?><div class="invalid-feedback"><?= e($errors['message']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= $testimonial['is_active'] ? 'checked' : '' ?>>
                <label class="form-check-label fw-medium" for="is_active">Show on Website Homepage</label>
            </div>
        </div>

        <div class="col-12 text-end pt-3 border-top">
            <a href="<?= ADMIN_URL ?>/testimonials.php" class="btn btn-light me-2">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check-circle me-1"></i> Save Testimonial
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
