<?php
/**
 * Admin - Website Settings
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/upload.php';

$pageTitle = 'Website Settings';

if (isPost()) {
    requireCsrfToken();
    
    // Handle About Image upload
    if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = handleFileUpload('about_image', 'about');
        if ($uploadResult['success']) {
            $filename = $uploadResult['filename'];
            
            $oldImage = getSetting('about_image');
            if (!empty($oldImage)) {
                deleteUploadedFile('about/' . $oldImage);
            }
            
            $existingAboutImg = dbFetchOne("SELECT id FROM settings WHERE `setting_key` = ?", ['about_image']);
            if ($existingAboutImg) {
                dbUpdate('settings', ['setting_value' => $filename], '`setting_key` = ?', ['about_image']);
            } else {
                dbInsert('settings', ['setting_key' => 'about_image', 'setting_value' => $filename]);
            }
        }
    }
    
    // Process multiple WhatsApp numbers
    $whatsappNumbers = [];
    $activeVal = '';
    
    if (isset($_POST['whatsapp_numbers_list']) && is_array($_POST['whatsapp_numbers_list'])) {
        $labels = $_POST['whatsapp_labels'] ?? [];
        $activeIdx = $_POST['whatsapp_active'] ?? '0';
        
        foreach ($_POST['whatsapp_numbers_list'] as $idx => $num) {
            $num = trim($num);
            if (empty($num)) continue;
            
            $label = trim($labels[$idx] ?? 'WhatsApp');
            $isActive = ($activeIdx == $idx);
            
            $whatsappNumbers[] = [
                'label' => $label,
                'number' => $num,
                'is_active' => $isActive
            ];
            
            if ($isActive) {
                $activeVal = $num;
            }
        }
    }
    
    if (empty($activeVal) && !empty($whatsappNumbers)) {
        $whatsappNumbers[0]['is_active'] = true;
        $activeVal = $whatsappNumbers[0]['number'];
    }
    
    $jsonVal = json_encode($whatsappNumbers);
    $existingNumbers = dbFetchOne("SELECT id FROM settings WHERE `setting_key` = ?", ['whatsapp_numbers']);
    if ($existingNumbers) {
        dbUpdate('settings', ['setting_value' => $jsonVal], '`setting_key` = ?', ['whatsapp_numbers']);
    } else {
        dbInsert('settings', ['setting_key' => 'whatsapp_numbers', 'setting_value' => $jsonVal]);
    }
    
    $existingWhatsapp = dbFetchOne("SELECT id FROM settings WHERE `setting_key` = ?", ['whatsapp']);
    if ($existingWhatsapp) {
        dbUpdate('settings', ['setting_value' => $activeVal], '`setting_key` = ?', ['whatsapp']);
    } else {
        dbInsert('settings', ['setting_key' => 'whatsapp', 'setting_value' => $activeVal]);
    }
    
    $keys = [
        'site_name', 'site_tagline', 'site_description', 'phone', 'email', 
        'address', 'whatsapp_floating_enabled', 'whatsapp_default_msg', 'facebook_url', 'instagram_url', 'youtube_url', 
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

<form method="POST" action="" enctype="multipart/form-data">
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
            <div class="col-12">
                <label class="form-label fw-semibold">About Page Image</label>
                <input type="file" name="about_image" class="form-control" accept="image/*">
                <div class="form-text">Choose a custom image for the About Us page section (Recommended landscape aspect ratio e.g. 600x450).</div>
                <?php 
                $aboutImg = getSetting('about_image');
                $aboutImgUrl = !empty($aboutImg) ? getUploadUrl('about/' . $aboutImg) : ASSETS_URL . '/images/about/goushala-about.jpg';
                ?>
                <div class="mt-2">
                    <img src="<?= e($aboutImgUrl) ?>" alt="Current About Image" class="img-thumbnail" style="max-height: 150px;">
                </div>
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
            <div class="col-md-12">
                <label class="form-label fw-semibold">WhatsApp Numbers List (Select Radio to set site default active number)</label>
                <div id="whatsapp-list-container" class="mb-2">
                    <?php 
                    $numbersJson = settingVal('whatsapp_numbers', '[]');
                    $numbers = json_decode($numbersJson, true);
                    if (!is_array($numbers)) {
                        $numbers = [];
                    }
                    
                    if (empty($numbers) && !empty(settingVal('whatsapp'))) {
                        $numbers[] = [
                            'label' => 'Primary',
                            'number' => settingVal('whatsapp'),
                            'is_active' => true
                        ];
                    }
                    
                    if (empty($numbers)) {
                        $numbers[] = [
                            'label' => 'Primary',
                            'number' => '',
                            'is_active' => true
                        ];
                    }
                    
                    foreach ($numbers as $idx => $item):
                    ?>
                    <div class="row g-2 align-items-center whatsapp-row mb-2">
                        <div class="col-auto text-center" style="width: 40px;">
                            <input type="radio" name="whatsapp_active" value="<?= $idx ?>" class="form-check-input" <?= $item['is_active'] ? 'checked' : '' ?> title="Select as Active Default" style="transform: scale(1.2);">
                        </div>
                        <div class="col">
                            <input type="text" name="whatsapp_labels[]" class="form-control form-control-sm" value="<?= e($item['label']) ?>" placeholder="Label (e.g. Sales, Support)" required>
                        </div>
                        <div class="col">
                            <input type="text" name="whatsapp_numbers_list[]" class="form-control form-control-sm" value="<?= e($item['number']) ?>" placeholder="Number (with country code e.g. +91...)" required>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-num-btn"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add-whatsapp-btn" class="btn btn-outline-primary btn-sm mb-3">
                    <i class="bi bi-plus-lg me-1"></i> Add New Number
                </button>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Show WhatsApp Floating Button on Site</label>
                <select name="whatsapp_floating_enabled" class="form-select">
                    <option value="1" <?= settingVal('whatsapp_floating_enabled', '1') === '1' ? 'selected' : '' ?>>Enabled</option>
                    <option value="0" <?= settingVal('whatsapp_floating_enabled', '1') === '0' ? 'selected' : '' ?>>Disabled</option>
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold">Default WhatsApp Chat Message</label>
                <input type="text" name="whatsapp_default_msg" class="form-control" value="<?= e(settingVal('whatsapp_default_msg', '🙏 Namaste, I would like to support Gau Seva.')) ?>" placeholder="Message populated when user clicks WhatsApp link">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('whatsapp-list-container');
    const addButton = document.getElementById('add-whatsapp-btn');
    
    addButton.addEventListener('click', function() {
        const rows = container.querySelectorAll('.whatsapp-row');
        const nextIndex = rows.length;
        
        const newRow = document.createElement('div');
        newRow.className = 'row g-2 align-items-center whatsapp-row mb-2';
        newRow.innerHTML = `
            <div class="col-auto text-center" style="width: 40px;">
                <input type="radio" name="whatsapp_active" value="${nextIndex}" class="form-check-input" ${nextIndex === 0 ? 'checked' : ''} title="Select as Active Default" style="transform: scale(1.2);">
            </div>
            <div class="col">
                <input type="text" name="whatsapp_labels[]" class="form-control form-control-sm" value="" placeholder="Label (e.g. Sales, Support)" required>
            </div>
            <div class="col">
                <input type="text" name="whatsapp_numbers_list[]" class="form-control form-control-sm" value="" placeholder="Number (with country code e.g. +91...)" required>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-outline-danger btn-sm remove-num-btn"><i class="bi bi-trash"></i></button>
            </div>
        `;
        container.appendChild(newRow);
        attachRemoveEvent(newRow.querySelector('.remove-num-btn'));
    });
    
    function attachRemoveEvent(button) {
        button.addEventListener('click', function() {
            const rows = container.querySelectorAll('.whatsapp-row');
            if (rows.length <= 1) {
                alert('At least one WhatsApp number is required.');
                return;
            }
            const row = button.closest('.whatsapp-row');
            const wasChecked = row.querySelector('input[type="radio"]').checked;
            row.remove();
            
            // Re-index all radio buttons
            const remainingRows = container.querySelectorAll('.whatsapp-row');
            remainingRows.forEach((r, idx) => {
                const radio = r.querySelector('input[type="radio"]');
                radio.value = idx;
                if (wasChecked && idx === 0) {
                    radio.checked = true;
                }
            });
        });
    }
    
    container.querySelectorAll('.remove-num-btn').forEach(attachRemoveEvent);
});
</script>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
