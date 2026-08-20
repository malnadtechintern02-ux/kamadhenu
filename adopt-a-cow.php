<?php
/**
 * Adopt a Cow Dedicated Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/validation.php';

$seo = [
    'title' => 'Adopt a Cow - Become a Gau Guardian',
    'description' => 'Adopt an indigenous cow at Kamadhenu Goushala. Support food, shelter, veterinary care, and receive personalized certificate and regular updates.',
];

$preselectedCowId = getIntParam('cow');
$adoptableCows = dbFetchAll("SELECT c.*, b.name as breed_name FROM cows c LEFT JOIN breeds b ON c.breed_id = b.id WHERE c.is_adoptable = 1 AND c.status = 'Available' ORDER BY c.name ASC");

$errors = [];
$formData = [
    'cow_id' => $preselectedCowId ?: '',
    'duration_months' => 12,
    'adopter_name' => '',
    'adopter_email' => '',
    'adopter_phone' => '',
    'adopter_address' => '',
    'pan_number' => '',
    'certificate_name' => '',
    'special_occasion' => ''
];

$monthlyFee = 3000;

if (isPost()) {
    requireCsrfToken();
    
    $cowId = getIntParam('cow_id', 0, 'POST') ?: null;
    $duration = getIntParam('duration_months', 0, 'POST') ?: 12;
    $totalAmount = $monthlyFee * $duration;
    
    $formData = [
        'cow_id'           => $cowId,
        'duration_months'  => $duration,
        'adopter_name'     => getParam('adopter_name', '', 'POST'),
        'adopter_email'    => getParam('adopter_email', '', 'POST'),
        'adopter_phone'    => getParam('adopter_phone', '', 'POST'),
        'adopter_address'  => getParam('adopter_address', '', 'POST'),
        'pan_number'       => strtoupper(trim(getParam('pan_number', '', 'POST'))),
        'certificate_name' => getParam('certificate_name', '', 'POST'),
        'special_occasion' => getParam('special_occasion', '', 'POST')
    ];

    $validator = new Validator($formData);
    $validator->required('adopter_name', 'Full Name')
              ->required('adopter_email', 'Email Address')
              ->email('adopter_email')
              ->required('adopter_phone', 'Phone Number');

    if ($validator->passes()) {
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime("+{$duration} months"));
        
        $inserted = dbInsert('adoptions', [
            'cow_id'           => $cowId,
            'adopter_name'     => $formData['adopter_name'],
            'adopter_email'    => $formData['adopter_email'],
            'adopter_phone'    => $formData['adopter_phone'],
            'adopter_address'  => $formData['adopter_address'],
            'pan_number'       => $formData['pan_number'],
            'duration_months'  => $duration,
            'monthly_amount'   => $monthlyFee,
            'total_amount'     => $totalAmount,
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'certificate_name' => $formData['certificate_name'] ?: $formData['adopter_name'],
            'special_occasion' => $formData['special_occasion'],
            'status'           => 'Active'
        ]);

        if ($inserted) {
            $adoptionId = getDB()->lastInsertId();
            
            // If specific cow adopted, mark as Adopted
            if ($cowId) {
                dbUpdate('cows', ['status' => 'Adopted'], 'id = ?', [$cowId]);
            }
            
            setFlash('success', '🙏 Congratulations! You are now an official Gau Guardian.');
            redirect(SITE_URL . '/adoption-success.php?id=' . $adoptionId);
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
                <li class="breadcrumb-item active">Adopt a Cow</li>
            </ol>
        </nav>
        <h1>Adopt a Sacred Cow</h1>
        <p>Become a lifelong guardian of Gau Mata. Receive monthly health reports, photo updates, and an official Adoption Certificate.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <h3 class="mb-3">Adoption Registration Form</h3>

                    <form method="POST" action="" class="needs-validation" novalidate id="adoptionForm">
                        <?= csrfField() ?>

                        <!-- Cow Selector -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Choose Cow to Adopt (Optional)</label>
                            <select name="cow_id" class="form-select form-select-lg" id="cowSelect">
                                <option value="">Assign me any cow most in need of adoption</option>
                                <?php foreach ($adoptableCows as $ac): ?>
                                <option value="<?= $ac['id'] ?>" <?= $formData['cow_id'] == $ac['id'] ? 'selected' : '' ?>>
                                    <?= e($ac['name']) ?> (<?= e($ac['breed_name'] ?? 'Indigenous') ?>) - <?= calculateAge($ac['date_of_birth']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Duration Selector -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Adoption Duration <span class="text-danger">*</span></label>
                            <select name="duration_months" class="form-select form-select-lg" id="adoptionDuration">
                                <option value="1" <?= $formData['duration_months'] == 1 ? 'selected' : '' ?>>1 Month - ₹3,000</option>
                                <option value="3" <?= $formData['duration_months'] == 3 ? 'selected' : '' ?>>3 Months (Quarterly) - ₹9,000</option>
                                <option value="6" <?= $formData['duration_months'] == 6 ? 'selected' : '' ?>>6 Months (Half-Yearly) - ₹18,000</option>
                                <option value="12" <?= $formData['duration_months'] == 12 ? 'selected' : '' ?>>1 Year (Annual Guardian) - ₹36,000</option>
                                <option value="36" <?= $formData['duration_months'] == 36 ? 'selected' : '' ?>>3 Years (Lifelong Protector) - ₹1,08,000</option>
                            </select>
                            
                            <div class="p-3 bg-light rounded-3 mt-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">Monthly Care Amount:</small>
                                    <strong id="monthlyAmount" data-amount="3000">₹3,000 / month</strong>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Total Contribution:</small>
                                    <h4 class="fw-bold text-success mb-0" id="totalAmount">₹36,000</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Adopter Details -->
                        <h4 class="fs-5 fw-bold mt-4 mb-3 pt-3 border-top">Guardian Details</h4>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="adopter_name" class="form-control <?= isset($errors['adopter_name']) ? 'is-invalid' : '' ?>" value="<?= e($formData['adopter_name']) ?>" required>
                                <?php if (isset($errors['adopter_name'])): ?><div class="invalid-feedback"><?= e($errors['adopter_name']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="adopter_email" class="form-control <?= isset($errors['adopter_email']) ? 'is-invalid' : '' ?>" value="<?= e($formData['adopter_email']) ?>" required placeholder="For photo updates">
                                <?php if (isset($errors['adopter_email'])): ?><div class="invalid-feedback"><?= e($errors['adopter_email']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone / WhatsApp <span class="text-danger">*</span></label>
                                <input type="tel" name="adopter_phone" class="form-control <?= isset($errors['adopter_phone']) ? 'is-invalid' : '' ?>" value="<?= e($formData['adopter_phone']) ?>" required>
                                <?php if (isset($errors['adopter_phone'])): ?><div class="invalid-feedback"><?= e($errors['adopter_phone']) ?></div><?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Name to Print on Certificate</label>
                                <input type="text" name="certificate_name" class="form-control" value="<?= e($formData['certificate_name']) ?>" placeholder="Leave blank to use full name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">PAN Number (80G Tax Exemption)</label>
                                <input type="text" name="pan_number" class="form-control text-uppercase" value="<?= e($formData['pan_number']) ?>" maxlength="10">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Postal Address (For physical certificate delivery)</label>
                                <input type="text" name="adopter_address" class="form-control" value="<?= e($formData['adopter_address']) ?>" placeholder="House No, Street, City, State, PIN">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Dedication / Special Occasion</label>
                                <textarea name="special_occasion" rows="2" class="form-control" placeholder="In loving memory of..., On the occasion of my daughter's birthday..."><?= e($formData['special_occasion']) ?></textarea>
                            </div>
                        </div>

                        <div class="mt-4 pt-3">
                            <button type="submit" class="btn btn-donate btn-lg w-100 py-3 fs-5">
                                <i class="bi bi-house-heart-fill me-2"></i> Confirm Cow Adoption
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Adoption Benefits Sidebar -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: linear-gradient(135deg, var(--clr-primary-light), var(--clr-gold-light));">
                    <h4 class="fw-bold mb-3"><i class="bi bi-award me-2 text-primary-custom"></i>Guardian Privileges</h4>
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-3 d-flex">
                            <i class="bi bi-file-earmark-pdf-fill fs-5 text-primary me-2"></i>
                            <div><strong>Adoption Certificate:</strong> Official personalized certificate recognizing your sacred guardianship.</div>
                        </li>
                        <li class="mb-3 d-flex">
                            <i class="bi bi-camera-fill fs-5 text-primary me-2"></i>
                            <div><strong>Monthly Photo Updates:</strong> Receive quarterly health reports and pictures of your adopted cow.</div>
                        </li>
                        <li class="mb-3 d-flex">
                            <i class="bi bi-flower2 fs-5 text-primary me-2"></i>
                            <div><strong>Special Pooja:</strong> Sankalpa performed on your chosen auspicious dates &amp; birthdays.</div>
                        </li>
                        <li class="d-flex">
                            <i class="bi bi-heart-pulse-fill fs-5 text-primary me-2"></i>
                            <div><strong>Comprehensive Care:</strong> 100% of your fee funds green fodder, concentrates, veterinarian care, and shelter.</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
