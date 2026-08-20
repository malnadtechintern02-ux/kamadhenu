<?php
/**
 * Admin - Adoption Details
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';

$id = getIntParam('id');
if (!$id) { redirect(ADMIN_URL . '/adoptions.php'); }

$adoption = dbFetchOne(
    "SELECT a.*, c.name as cow_name, c.photo as cow_photo, b.name as breed_name 
     FROM adoptions a 
     LEFT JOIN cows c ON a.cow_id = c.id 
     LEFT JOIN breeds b ON c.breed_id = b.id 
     WHERE a.id = ?", 
    [$id]
);

if (!$adoption) {
    setFlash('error', 'Adoption record not found.');
    redirect(ADMIN_URL . '/adoptions.php');
}

// Handle Status Update
if (isPost()) {
    requireCsrfToken();
    $newStatus = getParam('status', 'Active', 'POST');
    dbUpdate('adoptions', ['status' => $newStatus], 'id = ?', [$id]);
    setFlash('success', 'Adoption status updated.');
    redirect(ADMIN_URL . '/adoption-details.php?id=' . $id);
}

$pageTitle = 'Adoption: ' . e($adoption['adopter_name']);

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold"><?= $pageTitle ?></h2>
        <p class="text-muted small mb-0">Record initiated on <?= formatDate($adoption['created_at']) ?></p>
    </div>
    <a href="<?= ADMIN_URL ?>/adoptions.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Adoptions
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card p-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Gau Adoption Certificate &amp; Details</h5>
                    <small class="text-muted">Guardian: <?= e($adoption['adopter_name']) ?></small>
                </div>
                <span class="badge <?= getStatusBadgeClass($adoption['status']) ?> fs-6"><?= e($adoption['status']) ?></span>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <small class="text-muted d-block">Adopted Cow</small>
                    <h6 class="fw-bold text-primary mb-0"><?= e($adoption['cow_name'] ?? 'General Cow Support') ?></h6>
                    <?php if (!empty($adoption['breed_name'])): ?>
                    <small class="text-muted">Breed: <?= e($adoption['breed_name']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Total Contribution</small>
                    <h4 class="fw-bold text-success mb-0"><?= formatCurrency((float)$adoption['total_amount']) ?></h4>
                    <small class="text-muted"><?= formatCurrency((float)$adoption['monthly_amount']) ?> / month</small>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Adoption Duration</small>
                    <div><strong><?= e($adoption['duration_months']) ?> Months</strong> (<?= formatDate($adoption['start_date']) ?> to <?= formatDate($adoption['end_date']) ?>)</div>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Adopter Email</small>
                    <div><?= e($adoption['adopter_email'] ?? 'Not provided') ?></div>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block">Adopter Phone</small>
                    <div><?= e($adoption['adopter_phone'] ?? 'Not provided') ?></div>
                </div>
                <?php if (!empty($adoption['pan_number'])): ?>
                <div class="col-sm-6">
                    <small class="text-muted d-block">PAN Number (80G)</small>
                    <div class="font-monospace fw-bold"><?= e($adoption['pan_number']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($adoption['adopter_address'])): ?>
                <div class="col-sm-12">
                    <small class="text-muted d-block">Postal Address</small>
                    <div><?= nl2br(e($adoption['adopter_address'])) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($adoption['certificate_name'])): ?>
                <div class="col-sm-12">
                    <small class="text-muted d-block">Name Printed on Certificate</small>
                    <div class="fw-bold"><?= e($adoption['certificate_name']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($adoption['special_occasion'])): ?>
                <div class="col-sm-12">
                    <small class="text-muted d-block">Special Occasion / Dedication</small>
                    <div class="p-3 bg-light rounded-3"><?= nl2br(e($adoption['special_occasion'])) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 pt-3 border-top">
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i> Print Certificate Info</button>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card p-4">
            <h6 class="fw-bold mb-3">Update Adoption Status</h6>
            <form method="POST" action="">
                <?= csrfField() ?>
                <div class="mb-3">
                    <select name="status" class="form-select">
                        <option value="Active" <?= $adoption['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                        <option value="Completed" <?= $adoption['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="Pending" <?= $adoption['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Cancelled" <?= $adoption['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Save Status</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
