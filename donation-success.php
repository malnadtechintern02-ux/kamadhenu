<?php
/**
 * Donation Confirmation & Receipt
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';

$txnId = getParam('txn');
if (!$txnId) { redirect(SITE_URL . '/donate.php'); }

$donation = dbFetchOne("SELECT * FROM donations WHERE transaction_id = ?", [$txnId]);
if (!$donation) {
    http_response_code(404);
    include BASE_PATH . '/404.php';
    exit;
}

$seo = [
    'title' => 'Donation Receipt #' . $txnId,
    'description' => 'Thank you for your Gau Seva contribution to Kamadhenu Goushala.',
];

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<section class="section py-5">
    <div class="container" style="max-width: 800px;">
        <div class="text-center mb-4">
            <div class="stat-icon mx-auto mb-3" style="width: 70px; height: 70px; font-size: 2rem; background: var(--clr-primary-light); color: var(--clr-primary);">
                <i class="bi bi-check-lg"></i>
            </div>
            <h1 class="h2 fw-bold text-brown">Gau Seva Contribution Received!</h1>
            <p class="text-muted">🙏 Dhanyavad! May Gau Mata shower abundant health, peace, and prosperity upon you and your family.</p>
        </div>

        <!-- Printable Receipt Card -->
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4" id="receiptCard">
            <div class="p-4 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, var(--clr-primary-dark), var(--clr-primary));">
                <div>
                    <h4 class="mb-0 font-heading text-white">Kamadenu Goushala</h4>
                    <small style="opacity: 0.85;">Official Seva Contribution Receipt</small>
                </div>
                <div class="text-end">
                    <div class="badge bg-white text-dark fs-6 font-monospace"><?= e($donation['transaction_id']) ?></div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="row g-4 mb-4 pb-4 border-bottom">
                    <div class="col-sm-6">
                        <small class="text-muted d-block text-uppercase fw-semibold">Donor Name</small>
                        <h5 class="fw-bold text-dark mb-0"><?= e($donation['donor_name']) ?></h5>
                        <small class="text-muted"><?= e($donation['donor_email']) ?> | <?= e($donation['donor_phone']) ?></small>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <small class="text-muted d-block text-uppercase fw-semibold">Contribution Amount</small>
                        <h2 class="fw-bold text-success mb-0"><?= formatCurrency((float)$donation['amount']) ?></h2>
                        <span class="badge bg-success-subtle text-success">Payment <?= e($donation['payment_status']) ?></span>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Seva Program</small>
                        <strong><?= e($donation['seva_type']) ?></strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Receipt Date</small>
                        <strong><?= formatDate($donation['created_at']) ?></strong>
                    </div>
                    <?php if (!empty($donation['pan_number'])): ?>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Donor PAN (80G)</small>
                        <strong class="font-monospace"><?= e($donation['pan_number']) ?></strong>
                    </div>
                    <?php endif; ?>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Payment Mode</small>
                        <strong class="text-uppercase"><?= e($donation['payment_method']) ?></strong>
                    </div>
                    <?php if (!empty($donation['notes'])): ?>
                    <div class="col-12">
                        <small class="text-muted d-block">Sankalpa / Dedication</small>
                        <div class="p-3 bg-light rounded-3 small"><?= nl2br(e($donation['notes'])) ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="p-3 rounded-3 text-center small text-muted mb-0" style="background: var(--clr-warm-bg);">
                    Kamadhenu Goushala Trust · Kavadi, Virajpet Taluk, Kodagu (Coorg), Karnataka<br>
                    Website: <?= SITE_URL ?> · Phone: <?= e(getSetting('phone', '')) ?>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-center gap-3">
            <button onclick="window.print()" class="btn btn-outline-custom">
                <i class="bi bi-printer me-1"></i> Print Receipt
            </button>
            <a href="<?= SITE_URL ?>/" class="btn btn-primary-custom">
                <i class="bi bi-house me-1"></i> Back to Homepage
            </a>
        </div>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
