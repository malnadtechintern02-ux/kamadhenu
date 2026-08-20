<?php
/**
 * Admin - Donation Receipt & Details
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';

$id = getIntParam('id');
if (!$id) { redirect(ADMIN_URL . '/donations.php'); }

$donation = dbFetchOne("SELECT * FROM donations WHERE id = ?", [$id]);
if (!$donation) {
    setFlash('error', 'Donation record not found.');
    redirect(ADMIN_URL . '/donations.php');
}

// Handle Status Update
if (isPost()) {
    requireCsrfToken();
    $newStatus = getParam('payment_status', 'Completed', 'POST');
    dbUpdate('donations', ['payment_status' => $newStatus], 'id = ?', [$id]);
    setFlash('success', 'Donation status updated to ' . $newStatus);
    redirect(ADMIN_URL . '/donation-details.php?id=' . $id);
}

$pageTitle = 'Donation #' . ($donation['transaction_id'] ?: $donation['id']);

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold"><?= $pageTitle ?></h2>
        <p class="text-muted small mb-0">Receipt generated on <?= formatDate($donation['created_at']) ?></p>
    </div>
    <a href="<?= ADMIN_URL ?>/donations.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Ledger
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card p-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Donation Receipt</h5>
                    <small class="text-muted">Transaction ID: <?= e($donation['transaction_id'] ?: 'N/A') ?></small>
                </div>
                <span class="badge <?= getStatusBadgeClass($donation['payment_status']) ?> fs-6"><?= e($donation['payment_status']) ?></span>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <small class="text-muted d-block">Donor Name</small>
                    <h6 class="fw-bold"><?= e($donation['donor_name']) ?></h6>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Amount Contributed</small>
                    <h4 class="fw-bold text-success mb-0"><?= formatCurrency((float)$donation['amount']) ?></h4>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Seva Program</small>
                    <div><?= e($donation['seva_type'] ?? 'General Gau Seva') ?></div>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Payment Mode &amp; Gateway</small>
                    <div><?= e(strtoupper($donation['payment_method'] ?? 'Online UPI')) ?> (<?= e($donation['payment_gateway'] ?? 'Manual') ?>)</div>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Email Address</small>
                    <div><?= e($donation['donor_email'] ?? 'Not provided') ?></div>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Phone Number</small>
                    <div><?= e($donation['donor_phone'] ?? 'Not provided') ?></div>
                </div>
                <?php if (!empty($donation['pan_number'])): ?>
                <div class="col-sm-6">
                    <small class="text-muted d-block">PAN (80G Tax Exemption)</small>
                    <div class="font-monospace fw-bold"><?= e($donation['pan_number']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($donation['address'])): ?>
                <div class="col-sm-12">
                    <small class="text-muted d-block">Donor Address</small>
                    <div><?= nl2br(e($donation['address'])) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($donation['notes'])): ?>
                <div class="col-sm-12">
                    <small class="text-muted d-block">Remarks / Sankalpa</small>
                    <div class="p-3 bg-light rounded-3"><?= nl2br(e($donation['notes'])) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 pt-3 border-top">
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i> Print Receipt</button>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card p-4">
            <h6 class="fw-bold mb-3">Update Transaction Status</h6>
            <form method="POST" action="">
                <?= csrfField() ?>
                <div class="mb-3">
                    <select name="payment_status" class="form-select">
                        <option value="Completed" <?= $donation['payment_status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="Pending" <?= $donation['payment_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Failed" <?= $donation['payment_status'] === 'Failed' ? 'selected' : '' ?>>Failed</option>
                        <option value="Refunded" <?= $donation['payment_status'] === 'Refunded' ? 'selected' : '' ?>>Refunded</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Update Status</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
