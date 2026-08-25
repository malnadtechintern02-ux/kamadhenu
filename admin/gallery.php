<?php
/**
 * Admin - Gallery Management
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';

$pageTitle = 'Manage Gallery';

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $imgId = getIntParam('id');
    if ($imgId) {
        $img = dbFetchOne("SELECT image_path FROM gallery WHERE id = ?", [$imgId]);
        if ($img && $img['image_path']) {
            deleteUploadedFile('gallery/' . $img['image_path']);
        }
        dbDelete('gallery', 'id = ?', [$imgId]);
        setFlash('success', 'Photo removed from gallery.');
        redirect(ADMIN_URL . '/gallery.php');
    }
}

$photos = dbFetchAll(
    "SELECT g.*, gc.name as category_name 
     FROM gallery g LEFT JOIN gallery_categories gc ON g.category_id = gc.id 
     ORDER BY g.sort_order ASC, g.created_at DESC"
);

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Photo Gallery</h2>
        <p class="text-muted small mb-0">Total of <?= count($photos) ?> photos in album.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/gallery-upload.php" class="btn btn-primary btn-sm">
        <i class="bi bi-cloud-arrow-up me-1"></i> Upload Photos
    </a>
</div>

<div class="admin-card p-3">
    <div class="row g-3">
        <?php if (empty($photos)): ?>
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-images fs-1 d-block mb-2"></i>
            No photos uploaded yet.
        </div>
        <?php else: foreach ($photos as $p): ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                <div style="aspect-ratio: 1; overflow: hidden; background: #eee; position: relative;">
                    <img src="<?= getUploadUrl('gallery/' . $p['image_path'], getPlaceholderImage($p['caption'] ?? 'Photo', 250, 250)) ?>" 
                         alt="<?= e($p['caption'] ?? '') ?>" class="w-100 h-100" style="object-fit: cover;"
                         onerror="this.src='<?= getPlaceholderImage($p['caption'] ?? 'Photo', 250, 250) ?>'">
                    <span class="badge bg-dark bg-opacity-75 position-absolute top-0 start-0 m-2 small">
                        <?= e($p['category_name'] ?? 'General') ?>
                    </span>
                </div>
                <div class="card-body p-2 d-flex flex-column justify-content-between">
                    <div class="small fw-medium text-truncate" title="<?= e($p['caption'] ?? '') ?>"><?= e($p['caption'] ?: 'Untitled') ?></div>
                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                        <span class="badge <?= $p['is_active'] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> small">
                            <?= $p['is_active'] ? 'Active' : 'Hidden' ?>
                        </span>
                        <div class="d-flex gap-2 align-items-center">
                            <a href="<?= ADMIN_URL ?>/gallery-edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary py-0 px-2 small" style="font-size: 0.72rem;">
                                <i class="bi bi-pencil-fill me-1" style="font-size: 0.65rem;"></i>Edit
                            </a>
                            <a href="<?= ADMIN_URL ?>/gallery.php?action=delete&id=<?= $p['id'] ?>" class="btn btn-sm text-danger p-0" data-confirm-delete="Delete this photo?">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
