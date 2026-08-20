<?php
/**
 * Admin - Event Add/Edit Form
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/validation.php';
require_once BASE_PATH . '/includes/upload.php';

$eventId = getIntParam('id');
$isEdit = !empty($eventId);
$pageTitle = $isEdit ? 'Edit Event' : 'Create New Event';

$event = [
    'title' => '',
    'slug' => '',
    'image' => '',
    'short_description' => '',
    'description' => '',
    'event_date' => '',
    'event_time' => '',
    'location' => 'Kamadenu Goushala, Kavadi, Virajpet Taluk, Kodagu',
    'status' => 'Upcoming',
    'registration_url' => ''
];

if ($isEdit) {
    $existing = dbFetchOne("SELECT * FROM events WHERE id = ?", [$eventId]);
    if (!$existing) {
        setFlash('error', 'Event not found.');
        redirect(ADMIN_URL . '/events.php');
    }
    $event = array_merge($event, $existing);
}

$errors = [];

if (isPost()) {
    requireCsrfToken();
    
    $event['title'] = getParam('title', '', 'POST');
    $event['slug'] = getParam('slug', '', 'POST') ?: slugify($event['title']);
    $event['short_description'] = getParam('short_description', '', 'POST');
    $event['description'] = getParam('description', '', 'POST');
    $event['event_date'] = getParam('event_date', '', 'POST');
    $event['event_time'] = getParam('event_time', '', 'POST');
    $event['location'] = getParam('location', '', 'POST');
    $event['status'] = getParam('status', 'Upcoming', 'POST');
    $event['registration_url'] = getParam('registration_url', '', 'POST');

    $validator = new Validator($event);
    $validator->required('title', 'Event Title')
              ->required('slug', 'Slug')
              ->required('event_date', 'Event Date');

    if ($validator->passes()) {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['image'], 'events', ['image/jpeg', 'image/png', 'image/webp'], 5 * 1024 * 1024);
            if ($uploadResult['success']) {
                if ($isEdit && !empty($event['image'])) {
                    deleteUploadedFile('events/' . $event['image']);
                }
                $event['image'] = $uploadResult['filename'];
            } else {
                $errors['image'] = $uploadResult['message'];
            }
        }

        if (empty($errors)) {
            $data = [
                'title' => $event['title'],
                'slug' => $event['slug'],
                'image' => $event['image'],
                'short_description' => $event['short_description'],
                'description' => $event['description'],
                'event_date' => $event['event_date'],
                'event_time' => $event['event_time'] ?: null,
                'location' => $event['location'],
                'status' => $event['status'],
                'registration_url' => $event['registration_url']
            ];

            if ($isEdit) {
                dbUpdate('events', $data, 'id = ?', [$eventId]);
                setFlash('success', 'Event updated successfully.');
            } else {
                dbInsert('events', $data);
                setFlash('success', 'New event created successfully.');
            }
            redirect(ADMIN_URL . '/events.php');
        }
    } else {
        $errors = array_merge($errors, $validator->getErrors());
    }
}

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold"><?= $pageTitle ?></h2>
        <p class="text-muted small mb-0">Publish details and schedule for community events.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/events.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Events
    </a>
</div>

<form method="POST" action="" enctype="multipart/form-data" class="admin-card p-4">
    <?= csrfField() ?>
    <div class="row g-4">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Event Title <span class="text-danger">*</span></label>
            <input type="text" name="title" data-slug-source class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= e($event['title']) ?>" required>
            <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?= e($errors['title']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
            <input type="text" name="slug" data-slug-target class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" value="<?= e($event['slug']) ?>" required>
            <?php if (isset($errors['slug'])): ?><div class="invalid-feedback"><?= e($errors['slug']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
            <input type="date" name="event_date" class="form-control <?= isset($errors['event_date']) ? 'is-invalid' : '' ?>" value="<?= e($event['event_date']) ?>" required>
            <?php if (isset($errors['event_date'])): ?><div class="invalid-feedback"><?= e($errors['event_date']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Time</label>
            <input type="time" name="event_time" class="form-control" value="<?= e($event['event_time']) ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
                <option value="Upcoming" <?= $event['status'] === 'Upcoming' ? 'selected' : '' ?>>Upcoming</option>
                <option value="Ongoing" <?= $event['status'] === 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                <option value="Completed" <?= $event['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                <option value="Cancelled" <?= $event['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Location</label>
            <input type="text" name="location" class="form-control" value="<?= e($event['location']) ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Registration URL (Optional)</label>
            <input type="url" name="registration_url" class="form-control" value="<?= e($event['registration_url']) ?>" placeholder="https://...">
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Event Banner Image</label>
            <input type="file" name="image" class="form-control" accept="image/*" data-preview="eventImgPreview">
            <div class="mt-2">
                <img id="eventImgPreview" src="<?= $event['image'] ? getUploadUrl('events/' . $event['image']) : '' ?>" 
                     style="max-width: 200px; max-height: 120px; object-fit: cover; border-radius: 8px; display: <?= $event['image'] ? 'block' : 'none' ?>;">
            </div>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Short Summary</label>
            <textarea name="short_description" rows="2" class="form-control"><?= e($event['short_description']) ?></textarea>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Full Event Details</label>
            <textarea name="description" rows="5" class="form-control"><?= e($event['description']) ?></textarea>
        </div>

        <div class="col-12 text-end pt-3 border-top">
            <a href="<?= ADMIN_URL ?>/events.php" class="btn btn-light me-2">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check-circle me-1"></i> Save Event
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
