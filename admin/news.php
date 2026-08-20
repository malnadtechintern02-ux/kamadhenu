<?php
/**
 * Admin - News/Articles Management
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';

$pageTitle = 'News & Articles';

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $newsId = getIntParam('id');
    if ($newsId) {
        $news = dbFetchOne("SELECT featured_image FROM news WHERE id = ?", [$newsId]);
        if ($news && $news['featured_image']) {
            deleteUploadedFile('news/' . $news['featured_image']);
        }
        dbDelete('news', 'id = ?', [$newsId]);
        setFlash('success', 'Article deleted.');
        redirect(ADMIN_URL . '/news.php');
    }
}

$articles = dbFetchAll(
    "SELECT n.*, nc.name as category_name 
     FROM news n LEFT JOIN news_categories nc ON n.category_id = nc.id 
     ORDER BY n.published_date DESC"
);

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">News &amp; Blog Articles</h2>
        <p class="text-muted small mb-0">Publish stories, announcements, and seva updates.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/news-form.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Write New Article
    </a>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Published Date</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($articles)): ?>
                <tr><td colspan="6" class="text-center py-4 text-muted">No news articles published yet.</td></tr>
                <?php else: foreach ($articles as $a): ?>
                <tr>
                    <td style="width: 70px;">
                        <img src="<?= getUploadUrl($a['featured_image'] ? 'news/' . $a['featured_image'] : '', getPlaceholderImage($a['title'], 60, 40)) ?>" 
                             alt="<?= e($a['title']) ?>" class="rounded-3" style="width: 60px; height: 40px; object-fit: cover;"
                             onerror="this.src='<?= getPlaceholderImage($a['title'], 60, 40) ?>'">
                    </td>
                    <td>
                        <div class="fw-bold text-dark"><?= e($a['title']) ?></div>
                        <small class="text-muted">By <?= e($a['author'] ?? 'Kamadenu Goushala') ?></small>
                    </td>
                    <td><span class="badge bg-light text-dark border"><?= e($a['category_name'] ?? 'General') ?></span></td>
                    <td><?= formatDate($a['published_date']) ?></td>
                    <td><span class="badge <?= getStatusBadgeClass($a['status']) ?>"><?= e($a['status']) ?></span></td>
                    <td class="text-end">
                        <a href="<?= SITE_URL ?>/news-details.php?slug=<?= e($a['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-info me-1"><i class="bi bi-eye"></i></a>
                        <a href="<?= ADMIN_URL ?>/news-form.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        <a href="<?= ADMIN_URL ?>/news.php?action=delete&id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm-delete="Delete article '<?= e($a['title']) ?>'?"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
