<?php
/**
 * Admin - News Add/Edit Form
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';
require_once BASE_PATH . '/includes/validation.php';
require_once BASE_PATH . '/includes/upload.php';

$newsId = getIntParam('id');
$isEdit = !empty($newsId);
$pageTitle = $isEdit ? 'Edit Article' : 'Write New Article';

$article = [
    'title' => '',
    'slug' => '',
    'category_id' => '',
    'featured_image' => '',
    'short_description' => '',
    'content' => '',
    'author' => 'Kamadenu Goushala',
    'published_date' => date('Y-m-d'),
    'status' => 'Published',
    'tags' => '',
    'seo_title' => '',
    'seo_description' => ''
];

if ($isEdit) {
    $existing = dbFetchOne("SELECT * FROM news WHERE id = ?", [$newsId]);
    if (!$existing) {
        setFlash('error', 'Article not found.');
        redirect(ADMIN_URL . '/news.php');
    }
    $article = array_merge($article, $existing);
}

$errors = [];

if (isPost()) {
    requireCsrfToken();
    
    $article['title'] = getParam('title', '', 'POST');
    $article['slug'] = getParam('slug', '', 'POST') ?: slugify($article['title']);
    $article['category_id'] = getIntParam('category_id', 'POST');
    $article['short_description'] = getParam('short_description', '', 'POST');
    $article['content'] = $_POST['content'] ?? '';
    $article['author'] = getParam('author', 'Kamadenu Goushala', 'POST');
    $article['published_date'] = getParam('published_date', date('Y-m-d'), 'POST');
    $article['status'] = getParam('status', 'Published', 'POST');
    $article['tags'] = getParam('tags', '', 'POST');
    $article['seo_title'] = getParam('seo_title', '', 'POST');
    $article['seo_description'] = getParam('seo_description', '', 'POST');

    $validator = new Validator($article);
    $validator->required('title', 'Article Title')
              ->required('slug', 'Slug')
              ->required('content', 'Article Content');

    if ($validator->passes()) {
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['featured_image'], 'news', ['image/jpeg', 'image/png', 'image/webp'], 5 * 1024 * 1024);
            if ($uploadResult['success']) {
                if ($isEdit && !empty($article['featured_image'])) {
                    deleteUploadedFile('news/' . $article['featured_image']);
                }
                $article['featured_image'] = $uploadResult['filename'];
            } else {
                $errors['featured_image'] = $uploadResult['message'];
            }
        }

        if (empty($errors)) {
            $data = [
                'title' => $article['title'],
                'slug' => $article['slug'],
                'category_id' => $article['category_id'] ?: null,
                'featured_image' => $article['featured_image'],
                'short_description' => $article['short_description'],
                'content' => $article['content'],
                'author' => $article['author'],
                'published_date' => $article['published_date'],
                'status' => $article['status'],
                'tags' => $article['tags'],
                'seo_title' => $article['seo_title'],
                'seo_description' => $article['seo_description']
            ];

            if ($isEdit) {
                dbUpdate('news', $data, 'id = ?', [$newsId]);
                setFlash('success', 'Article updated.');
            } else {
                dbInsert('news', $data);
                setFlash('success', 'New article published.');
            }
            redirect(ADMIN_URL . '/news.php');
        }
    } else {
        $errors = array_merge($errors, $validator->getErrors());
    }
}

$categories = dbFetchAll("SELECT id, name FROM news_categories ORDER BY name ASC");

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold"><?= $pageTitle ?></h2>
        <p class="text-muted small mb-0">Compose articles and news announcements.</p>
    </div>
    <a href="<?= ADMIN_URL ?>/news.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to News
    </a>
</div>

<form method="POST" action="" enctype="multipart/form-data" class="admin-card p-4">
    <?= csrfField() ?>
    <div class="row g-4">
        <div class="col-md-8">
            <label class="form-label fw-semibold">Article Title <span class="text-danger">*</span></label>
            <input type="text" name="title" data-slug-source class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= e($article['title']) ?>" required>
            <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?= e($errors['title']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Category</label>
            <select name="category_id" class="form-select">
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $article['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
            <input type="text" name="slug" data-slug-target class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" value="<?= e($article['slug']) ?>" required>
            <?php if (isset($errors['slug'])): ?><div class="invalid-feedback"><?= e($errors['slug']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Author</label>
            <input type="text" name="author" class="form-control" value="<?= e($article['author']) ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Publication Date</label>
            <input type="date" name="published_date" class="form-control" value="<?= e($article['published_date']) ?>">
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Featured Image</label>
            <input type="file" name="featured_image" class="form-control" accept="image/*" data-preview="newsImgPreview">
            <div class="mt-2">
                <img id="newsImgPreview" src="<?= $article['featured_image'] ? getUploadUrl('news/' . $article['featured_image']) : '' ?>" 
                     style="max-width: 200px; max-height: 120px; object-fit: cover; border-radius: 8px; display: <?= $article['featured_image'] ? 'block' : 'none' ?>;">
            </div>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Short Excerpt</label>
            <textarea name="short_description" rows="2" class="form-control" placeholder="A brief summary for previews and social cards..."><?= e($article['short_description']) ?></textarea>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Article Content (HTML supported) <span class="text-danger">*</span></label>
            <textarea name="content" rows="12" class="form-control <?= isset($errors['content']) ? 'is-invalid' : '' ?>" required><?= e($article['content']) ?></textarea>
            <?php if (isset($errors['content'])): ?><div class="invalid-feedback"><?= e($errors['content']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
                <option value="Published" <?= $article['status'] === 'Published' ? 'selected' : '' ?>>Published</option>
                <option value="Draft" <?= $article['status'] === 'Draft' ? 'selected' : '' ?>>Draft</option>
                <option value="Archived" <?= $article['status'] === 'Archived' ? 'selected' : '' ?>>Archived</option>
            </select>
        </div>

        <div class="col-md-8">
            <label class="form-label fw-semibold">Tags (Comma Separated)</label>
            <input type="text" name="tags" class="form-control" value="<?= e($article['tags']) ?>" placeholder="Gau Seva, Gir Cow, Panchagavya">
        </div>

        <!-- SEO -->
        <div class="col-12 pt-3 border-top">
            <h6 class="fw-bold mb-3"><i class="bi bi-search me-1"></i> Search Engine Optimization (SEO)</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">SEO Title</label>
                    <input type="text" name="seo_title" class="form-control form-control-sm" value="<?= e($article['seo_title']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">SEO Meta Description</label>
                    <input type="text" name="seo_description" class="form-control form-control-sm" value="<?= e($article['seo_description']) ?>">
                </div>
            </div>
        </div>

        <div class="col-12 text-end pt-3 border-top">
            <a href="<?= ADMIN_URL ?>/news.php" class="btn btn-light me-2">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-check-circle me-1"></i> Save Article
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
