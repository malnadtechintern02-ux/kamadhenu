<?php
/**
 * Admin - Message View & Reply
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';

$id = getIntParam('id');
if (!$id) { redirect(ADMIN_URL . '/messages.php'); }

// Mark as read
dbUpdate('contact_messages', ['is_read' => 1], 'id = ?', [$id]);

$message = dbFetchOne("SELECT * FROM contact_messages WHERE id = ?", [$id]);
if (!$message) {
    setFlash('error', 'Message not found.');
    redirect(ADMIN_URL . '/messages.php');
}

$pageTitle = 'Message: ' . e($message['subject']);

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Inquiry Details</h2>
        <p class="text-muted small mb-0">Received on <?= formatDate($message['created_at']) ?></p>
    </div>
    <a href="<?= ADMIN_URL ?>/messages.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Messages
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card p-4">
            <h5 class="fw-bold mb-3"><?= e($message['subject']) ?></h5>
            <div class="p-3 bg-light rounded-3 mb-4" style="line-height: 1.8;">
                <?= nl2br(e($message['message'])) ?>
            </div>
            
            <div class="d-flex gap-2">
                <a href="mailto:<?= e($message['email']) ?>?subject=Re: <?= urlencode($message['subject']) ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-reply-fill me-1"></i> Reply via Email
                </a>
                <?php if (!empty($message['phone'])): ?>
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $message['phone']) ?>?text=<?= urlencode("🙏 Namaste {$message['name']}, regarding your inquiry at Kamadhenu Goushala:") ?>" target="_blank" class="btn btn-success btn-sm">
                    <i class="bi bi-whatsapp me-1"></i> Reply on WhatsApp
                </a>
                <?php endif; ?>
                <a href="<?= ADMIN_URL ?>/messages.php?action=delete&id=<?= $message['id'] ?>" class="btn btn-outline-danger btn-sm ms-auto" data-confirm-delete="Delete this message?">
                    <i class="bi bi-trash me-1"></i> Delete
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card p-4">
            <h6 class="fw-bold mb-3">Sender Details</h6>
            <ul class="list-unstyled mb-0">
                <li class="mb-3">
                    <small class="text-muted d-block">Full Name</small>
                    <strong><?= e($message['name']) ?></strong>
                </li>
                <li class="mb-3">
                    <small class="text-muted d-block">Email Address</small>
                    <a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a>
                </li>
                <li class="mb-3">
                    <small class="text-muted d-block">Phone / WhatsApp</small>
                    <?= !empty($message['phone']) ? e($message['phone']) : '<span class="text-muted">Not provided</span>' ?>
                </li>
                <li>
                    <small class="text-muted d-block">Received Date &amp; Time</small>
                    <span><?= date('d M Y, h:i A', strtotime($message['created_at'])) ?></span>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
