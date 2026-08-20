<?php
/**
 * Admin - Breeds Management
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';

$pageTitle = 'Manage Breeds';

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $breedId = getIntParam('id');
    if ($breedId) {
        $cowCount = dbCount('cows', 'breed_id = ?', [$breedId]);
        if ($cowCount > 0) {
            setFlash('error', "Cannot delete breed because {$cowCount} cow(s) are linked to it. Reassign or delete those cows first.");
        } else {
            $breed = dbFetchOne("SELECT image FROM breeds WHERE id = ?", [$breedId]);
            if ($breed && $breed['image']) {
                deleteUploadedFile('breeds/' . $breed['image']);
            }
            dbDelete('breeds', 'id = ?', [$breedId]);
            setFlash('success', 'Breed deleted successfully.');
        }
        redirect(ADMIN_URL . '/breeds.php');
    }
}

$breeds = dbFetchAll("SELECT b.*, COUNT(c.id) as cow_count FROM breeds b LEFT JOIN cows c ON b.id = c.breed_id GROUP BY b.id ORDER BY b.sort_order ASC");

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Indigenous Breeds</h2>
        <p class="text-muted small mb-0">Native Indian cow breeds preserved at our sanctuary.</p>
    </div>
    <div>
        <a href="<?= ADMIN_URL ?>/breed-form.php" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Add New Breed
        </a>
    </div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Origin</th>
                    <th>Cows Count</th>
                    <th>Status</th>
                    <th>Sort Order</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($breeds)): ?>
                <tr><td colspan="7" class="text-center py-4 text-muted">No breeds registered yet.</td></tr>
                <?php else: foreach ($breeds as $b): ?>
                <tr>
                    <td style="width: 70px;">
                        <img src="<?= getUploadUrl($b['image'] ? 'breeds/' . $b['image'] : '', getPlaceholderImage($b['name'], 60, 45)) ?>" 
                             alt="<?= e($b['name']) ?>" class="rounded-3" style="width: 60px; height: 45px; object-fit: cover;"
                             onerror="this.src='<?= getPlaceholderImage($b['name'], 60, 45) ?>'">
                    </td>
                    <td>
                        <div class="fw-bold text-dark"><?= e($b['name']) ?></div>
                        <small class="text-muted">Slug: /<?= e($b['slug']) ?></small>
                    </td>
                    <td><?= e($b['origin'] ?? 'India') ?></td>
                    <td><span class="badge bg-primary-subtle text-primary fw-bold"><?= $b['cow_count'] ?> cows</span></td>
                    <td>
                        <?php if ($b['is_active']): ?>
                        <span class="badge bg-success-subtle text-success">Active</span>
                        <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $b['sort_order'] ?></td>
                    <td class="text-end">
                        <a href="<?= SITE_URL ?>/breed-details.php?slug=<?= e($b['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-info me-1" title="View Public Page"><i class="bi bi-eye"></i></a>
                        <a href="<?= ADMIN_URL ?>/breed-form.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="<?= ADMIN_URL ?>/breeds.php?action=delete&id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm-delete="Delete breed '<?= e($b['name']) ?>'?" title="Delete"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
