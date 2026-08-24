<?php
/**
 * Feed a Cow Dedicated Seva Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/validation.php';

$seo = [
    'title' => 'Feed a Cow - Gau Grass Seva',
    'description' => 'Sponsor nutritious green fodder, grains, jaggery, and mineral feed for cows at Kamadhenu Goushala. Perform Gau Grass Seva for good health and blessings.',
];

$bankName = getSetting('bank_name', 'State Bank of India');
$accountName = getSetting('bank_account_name', 'Kamadenu Goushala Trust');
$accountNumber = getSetting('bank_account_number', '123456789012');
$ifsc = getSetting('bank_ifsc', 'SBIN0001234');
$upiId = getSetting('upi_id', 'kamadhenu@upi');

// Handle form
$errors = [];
$formData = [
    'donor_name' => '',
    'donor_email' => '',
    'donor_phone' => '',
    'feed_package' => 'feed_one_day',
    'pan_number' => '',
    'notes' => ''
];

$feedPackages = [
    'feed_1_cow_1_day'   => ['title' => 'Feed 1 Cow for 1 Day', 'amount' => 150, 'desc' => 'Fresh green grass, cattle feed & jaggery'],
    'feed_5_cows_1_day'  => ['title' => 'Feed 5 Cows for 1 Day', 'amount' => 750, 'desc' => 'Nutritious fodder for 5 cows'],
    'feed_all_1_day'     => ['title' => 'Feed Entire Sanctuary (70+ Cows) for 1 Day', 'amount' => 5000, 'desc' => 'Complete daily feast for the whole herd'],
    'feed_1_cow_1_month' => ['title' => 'Feed 1 Cow for 1 Month', 'amount' => 3000, 'desc' => 'Complete month-long nourishment'],
    'feed_all_1_week'    => ['title' => 'Feed Entire Sanctuary for 1 Week', 'amount' => 35000, 'desc' => 'Sponsor an entire week of sacred feeding'],
];

if (isPost()) {
    requireCsrfToken();
    
    $pkgKey = getParam('feed_package', 'feed_1_cow_1_day', 'POST');
    $pkg = $feedPackages[$pkgKey] ?? $feedPackages['feed_1_cow_1_day'];
    
    $formData = [
        'donor_name'   => getParam('donor_name', '', 'POST'),
        'donor_email'  => getParam('donor_email', '', 'POST'),
        'donor_phone'  => getParam('donor_phone', '', 'POST'),
        'feed_package' => $pkgKey,
        'pan_number'   => strtoupper(trim(getParam('pan_number', '', 'POST'))),
        'notes'        => getParam('notes', '', 'POST')
    ];

    $validator = new Validator($formData);
    $validator->required('donor_name', 'Full Name')
              ->required('donor_email', 'Email Address')
              ->email('donor_email')
              ->required('donor_phone', 'Phone Number');

    if ($validator->passes()) {
        $transactionId = 'KGFEED' . strtoupper(substr(uniqid(), -6)) . rand(10, 99);
        
        $inserted = dbInsert('donations', [
            'donor_name'      => $formData['donor_name'],
            'donor_email'     => $formData['donor_email'],
            'donor_phone'     => $formData['donor_phone'],
            'amount'          => $pkg['amount'],
            'seva_type'       => 'Feed a Cow - ' . $pkg['title'],
            'pan_number'      => $formData['pan_number'],
            'payment_method'  => 'upi',
            'payment_gateway' => 'Direct/UPI',
            'payment_status'  => 'Completed',
            'transaction_id'  => $transactionId,
            'notes'           => $formData['notes']
        ]);

        if ($inserted) {
            setFlash('success', '🙏 Thank you for performing Gau Grass Seva!');
            redirect(SITE_URL . '/donation-success.php?txn=' . $transactionId);
        }
    } else {
        $errors = $validator->getErrors();
    }
}

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/gau-seva.php">Gau Seva</a></li>
                <li class="breadcrumb-item active">Feed a Cow</li>
            </ol>
        </nav>
        <h1>Feed a Cow (Gau Grass Seva)</h1>
        <p>Offering grass and food to Gau Mata is considered one of the highest forms of punya in Sanatana Dharma.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <h3 class="mb-3">Select Feeding Seva Package</h3>

                    <form method="POST" action="" class="needs-validation" novalidate>
                        <?= csrfField() ?>

                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-3">Choose Seva Package <span class="text-danger">*</span></label>
                            <div class="row g-3">
                                <?php foreach ($feedPackages as $k => $p): ?>
                                <div class="col-12">
                                    <label class="card p-3 border rounded-3 cursor-pointer d-flex flex-row justify-content-between align-items-center" style="cursor: pointer;">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="feed_package" value="<?= $k ?>" class="form-check-input me-3" <?= $formData['feed_package'] === $k ? 'checked' : '' ?>>
                                            <div>
                                                <div class="fw-bold text-dark"><?= e($p['title']) ?></div>
                                                <small class="text-muted"><?= e($p['desc']) ?></small>
                                            </div>
                                        </div>
                                        <div class="fs-5 fw-bold text-success ms-3"><?= formatCurrency($p['amount']) ?></div>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Donor Info -->
                        <h4 class="fs-5 fw-bold mt-4 mb-3 pt-3 border-top">Devotee Information</h4>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="donor_name" class="form-control <?= isset($errors['donor_name']) ? 'is-invalid' : '' ?>" value="<?= e($formData['donor_name']) ?>" required>
                                <?php if (isset($errors['donor_name'])): ?><div class="invalid-feedback"><?= e($errors['donor_name']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="donor_email" class="form-control <?= isset($errors['donor_email']) ? 'is-invalid' : '' ?>" value="<?= e($formData['donor_email']) ?>" required>
                                <?php if (isset($errors['donor_email'])): ?><div class="invalid-feedback"><?= e($errors['donor_email']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone / WhatsApp <span class="text-danger">*</span></label>
                                <input type="tel" name="donor_phone" class="form-control <?= isset($errors['donor_phone']) ? 'is-invalid' : '' ?>" value="<?= e($formData['donor_phone']) ?>" required>
                                <?php if (isset($errors['donor_phone'])): ?><div class="invalid-feedback"><?= e($errors['donor_phone']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">PAN Number (Optional, for 80G Tax Benefit)</label>
                                <input type="text" name="pan_number" class="form-control text-uppercase" value="<?= e($formData['pan_number']) ?>" maxlength="10">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Sankalpa / Gotra / Special Occasion</label>
                                <textarea name="notes" rows="2" class="form-control" placeholder="e.g. Birthday, death anniversary (Shraadh), wedding anniversary, or health prayer..."><?= e($formData['notes']) ?></textarea>
                            </div>
                        </div>

                        <div class="mt-4 pt-3">
                            <button type="submit" class="btn btn-donate btn-lg w-100 py-3 fs-5">
                                <i class="bi bi-flower1 me-2"></i> Offer Gau Grass Seva
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Spiritual Importance Sidebar -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: linear-gradient(135deg, var(--clr-primary-light), var(--clr-gold-light));">
                    <h4 class="fw-bold mb-3"><i class="bi bi-bank me-2 text-primary-custom"></i>Direct Bank Transfer</h4>
                    <p class="small text-muted mb-3">You can also donate directly to our official Goushala trust bank account:</p>
                    
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2"><strong>Account Name:</strong> <?= e($accountName) ?></li>
                        <li class="mb-2"><strong>Bank:</strong> <?= e($bankName) ?></li>
                        <li class="mb-2"><strong>Account No:</strong> <code class="fs-6"><?= e($accountNumber) ?></code></li>
                        <li class="mb-2"><strong>IFSC Code:</strong> <code class="fs-6"><?= e($ifsc) ?></code></li>
                        <li><strong>UPI ID:</strong> <code class="fs-6"><?= e($upiId) ?></code></li>
                    </ul>
                    
                    <div class="text-center mt-4 pt-3 border-top border-dark border-opacity-10">
                        <p class="fw-bold mb-2 small">Scan to Donate via UPI</p>
                        <?php 
                        $qrData = urlencode("upi://pay?pa={$upiId}&pn={$accountName}");
                        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={$qrData}";
                        ?>
                        <img src="<?= e($qrUrl) ?>" alt="UPI QR Code" class="img-fluid rounded border p-2 bg-white" style="max-width: 150px;">
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: linear-gradient(135deg, var(--clr-primary-light), var(--clr-gold-light));">
                    <h4 class="fw-bold mb-3"><i class="bi bi-stars me-2 text-warning"></i>The Power of Gau Grass Seva</h4>
                    <p class="small text-muted mb-3">According to Vedic scriptures, feeding a cow pleases all 33 crore deities residing within Gau Mata:</p>
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i><strong>Planetary Remedies:</strong> Mitigates negative effects of Rahu, Ketu, and Shani.</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i><strong>Pitru Dosha Nivarana:</strong> Feeding cows on Amavasya brings peace to ancestors.</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i><strong>Health &amp; Prosperity:</strong> Bestows long life, purity of mind, and family harmony.</li>
                        <li><i class="bi bi-check2-circle text-primary me-2"></i><strong>Direct Care:</strong> Every bundle of grass ensures cows are fed on time, every day.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
