<?php
/**
 * Admin - Website Settings
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';

$pageTitle = 'Website Settings';

if (isPost()) {
    requireCsrfToken();
    
    $keys = [
        'site_name', 'site_tagline', 'site_description', 'phone', 'email', 
        'address', 'whatsapp', 'facebook_url', 'instagram_url', 'youtube_url', 
        'google_maps_url', 'footer_text', 'stat_total_cows', 'stat_rescued_cows', 
        'stat_seva_programs', 'stat_years_service', 'bank_account_name', 
        'bank_name', 'bank_account_number', 'bank_ifsc', 'upi_id', 'upi_qr_code'
    ];
    
    foreach ($keys as $k) {
        if (isset($_POST[$k])) {
            $val = trim($_POST[$k]);
            $existing = dbFetchOne("SELECT id FROM settings WHERE `setting_key` = ?", [$k]);
            if ($existing) {
                dbUpdate('settings', ['setting_value' => $val], '`setting_key` = ?', [$k]);
            } else {
                dbInsert('settings', ['setting_key' => $k, 'setting_value' => $val]);
            }
        }
    }
    
    setFlash('success', 'Settings updated successfully.');
    redirect(ADMIN_URL . '/settings.php');
}

$allSettings = [];
$rows = dbFetchAll("SELECT * FROM settings");
foreach ($rows as $r) {
    $allSettings[$r['setting_key']] = $r['setting_value'];
}

function settingVal(string $k, string $def = ''): string {
    global $allSettings;
    return $allSettings[$k] ?? $def;
}

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Site &amp; Goushala Settings</h2>
        <p class="text-muted small mb-0">Configure contact information, social links, stats, and bank details.</p>
    </div>
</div>

<form method="POST" action="">
    <?= csrfField() ?>
    
    <!-- General & Branding -->
    <div class="admin-card p-4 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-gear-wide-connected me-2 text-primary"></i>General Information</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Organization / Site Name</label>
                <input type="text" name="site_name" class="form-control" value="<?= e(settingVal('site_name', 'Kamadenu Goushala')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Tagline</label>
                <input type="text" name="site_tagline" class="form-control" value="<?= e(settingVal('site_tagline', 'Gau Seva & Protection')) ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Site Description (Default SEO)</label>
                <textarea name="site_description" rows="2" class="form-control"><?= e(settingVal('site_description')) ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Footer About Text</label>
                <textarea name="footer_text" rows="2" class="form-control"><?= e(settingVal('footer_text')) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Contact & Location -->
    <div class="admin-card p-4 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt me-2 text-primary"></i>Contact &amp; Location</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Official Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= e(settingVal('phone')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Official Email</label>
                <input type="email" name="email" class="form-control" value="<?= e(settingVal('email')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">WhatsApp Number (with country code)</label>
                <input type="text" name="whatsapp" class="form-control" value="<?= e(settingVal('whatsapp')) ?>" placeholder="+919876543210">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Google Maps Embed URL (Iframe Src)</label>
                <input type="text" name="google_maps_url" class="form-control" value="<?= e(settingVal('google_maps_url')) ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Physical Postal Address</label>
                <textarea name="address" rows="2" class="form-control"><?= e(settingVal('address')) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Social Links -->
    <div class="admin-card p-4 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-share me-2 text-primary"></i>Social Media Links</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold"><i class="bi bi-facebook me-1 text-primary"></i>Facebook URL</label>
                <input type="url" name="facebook_url" class="form-control" value="<?= e(settingVal('facebook_url')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold"><i class="bi bi-instagram me-1 text-danger"></i>Instagram URL</label>
                <input type="url" name="instagram_url" class="form-control" value="<?= e(settingVal('instagram_url')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold"><i class="bi bi-youtube me-1 text-danger"></i>YouTube Channel URL</label>
                <input type="url" name="youtube_url" class="form-control" value="<?= e(settingVal('youtube_url')) ?>">
            </div>
        </div>
    </div>

    <!-- Statistics (Homepage Counters) -->
    <div class="admin-card p-4 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-speedometer2 me-2 text-primary"></i>Homepage Impact Statistics</h5>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Total Cows Protected</label>
                <input type="number" name="stat_total_cows" class="form-control" value="<?= e(settingVal('stat_total_cows', '70')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Rescued Cows</label>
                <input type="number" name="stat_rescued_cows" class="form-control" value="<?= e(settingVal('stat_rescued_cows', '45')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Seva Programs</label>
                <input type="number" name="stat_seva_programs" class="form-control" value="<?= e(settingVal('stat_seva_programs', '8')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Years of Service</label>
                <input type="number" name="stat_years_service" class="form-control" value="<?= e(settingVal('stat_years_service', '6')) ?>">
            </div>
        </div>
    </div>

    <!-- Bank Details for Direct Donations -->
    <div class="admin-card p-4 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-bank me-2 text-primary"></i>Bank &amp; UPI Details (Donations)</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Account Holder Name</label>
                <input type="text" name="bank_account_name" class="form-control" value="<?= e(settingVal('bank_account_name')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Bank Name</label>
                <input type="text" name="bank_name" class="form-control" value="<?= e(settingVal('bank_name')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Account Number</label>
                <input type="text" name="bank_account_number" class="form-control" value="<?= e(settingVal('bank_account_number')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">IFSC Code</label>
                <input type="text" name="bank_ifsc" class="form-control" value="<?= e(settingVal('bank_ifsc')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">UPI ID (e.g., kamadhenu@upi)</label>
                <input type="text" name="upi_id" class="form-control" value="<?= e(settingVal('upi_id')) ?>">
            </div>
        </div>
    </div>

    <div class="text-end mb-5">
        <button type="submit" class="btn btn-primary btn-lg px-4">
            <i class="bi bi-check-circle me-1"></i> Save All Settings
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
