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

$photos = dbFetchAll("
SELECT * FROM (
    SELECT 
        'gallery' as source,
        g.id,
        g.image_path,
        g.caption,
        g.category_id,
        gc.name as category_name,
        g.is_active,
        g.sort_order,
        g.created_at
    FROM gallery g
    LEFT JOIN gallery_categories gc ON g.category_id = gc.id

    UNION ALL

    SELECT 
        'cow' as source,
        c.id,
        CONCAT('../cows/', c.photo) as image_path,
        CONCAT(c.name, ' - ', COALESCE(b.name, 'Indigenous'), ' Breed') as caption,
        gc.id as category_id,
        gc.name as category_name,
        1 as is_active,
        100 as sort_order,
        c.created_at
    FROM cows c
    LEFT JOIN breeds b ON c.breed_id = b.id
    CROSS JOIN (SELECT id, name FROM gallery_categories WHERE slug = 'our-cows' LIMIT 1) gc
    WHERE c.photo IS NOT NULL AND c.photo != ''

    UNION ALL

    SELECT 
        'breed' as source,
        br.id,
        CONCAT('../breeds/', br.image) as image_path,
        CONCAT(br.name, ' Breed') as caption,
        gc.id as category_id,
        gc.name as category_name,
        br.is_active,
        110 as sort_order,
        br.created_at
    FROM breeds br
    CROSS JOIN (SELECT id, name FROM gallery_categories WHERE slug = 'goushala' LIMIT 1) gc
    WHERE br.image IS NOT NULL AND br.image != '' AND br.is_active = 1

    UNION ALL

    SELECT 
        'event' as source,
        ev.id,
        CONCAT('../events/', ev.image) as image_path,
        ev.title as caption,
        gc.id as category_id,
        gc.name as category_name,
        1 as is_active,
        120 as sort_order,
        ev.created_at
    FROM events ev
    CROSS JOIN (SELECT id, name FROM gallery_categories WHERE slug = 'events' LIMIT 1) gc
    WHERE ev.image IS NOT NULL AND ev.image != ''

    UNION ALL

    SELECT 
        'news' as source,
        nw.id,
        CONCAT('../news/', nw.featured_image) as image_path,
        nw.title as caption,
        gc.id as category_id,
        gc.name as category_name,
        CASE WHEN nw.status = 'Published' THEN 1 ELSE 0 END as is_active,
        130 as sort_order,
        nw.created_at
    FROM news nw
    CROSS JOIN (SELECT id, name FROM gallery_categories WHERE slug = 'goushala' LIMIT 1) gc
    WHERE nw.featured_image IS NOT NULL AND nw.featured_image != '' AND nw.status = 'Published'
) AS aggregated_gallery
ORDER BY sort_order ASC, created_at DESC
");

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
        <?php
        $editUrl = '';
        $imgUrl = '';
        $isDeletable = false;
        $sourceLabel = '';

        switch ($p['source']) {
            case 'gallery':
                $imgUrl = getUploadUrl('gallery/' . $p['image_path']);
                $editUrl = ADMIN_URL . '/gallery-edit.php?id=' . $p['id'];
                $isDeletable = true;
                $sourceLabel = 'Showcase';
                break;
            case 'cow':
                $cleanName = str_replace('../cows/', '', $p['image_path']);
                $imgUrl = getUploadUrl('cows/' . $cleanName);
                $editUrl = ADMIN_URL . '/cow-form.php?id=' . $p['id'];
                $sourceLabel = 'Cow';
                break;
            case 'breed':
                $cleanName = str_replace('../breeds/', '', $p['image_path']);
                $imgUrl = getUploadUrl('breeds/' . $cleanName);
                $editUrl = ADMIN_URL . '/breed-form.php?id=' . $p['id'];
                $sourceLabel = 'Breed';
                break;
            case 'event':
                $cleanName = str_replace('../events/', '', $p['image_path']);
                $imgUrl = getUploadUrl('events/' . $cleanName);
                $editUrl = ADMIN_URL . '/event-form.php?id=' . $p['id'];
                $sourceLabel = 'Event';
                break;
            case 'news':
                $cleanName = str_replace('../news/', '', $p['image_path']);
                $imgUrl = getUploadUrl('news/' . $cleanName);
                $editUrl = ADMIN_URL . '/news-form.php?id=' . $p['id'];
                $sourceLabel = 'News';
                break;
        }
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                <div style="aspect-ratio: 1; overflow: hidden; background: #eee; position: relative;">
                    <img src="<?= $imgUrl ?>" 
                         alt="<?= e($p['caption'] ?? '') ?>" class="w-100 h-100" style="object-fit: cover;"
                         onerror="this.src='<?= getPlaceholderImage($p['caption'] ?? 'Photo', 250, 250) ?>'">
                    <span class="badge bg-dark bg-opacity-75 position-absolute top-0 start-0 m-2 small">
                        <?= e($p['category_name'] ?? 'General') ?> (<?= $sourceLabel ?>)
                    </span>
                </div>
                <div class="card-body p-2 d-flex flex-column justify-content-between">
                    <div class="small fw-medium text-truncate" title="<?= e($p['caption'] ?? '') ?>"><?= e($p['caption'] ?: 'Untitled') ?></div>
                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                        <span class="badge <?= $p['is_active'] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> small">
                            <?= $p['is_active'] ? 'Active' : 'Hidden' ?>
                        </span>
                        <div class="d-flex gap-2 align-items-center">
                            <?php if (!empty($editUrl)): ?>
                            <a href="<?= $editUrl ?>" class="btn btn-sm btn-outline-primary py-0 px-2 small" style="font-size: 0.72rem;">
                                <i class="bi bi-pencil-fill me-1" style="font-size: 0.65rem;"></i>Edit
                            </a>
                            <?php endif; ?>
                            <?php if ($isDeletable): ?>
                            <a href="<?= ADMIN_URL ?>/gallery.php?action=delete&id=<?= $p['id'] ?>" class="btn btn-sm text-danger p-0" data-confirm-delete="Delete this photo?">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
