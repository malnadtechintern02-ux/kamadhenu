<?php
/**
 * Admin - Inquiries / Contact Messages
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';

$pageTitle = 'Contact Messages';

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = getIntParam('id');
    if ($id) {
        dbDelete('contact_messages', 'id = ?', [$id]);
        setFlash('success', 'Message deleted.');
        redirect(ADMIN_URL . '/messages.php');
    }
}

// Mark All Read
if (isset($_GET['action']) && $_GET['action'] === 'mark_all_read') {
    dbUpdate('contact_messages', ['is_read' => 1], 'is_read = 0');
    setFlash('success', 'All messages marked as read.');
    redirect(ADMIN_URL . '/messages.php');
}

$messages = dbFetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC");

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Contact Messages &amp; Inquiries</h2>
        <p class="text-muted small mb-0">Total of <?= count($messages) ?> inquiries received.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/messages.php?action=mark_all_read" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-check2-all me-1"></i> Mark All as Read
    </a>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Sender Name</th>
                    <th>Email &amp; Phone</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                <tr><td colspan="6" class="text-center py-4 text-muted">No messages received yet.</td></tr>
                <?php else: foreach ($messages as $m): ?>
                <tr class="<?= $m['is_read'] ? '' : 'table-light fw-medium' ?>">
                    <td>
                        <?php if ($m['is_read']): ?>
                        <span class="badge bg-secondary-subtle text-secondary">Read</span>
                        <?php else: ?>
                        <span class="badge bg-danger-subtle text-danger"><i class="bi bi-envelope-fill me-1"></i>New</span>
                        <?php endif; ?>
                    </td>
                    <td><div class="fw-bold text-dark"><?= e($m['name']) ?></div></td>
                    <td>
                        <div><?= e($m['email']) ?></div>
                        <small class="text-muted"><?= e($m['phone'] ?? '-') ?></small>
                    </td>
                    <td><?= e(truncateText($m['subject'], 45)) ?></td>
                    <td><?= formatDate($m['created_at']) ?></td>
                    <td class="text-end">
                        <a href="<?= ADMIN_URL ?>/message-details.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-eye"></i></a>
                        <a href="<?= ADMIN_URL ?>/messages.php?action=delete&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm-delete="Delete message from <?= e($m['name']) ?>?"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
