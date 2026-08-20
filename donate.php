<?php
/**
 * Donation Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/validation.php';

$seo = [
    'title' => 'Donate - Support Gau Seva',
    'description' => 'Contribute to Kamadhenu Goushala for cow feed, medical care, shelter, and seva. Eligible for 80G tax benefits. Pure devotion in every seva.',
];

$preselectedSeva = getParam('seva');
$sevaCategories = dbFetchAll("SELECT * FROM seva_categories WHERE is_active = 1 ORDER BY sort_order ASC");

$errors = [];
$formData = [
    'donor_name' => '',
    'donor_email' => '',
    'donor_phone' => '',
    'amount' => '1001',
    'seva_type' => 'General Gau Seva',
    'pan_number' => '',
    'payment_method' => 'upi',
    'address' => '',
    'notes' => ''
];

if ($preselectedSeva) {
    $found = dbFetchOne("SELECT title FROM seva_categories WHERE slug = ?", [$preselectedSeva]);
    if ($found) {
        $formData['seva_type'] = $found['title'];
    }
}

if (isPost()) {
    requireCsrfToken();
    
    $formData = [
        'donor_name'     => getParam('donor_name', '', 'POST'),
        'donor_email'    => getParam('donor_email', '', 'POST'),
        'donor_phone'    => getParam('donor_phone', '', 'POST'),
        'amount'         => (float)getParam('amount', 0, 'POST'),
        'seva_type'      => getParam('seva_type', 'General Gau Seva', 'POST'),
        'pan_number'     => strtoupper(trim(getParam('pan_number', '', 'POST'))),
        'payment_method' => getParam('payment_method', 'upi', 'POST'),
        'address'        => getParam('address', '', 'POST'),
        'notes'          => getParam('notes', '', 'POST')
    ];

    $validator = new Validator($formData);
    $validator->required('donor_name', 'Full Name')
              ->required('donor_email', 'Email Address')
              ->email('donor_email')
              ->required('donor_phone', 'Phone Number')
              ->min('amount', 1, 'Donation Amount');

    if ($validator->passes()) {
        $transactionId = 'KG' . strtoupper(substr(uniqid(), -8)) . rand(10, 99);
        
        $inserted = dbInsert('donations', [
            'donor_name'      => $formData['donor_name'],
            'donor_email'     => $formData['donor_email'],
            'donor_phone'     => $formData['donor_phone'],
            'amount'          => $formData['amount'],
            'seva_type'       => $formData['seva_type'],
            'pan_number'      => $formData['pan_number'],
            'payment_method'  => $formData['payment_method'],
            'payment_gateway' => 'Direct/UPI',
            'payment_status'  => 'Completed',
            'transaction_id'  => $transactionId,
            'address'         => $formData['address'],
            'notes'           => $formData['notes']
        ]);

        if ($inserted) {
            setFlash('success', '🙏 Thank you for your generous Gau Seva contribution!');
            redirect(SITE_URL . '/donation-success.php?txn=' . $transactionId);
        } else {
            $errors['general'] = 'Failed to process donation. Please try again.';
        }
    } else {
        $errors = $validator->getErrors();
    }
}

// Bank & UPI settings
$bankName = getSetting('bank_name', 'State Bank of India');
$accountName = getSetting('bank_account_name', 'Kamadenu Goushala Trust');
$accountNumber = getSetting('bank_account_number', '123456789012');
$ifsc = getSetting('bank_ifsc', 'SBIN0001234');
$upiId = getSetting('upi_id', 'kamadhenu@upi');

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active">Donate</li>
            </ol>
        </nav>
        <h1>Support Gau Seva</h1>
        <p>Your contribution directly nourishes, shelters, and heals sacred indigenous cows.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5">
            <!-- Donation Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <h3 class="mb-2">Choose Your Seva &amp; Amount</h3>
                    <p class="text-muted small mb-4">All contributions go directly toward cow fodder, medical camps, and shelter maintenance.</p>

                    <form method="POST" action="" class="needs-validation" novalidate id="donationForm">
                        <?= csrfField() ?>

                        <!-- Seva Offering -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Select Seva Program <span class="text-danger">*</span></label>
                            <select name="seva_type" class="form-select form-select-lg" required>
                                <option value="General Gau Seva" <?= $formData['seva_type'] === 'General Gau Seva' ? 'selected' : '' ?>>General Gau Seva (Where most needed)</option>
                                <?php foreach ($sevaCategories as $sc): ?>
                                <option value="<?= e($sc['title']) ?>" <?= $formData['seva_type'] === $sc['title'] ? 'selected' : '' ?>><?= e($sc['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Amount Selector -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Select Contribution Amount <span class="text-danger">*</span></label>
                            <div class="amount-selector">
                                <button type="button" class="amount-btn <?= $formData['amount'] == 501 ? 'active' : '' ?>" data-amount="501">₹501</button>
                                <button type="button" class="amount-btn <?= $formData['amount'] == 1001 ? 'active' : '' ?>" data-amount="1001">₹1,001</button>
                                <button type="button" class="amount-btn <?= $formData['amount'] == 2501 ? 'active' : '' ?>" data-amount="2501">₹2,501</button>
                                <button type="button" class="amount-btn <?= $formData['amount'] == 5001 ? 'active' : '' ?>" data-amount="5001">₹5,001</button>
                                <button type="button" class="amount-btn <?= $formData['amount'] == 11000 ? 'active' : '' ?>" data-amount="11000">₹11,000</button>
                                <button type="button" class="amount-btn <?= $formData['amount'] == 21000 ? 'active' : '' ?>" data-amount="21000">₹21,000</button>
                            </div>
                            <div class="input-group input-group-lg mt-2">
                                <span class="input-group-text bg-light fw-bold text-success">₹</span>
                                <input type="number" step="1" min="1" class="form-control <?= isset($errors['amount']) ? 'is-invalid' : '' ?>" 
                                       id="donationAmount" name="amount" value="<?= e($formData['amount']) ?>" placeholder="Or enter custom amount" required>
                                <?php if (isset($errors['amount'])): ?><div class="invalid-feedback"><?= e($errors['amount']) ?></div><?php endif; ?>
                            </div>
                        </div>

                        <!-- Donor Details -->
                        <h4 class="fs-5 fw-bold mt-4 mb-3 pt-3 border-top">Donor Details</h4>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="donor_name" class="form-control <?= isset($errors['donor_name']) ? 'is-invalid' : '' ?>" value="<?= e($formData['donor_name']) ?>" required placeholder="Your full name for receipt">
                                <?php if (isset($errors['donor_name'])): ?><div class="invalid-feedback"><?= e($errors['donor_name']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="donor_email" class="form-control <?= isset($errors['donor_email']) ? 'is-invalid' : '' ?>" value="<?= e($formData['donor_email']) ?>" required placeholder="For receipt copy">
                                <?php if (isset($errors['donor_email'])): ?><div class="invalid-feedback"><?= e($errors['donor_email']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone / WhatsApp <span class="text-danger">*</span></label>
                                <input type="tel" name="donor_phone" class="form-control <?= isset($errors['donor_phone']) ? 'is-invalid' : '' ?>" value="<?= e($formData['donor_phone']) ?>" required placeholder="+91...">
                                <?php if (isset($errors['donor_phone'])): ?><div class="invalid-feedback"><?= e($errors['donor_phone']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">PAN Number (Optional, for 80G Tax Exemption)</label>
                                <input type="text" name="pan_number" class="form-control text-uppercase" value="<?= e($formData['pan_number']) ?>" placeholder="ABCDE1234F" maxlength="10">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Payment Mode</label>
                                <select name="payment_method" class="form-select">
                                    <option value="upi">UPI / QR Code / GPay / PhonePe</option>
                                    <option value="netbanking">Net Banking / NEFT / IMPS</option>
                                    <option value="card">Debit / Credit Card</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Postal Address (Optional)</label>
                                <input type="text" name="address" class="form-control" value="<?= e($formData['address']) ?>" placeholder="City, State, Pin Code">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Special Prayer / Sankalpa / Occasion</label>
                                <textarea name="notes" rows="2" class="form-control" placeholder="e.g., Birthday seva for family member, anniversary, or special prayer..."><?= e($formData['notes']) ?></textarea>
                            </div>
                        </div>

                        <div class="mt-4 pt-3">
                            <button type="submit" class="btn btn-donate btn-lg w-100 py-3 fs-5">
                                <i class="bi bi-heart-fill me-2"></i> Complete Gau Seva Contribution
                            </button>
                            <p class="text-center text-muted small mt-2">🔒 Secure transaction. An official digital receipt will be generated instantly.</p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Direct Bank / UPI Details Sidebar -->
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
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-shield-check me-2 text-success"></i>Why Donate to Kamadenu?</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                            <div><strong>100% Direct Impact:</strong> Every rupee funds daily nutrition, green fodder, and health care for cows.</div>
                        </li>
                        <li class="d-flex mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                            <div><strong>Indigenous Breeds:</strong> Sanctuary dedicated exclusively to native Bharath cow breeds.</div>
                        </li>
                        <li class="d-flex mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                            <div><strong>Transparency:</strong> Regular updates, photo receipts, and open invitation to visit our sanctuary.</div>
                        </li>
                        <li class="d-flex">
                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                            <div><strong>Spiritual Blessings:</strong> Sankalpa performed in your name during daily Gau Pooja.</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
