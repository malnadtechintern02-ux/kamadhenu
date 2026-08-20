<?php
/**
 * Admin - Profile & Password Update
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/validation.php';

$pageTitle = 'Admin Profile';
$adminId = $_SESSION['admin_id'] ?? 1;
$admin = dbFetchOne("SELECT id, full_name as name, username, email FROM admins WHERE id = ?", [$adminId]);

$errors = [];

if (isPost()) {
    requireCsrfToken();
    
    $name = getParam('name', '', 'POST');
    $email = getParam('email', '', 'POST');
    $currentPassword = getParam('current_password', '', 'POST');
    $newPassword = getParam('new_password', '', 'POST');
    $confirmPassword = getParam('confirm_password', '', 'POST');

    $validator = new Validator(['name' => $name, 'email' => $email]);
    $validator->required('name', 'Name')->required('email', 'Email')->email('email');

    if ($validator->passes()) {
        $updateData = ['full_name' => $name, 'email' => $email];

        // If changing password
        if (!empty($currentPassword) || !empty($newPassword)) {
            $userRecord = dbFetchOne("SELECT password_hash FROM admins WHERE id = ?", [$adminId]);
            if (!$userRecord || !password_verify($currentPassword, $userRecord['password_hash'])) {
                $errors['current_password'] = 'Current password is incorrect.';
            } elseif (strlen($newPassword) < 8) {
                $errors['new_password'] = 'New password must be at least 8 characters long.';
            } elseif ($newPassword !== $confirmPassword) {
                $errors['confirm_password'] = 'New passwords do not match.';
            } else {
                $updateData['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }
        }

        if (empty($errors)) {
            dbUpdate('admins', $updateData, 'id = ?', [$adminId]);
            $_SESSION['admin_name'] = $name;
            $_SESSION['admin_email'] = $email;
            setFlash('success', 'Profile updated successfully.');
            redirect(ADMIN_URL . '/profile.php');
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
        <h2 class="h4 mb-0 fw-bold">Admin Profile</h2>
        <p class="text-muted small mb-0">Update account credentials and administrator password.</p>
    </div>
</div>

<form method="POST" action="" class="admin-card p-4" style="max-width: 650px;">
    <?= csrfField() ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Administrator Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= e($admin['name']) ?>" required>
            <?php if (isset($errors['name'])): ?><div class="invalid-feedback"><?= e($errors['name']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= e($admin['email']) ?>" required>
            <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= e($errors['email']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Username</label>
            <input type="text" class="form-control" value="<?= e($admin['username']) ?>" disabled>
            <small class="text-muted">Username cannot be changed.</small>
        </div>

        <div class="col-12 pt-3 border-top mt-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock me-1"></i> Change Password (Leave blank to keep unchanged)</h6>
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Current Password</label>
            <input type="password" name="current_password" class="form-control <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>">
            <?php if (isset($errors['current_password'])): ?><div class="invalid-feedback"><?= e($errors['current_password']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">New Password</label>
            <input type="password" name="new_password" class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>">
            <?php if (isset($errors['new_password'])): ?><div class="invalid-feedback"><?= e($errors['new_password']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>">
            <?php if (isset($errors['confirm_password'])): ?><div class="invalid-feedback"><?= e($errors['confirm_password']) ?></div><?php endif; ?>
        </div>

        <div class="col-12 text-end pt-3 border-top">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check-circle me-1"></i> Update Profile
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
