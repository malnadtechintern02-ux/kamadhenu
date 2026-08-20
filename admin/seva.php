<?php
/**
 * Admin - Seva Offerings Management
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';

$pageTitle = 'Gau Seva Programs';

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $sevaId = getIntParam('id');
    if ($sevaId) {
        $seva = dbFetchOne("SELECT image FROM seva_categories WHERE id = ?", [$sevaId]);
        if ($seva && $seva['image']) {
            deleteUploadedFile('seva/' . $seva['image']);
        }
        dbDelete('seva_categories', 'id = ?', [$sevaId]);
        setFlash('success', 'Seva program deleted.');
        redirect(ADMIN_URL . '/seva.php');
    }
}

$sevaPrograms = dbFetchAll("SELECT * FROM seva_categories ORDER BY sort_order ASC");

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Gau Seva Offerings</h2>
        <p class="text-muted small mb-0">Manage donation programs and suggested seva amounts.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/seva-form.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Add Seva Offering
    </a>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Icon / Img</th>
                    <th>Title</th>
                    <th>Suggested Amounts</th>
                    <th>Status</th>
                    <th>Sort</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sevaPrograms)): ?>
                <tr><td colspan="6" class="text-center py-4 text-muted">No seva categories found.</td></tr>
                <?php else: foreach ($sevaPrograms as $s): ?>
                <tr>
                    <td style="width: 50px;">
                        <div class="stat-icon-wrapper bg-light text-success" style="width: 38px; height: 38px; font-size: 1.1rem;">
                            <i class="bi <?= e($s['icon'] ?: 'bi-heart-fill') ?>"></i>
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold text-dark"><?= e($s['title']) ?></div>
                        <small class="text-muted">Slug: /<?= e($s['slug']) ?></small>
                    </td>
                    <td>
                        <?php 
                        $amts = array_filter(explode(',', $s['suggested_amounts'] ?? ''));
                        foreach ($amts as $a):
                        ?>
                        <span class="badge bg-light text-dark border me-1">₹<?= number_format((float)trim($a)) ?></span>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php if ($s['is_active']): ?>
                        <span class="badge bg-success-subtle text-success">Active</span>
                        <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $s['sort_order'] ?></td>
                    <td class="text-end">
                        <a href="<?= ADMIN_URL ?>/seva-form.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        <a href="<?= ADMIN_URL ?>/seva.php?action=delete&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm-delete="Delete <?= e($s['title']) ?>?"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
