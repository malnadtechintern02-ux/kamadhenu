<?php
/**
 * Adoption Confirmation & Certificate Preview
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';

$id = getIntParam('id');
if (!$id) { redirect(SITE_URL . '/adopt-a-cow.php'); }

$adoption = dbFetchOne(
    "SELECT a.*, c.name as cow_name, c.photo as cow_photo, b.name as breed_name 
     FROM adoptions a 
     LEFT JOIN cows c ON a.cow_id = c.id 
     LEFT JOIN breeds b ON c.breed_id = b.id 
     WHERE a.id = ?", 
    [$id]
);

if (!$adoption) {
    http_response_code(404);
    include BASE_PATH . '/404.php';
    exit;
}

$seo = [
    'title' => 'Adoption Certificate - ' . $adoption['adopter_name'],
    'description' => 'Thank you for adopting a cow at Kamadhenu Goushala.',
];

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="section py-5">
    <div class="container" style="max-width: 850px;">
        <div class="text-center mb-4">
            <div class="stat-icon mx-auto mb-3" style="width: 70px; height: 70px; font-size: 2rem; background: var(--clr-primary-light); color: var(--clr-primary);">
                <i class="bi bi-house-heart-fill"></i>
            </div>
            <h1 class="h2 fw-bold text-brown">Gau Guardian Certificate</h1>
            <p class="text-muted">🙏 Congratulations! You are now an official protector of Gau Mata at Kamadhenu Goushala.</p>
        </div>

        <!-- Official Certificate Box -->
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4 p-4 p-md-5 text-center" style="background: #FFFDF5; border: 8px double #C8A951 !important;">
            <div class="mb-3">
                <i class="bi bi-flower2 fs-1 text-success"></i>
                <h2 class="font-heading fs-3 text-brown mt-2 mb-0">Kamadenu Goushala</h2>
                <small class="text-muted text-uppercase letter-spacing-2">Kavadi, Virajpet Taluk, Kodagu, Karnataka</small>
            </div>

            <hr style="border-color: #C8A951; width: 60%; margin: 1.5rem auto;">

            <div class="my-4">
                <span class="text-uppercase small fw-bold text-muted d-block mb-2">Certificate of Gau Adoption</span>
                <p class="mb-1 text-muted">This is proudly presented to</p>
                <h3 class="font-heading fs-2 text-primary-dark my-2"><?= e($adoption['certificate_name'] ?: $adoption['adopter_name']) ?></h3>
                <p class="text-muted mt-2 mx-auto" style="max-width: 600px;">
                    in deep gratitude for becoming the sacred guardian of 
                    <strong class="text-dark"><?= e($adoption['cow_name'] ?? 'Sanctuary Cow') ?></strong> 
                    <?php if (!empty($adoption['breed_name'])): ?>(<?= e($adoption['breed_name']) ?>)<?php endif; ?> 
                    for a period of <strong><?= e($adoption['duration_months']) ?> Months</strong> (<?= formatDate($adoption['start_date']) ?> – <?= formatDate($adoption['end_date']) ?>).
                </p>
            </div>

            <?php if (!empty($adoption['special_occasion'])): ?>
            <div class="p-3 bg-light rounded-3 my-3 mx-auto small" style="max-width: 550px;">
                <strong>Special Dedication:</strong> <?= e($adoption['special_occasion']) ?>
            </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-end mt-5 pt-4 border-top">
                <div class="text-start">
                    <small class="text-muted d-block">Contribution</small>
                    <strong class="text-success fs-5"><?= formatCurrency((float)$adoption['total_amount']) ?></strong>
                </div>
                <div class="text-center">
                    <div class="badge bg-success-subtle text-success fs-6">Guardian ID #<?= $adoption['id'] ?></div>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">Authorized Signatory</small>
                    <span class="font-heading fw-bold text-brown">Kamadenu Trust</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-center gap-3">
            <button onclick="window.print()" class="btn btn-outline-custom">
                <i class="bi bi-printer me-1"></i> Print Certificate
            </button>
            <a href="<?= SITE_URL ?>/" class="btn btn-primary-custom">
                <i class="bi bi-house me-1"></i> Back to Homepage
            </a>
        </div>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
