<?php
/**
 * Admin - Donations Ledger
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/pagination.php';

$pageTitle = 'Donations Ledger';

$status = getParam('status');
$search = getParam('search');

$where = "1=1";
$params = [];

if ($status) {
    $where .= " AND payment_status = ?";
    $params[] = $status;
}
if ($search) {
    $where .= " AND (donor_name LIKE ? OR donor_email LIKE ? OR donor_phone LIKE ? OR transaction_id LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$totalItems = dbCount('donations', $where, $params);
$page = getCurrentPage();
$pagination = buildPagination($totalItems, 20, $page);

$donations = dbFetchAll(
    "SELECT * FROM donations 
     WHERE {$where} 
     ORDER BY created_at DESC 
     LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
    $params
);

$totalCompletedSum = dbFetchOne("SELECT COALESCE(SUM(amount), 0) as s FROM donations WHERE payment_status = 'Completed'")['s'];

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Donations Ledger</h2>
        <p class="text-muted small mb-0">Total Completed Seva Contributions: <strong class="text-success"><?= formatCurrency((float)$totalCompletedSum) ?></strong></p>
    </div>
</div>

<!-- Search & Filter -->
<div class="admin-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by donor name, email, phone, transaction ID..." value="<?= e($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
                <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Failed" <?= $status === 'Failed' ? 'selected' : '' ?>>Failed</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-secondary flex-grow-1">Filter</button>
            <?php if ($search || $status): ?>
            <a href="<?= ADMIN_URL ?>/donations.php" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Txn ID</th>
                    <th>Donor Name</th>
                    <th>Amount</th>
                    <th>Seva Type</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($donations)): ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">No donations recorded yet.</td></tr>
                <?php else: foreach ($donations as $d): ?>
                <tr>
                    <td><code><?= e($d['transaction_id'] ?: '#' . $d['id']) ?></code></td>
                    <td>
                        <div class="fw-bold text-dark"><?= e($d['donor_name']) ?></div>
                        <small class="text-muted"><?= e($d['donor_phone'] ?? $d['donor_email']) ?></small>
                    </td>
                    <td class="fw-bold text-success"><?= formatCurrency((float)$d['amount']) ?></td>
                    <td><span class="badge bg-light text-dark border"><?= e($d['seva_type'] ?? 'General') ?></span></td>
                    <td><small class="text-uppercase fw-semibold"><?= e($d['payment_method'] ?? 'Online') ?></small></td>
                    <td><span class="badge <?= getStatusBadgeClass($d['payment_status']) ?>"><?= e($d['payment_status']) ?></span></td>
                    <td><?= formatDate($d['created_at']) ?></td>
                    <td class="text-end">
                        <a href="<?= ADMIN_URL ?>/donation-details.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-receipt"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalItems > 20): ?>
    <div class="p-3 border-top">
        <?= renderPagination($pagination) ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
