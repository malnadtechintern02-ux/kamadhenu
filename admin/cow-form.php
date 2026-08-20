<?php
/**
 * Admin - Cow Add/Edit Form
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/validation.php';
require_once BASE_PATH . '/includes/upload.php';

$cowId = getIntParam('id');
$isEdit = !empty($cowId);
$pageTitle = $isEdit ? 'Edit Cow Profile' : 'Add New Cow';

$cow = [
    'name' => '',
    'breed_id' => '',
    'date_of_birth' => '',
    'gender' => 'Female',
    'photo' => '',
    'status' => 'Available',
    'is_adoptable' => 1,
    'monthly_adoption_amount' => 3000,
    'description' => '',
    'rescue_story' => '',
    'health_status' => 'Healthy',
    'is_featured' => 0
];

if ($isEdit) {
    $existing = dbFetchOne("SELECT * FROM cows WHERE id = ?", [$cowId]);
    if (!$existing) {
        setFlash('error', 'Cow profile not found.');
        redirect(ADMIN_URL . '/cows.php');
    }
    $cow = array_merge($cow, $existing);
}

$errors = [];

if (isPost()) {
    requireCsrfToken();
    
    $cow['name'] = getParam('name', '', 'POST');
    $cow['breed_id'] = getIntParam('breed_id', 0, 'POST');
    $cow['date_of_birth'] = getParam('date_of_birth', '', 'POST');
    $cow['gender'] = getParam('gender', 'Female', 'POST');
    $cow['status'] = getParam('status', 'Available', 'POST');
    $cow['is_adoptable'] = isset($_POST['is_adoptable']) ? 1 : 0;
    $cow['monthly_adoption_amount'] = (float)getParam('monthly_adoption_amount', 3000, 'POST');
    $cow['description'] = getParam('description', '', 'POST');
    $cow['rescue_story'] = getParam('rescue_story', '', 'POST');
    $cow['health_status'] = getParam('health_status', 'Healthy', 'POST');
    $cow['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;

    $validator = new Validator($cow);
    $validator->required('name', 'Cow Name')
              ->required('breed_id', 'Breed');

    if ($validator->passes()) {
        // Handle Photo Upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['photo'], 'cows', ['image/jpeg', 'image/png', 'image/webp'], 5 * 1024 * 1024);
            if ($uploadResult['success']) {
                if ($isEdit && !empty($cow['photo'])) {
                    deleteUploadedFile('cows/' . $cow['photo']);
                }
                $cow['photo'] = $uploadResult['filename'];
            } else {
                $errors['photo'] = $uploadResult['message'];
            }
        }

        if (empty($errors)) {
            $data = [
                'name' => $cow['name'],
                'breed_id' => $cow['breed_id'] ?: null,
                'date_of_birth' => $cow['date_of_birth'] ?: null,
                'gender' => $cow['gender'],
                'photo' => $cow['photo'],
                'status' => $cow['status'],
                'is_adoptable' => $cow['is_adoptable'],
                'monthly_adoption_amount' => $cow['monthly_adoption_amount'],
                'description' => $cow['description'],
                'rescue_story' => $cow['rescue_story'],
                'health_status' => $cow['health_status'],
                'is_featured' => $cow['is_featured']
            ];

            if ($isEdit) {
                dbUpdate('cows', $data, 'id = ?', [$cowId]);
                setFlash('success', 'Cow profile updated successfully.');
            } else {
                dbInsert('cows', $data);
                setFlash('success', 'New cow profile added successfully.');
            }
            redirect(ADMIN_URL . '/cows.php');
        }
    } else {
        $errors = array_merge($errors, $validator->getErrors());
    }
}

$breeds = dbFetchAll("SELECT id, name FROM breeds ORDER BY name ASC");

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold"><?= $pageTitle ?></h2>
        <p class="text-muted small mb-0">Fill in details about the sacred cow.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/cows.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
</div>

<form method="POST" action="" enctype="multipart/form-data" class="admin-card p-4">
    <?= csrfField() ?>
    <div class="row g-4">
        <!-- Basic Info -->
        <div class="col-md-6">
            <label class="form-label fw-semibold">Cow Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= e($cow['name']) ?>" required>
            <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= e($errors['name']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Breed <span class="text-danger">*</span></label>
            <select name="breed_id" class="form-select <?= isset($errors['breed_id']) ? 'is-invalid' : '' ?>" required>
                <option value="">Select Breed</option>
                <?php foreach ($breeds as $b): ?>
                <option value="<?= $b['id'] ?>" <?= $cow['breed_id'] == $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['breed_id'])): ?><div class="invalid-feedback"><?= e($errors['breed_id']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-control" value="<?= e($cow['date_of_birth']) ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Gender</label>
            <select name="gender" class="form-select">
                <option value="Female" <?= $cow['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                <option value="Male" <?= $cow['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Calf" <?= $cow['gender'] === 'Calf' ? 'selected' : '' ?>>Calf</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
                <option value="Available" <?= $cow['status'] === 'Available' ? 'selected' : '' ?>>Available</option>
                <option value="Adopted" <?= $cow['status'] === 'Adopted' ? 'selected' : '' ?>>Adopted</option>
                <option value="Permanent Resident" <?= $cow['status'] === 'Permanent Resident' ? 'selected' : '' ?>>Permanent Resident</option>
                <option value="Medical Care" <?= $cow['status'] === 'Medical Care' ? 'selected' : '' ?>>Medical Care</option>
                <option value="Rescued" <?= $cow['status'] === 'Rescued' ? 'selected' : '' ?>>Rescued</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Health Status</label>
            <input type="text" name="health_status" class="form-control" value="<?= e($cow['health_status']) ?>" placeholder="e.g., Excellent, Under Medical Care, Recovering">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Monthly Adoption Amount (₹)</label>
            <input type="number" name="monthly_adoption_amount" step="100" class="form-control" value="<?= e($cow['monthly_adoption_amount']) ?>">
        </div>

        <!-- Photo -->
        <div class="col-md-12">
            <label class="form-label fw-semibold">Profile Photo</label>
            <input type="file" name="photo" class="form-control <?= isset($errors['photo']) ? 'is-invalid' : '' ?>" accept="image/*" data-preview="cowPhotoPreview">
            <?php if (isset($errors['photo'])): ?><div class="invalid-feedback"><?= e($errors['photo']) ?></div><?php endif; ?>
            <div class="mt-2">
                <img id="cowPhotoPreview" src="<?= $cow['photo'] ? getCowPhotoUrl($cow['photo'], $cow['name']) : '' ?>" 
                     style="max-width: 150px; max-height: 150px; object-fit: cover; border-radius: 8px; display: <?= $cow['photo'] ? 'block' : 'none' ?>;">
            </div>
        </div>

        <!-- Description & Rescue Story -->
        <div class="col-md-12">
            <label class="form-label fw-semibold">About / Description</label>
            <textarea name="description" rows="4" class="form-control" placeholder="Describe personality, characteristics, behavior..."><?= e($cow['description']) ?></textarea>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Rescue Story (Optional)</label>
            <textarea name="rescue_story" rows="3" class="form-control" placeholder="If this cow was rescued, share how it came to the Goushala..."><?= e($cow['rescue_story']) ?></textarea>
        </div>

        <!-- Options -->
        <div class="col-md-12">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_adoptable" id="is_adoptable" value="1" <?= $cow['is_adoptable'] ? 'checked' : '' ?>>
                <label class="form-check-label fw-medium" for="is_adoptable">Open for Adoption</label>
            </div>
            <div class="form-check form-check-inline ms-3">
                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" <?= $cow['is_featured'] ? 'checked' : '' ?>>
                <label class="form-check-label fw-medium" for="is_featured">Feature on Homepage</label>
            </div>
        </div>

        <div class="col-12 text-end pt-3 border-top">
            <a href="<?= ADMIN_URL ?>/cows.php" class="btn btn-light me-2">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check-circle me-1"></i> Save Profile
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
