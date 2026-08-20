<?php
/**
 * Admin - Events Management
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';

$pageTitle = 'Manage Events';

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $eventId = getIntParam('id');
    if ($eventId) {
        $event = dbFetchOne("SELECT image FROM events WHERE id = ?", [$eventId]);
        if ($event && $event['image']) {
            deleteUploadedFile('events/' . $event['image']);
        }
        dbDelete('events', 'id = ?', [$eventId]);
        setFlash('success', 'Event deleted successfully.');
        redirect(ADMIN_URL . '/events.php');
    }
}

$events = dbFetchAll("SELECT * FROM events ORDER BY event_date DESC");

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Goushala Events</h2>
        <p class="text-muted small mb-0">Manage community programs, yagnas, and seva events.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/event-form.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Create Event
    </a>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Event Title</th>
                    <th>Date &amp; Time</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                <tr><td colspan="6" class="text-center py-4 text-muted">No events found.</td></tr>
                <?php else: foreach ($events as $e): 
                    $ed = new DateTime($e['event_date']);
                ?>
                <tr>
                    <td style="width: 70px;">
                        <img src="<?= getUploadUrl($e['image'] ? 'events/' . $e['image'] : '', getPlaceholderImage($e['title'], 60, 40)) ?>" 
                             alt="<?= e($e['title']) ?>" class="rounded-3" style="width: 60px; height: 40px; object-fit: cover;"
                             onerror="this.src='<?= getPlaceholderImage($e['title'], 60, 40) ?>'">
                    </td>
                    <td>
                        <div class="fw-bold text-dark"><?= e($e['title']) ?></div>
                        <small class="text-muted"><?= e(truncateText($e['short_description'] ?? '', 50)) ?></small>
                    </td>
                    <td>
                        <div><?= $ed->format('d M, Y') ?></div>
                        <?php if ($e['event_time']): ?><small class="text-muted"><?= formatTime($e['event_time']) ?></small><?php endif; ?>
                    </td>
                    <td><?= e($e['location'] ?? 'Sanctuary') ?></td>
                    <td><span class="badge <?= getStatusBadgeClass($e['status']) ?>"><?= e($e['status']) ?></span></td>
                    <td class="text-end">
                        <a href="<?= SITE_URL ?>/event-details.php?slug=<?= e($e['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-info me-1"><i class="bi bi-eye"></i></a>
                        <a href="<?= ADMIN_URL ?>/event-form.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        <a href="<?= ADMIN_URL ?>/events.php?action=delete&id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm-delete="Delete event '<?= e($e['title']) ?>'?"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
