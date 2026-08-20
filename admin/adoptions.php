<?php
/**
 * Admin - Adoptions Management
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/pagination.php';

$pageTitle = 'Cow Adoptions';

$status = getParam('status');
$search = getParam('search');

$where = "1=1";
$params = [];

if ($status) {
    $where .= " AND a.status = ?";
    $params[] = $status;
}
if ($search) {
    $where .= " AND (a.adopter_name LIKE ? OR a.adopter_email LIKE ? OR a.adopter_phone LIKE ? OR c.name LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$totalItems = dbCount('adoptions a LEFT JOIN cows c ON a.cow_id = c.id', $where, $params);
$page = getCurrentPage();
$pagination = buildPagination($totalItems, 20, $page);

$adoptions = dbFetchAll(
    "SELECT a.*, c.name as cow_name, c.photo as cow_photo 
     FROM adoptions a LEFT JOIN cows c ON a.cow_id = c.id 
     WHERE {$where} 
     ORDER BY a.created_at DESC 
     LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
    $params
);

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Gau Guardians (Adoptions)</h2>
        <p class="text-muted small mb-0">Total of <?= $totalItems ?> cow adoptions recorded.</p>
    </div>
</div>

<!-- Search & Filter -->
<div class="admin-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search adopter name, cow name, phone..." value="<?= e($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
                <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Cancelled" <?= $status === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-secondary flex-grow-1">Filter</button>
            <?php if ($search || $status): ?>
            <a href="<?= ADMIN_URL ?>/adoptions.php" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Adopter Name</th>
                    <th>Adopted Cow</th>
                    <th>Duration</th>
                    <th>Total Contribution</th>
                    <th>Start Date</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($adoptions)): ?>
                <tr><td colspan="7" class="text-center py-4 text-muted">No adoptions found.</td></tr>
                <?php else: foreach ($adoptions as $a): ?>
                <tr>
                    <td>
                        <div class="fw-bold text-dark"><?= e($a['adopter_name']) ?></div>
                        <small class="text-muted"><?= e($a['adopter_phone'] ?? $a['adopter_email']) ?></small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="fw-semibold"><?= e($a['cow_name'] ?? 'General Sanctuary Cow') ?></span>
                        </div>
                    </td>
                    <td><?= e($a['duration_months']) ?> Months</td>
                    <td class="fw-bold text-success"><?= formatCurrency((float)$a['total_amount']) ?></td>
                    <td><?= formatDate($a['start_date']) ?></td>
                    <td><span class="badge <?= getStatusBadgeClass($a['status']) ?>"><?= e($a['status']) ?></span></td>
                    <td class="text-end">
                        <a href="<?= ADMIN_URL ?>/adoption-details.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
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
