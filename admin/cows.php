<?php
/**
 * Admin - Cows Management
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/pagination.php';

$pageTitle = 'Manage Cows';

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $cowId = getIntParam('id');
    if ($cowId) {
        $cow = dbFetchOne("SELECT photo FROM cows WHERE id = ?", [$cowId]);
        if ($cow && $cow['photo']) {
            deleteUploadedFile('cows/' . $cow['photo']);
        }
        dbDelete('cows', 'id = ?', [$cowId]);
        setFlash('success', 'Cow profile deleted successfully.');
        redirect(ADMIN_URL . '/cows.php');
    }
}

// Search and filter
$search = getParam('search');
$breedId = getIntParam('breed_id');
$where = "1=1";
$params = [];

if ($search) {
    $where .= " AND (c.name LIKE ? OR c.description LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}
if ($breedId) {
    $where .= " AND c.breed_id = ?";
    $params[] = $breedId;
}

$totalItems = dbCount('cows c', $where, $params);
$page = getCurrentPage();
$pagination = buildPagination($totalItems, 15, $page);

$cows = dbFetchAll(
    "SELECT c.*, b.name as breed_name 
     FROM cows c LEFT JOIN breeds b ON c.breed_id = b.id 
     WHERE {$where} 
     ORDER BY c.created_at DESC 
     LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
    $params
);

$breeds = dbFetchAll("SELECT id, name FROM breeds ORDER BY name ASC");

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Manage Cows</h2>
        <p class="text-muted small mb-0">Total of <?= $totalItems ?> registered cows in sanctuary.</p>
    </div>
    <div>
        <a href="<?= ADMIN_URL ?>/cow-form.php" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Add New Cow
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="admin-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search by name or description..." value="<?= e($search) ?>">
            </div>
        </div>
        <div class="col-md-4">
            <select name="breed_id" class="form-select form-select-sm">
                <option value="">All Breeds</option>
                <?php foreach ($breeds as $b): ?>
                <option value="<?= $b['id'] ?>" <?= $breedId == $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-secondary flex-grow-1">Filter</button>
            <?php if ($search || $breedId): ?>
            <a href="<?= ADMIN_URL ?>/cows.php" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table -->
<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Breed</th>
                    <th>Gender &amp; Age</th>
                    <th>Status</th>
                    <th>Adoptable</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cows)): ?>
                <tr><td colspan="7" class="text-center py-4 text-muted">No cows found matching the criteria.</td></tr>
                <?php else: foreach ($cows as $c): ?>
                <tr>
                    <td style="width: 60px;">
                        <img src="<?= getCowPhotoUrl($c['photo'], $c['name']) ?>" alt="<?= e($c['name']) ?>" class="rounded-3" style="width: 48px; height: 48px; object-fit: cover;">
                    </td>
                    <td>
                        <div class="fw-bold text-dark"><?= e($c['name']) ?></div>
                        <?php if ($c['is_featured']): ?>
                        <span class="badge bg-warning-subtle text-warning small"><i class="bi bi-star-fill me-1"></i>Featured</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($c['breed_name'] ?? 'Unknown') ?></td>
                    <td>
                        <div><?= e($c['gender']) ?></div>
                        <small class="text-muted"><?= calculateAge($c['date_of_birth']) ?></small>
                    </td>
                    <td><span class="badge <?= getStatusBadgeClass($c['status']) ?>"><?= e($c['status']) ?></span></td>
                    <td>
                        <?php if ($c['is_adoptable']): ?>
                        <span class="text-success small fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Yes</span>
                        <?php else: ?>
                        <span class="text-muted small">No</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <a href="<?= SITE_URL ?>/cow-details.php?id=<?= $c['id'] ?>" target="_blank" class="btn btn-sm btn-outline-info me-1" title="View Public Profile"><i class="bi bi-eye"></i></a>
                        <a href="<?= ADMIN_URL ?>/cow-form.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="<?= ADMIN_URL ?>/cows.php?action=delete&id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm-delete="Are you sure you want to remove <?= e($c['name']) ?> from records?" title="Delete"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalItems > 15): ?>
    <div class="p-3 border-top">
        <?= renderPagination($pagination) ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
