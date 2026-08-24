<?php
/**
 * Admin - Hero Section Settings
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/validation.php';
require_once BASE_PATH . '/includes/upload.php';

$pageTitle = 'Hero Section Settings';
$errors = [];

if (isPost()) {
    requireCsrfToken();
    
    $keys = [
        'hero_badge', 'hero_title', 'hero_subtitle', 
        'hero_btn1_text', 'hero_btn1_url', 
        'hero_btn2_text', 'hero_btn2_url'
    ];
    
    foreach ($keys as $k) {
        if (isset($_POST[$k])) {
            $val = trim($_POST[$k]);
            $existing = dbFetchOne("SELECT id FROM settings WHERE `setting_key` = ?", [$k]);
            if ($existing) {
                dbUpdate('settings', ['setting_value' => $val, 'setting_group' => 'hero'], '`setting_key` = ?', [$k]);
            } else {
                dbInsert('settings', ['setting_key' => $k, 'setting_value' => $val, 'setting_group' => 'hero']);
            }
        }
    }
    
    // File upload
    if (isset($_FILES['hero_bg']) && $_FILES['hero_bg']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = handleFileUpload('hero_bg', 'hero');
        if ($uploadResult['success']) {
            $filename = $uploadResult['filename'];
            
            // Delete old background image if exists
            $oldBg = getSetting('hero_bg');
            if (!empty($oldBg)) {
                deleteUploadedFile('hero/' . $oldBg);
            }
            
            $existing = dbFetchOne("SELECT id FROM settings WHERE `setting_key` = ?", ['hero_bg']);
            if ($existing) {
                dbUpdate('settings', ['setting_value' => $filename, 'setting_group' => 'hero'], '`setting_key` = ?', ['hero_bg']);
            } else {
                dbInsert('settings', ['setting_key' => 'hero_bg', 'setting_value' => $filename, 'setting_group' => 'hero']);
            }
        } else {
            $errors['hero_bg'] = $uploadResult['error'];
        }
    }
    
    if (empty($errors)) {
        setFlash('success', 'Hero settings updated successfully.');
        redirect(ADMIN_URL . '/hero-settings.php');
    }
}

$heroBgPath = getSetting('hero_bg');
$heroBgUrl = !empty($heroBgPath) ? getUploadUrl('hero/' . $heroBgPath) : ASSETS_URL . '/images/hero/hero-bg.jpg';

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Hero Section Settings</h2>
        <p class="text-muted small mb-0">Customize the welcome/introduction section of the homepage.</p>
    </div>
</div>

<form method="POST" action="" enctype="multipart/form-data">
    <?= csrfField() ?>
    
    <!-- Hero Text Settings -->
    <div class="admin-card p-4 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-fonts me-2 text-primary"></i>Hero Content</h5>
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label fw-semibold">Badge Text</label>
                <input type="text" name="hero_badge" class="form-control" value="<?= e(getSetting('hero_badge', '🙏 Vande Gou Mataram')) ?>" placeholder="e.g. 🙏 Vande Gou Mataram">
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold">Hero Title</label>
                <textarea name="hero_title" rows="2" class="form-control" placeholder="Enter title (use newlines for natural breaks)"><?= e(getSetting('hero_title', "Protecting Gau Mata.\nPreserving Our Heritage.")) ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Hero Subtitle</label>
                <textarea name="hero_subtitle" rows="3" class="form-control" placeholder="Enter introduction paragraph..."><?= e(getSetting('hero_subtitle', 'Serving indigenous cows with compassion, care and devotion at Kamadhenu Goushala, nestled in the sacred lands of Kodagu, Karnataka.')) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Background Image Settings -->
    <div class="admin-card p-4 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-image me-2 text-primary"></i>Hero Background Image</h5>
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label fw-semibold">Upload New Background Image</label>
                <input type="file" name="hero_bg" class="form-control <?= isset($errors['hero_bg']) ? 'is-invalid' : '' ?>" accept="image/*">
                <?php if (isset($errors['hero_bg'])): ?><div class="invalid-feedback"><?= e($errors['hero_bg']) ?></div><?php endif; ?>
                <div class="form-text">Recommended size: 1920x1080px (Landscape format). Max size 5MB.</div>
                
                <div class="mt-3">
                    <label class="form-label fw-semibold d-block">Current Background Image Preview</label>
                    <img src="<?= e($heroBgUrl) ?>" alt="Hero BG Preview" class="img-thumbnail" style="max-height: 250px; width: auto; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Buttons Settings -->
    <div class="admin-card p-4 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-link-45deg me-2 text-primary"></i>Call to Action Buttons</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <h6 class="fw-bold text-secondary mb-2">Primary Button</h6>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Button Text</label>
                    <input type="text" name="hero_btn1_text" class="form-control" value="<?= e(getSetting('hero_btn1_text', 'Donate Now')) ?>">
                </div>
                <div>
                    <label class="form-label small fw-semibold">Button URL</label>
                    <input type="text" name="hero_btn1_url" class="form-control" value="<?= e(getSetting('hero_btn1_url', '/donate.php')) ?>">
                    <span class="form-text text-muted">Use relative paths (e.g. <code>/donate.php</code>) or full URLs.</span>
                </div>
            </div>
            
            <div class="col-md-6 border-start ps-md-4">
                <h6 class="fw-bold text-secondary mb-2">Secondary Button</h6>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Button Text</label>
                    <input type="text" name="hero_btn2_text" class="form-control" value="<?= e(getSetting('hero_btn2_text', 'Adopt a Cow')) ?>">
                </div>
                <div>
                    <label class="form-label small fw-semibold">Button URL</label>
                    <input type="text" name="hero_btn2_url" class="form-control" value="<?= e(getSetting('hero_btn2_url', '/adopt-a-cow.php')) ?>">
                    <span class="form-text text-muted">Use relative paths (e.g. <code>/adopt-a-cow.php</code>) or full URLs.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="text-end mb-5">
        <button type="submit" class="btn btn-primary btn-lg px-4">
            <i class="bi bi-check-circle me-1"></i> Save Hero Settings
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
