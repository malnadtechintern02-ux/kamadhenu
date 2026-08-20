<?php
/**
 * Our Cows Listing Page
 * Kamadenu Goushala
 */

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/pagination.php';

$seo = [
    'title' => 'Our Cows',
    'description' => 'Meet the indigenous cows protected at Kamadhenu Goushala. Browse, search, and learn about our beloved Gir, Hallikar, and Malenadu Gidda cows.',
];

// Filters
$breedFilter = getParam('breed');
$genderFilter = getParam('gender');
$statusFilter = getParam('status');
$searchQuery = getParam('search');

// Build query
$where = '1=1';
$params = [];

if ($breedFilter) {
    $where .= ' AND b.slug = ?';
    $params[] = $breedFilter;
}
if ($genderFilter) {
    $where .= ' AND c.gender = ?';
    $params[] = $genderFilter;
}
if ($statusFilter) {
    $where .= ' AND c.status = ?';
    $params[] = $statusFilter;
}
if ($searchQuery) {
    $where .= ' AND (c.name LIKE ? OR c.description LIKE ? OR b.name LIKE ?)';
    $params[] = "%{$searchQuery}%";
    $params[] = "%{$searchQuery}%";
    $params[] = "%{$searchQuery}%";
}

// Count total
$countStmt = getDB()->prepare("SELECT COUNT(*) as count FROM cows c LEFT JOIN breeds b ON c.breed_id = b.id WHERE {$where}");
$countStmt->execute($params);
$totalItems = (int)$countStmt->fetch()['count'];

// Pagination
$page = getCurrentPage();
$pagination = buildPagination($totalItems, ITEMS_PER_PAGE, $page);

// Fetch cows
$sql = "SELECT c.*, b.name as breed_name, b.slug as breed_slug 
        FROM cows c LEFT JOIN breeds b ON c.breed_id = b.id 
        WHERE {$where} 
        ORDER BY c.is_featured DESC, c.created_at DESC 
        LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";
$stmt = getDB()->prepare($sql);
$stmt->execute($params);
$cows = $stmt->fetchAll();

// Get breeds for filter
$breeds = dbFetchAll("SELECT id, name, slug FROM breeds WHERE is_active = 1 ORDER BY sort_order ASC");

include BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/navbar.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active">Our Cows</li>
            </ol>
        </nav>
        <h1>Our Beloved Cows</h1>
        <p>Meet the indigenous cows we protect and nurture with love and devotion.</p>
    </div>
</section>

<!-- Filters -->
<section class="py-4">
    <div class="container">
        <div class="card border-0 shadow-sm rounded-4 p-3">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small fw-semibold">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by name..." value="<?= e($searchQuery) ?>" id="searchInput">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small fw-semibold">Breed</label>
                    <select name="breed" class="form-select">
                        <option value="">All Breeds</option>
                        <?php foreach ($breeds as $b): ?>
                        <option value="<?= e($b['slug']) ?>" <?= $breedFilter === $b['slug'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small fw-semibold">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">All</option>
                        <option value="Female" <?= $genderFilter === 'Female' ? 'selected' : '' ?>>Female</option>
                        <option value="Male" <?= $genderFilter === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Calf" <?= $genderFilter === 'Calf' ? 'selected' : '' ?>>Calf</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="Available" <?= $statusFilter === 'Available' ? 'selected' : '' ?>>Available</option>
                        <option value="Adopted" <?= $statusFilter === 'Adopted' ? 'selected' : '' ?>>Adopted</option>
                        <option value="Permanent Resident" <?= $statusFilter === 'Permanent Resident' ? 'selected' : '' ?>>Permanent Resident</option>
                        <option value="Medical Care" <?= $statusFilter === 'Medical Care' ? 'selected' : '' ?>>Medical Care</option>
                        <option value="Rescued" <?= $statusFilter === 'Rescued' ? 'selected' : '' ?>>Rescued</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-12">
                    <button type="submit" class="btn btn-primary-custom me-2"><i class="bi bi-search me-1"></i> Filter</button>
                    <a href="<?= SITE_URL ?>/cows.php" class="btn btn-outline-custom">Clear</a>
                </div>
            </form>
        </div>
        <p class="text-muted mt-3 small">Showing <?= count($cows) ?> of <?= $totalItems ?> cows</p>
    </div>
</section>

<!-- Cow Grid -->
<section class="section pt-0">
    <div class="container">
        <?php if (empty($cows)): ?>
        <div class="text-center py-5">
            <i class="bi bi-heart text-muted" style="font-size: 3rem;"></i>
            <h3 class="mt-3">No cows found</h3>
            <p class="text-muted">Try adjusting your filters or search query.</p>
            <a href="<?= SITE_URL ?>/cows.php" class="btn btn-primary-custom">View All Cows</a>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($cows as $cow): ?>
            <div class="col-lg-4 col-md-6 animate-on-scroll" data-searchable="<?= e($cow['name'] . ' ' . ($cow['breed_name'] ?? '') . ' ' . $cow['status']) ?>">
                <div class="cow-card">
                    <div class="cow-img-wrapper">
                        <img src="<?= getCowPhotoUrl($cow['photo'], $cow['name']) ?>" alt="<?= e($cow['name']) ?>" loading="lazy">
                        <span class="cow-status">
                            <span class="badge <?= getStatusBadgeClass($cow['status']) ?>"><?= e($cow['status']) ?></span>
                        </span>
                    </div>
                    <div class="cow-info">
                        <h3 class="cow-name"><?= e($cow['name']) ?></h3>
                        <div class="cow-meta">
                            <span><i class="bi bi-tag"></i> <?= e($cow['breed_name'] ?? 'Indigenous') ?></span>
                            <span><i class="bi bi-calendar"></i> <?= calculateAge($cow['date_of_birth']) ?></span>
                            <span><i class="bi bi-gender-ambiguous"></i> <?= e($cow['gender']) ?></span>
                        </div>
                        <p class="card-text"><?= e(truncateText($cow['description'] ?? '', 100)) ?></p>
                        <div class="cow-actions">
                            <a href="<?= SITE_URL ?>/cow-details.php?id=<?= (int)$cow['id'] ?>" class="btn btn-outline-custom btn-sm flex-fill">View Profile</a>
                            <?php if ($cow['is_adoptable'] && $cow['status'] === 'Available'): ?>
                            <a href="<?= SITE_URL ?>/adopt-a-cow.php?cow=<?= (int)$cow['id'] ?>" class="btn btn-primary-custom btn-sm flex-fill">Adopt</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <div class="mt-5">
            <?= renderPagination($pagination) ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include BASE_PATH . '/includes/footer.php'; ?>
