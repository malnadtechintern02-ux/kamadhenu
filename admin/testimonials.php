<?php
/**
 * Admin - Testimonials Management
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';

$pageTitle = 'Manage Testimonials';

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = getIntParam('id');
    if ($id) {
        dbDelete('testimonials', 'id = ?', [$id]);
        setFlash('success', 'Testimonial deleted.');
        redirect(ADMIN_URL . '/testimonials.php');
    }
}

$testimonials = dbFetchAll("SELECT * FROM testimonials ORDER BY sort_order ASC, created_at DESC");

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Testimonials &amp; Reviews</h2>
        <p class="text-muted small mb-0">Words of support and appreciation from devotees and visitors.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/testimonial-form.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Add Testimonial
    </a>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location / Role</th>
                    <th>Rating</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($testimonials)): ?>
                <tr><td colspan="6" class="text-center py-4 text-muted">No testimonials registered.</td></tr>
                <?php else: foreach ($testimonials as $t): ?>
                <tr>
                    <td>
                        <div class="fw-bold text-dark"><?= e($t['name']) ?></div>
                    </td>
                    <td><?= e($t['location'] ?: $t['designation'] ?: 'Supporter') ?></td>
                    <td>
                        <span class="text-warning">
                            <?php for ($i = 0; $i < $t['rating']; $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
                        </span>
                    </td>
                    <td><small class="text-muted"><?= e(truncateText($t['message'], 70)) ?></small></td>
                    <td>
                        <?php if ($t['is_active']): ?>
                        <span class="badge bg-success-subtle text-success">Visible</span>
                        <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary">Hidden</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <a href="<?= ADMIN_URL ?>/testimonial-form.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        <a href="<?= ADMIN_URL ?>/testimonials.php?action=delete&id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm-delete="Delete this testimonial?"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
